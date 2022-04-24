<?php

namespace App\FieldTypes;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;

abstract class BaseFieldType implements FieldTypeInterface
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();
    }

    abstract public function validationRules(): array;

    public function validateAndGetErrors($value): ?MessageBag
    {
        $field_validator = Validator::make(['field' => $value], $this->validationRules());

        return $field_validator->errors();
    }
}
