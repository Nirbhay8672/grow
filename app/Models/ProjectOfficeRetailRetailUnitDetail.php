<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectOfficeRetailRetailUnitDetail extends Model
{
    protected $fillable = [
        'project_id',
        'tower_name',
        'sub_category_id',
        'size_from',
        'size_to',
        'size_unit_id',
        'front_opening',
        'front_opening_unit_id',
        'no_of_unit_each_floor',
        'ceiling_height',
        'ceiling_height_unit_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function sizeUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'size_unit_id');
    }

    public function frontOpeningUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'front_opening_unit_id');
    }

    public function ceilingHeightUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'ceiling_height_unit_id');
    }
}


