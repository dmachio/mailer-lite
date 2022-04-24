<?php

namespace Tests\Feature;

use App\Models\Field;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FieldListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_paginated_list_of_fields()
    {
        $fields = Field::factory()
            ->count(50)
            ->create();

        $response = $this->get('/api/fields');

        $expected = $fields->take(15)
            ->pluck('id')
            ->toArray();

        $response->assertStatus(200);
        $actual = collect($response->decodeResponseJson()['data'])
            ->pluck('id')
            ->toArray();
        $this->assertEquals($expected, $actual, 'Only 15 subscribers are returned');
    }
}
