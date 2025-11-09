<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTowerDetail extends Model
{
    protected $fillable = [
        'project_id',
        'tower_name',
        'saleable_area_from',
        'saleable_area_to',
        'saleable_area_unit_id',
        'ceiling_height',
        'ceiling_height_unit_id',
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
            'show_carpet_area' => 'boolean',
            'show_builtup_area' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function saleableAreaUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'saleable_area_unit_id');
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

