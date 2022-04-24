<?php

namespace App\Http\Requests\Api;

use App\Models\Field;
use App\Services\FieldService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class UpdateFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $current_params = Route::current()->parameters();

        $fields_table = FieldService::getTable();

        return [
            'title' => [
                'required',
                'max:255',
                Rule::unique($fields_table)->ignore($current_params['field'])
            ],
            'type' => ['required', Rule::in(Field::TYPES)],
        ];
    }
}
