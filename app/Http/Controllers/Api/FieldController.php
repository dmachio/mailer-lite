<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreFieldRequest;
use App\Http\Requests\Api\UpdateFieldRequest;
use App\Models\Field;
use App\Services\FieldService;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(FieldService::getPaginatedList($request));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Api\StoreFieldRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFieldRequest $request)
    {
        return response()->json(FieldService::createFromRequest($request));
    }

    /**
     * Display the specified resource.
     *
     * @param  Field $field
     * @return \Illuminate\Http\Response
     */
    public function show(Field $field)
    {
        return response()->json($field);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Api\UpdateFieldRequest  $request
     * @param  Field $field
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFieldRequest $request, Field $field)
    {
        return response()->json(FieldService::updateFromRequest($field->id, $request));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Field $field
     * @return \Illuminate\Http\Response
     */
    public function destroy(Field $field)
    {
        return response()->json(['success' => $field->delete()]);
    }
}
