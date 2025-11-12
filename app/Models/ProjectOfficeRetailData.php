<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectOfficeRetailData extends Model
{
    protected $fillable = [
        'project_id',
        'office_sub_category_id',
        'no_of_towers',
        'no_of_floors',
        'no_of_unit_each_tower',
        'no_of_lift',
        'front_road_width',
        'front_road_width_unit_id',
        'washroom',
        'two_road_corner',
        'tower_name',
        'total_units',
        'saleable_from',
        'saleable_to',
        'saleable_unit_id',
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
            'two_road_corner' => 'boolean',
            'show_carpet_area' => 'boolean',
            'show_builtup_area' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function officeSubCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'office_sub_category_id');
    }

    public function frontRoadWidthUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'front_road_width_unit_id');
    }

    public function saleableUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'saleable_unit_id');
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


