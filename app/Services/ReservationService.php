<?php

declare(strict_types = 1);

namespace App\Services;

use App\Exceptions\CancelOrDeclineReservationDateHasPassedException;
use App\Exceptions\ClassIsAlreadyReservedException;
use App\Exceptions\GymClassIsFullException;
use App\Exceptions\NoActiveSubscriptionForTheRequestedDateException;
use App\Exceptions\ReservationAlreadyCanceledException;
use App\Exceptions\ReservationCancellationIsNotAllowedException;
use App\Exceptions\ReservationDateCoincidesWithAnotherReservationException;
use App\Exceptions\ReservationDateNotFoundException;
use App\Exceptions\ReservationIsDeclinedException;
use App\Exceptions\SessionsLimitExceededException;
use App\Exceptions\SessionsWeekLimitExceededException;
use App\Libraries\ReservationGymClassHelper;
use App\Libraries\ReservationSubscriptionHelper;
use App\Models\GymClass;
use App\Models\Reservation;
use App\Models\User;
use App\Models\WeekDay;
use App\Validators\ReservationValidation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * @var ReservationSubscriptionHelper
     */
    private $reservationSubscriptionHelper;

    /**
     * @var ReservationGymClassHelper
     */
    private $reservationGymClassHelper;

    public function __construct(ReservationValidation $reservationValidation, ReservationSubscriptionHelper $reservationSubscriptionHelper, ReservationGymClassHelper $reservationGymClassHelper)
    {
        $this->reservationValidation = $reservationValidation;
        $this->reservationSubscriptionHelper = $reservationSubscriptionHelper;
        $this->reservationGymClassHelper = $reservationGymClassHelper;
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
     * @param User  $user
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
            // check if reservation date has passed
            $isPastDate = $this->isPastDate($data['date']);
            if ($isPastDate) {
                throw new CancelOrDeclineReservationDateHasPassedException();
            }

            // check if the date is valid
            $isValidDate = $this->isValidDate($data['week_day_id'], $data['date']);
            if (!$isValidDate) {
                throw new ReservationDateNotFoundException();
            }

            // check if date for specific gym class is already reserved
            $classIsAlreadyReserved = $this->classIsAlreadyReserved($data['user_id'], $data['gym_class_id'], $data['date']);
            if ($classIsAlreadyReserved) {
                throw new ClassIsAlreadyReservedException();
            }

            // check if date is already reserved
            $reservationDateCoincidesWithAnotherReservation = $this->reservationDateCoincidesWithAnotherReservation($data['user_id'], $data['date']);
            if ($reservationDateCoincidesWithAnotherReservation) {
                throw new ReservationDateCoincidesWithAnotherReservationException();
            }

            // check if reservation is declined for this date
            if ($user->isStudent) {
                $isDeclined = $this->isDeclined($data['user_id'], $data['gym_class_id'], $data['week_day_id'], $data['date']);
                if ($isDeclined) {
                    throw new ReservationIsDeclinedException();
                }
            }

            // check if the class is full
            $isGymClassFull = $this->reservationGymClassHelper->isGymClassFull($data['gym_class_id'], $data['week_day_id'], $data['date']);
            if ($isGymClassFull) {
                throw new GymClassIsFullException();
            }

            // get active subscription for reservation date
            $activeSubscription = $this->reservationSubscriptionHelper->getActiveSubscriptionForReservationDate($data['user_id'], $data['date']);

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
            $data['declined'] = false;

            $reservation = Reservation::updateOrCreate(['user_id' => $data['user_id'], 'date' => $data['date'], 'gym_class_id' => $data['gym_class_id']], $data);
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
     *
     * @return void
     */
    public function cancel(Reservation $reservation)
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

            // get active subscription for reservation date
            $activeSubscription = $this->reservationSubscriptionHelper->getActiveSubscriptionForReservationDate($reservation->user_id, $reservation->date);

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
     * @param Reservation $reservation
     *
     * @return void
     */
    public function decline(Reservation $reservation)
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

            // get active subscription for reservation date
            $activeSubscription = $this->reservationSubscriptionHelper->getActiveSubscriptionForReservationDate($reservation->user_id, $reservation->date);

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
     * @param Reservation $reservation
     *
     * @return void
     */
    public function delete(Reservation $reservation)
    {
        // start db transaction
        DB::beginTransaction();

        try {
            // get active subscription for reservation date
            $activeSubscription = $this->reservationSubscriptionHelper->getActiveSubscriptionForReservationDate($reservation->user_id, $reservation->date);

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
     * check if date for specific gym class is already reserved
     *
     * @param int    $userId
     * @param int    $gymClassId
     * @param string $date
     *
     * @return bool
     */
    private function classIsAlreadyReserved(int $userId, int $gymClassId, string $date): bool
    {
        $classIsAlreadyReserved = Reservation::where('user_id', $userId)
            ->where('gym_class_id', $gymClassId)
            ->where('date', $date)
            ->where('canceled', false)
            ->where('declined', false)
            ->exists();

        return $classIsAlreadyReserved;
    }

    /**
     * check if reservation date coincides with another reservation
     *
     * @param int    $userId
     * @param string $date
     *
     * @return bool
     */
    private function reservationDateCoincidesWithAnotherReservation(int $userId, string $date): bool
    {
        $reservationDateCoincidesWithAnotherReservation = Reservation::where('user_id', $userId)
            ->where('date', $date)
            ->where('canceled', false)
            ->where('declined', false)
            ->exists();

        return $reservationDateCoincidesWithAnotherReservation;
    }

    /**
     * check if reservation is declined for this date
     *
     * @param int    $userId
     * @param int    $gymClassId
     * @param int    $weekDayId
     * @param string $date
     *
     * @return bool
     */
    private function isDeclined(int $userId, int $gymClassId, int $weekDayId, string $date): bool
    {
        $isDeclined = Reservation::where('user_id', $userId)
            ->where('gym_class_id', $gymClassId)
            ->where('week_day_id', $weekDayId)
            ->where('date', $date)
            ->where('declined', true)
            ->exists();

        return $isDeclined;
    }

    /**
     * check if the date is valid
     *
     * @param int    $weekDayId
     * @param string $date
     *
     * @return bool
     */
    private function isValidDate(int $weekDayId, string $date): bool
    {
        $startTime = Carbon::parse($date, 'Europe/Athens')->format('H:i:s');
        $nameOfDay = strtoupper(Carbon::parse($date, 'Europe/Athens')->format('l'));

        $weekDay = WeekDay::where('id', $weekDayId)
            ->where('day', $nameOfDay)
            ->where('start_time', $startTime)
            ->exists();

        if ($weekDay) {
            return true;
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
     * get pending reservations of the selected gym class
     *
     * @param GymClass $gymClass
     *
     * @return Collection
     */
    public function getPendingReservationsOfGymClass(GymClass $gymClass): Collection
    {
        $now = Carbon::now('Europe/Athens');

        $pendingReservations = Reservation::where('gym_class_id', $gymClass->id)
            ->where('date', '>=', $now)
            ->where('canceled', false)
            ->where('declined', false)
            ->get();

        return $pendingReservations;
    }

    /**
     * get pending reservations of the selected gym class
     *
     * @param array $weekDayIds
     *
     * @return Collection
     */
    public function getPendingReservationsBasedOnWeekDays(array $weekDayIds): Collection
    {
        $now = Carbon::now('Europe/Athens');

        $pendingReservations = Reservation::whereIn('week_day_id', $weekDayIds)
            ->where('date', '>=', $now)
            ->where('canceled', false)
            ->where('declined', false)
            ->get();

        return $pendingReservations;
    }
}
