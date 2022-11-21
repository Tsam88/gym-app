<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Models\ExcludedCalendarDate;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class ExcludedCalendarDatesHelper
{
    /**
     * Get excluded calendar dates for the given date range
     *
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    public function getExcludedCalendarDatesPerGymClassForTheGivenDateRange(string $startDate, string $endDate): array
    {
        $excludedCalendarDates = $this->getExcludedCalendarDatesForTheGivenDateRange($startDate, $endDate, false);

        $allExcludedCalendarDates = [];

        foreach ($excludedCalendarDates as $excludedCalendarDate) {
            // set 'Europe/Athens' timezone for start_date and end_date
            $excludedCalendarDateStartDate = Carbon::parse($excludedCalendarDate->start_date->format('Y-m-d'), 'Europe/Athens');
            $excludedCalendarDateEndDate = Carbon::parse($excludedCalendarDate->end_date->format('Y-m-d'), 'Europe/Athens');

            // set the date period
            $period = CarbonPeriod::create($excludedCalendarDateStartDate, $excludedCalendarDateEndDate);

            $gymClasses = $excludedCalendarDate->gymClasses()->get();

            // get the days of the period
            foreach ($period as $date) {
                foreach ($gymClasses as $gymClass) {
                    $allExcludedCalendarDates[$gymClass->id][] = $date->format('Y-m-d');
                }
            }
        }

        return $allExcludedCalendarDates;
    }

    /**
     * Get excluded calendar dates for the given date range
     *
     * @param string $startDate
     * @param string $endDate
     *
     * @return Collection
     */
    public function getExcludedCalendarDatesForTheGivenDateRange(string $startDate, string $endDate, bool $onlyWithExtendSubscription): Collection
    {
        $excludedCalendarDates = ExcludedCalendarDate::when($onlyWithExtendSubscription === true, function ($query) {
                $query->where('extend_subscription', true);
            })
            ->where(function ($query) use ($startDate, $endDate) {
                $query->WhereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
//                    ->orWhere(function ($query) use ($startDate, $endDate) {
//                        $query->where('start_date', '>=', $startDate)
//                            ->Where('end_date', '<=', $endDate);
//                    });
            })
            ->get();

        return $excludedCalendarDates;
    }
}
