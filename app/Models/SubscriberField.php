<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $subscriber_id
 * @property int $field_id
 * @property string|Carbon|float|bool $value
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */
class SubscriberField extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Cast the value depending on the field type
     * 
     * @param string $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        switch ($this->field->type) {
            case Field::TYPE_BOOLEAN:
                return (bool) $value;

            case Field::TYPE_NUMBER:
                return (float) $value;

            case Field::TYPE_DATE:
                return new Carbon($value);

            default:
                return $value;
        }
    }
}
