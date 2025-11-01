<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { profile } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

interface State {
    id: number;
    name: string;
    code: string;
}

interface City {
    id: number;
    name: string;
    state_id: number;
}

interface User {
    id: number;
    name: string;
    username: string;
    first_name: string;
    middle_name?: string;
    last_name: string;
    mobile?: string;
    email: string;
    company_name?: string;
    birth_date?: string;
    state_id?: number;
    city_id?: number;
    state?: State;
    city?: City;
}

interface Props {
    user: User;
    states: State[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile',
        href: profile().url,
    },
];

const form = reactive({
    name: props.user.name || '',
    username: props.user.username || '',
    first_name: props.user.first_name || '',
    middle_name: props.user.middle_name || '',
    last_name: props.user.last_name || '',
    mobile: props.user.mobile || '',
    email: props.user.email || '',
    company_name: props.user.company_name || '',
    birth_date: props.user.birth_date || '',
    state_id: props.user.state_id || '',
    city_id: props.user.city_id || '',
    password: '',
    password_confirmation: '',
});

const cities = ref<City[]>([]);
const loadingCities = ref(false);
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const toast = ref({
    show: false,
    message: '',
    type: 'success' as 'success' | 'error',
});

// Format birth_date for date input (yyyy-MM-dd format)
const formattedBirthDate = computed({
    get: () => {
        if (!form.birth_date) return '';
        // If it's already in yyyy-MM-dd format, return as is
        if (typeof form.birth_date === 'string' && form.birth_date.match(/^\d{4}-\d{2}-\d{2}$/)) {
            return form.birth_date;
        }
        // If it's a full datetime string, extract just the date part
        if (typeof form.birth_date === 'string') {
            return form.birth_date.split('T')[0];
        }
        return '';
    },
    set: (value) => {
        form.birth_date = value;
    }
});

const loadCities = async (stateId: string | number) => {
    if (!stateId) return;
    
    loadingCities.value = true;
    try {
        const response = await axios.get(`/states/${stateId}/cities`);
        cities.value = response.data;
    } catch (error) {
        console.error('Error loading cities:', error);
        cities.value = [];
    } finally {
        loadingCities.value = false;
    }
};

// Load cities when state changes
const handleStateChange = async () => {
    if (form.state_id) {
        await loadCities(form.state_id);
        form.city_id = ''; // Clear city when state changes
    } else {
        cities.value = [];
        form.city_id = '';
    }
};

// Load cities on mount if user has a state
onMounted(async () => {
    if (props.user.state_id) {
        await loadCities(props.user.state_id);
    }
});

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};

    try {
        const formData: any = {
            name: form.name,
            username: form.username,
            first_name: form.first_name,
            last_name: form.last_name,
            middle_name: form.middle_name || null,
            mobile: form.mobile || null,
            email: form.email,
            company_name: form.company_name || null,
            birth_date: form.birth_date || null,
            state_id: form.state_id ? parseInt(form.state_id) : null,
            city_id: form.city_id ? parseInt(form.city_id) : null,
        };
        
        if (form.password) {
            formData.password = form.password;
            formData.password_confirmation = form.password_confirmation;
        }

        const response = await axios.put('/profile', formData);
        
        showToast('Profile updated successfully!');
        
        // Update the user data
        Object.assign(props.user, response.data);
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            showToast('Error updating profile. Please try again.', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const resetForm = () => {
    Object.assign(form, {
        name: props.user.name || '',
        username: props.user.username || '',
        first_name: props.user.first_name || '',
        middle_name: props.user.middle_name || '',
        last_name: props.user.last_name || '',
        mobile: props.user.mobile || '',
        email: props.user.email || '',
        company_name: props.user.company_name || '',
        birth_date: props.user.birth_date || '',
        state_id: props.user.state_id || '',
        city_id: props.user.city_id || '',
        password: '',
        password_confirmation: '',
    });
    errors.value = {};
};
</script>

<template>
    <Head title="Profile" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <h5 class="color-dark fw-500 mb-3" style="font-size: 1.25rem;">My Profile</h5>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2">
                        <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">Profile Information</h6>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="handleSubmit">
                            <div class="row">
                                <!-- Full Name -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.name }"
                                            placeholder="Enter full name"
                                            required
                                        />
                                        <div v-if="errors.name" class="invalid-feedback">
                                            {{ errors.name[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Username -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="username" class="form-label">Username</label>
                                        <input
                                            id="username"
                                            v-model="form.username"
                                            type="text"
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.username }"
                                            placeholder="Enter username"
                                            required
                                        />
                                        <div v-if="errors.username" class="invalid-feedback">
                                            {{ errors.username[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- First Name -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input
                                            id="first_name"
                                            v-model="form.first_name"
                                            type="text"
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.first_name }"
                                            placeholder="Enter first name"
                                            required
                                        />
                                        <div v-if="errors.first_name" class="invalid-feedback">
                                            {{ errors.first_name[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input
                                            id="last_name"
                                            v-model="form.last_name"
                                            type="text"
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.last_name }"
                                            placeholder="Enter last name"
                                            required
                                        />
                                        <div v-if="errors.last_name" class="invalid-feedback">
                                            {{ errors.last_name[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Middle Name -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="middle_name" class="form-label">Middle Name</label>
                                        <input
                                            id="middle_name"
                                            v-model="form.middle_name"
                                            type="text"
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.middle_name }"
                                            placeholder="Enter middle name (optional)"
                                        />
                                        <div v-if="errors.middle_name" class="invalid-feedback">
                                            {{ errors.middle_name[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email</label>
                                        <input
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.email }"
                                            placeholder="Enter email"
                                            @input="form.email = $event.target.value.toLowerCase()"
                                            required
                                        />
                                        <div v-if="errors.email" class="invalid-feedback">
                                            {{ errors.email[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Mobile -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="mobile" class="form-label">Mobile</label>
                                        <input
                                            id="mobile"
                                            v-model="form.mobile"
                                            type="text"
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.mobile }"
                                            placeholder="Enter mobile number (optional)"
                                        />
                                        <div v-if="errors.mobile" class="invalid-feedback">
                                            {{ errors.mobile[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Company Name -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="company_name" class="form-label">Company Name</label>
                                        <input
                                            id="company_name"
                                            v-model="form.company_name"
                                            type="text"
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.company_name }"
                                            placeholder="Enter company name (optional)"
                                        />
                                        <div v-if="errors.company_name" class="invalid-feedback">
                                            {{ errors.company_name[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Birth Date -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="birth_date" class="form-label">Birth Date</label>
                                        <input
                                            id="birth_date"
                                            v-model="formattedBirthDate"
                                            type="date"
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.birth_date }"
                                        />
                                        <div v-if="errors.birth_date" class="invalid-feedback">
                                            {{ errors.birth_date[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- State Selection -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="state_id" class="form-label">State</label>
                                        <select
                                            id="state_id"
                                            v-model="form.state_id"
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.state_id }"
                                            @change="handleStateChange"
                                        >
                                            <option value="">Select a state</option>
                                            <option
                                                v-for="state in states"
                                                :key="state.id"
                                                :value="state.id"
                                            >
                                                {{ state.name }} ({{ state.code }})
                                            </option>
                                        </select>
                                        <div v-if="errors.state_id" class="invalid-feedback">
                                            {{ errors.state_id[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- City Selection (Dependent on State) -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="city_id" class="form-label">City</label>
                                        <select
                                            id="city_id"
                                            v-model="form.city_id"
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.city_id }"
                                            :disabled="!form.state_id || loadingCities"
                                        >
                                            <option value="">Select a city</option>
                                            <option
                                                v-for="city in cities"
                                                :key="city.id"
                                                :value="city.id"
                                            >
                                                {{ city.name }}
                                            </option>
                                        </select>
                                        <div v-if="loadingCities" class="form-text">Loading cities...</div>
                                        <div v-if="errors.city_id" class="invalid-feedback">
                                            {{ errors.city_id[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="password" class="form-label">New Password</label>
                                        <input
                                            id="password"
                                            v-model="form.password"
                                            type="password"
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.password }"
                                            placeholder="Enter new password (optional)"
                                        />
                                        <div v-if="errors.password" class="invalid-feedback">
                                            {{ errors.password[0] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Password Confirmation -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                        <input
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            type="password"
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.password_confirmation }"
                                            placeholder="Confirm new password"
                                        />
                                        <div v-if="errors.password_confirmation" class="invalid-feedback">
                                            {{ errors.password_confirmation[0] }}
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    @click="resetForm"
                                >
                                    Reset
                                </button>
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    :disabled="loading"
                                >
                                    <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                                    {{ loading ? 'Updating...' : 'Update Profile' }}
                                </button>
                            </div>
                        </form>
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
</style>
