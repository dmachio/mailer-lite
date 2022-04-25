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
        $per_page = $request->get('per_page') ?? 15;

        if ($per_page == -1) {
            $per_page = Field::filter($request->all())->count();
        }

        $sort_by = $request->get('sort_by') ?? 'created_at';
        $sort_order = $request->get('sort_order') ?? 'desc';

        return Field::filter($request->all())
            ->orderBy($sort_by, $sort_order)
            ->paginate($per_page);
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
