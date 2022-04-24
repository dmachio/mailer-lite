<?php

namespace App\FieldTypes;

class StringFieldType extends BaseFieldType implements FieldTypeInterface
{
    public function validationRules(): array
    {
        return [];
    }

    public function getFormattedValue($value)
    {
        return $value;
    }

    public function getFakeValue()
    {
        return $this->faker->word();
    }
}
