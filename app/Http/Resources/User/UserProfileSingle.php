<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileSingle extends JsonResource
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

        $response = [
            'id' => $parent['id'],
            'name' => $parent['name'],
            'surname' => $parent['surname'],
            'email' => $parent['email'],
            'phone_number' => $parent['phone_number'],
            'role' => $parent['role'],
            'email_verified_at' => $parent['email_verified_at'],
        ];

        if (isset($parent['active_subscription'])) {
            $response['subscription'] = $parent['active_subscription'];
        }

        return $response;
    }
}
