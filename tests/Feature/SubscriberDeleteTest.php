<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Models\Subscriber;
use App\Services\SubscriberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriberDeleteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function it_deletes_subscriber()
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

        $response = $this->delete("/api/subscribers/$subscriber->id");

        $response->assertStatus(200);

        $this->assertDatabaseMissing(
            SubscriberService::getTable(),
            [
                'id' => $subscriber->id,
            ]
        );

        $this->assertDatabaseMissing(
            'subscriber_fields',
            [
                'subscriber_id' => $subscriber->id,
            ]
        );
    }
}
