<?php

namespace App\Http\Resources\GymClass;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GymClassSingle extends JsonResource
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

        $weekDays = [];
        foreach ($parent['week_days'] as $key => $weekDay) {
            $weekDays[] = [
                'day' => $weekDay['day'],
                'start_time' => $weekDay['start_time'],
                'end_time' => $weekDay['end_time'],
            ];
        }

        return [
            'id' => $parent['id'],
            'name' => $parent['name'],
            'description' => $parent['description'],
            'teacher' => $parent['teacher'],
            'number_of_students' => $parent['number_of_students'],
            'color' => $parent['color'],
            'week_days' => $weekDays,
        ];
    }
}
