<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\GymClass;
use App\Models\Reservation;
use App\Models\User;
use App\Models\WeekDay;
use App\Validators\WeekDayValidation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class WeekDayService
{
    /**
     * The default number of items per page
     *
     * @var int
     */
    private const DEFAULT_ITEMS_PER_PAGE = 0;

    /**
     * @var WeekDayValidation
     */
    private $weekDayValidation;

    public function __construct(WeekDayValidation $weekDayValidation)
    {
        $this->weekDayValidation = $weekDayValidation;
    }

    /**
     * Return week calendar
     *
     * @return array
     */
    public function getWeekCalendar(): array
    {

        $weekDays = WeekDay::orderBy('start_time')
            ->get();

        $weekCalendar = WeekDay::WEEK_DAYS;

        foreach ($weekDays as $weekDay) {
            $weekCalendar[$weekDay->day][] = [
                'gym_class_name' => $weekDay->gymClass->name,
                'start_time' => $weekDay->start_time,
                'end_time' => $weekDay->end_time,
            ];
        }

//        $response = new Response($weekCalendar, Response::HTTP_OK);

        return $weekCalendar;
    }

    /**
     * Return calendar
     *
     * @param User $user
     *
     * @return array
     */
    public function getCalendar(User $user): array
    {
        // if the logged-in user is not student, then set user as null, so we can get the subscriptions of all students
        if (!$user->isStudent) {
            $user = null;
        }

        $weekDays = WeekDay::with('gymClass')
            ->orderBy('start_time')
            ->get()
            ->toArray();

        foreach ($weekDays as $weekDay) {
            $weekCalendar[$weekDay['day']][] = [
                'gym_class_id' => $weekDay['gym_class_id'],
                'week_day_id' => $weekDay['id'],
                'gym_class_name' => $weekDay['gym_class']['name'],
                'start_time' => $weekDay['start_time'],
                'end_time' => $weekDay['end_time'],
            ];
        }

        $fromDate = Carbon::now('Europe/Athens');
        $toDate = Carbon::today('Europe/Athens')->endOfMonth();
        $period = CarbonPeriod::create($fromDate, $toDate);

        $calendar = [];
        foreach ($period as $date) {
            $calendar[$date->format('Y-m-d')] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->dayName,
                'month_name' => $date->format('M'),
                'date_number' => $date->day,
                'gym_classes' => [],
                'disabled' => false,
            ];

            $dayName = strtoupper($date->dayName);

            if (!isset($weekCalendar[$dayName])) {
                continue;
            }

            $dailyGymClasses = [];

            foreach ($weekCalendar[$dayName] as $key => $gymClass) {
                $gymClassDateTime = Carbon::parse("{$date->format('Y-m-d')} {$gymClass['start_time']}");

                $dailyGymClasses[$key] = [
                    'gym_class_id' => $gymClass['gym_class_id'],
                    'week_day_id' => $gymClass['week_day_id'],
                    'gym_class_name' => $gymClass['gym_class_name'],
                    'start_time' => $gymClass['start_time'],
                    'end_time' => $gymClass['end_time'],
                    'users' => [],
                ];

                if ($user) {
                    $dailyGymClasses[$key]['users'] = [
                        'user_id' => $user->id,
                        'has_reservation' => false,
                        'canceled' => false,
                        'declined' => false,
                    ];
                }

                $reservations = Reservation::when($user, function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->where('gym_class_id', $gymClass['gym_class_id'])
                    ->where('week_day_id', $gymClass['week_day_id'])
                    ->where('date', $gymClassDateTime)
                    ->get();

                foreach ($reservations as $reservation) {
                    $dailyGymClasses[$key]['users'][] = [
                        'user_id' => $reservation->user_id,
                        'has_reservation' => true,
                        'canceled' => $reservation->canceled,
                        'declined' => $reservation->declined,
                    ];
                }
            }

            $calendar[$date->format('Y-m-d')]['day_name'] = $date->dayName;
            $calendar[$date->format('Y-m-d')]['month_name'] = $date->format('M');
            $calendar[$date->format('Y-m-d')]['date_number'] = $date->day;
            $calendar[$date->format('Y-m-d')]['gym_classes'] = $dailyGymClasses;
            $calendar[$date->format('Y-m-d')]['disabled'] = false;

            while (count($calendar) % 7 !== 0) {
                $lastDate = array_key_last ($calendar);
                $nextDate = Carbon::parse($lastDate)->addDay();

                $calendar[$nextDate->format('Y-m-d')] = [
                    'date' => $nextDate->format('Y-m-d'),
                    'day_name' => $nextDate->dayName,
                    'month_name' => $nextDate->format('M'),
                    'date_number' => $nextDate->day,
                    'gym_classes' => [],
                    'disabled' => true,
                ];
            }
        }

        return array_values($calendar);
    }

    /**
     * Create week days for a gym class.
     *
     * @param array $input WeekDay data
     *
     * @return void
     */
    public function createMany(array $input): void
    {
        // data validation
        $data = $this->weekDayValidation->weekDayCreate($input);

        // start db transaction
        DB::beginTransaction();

        try {
            WeekDay::insert($data);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Delete week days.
     *
     * @param array $input $weekDayIds
     *
     * @return void
     */
    public function deleteMany(array $weekDayIds): void
    {
        // start db transaction
        DB::beginTransaction();

        try {
            WeekDay::whereIn('id', $weekDayIds)->delete();
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Get the valid new week days to create, by comparing the old week days with new ones
     *
     * @param array    $newWeekDays
     * @param GymClass $gymClass
     *
     * @return array
     */
    public function getWeekDaysToCreate(array $newWeekDays, GymClass $gymClass): array
    {
        $oldWeekDays = WeekDay::where('gym_class_id', $gymClass->id)
            ->get()
            ->toArray();

        // find week days to create
        foreach ($newWeekDays as $key => $newWeekDay) {
            foreach ($oldWeekDays as $oldWeekDay) {
                $tempOldWeekDay = [
                    'day' => $oldWeekDay['day'],
                    'start_time' => $oldWeekDay['start_time'],
                    'end_time' => $oldWeekDay['end_time'],
                ];

                if ($tempOldWeekDay === $newWeekDay) {
                    unset($newWeekDays[$key]);
                }
            }
        }

        return $newWeekDays;
    }

    /**
     * Get the valid new week days to create, by comparing the old week days with new ones
     *
     * @param array    $newWeekDays
     * @param GymClass $gymClass
     *
     * @return array
     */
    public function getWeekDayIdsToDelete(array $newWeekDays, GymClass $gymClass): array
    {
        $weekDayIdsToDelete = [];

        $oldWeekDays = WeekDay::where('gym_class_id', $gymClass->id)
            ->get()
            ->toArray();

        // find week days to delete
        foreach ($oldWeekDays as $key => $oldWeekDay) {
            $tempOldWeekDay = [
                'day' => $oldWeekDay['day'],
                'start_time' => $oldWeekDay['start_time'],
                'end_time' => $oldWeekDay['end_time'],
            ];

            foreach ($newWeekDays as $newWeekDay) {
                if ($tempOldWeekDay === $newWeekDay) {
                    unset($oldWeekDays[$key]);
                }
            }
        }

        foreach ($oldWeekDays as $oldWeekDay) {
            $weekDayIdsToDelete[] = $oldWeekDay['id'];
        }

        return $weekDayIdsToDelete;
    }
}
