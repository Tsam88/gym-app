<?php

declare(strict_types = 1);

namespace App\Services;

use App\Exceptions\ExcludedDatesCoincideWithAnotherExcludedDateException;
use App\Exceptions\ExcludedStartDateIsEarlierThanTodayException;
use App\Models\ExcludedCalendarDate;
use App\Models\GymClass;
use App\Validators\ExcludedCalendarDateValidation;
use Carbon\Carbon;
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
            $today = Carbon::today('Europe/Athens');
            $excludedStartDate = Carbon::parse($data['start_date'], 'Europe/Athens');

            // check if excluded start date is earlier than today
            if ($excludedStartDate < $today) {
                throw new ExcludedStartDateIsEarlierThanTodayException();
            }

            // check if the excluded calendar dates have conflict with existed excluded calendar dates, for the given gym classes
            $this->hasConflictWithOtherExcludedCalendarDateForTheGivenGymClasses($data['start_date'], $data['end_date'], $data['gym_class_ids'], null);

            // create excluded calendar dates
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
            $today = Carbon::today('Europe/Athens');
            $excludedStartDate = Carbon::parse($data['start_date'], 'Europe/Athens');

            // check if excluded start date is earlier than today
            if ($excludedStartDate < $today) {
                throw new ExcludedStartDateIsEarlierThanTodayException();
            }

            // check if the excluded calendar dates have conflict with existed excluded calendar dates, for the given gym classes
            $this->hasConflictWithOtherExcludedCalendarDateForTheGivenGymClasses($data['start_date'], $data['end_date'], $data['gym_class_ids'], $excludedCalendarDate->id);

            // update excluded calendar dates
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

    /**
     * Delete excluded calendar date
     *
     * @param string    $excludedStartDate
     * @param string    $excludedEndDate
     * @param array     $gymClassIds
     * @param int|null  $excludedCalendarDateIdToIgnore
     *
     * @return void
     */
    public function hasConflictWithOtherExcludedCalendarDateForTheGivenGymClasses(string $excludedStartDate, string $excludedEndDate, array $gymClassIds, ?int $excludedCalendarDateIdToIgnore): void
    {
        foreach ($gymClassIds as $gymClassId) {
            $hasConflictWithOtherExcludedCalendarDate = ExcludedCalendarDate::whereHas('gymClasses', function ($query) use ($gymClassId) {
                    return $query->where('id', $gymClassId);
                })
                ->where(function ($query) use ($excludedStartDate, $excludedEndDate) {
                    $query->WhereBetween('start_date', [$excludedStartDate, $excludedEndDate])
                        ->orWhereBetween('end_date', [$excludedStartDate, $excludedEndDate])
                        ->orWhere(function ($query) use ($excludedStartDate, $excludedEndDate) {
                            $query->where('start_date', '<=', $excludedStartDate)
                                ->Where('end_date', '>=', $excludedEndDate);
                        });
                })
                ->when(!empty($excludedCalendarDateIdToIgnore), function ($query) use ($excludedCalendarDateIdToIgnore) {
                    $query->where('id', '!=', $excludedCalendarDateIdToIgnore);
                })
                ->exists();

            if ($hasConflictWithOtherExcludedCalendarDate) {
                $gymClass = GymClass::where('id', $gymClassId)->first();

                throw new ExcludedDatesCoincideWithAnotherExcludedDateException($gymClass->name);
            }
        }
    }
}
