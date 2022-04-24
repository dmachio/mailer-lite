<?php

namespace App\Http\Requests\Api\Traits;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use App\FieldTypes\FieldTypeDirector;
use App\Models\Field;

trait ValidatesCustomFieldValues
{
    /**
     * Create the default validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Factory  $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function createDefaultValidator(ValidationFactory $factory)
    {
        $validator = parent::createDefaultValidator($factory);

        $validator->after(function ($validator) {
            $data = $this->validationData();
            $fields = $data['fields'] ?? [];

            foreach ($fields as $field => $value) {
                $field = Field::find($field);

                if ($field) {
                    $director = new FieldTypeDirector($field->type);

                    $errors = $director->validateAndGetErrors($value);

                    if ($errors->isNotEmpty()) {
                        foreach ($errors->all() as $error) {
                            $validator->errors()->add("custom_field_{$field->id}", $error);
                        }
                    }
                }
            }
        });

        return $validator;
    }
}
