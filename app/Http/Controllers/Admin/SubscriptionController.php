<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Subscription\SubscriptionSingle;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscriptionController extends Controller
{
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
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

        // create subscription
        $subscription = $this->subscriptionService->create($data);

        $response = new Response(null, Response::HTTP_CREATED);
        $response->headers->set('Location', route('admin.subscription.show', ['subscription' => $subscription]));

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

        // if data is empty nothing to update
        if (empty($data)) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        // update subscription
        $this->subscriptionService->update($data, $subscription);

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
