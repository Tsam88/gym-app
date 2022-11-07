<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExcludedCalendarDate\ExcludedCalendarDates;
use App\Http\Resources\ExcludedCalendarDate\ExcludedCalendarDateSingle;
use App\Models\ExcludedCalendarDate;
use App\Services\ExcludedCalendarDateService;
use App\Services\ReservationService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ExcludedCalendarDateController extends Controller
{
    /**
     * @var ExcludedCalendarDateService
     */
    private $excludedCalendarDateService;

    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * @var ReservationService
     */
    private $reservationService;

    public function __construct(ExcludedCalendarDateService $excludedCalendarDateService, SubscriptionService $subscriptionService, ReservationService $reservationService)
    {
        $this->excludedCalendarDateService = $excludedCalendarDateService;
        $this->subscriptionService = $subscriptionService;
        $this->reservationService = $reservationService;
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

        // start db transaction
        DB::beginTransaction();

        try {
            // create excluded calendar date
            $excludedCalendarDate = $this->excludedCalendarDateService->create($data);

            // extend subscription based on "excluded calendar dates"
            if ($excludedCalendarDate->extend_subscription) {
                $this->subscriptionService->extendSubscriptions($excludedCalendarDate->start_date, $excludedCalendarDate->end_date);
            }

            // decline reservations that are included in the "excluded calendar dates"
            $this->reservationService->declineReservationsOfGymClassWithinDateRange($excludedCalendarDate->start_date, $excludedCalendarDate->end_date, $data['gym_class_ids']);

        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();

        $response = new Response(null, Response::HTTP_CREATED);
        $response->headers->set('Location', route('admin.excluded-calendar-dates.show', ['excludedCalendarDate' => $excludedCalendarDate]));

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

        // start db transaction
        DB::beginTransaction();

        try {
            // remove days of subscription based on "excluded calendar dates"
            if ($excludedCalendarDate->extend_subscription) {
                $this->subscriptionService->removeDaysExtensionFromSubscriptions($excludedCalendarDate->start_date, $excludedCalendarDate->end_date);
            }

            // update excluded calendar date
            $this->excludedCalendarDateService->update($data, $excludedCalendarDate);

            // extend subscription based on "excluded calendar dates"
            if ($excludedCalendarDate->extend_subscription) {
                $this->subscriptionService->extendSubscriptions($excludedCalendarDate->start_date, $excludedCalendarDate->end_date);
            }

            // decline reservations that are included in the "excluded calendar dates"
            $this->reservationService->declineReservationsOfGymClassWithinDateRange($excludedCalendarDate->start_date, $excludedCalendarDate->end_date, $data['gym_class_ids']);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();

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
        // start db transaction
        DB::beginTransaction();

        try {
            // remove days of subscription based on "excluded calendar dates"
            if ($excludedCalendarDate->extend_subscription) {
                $this->subscriptionService->removeDaysExtensionFromSubscriptions($excludedCalendarDate->start_date, $excludedCalendarDate->end_date);
            }

            // delete excluded calendar date
            $this->excludedCalendarDateService->delete($excludedCalendarDate);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
