<?php

namespace App\Models;

use App\ModelFilters\SubscriberFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string $state
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */
class Subscriber extends Model
{
    use HasFactory;
    use Filterable;

    const STATE_ACTIVE = 'active';
    const STATE_UNSUBSCRIBED = 'unsubscribed';
    const STATE_JUNK = 'junk';
    const STATE_BOUNCED = 'bounced';
    const STATE_UNCONFIRMED = 'unconfirmed';

    const STATES = [
        self::STATE_ACTIVE,
        self::STATE_UNSUBSCRIBED,
        self::STATE_JUNK,
        self::STATE_BOUNCED,
        self::STATE_UNCONFIRMED,
    ];

    protected $guarded = ['id'];

    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class, 'subscriber_fields')
            ->using(SubscriberField::class)
            ->withTimestamps()
            ->withPivot('value');
    }

    public function modelFilter()
    {
        return $this->provideFilter(SubscriberFilter::class);
    }
}
