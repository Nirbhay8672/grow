<script setup lang="ts">
import { ref, reactive, watch, onMounted, nextTick } from 'vue';
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

interface User {
    id: number;
    name: string;
    email: string;
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

interface Props {
    builders: Builder[];
    states: State[];
    measurementUnits: MeasurementUnit[];
    users: User[];
    constructionTypes: ConstructionType[];
    amenities: Amenity[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Create Project',
        href: '/projects/create',
    },
];

const currentStep = ref(2); // Set to 2 for testing - change back to 1 for production
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});

const cities = ref<City[]>([]);
const localities = ref<Locality[]>([]);

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
    restricted_user_ids: [] as number[],
    
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
        id: number;
        category: string;
        files: File[];
        uploaded_files?: Array<{
            id?: number;
            name: string;
            url?: string;
        }>;
    }>,
    brochure_file: null as File | null,
    remark: '',
});

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
        // Prepare form data with contacts
        const formData = {
            ...form,
            contacts: contacts.value,
        };
        
        // TODO: Implement project creation API call
        console.log('Form data:', formData);
        // router.post('/projects', formData, {
        //     onSuccess: () => {
        //         // Handle success
        //     },
        //     onError: (err) => {
        //         if (err.errors) {
        //             errors.value = err.errors;
        //         }
        //     },
        //     onFinish: () => {
        //         loading.value = false;
        //     },
        // });
    } catch (error) {
        console.error('Error creating project:', error);
        loading.value = false;
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

// Initialize Select2 only for restricted users multiple select
onMounted(() => {
    nextTick(() => {
        if (typeof window.$ !== 'undefined' && window.$.fn.select2) {
            // Initialize Select2 for restricted users multiple select
            window.$('#restricted_user_ids').select2({
                placeholder: 'Select Restricted Users',
                allowClear: true,
                width: '100%',
            });
            
            // Sync Select2 with Vue model for restricted users
            window.$('#restricted_user_ids').on('change', function(this: HTMLSelectElement) {
                const selectedValues = window.$(this).val() || [];
                form.restricted_user_ids = Array.isArray(selectedValues) 
                    ? selectedValues.map((val: string | number) => Number(val))
                    : [];
            });
        }
    });
});
</script>

<template>
    <Head title="Create Project" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mt-4">
                    <div class="card-header color-dark fw-500">
                        <h4 class="mb-0">Create Project</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Step Indicator (Left Side) -->
                            <div class="col-md-3 col-lg-2">
                                <StepIndicator :current-step="currentStep" />
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
                                    :users="users"
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
    </AppLayout>
</template>

