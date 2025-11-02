<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $fillable = [
        'name',
        'state_id',
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

    public function talukas(): HasMany
    {
        return $this->hasMany(Taluka::class);
    }

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

