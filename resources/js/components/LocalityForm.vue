<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';

interface State {
    id: number;
    name: string;
    code: string;
    is_active: boolean;
}

interface City {
    id: number;
    name: string;
    state_id: number;
    is_active: boolean;
}

interface Locality {
    id: number;
    name: string;
    state_id: number;
    city_id: number;
    zip_code: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    state: State;
    city: City;
}

interface Props {
    form: any;
    states: State[];
    editingLocality: Locality | null;
    errors: Record<string, string[]>;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    submit: [];
    cancel: [];
}>();

const cities = ref<City[]>([]);

watch(() => props.form.state_id, async (stateId) => {
    if (stateId) {
        try {
            const response = await axios.get(`/states/${stateId}/cities`);
            cities.value = response.data;
        } catch (error) {
            console.error('Error fetching cities:', error);
            cities.value = [];
        }
    } else {
        cities.value = [];
        props.form.city_id = '';
    }
}, { immediate: true });

if (props.editingLocality) {
    axios.get(`/states/${props.editingLocality.state_id}/cities`).then(response => {
        cities.value = response.data;
    });
}
</script>

<template>
    <div class="space-y-4">
        <!-- State Selection -->
        <div class="form-group">
            <label for="state_id" class="form-label">
                State
            </label>
            <select
                id="state_id"
                v-model="form.state_id"
                class="form-control"
                :class="{ 'is-invalid': errors.state_id }"
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
            <div v-if="errors.state_id" class="invalid-feedback">
                {{ errors.state_id[0] }}
            </div>
        </div>

        <!-- City Selection -->
        <div class="form-group">
            <label for="city_id" class="form-label">
                City
            </label>
            <select
                id="city_id"
                v-model="form.city_id"
                class="form-control"
                :class="{ 'is-invalid': errors.city_id }"
                :disabled="!form.state_id"
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
            <div v-if="errors.city_id" class="invalid-feedback">
                {{ errors.city_id[0] }}
            </div>
        </div>

        <!-- Locality Name -->
        <div class="form-group">
            <label for="name" class="form-label">
                Locality Name
            </label>
            <input
                id="name"
                v-model="form.name"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.name }"
                placeholder="Enter locality name"
                required
            />
            <div v-if="errors.name" class="invalid-feedback">
                {{ errors.name[0] }}
            </div>
        </div>

        <!-- Zip Code -->
        <div class="form-group">
            <label for="zip_code" class="form-label">
                Zip Code
            </label>
            <input
                id="zip_code"
                v-model="form.zip_code"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.zip_code }"
                placeholder="Enter zip code"
                required
            />
            <div v-if="errors.zip_code" class="invalid-feedback">
                {{ errors.zip_code[0] }}
            </div>
        </div>

        <!-- Active Status -->
        <div class="form-group">
            <div class="form-check">
                <input
                    id="is_active"
                    v-model="form.is_active"
                    type="checkbox"
                    class="form-check-input"
                />
                <label for="is_active" class="form-check-label">
                    Active
                </label>
            </div>
        </div>
    </div>
</template>

