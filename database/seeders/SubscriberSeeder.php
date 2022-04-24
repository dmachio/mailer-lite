<?php

namespace Database\Seeders;

use App\FieldTypes\FieldTypeDirector;
use App\Models\Field;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

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

    private function getFakeValueForFieldType(string $field_type)
    {
        $field_type_director = new FieldTypeDirector($field_type);

        return $field_type_director->getFakeValue();
    }
}
