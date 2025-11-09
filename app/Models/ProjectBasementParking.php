<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectBasementParking extends Model
{
    protected $fillable = [
        'project_id',
        'floor_no',
        'ev_charging_point',
        'hydraulic_parking',
        'height_of_basement',
        'height_of_basement_unit_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function heightOfBasementUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'height_of_basement_unit_id');
    }
}

