<?php

namespace App\Http\Resources\SubscriptionPlan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanSingle extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $parent = parent::toArray($request);

        return [
            'id' => $parent['id'],
            'name' => $parent['name'],
            'plan_price' => $parent['plan_price'],
            'number_of_sessions' => $parent['number_of_sessions'],
            'number_of_months' => $parent['number_of_months'],
            'unlimited_sessions' => $parent['unlimited_sessions'],
            'display_on_page' => $parent['display_on_page'],
        ];
    }
}
