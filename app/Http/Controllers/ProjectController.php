<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Models\Amenity;
use App\Models\Builder;
use App\Models\Category;
use App\Models\City;
use App\Models\ConstructionType;
use App\Models\Locality;
use App\Models\MeasurementUnit;
use App\Models\Project;
use App\Models\ProjectBasementParking;
use App\Models\ProjectCategory3UnitDetail;
use App\Models\ProjectCategory4TowerDetail;
use App\Models\ProjectCategory4UnitDetail;
use App\Models\ProjectCategory5TowerDetail;
use App\Models\ProjectCategory5UnitDetail;
use App\Models\ProjectCategory6Data;
use App\Models\ProjectOfficeRetailData;
use App\Models\ProjectOfficeRetailRetailUnitDetail;
use App\Models\ProjectContact;
use App\Models\ProjectDocument;
use App\Models\ProjectTowerDetail;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        $projects = Project::with(['builder', 'state', 'city', 'locality', 'contacts'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('projects/Projects', [
            'projects' => $projects,
        ]);
    }

    public function create(): Response
    {
        $builders = Builder::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $states = State::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $measurementUnits = MeasurementUnit::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Static restricted user options
        $restrictedUserOptions = [
            ['id' => 'hospital', 'name' => 'Hospital', 'email' => ''],
            ['id' => 'bachelor', 'name' => 'Bachelor', 'email' => ''],
        ];

        $constructionTypes = ConstructionType::where('is_active', true)
            ->with(['categories' => function ($query) {
                $query->where('is_active', true)
                    ->with(['subCategories' => function ($subQuery) {
                        $subQuery->where('is_active', true);
                    }]);
            }])
            ->get(['id', 'name']);

        $amenities = Amenity::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'is_active']);

        return Inertia::render('projects/CreateProject', [
            'builders' => $builders,
            'states' => $states,
            'measurementUnits' => $measurementUnits,
            'restrictedUserOptions' => $restrictedUserOptions,
            'constructionTypes' => $constructionTypes,
            'amenities' => $amenities,
        ]);
    }

    public function getCitiesByState(Request $request, State $state)
    {
        $cities = City::where('state_id', $state->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($cities);
    }

    public function getLocalitiesByCity(Request $request, City $city)
    {
        $localities = Locality::where('city_id', $city->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'zip_code']);

        return response()->json($localities);
    }

    public function store(ProjectStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            // Prepare project data
            $projectData = $request->only([
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
                'construction_type_id',
                'category_id',
                'no_of_towers',
                'no_of_floors',
                'total_units',
                'no_of_unit_each_tower',
                'no_of_lift',
                'front_road_width',
                'front_road_width_measurement_unit_id',
                'washroom',
                'total_floor_for_parking',
                'remark',
            ]);

            // Handle sub_category_id - single value or array for Retail, Category 4, and Category 7
            if ($request->has('sub_category_ids') && is_array($request->sub_category_ids) && count($request->sub_category_ids) > 0) {
                // For Retail category (ID 2), Category 4 (ID 4), and Category 7 (ID 7), store multiple in sub_category_ids JSON column
                $projectData['sub_category_ids'] = json_encode($request->sub_category_ids);
                $projectData['sub_category_id'] = null; // Set single to null when using multiple
            } elseif ($request->has('sub_category_id') && !empty($request->sub_category_id)) {
                // For other categories, single value
                $projectData['sub_category_id'] = $request->sub_category_id;
                $projectData['sub_category_ids'] = null; // Set multiple to null when using single
            } else {
                // No sub-category selected
                $projectData['sub_category_id'] = null;
                $projectData['sub_category_ids'] = null;
            }

            // Handle checkbox fields - explicitly set to false if not present or '0'
            $projectData['towers_different_specification'] = $request->has('towers_different_specification') 
                && ($request->towers_different_specification === '1' || $request->towers_different_specification === true || $request->towers_different_specification === 1);
            
            $projectData['two_road_corner'] = $request->has('two_road_corner') 
                && ($request->two_road_corner === '1' || $request->two_road_corner === true || $request->two_road_corner === 1);
            
            // Handle retail_unit_details - store as JSON array
            if ($request->has('retail_unit_details')) {
                $retailUnitDetails = $request->input('retail_unit_details');
                if (is_array($retailUnitDetails) && count($retailUnitDetails) > 0) {
                    // Filter out empty entries
                    $filteredDetails = array_filter($retailUnitDetails, function($unit) {
                        return !empty($unit['tower_name']) || !empty($unit['sub_category_id']) || !empty($unit['size_from']);
                    });
                    $projectData['retail_unit_details'] = !empty($filteredDetails) ? json_encode(array_values($filteredDetails)) : null;
                } else {
                    $projectData['retail_unit_details'] = null;
                }
            } else {
                $projectData['retail_unit_details'] = null;
            }

            // Handle Category 3: Utility Board and Dynamic Facilities (Construction Type 1, Category 3)
            if ($request->has('utility_board')) {
                $utilityBoard = $request->input('utility_board');
                if (is_array($utilityBoard) && !empty($utilityBoard)) {
                    $projectData['category3_utility_board'] = json_encode($utilityBoard);
                } else {
                    $projectData['category3_utility_board'] = null;
                }
            } else {
                $projectData['category3_utility_board'] = null;
            }

            if ($request->has('dynamic_facilities')) {
                $dynamicFacilities = $request->input('dynamic_facilities');
                if (is_array($dynamicFacilities) && count($dynamicFacilities) > 0) {
                    // Filter out empty entries
                    $filteredFacilities = array_filter($dynamicFacilities, function($facility) {
                        return !empty($facility['label']) || !empty($facility['value']);
                    });
                    $projectData['category3_dynamic_facilities'] = !empty($filteredFacilities) ? json_encode(array_values($filteredFacilities)) : null;
                } else {
                    $projectData['category3_dynamic_facilities'] = null;
                }
            } else {
                $projectData['category3_dynamic_facilities'] = null;
            }
            
            $projectData['free_allotted_parking_four_wheeler'] = $request->has('free_allotted_parking_four_wheeler') 
                && ($request->free_allotted_parking_four_wheeler === '1' || $request->free_allotted_parking_four_wheeler === true || $request->free_allotted_parking_four_wheeler === 1);
            
            $projectData['free_allotted_parking_two_wheeler'] = $request->has('free_allotted_parking_two_wheeler') 
                && ($request->free_allotted_parking_two_wheeler === '1' || $request->free_allotted_parking_two_wheeler === true || $request->free_allotted_parking_two_wheeler === 1);
            
            $projectData['available_for_purchase'] = $request->has('available_for_purchase') 
                && ($request->available_for_purchase === '1' || $request->available_for_purchase === true || $request->available_for_purchase === 1);
            
            // Handle no_of_parking - set to null if available_for_purchase is false
            if ($projectData['available_for_purchase']) {
                $projectData['no_of_parking'] = $request->input('no_of_parking');
            } else {
                $projectData['no_of_parking'] = null;
            }

            // Category 4 Total Room (Construction Type 2, Category 4)
            if ($request->has('category4_total_room')) {
                $projectData['category4_total_room'] = $request->input('category4_total_room');
            }

            // Category 5 Total Room (Construction Type 2, Category 5)
            if ($request->has('category5_total_room')) {
                $projectData['category5_total_room'] = $request->input('category5_total_room');
            }

            $projectData['user_id'] = auth()->id();

            // Handle brochure file upload
            if ($request->hasFile('brochure_file')) {
                $brochureFile = $request->file('brochure_file');
                $brochurePath = $brochureFile->store('projects/brochures', 'public');
                $projectData['brochure_file'] = $brochurePath;
            }

            // Create project
            $project = Project::create($projectData);

            // Create contacts
            if ($request->has('contacts') && is_array($request->contacts)) {
                foreach ($request->contacts as $contactData) {
                    ProjectContact::create([
                        'project_id' => $project->id,
                        'name' => $contactData['name'],
                        'mobile' => $contactData['mobile'],
                        'email' => $contactData['email'] ?? null,
                        'designation' => $contactData['designation'] ?? null,
                    ]);
                }
            }

            // Create tower details
            if ($request->has('tower_details') && is_array($request->tower_details)) {
                foreach ($request->tower_details as $towerData) {
                    ProjectTowerDetail::create([
                        'project_id' => $project->id,
                        'tower_name' => $towerData['tower_name'] ?? null,
                        'saleable_area_from' => $towerData['saleable_area_from'] ?? null,
                        'saleable_area_to' => $towerData['saleable_area_to'] ?? null,
                        'saleable_area_unit_id' => $towerData['saleable_area_unit_id'] ?? null,
                        'ceiling_height' => $towerData['ceiling_height'] ?? null,
                        'ceiling_height_unit_id' => $towerData['ceiling_height_unit_id'] ?? null,
                        'show_carpet_area' => $towerData['show_carpet_area'] ?? false,
                        'carpet_area_from' => $towerData['carpet_area_from'] ?? null,
                        'carpet_area_to' => $towerData['carpet_area_to'] ?? null,
                        'carpet_area_unit_id' => $towerData['carpet_area_unit_id'] ?? null,
                        'show_builtup_area' => $towerData['show_builtup_area'] ?? false,
                        'builtup_area_from' => $towerData['builtup_area_from'] ?? null,
                        'builtup_area_to' => $towerData['builtup_area_to'] ?? null,
                        'builtup_area_unit_id' => $towerData['builtup_area_unit_id'] ?? null,
                    ]);
                }
            }

            // Create Category 3 unit details (Construction Type 1, Category 3)
            if ($request->has('category3_unit_details') && is_array($request->category3_unit_details)) {
                foreach ($request->category3_unit_details as $unitData) {
                    // Filter out completely empty entries
                    if (!empty($unitData['total_no_of_units']) || 
                        !empty($unitData['ceiling_height']) || 
                        !empty($unitData['plot_area_from']) || 
                        !empty($unitData['road_width_from']) || 
                        !empty($unitData['constructed_area_from'])) {
                        ProjectCategory3UnitDetail::create([
                            'project_id' => $project->id,
                            'total_no_of_units' => $unitData['total_no_of_units'] ?? null,
                            'ceiling_height' => $unitData['ceiling_height'] ?? null,
                            'ceiling_height_unit_id' => $unitData['ceiling_height_unit_id'] ?? null,
                            'plot_area_from' => $unitData['plot_area_from'] ?? null,
                            'plot_area_to' => $unitData['plot_area_to'] ?? null,
                            'plot_area_unit_id' => $unitData['plot_area_unit_id'] ?? null,
                            'road_width_from' => $unitData['road_width_from'] ?? null,
                            'road_width_to' => $unitData['road_width_to'] ?? null,
                            'road_width_unit_id' => $unitData['road_width_unit_id'] ?? null,
                            'constructed_area_from' => $unitData['constructed_area_from'] ?? null,
                            'constructed_area_to' => $unitData['constructed_area_to'] ?? null,
                            'constructed_area_unit_id' => $unitData['constructed_area_unit_id'] ?? null,
                        ]);
                    }
                }
            }

            // Create Category 4 tower details (Construction Type 2, Category 4)
            if ($request->has('category4_tower_details') && is_array($request->category4_tower_details)) {
                foreach ($request->category4_tower_details as $towerData) {
                    // Filter out completely empty entries
                    if (!empty($towerData['tower_name']) || 
                        !empty($towerData['total_units']) || 
                        !empty($towerData['total_floor'])) {
                        ProjectCategory4TowerDetail::create([
                            'project_id' => $project->id,
                            'tower_name' => $towerData['tower_name'] ?? null,
                            'total_units' => $towerData['total_units'] ?? null,
                            'total_floor' => $towerData['total_floor'] ?? null,
                            'sub_category_ids' => $towerData['sub_category_ids'] ?? [],
                        ]);
                    }
                }
            }

            // Create Category 4 unit details (Construction Type 2, Category 4)
            if ($request->has('category4_unit_details') && is_array($request->category4_unit_details)) {
                foreach ($request->category4_unit_details as $unitData) {
                    // Filter out completely empty entries
                    if (!empty($unitData['tower_name']) || 
                        !empty($unitData['saleable_from']) || 
                        !empty($unitData['wash_area']) || 
                        !empty($unitData['balcony_area']) || 
                        !empty($unitData['ceiling_height'])) {
                        ProjectCategory4UnitDetail::create([
                            'project_id' => $project->id,
                            'tower_name' => $unitData['tower_name'] ?? null,
                            'saleable_from' => $unitData['saleable_from'] ?? null,
                            'saleable_to' => $unitData['saleable_to'] ?? null,
                            'saleable_unit_id' => $unitData['saleable_unit_id'] ?? null,
                            'wash_area' => $unitData['wash_area'] ?? null,
                            'wash_area_unit_id' => $unitData['wash_area_unit_id'] ?? null,
                            'balcony_area' => $unitData['balcony_area'] ?? null,
                            'balcony_area_unit_id' => $unitData['balcony_area_unit_id'] ?? null,
                            'ceiling_height' => $unitData['ceiling_height'] ?? null,
                            'ceiling_height_unit_id' => $unitData['ceiling_height_unit_id'] ?? null,
                            'servant_room' => isset($unitData['servant_room']) && $unitData['servant_room'] === '1',
                            'show_carpet_area' => isset($unitData['show_carpet_area']) && $unitData['show_carpet_area'] === '1',
                            'carpet_area_from' => $unitData['carpet_area_from'] ?? null,
                            'carpet_area_to' => $unitData['carpet_area_to'] ?? null,
                            'carpet_area_unit_id' => $unitData['carpet_area_unit_id'] ?? null,
                            'show_builtup_area' => isset($unitData['show_builtup_area']) && $unitData['show_builtup_area'] === '1',
                            'builtup_area_from' => $unitData['builtup_area_from'] ?? null,
                            'builtup_area_to' => $unitData['builtup_area_to'] ?? null,
                            'builtup_area_unit_id' => $unitData['builtup_area_unit_id'] ?? null,
                        ]);
                    }
                }
            }

            // Create Category 6 data (Construction Type 2, Category 6)
            if ($request->has('category6_data')) {
                $category6Data = $request->input('category6_data');
                ProjectCategory6Data::create([
                    'project_id' => $project->id,
                    'total_open_area' => $category6Data['total_open_area'] ?? null,
                    'total_open_area_unit_id' => $category6Data['total_open_area_unit_id'] ?? null,
                    'total_no_of_plots' => $category6Data['total_no_of_plots'] ?? null,
                    'project_with_multiple_theme_phase' => isset($category6Data['project_with_multiple_theme_phase']) && ($category6Data['project_with_multiple_theme_phase'] === '1' || $category6Data['project_with_multiple_theme_phase'] === true || $category6Data['project_with_multiple_theme_phase'] === 'true'),
                    'phase_name' => $category6Data['phase_name'] ?? null,
                    'plots_with_construction' => isset($category6Data['plots_with_construction']) && ($category6Data['plots_with_construction'] === '1' || $category6Data['plots_with_construction'] === true || $category6Data['plots_with_construction'] === 'true'),
                    'saleable_plot_from' => $category6Data['saleable_plot_from'] ?? null,
                    'saleable_plot_to' => $category6Data['saleable_plot_to'] ?? null,
                    'saleable_plot_unit_id' => $category6Data['saleable_plot_unit_id'] ?? null,
                    'show_carpet_plot_size' => isset($category6Data['show_carpet_plot_size']) && ($category6Data['show_carpet_plot_size'] === '1' || $category6Data['show_carpet_plot_size'] === true || $category6Data['show_carpet_plot_size'] === 'true'),
                    'carpet_plot_from' => $category6Data['carpet_plot_from'] ?? null,
                    'carpet_plot_to' => $category6Data['carpet_plot_to'] ?? null,
                    'carpet_plot_unit_id' => $category6Data['carpet_plot_unit_id'] ?? null,
                    'constructed_saleable_area_from' => $category6Data['constructed_saleable_area_from'] ?? null,
                    'constructed_saleable_area_to' => $category6Data['constructed_saleable_area_to'] ?? null,
                    'constructed_saleable_area_unit_id' => $category6Data['constructed_saleable_area_unit_id'] ?? null,
                ]);
            }

            // Create Office & Retail data
            if ($request->has('office_retail_data')) {
                $officeRetailData = $request->input('office_retail_data');
                ProjectOfficeRetailData::create([
                    'project_id' => $project->id,
                    'office_sub_category_id' => $officeRetailData['office_sub_category_id'] ?? null,
                    'no_of_towers' => $officeRetailData['no_of_towers'] ?? null,
                    'no_of_floors' => $officeRetailData['no_of_floors'] ?? null,
                    'no_of_unit_each_tower' => $officeRetailData['no_of_unit_each_tower'] ?? null,
                    'no_of_lift' => $officeRetailData['no_of_lift'] ?? null,
                    'front_road_width' => $officeRetailData['front_road_width'] ?? null,
                    'front_road_width_unit_id' => $officeRetailData['front_road_width_unit_id'] ?? null,
                    'washroom' => $officeRetailData['washroom'] ?? null,
                    'two_road_corner' => isset($officeRetailData['two_road_corner']) && ($officeRetailData['two_road_corner'] === '1' || $officeRetailData['two_road_corner'] === true || $officeRetailData['two_road_corner'] === 'true'),
                    'tower_name' => $officeRetailData['tower_name'] ?? null,
                    'total_units' => $officeRetailData['total_units'] ?? null,
                    'saleable_from' => $officeRetailData['saleable_from'] ?? null,
                    'saleable_to' => $officeRetailData['saleable_to'] ?? null,
                    'saleable_unit_id' => $officeRetailData['saleable_unit_id'] ?? null,
                    'show_carpet_area' => isset($officeRetailData['show_carpet_area']) && ($officeRetailData['show_carpet_area'] === '1' || $officeRetailData['show_carpet_area'] === true || $officeRetailData['show_carpet_area'] === 'true'),
                    'carpet_area_from' => $officeRetailData['carpet_area_from'] ?? null,
                    'carpet_area_to' => $officeRetailData['carpet_area_to'] ?? null,
                    'carpet_area_unit_id' => $officeRetailData['carpet_area_unit_id'] ?? null,
                    'show_builtup_area' => isset($officeRetailData['show_builtup_area']) && ($officeRetailData['show_builtup_area'] === '1' || $officeRetailData['show_builtup_area'] === true || $officeRetailData['show_builtup_area'] === 'true'),
                    'builtup_area_from' => $officeRetailData['builtup_area_from'] ?? null,
                    'builtup_area_to' => $officeRetailData['builtup_area_to'] ?? null,
                    'builtup_area_unit_id' => $officeRetailData['builtup_area_unit_id'] ?? null,
                ]);
            }

            // Create Office & Retail Retail Unit Details
            if ($request->has('office_retail_retail_unit_details') && is_array($request->office_retail_retail_unit_details)) {
                foreach ($request->office_retail_retail_unit_details as $unitData) {
                    // Filter out completely empty entries
                    if (!empty($unitData['tower_name']) || 
                        !empty($unitData['sub_category_id']) || 
                        !empty($unitData['size_from']) || 
                        !empty($unitData['front_opening']) || 
                        !empty($unitData['no_of_unit_each_floor']) || 
                        !empty($unitData['ceiling_height'])) {
                        ProjectOfficeRetailRetailUnitDetail::create([
                            'project_id' => $project->id,
                            'tower_name' => $unitData['tower_name'] ?? null,
                            'sub_category_id' => $unitData['sub_category_id'] ?? null,
                            'size_from' => $unitData['size_from'] ?? null,
                            'size_to' => $unitData['size_to'] ?? null,
                            'size_unit_id' => $unitData['size_unit_id'] ?? null,
                            'front_opening' => $unitData['front_opening'] ?? null,
                            'front_opening_unit_id' => $unitData['front_opening_unit_id'] ?? null,
                            'no_of_unit_each_floor' => $unitData['no_of_unit_each_floor'] ?? null,
                            'ceiling_height' => $unitData['ceiling_height'] ?? null,
                            'ceiling_height_unit_id' => $unitData['ceiling_height_unit_id'] ?? null,
                        ]);
                    }
                }
            }

            // Create basement parking
            if ($request->has('basement_parking') && is_array($request->basement_parking)) {
                foreach ($request->basement_parking as $parkingData) {
                    ProjectBasementParking::create([
                        'project_id' => $project->id,
                        'floor_no' => $parkingData['floor_no'] ?? null,
                        'ev_charging_point' => $parkingData['ev_charging_point'] ?? null,
                        'hydraulic_parking' => $parkingData['hydraulic_parking'] ?? null,
                        'height_of_basement' => $parkingData['height_of_basement'] ?? null,
                        'height_of_basement_unit_id' => $parkingData['height_of_basement_unit_id'] ?? null,
                    ]);
                }
            }

            // Sync amenities
            if ($request->has('amenity_ids') && is_array($request->amenity_ids)) {
                $project->amenities()->sync($request->amenity_ids);
            }

            // Handle document uploads
            if ($request->has('document_uploads') && is_array($request->document_uploads)) {
                foreach ($request->document_uploads as $docUpload) {
                    $category = $docUpload['category'] ?? null;
                    
                    // Handle new file uploads
                    if (isset($docUpload['files']) && is_array($docUpload['files'])) {
                        foreach ($docUpload['files'] as $file) {
                            if ($file && $file->isValid()) {
                                $filePath = $file->store('projects/documents', 'public');
                                
                                ProjectDocument::create([
                                    'project_id' => $project->id,
                                    'category' => $category,
                                    'file_path' => $filePath,
                                    'file_name' => $file->getClientOriginalName(),
                                    'file_type' => $file->getMimeType(),
                                    'file_size' => $file->getSize(),
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully',
                'project' => $project->load(['builder', 'state', 'city', 'locality', 'contacts', 'constructionType', 'category', 'subCategory']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create project: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Project $project): Response
    {
        $builders = Builder::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $states = State::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $measurementUnits = MeasurementUnit::where('is_active', true)
            ->get(['id', 'name']);

        // Static restricted user options
        $restrictedUserOptions = [
            ['id' => 'hospital', 'name' => 'Hospital', 'email' => ''],
            ['id' => 'bachelor', 'name' => 'Bachelor', 'email' => ''],
        ];

        $constructionTypes = ConstructionType::where('is_active', true)
            ->with(['categories' => function ($query) {
                $query->where('is_active', true)
                    ->with(['subCategories' => function ($subQuery) {
                        $subQuery->where('is_active', true);
                    }]);
            }])
            ->get(['id', 'name']);

        $amenities = Amenity::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'is_active']);

        // Load project with all relationships
        $project->load([
            'builder',
            'state',
            'city',
            'locality',
            'contacts',
            'constructionType',
            'category',
            'subCategory',
            'towerDetails',
            'category3UnitDetails',
            'category4TowerDetails',
            'category4UnitDetails',
            'category5TowerDetails',
            'category5UnitDetails',
            'category6Data',
            'officeRetailData',
            'officeRetailRetailUnitDetails',
            'basementParking',
            'amenities',
            'documents' => function ($query) {
                $query->orderBy('created_at', 'asc')->orderBy('id', 'asc');
            },
        ]);

        return Inertia::render('projects/CreateProject', [
            'project' => $project,
            'builders' => $builders,
            'states' => $states,
            'measurementUnits' => $measurementUnits,
            'restrictedUserOptions' => $restrictedUserOptions,
            'constructionTypes' => $constructionTypes,
            'amenities' => $amenities,
        ]);
    }

    public function update(ProjectUpdateRequest $request, Project $project)
    {
        try {
            DB::beginTransaction();

            // Prepare project data
            $projectData = $request->only([
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
                'construction_type_id',
                'category_id',
                'no_of_towers',
                'no_of_floors',
                'total_units',
                'no_of_unit_each_tower',
                'no_of_lift',
                'front_road_width',
                'front_road_width_measurement_unit_id',
                'washroom',
                'total_floor_for_parking',
                'remark',
            ]);

            // Handle sub_category_id - single value or array for Retail, Category 4, and Category 7
            if ($request->has('sub_category_ids') && is_array($request->sub_category_ids) && count($request->sub_category_ids) > 0) {
                // For Retail category (ID 2), Category 4 (ID 4), and Category 7 (ID 7), store multiple in sub_category_ids JSON column
                $projectData['sub_category_ids'] = json_encode($request->sub_category_ids);
                $projectData['sub_category_id'] = null; // Set single to null when using multiple
            } elseif ($request->has('sub_category_id') && !empty($request->sub_category_id)) {
                // For other categories, single value
                $projectData['sub_category_id'] = $request->sub_category_id;
                $projectData['sub_category_ids'] = null; // Set multiple to null when using single
            } else {
                // No sub-category selected
                $projectData['sub_category_id'] = null;
                $projectData['sub_category_ids'] = null;
            }

            // Handle checkbox fields - explicitly set to false if not present or '0'
            $projectData['towers_different_specification'] = $request->has('towers_different_specification') 
                && ($request->towers_different_specification === '1' || $request->towers_different_specification === true || $request->towers_different_specification === 1);
            
            $projectData['two_road_corner'] = $request->has('two_road_corner') 
                && ($request->two_road_corner === '1' || $request->two_road_corner === true || $request->two_road_corner === 1);
            
            // Handle retail_unit_details - store as JSON array
            if ($request->has('retail_unit_details')) {
                $retailUnitDetails = $request->input('retail_unit_details');
                if (is_array($retailUnitDetails) && count($retailUnitDetails) > 0) {
                    // Filter out empty entries
                    $filteredDetails = array_filter($retailUnitDetails, function($unit) {
                        return !empty($unit['tower_name']) || !empty($unit['sub_category_id']) || !empty($unit['size_from']);
                    });
                    $projectData['retail_unit_details'] = !empty($filteredDetails) ? json_encode(array_values($filteredDetails)) : null;
                } else {
                    $projectData['retail_unit_details'] = null;
                }
            } else {
                $projectData['retail_unit_details'] = null;
            }

            // Handle Category 3: Utility Board and Dynamic Facilities (Construction Type 1, Category 3)
            if ($request->has('utility_board')) {
                $utilityBoard = $request->input('utility_board');
                if (is_array($utilityBoard) && !empty($utilityBoard)) {
                    $projectData['category3_utility_board'] = json_encode($utilityBoard);
                } else {
                    $projectData['category3_utility_board'] = null;
                }
            } else {
                $projectData['category3_utility_board'] = null;
            }

            if ($request->has('dynamic_facilities')) {
                $dynamicFacilities = $request->input('dynamic_facilities');
                if (is_array($dynamicFacilities) && count($dynamicFacilities) > 0) {
                    // Filter out empty entries
                    $filteredFacilities = array_filter($dynamicFacilities, function($facility) {
                        return !empty($facility['label']) || !empty($facility['value']);
                    });
                    $projectData['category3_dynamic_facilities'] = !empty($filteredFacilities) ? json_encode(array_values($filteredFacilities)) : null;
                } else {
                    $projectData['category3_dynamic_facilities'] = null;
                }
            } else {
                $projectData['category3_dynamic_facilities'] = null;
            }
            
            $projectData['free_allotted_parking_four_wheeler'] = $request->has('free_allotted_parking_four_wheeler') 
                && ($request->free_allotted_parking_four_wheeler === '1' || $request->free_allotted_parking_four_wheeler === true || $request->free_allotted_parking_four_wheeler === 1);
            
            $projectData['free_allotted_parking_two_wheeler'] = $request->has('free_allotted_parking_two_wheeler') 
                && ($request->free_allotted_parking_two_wheeler === '1' || $request->free_allotted_parking_two_wheeler === true || $request->free_allotted_parking_two_wheeler === 1);
            
            $projectData['available_for_purchase'] = $request->has('available_for_purchase') 
                && ($request->available_for_purchase === '1' || $request->available_for_purchase === true || $request->available_for_purchase === 1);
            
            // Handle no_of_parking - set to null if available_for_purchase is false
            if ($projectData['available_for_purchase']) {
                $projectData['no_of_parking'] = $request->input('no_of_parking');
            } else {
                $projectData['no_of_parking'] = null;
            }

            // Category 4 Total Room (Construction Type 2, Category 4)
            if ($request->has('category4_total_room')) {
                $projectData['category4_total_room'] = $request->input('category4_total_room');
            } else {
                $projectData['category4_total_room'] = null;
            }

            // Category 5 Total Room (Construction Type 2, Category 5)
            if ($request->has('category5_total_room')) {
                $projectData['category5_total_room'] = $request->input('category5_total_room');
            } else {
                $projectData['category5_total_room'] = null;
            }

            // Handle brochure file upload
            if ($request->hasFile('brochure_file')) {
                // Delete old brochure if exists
                if ($project->brochure_file) {
                    Storage::disk('public')->delete($project->brochure_file);
                }
                $brochureFile = $request->file('brochure_file');
                $brochurePath = $brochureFile->store('projects/brochures', 'public');
                $projectData['brochure_file'] = $brochurePath;
            } else {
                // If no new file is uploaded but project had a brochure, check if it should be deleted
                // This happens when user deletes existing brochure without uploading a new one
                // We'll check if brochure_file is explicitly set to null in the request
                if ($request->has('delete_brochure') && $request->delete_brochure) {
                    // Delete old brochure if exists
                    if ($project->brochure_file) {
                        Storage::disk('public')->delete($project->brochure_file);
                    }
                    $projectData['brochure_file'] = null;
                }
                // Otherwise, keep the existing brochure (don't update the field)
            }

            // Update project
            $project->update($projectData);

            // Update contacts - delete old and create new
            $project->contacts()->delete();
            if ($request->has('contacts') && is_array($request->contacts)) {
                foreach ($request->contacts as $contactData) {
                    ProjectContact::create([
                        'project_id' => $project->id,
                        'name' => $contactData['name'],
                        'mobile' => $contactData['mobile'],
                        'email' => $contactData['email'] ?? null,
                        'designation' => $contactData['designation'] ?? null,
                    ]);
                }
            }

            // Update tower details - delete old and create new
            $project->towerDetails()->delete();
            if ($request->has('tower_details') && is_array($request->tower_details)) {
                foreach ($request->tower_details as $towerData) {
                    ProjectTowerDetail::create([
                        'project_id' => $project->id,
                        'tower_name' => $towerData['tower_name'] ?? null,
                        'saleable_area_from' => $towerData['saleable_area_from'] ?? null,
                        'saleable_area_to' => $towerData['saleable_area_to'] ?? null,
                        'saleable_area_unit_id' => $towerData['saleable_area_unit_id'] ?? null,
                        'ceiling_height' => $towerData['ceiling_height'] ?? null,
                        'ceiling_height_unit_id' => $towerData['ceiling_height_unit_id'] ?? null,
                        'show_carpet_area' => $towerData['show_carpet_area'] ?? false,
                        'carpet_area_from' => $towerData['carpet_area_from'] ?? null,
                        'carpet_area_to' => $towerData['carpet_area_to'] ?? null,
                        'carpet_area_unit_id' => $towerData['carpet_area_unit_id'] ?? null,
                        'show_builtup_area' => $towerData['show_builtup_area'] ?? false,
                        'builtup_area_from' => $towerData['builtup_area_from'] ?? null,
                        'builtup_area_to' => $towerData['builtup_area_to'] ?? null,
                        'builtup_area_unit_id' => $towerData['builtup_area_unit_id'] ?? null,
                    ]);
                }
            }

            // Update Category 3 unit details - delete old and create new
            $project->category3UnitDetails()->delete();
            if ($request->has('category3_unit_details') && is_array($request->category3_unit_details)) {
                foreach ($request->category3_unit_details as $unitData) {
                    // Filter out completely empty entries
                    if (!empty($unitData['total_no_of_units']) || 
                        !empty($unitData['ceiling_height']) || 
                        !empty($unitData['plot_area_from']) || 
                        !empty($unitData['road_width_from']) || 
                        !empty($unitData['constructed_area_from'])) {
                        ProjectCategory3UnitDetail::create([
                            'project_id' => $project->id,
                            'total_no_of_units' => $unitData['total_no_of_units'] ?? null,
                            'ceiling_height' => $unitData['ceiling_height'] ?? null,
                            'ceiling_height_unit_id' => $unitData['ceiling_height_unit_id'] ?? null,
                            'plot_area_from' => $unitData['plot_area_from'] ?? null,
                            'plot_area_to' => $unitData['plot_area_to'] ?? null,
                            'plot_area_unit_id' => $unitData['plot_area_unit_id'] ?? null,
                            'road_width_from' => $unitData['road_width_from'] ?? null,
                            'road_width_to' => $unitData['road_width_to'] ?? null,
                            'road_width_unit_id' => $unitData['road_width_unit_id'] ?? null,
                            'constructed_area_from' => $unitData['constructed_area_from'] ?? null,
                            'constructed_area_to' => $unitData['constructed_area_to'] ?? null,
                            'constructed_area_unit_id' => $unitData['constructed_area_unit_id'] ?? null,
                        ]);
                    }
                }
            }

            // Update Category 4 tower details - delete old and create new
            $project->category4TowerDetails()->delete();
            if ($request->has('category4_tower_details') && is_array($request->category4_tower_details)) {
                foreach ($request->category4_tower_details as $towerData) {
                    // Filter out completely empty entries
                    if (!empty($towerData['tower_name']) || 
                        !empty($towerData['total_units']) || 
                        !empty($towerData['total_floor'])) {
                        ProjectCategory4TowerDetail::create([
                            'project_id' => $project->id,
                            'tower_name' => $towerData['tower_name'] ?? null,
                            'total_units' => $towerData['total_units'] ?? null,
                            'total_floor' => $towerData['total_floor'] ?? null,
                            'sub_category_ids' => $towerData['sub_category_ids'] ?? [],
                        ]);
                    }
                }
            }

            // Update Category 4 unit details - delete old and create new
            $project->category4UnitDetails()->delete();
            if ($request->has('category4_unit_details') && is_array($request->category4_unit_details)) {
                foreach ($request->category4_unit_details as $unitData) {
                    // Filter out completely empty entries
                    if (!empty($unitData['tower_name']) || 
                        !empty($unitData['saleable_from']) || 
                        !empty($unitData['wash_area']) || 
                        !empty($unitData['balcony_area']) || 
                        !empty($unitData['ceiling_height'])) {
                        ProjectCategory4UnitDetail::create([
                            'project_id' => $project->id,
                            'tower_name' => $unitData['tower_name'] ?? null,
                            'saleable_from' => $unitData['saleable_from'] ?? null,
                            'saleable_to' => $unitData['saleable_to'] ?? null,
                            'saleable_unit_id' => $unitData['saleable_unit_id'] ?? null,
                            'wash_area' => $unitData['wash_area'] ?? null,
                            'wash_area_unit_id' => $unitData['wash_area_unit_id'] ?? null,
                            'balcony_area' => $unitData['balcony_area'] ?? null,
                            'balcony_area_unit_id' => $unitData['balcony_area_unit_id'] ?? null,
                            'ceiling_height' => $unitData['ceiling_height'] ?? null,
                            'ceiling_height_unit_id' => $unitData['ceiling_height_unit_id'] ?? null,
                            'servant_room' => isset($unitData['servant_room']) && $unitData['servant_room'] === '1',
                            'show_carpet_area' => isset($unitData['show_carpet_area']) && $unitData['show_carpet_area'] === '1',
                            'carpet_area_from' => $unitData['carpet_area_from'] ?? null,
                            'carpet_area_to' => $unitData['carpet_area_to'] ?? null,
                            'carpet_area_unit_id' => $unitData['carpet_area_unit_id'] ?? null,
                            'show_builtup_area' => isset($unitData['show_builtup_area']) && $unitData['show_builtup_area'] === '1',
                            'builtup_area_from' => $unitData['builtup_area_from'] ?? null,
                            'builtup_area_to' => $unitData['builtup_area_to'] ?? null,
                            'builtup_area_unit_id' => $unitData['builtup_area_unit_id'] ?? null,
                        ]);
                    }
                }
            }

            // Update Category 5 tower details - delete old and create new
            $project->category5TowerDetails()->delete();
            if ($request->has('category5_tower_details') && is_array($request->category5_tower_details)) {
                foreach ($request->category5_tower_details as $towerData) {
                    // Filter out completely empty entries
                    if (!empty($towerData['tower_name']) || 
                        !empty($towerData['total_units']) || 
                        !empty($towerData['total_floor'])) {
                        ProjectCategory5TowerDetail::create([
                            'project_id' => $project->id,
                            'tower_name' => $towerData['tower_name'] ?? null,
                            'total_units' => $towerData['total_units'] ?? null,
                            'total_floor' => $towerData['total_floor'] ?? null,
                            'sub_category_ids' => $towerData['sub_category_ids'] ?? [],
                        ]);
                    }
                }
            }

            // Update Category 5 unit details - delete old and create new
            $project->category5UnitDetails()->delete();
            if ($request->has('category5_unit_details') && is_array($request->category5_unit_details)) {
                foreach ($request->category5_unit_details as $unitData) {
                    // Filter out completely empty entries
                    if (!empty($unitData['tower_name']) || 
                        !empty($unitData['saleable_from']) || 
                        !empty($unitData['wash_area']) || 
                        !empty($unitData['balcony_area']) || 
                        !empty($unitData['ceiling_height'])) {
                        ProjectCategory5UnitDetail::create([
                            'project_id' => $project->id,
                            'tower_name' => $unitData['tower_name'] ?? null,
                            'saleable_from' => $unitData['saleable_from'] ?? null,
                            'saleable_to' => $unitData['saleable_to'] ?? null,
                            'saleable_unit_id' => $unitData['saleable_unit_id'] ?? null,
                            'wash_area' => $unitData['wash_area'] ?? null,
                            'wash_area_unit_id' => $unitData['wash_area_unit_id'] ?? null,
                            'balcony_area' => $unitData['balcony_area'] ?? null,
                            'balcony_area_unit_id' => $unitData['balcony_area_unit_id'] ?? null,
                            'ceiling_height' => $unitData['ceiling_height'] ?? null,
                            'ceiling_height_unit_id' => $unitData['ceiling_height_unit_id'] ?? null,
                            'servant_room' => isset($unitData['servant_room']) && $unitData['servant_room'] === '1',
                            'show_carpet_area' => isset($unitData['show_carpet_area']) && $unitData['show_carpet_area'] === '1',
                            'carpet_area_from' => $unitData['carpet_area_from'] ?? null,
                            'carpet_area_to' => $unitData['carpet_area_to'] ?? null,
                            'carpet_area_unit_id' => $unitData['carpet_area_unit_id'] ?? null,
                            'show_builtup_area' => isset($unitData['show_builtup_area']) && $unitData['show_builtup_area'] === '1',
                            'builtup_area_from' => $unitData['builtup_area_from'] ?? null,
                            'builtup_area_to' => $unitData['builtup_area_to'] ?? null,
                            'builtup_area_unit_id' => $unitData['builtup_area_unit_id'] ?? null,
                        ]);
                    }
                }
            }

            // Update Category 6 data (Construction Type 2, Category 6)
            $project->category6Data()->delete();
            if ($request->has('category6_data')) {
                $category6Data = $request->input('category6_data');
                ProjectCategory6Data::create([
                    'project_id' => $project->id,
                    'total_open_area' => $category6Data['total_open_area'] ?? null,
                    'total_open_area_unit_id' => $category6Data['total_open_area_unit_id'] ?? null,
                    'total_no_of_plots' => $category6Data['total_no_of_plots'] ?? null,
                    'project_with_multiple_theme_phase' => isset($category6Data['project_with_multiple_theme_phase']) && ($category6Data['project_with_multiple_theme_phase'] === '1' || $category6Data['project_with_multiple_theme_phase'] === true || $category6Data['project_with_multiple_theme_phase'] === 'true'),
                    'phase_name' => $category6Data['phase_name'] ?? null,
                    'plots_with_construction' => isset($category6Data['plots_with_construction']) && ($category6Data['plots_with_construction'] === '1' || $category6Data['plots_with_construction'] === true || $category6Data['plots_with_construction'] === 'true'),
                    'saleable_plot_from' => $category6Data['saleable_plot_from'] ?? null,
                    'saleable_plot_to' => $category6Data['saleable_plot_to'] ?? null,
                    'saleable_plot_unit_id' => $category6Data['saleable_plot_unit_id'] ?? null,
                    'show_carpet_plot_size' => isset($category6Data['show_carpet_plot_size']) && ($category6Data['show_carpet_plot_size'] === '1' || $category6Data['show_carpet_plot_size'] === true || $category6Data['show_carpet_plot_size'] === 'true'),
                    'carpet_plot_from' => $category6Data['carpet_plot_from'] ?? null,
                    'carpet_plot_to' => $category6Data['carpet_plot_to'] ?? null,
                    'carpet_plot_unit_id' => $category6Data['carpet_plot_unit_id'] ?? null,
                    'constructed_saleable_area_from' => $category6Data['constructed_saleable_area_from'] ?? null,
                    'constructed_saleable_area_to' => $category6Data['constructed_saleable_area_to'] ?? null,
                    'constructed_saleable_area_unit_id' => $category6Data['constructed_saleable_area_unit_id'] ?? null,
                ]);
            }

            // Update Office & Retail data
            $project->officeRetailData()->delete();
            if ($request->has('office_retail_data')) {
                $officeRetailData = $request->input('office_retail_data');
                ProjectOfficeRetailData::create([
                    'project_id' => $project->id,
                    'office_sub_category_id' => $officeRetailData['office_sub_category_id'] ?? null,
                    'no_of_towers' => $officeRetailData['no_of_towers'] ?? null,
                    'no_of_floors' => $officeRetailData['no_of_floors'] ?? null,
                    'no_of_unit_each_tower' => $officeRetailData['no_of_unit_each_tower'] ?? null,
                    'no_of_lift' => $officeRetailData['no_of_lift'] ?? null,
                    'front_road_width' => $officeRetailData['front_road_width'] ?? null,
                    'front_road_width_unit_id' => $officeRetailData['front_road_width_unit_id'] ?? null,
                    'washroom' => $officeRetailData['washroom'] ?? null,
                    'two_road_corner' => isset($officeRetailData['two_road_corner']) && ($officeRetailData['two_road_corner'] === '1' || $officeRetailData['two_road_corner'] === true || $officeRetailData['two_road_corner'] === 'true'),
                    'tower_name' => $officeRetailData['tower_name'] ?? null,
                    'total_units' => $officeRetailData['total_units'] ?? null,
                    'saleable_from' => $officeRetailData['saleable_from'] ?? null,
                    'saleable_to' => $officeRetailData['saleable_to'] ?? null,
                    'saleable_unit_id' => $officeRetailData['saleable_unit_id'] ?? null,
                    'show_carpet_area' => isset($officeRetailData['show_carpet_area']) && ($officeRetailData['show_carpet_area'] === '1' || $officeRetailData['show_carpet_area'] === true || $officeRetailData['show_carpet_area'] === 'true'),
                    'carpet_area_from' => $officeRetailData['carpet_area_from'] ?? null,
                    'carpet_area_to' => $officeRetailData['carpet_area_to'] ?? null,
                    'carpet_area_unit_id' => $officeRetailData['carpet_area_unit_id'] ?? null,
                    'show_builtup_area' => isset($officeRetailData['show_builtup_area']) && ($officeRetailData['show_builtup_area'] === '1' || $officeRetailData['show_builtup_area'] === true || $officeRetailData['show_builtup_area'] === 'true'),
                    'builtup_area_from' => $officeRetailData['builtup_area_from'] ?? null,
                    'builtup_area_to' => $officeRetailData['builtup_area_to'] ?? null,
                    'builtup_area_unit_id' => $officeRetailData['builtup_area_unit_id'] ?? null,
                ]);
            }

            // Update Office & Retail Retail Unit Details - delete old and create new
            $project->officeRetailRetailUnitDetails()->delete();
            if ($request->has('office_retail_retail_unit_details') && is_array($request->office_retail_retail_unit_details)) {
                foreach ($request->office_retail_retail_unit_details as $unitData) {
                    // Filter out completely empty entries
                    if (!empty($unitData['tower_name']) || 
                        !empty($unitData['sub_category_id']) || 
                        !empty($unitData['size_from']) || 
                        !empty($unitData['front_opening']) || 
                        !empty($unitData['no_of_unit_each_floor']) || 
                        !empty($unitData['ceiling_height'])) {
                        ProjectOfficeRetailRetailUnitDetail::create([
                            'project_id' => $project->id,
                            'tower_name' => $unitData['tower_name'] ?? null,
                            'sub_category_id' => $unitData['sub_category_id'] ?? null,
                            'size_from' => $unitData['size_from'] ?? null,
                            'size_to' => $unitData['size_to'] ?? null,
                            'size_unit_id' => $unitData['size_unit_id'] ?? null,
                            'front_opening' => $unitData['front_opening'] ?? null,
                            'front_opening_unit_id' => $unitData['front_opening_unit_id'] ?? null,
                            'no_of_unit_each_floor' => $unitData['no_of_unit_each_floor'] ?? null,
                            'ceiling_height' => $unitData['ceiling_height'] ?? null,
                            'ceiling_height_unit_id' => $unitData['ceiling_height_unit_id'] ?? null,
                        ]);
                    }
                }
            }

            // Update basement parking - delete old and create new
            $project->basementParking()->delete();
            if ($request->has('basement_parking') && is_array($request->basement_parking)) {
                foreach ($request->basement_parking as $parkingData) {
                    ProjectBasementParking::create([
                        'project_id' => $project->id,
                        'floor_no' => $parkingData['floor_no'] ?? null,
                        'ev_charging_point' => $parkingData['ev_charging_point'] ?? null,
                        'hydraulic_parking' => $parkingData['hydraulic_parking'] ?? null,
                        'height_of_basement' => $parkingData['height_of_basement'] ?? null,
                        'height_of_basement_unit_id' => $parkingData['height_of_basement_unit_id'] ?? null,
                    ]);
                }
            }

            // Sync amenities
            if ($request->has('amenity_ids') && is_array($request->amenity_ids)) {
                $project->amenities()->sync($request->amenity_ids);
            } else {
                $project->amenities()->sync([]);
            }

            // Handle new document uploads (keep existing ones)
            if ($request->has('document_uploads') && is_array($request->document_uploads)) {
                foreach ($request->document_uploads as $docUpload) {
                    $category = $docUpload['category'] ?? null;
                    
                    // Handle new file uploads
                    if (isset($docUpload['files']) && is_array($docUpload['files'])) {
                        foreach ($docUpload['files'] as $file) {
                            if ($file && $file->isValid()) {
                                $filePath = $file->store('projects/documents', 'public');
                                
                                ProjectDocument::create([
                                    'project_id' => $project->id,
                                    'category' => $category,
                                    'file_path' => $filePath,
                                    'file_name' => $file->getClientOriginalName(),
                                    'file_type' => $file->getMimeType(),
                                    'file_size' => $file->getSize(),
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully',
                'project' => $project->load(['builder', 'state', 'city', 'locality', 'contacts', 'constructionType', 'category', 'subCategory']),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update project: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteDocument(ProjectDocument $document)
    {
        try {
            // Get the file path before deleting
            $filePath = $document->file_path;
            
            // Delete from database
            $document->delete();
            
            // Delete from storage
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully',
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document: ' . $e->getMessage(),
            ], 500);
        }
    }
}

