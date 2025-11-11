<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCategory5UnitDetail extends Model
{
    protected $fillable = [
        'project_id',
        'tower_name',
        'saleable_from',
        'saleable_to',
        'saleable_unit_id',
        'wash_area',
        'wash_area_unit_id',
        'balcony_area',
        'balcony_area_unit_id',
        'ceiling_height',
        'ceiling_height_unit_id',
        'servant_room',
        'show_carpet_area',
        'carpet_area_from',
        'carpet_area_to',
        'carpet_area_unit_id',
        'show_builtup_area',
        'builtup_area_from',
        'builtup_area_to',
        'builtup_area_unit_id',
    ];

    protected function casts(): array
    {
        return [
            'servant_room' => 'boolean',
            'show_carpet_area' => 'boolean',
            'show_builtup_area' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function saleableUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'saleable_unit_id');
    }

    public function washAreaUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'wash_area_unit_id');
    }

    public function balconyAreaUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'balcony_area_unit_id');
    }

    public function ceilingHeightUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'ceiling_height_unit_id');
    }

    public function carpetAreaUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'carpet_area_unit_id');
    }

    public function builtupAreaUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'builtup_area_unit_id');
    }
}

