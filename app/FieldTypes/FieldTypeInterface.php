<?php

namespace App\FieldTypes;

use Illuminate\Contracts\Support\MessageBag;

interface FieldTypeInterface
{
    /**
     * Validation rules that apply to a field of this type
     * 
     * @return array
     */
    public function validationRules(): array;

    /**
     * Validate the given value for this field type
     * 
     * @param mixed $value
     * @return MessageBag|null
     */
    public function validateAndGetErrors($value): ?MessageBag;

    /**
     * Get formatted value based on field type
     * 
     * @param string $value
     * @return mixed
     */
    public function getFormattedValue($value);

    /**
     * Get fake value based on field type - useful for factories and when testing
     * 
     * @return mixed
     */
    public function getFakeValue();
}
