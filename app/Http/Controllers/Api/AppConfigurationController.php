<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Subscriber;

class AppConfigurationController extends Controller
{
    /**
     * Return the application's configuration.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $field_types = Field::TYPES;
        sort($field_types);

        $subscriber_states = Subscriber::STATES;
        sort($subscriber_states);

        $fields = Field::get();

        return response()->json([
            'field_types' => $field_types,
            'subscriber_states' => $subscriber_states,
            'fields' => $fields
        ]);
    }
}
