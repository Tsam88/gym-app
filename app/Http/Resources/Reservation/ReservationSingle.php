<?php

namespace App\Http\Resources\Reservation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationSingle extends JsonResource
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
            'gym_class_id' => $parent['gym_class_id'],
            'date' => $parent['date'],
            'declined' => $parent['declined'],
            'canceled' => $parent['canceled'],
        ];
    }
}
