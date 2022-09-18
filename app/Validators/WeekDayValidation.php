<?php

declare(strict_types = 1);

namespace App\Validators;

use App\Models\WeekDay;

class WeekDayValidation extends AbstractValidation
{
    /**
     * The basic validation rules of model
     *
     * @var array
     */
    private const VALIDATION_RULES = [
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
    ];

    /**
     * Create week day validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function weekDayCreate(array $input)
    {
        // build the rules for create
        $validationRules = [
            'week_days' => $this->getRule(self::VALIDATION_RULES, 'week_days', []),
            'week_days.*' => $this->getRule(self::VALIDATION_RULES, 'week_days.*', []),
            'week_days.*.day' => $this->getRule(self::VALIDATION_RULES, 'week_days.*.day', ['in:' . implode(',', WeekDay::WEEK_DAYS)]),
            'week_days.*.start_time' => $this->getRule(self::VALIDATION_RULES, 'week_days.*.start_time', []),
            'week_days.*.end_time' => $this->getRule(self::VALIDATION_RULES, 'week_days.*.end_time', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data['week_days'];
    }

    /**
     * Update week day validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function weekDayUpdate(array $input)
    {
        // build the rules for update
        $validationRules = [
            'week_days' => $this->getRule(self::VALIDATION_RULES, 'week_days', []),
            'week_days.*' => $this->getRule(self::VALIDATION_RULES, 'week_days.*', []),
            'week_days.*.day' => $this->getRule(self::VALIDATION_RULES, 'week_days.*.day', ['in:' . implode(',', WeekDay::WEEK_DAYS)]),
            'week_days.*.start_time' => $this->getRule(self::VALIDATION_RULES, 'week_days.*.start_time', []),
            'week_days.*.end_time' => $this->getRule(self::VALIDATION_RULES, 'week_days.*.end_time', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data['week_days'];
    }
}
