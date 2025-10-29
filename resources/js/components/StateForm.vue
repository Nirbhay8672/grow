<script setup lang="ts">
interface State {
    id: number;
    name: string;
    code: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    form: any;
    editingState: State | null;
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
        <!-- State Name -->
        <div class="form-group">
            <label for="name" class="form-label">
                State Name
            </label>
            <input
                id="name"
                v-model="form.name"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.name }"
                placeholder="Enter state name"
                required
            />
            <div v-if="errors.name" class="invalid-feedback">
                {{ errors.name[0] }}
            </div>
        </div>

        <!-- State Code -->
        <div class="form-group">
            <label for="code" class="form-label">
                State Code
            </label>
            <input
                id="code"
                v-model="form.code"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.code }"
                placeholder="Enter state code (e.g., CA, NY)"
                maxlength="3"
                required
            />
            <div v-if="errors.code" class="invalid-feedback">
                {{ errors.code[0] }}
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


