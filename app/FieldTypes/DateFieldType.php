<?php

namespace App\FieldTypes;

use Carbon\Carbon;

class DateFieldType extends BaseFieldType implements FieldTypeInterface
{
    public function validationRules(): array
    {
        return [
            'field' => 'date_format:Y-m-d',
        ];
    }

    public function getFormattedValue($value)
    {
        return new Carbon($value);
    }

    public function getFakeValue()
    {
        return $this->faker->date('Y-m-d');
    }
}
