<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $type
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */
class Field extends Model
{
    const TYPE_DATE = 'date';
    const TYPE_NUMBER = 'number';
    const TYPE_STRING = 'string';
    const TYPE_BOOLEAN = 'boolean';

    const TYPES = [
        self::TYPE_DATE,
        self::TYPE_NUMBER,
        self::TYPE_NUMBER,
        self::TYPE_BOOLEAN,
    ];

    use HasFactory;
}
