<script setup lang="ts">
import { ref, reactive, watch, onMounted, nextTick } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Plus, X } from 'lucide-vue-next';

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

interface Props {
    builders: Builder[];
    states: State[];
    measurementUnits: MeasurementUnit[];
    users: User[];
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

const currentStep = ref(1);
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
            
            // Update Select2 for city with new options
            nextTick(() => {
                if (typeof window.$ !== 'undefined' && window.$.fn.select2) {
                    window.$('#city_id').val(null).trigger('change.select2');
                }
            });
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
        
            // Update Select2
            nextTick(() => {
                if (typeof window.$ !== 'undefined' && window.$.fn.select2) {
                    window.$('#city_id').val(null).trigger('change.select2');
                    window.$('#locality_id').val(null).trigger('change.select2');
                }
            });
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
            
            // Update Select2 for locality with new options
            nextTick(() => {
                if (typeof window.$ !== 'undefined' && window.$.fn.select2) {
                    window.$('#locality_id').val(null).trigger('change.select2');
                }
            });
        } catch (error) {
            console.error('Error loading localities:', error);
            localities.value = [];
        }
    } else {
        localities.value = [];
        form.locality_id = '';
        form.pincode = '';
        
            // Update Select2
            nextTick(() => {
                if (typeof window.$ !== 'undefined' && window.$.fn.select2) {
                    window.$('#locality_id').val(null).trigger('change.select2');
                }
            });
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

const handleCancel = () => {
    router.visit('/dashboard');
};

// Initialize Select2 for all select inputs after component mounts
onMounted(() => {
    nextTick(() => {
        if (typeof window.$ !== 'undefined' && window.$.fn.select2) {
            // Initialize Select2 for single select inputs with search
            const singleSelects = [
                { id: 'builder_id', placeholder: 'Select Builder' },
                { id: 'state_id', placeholder: 'Select State' },
                { id: 'city_id', placeholder: 'Select City' },
                { id: 'locality_id', placeholder: 'Select Locality' },
                { id: 'measurement_unit_id', placeholder: 'Select Unit' },
            ];

            singleSelects.forEach(({ id, placeholder }) => {
                window.$(`#${id}`).select2({
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%',
                });

                // Sync Select2 with Vue model
                window.$(`#${id}`).on('change', function(this: HTMLSelectElement) {
                    const value = window.$(this).val();
                    if (id === 'builder_id') form.builder_id = value ? String(value) : '';
                    else if (id === 'state_id') form.state_id = value ? String(value) : '';
                    else if (id === 'city_id') form.city_id = value ? String(value) : '';
                    else if (id === 'locality_id') form.locality_id = value ? String(value) : '';
                    else if (id === 'measurement_unit_id') form.measurement_unit_id = value ? String(value) : '';
                });
            });

            // Watch Vue model changes and update Select2
            watch(() => form.builder_id, (newVal) => {
                nextTick(() => {
                    if (typeof window.$ !== 'undefined') {
                        window.$('#builder_id').val(newVal || null).trigger('change');
                    }
                });
            });

            watch(() => form.state_id, (newVal) => {
                nextTick(() => {
                    if (typeof window.$ !== 'undefined') {
                        window.$('#state_id').val(newVal || null).trigger('change');
                    }
                });
            });

            watch(() => form.city_id, (newVal) => {
                nextTick(() => {
                    if (typeof window.$ !== 'undefined') {
                        window.$('#city_id').val(newVal || null).trigger('change');
                    }
                });
            });

            watch(() => form.locality_id, (newVal) => {
                nextTick(() => {
                    if (typeof window.$ !== 'undefined') {
                        window.$('#locality_id').val(newVal || null).trigger('change');
                    }
                });
            });

            watch(() => form.measurement_unit_id, (newVal) => {
                nextTick(() => {
                    if (typeof window.$ !== 'undefined') {
                        window.$('#measurement_unit_id').val(newVal || null).trigger('change');
                    }
                });
            });

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
                        <div class="d-flex justify-content-center mb-4">
                            <div class="d-flex align-items-center" style="gap: 20px;">
                                <div class="d-flex flex-column align-items-center">
                                    <div 
                                        class="rounded-circle d-flex align-items-center justify-content-center"
                                        :style="{
                                            width: '40px',
                                            height: '40px',
                                            backgroundColor: currentStep >= 1 ? '#1c467b' : '#e5e7eb',
                                            color: currentStep >= 1 ? '#ffffff' : '#9ca3af',
                                            fontWeight: 'bold',
                                            fontSize: '16px'
                                        }"
                                    >
                                        1
                                    </div>
                                    <span 
                                        class="mt-2"
                                        :style="{
                                            fontSize: '14px',
                                            color: currentStep >= 1 ? '#1c467b' : '#9ca3af',
                                            fontWeight: currentStep >= 1 ? '500' : 'normal'
                                        }"
                                    >
                                        Project & Builder Information
                                    </span>
                                </div>
                                <div style="width: 100px; height: 2px; background-color: #e5e7eb;"></div>
                                <div class="d-flex flex-column align-items-center">
                                    <div 
                                        class="rounded-circle d-flex align-items-center justify-content-center"
                                        :style="{
                                            width: '40px',
                                            height: '40px',
                                            backgroundColor: currentStep >= 2 ? '#1c467b' : '#e5e7eb',
                                            color: currentStep >= 2 ? '#ffffff' : '#9ca3af',
                                            fontWeight: 'bold',
                                            fontSize: '16px'
                                        }"
                                    >
                                        2
                                    </div>
                                    <span 
                                        class="mt-2"
                                        :style="{
                                            fontSize: '14px',
                                            color: currentStep >= 2 ? '#1c467b' : '#9ca3af',
                                            fontWeight: currentStep >= 2 ? '500' : 'normal'
                                        }"
                                    >
                                        Project Type & Basic Info
                                    </span>
                                </div>
                                <div style="width: 100px; height: 2px; background-color: #e5e7eb;"></div>
                                <div class="d-flex flex-column align-items-center">
                                    <div 
                                        class="rounded-circle d-flex align-items-center justify-content-center"
                                        :style="{
                                            width: '40px',
                                            height: '40px',
                                            backgroundColor: currentStep >= 3 ? '#1c467b' : '#e5e7eb',
                                            color: currentStep >= 3 ? '#ffffff' : '#9ca3af',
                                            fontWeight: 'bold',
                                            fontSize: '16px'
                                        }"
                                    >
                                        3
                                    </div>
                                    <span 
                                        class="mt-2"
                                        :style="{
                                            fontSize: '14px',
                                            color: currentStep >= 3 ? '#1c467b' : '#9ca3af',
                                            fontWeight: currentStep >= 3 ? '500' : 'normal'
                                        }"
                                    >
                                        Parking & Amenities
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="handleSubmit">
                            <!-- Builder Information Section -->
                            <div class="mb-4">
                                <h5 class="mb-3" style="color: #1c467b; font-weight: 600;">Builder Information</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="builder_id" class="form-label">
                                                Builder Name <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                id="builder_id"
                                                v-model="form.builder_id"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.builder_id }"
                                                @blur="validateForm"
                                                required
                                            >
                                                <option value="">Select Builder</option>
                                                <option 
                                                    v-for="builder in builders" 
                                                    :key="builder.id" 
                                                    :value="builder.id"
                                                >
                                                    {{ builder.name }}
                                                </option>
                                            </select>
                                            <div v-if="errors.builder_id" class="invalid-feedback">
                                                {{ errors.builder_id[0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="builder_website" class="form-label">Website</label>
                                            <input
                                                id="builder_website"
                                                v-model="form.builder_website"
                                                type="url"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.builder_website }"
                                                placeholder="https://example.com"
                                                @blur="validateForm"
                                            />
                                            <div v-if="errors.builder_website" class="invalid-feedback">
                                                {{ errors.builder_website[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Project Information Section -->
                            <div class="mb-4">
                                <h5 class="mb-3" style="color: #1c467b; font-weight: 600;">Project Information</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="project_name" class="form-label">
                                                Project Name <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                id="project_name"
                                                v-model="form.project_name"
                                                type="text"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.project_name }"
                                                placeholder="Enter project name"
                                                @blur="validateForm"
                                                required
                                            />
                                            <div v-if="errors.project_name" class="invalid-feedback">
                                                {{ errors.project_name[0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea
                                                id="address"
                                                v-model="form.address"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.address }"
                                                placeholder="Enter address"
                                                rows="2"
                                            ></textarea>
                                            <div v-if="errors.address" class="invalid-feedback">
                                                {{ errors.address[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="state_id" class="form-label">State</label>
                                            <select
                                                id="state_id"
                                                v-model="form.state_id"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.state_id }"
                                            >
                                                <option value="">Select State</option>
                                                <option 
                                                    v-for="state in states" 
                                                    :key="state.id" 
                                                    :value="state.id"
                                                >
                                                    {{ state.name }}
                                                </option>
                                            </select>
                                            <div v-if="errors.state_id" class="invalid-feedback">
                                                {{ errors.state_id[0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="city_id" class="form-label">City</label>
                                            <select
                                                id="city_id"
                                                v-model="form.city_id"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.city_id }"
                                                :disabled="!form.state_id"
                                            >
                                                <option value="">Select City</option>
                                                <option 
                                                    v-for="city in cities" 
                                                    :key="city.id" 
                                                    :value="city.id"
                                                >
                                                    {{ city.name }}
                                                </option>
                                            </select>
                                            <div v-if="errors.city_id" class="invalid-feedback">
                                                {{ errors.city_id[0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="locality_id" class="form-label">
                                                Locality <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                id="locality_id"
                                                v-model="form.locality_id"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.locality_id }"
                                                :disabled="!form.city_id"
                                                @blur="validateForm"
                                                @change="validateForm"
                                                required
                                            >
                                                <option value="">Select Locality</option>
                                                <option 
                                                    v-for="locality in localities" 
                                                    :key="locality.id" 
                                                    :value="locality.id"
                                                >
                                                    {{ locality.name }}
                                                </option>
                                            </select>
                                            <div v-if="errors.locality_id" class="invalid-feedback">
                                                {{ errors.locality_id[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="pincode" class="form-label">Pincode</label>
                                            <input
                                                id="pincode"
                                                v-model="form.pincode"
                                                type="text"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.pincode }"
                                                placeholder="Enter pincode"
                                            />
                                            <div v-if="errors.pincode" class="invalid-feedback">
                                                {{ errors.pincode[0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="location_link" class="form-label">Location Link</label>
                                            <input
                                                id="location_link"
                                                v-model="form.location_link"
                                                type="url"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.location_link }"
                                                placeholder="https://maps.google.com/..."
                                                @blur="validateForm"
                                            />
                                            <div v-if="errors.location_link" class="invalid-feedback">
                                                {{ errors.location_link[0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="land_size" class="form-label">Land Size</label>
                                            <div class="input-group input-group-sm">
                                                <input
                                                    id="land_size"
                                                    v-model="form.land_size"
                                                    type="text"
                                                    class="form-control form-control-sm"
                                                    :class="{ 'is-invalid': errors.land_size }"
                                                    placeholder="Enter land size"
                                                />
                                                <select
                                                    id="measurement_unit_id"
                                                    v-model="form.measurement_unit_id"
                                                    class="form-control form-control-sm"
                                                    :class="{ 'is-invalid': errors.measurement_unit_id }"
                                                    style="max-width: 150px;"
                                                >
                                                    <option value="">Select Unit</option>
                                                    <option 
                                                        v-for="unit in measurementUnits" 
                                                        :key="unit.id" 
                                                        :value="unit.id"
                                                    >
                                                        {{ unit.name }}
                                                    </option>
                                                </select>
                                                <div v-if="errors.land_size || errors.measurement_unit_id" class="invalid-feedback d-block w-100">
                                                    <span v-if="errors.land_size">{{ errors.land_size[0] }}</span>
                                                    <span v-if="errors.measurement_unit_id">{{ errors.measurement_unit_id[0] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rera_no" class="form-label">Rera No</label>
                                            <input
                                                id="rera_no"
                                                v-model="form.rera_no"
                                                type="text"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.rera_no }"
                                                placeholder="Enter RERA number"
                                            />
                                            <div v-if="errors.rera_no" class="invalid-feedback">
                                                {{ errors.rera_no[0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="project_status" class="form-label">Project Status</label>
                                            <input
                                                id="project_status"
                                                v-model="form.project_status"
                                                type="text"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.project_status }"
                                                placeholder="Enter project status"
                                            />
                                            <div v-if="errors.project_status" class="invalid-feedback">
                                                {{ errors.project_status[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Details Section -->
                            <div class="mb-4">
                                <h5 class="mb-3" style="color: #1c467b; font-weight: 600;">Contact Details</h5>
                                
                                <!-- Dynamic Contact Fields -->
                                <div 
                                    v-for="(contact, index) in contacts" 
                                    :key="contact.id" 
                                    class="row mb-3"
                                >
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label :for="`contact_name_${contact.id}`" class="form-label">
                                                Name <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                :id="`contact_name_${contact.id}`"
                                                v-model="contact.name"
                                                type="text"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors[`contacts.${index}.name`] }"
                                                placeholder="Enter name"
                                                @blur="validateForm"
                                                required
                                            />
                                            <div v-if="errors[`contacts.${index}.name`]" class="invalid-feedback">
                                                {{ errors[`contacts.${index}.name`][0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label :for="`contact_mobile_${contact.id}`" class="form-label">
                                                Mobile <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                :id="`contact_mobile_${contact.id}`"
                                                v-model="contact.mobile"
                                                type="tel"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors[`contacts.${index}.mobile`] }"
                                                placeholder="Enter mobile number"
                                                @blur="validateForm"
                                                @input="validateForm"
                                                maxlength="10"
                                                required
                                            />
                                            <div v-if="errors[`contacts.${index}.mobile`]" class="invalid-feedback">
                                                {{ errors[`contacts.${index}.mobile`][0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label :for="`contact_email_${contact.id}`" class="form-label">Email</label>
                                            <input
                                                :id="`contact_email_${contact.id}`"
                                                v-model="contact.email"
                                                type="email"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors[`contacts.${index}.email`] }"
                                                placeholder="Enter email address"
                                                @blur="validateForm"
                                            />
                                            <div v-if="errors[`contacts.${index}.email`]" class="invalid-feedback">
                                                {{ errors[`contacts.${index}.email`][0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label :for="`contact_designation_${contact.id}`" class="form-label">Designation</label>
                                            <input
                                                :id="`contact_designation_${contact.id}`"
                                                v-model="contact.designation"
                                                type="text"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors[`contacts.${index}.designation`] }"
                                                placeholder="Enter designation"
                                            />
                                            <div v-if="errors[`contacts.${index}.designation`]" class="invalid-feedback">
                                                {{ errors[`contacts.${index}.designation`][0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group mb-0">
                                            <label class="form-label" style="visibility: hidden;">Action</label>
                                            <div class="d-flex gap-1">
                                                <button
                                                    v-if="index === 0"
                                                    type="button"
                                                    @click="addContact"
                                                    class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 38px; padding: 0; background-color: #1c467b; border: none; flex-shrink: 0; margin: 0;"
                                                    title="Add Contact"
                                                >
                                                    <Plus :size="18" color="white" style="display: block; margin: 0;" />
                                                </button>
                                                <button
                                                    v-if="index > 0"
                                                    type="button"
                                                    @click="removeContact(contact.id)"
                                                    class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 38px; padding: 0; border: none; flex-shrink: 0; margin: 0;"
                                                    title="Remove Contact"
                                                >
                                                    <X :size="18" color="white" style="display: block; margin: 0;" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Restricted User Field -->
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="restricted_user_ids" class="form-label">Restricted Users</label>
                                            <select
                                                id="restricted_user_ids"
                                                v-model="form.restricted_user_ids"
                                                class="form-control form-control-sm"
                                                :class="{ 'is-invalid': errors.restricted_user_ids }"
                                                :multiple="true"
                                            >
                                                <option 
                                                    v-for="user in users" 
                                                    :key="user.id" 
                                                    :value="user.id"
                                                >
                                                    {{ user.name }} ({{ user.email }})
                                                </option>
                                            </select>
                                            <div v-if="errors.restricted_user_ids" class="invalid-feedback">
                                                {{ errors.restricted_user_ids[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button
                                    type="button"
                                    @click="handleCancel"
                                    class="btn btn-warning btn-sm"
                                    style="background-color: #ff9800; color: white; border: none;"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm"
                                    :disabled="loading"
                                    style="background-color: #1c467b; color: white; border: none;"
                                >
                                    {{ loading ? 'Creating...' : 'Create Project' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.form-label {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 4px;
    color: #374151;
}

.form-control-sm {
    font-size: 14px;
    padding: 8px 12px;
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    font-size: 12px;
    color: #dc3545;
    margin-top: 4px;
}

.text-danger {
    color: #dc3545;
}
</style>

