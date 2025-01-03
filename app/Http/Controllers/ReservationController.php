<?php

namespace App\Http\Controllers;

use App\Http\Resources\Reservation\ReservationSingle;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReservationController extends Controller
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
     * Display logged-in user's reservations
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = $request->query();

        // get reservations
        $reservations = $this->reservationService->getReservations($data);

        $response = new Response($reservations, Response::HTTP_OK);

        return $response;
    }

    /**
     * Display single reservation
     *
     * @param Reservation $reservation
     *
     * @throws AuthorizationException
     *
     * @return Response
     */
    public function show(Reservation $reservation)
    {
        // get reservation
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
        $response->headers->set('Location', route('reservations.show', ['reservation' => $reservation]));

        return $response;
    }

    /**
     * Cancel reservation
     *
     * @param Reservation $reservation
     *
     * @throws AuthorizationException
     *
     * @return Response
     */
    public function cancel(Reservation $reservation)
    {
        // cancel reservation
        $this->reservationService->cancel($reservation);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
