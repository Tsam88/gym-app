<?php

declare(strict_types = 1);

namespace App\Services;

use App\Exceptions\DeleteUserHasParcelsException;
use App\Exceptions\NotActiveUserException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotVerifiedUserException;
use App\Libraries\Constants\LocationConstants;
use App\Models\GymClass;
use App\Models\Language;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSetting;
use App\Models\UserType;
use App\Rules\CheckPassword;
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
        $subscriptionPlans = GymClass::paginate($itemsPerPage);

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
//
//    /**
//     * Delete user.
//     *
//     * @param User $user
//     *
//     * @return void
//     */
//    public function delete(User $user)
//    {
//        // start db transaction
//        DB::beginTransaction();
//
//        try {
//            if (0 < $user->parcels->count()) {
//                throw new DeleteUserHasParcelsException();
//            }
//
//            $user->delete();
//        } catch (\Exception $e) {
//            // something went wrong, rollback and throw same exception
//            DB::rollBack();
//
//            throw $e;
//        }
//
//        // commit database changes
//        DB::commit();
//    }
}
