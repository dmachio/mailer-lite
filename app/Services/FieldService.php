<?php

namespace App\Services;

use App\Models\Field;
use Illuminate\Http\Request;

class FieldService
{
    public static function getTable()
    {
        return (new Field())->getTable();
    }

    public static function getPaginatedList(Request $request)
    {
        return Field::filter($request->all())->paginate();
    }

    private static function fillableAttributes(): array
    {
        return [
            'title',
            'type',
        ];
    }

    public static function createFromRequest(Request $request)
    {
        $attributes = $request->only(self::fillableAttributes());

        return Field::query()->create($attributes);
    }

    public static function updateFromRequest(int $id, Request $request)
    {
        $attributes = $request->only(self::fillableAttributes());

        $Field = Field::query()->findOrFail($id);

        $Field->update($attributes);

        return $Field;
    }
}
