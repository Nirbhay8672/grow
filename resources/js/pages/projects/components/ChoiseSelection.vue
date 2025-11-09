<script setup lang="ts">
import { ref, watch, onMounted, nextTick, computed } from 'vue';

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
        sub_category_id: string | string[];
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
        sub_category_id: string | string[];
    }];
}>();

// Check if Retail category is selected (ID 2) - allows multiple sub-categories
const isRetailCategory = computed(() => {
    return props.modelValue.category_id === '2';
});

// Handle sub-category selection (single or multiple based on category)
const selectedSubCategories = ref<string[]>([]);

// Initialize selected sub-categories from form value
watch(() => props.modelValue.sub_category_id, (newValue) => {
    if (isRetailCategory.value) {
        // For Retail, sub_category_id should be an array
        selectedSubCategories.value = Array.isArray(newValue) ? newValue : (newValue ? [newValue] : []);
    } else {
        // For other categories, it's a single value
        selectedSubCategories.value = newValue ? [String(newValue)] : [];
    }
}, { immediate: true });

const categories = ref<Category[]>([]);
const subCategories = ref<SubCategory[]>([]);

// Track if this is the initial mount to prevent clearing values on remount
const isInitialMount = ref(true);
// Track if we're currently initializing to prevent watchers from interfering
const isInitializing = ref(true);

// Initialize categories and subcategories from existing form values on mount
const initializeFromForm = () => {
    if (props.modelValue.construction_type_id) {
        const selectedConstructionType = props.constructionTypes.find(
            (ct) => ct.id === Number(props.modelValue.construction_type_id)
        );
        if (selectedConstructionType && selectedConstructionType.categories) {
            categories.value = selectedConstructionType.categories;
            
            // If category is already selected, load subcategories
            if (props.modelValue.category_id) {
                const selectedCategory = categories.value.find(
                    (cat) => cat.id === Number(props.modelValue.category_id)
                );
                if (selectedCategory && selectedCategory.sub_categories) {
                    subCategories.value = selectedCategory.sub_categories;
                }
            }
        }
    }
};

onMounted(async () => {
    // Set initializing flag to prevent watchers from interfering
    isInitializing.value = true;
    
    // Initialize categories and subcategories from form values
    initializeFromForm();
    
    // Wait for next tick to ensure initialization is complete
    await nextTick();
    
    // Mark that initialization is complete and watchers can now run
    isInitializing.value = false;
    // Mark that initial mount is complete after initialization
    isInitialMount.value = false;
});

// Watch construction type changes to load categories from relationship
watch(() => props.modelValue.construction_type_id, (newConstructionTypeId, oldConstructionTypeId) => {
    // Skip watcher during initialization to prevent clearing values on remount
    if (isInitializing.value) {
        return;
    }
    
    if (newConstructionTypeId) {
        const selectedConstructionType = props.constructionTypes.find(
            (ct) => ct.id === Number(newConstructionTypeId)
        );
        if (selectedConstructionType && selectedConstructionType.categories) {
            categories.value = selectedConstructionType.categories;
        } else {
            categories.value = [];
        }
        
        // Only clear category/subcategory if construction type actually changed by user
        // Don't clear on initial mount or when old value is undefined (remount)
        if (!isInitialMount.value && oldConstructionTypeId !== undefined && newConstructionTypeId !== oldConstructionTypeId) {
            emit('update:modelValue', {
                ...props.modelValue,
                category_id: '',
                sub_category_id: '',
            });
            subCategories.value = [];
        } else {
            // Restore subcategories if category still exists
            if (props.modelValue.category_id) {
                const selectedCategory = categories.value.find(
                    (cat) => cat.id === Number(props.modelValue.category_id)
                );
                if (selectedCategory && selectedCategory.sub_categories) {
                    subCategories.value = selectedCategory.sub_categories;
                } else {
                    subCategories.value = [];
                }
            }
        }
    } else {
        categories.value = [];
        // Only clear if not initial mount and value actually changed
        if (!isInitialMount.value && oldConstructionTypeId !== undefined) {
            emit('update:modelValue', {
                ...props.modelValue,
                category_id: '',
                sub_category_id: '',
            });
        }
        subCategories.value = [];
    }
}, { immediate: false });

// Watch category changes to load sub-categories from relationship
watch(() => props.modelValue.category_id, (newCategoryId, oldCategoryId) => {
    // Skip watcher during initialization to prevent clearing values on remount
    if (isInitializing.value) {
        return;
    }
    
    if (newCategoryId) {
        const selectedCategory = categories.value.find(
            (cat) => cat.id === Number(newCategoryId)
        );
        if (selectedCategory && selectedCategory.sub_categories) {
            subCategories.value = selectedCategory.sub_categories;
        } else {
            subCategories.value = [];
        }
        
        // Only clear subcategory if category actually changed by user
        // Don't clear on initial mount or when old value is undefined (remount)
        if (!isInitialMount.value && oldCategoryId !== undefined && newCategoryId !== oldCategoryId) {
            emit('update:modelValue', {
                ...props.modelValue,
                sub_category_id: '',
            });
        }
    } else {
        subCategories.value = [];
        // Only clear if not initial mount and value actually changed
        if (!isInitialMount.value && oldCategoryId !== undefined) {
            emit('update:modelValue', {
                ...props.modelValue,
                sub_category_id: '',
            });
        }
    }
    
    // Mark that initial mount is complete after first watch execution
    if (isInitialMount.value) {
        isInitialMount.value = false;
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
    if (isRetailCategory.value) {
        // For Retail, toggle the sub-category in the array
        const index = selectedSubCategories.value.indexOf(value);
        if (index > -1) {
            selectedSubCategories.value.splice(index, 1);
        } else {
            selectedSubCategories.value.push(value);
        }
        emit('update:modelValue', {
            ...props.modelValue,
            sub_category_id: [...selectedSubCategories.value],
        });
    } else {
        // For other categories, single selection
        emit('update:modelValue', {
            ...props.modelValue,
            sub_category_id: value,
        });
    }
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
                            :checked="isRetailCategory ? selectedSubCategories.includes(String(subCategory.id)) : modelValue.sub_category_id === String(subCategory.id)"
                            @change="updateSubCategory(String(subCategory.id))"
                            :type="isRetailCategory ? 'checkbox' : 'radio'"
                            :value="String(subCategory.id)"
                            class="form-check-input"
                            :class="{ 'is-invalid': errors.sub_category_id }"
                        />
                        <label 
                            :for="`sub_category_${subCategory.id}`"
                            class="form-check-label construction-type-label"
                        >
                            {{ subCategory.name }}
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

