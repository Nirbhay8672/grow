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
                'sub_category_id',
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

            // Handle checkbox fields - explicitly set to false if not present or '0'
            $projectData['towers_different_specification'] = $request->has('towers_different_specification') 
                && ($request->towers_different_specification === '1' || $request->towers_different_specification === true || $request->towers_different_specification === 1);
            
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
                'sub_category_id',
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

            // Handle checkbox fields - explicitly set to false if not present or '0'
            $projectData['towers_different_specification'] = $request->has('towers_different_specification') 
                && ($request->towers_different_specification === '1' || $request->towers_different_specification === true || $request->towers_different_specification === 1);
            
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

