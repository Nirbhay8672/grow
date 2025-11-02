<script setup lang="ts">
interface ConstructionType {
    id: number;
    name: string;
    is_active: boolean;
}

interface Category {
    id: number;
    name: string;
    is_active: boolean;
    user_id: number;
    construction_type_id: number | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    form: any;
    editingCategory: Category | null;
    errors: Record<string, string[]>;
    constructionTypes: ConstructionType[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    submit: [];
    cancel: [];
}>();
</script>

<template>
    <div>
        <!-- Construction Type Selection -->
        <div class="form-group mb-2">
            <label for="construction_type_id" class="form-label mb-1" style="font-size: 14px;">
                Construction Type
            </label>
            <select
                id="construction_type_id"
                v-model="form.construction_type_id"
                class="form-control form-control-sm"
                :class="{ 'is-invalid': errors.construction_type_id }"
            >
                <option :value="null">Select a construction type</option>
                <option
                    v-for="constructionType in constructionTypes"
                    :key="constructionType.id"
                    :value="constructionType.id"
                >
                    {{ constructionType.name }}
                </option>
            </select>
            <div v-if="errors.construction_type_id" class="invalid-feedback" style="font-size: 12px;">
                {{ errors.construction_type_id[0] }}
            </div>
        </div>

        <!-- Category Name -->
        <div class="form-group mb-2">
            <label for="name" class="form-label mb-1" style="font-size: 14px;">
                Category Name
            </label>
            <input
                id="name"
                v-model="form.name"
                type="text"
                class="form-control form-control-sm"
                :class="{ 'is-invalid': errors.name }"
                placeholder="Enter category name"
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


