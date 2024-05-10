<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbTestVariant extends Model
{
    use HasFactory;

    public function abTest()
    {
        return $this->belongsTo(AbTest::class);
    }

    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'sessions_ab_test_variants', 'ab_test_variant_id', 'session_id');
    }
}
