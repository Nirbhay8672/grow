<script setup lang="ts">
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
    created_at: string;
    updated_at: string;
    state: State;
}

interface Props {
    form: any;
    states: State[];
    editingCity: City | null;
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

        <!-- City Name -->
        <div class="form-group">
            <label for="name" class="form-label">
                City Name
            </label>
            <input
                id="name"
                v-model="form.name"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.name }"
                placeholder="Enter city name"
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


