<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WeekDayService;
use Illuminate\Http\Response;

class AdminWeekDayController extends Controller
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
     * Display admin calendar.
     *
     * @return Response
     */
    public function adminCalendar()
    {
        $gymClasses = $this->weekDayService->getAdminCalendar();

        $response = new Response($gymClasses, Response::HTTP_OK);

        return $response;
    }
}
