<?php

declare(strict_types = 1);

namespace App\Validators;

class UserValidation extends AbstractValidation
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
        'surname' => [
            'required',
            'string',
            'max:255',
        ],
        'email' => [
            'required',
            'email',
            'max:255',
        ],
        'password' => [
            'required',
            'string',
        ],
        'phone_number' => [
            'string',
            'max:255',
            'nullable',
        ],
        'token' => [
            'string'
        ],
        'items_per_page' => [
            'integer',
            'gt:0',
        ],
    ];

    /**
     * Get users validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function userGetUsers(array $input)
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
     * User register validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function userRegister(array $input)
    {
        // build the rules for register
        $validationRules = [
            'name' => $this->getRule(self::VALIDATION_RULES, 'name', []),
            'surname' => $this->getRule(self::VALIDATION_RULES, 'surname', []),
            'email' => $this->getRule(self::VALIDATION_RULES, 'email', []),
            'phone_number' => $this->getRule(self::VALIDATION_RULES, 'phone_number', []),
            'password' => $this->getRule(self::VALIDATION_RULES, 'password', ['min:8']),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * User login validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function userLogin(array $input)
    {
        // build the rules for login
        $validationRules = [
            'email' => $this->getRule(self::VALIDATION_RULES, 'email', []),
            'password' => $this->getRule(self::VALIDATION_RULES, 'password', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * Update user validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function userUpdate(array $input)
    {
        // build the rules for update
        $validationRules = [
            'name' => $this->getRule(self::VALIDATION_RULES, 'name', ['sometimes']),
            'surname' => $this->getRule(self::VALIDATION_RULES, 'surname', ['sometimes']),
            'phone_number' => $this->getRule(self::VALIDATION_RULES, 'phone_number', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * Update user validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function userSendResetPasswordLinkEmail(array $input)
    {
        // build the rules for update
        $validationRules = [
            'email' => $this->getRule(self::VALIDATION_RULES, 'email', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * Update user validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function userResetPassword(array $input)
    {
        // build the rules for update
        $validationRules = [
            'token' => $this->getRule(self::VALIDATION_RULES, 'token', ['required']),
            'email' => $this->getRule(self::VALIDATION_RULES, 'email', []),
            'password' => $this->getRule(self::VALIDATION_RULES, 'password', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * Update user password validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function userUpdatePassword(array $input)
    {
        // build the rules for update
        $validationRules = [
            'old_password' => $this->getRule(self::VALIDATION_RULES, 'password', []),
            'password' => $this->getRule(self::VALIDATION_RULES, 'password', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }

    /**
     * Update user password validation.
     *
     * @param array $input
     *
     * @return array
     */
    public function userUpdateEmail(array $input)
    {
        // build the rules for update
        $validationRules = [
            'password' => $this->getRule(self::VALIDATION_RULES, 'password', []),
            'email' => $this->getRule(self::VALIDATION_RULES, 'email', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        return $data;
    }
}
