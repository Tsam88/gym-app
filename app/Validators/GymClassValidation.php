<?php

declare(strict_types = 1);

namespace App\Validators;

//use App\Exceptions\DeleteUserHasParcelsException;
//use App\Exceptions\NotActiveUserException;
//use App\Exceptions\NotFoundException;
//use App\Exceptions\NotVerifiedUserException;
use App\Models\WeekDay;
//use App\Rules\CheckPassword;

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
        ],
        'week_days.*.day' => [
            'required',
            'string',
        ],
        'week_days.*.start_time' => [
            'required',
            'date_format:H:i',
        ],
        'week_days.*.end_time' => [
            'required',
            'date_format:H:i',
        ],
        'items_per_page' => [
            'integer',
            'gt:0',
        ],
    ];

    /**
     * Get gym classes validation.
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
     * Create gym class validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function gymClassCreate(array $input)
    {
        // build the rules for create
        $validationRules = [
            'name' => $this->getRule(self::VALIDATION_RULES, 'name', []),
            'description' => $this->getRule(self::VALIDATION_RULES, 'description', []),
            'teacher' => $this->getRule(self::VALIDATION_RULES, 'teacher', []),
            'number_of_students' => $this->getRule(self::VALIDATION_RULES, 'number_of_students', []),
            'week_days' => $this->getRule(self::VALIDATION_RULES, 'week_days', []),
            'week_days.*' => $this->getRule(self::VALIDATION_RULES, 'week_days.*', []),
            'week_days.*.day' => $this->getRule(self::VALIDATION_RULES, 'week_days.*.day', ['in:' . implode(',', WeekDay::WEEK_DAYS)]),
            'week_days.*.start_time' => $this->getRule(self::VALIDATION_RULES, 'week_days.*.start_time', []),
            'week_days.*.end_time' => $this->getRule(self::VALIDATION_RULES, 'week_days.*.end_time', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * Update gym class validation.
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
            'description' => $this->getRule(self::VALIDATION_RULES, 'surname', []),
            'teacher' => $this->getRule(self::VALIDATION_RULES, 'email', []),
            'number_of_students' => $this->getRule(self::VALIDATION_RULES, 'number_of_students', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }
}
