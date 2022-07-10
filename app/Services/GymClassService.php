<?php

declare(strict_types = 1);

namespace App\Services;

use App\Exceptions\DeleteUserHasParcelsException;
use App\Exceptions\InternalServerErrorException;
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
use App\Validators\GymClassValidation;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class GymClassService
{
    /**
     * The default number of items per page
     *
     * @var int
     */
    private const DEFAULT_ITEMS_PER_PAGE = 0;

    /**
     * @var GymClassValidation
     */
    private $gymClassValidation;

    public function __construct(GymClassValidation $gymClassValidation)
    {
        $this->gymClassValidation = $gymClassValidation;
    }

    /**
     * Return all gym classes.
     *
     * @param array $input
     *
     * @return LengthAwarePaginator
     */
    public function getGymClasses(array $input): LengthAwarePaginator
    {
        // data validation
        $data = $this->gymClassValidation->gymClassGetClasses($input);

        $itemsPerPage = $data['items_per_page'] ?? self::DEFAULT_ITEMS_PER_PAGE;
        $gymClasses = GymClass::paginate($itemsPerPage);

        return $gymClasses;
    }

    /**
     * Create a gym class.
     *
     * @param array $input GymClass data
     *
     * @return GymClass
     */
    public function create(array $input): GymClass
    {
        // data validation
        $data = $this->gymClassValidation->gymClassCreate($input);

        // start db transaction
        DB::beginTransaction();

        try {
            $gymClass = GymClass::create($data);

            // create week days for gym class
            $gymClass->weekDays()->createMany($data['week_days']);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();

        return $gymClass;
    }

    /**
     * Update a subscription plan.
     *
     * @param array $input SubscriptionPlan data
     * @param GymClass $gymClass
     *
     * @return void
     */
    public function update(array $input, GymClass $gymClass)
    {
        // data validation
        $data = $this->gymClassValidation->gymClassUpdate($input);

        // start db transaction
        DB::beginTransaction();

        try {
            $gymClass->update($data);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Delete user.
     *
     * @param User $user
     *
     * @return void
     */
    public function delete(User $user)
    {
        // start db transaction
        DB::beginTransaction();

        try {
            if (0 < $user->parcels->count()) {
                throw new DeleteUserHasParcelsException();
            }

            $user->delete();
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }
}
