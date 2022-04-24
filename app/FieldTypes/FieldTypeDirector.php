<?php

namespace App\FieldTypes;

use Illuminate\Contracts\Support\MessageBag;

class FieldTypeDirector
{
    private $field_type_class;

    /**
     * @var FieldTypeInterface
     */
    private $field_type_instance;

    public function __construct(string $field_type)
    {
        $field_types = [
            'string' => StringFieldType::class,
            'boolean' => BooleanFieldType::class,
            'date' => DateFieldType::class,
            'number' => NumberFieldType::class,
        ];

        $this->field_type_class = $field_types[$field_type] ?? null;

        if ($this->field_type_class) {
            $this->field_type_instance = new $this->field_type_class;
        }
    }

    public function validateAndGetErrors($value): ?MessageBag
    {
        return optional($this->field_type_instance)->validateAndGetErrors($value);
    }

    public function getFormattedValue($value)
    {
        return optional($this->field_type_instance)->getFormattedValue($value);
    }

    public function getFakeValue()
    {
        return optional($this->field_type_instance)->getFakeValue();
    }
}
