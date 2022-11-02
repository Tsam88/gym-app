<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExcludedCalendarDate\ExcludedCalendarDates;
use App\Http\Resources\ExcludedCalendarDate\ExcludedCalendarDateSingle;
use App\Models\ExcludedCalendarDate;
use App\Services\ExcludedCalendarDateService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExcludedCalendarDateController extends Controller
{
    /**
     * @var ExcludedCalendarDateService
     */
    private $excludedCalendarDateService;

    public function __construct(ExcludedCalendarDateService $excludedCalendarDateService)
    {
        $this->excludedCalendarDateService = $excludedCalendarDateService;
    }

    /**
     * Display excluded calendar dates
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = $request->query();

        $excludedCalendarDates = $this->excludedCalendarDateService->getExcludedCalendarDates($data);

        $response = new Response(new ExcludedCalendarDates($excludedCalendarDates), Response::HTTP_OK);

        return $response;
    }

    /**
     * Display single excluded calendar date
     *
     * @param ExcludedCalendarDate $excludedCalendarDate
     *
     * @return Response
     */
    public function show(ExcludedCalendarDate $excludedCalendarDate)
    {
        $excludedCalendarDate = $this->excludedCalendarDateService->getExcludedCalendarDate($excludedCalendarDate);

        $response = new Response(new ExcludedCalendarDateSingle($excludedCalendarDate), Response::HTTP_OK);

        return $response;
    }

    /**
     * Create an excluded calendar date
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        // get the payload
        $data = $request->post();

        // create reservation
        $reservation = $this->excludedCalendarDateService->create($data);

        $response = new Response(null, Response::HTTP_CREATED);
        $response->headers->set('Location', route('admin.reservations.show', ['reservation' => $reservation]));

        return $response;
    }

    /**
     * Update an excluded calendar date
     *
     * @param Request              $request
     * @param ExcludedCalendarDate $excludedCalendarDate
     *
     * @return Response
     */
    public function update(Request $request, ExcludedCalendarDate $excludedCalendarDate)
    {
        // get the payload
        $data = $request->post();

        // if data is empty nothing to update
        if (empty($data)) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        // update excluded calendar date
        $this->excludedCalendarDateService->update($data, $excludedCalendarDate);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete excluded calendar date
     *
     * @param ExcludedCalendarDate $excludedCalendarDate
     *
     * @return Response
     */
    public function delete(ExcludedCalendarDate $excludedCalendarDate)
    {
        $this->excludedCalendarDateService->delete($excludedCalendarDate);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
