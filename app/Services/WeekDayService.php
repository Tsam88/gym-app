<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Reservation;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WeekDay;
use App\Validators\SubscriptionValidation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class WeekDayService
{
    /**
     * The default number of items per page
     *
     * @var int
     */
    private const DEFAULT_ITEMS_PER_PAGE = 0;

//    /**
//     * @var SubscriptionValidation
//     */
//    private $subscriptionValidation;
//
//    public function __construct(SubscriptionValidation $subscriptionValidation)
//    {
//        $this->subscriptionValidation = $subscriptionValidation;
//    }

    /**
     * Return week calendar
     *
     * @return Response
     */
    public function getWeekCalendar(): Response
    {

        $weekDays = WeekDay::orderBy('start_time')
            ->get();

        $weekCalendar = WeekDay::WEEK_DAYS;

//        protected $fillable = [
//        'gym_class_id',
//        'day',
//        'start_time',
//        'end_time',
//    ];

        foreach ($weekDays as $weekDay) {
            $weekCalendar[$weekDay->day][] = [
                'gym_class_name' => $weekDay->gymClass->name,
                'start_time' => $weekDay->start_time,
                'end_time' => $weekDay->end_time,
            ];
        }

        $response = new Response($weekCalendar, Response::HTTP_OK);

        return $response;
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
        $weekDays = WeekDay::orderBy('start_time')
            ->get();


        foreach ($weekDays as $weekDay) {
            $weekCalendar[$weekDay->day][] = [
                'gym_class_id' => $weekDay->gym_class_id,
                'week_day_id' => $weekDay->week_day_id,
                'gym_class_name' => $weekDay->gymClass->name,
                'start_time' => $weekDay->start_time,
                'end_time' => $weekDay->end_time,
            ];
        }

        $fromDate = Carbon::now()->setTimezone('Europe/Athens');
        $toDate = Carbon::today()->setTimezone('Europe/Athens')->endOfMonth();
        $period = CarbonPeriod::create($fromDate, $toDate);

        $calendar = [];
        foreach ($period as $date) {
            foreach ($weekCalendar[$date->dayName] as $key => $gymClass) {
                $gymClassDateTimeUTC = Carbon::parse("{$date->format('Y-m-d')} {$gymClass['start_time']}")->setTimezone('UTC');
//                $gymClassDateTime = "{$date->format('Y-m-d')} {$gymClass['start_time']}";

                $dailyGymClasses[$key] = [
                    'gym_class_name' => $gymClass['gym_class_name'],
                    'start_time' => $gymClass['start_time'],
                    'end_time' => $gymClass['end_time'],
                    'has_reservation' => false,
                    'canceled' => false,
                    'declined' => false,
                ];

                $reservation = Reservation::where('user_id', $user->id)
                    ->where('gym_class_id', $gymClass['gym_class_id'])
                    ->where('week_day_id', $gymClass['week_day_id'])
                    ->where('date', $gymClassDateTimeUTC)
                    ->get()
                    ->first();

                if (!$reservation) {
                    $dailyGymClasses[$key]['has_reservation'] = true;
                    $dailyGymClasses[$key]['canceled'] = $reservation->canceled;
                    $dailyGymClasses[$key]['declined'] = $reservation->declined;
                }
            }

            $calendar[] = [
                'date' => $date->format('Y-m-d'),
                'gym_classes' => $dailyGymClasses,
            ];
        }

        return $calendar;
    }
//
//    /**
//     * Create a subscription
//     *
//     * @param array $input Subscription data
//     *
//     * @return Subscription
//     */
//    public function create(array $input): Subscription
//    {
//        // data validation
//        $data = $this->subscriptionValidation->subscriptionCreate($input);
//
//        // start db transaction
//        DB::beginTransaction();
//
//        try {
//            $subscription = Subscription::create($data);
//        } catch (\Exception $e) {
//            // something went wrong, rollback and throw same exception
//            DB::rollBack();
//
//            throw $e;
//        }
//
//        // commit database changes
//        DB::commit();
//
//        return $subscription;
//    }
//
//    /**
//     * Update a subscription
//     *
//     * @param array $input Subscription data
//     *
//     * @return void
//     */
//    public function update(array $input, Subscription $subscription)
//    {
//        // data validation
//        $data = $this->subscriptionValidation->subscriptionUpdate($input);
//
//        // start db transaction
//        DB::beginTransaction();
//
//        try {
//            $subscription->update($data);
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
//
//    /**
//     * Delete subscription
//     *
//     * @param Subscription $subscription
//     *
//     * @return void
//     */
//    public function delete(Subscription $subscription)
//    {
//        // start db transaction
//        DB::beginTransaction();
//
//        try {
//            $subscription->delete();
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
