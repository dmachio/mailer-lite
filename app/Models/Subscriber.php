<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
