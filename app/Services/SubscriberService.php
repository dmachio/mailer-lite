<?php

namespace App\Services;

use App\Models\Subscriber;
use App\Models\SubscriberField;
use Illuminate\Http\Request;

class SubscriberService
{
    public static function getTable()
    {
        return (new Subscriber)->getTable();
    }

    public static function getPaginatedList(Request $request)
    {
        $per_page = $request->get('per_page') ?? 15;

        if ($per_page == -1) {
            $per_page = Subscriber::filter($request->all())->count();
        }

        $sort_by = $request->get('sort_by') ?? 'created_at';
        $sort_order = $request->get('sort_order') ?? 'desc';

        return Subscriber::filter($request->all())->orderBy($sort_by, $sort_order)
            ->paginate($per_page);
    }

    private static function fillableAttributes(): array
    {
        return [
            'name',
            'email',
            'state',
        ];
    }

    private static function fieldValueEmpty($field_value)
    {
        return empty($field_value) && $field_value !== "0" && $field_value !== 0 && $field_value !== false;
    }

    private static function saveFieldsForNewSubscriber(Request $request, Subscriber $subscriber)
    {
        $fields = $request->get('fields');
        $fields = is_array($fields) ? $fields : [];
        $fields = collect($fields)->filter(fn ($field_value) => !self::fieldValueEmpty($field_value));

        foreach ($fields as $field_id => $value) {
            SubscriberField::query()->create([
                'field_id' => $field_id,
                'value' => $value,
                'subscriber_id' => $subscriber->id,
            ]);
        }
    }

    public static function createFromRequest(Request $request)
    {
        $attributes = $request->only(self::fillableAttributes());

        $subscriber = Subscriber::query()->create($attributes);

        self::saveFieldsForNewSubscriber($request, $subscriber);

        return $subscriber;
    }

    private static function saveFieldsForExistingSubscriber(Request $request, Subscriber $subscriber)
    {
        $fields = $request->get('fields');
        $fields = is_array($fields) ? $fields : [];

        foreach ($fields as $field_id => $value) {
            $subscriber_field = SubscriberField::query()->where([
                'subscriber_id' => $subscriber->id,
                'field_id' => $field_id,
            ])->first();

            if ($subscriber_field && !empty($value)) {
                $subscriber_field->update(['value' => $value]);
                continue;
            }

            if ($subscriber_field && empty($value)) {
                $subscriber_field->delete();
                continue;
            }

            if (self::fieldValueEmpty($value)) {
                continue;
            }

            SubscriberField::query()->create([
                'field_id' => $field_id,
                'value' => $value,
                'subscriber_id' => $subscriber->id,
            ]);
        }
    }

    public static function updateFromRequest(int $id, Request $request)
    {
        $attributes = $request->only(self::fillableAttributes());

        $subscriber = Subscriber::query()->findOrFail($id);

        $subscriber->update($attributes);

        self::saveFieldsForExistingSubscriber($request, $subscriber);

        return $subscriber;
    }
}
