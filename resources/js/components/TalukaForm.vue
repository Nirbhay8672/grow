<script setup lang="ts">
interface District {
    id: number;
    name: string;
    state_id: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

interface Taluka {
    id: number;
    name: string;
    district_id: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    district: District;
}

interface Props {
    form: any;
    districts: District[];
    editingTaluka: Taluka | null;
    errors: Record<string, string[]>;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    submit: [];
    cancel: [];
}>();
</script>

<template>
    <div class="space-y-4">
        <!-- District Selection -->
        <div class="form-group">
            <label for="district_id" class="form-label">
                District
            </label>
            <select
                id="district_id"
                v-model="form.district_id"
                class="form-control"
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
            <div v-if="errors.district_id" class="invalid-feedback">
                {{ errors.district_id[0] }}
            </div>
        </div>

        <!-- Taluka Name -->
        <div class="form-group">
            <label for="name" class="form-label">
                Taluka Name
            </label>
            <input
                id="name"
                v-model="form.name"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.name }"
                placeholder="Enter taluka name"
                required
            />
            <div v-if="errors.name" class="invalid-feedback">
                {{ errors.name[0] }}
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

