<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Locality extends Model
{
    protected $fillable = [
        'name',
        'state_id',
        'city_id',
        'zip_code',
        'is_active',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

