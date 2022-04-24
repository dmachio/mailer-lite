<?php

namespace Tests\Feature;

use App\Models\Field;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class FieldShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function it_returns_field()
    {
        $field = Field::factory()
            ->create([
                'title' => $this->faker->word,
                'type' => Field::TYPE_STRING,
            ]);

        $response = $this->get("/api/fields/$field->id");

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->where('id', $field->id)
                ->where('type', $field->type)
                ->where('title', $field->title)
                ->etc()
        );
    }
}
