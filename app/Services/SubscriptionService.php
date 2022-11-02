<?php

declare(strict_types = 1);

namespace App\Services;

use App\Exceptions\UserAlreadyHasSubscriptionOnTheseDatesException;
use App\Libraries\ReservationSubscriptionHelper;
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

    public function __construct(SubscriptionValidation $subscriptionValidation, ReservationSubscriptionHelper $reservationSubscriptionHelper)
    {
        $this->subscriptionValidation = $subscriptionValidation;
        $this->reservationSubscriptionHelper = $reservationSubscriptionHelper;
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
}
