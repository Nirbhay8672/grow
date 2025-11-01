<script setup lang="ts">
import { ref, watch, nextTick, computed } from 'vue';
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
    is_active: boolean;
}

interface Props {
    form: any;
    states: State[];
    editingUser: User | null;
    errors: Record<string, string[]>;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    submit: [];
    cancel: [];
}>();

const cities = ref<City[]>([]);
const loadingCities = ref(false);

// Format birth_date for date input (yyyy-MM-dd format)
const formattedBirthDate = computed({
    get: () => {
        if (!props.form.birth_date) return '';
        // If it's already in yyyy-MM-dd format, return as is
        if (typeof props.form.birth_date === 'string' && props.form.birth_date.match(/^\d{4}-\d{2}-\d{2}$/)) {
            return props.form.birth_date;
        }
        // If it's a full datetime string, extract just the date part
        if (typeof props.form.birth_date === 'string') {
            return props.form.birth_date.split('T')[0];
        }
        return '';
    },
    set: (value) => {
        props.form.birth_date = value;
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

// Load cities when editing user changes
watch(() => props.editingUser, async (user) => {
    if (user && user.state_id) {
        await loadCities(user.state_id);
    }
}, { immediate: true });

// Set city_id when cities are loaded and we have an editing user
watch([cities, () => props.editingUser], async ([citiesList, user]) => {
    if (citiesList.length > 0 && user && user.city_id && !props.form.city_id) {
        // Use nextTick to ensure the DOM is updated with the new cities options
        await nextTick();
        props.form.city_id = user.city_id;
    }
}, { immediate: true });

// Load cities when state changes in form
watch(() => props.form.state_id, async (newStateId, oldStateId) => {
    if (newStateId) {
        // Only load if state actually changed (not initial set)
        if (newStateId !== oldStateId && oldStateId !== undefined && oldStateId !== null && oldStateId !== '') {
            await loadCities(newStateId);
            props.form.city_id = ''; // Clear city when state changes
        } else if (!cities.value.length) {
            // Load cities if state_id exists but cities haven't been loaded yet
            await loadCities(newStateId);
        }
    } else {
        cities.value = [];
        props.form.city_id = '';
    }
}, { immediate: true });
</script>

<template>
    <div>
        <div class="row">
            <!-- Full Name -->
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="name" class="form-label mb-1" style="font-size: 14px;">Full Name <span class="text-danger">*</span></label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.name }"
                        placeholder="Enter full name"
                        required
                    />
                    <div v-if="errors.name" class="invalid-feedback" style="font-size: 12px;">
                        {{ errors.name[0] }}
                    </div>
                </div>
            </div>

            <!-- Username -->
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="username" class="form-label mb-1" style="font-size: 14px;">Username <span class="text-danger">*</span></label>
                    <input
                        id="username"
                        v-model="form.username"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.username }"
                        placeholder="Enter username"
                        required
                    />
                    <div v-if="errors.username" class="invalid-feedback" style="font-size: 12px;">
                        {{ errors.username[0] }}
                    </div>
                </div>
            </div>

            <!-- First Name -->
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="first_name" class="form-label mb-1" style="font-size: 14px;">First Name <span class="text-danger">*</span></label>
                    <input
                        id="first_name"
                        v-model="form.first_name"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.first_name }"
                        placeholder="Enter first name"
                        required
                    />
                    <div v-if="errors.first_name" class="invalid-feedback" style="font-size: 12px;">
                        {{ errors.first_name[0] }}
                    </div>
                </div>
            </div>

            <!-- Last Name -->
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="last_name" class="form-label mb-1" style="font-size: 14px;">Last Name <span class="text-danger">*</span></label>
                    <input
                        id="last_name"
                        v-model="form.last_name"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.last_name }"
                        placeholder="Enter last name"
                        required
                    />
                    <div v-if="errors.last_name" class="invalid-feedback" style="font-size: 12px;">
                        {{ errors.last_name[0] }}
                    </div>
                </div>
            </div>

            <!-- Middle Name -->
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="middle_name" class="form-label mb-1" style="font-size: 14px;">Middle Name</label>
                    <input
                        id="middle_name"
                        v-model="form.middle_name"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.middle_name }"
                        placeholder="Enter middle name (optional)"
                    />
                    <div v-if="errors.middle_name" class="invalid-feedback" style="font-size: 12px;">
                        {{ errors.middle_name[0] }}
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="email" class="form-label mb-1" style="font-size: 14px;">Email <span class="text-danger">*</span></label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.email }"
                        placeholder="Enter email"
                        @input="form.email = $event.target.value.toLowerCase()"
                        required
                    />
                    <div v-if="errors.email" class="invalid-feedback" style="font-size: 12px;">
                        {{ errors.email[0] }}
                    </div>
                </div>
            </div>

            <!-- Mobile -->
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="mobile" class="form-label mb-1" style="font-size: 14px;">Mobile</label>
                    <input
                        id="mobile"
                        v-model="form.mobile"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.mobile }"
                        placeholder="Enter mobile number (optional)"
                    />
                    <div v-if="errors.mobile" class="invalid-feedback" style="font-size: 12px;">
                        {{ errors.mobile[0] }}
                    </div>
                </div>
            </div>

            <!-- Company Name -->
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="company_name" class="form-label mb-1" style="font-size: 14px;">Company Name</label>
                    <input
                        id="company_name"
                        v-model="form.company_name"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.company_name }"
                        placeholder="Enter company name (optional)"
                    />
                    <div v-if="errors.company_name" class="invalid-feedback" style="font-size: 12px;">
                        {{ errors.company_name[0] }}
                    </div>
                </div>
            </div>

            <!-- Birth Date -->
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="birth_date" class="form-label mb-1" style="font-size: 14px;">Birth Date</label>
                    <input
                        id="birth_date"
                        v-model="formattedBirthDate"
                        type="date"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.birth_date }"
                    />
                    <div v-if="errors.birth_date" class="invalid-feedback" style="font-size: 12px;">
                        {{ errors.birth_date[0] }}
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="password" class="form-label mb-1" style="font-size: 14px;">
                        Password <span v-if="!editingUser" class="text-danger">*</span>
                    </label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.password }"
                        :placeholder="editingUser ? 'Enter new password (optional)' : 'Enter password'"
                        :required="!editingUser"
                    />
                    <div v-if="errors.password" class="invalid-feedback" style="font-size: 12px;">
                        {{ errors.password[0] }}
                    </div>
                </div>
            </div>

            <!-- State Selection -->
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="state_id" class="form-label mb-1" style="font-size: 14px;">State <span class="text-danger">*</span></label>
                    <select
                        id="state_id"
                        v-model="form.state_id"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.state_id }"
                        @change="form.city_id = ''"
                        required
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
                    <div v-if="errors.state_id" class="invalid-feedback" style="font-size: 12px;">
                        {{ errors.state_id[0] }}
                    </div>
                </div>
            </div>

            <!-- City Selection (Dependent on State) -->
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="city_id" class="form-label mb-1" style="font-size: 14px;">City <span class="text-danger">*</span></label>
                    <select
                        id="city_id"
                        v-model="form.city_id"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.city_id }"
                        :disabled="!form.state_id || loadingCities"
                        required
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
                    <div v-if="loadingCities" class="form-text" style="font-size: 12px;">Loading cities...</div>
                    <div v-if="errors.city_id" class="invalid-feedback" style="font-size: 12px;">
                        {{ errors.city_id[0] }}
                    </div>
                </div>
            </div>

            <!-- Active Status -->
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label class="form-label mb-1" style="font-size: 14px;">Status</label>
                    <div class="form-check mt-2">
                        <input
                            id="is_active"
                            v-model="form.is_active"
                            type="checkbox"
                            class="form-check-input"
                            style="width: 16px; height: 16px;"
                        />
                        <label for="is_active" class="form-check-label ms-2" style="font-size: 14px;">
                            Active
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

