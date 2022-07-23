<?php

declare(strict_types = 1);

namespace App\Services;

use App\Exceptions\CancelOrDeclineReservationDateHasPassedException;
use App\Exceptions\NoActiveSubscriptionForTheRequestedDateException;
use App\Exceptions\ReservationAlreadyCanceledException;
use App\Exceptions\ReservationCancellationIsNotAllowedException;
use App\Exceptions\SessionsLimitExceededException;
use App\Exceptions\SessionsWeekLimitExceededException;
use App\Models\Reservation;
use App\Models\Subscription;
use App\Models\User;
use App\Validators\ReservationValidation;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    /**
     * The default number of items per page
     *
     * @var int
     */
    private const DEFAULT_ITEMS_PER_PAGE = 0;

    /**
     * @var ReservationValidation
     */
    private $reservationValidation;

    public function __construct(ReservationValidation $reservationValidation)
    {
        $this->reservationValidation = $reservationValidation;
    }

    /**
     * Return all reservations
     *
     * @param array $input
     *
     * @return LengthAwarePaginator
     */
    public function getReservations(array $input): LengthAwarePaginator
    {
        // data validation
        $data = $this->reservationValidation->reservationGetReservations($input);

        $itemsPerPage = $data['items_per_page'] ?? self::DEFAULT_ITEMS_PER_PAGE;
        $reservations = Reservation::paginate($itemsPerPage);

        return $reservations;
    }

    /**
     * Create a reservation
     *
     * @param array $input Reservation data
     * @param User $user
     *
     * @return Reservation
     */
    public function create(array $input, User $user): Reservation
    {
        // if the logged-in user is student, then use as user_id his id
        if ($user->isStudent) {
            $input['user_id'] = $user->id;
        }

        // data validation
        $data = $this->reservationValidation->reservationCreate($input);

        // start db transaction
        DB::beginTransaction();

        try {
            // get subscription based on the reservation date
            $subscription = $this->getSubscriptionBasedOnReservationDate($data['user_id'], $data['date']);

            // if there is no active subscription for the requested date throw exception
            if (!$subscription) {
                throw new NoActiveSubscriptionForTheRequestedDateException();
            }

            // check if subscription has no unlimited sessions
            if (!$subscription->unlimited_sessions) {
                // check if subscription has sessions per week
                if ($subscription->sessions_per_week) {
                    $now = Carbon::now('Europe/Athens');
//                    $weekStartDate = $now->startOfWeek()->format('Y-m-d');
//                    $weekEndDate = $now->endOfWeek()->format('Y-m-d');
                    $firstDayOfWeek = $now->startOfWeek(Carbon::MONDAY);
                    $lastDayOfWeek = $now->endOfWeek(Carbon::SUNDAY);

                    // count reserved week sessions
                    $reservedWeekSessions = Reservation::whereBetween('date', [$firstDayOfWeek, $lastDayOfWeek])
                        ->where('canceled', false)
                        ->where('declined', false)
                        ->count();

                    // check if sessions week limit has been exceeded
                    if ($reservedWeekSessions >= $subscription->sessions_per_week) {
                        throw new SessionsWeekLimitExceededException();
                    }
                } else {
                    // check if there are remaining sessions
                    if ($subscription->remaining_sessions > 0) {
                        // decrease remaining sessions by one
                        $subscription->remaining_sessions--;
                        $subscription->save();
                    } else {
                        // sessions limit has been exceeded
                        throw new SessionsLimitExceededException();
                    }
                }
            }

            $reservation = Reservation::create($data);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();

        return $reservation;
    }

    /**
     * Cancel a reservation
     *
     * @param array $input Reservation data
     *
     * @return void
     */
    public function cancel(array $input, Reservation $reservation)
    {
        // data validation
        $data = $this->reservationValidation->reservationCancel($input);

        // start db transaction
        DB::beginTransaction();

        try {
            $now = Carbon::now('Europe/Athens');
            $reservationDate = Carbon::parse($reservation->date, 'Europe/Athens');

            // check if reservation date has passed
            if ($now > $reservationDate) {
                throw new CancelOrDeclineReservationDateHasPassedException();
            }

            // check if the reservation is already canceled or declined
            if ($reservation->canceled || $reservation->declined) {
                throw new ReservationAlreadyCanceledException();
            }

            // calculate hours before the reservation date
            $hoursBeforeReservationDate = $reservationDate->diffInHours($now);

            // check if the hours are less than the allowed hours to cancel the reservation
            if ($hoursBeforeReservationDate < Reservation::ALLOWED_HOURS_TO_CANCEL_BEFORE_RESERVATION_DATE) {
                throw new ReservationCancellationIsNotAllowedException;
            }

            // get subscription based on the reservation date
            $subscription = $this->getSubscriptionBasedOnReservationDate($data['user_id'], $data['date']);

            // increase remaining sessions by one
            $subscription->remaining_sessions++;
            $subscription->save();

            $reservation->update($data);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Decline a reservation
     *
     * @param array $input Reservation data
     *
     * @return void
     */
    public function decline(array $input, Reservation $reservation)
    {
        // data validation
        $data = $this->reservationValidation->reservationDecline($input);

        // start db transaction
        DB::beginTransaction();

        try {
            $now = Carbon::now('Europe/Athens');
            $reservationDate = Carbon::parse($reservation->date, 'Europe/Athens');

            // check if reservation date has passed
            if ($now > $reservationDate) {
                throw new CancelOrDeclineReservationDateHasPassedException();
            }

            // check if the reservation is already canceled or declined
            if ($reservation->canceled || $reservation->declined) {
                throw new ReservationAlreadyCanceledException();
            }

            // get subscription based on the reservation date
            $subscription = $this->getSubscriptionBasedOnReservationDate($data['user_id'], $data['date']);

            // increase remaining sessions by one
            $subscription->remaining_sessions++;
            $subscription->save();

            $reservation->update($data);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Delete reservation
     *
     * @param Reservation $reservation
     *
     * @return void
     */
    public function delete(Reservation $reservation)
    {
        // start db transaction
        DB::beginTransaction();

        try {
            $reservation->delete();
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * get subscription based on reservation date
     *
     * @param int    $user_id
     * @param string $date
     *
     * @return Subscription|null
     */
    private function getSubscriptionBasedOnReservationDate(int $user_id, string $date): Subscription
    {
        // get active subscription based on the reservation date
        $subscription = Subscription::where('user_id', $user_id)
            ->where('starts_at', '<=', $date)
            ->where('expires_at', '>=', $date)
            ->first();

        return $subscription;
    }
}
