<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Models\Subscriber;
use App\Services\SubscriberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriberUpdateTest extends TestCase
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

        $subscriber = Subscriber::factory()->create();
        $data = array_merge(
            $subscriber->toArray(),
            [
                'fields' => [
                    $date_field->id => '03/04/2020',
                    $number_field->id => 'not a number',
                    $boolean_field->id => 'not a boolean',
                ]
            ]
        );

        $response = $this->putJson("/api/subscribers/$subscriber->id", $data);
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
        $subscriber = Subscriber::factory()->create();

        $data = $subscriber->toArray();
        $data['email'] = "test@";

        $response = $this->putJson("/api/subscribers/{$subscriber->id}", $data);
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
        $subscriber = Subscriber::factory()->create();

        $data = $subscriber->toArray();
        $data['email'] = 'test@example.net';

        $response = $this->putJson("/api/subscribers/{$subscriber->id}", $data);
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

        $subscriber = Subscriber::factory()->create();

        $data = $subscriber->toArray();
        $data['email'] = $email;

        $response = $this->putJson("/api/subscribers/{$subscriber->id}", $data);
        $response->assertStatus(422);

        $response_data = $response->decodeResponseJson();
        $errors = $response_data['errors'] ?? [];
        $actual = collect($errors)->has('email');
        $expected = true;
        $this->assertEquals($expected, $actual, 'Email is marked as invalid when not unique');
    }

    /** @test */
    public function it_fails_when_state_is_not_provided()
    {
        $subscriber = Subscriber::factory()->create();

        $data = $subscriber->toArray();
        $data['state'] = null;

        $response = $this->putJson("/api/subscribers/{$subscriber->id}", $data);
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
        $subscriber = Subscriber::factory()->create();

        $data = $subscriber->toArray();
        $data['state'] = 'wrong state';

        $response = $this->putJson("/api/subscribers/{$subscriber->id}", $data);
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
        $subscriber = Subscriber::factory()->create();

        $data = $subscriber->toArray();
        $data['name'] = null;

        $response = $this->putJson("/api/subscribers/{$subscriber->id}", $data);
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

        $subscriber = Subscriber::factory()->create();

        $subscriber->fields()->attach(
            $date_field->id,
            ['value' => now()->addMonth()->format('Y-m-d')],
        );

        $new_data = [
            'name' => $this->faker->name,
            'email' => $this->faker->freeEmail(),
            'state' => $this->faker->randomElement(Subscriber::STATES),
            'fields' => [
                $date_field->id => now()->format('Y-m-d'),
                $number_field->id => $this->faker->randomNumber(),
                $boolean_field->id => $this->faker->randomElement([1, 0]),
            ]
        ];
        $data = array_merge(
            $subscriber->toArray(),
            $new_data
        );

        $response = $this->putJson("/api/subscribers/{$subscriber->id}", $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas(
            SubscriberService::getTable(),
            collect($new_data)->except('fields')->toArray()
        );

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
                    'subscriber_id' => $subscriber->id,
                    'value' => $new_data['fields'][$field->id],
                ]
            );
        }
    }
}
