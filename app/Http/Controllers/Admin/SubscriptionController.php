<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Subscription\SubscriptionSingle;
use App\Models\Subscription;
use App\Services\ReservationService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscriptionController extends Controller
{
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * @var ReservationService
     */
    private $reservationService;

    public function __construct(SubscriptionService $subscriptionService, ReservationService $reservationService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->reservationService = $reservationService;
    }

    /**
     * Display subscriptions
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = $request->query();

        $subscriptions = $this->subscriptionService->getSubscriptions($data);

        $response = new Response($subscriptions, Response::HTTP_OK);

        return $response;
    }

    /**
     * Display single subscription
     *
     * @param Subscription $subscription
     *
     * @return Response
     */
    public function show(Subscription $subscription)
    {
        $response = new Response(new SubscriptionSingle($subscription), Response::HTTP_OK);

        return $response;
    }

    /**
     * Create a subscription
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        // get the payload
        $data = $request->post();

        // get last reservation date
        $lastReservationDate = $this->reservationService->getLastReservationDate($data['user_id']);

        // create subscription
        $subscription = $this->subscriptionService->create($data, $lastReservationDate);

        $response = new Response(null, Response::HTTP_CREATED);
        $response->headers->set('Location', route('admin.subscriptions.show', ['subscription' => $subscription]));

        return $response;
    }

    /**
     * Update subscription
     *
     * @param Request $request
     * @param Subscription $subscription
     *
     * @return Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        // get the payload
        $data = $request->post();

        // get last reservation date
        $lastReservationDate = $this->reservationService->getLastReservationDate($data['user_id']);

        // if data is empty nothing to update
        if (empty($data)) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        // update subscription
        $this->subscriptionService->update($data, $subscription, $lastReservationDate);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete subscription
     *
     * @param Subscription $subscription
     *
     * @return Response
     */
    public function delete(Subscription $subscription)
    {
        $this->subscriptionService->delete($subscription);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
