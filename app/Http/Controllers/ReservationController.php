<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reservation\ReservationSingle;
use App\Models\Reservation;
use App\Services\ReservationService;
use App\Services\UserService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReservationController extends Controller
{
    /**
     * @var ReservationService
     */
    private $reservationService;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(ReservationService $reservationService, UserService $userService)
    {
        $this->reservationService = $reservationService;
        $this->userService = $userService;
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
        $this->userService->hasAccess($reservation->user_id);

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

        // create reservation
        $reservation = $this->reservationService->create($data);

        $response = new Response(null, Response::HTTP_CREATED);
        $response->headers->set('Location', route('admin.reservation.show', ['reservation' => $reservation]));

        return $response;
    }

    /**
     * Cancel reservation
     *
     * @param Request $request
     * @param Reservation $reservation
     *
     * @throws AuthorizationException
     *
     * @return Response
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        $this->userService->hasAccess($reservation->user_id);

        // get the payload
        $data = $request->post();

        // if data is empty nothing to update
        if (empty($data)) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        // cancel reservation
        $this->reservationService->cancel($data, $reservation);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete reservation
     *
     * @param Reservation $reservation
     *
     * @throws AuthorizationException
     *
     * @return Response
     */
    public function delete(Reservation $reservation)
    {
        $this->userService->hasAccess($reservation->user_id);

        $this->reservationService->delete($reservation);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
