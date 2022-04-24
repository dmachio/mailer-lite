<?php

namespace App\FieldTypes;

class NumberFieldType extends BaseFieldType implements FieldTypeInterface
{
    public function validationRules(): array
    {
        return [
            'field' => 'numeric',
        ];
    }

    public function getFormattedValue($value)
    {
        return (float) $value;
    }

    public function getFakeValue()
    {
        return $this->faker->randomNumber();
    }
}
