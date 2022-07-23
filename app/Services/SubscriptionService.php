<?php

declare(strict_types = 1);

namespace App\Services;

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

    public function __construct(SubscriptionValidation $subscriptionValidation)
    {
        $this->subscriptionValidation = $subscriptionValidation;
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
        $subscriptions = Subscription::paginate($itemsPerPage);

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
            $data['expires_at'] = Carbon::parse($data['starts_at'], 'Europe/Athens')->addMonths($subscriptionPlanData['number_of_months']);

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
     * @param array $input Subscription data
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
}
