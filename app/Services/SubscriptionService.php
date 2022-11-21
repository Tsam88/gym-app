<?php

declare(strict_types = 1);

namespace App\Services;

use App\Exceptions\ExcludedStartDateIsEarlierThanTodayException;
use App\Exceptions\UserAlreadyHasSubscriptionOnTheseDatesException;
use App\Libraries\ExcludedCalendarDatesHelper;
use App\Libraries\ReservationSubscriptionHelper;
use App\Models\ExcludedCalendarDate;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Validators\SubscriptionValidation;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * The default number of items per page
     *
     * @var int
     */
    private const DEFAULT_ITEMS_PER_PAGE = 0;

    /**
     * @var SubscriptionValidation
     */
    private $subscriptionValidation;

    /**
     * @var ReservationSubscriptionHelper
     */
    private $reservationSubscriptionHelper;


    /**
     * @var ExcludedCalendarDatesHelper
     */
    private $excludedCalendarDatesHelper;

    public function __construct(SubscriptionValidation $subscriptionValidation, ReservationSubscriptionHelper $reservationSubscriptionHelper, ExcludedCalendarDatesHelper $excludedCalendarDatesHelper)
    {
        $this->subscriptionValidation = $subscriptionValidation;
        $this->reservationSubscriptionHelper = $reservationSubscriptionHelper;
        $this->excludedCalendarDatesHelper = $excludedCalendarDatesHelper;
    }

    /**
     * Return all subscriptions
     *
     * @param array $input
     *
     * @return LengthAwarePaginator
     */
    public function getSubscriptions(array $input): LengthAwarePaginator
    {
        // data validation
        $data = $this->subscriptionValidation->subscriptionGetSubscriptions($input);

        $itemsPerPage = $data['items_per_page'] ?? self::DEFAULT_ITEMS_PER_PAGE;
        $subscriptions = Subscription::select('subscriptions.*', 'users.name as user_name', 'users.surname as user_surname', 'users.phone_number as user_phone_number')
            ->join('users', 'users.id', '=', 'subscriptions.user_id')
            ->orderBy('users.name', 'ASC')
            ->orderBy('users.surname', 'ASC')
            ->orderBy('subscriptions.id', 'DESC')
            ->paginate($itemsPerPage);

        // set is_active attribute per subscription
        $activeSubscriptionIdPerUser = [];
        foreach ($subscriptions as &$subscription) {
            if (!array_key_exists($subscription->user_id, $activeSubscriptionIdPerUser)) {
                $activeSubscription = $this->reservationSubscriptionHelper->getClosestActiveSubscription($subscription->user_id);
                $activeSubscriptionIdPerUser[$subscription->user_id] = $activeSubscription ? $activeSubscription->id : null;
            }

            if ($activeSubscriptionIdPerUser[$subscription->user_id] !== $subscription->id) {
                $subscription->is_active = false;
            } else {
                $subscription->is_active = true;
            }
        }

        return $subscriptions;
    }

    /**
     * Create a subscription
     *
     * @param array $input Subscription data
     *
     * @return Subscription
     */
    public function create(array $input): Subscription
    {
        // data validation
        $data = $this->subscriptionValidation->subscriptionCreate($input);

        // start db transaction
        DB::beginTransaction();

        try {
            $subscriptionPlanData = SubscriptionPlan::findOrFail($data['subscription_plan_id']);

            $data['price'] = $subscriptionPlanData['plan_price'];
            $data['remaining_sessions'] = $subscriptionPlanData['number_of_sessions'];
            $data['unlimited_sessions'] = $subscriptionPlanData['unlimited_sessions'];
            $data['sessions_per_week'] = $subscriptionPlanData['sessions_per_week'];
            $data['expires_at'] = Carbon::parse($data['starts_at'], 'Europe/Athens')->addMonths($subscriptionPlanData['number_of_months'])->format('Y-m-d');

            // check if there is another subscription for these dates
            $hasSubscriptionDateConflict = $this->reservationSubscriptionHelper->hasSubscriptionDateConflict($data['user_id'], $data['starts_at'], $data['expires_at'], null);

            // check if there is already active subscription
            if ($hasSubscriptionDateConflict) {
                throw new UserAlreadyHasSubscriptionOnTheseDatesException();
            }

            $subscription = Subscription::create($data);

            // extend subscription, based on every excluded calendar date range that exist in db
            $this->extendNewSubscriptionIfExcludedCalendarDatesAlreadyExist($subscription);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();

        return $subscription;
    }

    /**
     * Update a subscription
     *
     * @param array        $input Subscription data
     * @param Subscription $subscription
     *
     * @return void
     */
    public function update(array $input, Subscription $subscription)
    {
        // data validation
        $data = $this->subscriptionValidation->subscriptionUpdate($input);

        // start db transaction
        DB::beginTransaction();

        try {
            // check if there is another subscription for these dates
            $hasSubscriptionDateConflict = $this->reservationSubscriptionHelper->hasSubscriptionDateConflict($data['user_id'], $data['starts_at'], $data['expires_at'], $subscription->id);

            // check if there is already active subscription
            if ($hasSubscriptionDateConflict) {
                throw new UserAlreadyHasSubscriptionOnTheseDatesException();
            }

            $subscription->update($data);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Delete subscription
     *
     * @param Subscription $subscription
     *
     * @return void
     */
    public function delete(Subscription $subscription)
    {
        // start db transaction
        DB::beginTransaction();

        try {
            $subscription->delete();
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Check if the subscription is active
     *
     * @param Subscription $subscription
     *
     * @return bool
     */
    public function isActiveSubscription(Subscription $subscription): bool
    {
        $now = Carbon::now('Europe/Athens');
//        $dateLimit = clone $now->subHours(Reservation::ALLOWED_HOURS_TO_CANCEL_BEFORE_RESERVATION_DATE);
        $startsAt = Carbon::parse($subscription->starts_at->format('Y-m-d'), 'Europe/Athens');
        $expiresAt = Carbon::parse($subscription->expires_at->format('Y-m-d'), 'Europe/Athens');
        $lastReservationDate = $this->reservationSubscriptionHelper->getLastReservationDate($subscription->user_id);

        if ($now >= $startsAt && $now <= $expiresAt
            && ($subscription->unlimited_sessions || $subscription->remaining_sessions > 0
                || ($subscription->remaining_sessions <= 0 && !empty($lastReservationDate)))) {
//                || ($subscription->remaining_sessions <= 0 && !empty($lastReservationDate) && $lastReservationDate <= $dateLimit))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Extend subscription based on "excluded calendar dates"
     *
     * @param string $excludedStartDate
     * @param string $excludedEndDate
     *
     * @return void
     */
    public function extendSubscriptions(string $excludedStartDate, string $excludedEndDate): void
    {
        $today = Carbon::today('Europe/Athens');
        $excludedStartDate = Carbon::parse($excludedStartDate, 'Europe/Athens');
        $excludedEndDate = Carbon::parse($excludedEndDate, 'Europe/Athens');

        // check if excluded start date is earlier than today
        if ($excludedStartDate < $today) {
            throw new ExcludedStartDateIsEarlierThanTodayException();
        }

        // get subscriptions where starts_at is earlier than excludedStartDate and expires_at is later than excludedEndDate
        $subscriptionsSafeToExtend = Subscription::where('starts_at', '<=', $excludedStartDate)
            ->where('expires_at', '>=', $excludedEndDate)
            ->get();

        // get subscriptions where starts_at is between excludedStartDate and excludedEndDate.
        // also expires_at is later than excludedEndDate
        $subscriptionsWithStartsAtInExcludedDates = Subscription::where('starts_at', '>=', $excludedStartDate)
            ->where('starts_at', '<=', $excludedEndDate)
            ->where('expires_at', '>=', $excludedEndDate)
            ->get();

        // get subscriptions where expires_at is between excludedStartDate and excludedEndDate.
        // also starts_at is earlier than excludedEndDate
        $subscriptionsWithExpiresAtInExcludedDates = Subscription::where('expires_at', '>=', $excludedStartDate)
            ->where('expires_at', '<=', $excludedEndDate)
            ->where('starts_at', '<=', $excludedStartDate)
            ->get();

        // get subscriptions where starts_at and expires_at are between excludedStartDate and excludedEndDate.
        $subscriptionsWithStartsAtAndExpiresAtInExcludedDates = Subscription::where('expires_at', '>=', $excludedStartDate)
            ->where('expires_at', '<=', $excludedEndDate)
            ->where('starts_at', '>=', $excludedStartDate)
            ->where('starts_at', '<=', $excludedEndDate)
            ->get();

        foreach ($subscriptionsSafeToExtend as $subscription) {
            // set 'Europe/Athens' timezone for expires_at of subscription
            $subscriptionExpiresAt = Carbon::parse($subscription->expires_at->format('Y-m-d'), 'Europe/Athens');

            $daysToExtend = $excludedEndDate->diffInDays($excludedStartDate) + 1;
            $subscription->expires_at = $subscriptionExpiresAt->addDays($daysToExtend);
            $subscription->save();
        }

        foreach ($subscriptionsWithStartsAtInExcludedDates as $subscription) {
            // set 'Europe/Athens' timezone for starts_at and expires_at of subscription
            $subscriptionStartsAt = Carbon::parse($subscription->starts_at->format('Y-m-d'), 'Europe/Athens');
            $subscriptionExpiresAt = Carbon::parse($subscription->expires_at->format('Y-m-d'), 'Europe/Athens');

            $daysToExtend = $excludedEndDate->diffInDays($subscriptionStartsAt) + 1;
            $subscription->expires_at = $subscriptionExpiresAt->addDays($daysToExtend);
            $subscription->save();
        }

        foreach ($subscriptionsWithExpiresAtInExcludedDates as $subscription) {
            // set 'Europe/Athens' timezone for expires_at of subscription
            $subscriptionExpiresAt = Carbon::parse($subscription->expires_at->format('Y-m-d'), 'Europe/Athens');

            $daysToExtend = $subscriptionExpiresAt->diffInDays($excludedStartDate) + 1;
            $newExpirationDate = clone $excludedEndDate;
            $subscription->expires_at = $newExpirationDate->addDays($daysToExtend);
            $subscription->save();
        }

        foreach ($subscriptionsWithStartsAtAndExpiresAtInExcludedDates as $subscription) {
            // set 'Europe/Athens' timezone for starts_at and expires_at of subscription
            $subscriptionStartsAt = Carbon::parse($subscription->starts_at->format('Y-m-d'), 'Europe/Athens');
            $subscriptionExpiresAt = Carbon::parse($subscription->expires_at->format('Y-m-d'), 'Europe/Athens');

            $daysToExtend = $subscriptionExpiresAt->diffInDays($subscriptionStartsAt) + 1;
            $newExpirationDate = clone $excludedEndDate;
            $subscription->expires_at = $newExpirationDate->addDays($daysToExtend);
            $subscription->save();
        }
    }

    /**
     * Remove days of subscription based on "excluded calendar dates"
     *
     * @param string $excludedStartDate
     * @param string $excludedEndDate
     *
     * @return void
     */
    public function removeDaysExtensionFromSubscriptions(string $excludedStartDate, string $excludedEndDate): void
    {
        $today = Carbon::today('Europe/Athens');
        $excludedStartDate = Carbon::parse($excludedStartDate, 'Europe/Athens');
        $excludedStartDate = max($today, $excludedStartDate);
        $excludedEndDate = Carbon::parse($excludedEndDate, 'Europe/Athens');

        // check if excluded end date is earlier than today
        if ($excludedEndDate < $today) {
            return;
        }

        // get subscriptions where excludedEndDate is between starts_at and expires_at
        $subscriptions = Subscription::where('starts_at', '<=', $excludedEndDate)
            ->where('expires_at', '>=', $excludedEndDate)
            ->get();

        foreach ($subscriptions as $subscription) {
            // set 'Europe/Athens' timezone for starts_at and expires_at of subscription
            $subscriptionStartsAt = Carbon::parse($subscription->starts_at->format('Y-m-d'), 'Europe/Athens');
            $subscriptionExpiresAt = Carbon::parse($subscription->expires_at->format('Y-m-d'), 'Europe/Athens');

            $maxStartDate = max($excludedStartDate, $subscriptionStartsAt);
            $daysToRemove = $excludedEndDate->diffInDays($maxStartDate) + 1;

            $subscription->expires_at = $subscriptionExpiresAt->subDays($daysToRemove);
            $subscription->save();
        }
    }

    /**
     * Extend (a new) subscription if "excluded calendar dates" already exist
     *
     * @param Subscription $subscription
     *
     * @return void
     */
    public function extendNewSubscriptionIfExcludedCalendarDatesAlreadyExist(Subscription $subscription): void
    {
        $excludedCalendarDates = $this->excludedCalendarDatesHelper->getExcludedCalendarDatesForTheGivenDateRange($subscription->starts_at->format('Y-m-d'), $subscription->expires_at->format('Y-m-d'), true);

        $today = Carbon::today('Europe/Athens');
        $daysToExtend = 0;

        // set 'Europe/Athens' timezone for starts_at and expires_at of subscription
        $subscriptionStartsAt = Carbon::parse($subscription->starts_at->format('Y-m-d'), 'Europe/Athens');
        $subscriptionExpiresAt = Carbon::parse($subscription->expires_at->format('Y-m-d'), 'Europe/Athens');

        // calculate the days to extend for every "excluded calendar date" range
        foreach ($excludedCalendarDates as $excludedCalendarDate) {
            $excludedCalendarDateStartDate = Carbon::parse($excludedCalendarDate->start_date->format('Y-m-d'), 'Europe/Athens');
            $excludedCalendarDateEndDate = Carbon::parse($excludedCalendarDate->end_date->format('Y-m-d'), 'Europe/Athens');

            $maxStartDate = max($excludedCalendarDateStartDate, $subscriptionStartsAt, $today);
            $minEndDate = min($excludedCalendarDateEndDate, $subscriptionExpiresAt);

            $daysToExtend += $minEndDate->diffInDays($maxStartDate) + 1;
        }

        // extend subscription
        $newExpirationDate = $subscriptionExpiresAt->addDays($daysToExtend);

        // check if the new expiration date is included in "excluded calendar dates" range
        $excludedCalendarDateForNewExpirationDate = ExcludedCalendarDate::where('extend_subscription', true)
            ->where('start_date', '<=', $newExpirationDate)
            ->where('end_date', '>=', $newExpirationDate)
            ->first();

        // if the new expiration date is included in "excluded calendar dates" range, then add the extension days to the end_date of the "excluded calendar dates"
        if ($excludedCalendarDateForNewExpirationDate) {
            $excludedCalendarDateEndDateForNewExpirationDate = Carbon::parse($excludedCalendarDateForNewExpirationDate->end_date->format('Y-m-d'), 'Europe/Athens');

            $newExpirationDate = $excludedCalendarDateEndDateForNewExpirationDate->end_date->addDays($daysToExtend);
        }

        $subscription->expires_at = $newExpirationDate;
        $subscription->save();
    }
}
