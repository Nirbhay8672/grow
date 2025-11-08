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

interface Props {
    builders: Builder[];
    states: State[];
    measurementUnits: MeasurementUnit[];
    users: User[];
    constructionTypes: ConstructionType[];
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
        // Validate step 2 and submit
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
        
        // TODO: Submit form or proceed to next step
        console.log('Form validated, submitting...');
        // router.post('/projects', formData, { ... });
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

                                <!-- Tower Details (shown when construction_type_id=1, category_id=1, sub_category_id=1) -->
                                <div v-if="form.construction_type_id === '1' && form.category_id === '1' && form.sub_category_id === '1'" class="mb-4">
                                    <h5 class="mb-3 section-title">Tower Details</h5>
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label for="no_of_towers" class="form-label">No. Of Towers</label>
                                            <input
                                                id="no_of_towers"
                                                v-model="form.no_of_towers"
                                                type="number"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.no_of_towers }"
                                                placeholder="Enter number of towers"
                                            />
                                            <div v-if="errors.no_of_towers" class="invalid-feedback d-block">
                                                {{ errors.no_of_towers[0] }}
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="no_of_floors" class="form-label">No. Of Floors</label>
                                            <input
                                                id="no_of_floors"
                                                v-model="form.no_of_floors"
                                                type="number"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.no_of_floors }"
                                                placeholder="Enter number of floors"
                                            />
                                            <div v-if="errors.no_of_floors" class="invalid-feedback d-block">
                                                {{ errors.no_of_floors[0] }}
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="total_units" class="form-label">Total Units</label>
                                            <input
                                                id="total_units"
                                                v-model="form.total_units"
                                                type="number"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.total_units }"
                                                placeholder="Enter total units"
                                            />
                                            <div v-if="errors.total_units" class="invalid-feedback d-block">
                                                {{ errors.total_units[0] }}
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="no_of_unit_each_tower" class="form-label">No. Of Unit Each Tower</label>
                                            <input
                                                id="no_of_unit_each_tower"
                                                v-model="form.no_of_unit_each_tower"
                                                type="number"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.no_of_unit_each_tower }"
                                                placeholder="Enter units per tower"
                                            />
                                            <div v-if="errors.no_of_unit_each_tower" class="invalid-feedback d-block">
                                                {{ errors.no_of_unit_each_tower[0] }}
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="no_of_lift" class="form-label">No. Of Lift</label>
                                            <input
                                                id="no_of_lift"
                                                v-model="form.no_of_lift"
                                                type="number"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.no_of_lift }"
                                                placeholder="Enter number of lifts"
                                            />
                                            <div v-if="errors.no_of_lift" class="invalid-feedback d-block">
                                                {{ errors.no_of_lift[0] }}
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="front_road_width" class="form-label">Front Road Width <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-sm">
                                                <input
                                                    id="front_road_width"
                                                    v-model="form.front_road_width"
                                                    type="number"
                                                    class="form-control form-control-sm"
                                                    :class="{ 'is-invalid': errors.front_road_width }"
                                                    placeholder="Enter road width"
                                                    required
                                                />
                                                <select
                                                    id="front_road_width_measurement_unit_id"
                                                    v-model="form.front_road_width_measurement_unit_id"
                                                    class="form-select form-select-sm"
                                                    :class="{ 'is-invalid': errors.front_road_width_measurement_unit_id }"
                                                >
                                                    <option value="">Select Unit</option>
                                                    <option 
                                                        v-for="unit in props.measurementUnits" 
                                                        :key="unit.id" 
                                                        :value="String(unit.id)"
                                                    >
                                                        {{ unit.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div v-if="errors.front_road_width" class="invalid-feedback d-block">
                                                {{ errors.front_road_width[0] }}
                                            </div>
                                            <div v-if="errors.front_road_width_measurement_unit_id" class="invalid-feedback d-block">
                                                {{ errors.front_road_width_measurement_unit_id[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Washroom (shown when construction_type_id=1, category_id=1, sub_category_id=1) -->
                                <div v-if="form.construction_type_id === '1' && form.category_id === '1' && form.sub_category_id === '1'" class="mb-4">
                                    <h5 class="mb-3 section-title">Washroom</h5>
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="form-check form-check-inline construction-type-radio">
                                                <input
                                                    id="washroom_private"
                                                    v-model="form.washroom"
                                                    type="radio"
                                                    value="Private"
                                                    class="form-check-input"
                                                    :class="{ 'is-invalid': errors.washroom }"
                                                />
                                                <label 
                                                    for="washroom_private"
                                                    class="form-check-label construction-type-label"
                                                >
                                                    Private
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline construction-type-radio">
                                                <input
                                                    id="washroom_public"
                                                    v-model="form.washroom"
                                                    type="radio"
                                                    value="Public"
                                                    class="form-check-input"
                                                    :class="{ 'is-invalid': errors.washroom }"
                                                />
                                                <label 
                                                    for="washroom_public"
                                                    class="form-check-label construction-type-label"
                                                >
                                                    Public
                                                </label>
                                            </div>
                                        </div>
                                        <div v-if="errors.washroom" class="invalid-feedback d-block">
                                            {{ errors.washroom[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button
                                    v-if="currentStep === 2"
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
                                    {{ loading ? 'Processing...' : currentStep === 2 ? 'Create Project' : 'Next' }}
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

