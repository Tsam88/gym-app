<?php

declare(strict_types = 1);

namespace App\Validators;

use App\Exceptions\InternalServerErrorException;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Contracts\Validation\Validator;

class AbstractValidation
{
    /**
     * Create a new Validator instance.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     *
     * @return Validator
     */
    protected function getValidator(array $data, array $rules, array $messages = [], array $customAttributes = []): Validator
    {
        return ValidatorFacade::make($data, $rules, $messages, $customAttributes);
    }

    /**
     * Get the validation rules for the given property.
     *
     * @param array  $rules      Complete rules dataset
     * @param string $property   Property to get rule for
     * @param array  $extraRules Extra rules to apply to the given property
     *
     * @return array
     */
    protected function getRule(array $rules, string $property, array $extraRules = []): array
    {
        // if key is not defined throw an exception
        if (!array_key_exists($property, $rules)) {
            throw new InternalServerErrorException();
        }

        $rule = $rules[$property];

        // apply extra rules
        foreach ($extraRules as $extraRule) {
            // if rule already exists, ignore.
            if (!in_array($extraRule, $rule, true)) {
                $rule[] = $extraRule;
            }
        }

        return $rule;
    }
}
