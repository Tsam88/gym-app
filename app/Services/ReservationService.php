<?php

declare(strict_types = 1);

namespace App\Services;

use App\Exceptions\CancelOrDeclineReservationDateHasPassedException;
use App\Exceptions\DateIsAlreadyReservedException;
use App\Exceptions\NoActiveSubscriptionForTheRequestedDateException;
use App\Exceptions\ReservationAlreadyCanceledException;
use App\Exceptions\ReservationCancellationIsNotAllowedException;
use App\Exceptions\ReservationDateNotFoundException;
use App\Exceptions\ReservationIsDeclinedException;
use App\Exceptions\SessionsLimitExceededException;
use App\Exceptions\SessionsWeekLimitExceededException;
use App\Models\GymClass;
use App\Models\Reservation;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WeekDay;
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
     * @param array             $input Reservation data
     * @param User              $user
     * @param Subscription|null $activeSubscription
     *
     * @return Reservation
     */
    public function create(array $input, User $user, ?Subscription $activeSubscription): Reservation
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
            // check if reservation date has passed
            $isPastDate = $this->isPastDate($data['date']);
            if ($isPastDate) {
                throw new CancelOrDeclineReservationDateHasPassedException();
            }

            // check if the date is valid
            $isValidDate = $this->isValidDate($data['gym_class_id'], $data['date']);
            if (!$isValidDate) {
                throw new ReservationDateNotFoundException();
            }

            // check if date is already reserved
            $isAlreadyReserved = $this->isAlreadyReserved($data['user_id'], $data['gym_class_id'], $data['date']);
            if ($isAlreadyReserved) {
                throw new DateIsAlreadyReservedException();
            }

            // check if reservation is declined for this date
            $isDeclined = $this->isDeclined($data['user_id'], $data['gym_class_id'], $data['date']);
            if ($isDeclined) {
                throw new ReservationIsDeclinedException();
            }

            // if there is no active subscription for the requested date throw exception
            if (empty($activeSubscription)) {
                throw new NoActiveSubscriptionForTheRequestedDateException();
            }

            // check if subscription has no unlimited sessions
            if (!$activeSubscription->unlimited_sessions) {
                // check if subscription has sessions per week
                if ($activeSubscription->sessions_per_week) {
                    $now = Carbon::now('Europe/Athens');
                    $firstDayOfWeek = $now->startOfWeek(Carbon::MONDAY);
                    $lastDayOfWeek = $now->endOfWeek(Carbon::SUNDAY);

                    // count reserved week sessions
                    $reservedWeekSessions = Reservation::whereBetween('date', [$firstDayOfWeek, $lastDayOfWeek])
                        ->where('canceled', false)
                        ->where('declined', false)
                        ->count();

                    // check if sessions week limit has been exceeded
                    if ($reservedWeekSessions >= $activeSubscription->sessions_per_week) {
                        throw new SessionsWeekLimitExceededException();
                    }
                } else {
                    // check if there are remaining sessions
                    if ($activeSubscription->remaining_sessions > 0) {
                        // decrease remaining sessions by one
                        $activeSubscription->remaining_sessions--;
                        $activeSubscription->save();
                    } else {
                        // sessions limit has been exceeded
                        throw new SessionsLimitExceededException();
                    }
                }
            }

            $data['canceled'] = false;

            $reservation = Reservation::updateOrCreate(['user_id' => $data['user_id'], 'date' => $data['date']], $data);
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
     * @param Reservation       $reservation
     * @param Subscription|null $activeSubscription
     *
     * @return void
     */
    public function cancel(Reservation $reservation, ?Subscription $activeSubscription)
    {
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

            // if there is no active subscription for the requested date throw exception
            if (empty($activeSubscription)) {
                throw new NoActiveSubscriptionForTheRequestedDateException();
            }

            // increase remaining sessions by one, if subscription is based on remaining_sessions
            if (!$activeSubscription->unlimited_sessions && !$activeSubscription->sessions_per_week) {
                $activeSubscription->remaining_sessions++;
                $activeSubscription->save();
            }

            $reservation->canceled = true;
            $reservation->save();
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
     * @param Reservation       $reservation
     * @param Subscription|null $activeSubscription
     *
     * @return void
     */
    public function decline(Reservation $reservation, ?Subscription $activeSubscription)
    {
        // start db transaction
        DB::beginTransaction();

        try {
            // check if reservation date has passed
            $isPastDate = $this->isPastDate($reservation->date);
            if ($isPastDate) {
                throw new CancelOrDeclineReservationDateHasPassedException();
            }

            // check if the reservation is already canceled or declined
            if ($reservation->canceled || $reservation->declined) {
                throw new ReservationAlreadyCanceledException();
            }

            // if there is no active subscription for the requested date throw exception
            if (empty($activeSubscription)) {
                throw new NoActiveSubscriptionForTheRequestedDateException();
            }

            // increase remaining sessions by one, if subscription is based on remaining_sessions
            if (!$activeSubscription->unlimited_sessions && !$activeSubscription->sessions_per_week) {
                $activeSubscription->remaining_sessions++;
                $activeSubscription->save();
            }

            $reservation->declined = true;
            $reservation->save();
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
     * @param Reservation       $reservation
     * @param Subscription|null $activeSubscription
     *
     * @return void
     */
    public function delete(Reservation $reservation, Subscription $activeSubscription)
    {
        // start db transaction
        DB::beginTransaction();

        try {
            // if there is no active subscription for the requested date throw exception
            if (empty($activeSubscription)) {
                throw new NoActiveSubscriptionForTheRequestedDateException();
            }

            // increase remaining sessions by one, if subscription is based on remaining_sessions
            if (!$activeSubscription->unlimited_sessions && !$activeSubscription->sessions_per_week) {
                $activeSubscription->remaining_sessions++;
                $activeSubscription->save();
            }

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
     * check if date is already reserved
     *
     * @param int    $userId
     * @param int    $gymClassId
     * @param string $date
     *
     * @return bool
     */
    private function isAlreadyReserved(int $userId, int $gymClassId, string $date): bool
    {
        $isAlreadyReserved = Reservation::where('user_id', $userId)
            ->where('gym_class_id', $gymClassId)
            ->where('date', $date)
            ->where('canceled', false)
            ->where('declined', false)
            ->exists();

        return $isAlreadyReserved;
    }

    /**
     * check if reservation is declined for this date
     *
     * @param int    $userId
     * @param int    $gymClassId
     * @param string $date
     *
     * @return bool
     */
    private function isDeclined(int $userId, int $gymClassId, string $date): bool
    {
        $isDeclined = Reservation::where('user_id', $userId)
            ->where('gym_class_id', $gymClassId)
            ->where('date', $date)
            ->where('declined', true)
            ->exists();

        return $isDeclined;
    }

    /**
     * check if the date is valid
     *
     * @param int    $gymClassId
     * @param string $date
     *
     * @return bool
     */
    private function isValidDate(int $gymClassId, string $date): bool
    {
        $gymClass = GymClass::where('id', $gymClassId)->first();

        if ($gymClass) {
            $startTime = Carbon::parse($date, 'Europe/Athens')->format('H:i:s');
            $nameOfDay = strtoupper(Carbon::parse($date, 'Europe/Athens')->format('l'));

            $weekDay = WeekDay::where('gym_class_id', $gymClass->id)
                ->where('day', $nameOfDay)
                ->where('start_time', $startTime)
                ->exists();

            if ($weekDay) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * check if the date belongs in the past
     *
     * @param int    $gymClassId
     * @param string $date
     *
     * @return bool
     */
    private function isPastDate(string $date): bool
    {
        $now = Carbon::now('Europe/Athens');
        $reservationDate = Carbon::parse($date, 'Europe/Athens');

        // check if date has passed
        if ($now > $reservationDate) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get user's last reservation date
     *
     * @param int $userId
     *
     * @return Carbon|null
     */
    public function getLastReservationDate(int $userId): ?Carbon
    {
        $lastReservation = Reservation::where('user_id', $userId)
            ->where('canceled', false)
            ->where('declined', false)
            ->orderBy('date', 'DESC')
            ->first();

        $lastReservationDate = $lastReservation ? Carbon::parse($lastReservation->date) : null;

        return $lastReservationDate;
    }
}
