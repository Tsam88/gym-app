<?php

declare(strict_types = 1);

namespace App\Validators;

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
            'nullable',
            'string',
            'max:255',
        ],
        'teacher' => [
            'nullable',
            'string',
            'max:255',
        ],
        'number_of_students' => [
            'required',
            'integer',
            'gt:0',
        ],
        'color' => [
            'required',
            'string',
            'max:255',
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
            'color' => $this->getRule(self::VALIDATION_RULES, 'color', []),
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
            'description' => $this->getRule(self::VALIDATION_RULES, 'description', []),
            'teacher' => $this->getRule(self::VALIDATION_RULES, 'teacher', []),
            'number_of_students' => $this->getRule(self::VALIDATION_RULES, 'number_of_students', []),
            'color' => $this->getRule(self::VALIDATION_RULES, 'color', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }
}
