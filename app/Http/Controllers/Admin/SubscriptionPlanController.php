<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPlan\SubscriptionPlanSingle;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscriptionPlanController extends Controller
{
    /**
     * @var SubscriptionPlanService
     */
    private $subscriptionPlanService;

    public function __construct(SubscriptionPlanService $subscriptionPlanService)
    {
        $this->subscriptionPlanService = $subscriptionPlanService;
    }

    /**
     * Display subscription plans.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = $request->query();

        $subscriptionPlans = $this->subscriptionPlanService->getSubscriptionPlans($data);

        $response = new Response($subscriptionPlans, Response::HTTP_OK);

        return $response;
    }

    /**
     * Display single subscription plan.
     *
     * @param SubscriptionPlan $subscriptionPlan
     *
     * @return Response
     */
    public function show(SubscriptionPlan $subscriptionPlan)
    {
        $response = new Response(new SubscriptionPlanSingle($subscriptionPlan), Response::HTTP_OK);

        return $response;
    }

    /**
     * Create a subscription plan.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        // get the payload
        $data = $request->post();

        // create subscription plan
        $subscriptionPlan = $this->subscriptionPlanService->create($data);

        $response = new Response(null, Response::HTTP_CREATED);
        $response->headers->set('Location', route('admin.subscription-plans.show', ['subscriptionPlan' => $subscriptionPlan]));

        return $response;
    }

    /**
     * Update subscription plan
     *
     * @param Request $request
     * @param SubscriptionPlan $subscriptionPlan
     *
     * @return Response
     */
    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        // get the payload
        $data = $request->post();

        // if data is empty nothing to update
        if (empty($data)) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        // update subscription plan
        $this->subscriptionPlanService->update($data, $subscriptionPlan);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete subscription plan
     *
     * @param SubscriptionPlan $subscriptionPlan
     *
     * @return Response
     */
    public function delete(SubscriptionPlan $subscriptionPlan)
    {
        $this->subscriptionPlanService->delete($subscriptionPlan);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
