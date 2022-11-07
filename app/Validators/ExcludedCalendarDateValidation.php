<?php

declare(strict_types = 1);

namespace App\Validators;

class ExcludedCalendarDateValidation extends AbstractValidation
{
    /**
     * The basic validation rules of model
     *
     * @var array
     */
    private const VALIDATION_RULES = [
        'gym_class_ids' => [
            'required',
            'array',
            'min:1',
        ],
        'gym_class_ids.*' => [
            'required',
            'integer',
            'gt:0',
        ],
        'start_date' => [
            'required',
            'date',
        ],
        'end_date' => [
            'required',
            'date',
        ],
        'extend_subscription' => [
            'required',
            'boolean',
        ],
        'items_per_page' => [
            'integer',
            'gt:0',
        ],
    ];

    /**
     * Get excluded calendar dates validation
     *
     * @param array $input
     *
     * @return array
     */
    public function excludedCalendarDateGetExcludedDates(array $input)
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
     * Create excluded calendar date validation
     *
     * @param array $input
     *
     * @return array
     */
    public function excludedCalendarDateCreate(array $input)
    {
        // build the rules for create
        $validationRules = [
            'gym_class_ids' => $this->getRule(self::VALIDATION_RULES, 'gym_class_ids', []),
            'start_date' => $this->getRule(self::VALIDATION_RULES, 'start_date', []),
            'end_date' => $this->getRule(self::VALIDATION_RULES, 'end_date', []),
            'extend_subscription' => $this->getRule(self::VALIDATION_RULES, 'extend_subscription', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * Update excluded calendar date validation
     *
     * @param array $input
     *
     * @return array
     */
    public function excludedCalendarDateUpdate(array $input)
    {
        // build the rules for update
        $validationRules = [
            'gym_class_ids' => $this->getRule(self::VALIDATION_RULES, 'gym_class_ids', []),
            'start_date' => $this->getRule(self::VALIDATION_RULES, 'start_date', []),
            'end_date' => $this->getRule(self::VALIDATION_RULES, 'end_date', []),
            'extend_subscription' => $this->getRule(self::VALIDATION_RULES, 'extend_subscription', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }
}
