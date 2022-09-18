<?php

declare(strict_types = 1);

namespace App\Validators;

class ReservationValidation extends AbstractValidation
{
    /**
     * The basic validation rules of model
     *
     * @var array
     */
    private const VALIDATION_RULES = [
        'user_id' => [
            'required',
            'integer',
            'gt:0',
        ],
        'gym_class_id' => [
            'required',
            'integer',
            'gt:0',
        ],
        'week_day_id' => [
            'required',
            'integer',
            'gt:0',
        ],
        'date' => [
            'required',
            'date',
        ],
        'users' => [
            'array',
        ],
        'users.*' => [
            'integer',
        ],
        'items_per_page' => [
            'integer',
            'gt:0',
        ],
    ];

    /**
     * Get reservations validation
     *
     * @param array $input
     *
     * @return array
     */
    public function reservationGetReservations(array $input)
    {
        // build the rules for index
        $validationRules = [
            'users' => $this->getRule(self::VALIDATION_RULES, 'users', []),
            'items_per_page' => $this->getRule(self::VALIDATION_RULES, 'items_per_page', ['sometimes']),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * Create reservation validation
     *
     * @param array $input
     *
     * @return array
     */
    public function reservationCreate(array $input)
    {
        // build the rules for create
        $validationRules = [
            'user_id' => $this->getRule(self::VALIDATION_RULES, 'user_id', []),
            'gym_class_id' => $this->getRule(self::VALIDATION_RULES, 'gym_class_id', []),
            'week_day_id' => $this->getRule(self::VALIDATION_RULES, 'week_day_id', []),
            'date' => $this->getRule(self::VALIDATION_RULES, 'date', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }
}
