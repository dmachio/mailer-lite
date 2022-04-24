<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Models\Subscriber;
use App\Services\SubscriberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriberStoreTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function it_fails_when_custom_field_is_invalid()
    {
        $date_field = Field::factory()
            ->create([
                'title' => $this->faker->word,
                'type' => Field::TYPE_DATE,
            ]);

        $number_field = Field::factory()
            ->create([
                'title' => $this->faker->word,
                'type' => Field::TYPE_NUMBER,
            ]);

        $boolean_field = Field::factory()
            ->create([
                'title' => $this->faker->word,
                'type' => Field::TYPE_BOOLEAN,
            ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->freeEmail(),
            'state' => $this->faker->randomElement(Subscriber::STATES),
            'fields' => [
                $date_field->id => '03/04/2020',
                $number_field->id => 'not a number',
                $boolean_field->id => 'not a boolean',
            ]
        ];

        $response = $this->postJson('/api/subscribers', $data);
        $response->assertStatus(422);

        $response_data = $response->decodeResponseJson();
        $errors = $response_data['errors'] ?? [];
        $actual = array_keys($errors);
        sort($actual);

        $expected = collect([
            $date_field->id,
            $boolean_field->id,
            $number_field->id,
        ])->map(fn ($id) => "custom_field_$id")
            ->toArray();
        sort($expected);
        $this->assertEquals($expected, $actual, 'All invalid custom fields are marked as invalid in the response');
    }

    /** @test */
    public function it_fails_when_email_is_incorrectly_formatted()
    {

        $data = [
            'name' => $this->faker->name,
            'email' => "test@",
            'state' => $this->faker->randomElement(Subscriber::STATES),
        ];

        $response = $this->postJson('/api/subscribers', $data);
        $response->assertStatus(422);

        $response_data = $response->decodeResponseJson();
        $errors = $response_data['errors'] ?? [];
        $actual = collect($errors)->has('email');
        $expected = true;
        $this->assertEquals($expected, $actual, 'Incorrectly formatted email is marked as invalid');
    }

    /** @test */
    public function it_fails_when_email_uses_inactive_domain()
    {

        $data = [
            'name' => $this->faker->name,
            'email' => "test@example.net",
            'state' => $this->faker->randomElement(Subscriber::STATES),
        ];

        $response = $this->postJson('/api/subscribers', $data);
        $response->assertStatus(422);

        $response_data = $response->decodeResponseJson();
        $errors = $response_data['errors'] ?? [];
        $actual = collect($errors)->has('email');
        $expected = true;
        $this->assertEquals($expected, $actual, 'Email using inactive domain is marked as invalid');
    }

    /** @test */
    public function it_fails_when_email_is_not_unique()
    {
        $email = $this->faker->freeEmail();

        Subscriber::factory()->create(['email' => $email]);

        $data = [
            'name' => $this->faker->name,
            'email' => $email,
            'state' => $this->faker->randomElement(Subscriber::STATES),
        ];

        $response = $this->postJson('/api/subscribers', $data);
        $response->assertStatus(422);

        $response_data = $response->decodeResponseJson();
        $errors = $response_data['errors'] ?? [];
        $actual = collect($errors)->has('email');
        $expected = true;
        $this->assertEquals($expected, $actual, 'Email marked as invalid when not unique');
    }

    /** @test */
    public function it_fails_when_state_is_not_provided()
    {

        $data = [
            'name' => $this->faker->name,
            'email' => "test@example.net",
        ];

        $response = $this->postJson('/api/subscribers', $data);
        $response->assertStatus(422);

        $response_data = $response->decodeResponseJson();
        $errors = $response_data['errors'] ?? [];
        $actual = collect($errors)->has('state');
        $expected = true;
        $this->assertEquals($expected, $actual, 'State is marked as invalid when it\'s not provided');
    }

    /** @test */
    public function it_fails_when_state_is_incorrect()
    {

        $data = [
            'name' => $this->faker->name,
            'email' => "test@example.net",
            'state' => "wrong state"
        ];

        $response = $this->postJson('/api/subscribers', $data);
        $response->assertStatus(422);

        $response_data = $response->decodeResponseJson();
        $errors = $response_data['errors'] ?? [];
        $actual = collect($errors)->has('state');
        $expected = true;
        $this->assertEquals($expected, $actual, 'State is marked as invalid when it\'s incorrect');
    }

    /** @test */
    public function it_fails_when_name_is_not_provided()
    {

        $data = [
            'email' => "test@example.net",
            'state' => $this->faker->randomElement(Subscriber::STATES),
        ];

        $response = $this->postJson('/api/subscribers', $data);
        $response->assertStatus(422);

        $response_data = $response->decodeResponseJson();
        $errors = $response_data['errors'] ?? [];
        $actual = collect($errors)->has('name');
        $expected = true;
        $this->assertEquals($expected, $actual, 'Name is marked as invalid when it\'s not provided');
    }

    /** @test */
    public function it_works_when_all_fields_are_valid()
    {
        $date_field = Field::factory()
            ->create([
                'title' => $this->faker->word,
                'type' => Field::TYPE_DATE,
            ]);

        $number_field = Field::factory()
            ->create([
                'title' => $this->faker->word,
                'type' => Field::TYPE_NUMBER,
            ]);

        $boolean_field = Field::factory()
            ->create([
                'title' => $this->faker->word,
                'type' => Field::TYPE_BOOLEAN,
            ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->freeEmail(),
            'state' => $this->faker->randomElement(Subscriber::STATES),
            'fields' => [
                $date_field->id => now()->format('Y-m-d'),
                $number_field->id => $this->faker->randomNumber(),
                $boolean_field->id => $this->faker->randomElement([1, 0]),
            ]
        ];

        $response = $this->postJson('/api/subscribers', $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas(
            SubscriberService::getTable(),
            collect($data)->except('fields')->toArray()
        );

        $subscriber_id = Subscriber::query()
            ->where(collect($data)->except('fields')->toArray())
            ->pluck('id')
            ->first();

        $subscriber_fields = [
            $date_field,
            $boolean_field,
            $number_field
        ];
        
        foreach ($subscriber_fields as $field) {
            $this->assertDatabaseHas(
                'subscriber_fields',
                [
                    'field_id' => $field->id,
                    'subscriber_id' => $subscriber_id,
                    'value' => $data['fields'][$field->id],
                ]
            );
        }
    }
}
