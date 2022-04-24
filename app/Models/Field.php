<?php

namespace App\Models;

use App\ModelFilters\FieldFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $title
 * @property string $type
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */
class Field extends Model
{
    use HasFactory;
    use Filterable;

    const TYPE_DATE = 'date';
    const TYPE_NUMBER = 'number';
    const TYPE_STRING = 'string';
    const TYPE_BOOLEAN = 'boolean';

    const TYPES = [
        self::TYPE_DATE,
        self::TYPE_NUMBER,
        self::TYPE_STRING,
        self::TYPE_BOOLEAN,
    ];

    protected $fillable = ['title', 'type'];

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'subscriber_fields');
    }

    public function modelFilter()
    {
        return $this->provideFilter(FieldFilter::class);
    }
}
