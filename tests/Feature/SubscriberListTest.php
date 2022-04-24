<?php

namespace Tests\Feature;

use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriberListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_paginated_list_of_subscribers()
    {
        $subscribers = Subscriber::factory()
            ->count(50)
            ->create();

        $response = $this->get('/api/subscribers');

        $expected = $subscribers->take(15)
            ->pluck('id')
            ->toArray();

        $response->assertStatus(200);
        $actual = collect($response->decodeResponseJson()['data'])
            ->pluck('id')
            ->toArray();
        $this->assertEquals($expected, $actual, 'Only 15 subscribers are returned');
    }
}
