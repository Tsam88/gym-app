<?php

namespace App\Http\Controllers;

use App\Services\WeekDayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WeekDayController extends Controller
{
    /**
     * @var WeekDayService
     */
    private $weekDayService;

    public function __construct(WeekDayService $weekDayService)
    {
        $this->weekDayService = $weekDayService;
    }

    /**
     * Display student calendar.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function studentCalendar(Request $request)
    {
        // get user
        $user = $request->user();

        $gymClasses = $this->weekDayService->getStudentCalendar($user);

        $response = new Response($gymClasses, Response::HTTP_OK);

        return $response;
    }

    /**
     * Display week calendar.
     **
     * @return Response
     */
    public function weekCalendar()
    {
        $weekCalendar = $this->weekDayService->getWeekCalendarByTime();

        $response = new Response($weekCalendar, Response::HTTP_OK);

        return $response;
    }
}
