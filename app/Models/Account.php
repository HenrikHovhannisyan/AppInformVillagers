<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'field_size',
        'tree_count',
        'olive_type',
        'age_of_trees',
        'location_of_field',
        'continuous_season_count',
        'total_harvested_olives',
        'total_gained_oil',
        'account_creation_date',
        'is_request_pending',
        'is_approved',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
