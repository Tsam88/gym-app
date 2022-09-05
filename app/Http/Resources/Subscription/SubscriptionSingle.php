<?php

namespace App\Http\Resources\Subscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionSingle extends JsonResource
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
            'user_id' => $parent['user_id'],
//            'subscription_plan_id' => $parent['subscription_plan_id'],
            'price' => $parent['price'],
            'remaining_sessions' => $parent['remaining_sessions'],
            'sessions_per_week' => $parent['sessions_per_week'],
            'unlimited_sessions' => $parent['unlimited_sessions'],
            'starts_at' => $parent['starts_at'],
            'expires_at' => $parent['expires_at'],
        ];
    }
}
