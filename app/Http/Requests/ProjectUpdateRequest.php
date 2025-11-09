<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $project = $this->route('project');
        
        return [
            // Step 1: Builder Information
            'builder_id' => ['required', 'exists:builders,id'],
            'builder_website' => ['nullable', 'url', 'max:255'],
            
            // Step 1: Project Information
            'project_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'locality_id' => ['required', 'exists:localities,id'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'location_link' => ['nullable', 'url', 'max:255'],
            'land_size' => ['nullable', 'string', 'max:50'],
            'measurement_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'rera_no' => ['nullable', 'string', 'max:100'],
            'project_status' => ['nullable', 'string', 'max:50'],
            'restricted_user_ids' => ['nullable', 'array'],
            'restricted_user_ids.*' => ['nullable', 'string', 'in:hospital,bachelor'],
            
            // Step 1: Contacts
            'contacts' => ['required', 'array', 'min:1'],
            'contacts.*.name' => ['required', 'string', 'max:255'],
            'contacts.*.mobile' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'contacts.*.email' => ['nullable', 'email', 'max:255'],
            'contacts.*.designation' => ['nullable', 'string', 'max:255'],
            
            // Step 2: Construction Type & Category
            'construction_type_id' => ['required', 'exists:construction_types,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'exists:sub_categories,id'],
            
            // Step 2: Tower Details
            'no_of_towers' => ['nullable', 'string', 'max:10'],
            'no_of_floors' => ['nullable', 'string', 'max:10'],
            'total_units' => ['nullable', 'string', 'max:10'],
            'no_of_unit_each_tower' => ['nullable', 'string', 'max:10'],
            'no_of_lift' => ['nullable', 'string', 'max:10'],
            'front_road_width' => ['nullable', 'string', 'max:50'],
            'front_road_width_measurement_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'washroom' => ['nullable', 'string', 'max:50'],
            'towers_different_specification' => ['nullable', 'boolean'],
            
            // Step 2: Tower Details Array
            'tower_details' => ['nullable', 'array'],
            'tower_details.*.tower_name' => ['nullable', 'string', 'max:255'],
            'tower_details.*.saleable_area_from' => ['nullable', 'string', 'max:50'],
            'tower_details.*.saleable_area_to' => ['nullable', 'string', 'max:50'],
            'tower_details.*.saleable_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'tower_details.*.ceiling_height' => ['nullable', 'string', 'max:50'],
            'tower_details.*.ceiling_height_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'tower_details.*.show_carpet_area' => ['nullable', 'boolean'],
            'tower_details.*.carpet_area_from' => ['nullable', 'string', 'max:50'],
            'tower_details.*.carpet_area_to' => ['nullable', 'string', 'max:50'],
            'tower_details.*.carpet_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'tower_details.*.show_builtup_area' => ['nullable', 'boolean'],
            'tower_details.*.builtup_area_from' => ['nullable', 'string', 'max:50'],
            'tower_details.*.builtup_area_to' => ['nullable', 'string', 'max:50'],
            'tower_details.*.builtup_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            
            // Step 3: Parking Details
            'free_allotted_parking_four_wheeler' => ['nullable', 'boolean'],
            'free_allotted_parking_two_wheeler' => ['nullable', 'boolean'],
            'available_for_purchase' => ['nullable', 'boolean'],
            'no_of_parking' => ['nullable', 'string', 'max:10'],
            'total_floor_for_parking' => ['nullable', 'string', 'max:10'],
            
            // Step 3: Basement Parking Array
            'basement_parking' => ['nullable', 'array'],
            'basement_parking.*.floor_no' => ['nullable', 'string', 'max:50'],
            'basement_parking.*.ev_charging_point' => ['nullable', 'string', 'max:50'],
            'basement_parking.*.hydraulic_parking' => ['nullable', 'string', 'max:50'],
            'basement_parking.*.height_of_basement' => ['nullable', 'string', 'max:50'],
            'basement_parking.*.height_of_basement_unit_id' => ['nullable', 'exists:measurement_units,id'],
            
            // Step 3: Amenities
            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['exists:amenities,id'],
            
            // Step 3: Documents
            'document_uploads' => ['nullable', 'array'],
            'document_uploads.*.category' => ['nullable', 'string', 'max:100'],
            'document_uploads.*.files' => ['nullable', 'array'],
            'document_uploads.*.files.*' => ['file', 'max:10240'], // 10MB max per file
            
            // Step 3: Brochure
            'brochure_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048'], // 2MB max
            
            // Step 3: Other
            'remark' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'builder_id.required' => 'Please select a builder.',
            'locality_id.required' => 'Please select a locality.',
            'construction_type_id.required' => 'Please select a construction type.',
            'category_id.required' => 'Please select a category.',
            'contacts.required' => 'At least one contact is required.',
            'contacts.min' => 'At least one contact is required.',
            'contacts.*.name.required' => 'Contact name is required.',
            'contacts.*.mobile.required' => 'Contact mobile is required.',
            'contacts.*.mobile.regex' => 'Mobile must be 10 digits.',
        ];
    }
}


