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
                        <!-- Step Indicator -->
                        <StepIndicator :current-step="currentStep" />

                        <!-- Form -->
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

                            <!-- Step 2: Construction Type -->
                            <div v-if="currentStep === 2">
                                <div class="mb-4">
                                    <h5 class="mb-3 section-title">Property Type</h5>
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div 
                                                v-for="constructionType in constructionTypes" 
                                                :key="constructionType.id"
                                                class="form-check form-check-inline construction-type-radio"
                                            >
                                                <input
                                                    :id="`construction_type_${constructionType.id}`"
                                                    v-model="form.construction_type_id"
                                                    type="radio"
                                                    :value="String(constructionType.id)"
                                                    class="form-check-input"
                                                    :class="{ 'is-invalid': errors.construction_type_id }"
                                                />
                                                <label 
                                                    :for="`construction_type_${constructionType.id}`"
                                                    class="form-check-label construction-type-label"
                                                >
                                                    {{ constructionType.name }}
                                                </label>
                                            </div>
                                        </div>
                                        <div v-if="errors.construction_type_id" class="invalid-feedback d-block">
                                            {{ errors.construction_type_id[0] }}
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
    </AppLayout>
</template>
