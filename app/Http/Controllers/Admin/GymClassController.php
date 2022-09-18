<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GymClass\GymClassSingle;
use App\Models\GymClass;
use App\Services\GymClassService;
use App\Services\ReservationService;
use App\Services\WeekDayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class GymClassController extends Controller
{
    /**
     * @var GymClassService
     */
    private $gymClassService;

    /**
     * @var ReservationService
     */
    private $reservationService;

    /**
     * @var WeekDayService
     */
    private $weekDayService;

    public function __construct(GymClassService $gymClassService, ReservationService $reservationService, WeekDayService $weekDayService)
    {
        $this->gymClassService = $gymClassService;
        $this->reservationService = $reservationService;
        $this->weekDayService = $weekDayService;
    }

    /**
     * Display gym classes.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = $request->query();

        $gymClasses = $this->gymClassService->getGymClasses($data);

        $response = new Response($gymClasses, Response::HTTP_OK);

        return $response;
    }

    /**
     * Display single gym class.
     *
     * @param GymClass $gymClass
     *
     * @return Response
     */
    public function show(GymClass $gymClass)
    {
        $gymClass = $this->gymClassService->getGymClass($gymClass);

        $response = new Response(new GymClassSingle($gymClass), Response::HTTP_OK);

        return $response;
    }

    /**
     * Create gym class.
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
            // create gym class
            $gymClass = $this->gymClassService->create($data);

            if ($gymClass) {
                foreach ($data['week_days'] as &$weekDay) {
                    $weekDay['gym_class_id'] = $gymClass->id;
                    $weekDay['created_at'] = Carbon::now();
                    $weekDay['updated_at'] = Carbon::now();
                }

                $this->weekDayService->createMany($data);
            }
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();

        $response = new Response(null, Response::HTTP_CREATED);
        $response->headers->set('Location', route('admin.gym-classes.show', ['gymClass' => $gymClass]));

        return $response;
    }

    /**
     * Update gym class
     *
     * @param Request $request
     * @param GymClass $gymClass
     *
     * @return Response
     */
    public function update(Request $request, GymClass $gymClass)
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
            // update gym class
            $this->gymClassService->update($data, $gymClass);

            // get the valid new week days to create, by comparing the old week days with new ones
            $weekDaysToCreate['week_days'] = $this->weekDayService->getWeekDaysToCreate($data['week_days'], $gymClass);

//            dd($weekDaysToCreate['week_days']);

            if ($weekDaysToCreate['week_days']) {
                foreach ($weekDaysToCreate['week_days'] as &$weekDay) {
                    $weekDay['gym_class_id'] = $gymClass->id;
                    $weekDay['created_at'] = Carbon::now();
                    $weekDay['updated_at'] = Carbon::now();
                }

                // create valid new week days
                $this->weekDayService->createMany($weekDaysToCreate);
            }

            // get the old week days that we need to delete, by comparing the old week days with new ones
            $weekDayIdsToDelete = $this->weekDayService->getWeekDayIdsToDelete($data['week_days'], $gymClass);

            if ($weekDayIdsToDelete) {
                // get pending reservations for the selected week days
                $pendingReservations = $this->reservationService->getPendingReservationsBasedOnWeekDays($weekDayIdsToDelete);

                // decline all pending reservations before deleting the selected week days
                foreach ($pendingReservations as $pendingReservation) {
                    dd($pendingReservations);

                    $this->reservationService->decline($pendingReservation);
                }

                // delete valid old week days
                $this->weekDayService->deleteMany($weekDayIdsToDelete);
            }
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
     * Delete gym class
     *
     * @param GymClass $gymClass
     *
     * @return Response
     */
    public function delete(GymClass $gymClass)
    {
        // start db transaction
        DB::beginTransaction();

        try {
            // get pending reservations for this gym class
            $pendingReservations = $this->reservationService->getPendingReservationsOfGymClass($gymClass);

            // decline all pending reservations before deleting the gym class
            foreach ($pendingReservations as $pendingReservation) {
                $this->reservationService->decline($pendingReservation);
            }

            $this->gymClassService->delete($gymClass);
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
