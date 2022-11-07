<?php

namespace App\Http\Resources\ExcludedCalendarDate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExcludedCalendarDateSingle extends JsonResource
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

        $gymClasses = [];
        foreach ($parent['gym_classes'] as $gymClass) {
            $gymClasses[] = [
                'id' => $gymClass['id'],
                'name' => $gymClass['name'],
            ];
        }

        return [
            'id' => $parent['id'],
            'start_date' => $parent['start_date'],
            'end_date' => $parent['end_date'],
            'extend_subscription' => $parent['extend_subscription'],
            'gym_classes' => $gymClasses,
        ];
    }
}
