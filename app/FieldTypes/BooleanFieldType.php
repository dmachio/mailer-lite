<?php

namespace App\FieldTypes;

class BooleanFieldType extends BaseFieldType implements FieldTypeInterface
{
    public function validationRules(): array
    {
        return [
            'field' => 'boolean',
        ];
    }

    public function getFormattedValue($value)
    {
        return (bool) $value;
    }

    public function getFakeValue()
    {
        return $this->faker->randomElement([1, 0]);
    }
}
