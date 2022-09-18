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
     * Display gym classes.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function calendar(Request $request)
    {
        // get user
        $user = $request->user();

        $gymClasses = $this->weekDayService->getCalendar($user);

        $response = new Response($gymClasses, Response::HTTP_OK);

        return $response;
    }
}
