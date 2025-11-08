<script setup lang="ts">
import { ref, watch } from 'vue';

interface ConstructionType {
    id: number;
    name: string;
    categories?: Category[];
}

interface Category {
    id: number;
    name: string;
    sub_categories?: SubCategory[];
}

interface SubCategory {
    id: number;
    name: string;
}

interface Props {
    constructionTypes: ConstructionType[];
    modelValue: {
        construction_type_id: string;
        category_id: string;
        sub_category_id: string;
    };
    errors?: Record<string, string[]>;
    showIds?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    errors: () => ({}),
    showIds: false,
});

const emit = defineEmits<{
    'update:modelValue': [value: {
        construction_type_id: string;
        category_id: string;
        sub_category_id: string;
    }];
}>();

const categories = ref<Category[]>([]);
const subCategories = ref<SubCategory[]>([]);

// Watch construction type changes to load categories from relationship
watch(() => props.modelValue.construction_type_id, (newConstructionTypeId) => {
    if (newConstructionTypeId) {
        const selectedConstructionType = props.constructionTypes.find(
            (ct) => ct.id === Number(newConstructionTypeId)
        );
        if (selectedConstructionType && selectedConstructionType.categories) {
            categories.value = selectedConstructionType.categories;
        } else {
            categories.value = [];
        }
        emit('update:modelValue', {
            ...props.modelValue,
            category_id: '',
            sub_category_id: '',
        });
        subCategories.value = [];
    } else {
        categories.value = [];
        emit('update:modelValue', {
            ...props.modelValue,
            category_id: '',
            sub_category_id: '',
        });
        subCategories.value = [];
    }
}, { immediate: false });

// Watch category changes to load sub-categories from relationship
watch(() => props.modelValue.category_id, (newCategoryId) => {
    if (newCategoryId) {
        const selectedCategory = categories.value.find(
            (cat) => cat.id === Number(newCategoryId)
        );
        if (selectedCategory && selectedCategory.sub_categories) {
            subCategories.value = selectedCategory.sub_categories;
        } else {
            subCategories.value = [];
        }
        emit('update:modelValue', {
            ...props.modelValue,
            sub_category_id: '',
        });
    } else {
        subCategories.value = [];
        emit('update:modelValue', {
            ...props.modelValue,
            sub_category_id: '',
        });
    }
}, { immediate: false });

const updateConstructionType = (value: string) => {
    emit('update:modelValue', {
        ...props.modelValue,
        construction_type_id: value,
    });
};

const updateCategory = (value: string) => {
    emit('update:modelValue', {
        ...props.modelValue,
        category_id: value,
    });
};

const updateSubCategory = (value: string) => {
    emit('update:modelValue', {
        ...props.modelValue,
        sub_category_id: value,
    });
};
</script>

<template>
    <div class="choise-selection-container">
        <!-- Construction Type Selection -->
        <div class="mb-4">
            <h5 class="mb-3 section-title">Property Type</h5>
            <div class="form-group">
                <div class="d-flex flex-wrap gap-3">
                    <div 
                        v-for="constructionType in constructionTypes" 
                        :key="constructionType.id"
                        class="form-check form-check-inline construction-type-radio"
                    >
                        <input
                            :id="`construction_type_${constructionType.id}`"
                            :checked="modelValue.construction_type_id === String(constructionType.id)"
                            @change="updateConstructionType(String(constructionType.id))"
                            type="radio"
                            :value="String(constructionType.id)"
                            class="form-check-input"
                            :class="{ 'is-invalid': errors.construction_type_id }"
                        />
                        <label 
                            :for="`construction_type_${constructionType.id}`"
                            class="form-check-label construction-type-label"
                        >
                            {{ constructionType.name }}
                            <span v-if="showIds">(ID: {{ constructionType.id }})</span>
                        </label>
                    </div>
                </div>
                <div v-if="errors.construction_type_id" class="invalid-feedback d-block">
                    {{ errors.construction_type_id[0] }}
                </div>
            </div>
        </div>

        <!-- Category Selection (shown after construction type is selected) -->
        <div v-if="modelValue.construction_type_id" class="mb-4">
            <h5 class="mb-3 section-title">Category</h5>
            <div v-if="categories.length === 0" class="text-muted mb-3">
                Loading categories...
            </div>
            <div v-else-if="categories.length > 0" class="form-group">
                <div class="d-flex flex-wrap gap-3">
                    <div 
                        v-for="category in categories" 
                        :key="category.id"
                        class="form-check form-check-inline construction-type-radio"
                    >
                        <input
                            :id="`category_${category.id}`"
                            :checked="modelValue.category_id === String(category.id)"
                            @change="updateCategory(String(category.id))"
                            type="radio"
                            :value="String(category.id)"
                            class="form-check-input"
                            :class="{ 'is-invalid': errors.category_id }"
                        />
                        <label 
                            :for="`category_${category.id}`"
                            class="form-check-label construction-type-label"
                        >
                            {{ category.name }}
                            <span v-if="showIds">(ID: {{ category.id }})</span>
                        </label>
                    </div>
                </div>
                <div v-if="errors.category_id" class="invalid-feedback d-block">
                    {{ errors.category_id[0] }}
                </div>
            </div>
            <div v-else class="text-muted mb-3">
                No categories found for this construction type.
            </div>
        </div>

        <!-- Sub-Category Selection (shown after category is selected, only if sub-categories exist) -->
        <div v-if="modelValue.category_id && subCategories.length > 0" class="mb-4">
            <h5 class="mb-3 section-title">Sub Category</h5>
            <div class="form-group">
                <div class="d-flex flex-wrap gap-3">
                    <div 
                        v-for="subCategory in subCategories" 
                        :key="subCategory.id"
                        class="form-check form-check-inline construction-type-radio"
                    >
                        <input
                            :id="`sub_category_${subCategory.id}`"
                            :checked="modelValue.sub_category_id === String(subCategory.id)"
                            @change="updateSubCategory(String(subCategory.id))"
                            type="radio"
                            :value="String(subCategory.id)"
                            class="form-check-input"
                            :class="{ 'is-invalid': errors.sub_category_id }"
                        />
                        <label 
                            :for="`sub_category_${subCategory.id}`"
                            class="form-check-label construction-type-label"
                        >
                            {{ subCategory.name }}
                            <span v-if="showIds">(ID: {{ subCategory.id }})</span>
                        </label>
                    </div>
                </div>
                <div v-if="errors.sub_category_id" class="invalid-feedback d-block">
                    {{ errors.sub_category_id[0] }}
                </div>
            </div>
        </div>
    </div>
</template>

