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

        return [
            'id' => $parent['id'],
            'name' => $parent['name'],
            'surname' => $parent['surname'],
            'email' => $parent['email'],
            'phone_number' => $parent['phone_number'],
            'role' => $parent['role'],
        ];
    }
}
