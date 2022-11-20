<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Validators\SubscriptionPlanValidation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanService
{
    /**
     * The default number of items per page
     *
     * @var int
     */
    private const DEFAULT_ITEMS_PER_PAGE = 0;

    /**
     * @var SubscriptionPlanValidation
     */
    private $subscriptionPlanValidation;

    public function __construct(SubscriptionPlanValidation $subscriptionPlanValidation)
    {
        $this->subscriptionPlanValidation = $subscriptionPlanValidation;
    }

    /**
     * Return all subscription plans.
     *
     * @param array $input
     *
     * @return LengthAwarePaginator
     */
    public function getSubscriptionPlans(array $input): LengthAwarePaginator
    {
        // data validation
        $data = $this->subscriptionPlanValidation->subscriptionPlanGetPlans($input);

        $itemsPerPage = $data['items_per_page'] ?? self::DEFAULT_ITEMS_PER_PAGE;
        $subscriptionPlans = SubscriptionPlan::orderBy('name')
            ->paginate($itemsPerPage);

        return $subscriptionPlans;
    }

    /**
     * Return all subscription plans that are allowed to display on page.
     *
     * @param array $input
     *
     * @return LengthAwarePaginator
     */
    public function getAllowedSubscriptionPlansOnPage(array $input): LengthAwarePaginator
    {
        // data validation
        $data = $this->subscriptionPlanValidation->subscriptionPlanGetPlans($input);

        $itemsPerPage = $data['items_per_page'] ?? self::DEFAULT_ITEMS_PER_PAGE;
        $subscriptionPlans = SubscriptionPlan::where('display_on_page', true)
            ->paginate($itemsPerPage);

        return $subscriptionPlans;
    }

    /**
     * Create a subscription plan.
     *
     * @param array $input SubscriptionPlan data
     *
     * @return SubscriptionPlan
     */
    public function create(array $input): SubscriptionPlan
    {
        // data validation
        $data = $this->subscriptionPlanValidation->subscriptionPlanCreate($input);

        // start db transaction
        DB::beginTransaction();

        try {
            $subscriptionPlan = SubscriptionPlan::create($data);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();

        return $subscriptionPlan;
    }

    /**
     * Update a subscription plan.
     *
     * @param array $input SubscriptionPlan data
     *
     * @return void
     */
    public function update(array $input, SubscriptionPlan $subscriptionPlan)
    {
        // data validation
        $data = $this->subscriptionPlanValidation->subscriptionPlanUpdate($input);

        // start db transaction
        DB::beginTransaction();

        try {
            $subscriptionPlan->update($data);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Delete subscription plan
     *
     * @param SubscriptionPlan $subscriptionPlan
     *
     * @return void
     */
    public function delete(SubscriptionPlan $subscriptionPlan)
    {
        // start db transaction
        DB::beginTransaction();

        try {
            $subscriptionPlan->delete();
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }
}
