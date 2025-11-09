<script setup lang="ts">
interface Category {
    id: number;
    name: string;
    is_active: boolean;
}

interface SubCategory {
    id: number;
    name: string;
    category_id: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    category: Category;
}

interface Props {
    form: any;
    categories: Category[];
    editingSubCategory: SubCategory | null;
    errors: Record<string, string[]>;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    submit: [];
    cancel: [];
}>();
</script>

<template>
    <div>
        <!-- Category Selection -->
        <div class="form-group mb-2">
            <label for="category_id" class="form-label mb-1" style="font-size: 14px;">
                Category
            </label>
            <select
                id="category_id"
                v-model="form.category_id"
                class="form-control form-control-sm"
                :class="{ 'is-invalid': errors.category_id }"
                required
            >
                <option value="">Select a category</option>
                <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                >
                    {{ category.name }}
                </option>
            </select>
            <div v-if="errors.category_id" class="invalid-feedback" style="font-size: 12px;">
                {{ errors.category_id[0] }}
            </div>
        </div>

        <!-- Sub Category Name -->
        <div class="form-group mb-2">
            <label for="name" class="form-label mb-1" style="font-size: 14px;">
                Sub Category Name
            </label>
            <input
                id="name"
                v-model="form.name"
                type="text"
                class="form-control form-control-sm"
                :class="{ 'is-invalid': errors.name }"
                placeholder="Enter sub category name"
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








