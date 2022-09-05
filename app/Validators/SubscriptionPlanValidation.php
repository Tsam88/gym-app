<?php

declare(strict_types = 1);

namespace App\Validators;

use App\Exceptions\DeleteUserHasParcelsException;
use App\Exceptions\NotActiveUserException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotVerifiedUserException;

class SubscriptionPlanValidation extends AbstractValidation
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
        'plan_price' => [
            'required',
            'numeric',
            'gte:0',
        ],
        'number_of_sessions' => [
            'integer',
            'gte:0',
        ],
        'sessions_per_week' => [
            'integer',
            'nullable',
        ],
        'number_of_months' => [
            'integer',
            'gte:0',
        ],
        'unlimited_sessions' => [
            'required',
            'boolean',
        ],
        'display_on_page' => [
            'required',
            'boolean',
        ],
        'items_per_page' => [
            'integer',
            'gt:0',
        ],
    ];

    /**
     * Get subscription plans validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function subscriptionPlanGetPlans(array $input)
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
     * Create subscription plan validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function subscriptionPlanCreate(array $input)
    {
        // build the rules for create
        $validationRules = [
            'name' => $this->getRule(self::VALIDATION_RULES, 'name', []),
            'plan_price' => $this->getRule(self::VALIDATION_RULES, 'plan_price', []),
            'number_of_sessions' => $this->getRule(self::VALIDATION_RULES, 'number_of_sessions', []),
            'sessions_per_week' => $this->getRule(self::VALIDATION_RULES, 'sessions_per_week', ['sometimes|gte:0']),
            'number_of_months' => $this->getRule(self::VALIDATION_RULES, 'number_of_months', []),
            'unlimited_sessions' => $this->getRule(self::VALIDATION_RULES, 'unlimited_sessions', []),
            'display_on_page' => $this->getRule(self::VALIDATION_RULES, 'display_on_page', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * Update subscription plan validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function subscriptionPlanUpdate(array $input)
    {
        // build the rules for update
        $validationRules = [
            'name' => $this->getRule(self::VALIDATION_RULES, 'name', ['sometimes']),
            'plan_price' => $this->getRule(self::VALIDATION_RULES, 'plan_price', ['sometimes']),
            'number_of_sessions' => $this->getRule(self::VALIDATION_RULES, 'number_of_sessions', []),
            'sessions_per_week' => $this->getRule(self::VALIDATION_RULES, 'sessions_per_week', ['sometimes|gte:0']),
            'number_of_months' => $this->getRule(self::VALIDATION_RULES, 'number_of_months', []),
            'unlimited_sessions' => $this->getRule(self::VALIDATION_RULES, 'unlimited_sessions', ['sometimes']),
            'display_on_page' => $this->getRule(self::VALIDATION_RULES, 'display_on_page', ['sometimes']),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }
}
