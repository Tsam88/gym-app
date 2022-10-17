<?php

declare(strict_types = 1);

namespace App\Services;

use App\Libraries\ReservationGymClassHelper;
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
    private const CALENDAR_TO_DATE_ON_LAST_WEEK_ADD_DAYS = 7;

    /**
     * @var WeekDayValidation
     */
    private $weekDayValidation;

    /**
     * @var ReservationGymClassHelper
     */
    private $reservationGymClassHelper;


    public function __construct(WeekDayValidation $weekDayValidation, ReservationGymClassHelper $reservationGymClassHelper)
    {
        $this->weekDayValidation = $weekDayValidation;
        $this->reservationGymClassHelper = $reservationGymClassHelper;
    }

//    /**
//     * Return week calendar
//     *
//     * @return array
//     */
//    public function getWeekCalendar(): array
//    {
//
//        $weekDays = WeekDay::orderBy('start_time')
//            ->get();
//
//        $weekCalendar = WeekDay::WEEK_DAYS;
//
//        foreach ($weekDays as $weekDay) {
//            $weekCalendar[$weekDay->day][] = [
//                'gym_class_name' => $weekDay->gymClass->name,
//                'start_time' => $weekDay->start_time,
//                'end_time' => $weekDay->end_time,
//            ];
//        }
//
////        $response = new Response($weekCalendar, Response::HTTP_OK);
//
//        return $weekCalendar;
//    }

    /**
     * Get week calendar
     *
     * @return array
     */
    public function getWeekCalendar(): array
    {
        $weekDays = WeekDay::with('gymClass')
            ->orderBy('start_time')
            ->get()
            ->toArray();

        $weekCalendar = WeekDay::WEEK_DAYS;

        foreach ($weekDays as $weekDay) {
            $weekCalendar[$weekDay['day']][] = [
                'gym_class_id' => $weekDay['gym_class_id'],
                'week_day_id' => $weekDay['id'],
                'gym_class_name' => $weekDay['gym_class']['name'],
                'number_of_students' => $weekDay['gym_class']['number_of_students'],
                'teacher' => $weekDay['gym_class']['teacher'],
                'description' => $weekDay['gym_class']['description'],
                'start_time' => $weekDay['start_time'],
                'end_time' => $weekDay['end_time'],
            ];
        }

        return $weekCalendar;
    }

    /**
     * Return calendar
     *
     * @param User $user
     *
     * @return array
     */
    public function getAdminCalendar(): array
    {
        $weekCalendar = $this->getWeekCalendar();
        $period = $this->getPeriodForCalendar();

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
                $gymClassDateTimeString = "{$date->format('Y-m-d')} {$gymClass['start_time']}";
                $gymClassDateTime = Carbon::parse($gymClassDateTimeString);

                $dailyGymClasses[$key] = [
                    'gym_class_id' => $gymClass['gym_class_id'],
                    'week_day_id' => $gymClass['week_day_id'],
                    'gym_class_name' => $gymClass['gym_class_name'],
                    'number_of_students_limit' => $gymClass['number_of_students'],
                    'teacher' => $gymClass['teacher'],
                    'description' => $gymClass['description'],
                    'start_time' => $gymClass['start_time'],
                    'end_time' => $gymClass['end_time'],
                    'number_of_reservations' => $this->reservationGymClassHelper->countGymClassReservations($gymClass['gym_class_id'], $gymClass['week_day_id'], $gymClassDateTimeString),
                ];

                $reservations = Reservation::with('user')
                    ->where('gym_class_id', $gymClass['gym_class_id'])
                    ->where('week_day_id', $gymClass['week_day_id'])
                    ->where('date', $gymClassDateTime)
                    ->where('canceled', false)
                    ->get();

                $dailyGymClasses[$key]['users'] = [];

                foreach ($reservations as $reservation) {
                    $dailyGymClasses[$key]['users'][] = [
                        'user_id' => $reservation->user_id,
                        'user_name' => $reservation->user->name,
                        'user_surname' => $reservation->user->surname,
                        'reservation_id' => $reservation->id,
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
        }

        $calendar = $this->fillMissingDatesOfCalendar($calendar);

        return $calendar;
    }

    /**
     * Return calendar
     *
     * @param User $user
     *
     * @return array
     */
    public function getStudentCalendar(User $user): array
    {
        $weekCalendar = $this->getWeekCalendar();
        $period = $this->getPeriodForCalendar();

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
                $gymClassDateTimeString = "{$date->format('Y-m-d')} {$gymClass['start_time']}";
                $gymClassDateTime = Carbon::parse($gymClassDateTimeString);

                $dailyGymClasses[$key] = [
                    'gym_class_id' => $gymClass['gym_class_id'],
                    'week_day_id' => $gymClass['week_day_id'],
                    'gym_class_name' => $gymClass['gym_class_name'],
                    'number_of_students_limit' => $gymClass['number_of_students'],
                    'teacher' => $gymClass['teacher'],
                    'description' => $gymClass['description'],
                    'start_time' => $gymClass['start_time'],
                    'end_time' => $gymClass['end_time'],
                    'number_of_reservations' => $this->reservationGymClassHelper->countGymClassReservations($gymClass['gym_class_id'], $gymClass['week_day_id'], $gymClassDateTimeString),
                ];

                $reservation = Reservation::with('user')
                    ->where('user_id', $user->id)
                    ->where('gym_class_id', $gymClass['gym_class_id'])
                    ->where('week_day_id', $gymClass['week_day_id'])
                    ->where('date', $gymClassDateTime)
                    ->first();

                // get student's reservation
                $dailyGymClasses[$key]['user'] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_surname' => $user->surname,
                    'reservation_id' => null,
                    'has_reservation_record' => false,
                    'has_active_reservation' => false,
                    'canceled' => false,
                    'declined' => false,
                ];

                if (!empty($reservation)) {
                    $dailyGymClasses[$key]['user']['reservation_id'] = $reservation->id;
                    $dailyGymClasses[$key]['user']['has_reservation_record'] = true;
                    $dailyGymClasses[$key]['user']['has_active_reservation'] = $reservation->canceled === false && $reservation->declined === false;
                    $dailyGymClasses[$key]['user']['canceled'] = $reservation->canceled;
                    $dailyGymClasses[$key]['user']['declined'] = $reservation->declined;
                }
            }

            $calendar[$date->format('Y-m-d')]['day_name'] = $date->dayName;
            $calendar[$date->format('Y-m-d')]['month_name'] = $date->format('M');
            $calendar[$date->format('Y-m-d')]['date_number'] = $date->day;
            $calendar[$date->format('Y-m-d')]['gym_classes'] = $dailyGymClasses;
            $calendar[$date->format('Y-m-d')]['disabled'] = false;
        }

        $calendar = $this->fillMissingDatesOfCalendar($calendar);

        return $calendar;
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

    /**
     * Get calendar's date period (range of dates)
     *
     * @return CarbonPeriod
     */
    private function getPeriodForCalendar(): CarbonPeriod
    {
        $lastDateOfMonth = Carbon::today()->endOfMonth()->day;
        $fromDate = Carbon::now('Europe/Athens');
        // if fromDate (current date) does not belong in the last 7 days of the month,
        // then set as toDate the last date of the month
        // else set as toDate the last day of the first week of the next month
        if ($fromDate->day <= $lastDateOfMonth - 7 ) {
            $toDate = Carbon::today('Europe/Athens')->endOfMonth();
        } else {
            $toDate = Carbon::today('Europe/Athens')->endOfMonth()->addDays(self::CALENDAR_TO_DATE_ON_LAST_WEEK_ADD_DAYS);
        }

        $period = CarbonPeriod::create($fromDate, $toDate);

        return $period;
    }

    /**
     * Fill the missing dates at the start and at the end of the calendar
     *
     * @param array $calendar
     *
     * @return array
     */
    private function fillMissingDatesOfCalendar(array $calendar): array
    {
        // fill missing dates at the start of the calendar
        $firstDate = array_key_first($calendar);
        $firstDay = Carbon::parse($firstDate)->dayName;

        while (strtoupper($firstDay) !== WeekDay::WEEK_DAYS[0]) {
            $previousDate = Carbon::parse($firstDate)->subDay();

            $calendar[$previousDate->format('Y-m-d')] = [
                'date' => $previousDate->format('Y-m-d'),
                'day_name' => $previousDate->dayName,
                'month_name' => $previousDate->format('M'),
                'date_number' => $previousDate->day,
                'gym_classes' => [],
                'disabled' => true,
            ];

            // sort calendar array by key (date)
            ksort($calendar);

            $firstDate = array_key_first($calendar);
            $firstDay = Carbon::parse($firstDate)->dayName;
        }

        // fill missing dates at the end of the calendar
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

        // remove date keys from calendar
        $calendar = array_values($calendar);

        // check if first seven dates of calendar are empty
        $fisrtSevenDatesAreEmpty = true;
        for ($key = 0; $key <= 6; $key++) {
            if (!empty($calendar[$key]['gym_classes'])) {
                $fisrtSevenDatesAreEmpty = false;
                break;
            }
        }
        // if first seven dates are empty, then unset them from calendar
        if ($fisrtSevenDatesAreEmpty) {
            for ($key = 0; $key <= 6; $key++) {
                unset($calendar[$key]);
            }
        }

        return array_values($calendar);
    }
}
