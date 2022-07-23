<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reservation\ReservationSingle;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminReservationController extends Controller
{
    /**
     * @var ReservationService
     */
    private $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * Display reservations
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = $request->query();

        $reservations = $this->reservationService->getReservations($data);

        $response = new Response($reservations, Response::HTTP_OK);

        return $response;
    }

    /**
     * Display single reservation
     *
     * @param Reservation $reservation
     *
     * @return Response
     */
    public function show(Reservation $reservation)
    {
        $response = new Response(new ReservationSingle($reservation), Response::HTTP_OK);

        return $response;
    }

    /**
     * Create a reservation
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        // get the payload
        $data = $request->post();

        // get user
        $user = $request->user();

        // create reservation
        $reservation = $this->reservationService->create($data, $user);

        $response = new Response(null, Response::HTTP_CREATED);
        $response->headers->set('Location', route('admin.reservation.show', ['reservation' => $reservation]));

        return $response;
    }

    /**
     * Decline reservation
     *
     * @param Request $request
     * @param Reservation $reservation
     *
     * @return Response
     */
    public function decline(Request $request, Reservation $reservation)
    {
        // get the payload
        $data = $request->post();

        // if data is empty nothing to update
        if (empty($data)) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        // decline reservation
        $this->reservationService->decline($data, $reservation);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete reservation
     *
     * @param Reservation $reservation
     *
     * @return Response
     */
    public function delete(Reservation $reservation)
    {
        $this->reservationService->delete($reservation);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
