<?php

declare(strict_types = 1);

namespace App\Validators;

use App\Exceptions\DeleteUserHasParcelsException;
use App\Exceptions\NotActiveUserException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotVerifiedUserException;
use App\Models\WeekDay;
use App\Rules\CheckPassword;

class GymClassValidation extends AbstractValidation
{
    /**
     * The basic validation rules of model
     *
     * @var array
     */
    private const VALIDATION_RULES = [
        'name' => [
            'required',
            'string',
            'max:255',
        ],
        'description' => [
//            'required',
            'string',
            'max:255',
        ],
        'teacher' => [
//            'required',
            'string',
            'max:255',
        ],
        'number_of_students' => [
            'required',
            'integer',
            'gt:0',
        ],
        'week_days' => [
            'array',
            'min:1',
        ],
        'week_days.*' => [
            'required',
            'array',
        ],
        'week_days.*.gym_class_id' => [
            'required',
            'integer',
        ],
        'week_days.*.day' => [
            'required',
            'string in:' . WeekDay::WEEK_DAYS,
        ],
        'week_days.*.start_time' => [
            'required',
            'time',
        ],
        'week_days.*.end_time' => [
            'required',
            'time',
        ],
        'items_per_page' => [
            'integer',
            'gt:0',
        ],
    ];


    /**
     * Set user types for the given user.
     *
     * @param array $input
     *
     * @return array
     */
    public function gymClassGetClasses(array $input)
    {
        // build the rules for index
        $validationRules = [
            'items_per_page' => $this->getRule(self::VALIDATION_RULES, 'items_per_page', ['sometimes']),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * Set user types for the given user.
     *
     * @param array $input
     *
     * @return array
     */
    public function gymClassCreate(array $input)
    {
        // build the rules for update
        $validationRules = [
            'name' => $this->getRule(self::VALIDATION_RULES, 'name', []),
            'description' => $this->getRule(self::VALIDATION_RULES, 'description', []),
            'teacher' => $this->getRule(self::VALIDATION_RULES, 'teacher', []),
            'number_of_students' => $this->getRule(self::VALIDATION_RULES, 'number_of_students', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * Set user types for the given user.
     *
     * @param array $input
     *
     * @return array
     */
    public function gymClassUpdate(array $input)
    {
        // build the rules for update
        $validationRules = [
            'name' => $this->getRule(self::VALIDATION_RULES, 'name', []),
            'description' => $this->getRule(self::VALIDATION_RULES, 'surname', ['sometimes']),
            'teacher' => $this->getRule(self::VALIDATION_RULES, 'email', ['sometimes']),
            'number_of_students' => $this->getRule(self::VALIDATION_RULES, 'number_of_students', []),
//            'items_per_page' => $this->getRule(self::VALIDATION_RULES, 'items_per_page', ['sometimes']),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }
}
