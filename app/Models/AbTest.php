<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @property int $id
 * @property string $status
 */
class AbTest extends Model
{
    use HasFactory;

    const STATUS_READY = 'ready';
    const STATUS_STARTED = 'started';
    const STATUS_STOPPED = 'stopped';

    public function variants()
    {
        return $this->hasMany(AbTestVariant::class);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', self::STATUS_STARTED);
    }
}
