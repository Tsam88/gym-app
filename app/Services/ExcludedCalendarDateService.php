<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\ExcludedCalendarDate;
use App\Models\GymClass;
use App\Validators\ExcludedCalendarDateValidation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ExcludedCalendarDateService
{
    /**
     * The default number of items per page
     *
     * @var int
     */
    private const DEFAULT_ITEMS_PER_PAGE = 0;

    /**
     * @var ExcludedCalendarDateValidation
     */
    private $gymClassValidation;

    public function __construct(ExcludedCalendarDateValidation $excludedCalendarDateValidation)
    {
        $this->excludedCalendarDateValidation = $excludedCalendarDateValidation;
    }

    /**
     * Return all excluded calendar dates.
     *
     * @param array $input
     *
     * @return LengthAwarePaginator
     */
    public function getExcludedCalendarDates(array $input): LengthAwarePaginator
    {
        // data validation
        $data = $this->excludedCalendarDateValidation->excludedCalendarDateGetExcludedDates($input);

        $itemsPerPage = $data['items_per_page'] ?? self::DEFAULT_ITEMS_PER_PAGE;
        $excludedCalendarDates = ExcludedCalendarDate::with('gymClasses')
            ->paginate($itemsPerPage);

        return $excludedCalendarDates;
    }

    /**
     * Return specific excluded calendar date.
     *
     * @param ExcludedCalendarDate $excludedCalendarDate
     *
     * @return ExcludedCalendarDate
     */
    public function getExcludedCalendarDate(ExcludedCalendarDate $excludedCalendarDate): ExcludedCalendarDate
    {
        $excludedCalendarDate = ExcludedCalendarDate::with('gymClasses')
            ->findOrFail($excludedCalendarDate->id);

        return $excludedCalendarDate;
    }

    /**
     * Create an excluded calendar date.
     *
     * @param array $input ExcludedCalendarDate data
     *
     * @return ExcludedCalendarDate
     */
    public function create(array $input): ExcludedCalendarDate
    {
        // data validation
        $data = $this->excludedCalendarDateValidation->excludedCalendarDateCreate($input);

        // start db transaction
        DB::beginTransaction();

        try {
            $excludedCalendarDate = ExcludedCalendarDate::create($data);

            $gymClasses = GymClass::findOrFail($data['gym_class_ids']);

            $excludedCalendarDate->gymClasses()->attach($gymClasses);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();

        return $excludedCalendarDate;
    }

    /**
     * Update an excluded calendar date.
     *
     * @param array                $input ExcludedCalendarDate data
     * @param ExcludedCalendarDate $excludedCalendarDate
     *
     * @return void
     */
    public function update(array $input, ExcludedCalendarDate $excludedCalendarDate)
    {
        // data validation
        $data = $this->excludedCalendarDateValidation->excludedCalendarDateUpdate($input);

        // start db transaction
        DB::beginTransaction();

        try {
            $excludedCalendarDate->update($data);

            $gymClasses = GymClass::findOrFail($data['gym_class_ids']);

            $excludedCalendarDate->gymClasses()->sync($gymClasses);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Delete excluded calendar date
     *
     * @param ExcludedCalendarDate $excludedCalendarDate
     *
     * @return void
     */
    public function delete(ExcludedCalendarDate $excludedCalendarDate)
    {
        // start db transaction
        DB::beginTransaction();

        try {
            $excludedCalendarDate->delete();
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }
}
