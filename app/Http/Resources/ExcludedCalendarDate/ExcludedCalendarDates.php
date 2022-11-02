<?php

namespace App\Http\Resources\ExcludedCalendarDate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExcludedCalendarDates extends JsonResource
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

        foreach ($parent['data'] as &$excludedCalendarDate) {
            foreach ($excludedCalendarDate['gym_classes'] as &$gymClass) {
                $gymClass = [
                    'id' => $gymClass['id'],
                    'name' => $gymClass['name'],
                ];
            }
        }

        return $parent;
    }
}
