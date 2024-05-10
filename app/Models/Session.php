<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read EloquentCollection<int, Event> $events
 */
class Session extends Model
{
    use HasFactory;

    /**
     * @return HasMany<Event>
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function abTestVariants(): BelongsToMany
    {
        return $this->belongsToMany(AbTestVariant::class, 'sessions_ab_test_variants', 'session_id', 'ab_test_variant_id');
    }
}
