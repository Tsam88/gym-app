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

        return [
            'id' => $parent['id'],
            'name' => $parent['name'],
            'description' => $parent['description'],
            'teacher' => $parent['teacher'],
            'number_of_students' => $parent['number_of_students'],
        ];
    }
}
