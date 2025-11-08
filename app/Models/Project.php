<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'builder_id',
        'builder_website',
        'project_name',
        'address',
        'state_id',
        'city_id',
        'locality_id',
        'pincode',
        'location_link',
        'land_size',
        'measurement_unit_id',
        'rera_no',
        'project_status',
        'restricted_user_ids',
        'user_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'restricted_user_ids' => 'array',
        ];
    }

    public function builder(): BelongsTo
    {
        return $this->belongsTo(Builder::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function locality(): BelongsTo
    {
        return $this->belongsTo(Locality::class);
    }

    public function measurementUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restrictedUsers()
    {
        if (!$this->restricted_user_ids || empty($this->restricted_user_ids)) {
            return collect([]);
        }
        return User::whereIn('id', $this->restricted_user_ids)->get();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ProjectContact::class);
    }
}

