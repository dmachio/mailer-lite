<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\Traits\ValidatesCustomFieldValues;
use App\Models\Subscriber;
use App\Services\SubscriberService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class UpdateSubscriberRequest extends FormRequest
{
    use ValidatesCustomFieldValues;

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
        $subscribers_table = SubscriberService::getTable();

        return [
            'email' => [
                'email:rfc,dns',
                'required',
                'max:255',
                Rule::unique($subscribers_table)->ignore($current_params['subscriber'])
            ],
            'name' => ['required', 'max:255'],
            'state' => ['required', Rule::in(Subscriber::STATES)],
        ];
    }
}
