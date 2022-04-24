<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSubscriberRequest;
use App\Http\Requests\Api\UpdateSubscriberRequest;
use App\Models\Subscriber;
use App\Services\SubscriberService;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(SubscriberService::getPaginatedList($request));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Api\StoreSubscriberRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSubscriberRequest $request)
    {
        return response()->json(SubscriberService::createFromRequest($request));
    }

    /**
     * Display the specified resource.
     *
     * @param  Subscriber $subscriber
     * @return \Illuminate\Http\Response
     */
    public function show(Subscriber $subscriber)
    {
        $subscriber->load('fields');

        return response()->json($subscriber);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Api\UpdateSubscriberRequest  $request
     * @param  Subscriber $subscriber
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSubscriberRequest $request, Subscriber $subscriber)
    {
        return response()->json(SubscriberService::updateFromRequest($subscriber->id, $request));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Subscriber $subscriber
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscriber $subscriber)
    {
        return response()->json(['success' => $subscriber->delete()]);
    }
}
