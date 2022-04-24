<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Services\FieldService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FieldDeleteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function it_deletes_field()
    {
        $field = Field::factory()
            ->create([
                'title' => $this->faker->word,
                'type' => Field::TYPE_STRING,
            ]);

        $response = $this->delete("/api/fields/$field->id");

        $response->assertStatus(200);

        $this->assertDatabaseMissing(
            FieldService::getTable(),
            [
                'id' => $field->id,
            ]
        );

        $this->assertDatabaseMissing(
            'subscriber_fields',
            [
                'field_id' => $field->id,
            ]
        );
    }
}
