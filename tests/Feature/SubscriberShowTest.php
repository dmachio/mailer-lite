<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriberShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function it_returns_subscriber_with_fields()
    {
        $custom_field = Field::factory()
            ->create([
                'title' => $this->faker->word,
                'type' => Field::TYPE_STRING,
            ]);
        $subscriber = Subscriber::factory()->create();

        $custom_field_value = $this->faker->word();
        $subscriber->fields()->attach(
            $custom_field->id,
            ['value' => $custom_field_value],
        );

        $response = $this->get("/api/subscribers/$subscriber->id");

        $response->assertStatus(200);

        $response->assertJsonPath('fields.*.pivot.value', [$custom_field_value]);
    }
}
