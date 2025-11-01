<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';

interface District {
    id: number;
    name: string;
    state_id: number;
    is_active: boolean;
}

interface Taluka {
    id: number;
    name: string;
    district_id: number;
    is_active: boolean;
}

interface Village {
    id: number;
    name: string;
    district_id: number;
    taluka_id: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    district: District;
    taluka: Taluka;
}

interface Props {
    form: any;
    districts: District[];
    editingVillage: Village | null;
    errors: Record<string, string[]>;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    submit: [];
    cancel: [];
}>();

const talukas = ref<Taluka[]>([]);
const loadingTalukas = ref(false);

// Watch for district changes and load talukas
watch(() => props.form.district_id, async (newDistrictId) => {
    if (newDistrictId) {
        await loadTalukas(parseInt(newDistrictId));
    } else {
        talukas.value = [];
        props.form.taluka_id = '';
    }
}, { immediate: true });

// Load talukas for the selected district
const loadTalukas = async (districtId: number) => {
    if (!districtId) {
        talukas.value = [];
        return;
    }

    loadingTalukas.value = true;
    try {
        const response = await axios.get(`/districts/${districtId}/talukas`);
        talukas.value = response.data;
        
        // Reset taluka_id if current selection is not in the new list
        if (props.form.taluka_id) {
            const talukaExists = talukas.value.some(t => t.id === parseInt(props.form.taluka_id));
            if (!talukaExists) {
                props.form.taluka_id = '';
            }
        }
    } catch (error) {
        console.error('Error loading talukas:', error);
        talukas.value = [];
    } finally {
        loadingTalukas.value = false;
    }
};

// If editing and district is set, load talukas on mount
if (props.editingVillage && props.form.district_id) {
    loadTalukas(parseInt(props.form.district_id));
}
</script>

<template>
    <div>
        <!-- District Selection -->
        <div class="form-group mb-2">
            <label for="district_id" class="form-label mb-1" style="font-size: 14px;">
                District
            </label>
            <select
                id="district_id"
                v-model="form.district_id"
                class="form-control form-control-sm"
                :class="{ 'is-invalid': errors.district_id }"
                required
            >
                <option value="">Select a district</option>
                <option
                    v-for="district in districts"
                    :key="district.id"
                    :value="district.id"
                >
                    {{ district.name }}
                </option>
            </select>
            <div v-if="errors.district_id" class="invalid-feedback" style="font-size: 12px;">
                {{ errors.district_id[0] }}
            </div>
        </div>

        <!-- Taluka Selection -->
        <div class="form-group mb-2">
            <label for="taluka_id" class="form-label mb-1" style="font-size: 14px;">
                Taluka
            </label>
            <select
                id="taluka_id"
                v-model="form.taluka_id"
                class="form-control form-control-sm"
                :class="{ 'is-invalid': errors.taluka_id }"
                :disabled="!form.district_id || loadingTalukas"
                required
            >
                <option value="">{{ form.district_id ? (loadingTalukas ? 'Loading...' : 'Select a taluka') : 'Select district first' }}</option>
                <option
                    v-for="taluka in talukas"
                    :key="taluka.id"
                    :value="taluka.id"
                >
                    {{ taluka.name }}
                </option>
            </select>
            <div v-if="errors.taluka_id" class="invalid-feedback" style="font-size: 12px;">
                {{ errors.taluka_id[0] }}
            </div>
        </div>

        <!-- Village Name -->
        <div class="form-group mb-2">
            <label for="name" class="form-label mb-1" style="font-size: 14px;">
                Village Name
            </label>
            <input
                id="name"
                v-model="form.name"
                type="text"
                class="form-control form-control-sm"
                :class="{ 'is-invalid': errors.name }"
                placeholder="Enter village name"
                required
            />
            <div v-if="errors.name" class="invalid-feedback" style="font-size: 12px;">
                {{ errors.name[0] }}
            </div>
        </div>

        <!-- Active Status -->
        <div class="form-group mb-0">
            <div class="form-check">
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
</template>

