<?php

namespace App\Http\Controllers;

use App\Http\Resources\Reservation\ReservationSingle;
use App\Models\Reservation;
use App\Services\ReservationService;
use App\Services\SubscriptionService;
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

    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    public function __construct(ReservationService $reservationService, UserService $userService, SubscriptionService $subscriptionService)
    {
        $this->reservationService = $reservationService;
        $this->userService = $userService;
        $this->subscriptionService = $subscriptionService;
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

        // get last reservation date
        $lastReservationDate = $this->reservationService->getLastReservationDate($data['user_id']);

        // get active subscription
        $activeSubscription = $this->subscriptionService->getActiveSubscription($data['user_id'], $lastReservationDate);

        // create reservation
        $reservation = $this->reservationService->create($data, $user, $activeSubscription);

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
        // get last reservation date
        $lastReservationDate = $this->reservationService->getLastReservationDate($reservation->user_id);

        // get active subscription
        $activeSubscription = $this->subscriptionService->getActiveSubscription($reservation->user_id, $lastReservationDate);

        // cancel reservation
        $this->reservationService->cancel($reservation, $activeSubscription);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
