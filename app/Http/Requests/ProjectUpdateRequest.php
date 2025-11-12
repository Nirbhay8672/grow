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
            'sub_category_ids' => ['nullable', 'array'],
            'sub_category_ids.*' => ['nullable', 'exists:sub_categories,id'],
            
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
            
            // Office & Retail Data (Construction Type 3)
            'office_retail_data' => ['nullable', 'array'],
            'office_retail_data.office_sub_category_id' => ['nullable', 'exists:sub_categories,id'],
            'office_retail_data.no_of_towers' => ['nullable', 'string', 'max:10'],
            'office_retail_data.no_of_floors' => ['nullable', 'string', 'max:10'],
            'office_retail_data.no_of_unit_each_tower' => ['nullable', 'string', 'max:10'],
            'office_retail_data.no_of_lift' => ['nullable', 'string', 'max:10'],
            'office_retail_data.front_road_width' => ['nullable', 'string', 'max:50'],
            'office_retail_data.front_road_width_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'office_retail_data.washroom' => ['nullable', 'string', 'max:50'],
            'office_retail_data.two_road_corner' => ['nullable', 'boolean'],
            'office_retail_data.tower_name' => ['nullable', 'string', 'max:255'],
            'office_retail_data.total_units' => ['nullable', 'string', 'max:10'],
            'office_retail_data.saleable_from' => ['nullable', 'string', 'max:50'],
            'office_retail_data.saleable_to' => ['nullable', 'string', 'max:50'],
            'office_retail_data.saleable_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'office_retail_data.show_carpet_area' => ['nullable', 'boolean'],
            'office_retail_data.carpet_area_from' => ['nullable', 'string', 'max:50'],
            'office_retail_data.carpet_area_to' => ['nullable', 'string', 'max:50'],
            'office_retail_data.carpet_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'office_retail_data.show_builtup_area' => ['nullable', 'boolean'],
            'office_retail_data.builtup_area_from' => ['nullable', 'string', 'max:50'],
            'office_retail_data.builtup_area_to' => ['nullable', 'string', 'max:50'],
            'office_retail_data.builtup_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            
            // Office & Retail Retail Unit Details
            'office_retail_retail_unit_details' => ['nullable', 'array'],
            'office_retail_retail_unit_details.*.tower_name' => ['nullable', 'string', 'max:255'],
            'office_retail_retail_unit_details.*.sub_category_id' => ['nullable', 'exists:sub_categories,id'],
            'office_retail_retail_unit_details.*.size_from' => ['nullable', 'string', 'max:50'],
            'office_retail_retail_unit_details.*.size_to' => ['nullable', 'string', 'max:50'],
            'office_retail_retail_unit_details.*.size_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'office_retail_retail_unit_details.*.front_opening' => ['nullable', 'string', 'max:50'],
            'office_retail_retail_unit_details.*.front_opening_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'office_retail_retail_unit_details.*.no_of_unit_each_floor' => ['nullable', 'string', 'max:10'],
            'office_retail_retail_unit_details.*.ceiling_height' => ['nullable', 'string', 'max:50'],
            'office_retail_retail_unit_details.*.ceiling_height_unit_id' => ['nullable', 'exists:measurement_units,id'],
            
            // Category 4 Tower Details and Unit Details
            'category4_tower_details' => ['nullable', 'array'],
            'category4_tower_details.*.tower_name' => ['nullable', 'string', 'max:255'],
            'category4_tower_details.*.total_units' => ['nullable', 'string', 'max:10'],
            'category4_tower_details.*.total_floor' => ['nullable', 'string', 'max:10'],
            'category4_tower_details.*.sub_category_ids' => ['nullable', 'array'],
            'category4_tower_details.*.sub_category_ids.*' => ['nullable', 'exists:sub_categories,id'],
            'category4_unit_details' => ['nullable', 'array'],
            'category4_unit_details.*.tower_name' => ['nullable', 'string', 'max:255'],
            'category4_unit_details.*.saleable_from' => ['nullable', 'string', 'max:50'],
            'category4_unit_details.*.saleable_to' => ['nullable', 'string', 'max:50'],
            'category4_unit_details.*.saleable_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category4_unit_details.*.wash_area' => ['nullable', 'string', 'max:50'],
            'category4_unit_details.*.wash_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category4_unit_details.*.balcony_area' => ['nullable', 'string', 'max:50'],
            'category4_unit_details.*.balcony_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category4_unit_details.*.ceiling_height' => ['nullable', 'string', 'max:50'],
            'category4_unit_details.*.ceiling_height_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category4_unit_details.*.servant_room' => ['nullable', 'boolean'],
            'category4_unit_details.*.show_carpet_area' => ['nullable', 'boolean'],
            'category4_unit_details.*.carpet_area_from' => ['nullable', 'string', 'max:50'],
            'category4_unit_details.*.carpet_area_to' => ['nullable', 'string', 'max:50'],
            'category4_unit_details.*.carpet_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category4_unit_details.*.show_builtup_area' => ['nullable', 'boolean'],
            'category4_unit_details.*.builtup_area_from' => ['nullable', 'string', 'max:50'],
            'category4_unit_details.*.builtup_area_to' => ['nullable', 'string', 'max:50'],
            'category4_unit_details.*.builtup_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category4_total_room' => ['nullable', 'string', 'max:10'],
            
            // Category 5 Tower Details and Unit Details
            'category5_tower_details' => ['nullable', 'array'],
            'category5_tower_details.*.tower_name' => ['nullable', 'string', 'max:255'],
            'category5_tower_details.*.total_units' => ['nullable', 'string', 'max:10'],
            'category5_tower_details.*.total_floor' => ['nullable', 'string', 'max:10'],
            'category5_tower_details.*.sub_category_ids' => ['nullable', 'array'],
            'category5_tower_details.*.sub_category_ids.*' => ['nullable', 'exists:sub_categories,id'],
            'category5_unit_details' => ['nullable', 'array'],
            'category5_unit_details.*.tower_name' => ['nullable', 'string', 'max:255'],
            'category5_unit_details.*.saleable_from' => ['nullable', 'string', 'max:50'],
            'category5_unit_details.*.saleable_to' => ['nullable', 'string', 'max:50'],
            'category5_unit_details.*.saleable_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category5_unit_details.*.wash_area' => ['nullable', 'string', 'max:50'],
            'category5_unit_details.*.wash_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category5_unit_details.*.balcony_area' => ['nullable', 'string', 'max:50'],
            'category5_unit_details.*.balcony_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category5_unit_details.*.ceiling_height' => ['nullable', 'string', 'max:50'],
            'category5_unit_details.*.ceiling_height_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category5_unit_details.*.servant_room' => ['nullable', 'boolean'],
            'category5_unit_details.*.show_carpet_area' => ['nullable', 'boolean'],
            'category5_unit_details.*.carpet_area_from' => ['nullable', 'string', 'max:50'],
            'category5_unit_details.*.carpet_area_to' => ['nullable', 'string', 'max:50'],
            'category5_unit_details.*.carpet_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category5_unit_details.*.show_builtup_area' => ['nullable', 'boolean'],
            'category5_unit_details.*.builtup_area_from' => ['nullable', 'string', 'max:50'],
            'category5_unit_details.*.builtup_area_to' => ['nullable', 'string', 'max:50'],
            'category5_unit_details.*.builtup_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category5_total_room' => ['nullable', 'string', 'max:10'],
            
            // Category 6 Data
            'category6_data' => ['nullable', 'array'],
            'category6_data.total_open_area' => ['nullable', 'string', 'max:50'],
            'category6_data.total_open_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category6_data.total_no_of_plots' => ['nullable', 'string', 'max:10'],
            'category6_data.project_with_multiple_theme_phase' => ['nullable', 'boolean'],
            'category6_data.phase_name' => ['nullable', 'string', 'max:255'],
            'category6_data.plots_with_construction' => ['nullable', 'boolean'],
            'category6_data.saleable_plot_from' => ['nullable', 'string', 'max:50'],
            'category6_data.saleable_plot_to' => ['nullable', 'string', 'max:50'],
            'category6_data.saleable_plot_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category6_data.show_carpet_plot_size' => ['nullable', 'boolean'],
            'category6_data.carpet_plot_from' => ['nullable', 'string', 'max:50'],
            'category6_data.carpet_plot_to' => ['nullable', 'string', 'max:50'],
            'category6_data.carpet_plot_unit_id' => ['nullable', 'exists:measurement_units,id'],
            'category6_data.constructed_saleable_area_from' => ['nullable', 'string', 'max:50'],
            'category6_data.constructed_saleable_area_to' => ['nullable', 'string', 'max:50'],
            'category6_data.constructed_saleable_area_unit_id' => ['nullable', 'exists:measurement_units,id'],
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



