<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use Illuminate\Container\Container;

class SubscriberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * @var Collection<Subscriber>
         */
        $subscribers = Subscriber::factory()
            ->count(20)
            ->create();

        /**
         * @var Collection<Field>
         */
        $fields = Field::factory()
            ->count(10)
            ->create();

        foreach ($subscribers as $subscriber) {
            foreach ($fields as $field) {
                $value = $this->getFakeValueForFieldType($field->type);
                $subscriber->fields()->attach($field->id, ['value' => $value]);
            }
        }
    }

    private function getFakeValueForFieldType(string $field_type): string
    {
        $faker = Container::getInstance()->make(Faker::class);

        switch ($field_type) {
            case Field::TYPE_BOOLEAN:
                return $faker->randomElement([1, 0]);

            case Field::TYPE_STRING:
                return $faker->word();

            case Field::TYPE_NUMBER:
                return $faker->randomNumber();

            case Field::TYPE_DATE:
                return $faker->date('Y-m-d');
        }
    }
}
