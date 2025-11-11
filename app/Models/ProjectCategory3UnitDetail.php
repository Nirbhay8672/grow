<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCategory3UnitDetail extends Model
{
    protected $fillable = [
        'project_id',
        'total_no_of_units',
        'ceiling_height',
        'ceiling_height_unit_id',
        'plot_area_from',
        'plot_area_to',
        'plot_area_unit_id',
        'road_width_from',
        'road_width_to',
        'road_width_unit_id',
        'constructed_area_from',
        'constructed_area_to',
        'constructed_area_unit_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function ceilingHeightUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'ceiling_height_unit_id');
    }

    public function plotAreaUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'plot_area_unit_id');
    }

    public function roadWidthUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'road_width_unit_id');
    }

    public function constructedAreaUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'constructed_area_unit_id');
    }
}

