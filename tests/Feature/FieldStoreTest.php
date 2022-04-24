<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Services\FieldService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FieldStoreTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function it_fails_when_title_is_not_given()
    {
        $field = Field::factory()->make();

        $data = $field->toArray();
        $data['title'] = null;

        $response = $this->postJson("/api/fields", $data);
        $response->assertStatus(422);

        $response_data = $response->decodeResponseJson();
        $errors = $response_data['errors'] ?? [];
        $actual = collect($errors)->has('title');
        $expected = true;
        $this->assertEquals($expected, $actual, 'Title is marked as invalid when not provided');
    }

    /** @test */
    public function it_fails_when_title_is_not_unique()
    {
        $title = $this->faker->word;

        Field::factory()->create(['title' => $title]);

        $field = Field::factory()->make();

        $data = $field->toArray();
        $data['title'] = $title;

        $response = $this->postJson("/api/fields", $data);
        $response->assertStatus(422);

        $response_data = $response->decodeResponseJson();
        $errors = $response_data['errors'] ?? [];
        $actual = collect($errors)->has('title');
        $expected = true;
        $this->assertEquals($expected, $actual, 'Title is marked as invalid when not unique');
    }

    /** @test */
    public function it_fails_when_type_is_not_given()
    {
        $field = Field::factory()->make();

        $data = $field->toArray();
        $data['type'] = null;

        $response = $this->postJson("/api/fields", $data);
        $response->assertStatus(422);

        $response_data = $response->decodeResponseJson();
        $errors = $response_data['errors'] ?? [];
        $actual = collect($errors)->has('type');
        $expected = true;
        $this->assertEquals($expected, $actual, 'Type is marked as invalid when not provided');
    }

    /** @test */
    public function it_fails_when_type_is_incorrect()
    {
        $field = Field::factory()->make();

        $data = $field->toArray();
        $data['type'] = "wrong type";

        $response = $this->postJson("/api/fields", $data);
        $response->assertStatus(422);

        $response_data = $response->decodeResponseJson();
        $errors = $response_data['errors'] ?? [];
        $actual = collect($errors)->has('type');
        $expected = true;
        $this->assertEquals($expected, $actual, 'Type is marked as invalid when incorrect');
    }

    /** @test */
    public function it_works_when_all_fields_are_valid()
    {
        $field = Field::factory()->make();

        $new_data = [
            'type' => $this->faker->randomElement(Field::TYPES),
            'title' => $this->faker->word,
        ];
        $data = array_merge(
            $field->toArray(),
            $new_data
        );

        $response = $this->postJson("/api/fields", $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas(
            FieldService::getTable(),
            $new_data
        );
    }
}
