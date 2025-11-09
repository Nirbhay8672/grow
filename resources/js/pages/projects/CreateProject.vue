<script setup lang="ts">
import { ref, reactive, watch, onMounted, nextTick, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import StepIndicator from './components/StepIndicator.vue';
import BuilderInformation from './components/BuilderInformation.vue';
import ProjectInformation from './components/ProjectInformation.vue';
import ContactDetails from './components/ContactDetails.vue';
import ChoiseSelection from './components/ChoiseSelection.vue';
import TowerDetailsSection from './components/TowerDetailsSection.vue';
import ParkingAndAmenities from './components/ParkingAndAmenities.vue';

declare global {
    interface Window {
        $: any;
    }
}

interface Builder {
    id: number;
    name: string;
}

interface State {
    id: number;
    name: string;
}

interface City {
    id: number;
    name: string;
}

interface Locality {
    id: number;
    name: string;
    zip_code?: string;
}

interface MeasurementUnit {
    id: number;
    name: string;
}

interface ConstructionType {
    id: number;
    name: string;
    categories?: Category[];
}

interface Category {
    id: number;
    name: string;
    sub_categories?: SubCategory[];
}

interface SubCategory {
    id: number;
    name: string;
}

interface Amenity {
    id: number;
    name: string;
    is_active: boolean;
}

interface RestrictedUserOption {
    id: string;
    name: string;
    email: string;
}

interface Project {
    id: number;
    builder_id: string;
    builder_website?: string;
    project_name: string;
    address?: string;
    state_id?: string;
    city_id?: string;
    locality_id: string;
    pincode?: string;
    location_link?: string;
    land_size?: string;
    measurement_unit_id?: string;
    rera_no?: string;
    project_status?: string;
    restricted_user_ids?: (string | number)[];
    construction_type_id?: string;
    category_id?: string;
    sub_category_id?: string;
    no_of_towers?: string;
    no_of_floors?: string;
    total_units?: string;
    no_of_unit_each_tower?: string;
    no_of_lift?: string;
    front_road_width?: string;
    front_road_width_measurement_unit_id?: string;
    washroom?: string;
    towers_different_specification?: boolean;
    free_allotted_parking_four_wheeler?: boolean;
    free_allotted_parking_two_wheeler?: boolean;
    available_for_purchase?: boolean;
    no_of_parking?: string;
    total_floor_for_parking?: string;
    remark?: string;
    brochure_file?: string;
    existing_brochure_file?: string;
    contacts?: Array<{
        id: number;
        name: string;
        mobile: string;
        email?: string;
        designation?: string;
    }>;
    tower_details?: Array<any>;
    basement_parking?: Array<any>;
    amenities?: Array<{ id: number }>;
    documents?: Array<any>;
}

interface Props {
    project?: Project;
    builders: Builder[];
    states: State[];
    measurementUnits: MeasurementUnit[];
    restrictedUserOptions: RestrictedUserOption[];
    constructionTypes: ConstructionType[];
    amenities: Amenity[];
}

const props = defineProps<Props>();

const isEditMode = computed(() => !!props.project);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: isEditMode.value ? 'Edit Project' : 'Create Project',
        href: isEditMode.value ? `/projects/${props.project?.id}/edit` : '/projects/create',
    },
]);

const currentStep = ref(1);
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});

const cities = ref<City[]>([]);
const localities = ref<Locality[]>([]);
const isInitializingForm = ref(false);
const isPageLoading = ref(false);

const toast = ref<{ show: boolean; message: string; type: 'success' | 'error' }>({
    show: false,
    message: '',
    type: 'success'
});

interface Contact {
    id: number;
    name: string;
    mobile: string;
    email: string;
    designation: string;
}

const contacts = ref<Contact[]>([
    {
        id: 1,
        name: '',
        mobile: '',
        email: '',
        designation: '',
    },
]);

let contactIdCounter = 2;

const form = reactive({
    // Builder Information
    builder_id: '',
    builder_website: '',
    
    // Project Information
    project_name: '',
    address: '',
    state_id: '',
    city_id: '',
    locality_id: '',
    pincode: '',
    location_link: '',
    land_size: '',
    measurement_unit_id: '',
    rera_no: '',
    project_status: '',
    
    // Contact Details
    restricted_user_ids: [] as (string | number)[],
    
    // Construction Type
    construction_type_id: '',
    category_id: '',
    sub_category_id: '',
    
    // Tower Details
    no_of_towers: '',
    no_of_floors: '',
    total_units: '',
    no_of_unit_each_tower: '',
    no_of_lift: '',
    front_road_width: '',
    front_road_width_measurement_unit_id: '',
    
    // Washroom
    washroom: 'Private',
    
    // Tower Specifications
    towers_different_specification: false,
    
    // Tower Details Array (multiple towers) - Always start with one entry
    tower_details: [{
        tower_name: '',
        saleable_area_from: '',
        saleable_area_to: '',
        saleable_area_unit_id: '',
        ceiling_height: '',
        ceiling_height_unit_id: '',
        carpet_area_from: '',
        carpet_area_to: '',
        carpet_area_unit_id: '',
        builtup_area_from: '',
        builtup_area_to: '',
        builtup_area_unit_id: '',
        show_carpet_area: false,
        show_builtup_area: false,
    }] as Array<{
        tower_name: string;
        saleable_area_from: string;
        saleable_area_to: string;
        saleable_area_unit_id: string;
        ceiling_height: string;
        ceiling_height_unit_id: string;
        carpet_area_from: string;
        carpet_area_to: string;
        carpet_area_unit_id: string;
        builtup_area_from: string;
        builtup_area_to: string;
        builtup_area_unit_id: string;
        show_carpet_area: boolean;
        show_builtup_area: boolean;
    }>,
    
    // Parking & Amenities
    free_allotted_parking_four_wheeler: false,
    free_allotted_parking_two_wheeler: false,
    available_for_purchase: false,
    no_of_parking: '',
    total_floor_for_parking: '1',
    basement_parking: [{
        floor_no: '',
        ev_charging_point: '',
        hydraulic_parking: '',
        height_of_basement: '',
        height_of_basement_unit_id: '',
    }] as Array<{
        floor_no: string;
        ev_charging_point: string;
        hydraulic_parking: string;
        height_of_basement: string;
        height_of_basement_unit_id: string;
    }>,
    amenity_ids: [] as string[],
    document_uploads: [] as Array<{
        id?: number;
        category: string;
        files: File[];
        uploaded_files?: Array<{
            id?: number;
            name: string;
            url?: string;
        }>;
        existing_file_path?: string;
        existing_file_name?: string;
    }>,
    brochure_file: null as File | null,
    existing_brochure_file: null as string | null,
    brochure_deleted: false,
    remark: '',
});

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
    toast.value = {
        show: true,
        message,
        type
    };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const addContact = () => {
    contacts.value.push({
        id: contactIdCounter++,
        name: '',
        mobile: '',
        email: '',
        designation: '',
    });
};

const removeContact = (contactId: number) => {
    if (contacts.value.length > 1) {
        contacts.value = contacts.value.filter(contact => contact.id !== contactId);
    }
};

// Initialize tower details based on number of towers
watch(() => form.no_of_towers, (newValue) => {
    // Only update if checkbox is checked
    if (!form.towers_different_specification) {
        return;
    }
    
    const numTowers = parseInt(newValue) || 1; // Default to 1 if empty
    const currentLength = form.tower_details.length;
    
    if (numTowers > currentLength) {
        // Add new tower details
        for (let i = currentLength; i < numTowers; i++) {
            form.tower_details.push({
                tower_name: '',
                saleable_area_from: '',
                saleable_area_to: '',
                saleable_area_unit_id: '',
                ceiling_height: '',
                ceiling_height_unit_id: '',
                carpet_area_from: '',
                carpet_area_to: '',
                carpet_area_unit_id: '',
                builtup_area_from: '',
                builtup_area_to: '',
                builtup_area_unit_id: '',
                show_carpet_area: false,
                show_builtup_area: false,
            });
        }
    } else if (numTowers < currentLength && numTowers > 0) {
        // Remove excess tower details, but keep at least one
        form.tower_details = form.tower_details.slice(0, numTowers);
    }
    // If numTowers is 0 or empty, keep at least one entry
    if (numTowers === 0 || !newValue) {
        if (form.tower_details.length === 0) {
            form.tower_details.push({
                tower_name: '',
                saleable_area_from: '',
                saleable_area_to: '',
                saleable_area_unit_id: '',
                ceiling_height: '',
                ceiling_height_unit_id: '',
                carpet_area_from: '',
                carpet_area_to: '',
                carpet_area_unit_id: '',
                builtup_area_from: '',
                builtup_area_to: '',
                builtup_area_unit_id: '',
                show_carpet_area: false,
                show_builtup_area: false,
            });
        }
    }
}, { immediate: false });

// Watch for towers_different_specification checkbox
watch(() => form.towers_different_specification, (isChecked) => {
    if (!isChecked) {
        // If unchecked, ensure only one tower detail exists
        if (form.tower_details.length > 1) {
            form.tower_details = [form.tower_details[0]];
        }
    } else {
        // If checked, sync with number of towers
        const numTowers = parseInt(form.no_of_towers) || 1;
        const currentLength = form.tower_details.length;
        
        if (numTowers > currentLength) {
            // Add new tower details
            for (let i = currentLength; i < numTowers; i++) {
                form.tower_details.push({
                    tower_name: '',
                    saleable_area_from: '',
                    saleable_area_to: '',
                    saleable_area_unit_id: '',
                    ceiling_height: '',
                    ceiling_height_unit_id: '',
                    carpet_area_from: '',
                    carpet_area_to: '',
                    carpet_area_unit_id: '',
                    builtup_area_from: '',
                    builtup_area_to: '',
                    builtup_area_unit_id: '',
                    show_carpet_area: false,
                    show_builtup_area: false,
                });
            }
        } else if (numTowers < currentLength && numTowers > 0) {
            // Remove excess tower details, but keep at least one
            form.tower_details = form.tower_details.slice(0, numTowers);
        }
        
        // Ensure at least one entry
        if (form.tower_details.length === 0) {
            form.tower_details.push({
                tower_name: '',
                saleable_area_from: '',
                saleable_area_to: '',
                saleable_area_unit_id: '',
                ceiling_height: '',
                ceiling_height_unit_id: '',
                carpet_area_from: '',
                carpet_area_to: '',
                carpet_area_unit_id: '',
                builtup_area_from: '',
                builtup_area_to: '',
                builtup_area_unit_id: '',
                show_carpet_area: false,
                show_builtup_area: false,
            });
        }
    }
}, { immediate: false });


// Handler to ensure only integers are entered
const handleIntegerInput = (event: Event, field: string) => {
    const target = event.target as HTMLInputElement;
    const value = target.value;
    // Remove any non-digit characters
    const integerValue = value.replace(/[^0-9]/g, '');
    if (value !== integerValue) {
        target.value = integerValue;
        (form as any)[field] = integerValue;
    }
};

// Watch state changes to load cities
watch(() => form.state_id, async (newStateId) => {
    // Don't clear values during form initialization
    if (isInitializingForm.value) {
        return;
    }
    
    if (newStateId) {
        try {
            const response = await axios.get(`/states/${newStateId}/cities`);
            cities.value = response.data;
            form.city_id = '';
            form.locality_id = '';
            form.pincode = '';
            localities.value = [];
            
        } catch (error) {
            console.error('Error loading cities:', error);
            cities.value = [];
        }
    } else {
        cities.value = [];
        form.city_id = '';
        form.locality_id = '';
        form.pincode = '';
        localities.value = [];
        
    }
});

// Watch city changes to load localities
watch(() => form.city_id, async (newCityId) => {
    // Don't clear values during form initialization
    if (isInitializingForm.value) {
        return;
    }
    
    if (newCityId) {
        try {
            const response = await axios.get(`/cities/${newCityId}/localities`);
            localities.value = response.data;
            form.locality_id = '';
            form.pincode = '';
            
        } catch (error) {
            console.error('Error loading localities:', error);
            localities.value = [];
        }
    } else {
        localities.value = [];
        form.locality_id = '';
        form.pincode = '';
        
    }
});

// Watch locality changes to auto-set zip code
watch(() => form.locality_id, (newLocalityId) => {
    if (newLocalityId) {
        const selectedLocality = localities.value.find(loc => loc.id === Number(newLocalityId));
        if (selectedLocality && selectedLocality.zip_code) {
            form.pincode = selectedLocality.zip_code;
        }
    } else {
        form.pincode = '';
    }
});


const validateForm = (): boolean => {
    errors.value = {};
    let isValid = true;

    // Validate Builder Name (required)
    if (!form.builder_id || form.builder_id === '') {
        errors.value.builder_id = ['Builder Name is required'];
        isValid = false;
    }

    // Validate Project Name (required)
    if (!form.project_name || form.project_name.trim() === '') {
        errors.value.project_name = ['Project Name is required'];
        isValid = false;
    }

    // Validate Locality (required)
    if (!form.locality_id || form.locality_id === '') {
        errors.value.locality_id = ['Locality is required'];
        isValid = false;
    }

    // Validate Contacts
    contacts.value.forEach((contact, index) => {
        // Validate Contact Name (required)
        if (!contact.name || contact.name.trim() === '') {
            if (!errors.value[`contacts.${index}.name`]) {
                errors.value[`contacts.${index}.name`] = [];
            }
            errors.value[`contacts.${index}.name`].push('Name is required');
            isValid = false;
        }

        // Validate Contact Mobile (required)
        if (!contact.mobile || contact.mobile.trim() === '') {
            if (!errors.value[`contacts.${index}.mobile`]) {
                errors.value[`contacts.${index}.mobile`] = [];
            }
            errors.value[`contacts.${index}.mobile`].push('Mobile is required');
            isValid = false;
        }

        // Validate Mobile format (if provided)
        if (contact.mobile && contact.mobile.trim() !== '') {
            const mobileRegex = /^[0-9]{10}$/;
            if (!mobileRegex.test(contact.mobile.trim())) {
                if (!errors.value[`contacts.${index}.mobile`]) {
                    errors.value[`contacts.${index}.mobile`] = [];
                }
                errors.value[`contacts.${index}.mobile`].push('Mobile must be 10 digits');
                isValid = false;
            }
        }

        // Validate Email format (if provided)
        if (contact.email && contact.email.trim() !== '') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(contact.email.trim())) {
                if (!errors.value[`contacts.${index}.email`]) {
                    errors.value[`contacts.${index}.email`] = [];
                }
                errors.value[`contacts.${index}.email`].push('Please enter a valid email address');
                isValid = false;
            }
        }
    });

    // Validate Website format (if provided)
    if (form.builder_website && form.builder_website.trim() !== '') {
        try {
            new URL(form.builder_website);
        } catch {
            errors.value.builder_website = ['Please enter a valid URL'];
            isValid = false;
        }
    }

    // Validate Location Link format (if provided)
    if (form.location_link && form.location_link.trim() !== '') {
        try {
            new URL(form.location_link);
        } catch {
            errors.value.location_link = ['Please enter a valid URL'];
            isValid = false;
        }
    }

    return isValid;
};

const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};
    
    // Frontend validation
    if (!validateForm()) {
        loading.value = false;
        // Scroll to first error
        const firstErrorField = document.querySelector('.is-invalid');
        if (firstErrorField) {
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
    }
    
    try {
        // Create FormData for file uploads
        const formData = new FormData();
        
        // Step 1: Builder Information
        formData.append('builder_id', form.builder_id);
        if (form.builder_website) {
            formData.append('builder_website', form.builder_website);
        }
        
        // Step 1: Project Information
        formData.append('project_name', form.project_name);
        if (form.address) formData.append('address', form.address);
        if (form.state_id) formData.append('state_id', form.state_id);
        if (form.city_id) formData.append('city_id', form.city_id);
        formData.append('locality_id', form.locality_id);
        if (form.pincode) formData.append('pincode', form.pincode);
        if (form.location_link) formData.append('location_link', form.location_link);
        if (form.land_size) formData.append('land_size', form.land_size);
        if (form.measurement_unit_id) formData.append('measurement_unit_id', form.measurement_unit_id);
        if (form.rera_no) formData.append('rera_no', form.rera_no);
        if (form.project_status) formData.append('project_status', form.project_status);
        if (form.restricted_user_ids && form.restricted_user_ids.length > 0) {
            form.restricted_user_ids.forEach((id: string | number) => {
                formData.append('restricted_user_ids[]', String(id));
            });
        }
        
        // Step 1: Contacts
        contacts.value.forEach((contact, index) => {
            formData.append(`contacts[${index}][name]`, contact.name);
            formData.append(`contacts[${index}][mobile]`, contact.mobile);
            if (contact.email) formData.append(`contacts[${index}][email]`, contact.email);
            if (contact.designation) formData.append(`contacts[${index}][designation]`, contact.designation);
        });
        
        // Step 2: Construction Type & Category
        formData.append('construction_type_id', form.construction_type_id);
        formData.append('category_id', form.category_id);
        if (form.sub_category_id) formData.append('sub_category_id', form.sub_category_id);
        
        // Step 2: Tower Details
        if (form.no_of_towers) formData.append('no_of_towers', form.no_of_towers);
        if (form.no_of_floors) formData.append('no_of_floors', form.no_of_floors);
        if (form.total_units) formData.append('total_units', form.total_units);
        if (form.no_of_unit_each_tower) formData.append('no_of_unit_each_tower', form.no_of_unit_each_tower);
        if (form.no_of_lift) formData.append('no_of_lift', form.no_of_lift);
        if (form.front_road_width) formData.append('front_road_width', form.front_road_width);
        if (form.front_road_width_measurement_unit_id) formData.append('front_road_width_measurement_unit_id', form.front_road_width_measurement_unit_id);
        if (form.washroom) formData.append('washroom', form.washroom);
        formData.append('towers_different_specification', form.towers_different_specification ? '1' : '0');
        
        // Step 2: Tower Details Array
        if (form.tower_details && form.tower_details.length > 0) {
            form.tower_details.forEach((tower: any, index: number) => {
                if (tower.tower_name) formData.append(`tower_details[${index}][tower_name]`, tower.tower_name);
                if (tower.saleable_area_from) formData.append(`tower_details[${index}][saleable_area_from]`, tower.saleable_area_from);
                if (tower.saleable_area_to) formData.append(`tower_details[${index}][saleable_area_to]`, tower.saleable_area_to);
                if (tower.saleable_area_unit_id) formData.append(`tower_details[${index}][saleable_area_unit_id]`, tower.saleable_area_unit_id);
                if (tower.ceiling_height) formData.append(`tower_details[${index}][ceiling_height]`, tower.ceiling_height);
                if (tower.ceiling_height_unit_id) formData.append(`tower_details[${index}][ceiling_height_unit_id]`, tower.ceiling_height_unit_id);
                formData.append(`tower_details[${index}][show_carpet_area]`, tower.show_carpet_area ? '1' : '0');
                if (tower.carpet_area_from) formData.append(`tower_details[${index}][carpet_area_from]`, tower.carpet_area_from);
                if (tower.carpet_area_to) formData.append(`tower_details[${index}][carpet_area_to]`, tower.carpet_area_to);
                if (tower.carpet_area_unit_id) formData.append(`tower_details[${index}][carpet_area_unit_id]`, tower.carpet_area_unit_id);
                formData.append(`tower_details[${index}][show_builtup_area]`, tower.show_builtup_area ? '1' : '0');
                if (tower.builtup_area_from) formData.append(`tower_details[${index}][builtup_area_from]`, tower.builtup_area_from);
                if (tower.builtup_area_to) formData.append(`tower_details[${index}][builtup_area_to]`, tower.builtup_area_to);
                if (tower.builtup_area_unit_id) formData.append(`tower_details[${index}][builtup_area_unit_id]`, tower.builtup_area_unit_id);
            });
        }
        
        // Step 3: Parking Details
        formData.append('free_allotted_parking_four_wheeler', form.free_allotted_parking_four_wheeler ? '1' : '0');
        formData.append('free_allotted_parking_two_wheeler', form.free_allotted_parking_two_wheeler ? '1' : '0');
        formData.append('available_for_purchase', form.available_for_purchase ? '1' : '0');
        if (form.no_of_parking) formData.append('no_of_parking', form.no_of_parking);
        if (form.total_floor_for_parking) formData.append('total_floor_for_parking', form.total_floor_for_parking);
        
        // Step 3: Basement Parking Array
        if (form.basement_parking && form.basement_parking.length > 0) {
            form.basement_parking.forEach((parking: any, index: number) => {
                if (parking.floor_no) formData.append(`basement_parking[${index}][floor_no]`, parking.floor_no);
                if (parking.ev_charging_point) formData.append(`basement_parking[${index}][ev_charging_point]`, parking.ev_charging_point);
                if (parking.hydraulic_parking) formData.append(`basement_parking[${index}][hydraulic_parking]`, parking.hydraulic_parking);
                if (parking.height_of_basement) formData.append(`basement_parking[${index}][height_of_basement]`, parking.height_of_basement);
                if (parking.height_of_basement_unit_id) formData.append(`basement_parking[${index}][height_of_basement_unit_id]`, parking.height_of_basement_unit_id);
            });
        }
        
        // Step 3: Amenities
        if (form.amenity_ids && form.amenity_ids.length > 0) {
            form.amenity_ids.forEach((amenityId: string) => {
                formData.append('amenity_ids[]', amenityId);
            });
        }
        
        // Step 3: Documents
        if (form.document_uploads && form.document_uploads.length > 0) {
            form.document_uploads.forEach((doc: any, docIndex: number) => {
                if (doc.category) {
                    formData.append(`document_uploads[${docIndex}][category]`, doc.category);
                }
                if (doc.files && doc.files.length > 0) {
                    doc.files.forEach((file: File, fileIndex: number) => {
                        formData.append(`document_uploads[${docIndex}][files][${fileIndex}]`, file);
                    });
                }
            });
        }
        
        // Step 3: Brochure File
        if (form.brochure_file) {
            formData.append('brochure_file', form.brochure_file);
        } else if (isEditMode.value && form.brochure_deleted) {
            // If editing and brochure was marked as deleted, send delete flag
            formData.append('delete_brochure', '1');
        }
        
        // Step 3: Remark
        if (form.remark) formData.append('remark', form.remark);
        
        // Send request via axios
        // Note: Laravel handles CSRF token automatically via cookies
        // For PUT requests with file uploads, we need to use POST with _method=PUT
        const url = isEditMode.value ? `/projects/${props.project?.id}` : '/projects';
        
        if (isEditMode.value) {
            formData.append('_method', 'PUT');
        }
        
        const response = await axios.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        
        if (response.data.success) {
            // Show success toast
            showToast(
                isEditMode.value ? 'Project updated successfully!' : 'Project created successfully!',
                'success'
            );
            
            // Redirect to projects list after showing the toast
            setTimeout(() => {
                router.visit('/projects');
            }, 2500);
        }
    } catch (error: any) {
        console.error('Error creating project:', error);
        loading.value = false;
        
        // Handle validation errors
        if (error.response?.status === 422 && error.response?.data?.errors) {
            errors.value = error.response.data.errors;
            // Show validation error toast
            showToast('Please fix the validation errors.', 'error');
            // Scroll to first error
            const firstErrorField = document.querySelector('.is-invalid');
            if (firstErrorField) {
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            // Show error toast
            showToast(
                error.response?.data?.message || (isEditMode.value ? 'Failed to update project. Please try again.' : 'Failed to create project. Please try again.'),
                'error'
            );
        }
    }
};

const handleNext = () => {
    if (currentStep.value === 1) {
        // Validate step 1 before proceeding
        if (!validateForm()) {
            return;
        }
        currentStep.value = 2;
    } else if (currentStep.value === 2) {
        // Validate step 2 before proceeding
        if (!form.construction_type_id) {
            errors.value.construction_type_id = ['Please select a construction type'];
            return;
        }
        if (!form.category_id) {
            errors.value.category_id = ['Please select a category'];
            return;
        }
        // Validate sub-category - check if selected category has sub-categories
        if (form.category_id) {
            const selectedConstructionType = props.constructionTypes.find(
                (ct) => ct.id === Number(form.construction_type_id)
            );
            if (selectedConstructionType) {
                const selectedCategory = selectedConstructionType.categories?.find(
                    (cat) => cat.id === Number(form.category_id)
                );
                if (selectedCategory && selectedCategory.sub_categories && selectedCategory.sub_categories.length > 0 && !form.sub_category_id) {
                    errors.value.sub_category_id = ['Please select a sub-category'];
                    return;
                }
            }
        }
        currentStep.value = 3;
    } else if (currentStep.value === 3) {
        // Validate step 3 and submit
        // TODO: Add validation for step 3 if needed
        handleSubmit();
    }
};

const handlePrevious = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

// Initialize form data from project if editing
const initializeFormFromProject = async () => {
    if (!props.project) return;
    
    isInitializingForm.value = true;
    
    const project = props.project;
    
    // Basic project fields
    form.builder_id = String(project.builder_id || '');
    form.builder_website = project.builder_website || '';
    form.project_name = project.project_name || '';
    form.address = project.address || '';
    form.pincode = project.pincode || '';
    form.location_link = project.location_link || '';
    form.land_size = project.land_size || '';
    form.measurement_unit_id = project.measurement_unit_id ? String(project.measurement_unit_id) : '';
    form.rera_no = project.rera_no || '';
    form.project_status = project.project_status || '';
    form.restricted_user_ids = project.restricted_user_ids || [];
    form.construction_type_id = project.construction_type_id ? String(project.construction_type_id) : '';
    form.category_id = project.category_id ? String(project.category_id) : '';
    form.sub_category_id = project.sub_category_id ? String(project.sub_category_id) : '';
    form.no_of_towers = project.no_of_towers || '';
    form.no_of_floors = project.no_of_floors || '';
    form.total_units = project.total_units || '';
    form.no_of_unit_each_tower = project.no_of_unit_each_tower || '';
    form.no_of_lift = project.no_of_lift || '';
    form.front_road_width = project.front_road_width || '';
    form.front_road_width_measurement_unit_id = project.front_road_width_measurement_unit_id ? String(project.front_road_width_measurement_unit_id) : '';
    form.washroom = project.washroom || 'Private';
    form.towers_different_specification = project.towers_different_specification || false;
    form.free_allotted_parking_four_wheeler = project.free_allotted_parking_four_wheeler || false;
    form.free_allotted_parking_two_wheeler = project.free_allotted_parking_two_wheeler || false;
    form.available_for_purchase = project.available_for_purchase || false;
    form.no_of_parking = project.no_of_parking || '';
    form.total_floor_for_parking = project.total_floor_for_parking || '1';
    form.remark = project.remark || '';
    form.existing_brochure_file = project.brochure_file || null;
    
    // Load cities FIRST if state is selected, then set state_id
    if (project.state_id) {
        try {
            const citiesResponse = await axios.get(`/states/${project.state_id}/cities`);
            cities.value = citiesResponse.data;
            
            // Wait for cities to be loaded
            await nextTick();
            
            // Now set state_id (this will trigger watcher, but we're initializing so it won't clear)
            // Convert to number to match option value type (:value="state.id" is a number)
            form.state_id = project.state_id ? String(project.state_id) : '';
            
            // Load localities if city is selected
            if (project.city_id) {
                try {
                    const localitiesResponse = await axios.get(`/cities/${project.city_id}/localities`);
                    localities.value = localitiesResponse.data;
                    
                    // Wait for localities to be loaded
                    await nextTick();
                    
                    // Now set city_id and locality_id (as strings, Vue will handle conversion)
                    form.city_id = project.city_id ? String(project.city_id) : '';
                    form.locality_id = project.locality_id ? String(project.locality_id) : '';
                } catch (error) {
                    console.error('Error loading localities:', error);
                    form.city_id = project.city_id ? String(project.city_id) : '';
                    form.locality_id = project.locality_id ? String(project.locality_id) : '';
                }
            } else {
                form.city_id = '';
                form.locality_id = project.locality_id ? String(project.locality_id) : '';
            }
        } catch (error) {
            console.error('Error loading cities:', error);
            form.state_id = project.state_id ? String(project.state_id) : '';
            form.city_id = project.city_id ? String(project.city_id) : '';
            form.locality_id = project.locality_id ? String(project.locality_id) : '';
        }
    } else {
        form.state_id = '';
        form.city_id = '';
        form.locality_id = project.locality_id ? String(project.locality_id) : '';
    }
    
    // Wait a bit to ensure watchers don't interfere and DOM is updated
    await nextTick();
    isInitializingForm.value = false;
    await nextTick(); // One more tick to ensure selects are updated
    
    // Initialize contacts
    if (project.contacts && project.contacts.length > 0) {
        contacts.value = project.contacts.map((contact: any) => ({
            id: contact.id || contactIdCounter++,
            name: contact.name || '',
            mobile: contact.mobile || '',
            email: contact.email || '',
            designation: contact.designation || '',
        }));
    }
    
    // Initialize tower details
    if (project.tower_details && project.tower_details.length > 0) {
        form.tower_details = project.tower_details.map((tower: any) => ({
            tower_name: tower.tower_name || '',
            saleable_area_from: tower.saleable_area_from || '',
            saleable_area_to: tower.saleable_area_to || '',
            saleable_area_unit_id: tower.saleable_area_unit_id ? String(tower.saleable_area_unit_id) : '',
            ceiling_height: tower.ceiling_height || '',
            ceiling_height_unit_id: tower.ceiling_height_unit_id ? String(tower.ceiling_height_unit_id) : '',
            carpet_area_from: tower.carpet_area_from || '',
            carpet_area_to: tower.carpet_area_to || '',
            carpet_area_unit_id: tower.carpet_area_unit_id ? String(tower.carpet_area_unit_id) : '',
            builtup_area_from: tower.builtup_area_from || '',
            builtup_area_to: tower.builtup_area_to || '',
            builtup_area_unit_id: tower.builtup_area_unit_id ? String(tower.builtup_area_unit_id) : '',
            show_carpet_area: tower.show_carpet_area || false,
            show_builtup_area: tower.show_builtup_area || false,
        }));
    }
    
    // Initialize basement parking
    if (project.basement_parking && project.basement_parking.length > 0) {
        form.basement_parking = project.basement_parking.map((parking: any) => ({
            floor_no: parking.floor_no || '',
            ev_charging_point: parking.ev_charging_point || '',
            hydraulic_parking: parking.hydraulic_parking || '',
            height_of_basement: parking.height_of_basement || '',
            height_of_basement_unit_id: parking.height_of_basement_unit_id ? String(parking.height_of_basement_unit_id) : '',
        }));
    }
    
    // Initialize amenities
    if (project.amenities && project.amenities.length > 0) {
        form.amenity_ids = project.amenities.map((amenity: any) => String(amenity.id));
    }
    
    // Initialize documents (existing documents are shown, new ones can be added)
    // Group documents strictly by category - all documents with the same category are grouped together
    if (project.documents && project.documents.length > 0) {
        // Sort documents by category first, then by created_at to maintain order within each category
        const sortedDocs = [...project.documents].sort((a: any, b: any) => {
            const categoryA = (a.category || 'Other').toLowerCase();
            const categoryB = (b.category || 'Other').toLowerCase();
            if (categoryA !== categoryB) {
                return categoryA.localeCompare(categoryB);
            }
            const timeA = new Date(a.created_at || 0).getTime();
            const timeB = new Date(b.created_at || 0).getTime();
            return timeA - timeB;
        });
        
        // Group documents strictly by category
        const documentGroupsMap = new Map<string, any>();
        
        sortedDocs.forEach((doc: any) => {
            const category = doc.category || 'Other';
            
            // Check if a group for this category already exists
            if (!documentGroupsMap.has(category)) {
                // Create new group for this category
                documentGroupsMap.set(category, {
                    id: doc.id,
                    category: category,
                    files: [],
                    uploaded_files: [],
                    // Don't set existing_file_path/existing_file_name when using existing_documents array
                    // to avoid duplicate display
                    existing_documents: [{
                        id: doc.id,
                        file_path: doc.file_path,
                        file_name: doc.file_name,
                    }],
                });
            } else {
                // Add to existing group for this category
                const group = documentGroupsMap.get(category);
                group.existing_documents.push({
                    id: doc.id,
                    file_path: doc.file_path,
                    file_name: doc.file_name,
                });
            }
        });
        
        // Convert map to array
        const documentGroups = Array.from(documentGroupsMap.values());
        
        form.document_uploads = documentGroups;
        
        // Don't add empty row when editing - user can add new rows using the + button
    } else {
        // If no documents exist, ensure at least one empty entry
        form.document_uploads = [{
            id: 1,
            category: '',
            files: [],
            uploaded_files: [],
        }];
    }
};

// Initialize Select2 only for restricted users multiple select
onMounted(async () => {
    // Show loader if editing
    if (isEditMode.value) {
        isPageLoading.value = true;
    }
    
    try {
        // Initialize form data if editing
        if (isEditMode.value) {
            await initializeFormFromProject();
        }
        
        await nextTick();
        
        if (typeof window.$ !== 'undefined' && window.$.fn.select2) {
            // Initialize Select2 for restricted users multiple select
            const selectElement = window.$('#restricted_user_ids');
            
            if (selectElement.length) {
                selectElement.select2({
                    placeholder: 'Select Restricted Users',
                    allowClear: true,
                    width: '100%',
                });
                
                // Set initial values if form.restricted_user_ids has values
                if (form.restricted_user_ids && form.restricted_user_ids.length > 0) {
                    selectElement.val(form.restricted_user_ids.map(id => String(id))).trigger('change');
                }
                
                // Sync Select2 with Vue model for restricted users
                selectElement.on('change', function(this: HTMLSelectElement) {
                    const selectedValues = window.$(this).val() || [];
                    form.restricted_user_ids = Array.isArray(selectedValues) 
                        ? selectedValues.map((val: string | number) => val)
                        : selectedValues ? [selectedValues] : [];
                });
            }
        }
        
        // Wait a bit more to ensure all DOM updates are complete
        await nextTick();
        await new Promise(resolve => setTimeout(resolve, 100));
    } finally {
        // Hide loader after everything is loaded
        isPageLoading.value = false;
    }
});
</script>

<template>
    <Head :title="isEditMode ? 'Edit Project' : 'Create Project'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mt-4">
                    <div class="card-header color-dark fw-500">
                        <h4 class="mb-0">{{ isEditMode ? 'Edit Project' : 'Create Project' }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- Loading Indicator in Page Content -->
                        <div
                            v-if="isPageLoading"
                            class="d-flex flex-column align-items-center justify-content-center"
                            style="min-height: 400px;"
                        >
                            <div class="atbd-spin-dots spin-lg">
                                <span class="spin-dot badge-dot dot-primary"></span>
                                <span class="spin-dot badge-dot dot-primary"></span>
                                <span class="spin-dot badge-dot dot-primary"></span>
                                <span class="spin-dot badge-dot dot-primary"></span>
                            </div>
                            <p class="text-dark mt-3 mb-0 fw-medium">Loading project data...</p>
                        </div>
                        
                        <div v-show="!isPageLoading">
                            <div class="row">
                                <!-- Step Indicator (Left Side) -->
                                <div class="col-md-3 col-lg-2">
                                <StepIndicator 
                                    :current-step="currentStep"
                                />
                                </div>
                                
                                <!-- Form Content (Right Side) -->
                                <div class="col-md-9 col-lg-10">
                                <form @submit.prevent="handleSubmit">
                            <!-- Step 1: Project & Builder Information -->
                            <div v-if="currentStep === 1">
                                <!-- Builder Information Section -->
                                <BuilderInformation
                                    :builders="builders"
                                    :form="form"
                                    :errors="errors"
                                    :validate-form="validateForm"
                                />

                                <!-- Project Information Section -->
                                <ProjectInformation
                                    :states="states"
                                    :cities="cities"
                                    :localities="localities"
                                    :measurement-units="measurementUnits"
                                    :form="form"
                                    :errors="errors"
                                    :validate-form="validateForm"
                                />

                                <!-- Contact Details Section -->
                                <ContactDetails
                                    :restricted-user-options="restrictedUserOptions"
                                    :contacts="contacts"
                                    :form="form"
                                    :errors="errors"
                                    :validate-form="validateForm"
                                    :add-contact="addContact"
                                    :remove-contact="removeContact"
                                />
                            </div>

                            <!-- Step 2: Construction Type & Category -->
                            <div v-if="currentStep === 2">
                                <ChoiseSelection
                                    :construction-types="constructionTypes"
                                    :model-value="{
                                        construction_type_id: form.construction_type_id,
                                        category_id: form.category_id,
                                        sub_category_id: form.sub_category_id,
                                    }"
                                    :errors="errors"
                                    :show-ids="true"
                                    @update:modelValue="(value) => {
                                        form.construction_type_id = value.construction_type_id;
                                        form.category_id = value.category_id;
                                        form.sub_category_id = value.sub_category_id;
                                    }"
                                />

                                <!-- Tower Details Section (Conditional based on Construction Type, Category, Sub Category) -->
                                <TowerDetailsSection
                                    :form="form"
                                    :errors="errors"
                                    :measurement-units="measurementUnits"
                                    :condition="{
                                        constructionTypeId: '1',
                                        categoryId: '1',
                                        subCategoryId: '1'
                                    }"
                                />
                            </div>

                            <!-- Step 3: Parking & Amenities -->
                            <div v-if="currentStep === 3">
                                <ParkingAndAmenities
                                    :form="form"
                                    :errors="errors"
                                    :amenities="amenities"
                                    :measurement-units="measurementUnits"
                                />
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button
                                    v-if="currentStep > 1"
                                    type="button"
                                    @click="handlePrevious"
                                    class="btn btn-secondary btn-sm btn-previous"
                                >
                                    Previous
                                </button>
                                <button
                                    type="button"
                                    @click="handleNext"
                                    class="btn btn-primary btn-sm btn-primary-custom"
                                    :disabled="loading"
                                >
                                    {{ loading ? 'Processing...' : currentStep === 3 ? 'Create Project' : 'Next' }}
                                </button>
                            </div>
                                </form>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div
            v-if="toast.show"
            class="toast-notification"
            :class="toast.type === 'success' ? 'toast-success' : 'toast-error'"
            style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; padding: 12px 16px; border-radius: 8px; color: white; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.15);"
        >
            <div class="d-flex align-items-center">
                <span class="me-2" style="font-size: 18px;">
                    {{ toast.type === 'success' ? '✅' : '❌' }}
                </span>
                <span>{{ toast.message }}</span>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.toast-success {
    background-color: #28a745;
}

.toast-error {
    background-color: #dc3545;
}

.toast-notification {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

