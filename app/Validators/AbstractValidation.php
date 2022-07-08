<?php

declare(strict_types = 1);

namespace App\Validators;

use App\Exceptions\InternalServerErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;

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

    /**
     * Update if exist a property on a model.
     *
     * @param array  $data     Data to check property existence
     * @param string $property Property to update
     * @param Model  $model    Model to update the property
     *
     * @return void
     */
    protected function updateIfExist(array $data, string $property, Model $model)
    {
        $model->$property = \array_key_exists($property, $data) ? $data[$property] : $model->$property;
    }

    /**
     * Array map data from a model
     *
     * @param Model  $model            Model to map
     * @param string $transPlaceholder Translate Placeholder
     *
     * @return array
     */
    protected function mapModelRelation(Model $model, string $transPlaceholder): array
    {
        $availableTypes = $model::select('id', 'name')->get()->toArray();
        $types = \array_map(function ($value) use ($transPlaceholder) {
            return [
                'id' => $value['id'],
                'type' => $value['name'],
                'type_description' => Lang::get(sprintf('%s.%s', $transPlaceholder, $value['name'])),
            ];
        }, $availableTypes);

        return $types;
    }

    /**
     * Array map data from an array
     *
     * @param array  $availableTypes   Array values to map
     * @param string $transPlaceholder Translate Placeholder
     *
     * @return array
     */
    protected function mapArrayRelation(array $availableTypes, string $transPlaceholder): array
    {
        $types = \array_map(function ($value) use ($transPlaceholder) {
            return [
                'type' => $value,
                'type_description' => Lang::get(sprintf('%s.%s', $transPlaceholder, $value)),
            ];
        }, $availableTypes);

        return $types;
    }

    /**
     * Get all the existing identifiers for the given model.
     *
     * @param Model       $model
     * @param array|null  $filters
     * @param array|null  $without
     * @param string|null $keyName
     *
     * @return array
     */
    protected function getModelIdentifiers(Model $model, ?array $filters = [], ?array $without = [], ?string $keyName = null): array
    {
        $keyName = $keyName ?? $model->getKeyName();

        $identifiers = $model::select($keyName);

        // check if we have filters
        if (!empty($filters)) {
            // keep it simple
            // if for any reason we need something more complicated
            // create a new function
            foreach ($filters as $property => $value) {
                $identifiers->where($property, $value);
            }
        }

        // don't load relationships
        if (!empty($without)) {
            $identifiers->without($without);
        }

        $identifiers = $identifiers->get()
            ->pluck($keyName)
            ->toArray();

        return $identifiers;
    }
}
