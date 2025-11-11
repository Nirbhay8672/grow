<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        // Step 1: Builder Information
        'builder_id',
        'builder_website',
        
        // Step 1: Project Information
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
        
        // Step 2: Construction Type & Category
        'construction_type_id',
        'category_id',
        'sub_category_id',
        'sub_category_ids',
        
        // Step 2: Tower Details
        'no_of_towers',
        'no_of_floors',
        'total_units',
        'no_of_unit_each_tower',
        'no_of_lift',
        'front_road_width',
        'front_road_width_measurement_unit_id',
        'washroom',
        'two_road_corner',
        'towers_different_specification',
        'retail_unit_details',
        'category3_utility_board',
        'category3_dynamic_facilities',
        
        // Step 3: Parking Details
        'free_allotted_parking_four_wheeler',
        'free_allotted_parking_two_wheeler',
        'available_for_purchase',
        'no_of_parking',
        'total_floor_for_parking',
        
        // Step 3: Other
        'remark',
        'brochure_file',
        
        // Common
        'user_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'restricted_user_ids' => 'array',
            'sub_category_ids' => 'array',
            'towers_different_specification' => 'boolean',
            'two_road_corner' => 'boolean',
            'retail_unit_details' => 'array',
            'category3_utility_board' => 'array',
            'category3_dynamic_facilities' => 'array',
            'free_allotted_parking_four_wheeler' => 'boolean',
            'free_allotted_parking_two_wheeler' => 'boolean',
            'available_for_purchase' => 'boolean',
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

    // Step 2: Construction Type & Category relationships
    public function constructionType(): BelongsTo
    {
        return $this->belongsTo(ConstructionType::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    // Step 2: Tower Details relationship
    public function towerDetails(): HasMany
    {
        return $this->hasMany(ProjectTowerDetail::class);
    }

    // Step 2: Category 3 Unit Details relationship
    public function category3UnitDetails(): HasMany
    {
        return $this->hasMany(ProjectCategory3UnitDetail::class);
    }

    // Step 2: Front Road Width Measurement Unit
    public function frontRoadWidthMeasurementUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'front_road_width_measurement_unit_id');
    }

    // Step 3: Basement Parking relationship
    public function basementParking(): HasMany
    {
        return $this->hasMany(ProjectBasementParking::class);
    }

    // Step 3: Amenities relationship (many-to-many)
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'project_amenities');
    }

    // Step 3: Documents relationship
    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class);
    }
}

