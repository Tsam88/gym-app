<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubscriptionPlan\SubscriptionPlanSingle;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\Access\AuthorizationException;

class WeekDayController extends Controller
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

        $subscriptionPlans = $this->subscriptionPlanService->getAllowedSubscriptionPlansOnPage($data);

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
        if (!$subscriptionPlan->display_on_page) {
            throw new AuthorizationException();
        }

        $response = new Response(new SubscriptionPlanSingle($subscriptionPlan), Response::HTTP_OK);

        return $response;
    }
}
