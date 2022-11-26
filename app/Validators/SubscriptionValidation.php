<?php

declare(strict_types = 1);

namespace App\Validators;

class SubscriptionValidation extends AbstractValidation
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
        'subscription_plan_id' => [
            'required',
            'integer',
            'gt:0',
        ],
        'price' => [
            'required',
            'numeric',
            'gte:0',
        ],
        'remaining_sessions' => [
            'required_if:unlimited_sessions,"false"',
            'integer',
            'gte:0',
            'nullable',
            'prohibited_if:unlimited_sessions, "true"',
        ],
        'sessions_per_week' => [
            'integer',
            'gte:0',
            'nullable',
            'prohibited_if:unlimited_sessions, "false"',
        ],
        'unlimited_sessions' => [
            'required',
            'boolean',
        ],
        'starts_at' => [
            'required',
            'date',
        ],
        'expires_at' => [
            'required',
            'date',
        ],
        'users' => [
            'array',
        ],
        'users.*' => [
            'integer',
        ],
        'only_active_subscriptions' => [
            'required',
            'boolean',
        ],
        'items_per_page' => [
            'integer',
            'gt:0',
        ],
    ];

    /**
     * Get subscriptions validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function subscriptionGetSubscriptions(array $input)
    {
        // build the rules for index
        $validationRules = [
//            'users' => $this->getRule(self::VALIDATION_RULES, 'users', []),
            'user_id' => $this->getRule(self::VALIDATION_RULES, 'user_id', ['sometimes']),
            'only_active_subscriptions' => $this->getRule(self::VALIDATION_RULES, 'only_active_subscriptions', []),
            'items_per_page' => $this->getRule(self::VALIDATION_RULES, 'items_per_page', ['sometimes']),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * Create subscription validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function subscriptionCreate(array $input)
    {
        // build the rules for create
        $validationRules = [
            'user_id' => $this->getRule(self::VALIDATION_RULES, 'user_id', []),
            'subscription_plan_id' => $this->getRule(self::VALIDATION_RULES, 'subscription_plan_id', []),
            'starts_at' => $this->getRule(self::VALIDATION_RULES, 'starts_at', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * Update subscription validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function subscriptionUpdate(array $input)
    {
        // build the rules for update
        $validationRules = [
            'user_id' => $this->getRule(self::VALIDATION_RULES, 'user_id', []),
//            'subscription_plan_id' => $this->getRule(self::VALIDATION_RULES, 'subscription_plan_id', []),
            'price' => $this->getRule(self::VALIDATION_RULES, 'price', []),
            'remaining_sessions' => $this->getRule(self::VALIDATION_RULES, 'remaining_sessions', []),
            'sessions_per_week' => $this->getRule(self::VALIDATION_RULES, 'sessions_per_week', ['sometimes']),
            'unlimited_sessions' => $this->getRule(self::VALIDATION_RULES, 'unlimited_sessions', []),
            'starts_at' => $this->getRule(self::VALIDATION_RULES, 'starts_at', []),
            'expires_at' => $this->getRule(self::VALIDATION_RULES, 'expires_at', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }
}
