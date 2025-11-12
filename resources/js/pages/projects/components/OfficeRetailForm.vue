<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue';
import { Plus, X } from 'lucide-vue-next';

interface Props {
    form: any;
    errors: any;
    measurementUnits: any[];
    officeSubCategories: any[];
    retailSubCategories: any[];
}

const props = defineProps<Props>();

// Filter measurement units for height/width fields (only Ft. and Mt.)
const heightWidthUnits = computed(() => {
    return props.measurementUnits.filter(unit => 
        unit.name === 'Ft.' || unit.name === 'Mt.'
    );
});

// Filter measurement units for area fields (all except Ft. and Mt.)
const areaUnits = computed(() => {
    return props.measurementUnits.filter(unit => 
        unit.name !== 'Ft.' && unit.name !== 'Mt.'
    );
});

// Office section data structure
interface OfficeData {
    office_sub_category_id: string;
    no_of_towers: string;
    no_of_floors: string;
    no_of_unit_each_tower: string;
    no_of_lift: string;
    front_road_width: string;
    front_road_width_unit_id: string;
    washroom: string;
    two_road_corner: boolean;
    tower_name: string;
    total_units: string;
    saleable_from: string;
    saleable_to: string;
    saleable_unit_id: string;
    show_carpet_area: boolean;
    carpet_area_from: string;
    carpet_area_to: string;
    carpet_area_unit_id: string;
    show_builtup_area: boolean;
    builtup_area_from: string;
    builtup_area_to: string;
    builtup_area_unit_id: string;
}

// Retail unit details structure
interface RetailUnitDetail {
    id: number;
    tower_name: string;
    sub_category_id: string;
    size_from: string;
    size_to: string;
    size_unit_id: string;
    front_opening: string;
    front_opening_unit_id: string;
    no_of_unit_each_floor: string;
    ceiling_height: string;
    ceiling_height_unit_id: string;
}

// Initialize office data
const initializeOfficeData = () => {
    if (!props.form.office_retail_data) {
        props.form.office_retail_data = {
            office_sub_category_id: '',
            no_of_towers: '',
            no_of_floors: '',
            no_of_unit_each_tower: '',
            no_of_lift: '',
            front_road_width: '',
            front_road_width_unit_id: '',
            washroom: 'Private',
            two_road_corner: false,
            tower_name: '',
            total_units: '',
            saleable_from: '',
            saleable_to: '',
            saleable_unit_id: '',
            show_carpet_area: false,
            carpet_area_from: '',
            carpet_area_to: '',
            carpet_area_unit_id: '',
            show_builtup_area: false,
            builtup_area_from: '',
            builtup_area_to: '',
            builtup_area_unit_id: '',
        };
    }
};

// Initialize retail unit details
const initializeRetailUnitDetails = () => {
    if (!props.form.office_retail_retail_unit_details || props.form.office_retail_retail_unit_details.length === 0) {
        props.form.office_retail_retail_unit_details = [];
    }
};

// Initialize on component mount
initializeOfficeData();
initializeRetailUnitDetails();

// Get retail sub-category name by ID
const getRetailSubCategoryName = (subCategoryId: string | null) => {
    if (!subCategoryId) return '';
    const subCat = props.retailSubCategories.find(sc => String(sc.id) === String(subCategoryId));
    return subCat ? subCat.name.toUpperCase() : '';
};

// Track selected retail sub-categories
const selectedRetailSubCategories = ref<string[]>([]);

// Flag to prevent circular updates
let isInternalUpdate = false;

// Initialize retail sub-categories from existing data (for edit mode)
if (props.form.office_retail_retail_unit_details && props.form.office_retail_retail_unit_details.length > 0) {
    const subCatIds = props.form.office_retail_retail_unit_details
        .map((detail: any) => detail.sub_category_id ? String(detail.sub_category_id) : null)
        .filter((id: string | null) => id !== null);
    selectedRetailSubCategories.value = [...new Set(subCatIds)] as string[];
}

// Update retail unit details when sub-categories change
const updateRetailUnitDetails = () => {
    // Ensure the array exists
    if (!props.form.office_retail_retail_unit_details) {
        props.form.office_retail_retail_unit_details = [];
    }
    
    const currentDetails = props.form.office_retail_retail_unit_details || [];
    const currentSubCategoryIds = currentDetails.map((detail: any) => String(detail.sub_category_id));
    
    // Add new unit details for newly selected sub-categories
    selectedRetailSubCategories.value.forEach((subCatId: string) => {
        if (!currentSubCategoryIds.includes(String(subCatId))) {
            props.form.office_retail_retail_unit_details.push({
                id: Date.now() + Math.random(),
                tower_name: '',
                sub_category_id: subCatId,
                size_from: '',
                size_to: '',
                size_unit_id: '',
                front_opening: '',
                front_opening_unit_id: '',
                no_of_unit_each_floor: '',
                ceiling_height: '',
                ceiling_height_unit_id: '',
            });
        }
    });
    
    // Remove unit details for unselected sub-categories
    props.form.office_retail_retail_unit_details = props.form.office_retail_retail_unit_details.filter(
        (detail: any) => selectedRetailSubCategories.value.includes(String(detail.sub_category_id))
    );
};

let retailUnitIdCounter = 1;

// Toggle carpet area
const toggleCarpetArea = () => {
    props.form.office_retail_data.show_carpet_area = !props.form.office_retail_data.show_carpet_area;
    if (!props.form.office_retail_data.show_carpet_area) {
        props.form.office_retail_data.carpet_area_from = '';
        props.form.office_retail_data.carpet_area_to = '';
        props.form.office_retail_data.carpet_area_unit_id = '';
    }
};

// Toggle builtup area
const toggleBuiltupArea = () => {
    props.form.office_retail_data.show_builtup_area = !props.form.office_retail_data.show_builtup_area;
    if (!props.form.office_retail_data.show_builtup_area) {
        props.form.office_retail_data.builtup_area_from = '';
        props.form.office_retail_data.builtup_area_to = '';
        props.form.office_retail_data.builtup_area_unit_id = '';
    }
};

const handleIntegerInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const value = target.value;
    const integerValue = value.replace(/[^0-9]/g, '');
    
    if (value !== integerValue) {
        target.value = integerValue;
    }
};

// Handle Office sub-category selection (Category 1)
const handleOfficeSubCategorySelect = (subCatId: string) => {
    // Set the office sub-category
    props.form.office_retail_data.office_sub_category_id = subCatId;
    
    // Update form sub_category_id to include office sub-category
    // Preserve retail sub-categories (Category 2) if any
    const currentSubCategories = Array.isArray(props.form.sub_category_id) 
        ? [...props.form.sub_category_id] 
        : (props.form.sub_category_id ? [props.form.sub_category_id] : []);
    
    // Remove office sub-categories (Category 1) first, but keep retail sub-categories (Category 2)
    const filtered = currentSubCategories.filter(id => {
        // Keep retail sub-categories (Category 2)
        if (props.retailSubCategories.some(subCat => String(subCat.id) === String(id))) {
            return true;
        }
        // Remove office sub-categories
        return !props.officeSubCategories.some(subCat => String(subCat.id) === String(id));
    });
    
    // Add selected office sub-category
    props.form.sub_category_id = [...filtered, subCatId];
};

// Toggle retail sub-category pill button
const toggleRetailSubCategoryPill = (subCatId: string) => {
    isInternalUpdate = true;
    
    const index = selectedRetailSubCategories.value.indexOf(subCatId);
    if (index > -1) {
        selectedRetailSubCategories.value.splice(index, 1);
    } else {
        selectedRetailSubCategories.value.push(subCatId);
    }
    
    // Ensure array exists
    if (!props.form.office_retail_retail_unit_details) {
        props.form.office_retail_retail_unit_details = [];
    }
    
    // Update form sub_category_id to include all selected retail sub-categories
    // Also need to preserve office sub-category if it exists
    const currentSubCategories = Array.isArray(props.form.sub_category_id) 
        ? [...props.form.sub_category_id] 
        : (props.form.sub_category_id ? [props.form.sub_category_id] : []);
    
    // Remove all retail sub-categories (Category 2) first, but keep Office sub-category (Category 1)
    const filtered = currentSubCategories.filter(id => {
        // Keep Office sub-category (check if it's in officeSubCategories)
        if (props.officeSubCategories.some(subCat => String(subCat.id) === String(id))) {
            return true;
        }
        // Remove retail sub-categories
        return !props.retailSubCategories.some(subCat => String(subCat.id) === String(id));
    });
    
    // Add selected retail sub-categories
    props.form.sub_category_id = [...filtered, ...selectedRetailSubCategories.value];
    
    // Update retail unit details after DOM update to prevent recursive loops
    nextTick(() => {
        updateRetailUnitDetails();
        isInternalUpdate = false;
    });
};
</script>

<template>
    <div>
        <!-- OFFICE Section -->
        <div class="mb-4" style="padding-top: 1rem;">
            <h5 class="mb-3 section-title" style="background-color: orange; padding: 8px 12px; border-radius: 4px; width: 100%; font-weight: bold;">OFFICE</h5>
            
            <!-- Sub Category Selection (Pill Buttons) -->
            <div class="mb-3">
                <label class="form-label mb-2">Sub Category</label>
                <div v-if="props.officeSubCategories && props.officeSubCategories.length > 0" class="d-flex flex-wrap gap-2">
                    <button
                        v-for="subCat in props.officeSubCategories"
                        :key="subCat.id"
                        type="button"
                        class="btn btn-sm"
                        :class="form.office_retail_data.office_sub_category_id === String(subCat.id) ? 'btn-primary' : 'btn-outline-primary'"
                        @click="handleOfficeSubCategorySelect(String(subCat.id))"
                        style="border-color: #1c467b; border-radius: 20px; height:30px"
                    >
                        {{ subCat.name.toUpperCase() }}
                    </button>
                </div>
                <div v-else class="text-muted">
                    No Office sub-categories available
                </div>
                <div v-if="errors['office_retail_data.office_sub_category_id']" class="invalid-feedback d-block">
                    {{ errors['office_retail_data.office_sub_category_id'][0] }}
                </div>
            </div>

            <!-- Office Form Fields (shown only when sub-category is selected) -->
            <div v-if="form.office_retail_data.office_sub_category_id">
            <!-- Tower Details -->
            <div class="row mb-3">
                <div class="col-md-3 mb-3">
                    <label class="form-label">No. Of Towers</label>
                    <input
                        v-model="form.office_retail_data.no_of_towers"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors['office_retail_data.no_of_towers'] }"
                        placeholder="No. Of Towers"
                        @input="handleIntegerInput"
                    />
                    <div v-if="errors['office_retail_data.no_of_towers']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.no_of_towers'][0] }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">No. Of Floors</label>
                    <input
                        v-model="form.office_retail_data.no_of_floors"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors['office_retail_data.no_of_floors'] }"
                        placeholder="No. Of Floors"
                        @input="handleIntegerInput"
                    />
                    <div v-if="errors['office_retail_data.no_of_floors']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.no_of_floors'][0] }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">No. Of Unit Each Tower</label>
                    <input
                        v-model="form.office_retail_data.no_of_unit_each_tower"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors['office_retail_data.no_of_unit_each_tower'] }"
                        placeholder="No. Of Unit Each Tower"
                        @input="handleIntegerInput"
                    />
                    <div v-if="errors['office_retail_data.no_of_unit_each_tower']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.no_of_unit_each_tower'][0] }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">No. Of Lift</label>
                    <input
                        v-model="form.office_retail_data.no_of_lift"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors['office_retail_data.no_of_lift'] }"
                        placeholder="No. Of Lift"
                        @input="handleIntegerInput"
                    />
                    <div v-if="errors['office_retail_data.no_of_lift']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.no_of_lift'][0] }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Front Road Width</label>
                    <div class="input-group input-group-sm">
                        <input
                            v-model="form.office_retail_data.front_road_width"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['office_retail_data.front_road_width'] }"
                            placeholder="Front Road Width"
                            style="border: 1px solid #1c467b;"
                        />
                        <select
                            v-model="form.office_retail_data.front_road_width_unit_id"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': errors['office_retail_data.front_road_width_unit_id'] }"
                            style="border: 1px solid #1c467b; border-left: 0;"
                        >
                            <option value="">Ft.</option>
                            <option 
                                v-for="unit in heightWidthUnits" 
                                :key="unit.id" 
                                :value="String(unit.id)"
                            >
                                {{ unit.name }}
                            </option>
                        </select>
                    </div>
                    <div v-if="errors['office_retail_data.front_road_width']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.front_road_width'][0] }}
                    </div>
                    <div v-if="errors['office_retail_data.front_road_width_unit_id']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.front_road_width_unit_id'][0] }}
                    </div>
                </div>
            </div>

            <!-- Washroom -->
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Washroom</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input
                                id="office_washroom_private"
                                v-model="form.office_retail_data.washroom"
                                type="radio"
                                value="Private"
                                class="form-check-input"
                            />
                            <label for="office_washroom_private" class="form-check-label">Private</label>
                        </div>
                        <div class="form-check">
                            <input
                                id="office_washroom_public"
                                v-model="form.office_retail_data.washroom"
                                type="radio"
                                value="Public"
                                class="form-check-input"
                            />
                            <label for="office_washroom_public" class="form-check-label">Public</label>
                        </div>
                    </div>
                    <div v-if="errors['office_retail_data.washroom']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.washroom'][0] }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check mt-4">
                        <input
                            id="office_two_road_corner"
                            v-model="form.office_retail_data.two_road_corner"
                            type="checkbox"
                            class="form-check-input"
                        />
                        <label for="office_two_road_corner" class="form-check-label">Two Road Corner.</label>
                    </div>
                </div>
            </div>

            <!-- Tower Name and Total Units -->
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tower Name</label>
                    <input
                        v-model="form.office_retail_data.tower_name"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors['office_retail_data.tower_name'] }"
                        placeholder="Tower Name"
                    />
                    <div v-if="errors['office_retail_data.tower_name']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.tower_name'][0] }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Total Units</label>
                    <input
                        v-model="form.office_retail_data.total_units"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors['office_retail_data.total_units'] }"
                        placeholder="Total Units"
                        @input="handleIntegerInput"
                    />
                    <div v-if="errors['office_retail_data.total_units']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.total_units'][0] }}
                    </div>
                </div>
            </div>

            <!-- Saleable Area -->
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Saleable Area</label>
                    <div class="input-group input-group-sm">
                        <input
                            v-model="form.office_retail_data.saleable_from"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['office_retail_data.saleable_from'] }"
                            placeholder="Size"
                            style="border: 1px solid #1c467b;"
                        />
                        <input
                            v-model="form.office_retail_data.saleable_to"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['office_retail_data.saleable_to'] }"
                            placeholder="Size"
                            style="border: 1px solid #1c467b; border-left: 0;"
                        />
                        <select
                            v-model="form.office_retail_data.saleable_unit_id"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': errors['office_retail_data.saleable_unit_id'] }"
                            style="border: 1px solid #1c467b; border-left: 0;"
                        >
                            <option value="">SqFt</option>
                            <option 
                                v-for="unit in areaUnits" 
                                :key="unit.id" 
                                :value="String(unit.id)"
                            >
                                {{ unit.name }}
                            </option>
                        </select>
                    </div>
                    <div v-if="errors['office_retail_data.saleable_from']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.saleable_from'][0] }}
                    </div>
                    <div v-if="errors['office_retail_data.saleable_to']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.saleable_to'][0] }}
                    </div>
                    <div v-if="errors['office_retail_data.saleable_unit_id']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.saleable_unit_id'][0] }}
                    </div>
                </div>
            </div>

            <!-- Optional Areas -->
            <div class="mb-3">
                <a 
                    v-if="!form.office_retail_data.show_carpet_area"
                    href="javascript:void(0)" 
                    @click="toggleCarpetArea"
                    class="text-primary text-decoration-none me-3"
                    style="font-size: 14px;"
                >
                    + Add Carpet Area
                </a>
                <a 
                    v-else
                    href="javascript:void(0)" 
                    @click="toggleCarpetArea"
                    class="text-danger text-decoration-none me-3"
                    style="font-size: 14px;"
                >
                    Remove Carpet Area
                </a>
                <a 
                    v-if="!form.office_retail_data.show_builtup_area"
                    href="javascript:void(0)" 
                    @click="toggleBuiltupArea"
                    class="text-primary text-decoration-none"
                    style="font-size: 14px;"
                >
                    + Add BuiltUp Area
                </a>
                <a 
                    v-else
                    href="javascript:void(0)" 
                    @click="toggleBuiltupArea"
                    class="text-danger text-decoration-none"
                    style="font-size: 14px;"
                >
                    Remove BuiltUp Area
                </a>
            </div>

            <!-- Carpet Area Fields -->
            <div v-if="form.office_retail_data.show_carpet_area" class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Carpet Area</label>
                    <div class="input-group input-group-sm">
                        <input
                            v-model="form.office_retail_data.carpet_area_from"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['office_retail_data.carpet_area_from'] }"
                            placeholder="Carpet Area From"
                            style="border: 1px solid #1c467b;"
                        />
                        <input
                            v-model="form.office_retail_data.carpet_area_to"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['office_retail_data.carpet_area_to'] }"
                            placeholder="Carpet Area To"
                            style="border: 1px solid #1c467b; border-left: 0;"
                        />
                        <select
                            v-model="form.office_retail_data.carpet_area_unit_id"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': errors['office_retail_data.carpet_area_unit_id'] }"
                            style="border: 1px solid #1c467b; border-left: 0;"
                        >
                            <option value="">SqFt</option>
                            <option 
                                v-for="unit in areaUnits" 
                                :key="unit.id" 
                                :value="String(unit.id)"
                            >
                                {{ unit.name }}
                            </option>
                        </select>
                    </div>
                    <div v-if="errors['office_retail_data.carpet_area_from']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.carpet_area_from'][0] }}
                    </div>
                    <div v-if="errors['office_retail_data.carpet_area_to']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.carpet_area_to'][0] }}
                    </div>
                    <div v-if="errors['office_retail_data.carpet_area_unit_id']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.carpet_area_unit_id'][0] }}
                    </div>
                </div>
            </div>

            <!-- BuiltUp Area Fields -->
            <div v-if="form.office_retail_data.show_builtup_area" class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">BuiltUp Area</label>
                    <div class="input-group input-group-sm">
                        <input
                            v-model="form.office_retail_data.builtup_area_from"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['office_retail_data.builtup_area_from'] }"
                            placeholder="BuiltUp Area From"
                            style="border: 1px solid #1c467b;"
                        />
                        <input
                            v-model="form.office_retail_data.builtup_area_to"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['office_retail_data.builtup_area_to'] }"
                            placeholder="BuiltUp Area To"
                            style="border: 1px solid #1c467b; border-left: 0;"
                        />
                        <select
                            v-model="form.office_retail_data.builtup_area_unit_id"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': errors['office_retail_data.builtup_area_unit_id'] }"
                            style="border: 1px solid #1c467b; border-left: 0;"
                        >
                            <option value="">SqFt</option>
                            <option 
                                v-for="unit in areaUnits" 
                                :key="unit.id" 
                                :value="String(unit.id)"
                            >
                                {{ unit.name }}
                            </option>
                        </select>
                    </div>
                    <div v-if="errors['office_retail_data.builtup_area_from']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.builtup_area_from'][0] }}
                    </div>
                    <div v-if="errors['office_retail_data.builtup_area_to']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.builtup_area_to'][0] }}
                    </div>
                    <div v-if="errors['office_retail_data.builtup_area_unit_id']" class="invalid-feedback d-block">
                        {{ errors['office_retail_data.builtup_area_unit_id'][0] }}
                    </div>
                </div>
            </div>
            </div>
        </div>

        <!-- RETAIL Section -->
        <div class="mb-4" style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
            <h5 class="mb-3 section-title" style="background-color: orange; color: #1c467b; padding: 8px 12px; border-radius: 4px; width: 100%; font-weight: bold;">RETAIL</h5>
            
            <!-- Sub Category Selection (Pill Buttons) -->
            <div class="mb-3">
                <label class="form-label mb-2">Sub Category</label>
                <div v-if="props.retailSubCategories && props.retailSubCategories.length > 0" class="d-flex flex-wrap gap-2">
                    <button
                        v-for="subCat in props.retailSubCategories"
                        :key="subCat.id"
                        type="button"
                        class="btn btn-sm"
                        :class="selectedRetailSubCategories.includes(String(subCat.id)) ? 'btn-primary' : 'btn-outline-primary'"
                        @click="toggleRetailSubCategoryPill(String(subCat.id))"
                        style="border-color: #1c467b; border-radius: 20px;height:30px"
                    >
                        {{ subCat.name.toUpperCase() }}
                    </button>
                </div>
                <div v-else class="text-muted">
                    No Retail sub-categories available
                </div>
            </div>

            <!-- Retail Form Fields (shown only when at least one sub-category is selected) -->
            <div v-if="selectedRetailSubCategories.length > 0">
            <!-- Unit Details Section -->
            <div class="mt-4">
                <h6 class="mb-3" style="color: #1c467b; font-weight: 600;">Unit Details</h6>
                
                <!-- Retail Unit Details (shown for each selected sub-category) -->
                <div v-if="form.office_retail_retail_unit_details && form.office_retail_retail_unit_details.length > 0">
                    <div v-for="(unit, index) in form.office_retail_retail_unit_details" :key="unit.id" class="mb-4 pb-3" style="border-bottom: 1px solid #e5e7eb;">
                    <!-- Section Header with Sub Category Name -->
                    <div class="mb-3">
                        <a href="javascript:void(0)" class="text-primary text-decoration-none" style="font-weight: 500;">
                            {{ getRetailSubCategoryName(unit.sub_category_id) }} Details
                        </a>
                    </div>
                    <div class="row">
                        <!-- Row 1: Tower Name and Sub Category -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tower Name</label>
                            <input
                                v-model="unit.tower_name"
                                type="text"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`office_retail_retail_unit_details.${index}.tower_name`] }"
                                placeholder="Tower Name"
                            />
                            <div v-if="errors[`office_retail_retail_unit_details.${index}.tower_name`]" class="invalid-feedback d-block">
                                {{ errors[`office_retail_retail_unit_details.${index}.tower_name`][0] }}
                            </div>
                        </div>

                        <!-- Sub Category (pre-filled, read-only) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sub Category</label>
                            <select
                                v-model="unit.sub_category_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`office_retail_retail_unit_details.${index}.sub_category_id`] }"
                                disabled
                            >
                                <option value="">Sub Category</option>
                                <option 
                                    v-for="subCat in props.retailSubCategories" 
                                    :key="subCat.id" 
                                    :value="String(subCat.id)"
                                >
                                    {{ subCat.name }}
                                </option>
                            </select>
                            <div v-if="errors[`office_retail_retail_unit_details.${index}.sub_category_id`]" class="invalid-feedback d-block">
                                {{ errors[`office_retail_retail_unit_details.${index}.sub_category_id`][0] }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">

                        <!-- Size -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Size</label>
                            <div class="input-group input-group-sm">
                                <input
                                    v-model="unit.size_from"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control form-control-sm"
                                    :class="{ 'is-invalid': errors[`office_retail_retail_unit_details.${index}.size_from`] }"
                                    placeholder="Size"
                                    style="border: 1px solid #1c467b;"
                                />
                                <input
                                    v-model="unit.size_to"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control form-control-sm"
                                    :class="{ 'is-invalid': errors[`office_retail_retail_unit_details.${index}.size_to`] }"
                                    placeholder="Size"
                                    style="border: 1px solid #1c467b; border-left: 0;"
                                />
                                <select
                                    v-model="unit.size_unit_id"
                                    class="form-select form-select-sm"
                                    :class="{ 'is-invalid': errors[`office_retail_retail_unit_details.${index}.size_unit_id`] }"
                                    style="border: 1px solid #1c467b; border-left: 0;"
                                >
                                    <option value="">SqFt</option>
                                    <option 
                                        v-for="unitOption in areaUnits" 
                                        :key="unitOption.id" 
                                        :value="String(unitOption.id)"
                                    >
                                        {{ unitOption.name }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="errors[`office_retail_retail_unit_details.${index}.size_from`]" class="invalid-feedback d-block">
                                {{ errors[`office_retail_retail_unit_details.${index}.size_from`][0] }}
                            </div>
                            <div v-if="errors[`office_retail_retail_unit_details.${index}.size_to`]" class="invalid-feedback d-block">
                                {{ errors[`office_retail_retail_unit_details.${index}.size_to`][0] }}
                            </div>
                            <div v-if="errors[`office_retail_retail_unit_details.${index}.size_unit_id`]" class="invalid-feedback d-block">
                                {{ errors[`office_retail_retail_unit_details.${index}.size_unit_id`][0] }}
                            </div>
                        </div>

                        <!-- Row 3: Front Opening, No. Of Unit Each Floor, Ceiling Height -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Front Opening</label>
                            <div class="input-group input-group-sm">
                                <input
                                    v-model="unit.front_opening"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control form-control-sm"
                                    :class="{ 'is-invalid': errors[`office_retail_retail_unit_details.${index}.front_opening`] }"
                                    placeholder="Front Opening"
                                    style="border: 1px solid #1c467b;"
                                />
                                <select
                                    v-model="unit.front_opening_unit_id"
                                    class="form-select form-select-sm"
                                    :class="{ 'is-invalid': errors[`office_retail_retail_unit_details.${index}.front_opening_unit_id`] }"
                                    style="border: 1px solid #1c467b; border-left: 0;"
                                >
                                    <option value="">Ft.</option>
                                    <option 
                                        v-for="unitOption in heightWidthUnits" 
                                        :key="unitOption.id" 
                                        :value="String(unitOption.id)"
                                    >
                                        {{ unitOption.name }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="errors[`office_retail_retail_unit_details.${index}.front_opening`]" class="invalid-feedback d-block">
                                {{ errors[`office_retail_retail_unit_details.${index}.front_opening`][0] }}
                            </div>
                            <div v-if="errors[`office_retail_retail_unit_details.${index}.front_opening_unit_id`]" class="invalid-feedback d-block">
                                {{ errors[`office_retail_retail_unit_details.${index}.front_opening_unit_id`][0] }}
                            </div>
                        </div>

                        <!-- No. Of Unit Each Floor -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">No. Of Unit Each Floor</label>
                            <input
                                v-model="unit.no_of_unit_each_floor"
                                type="number"
                                step="1"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`office_retail_retail_unit_details.${index}.no_of_unit_each_floor`] }"
                                placeholder="No. Of Unit Each Floor"
                                @input="handleIntegerInput"
                            />
                            <div v-if="errors[`office_retail_retail_unit_details.${index}.no_of_unit_each_floor`]" class="invalid-feedback d-block">
                                {{ errors[`office_retail_retail_unit_details.${index}.no_of_unit_each_floor`][0] }}
                            </div>
                        </div>

                        <!-- Ceiling Height -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ceiling Height</label>
                            <div class="input-group input-group-sm">
                                <input
                                    v-model="unit.ceiling_height"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control form-control-sm"
                                    :class="{ 'is-invalid': errors[`office_retail_retail_unit_details.${index}.ceiling_height`] }"
                                    placeholder="Ceiling Height"
                                    style="border: 1px solid #1c467b;"
                                />
                                <select
                                    v-model="unit.ceiling_height_unit_id"
                                    class="form-select form-select-sm"
                                    :class="{ 'is-invalid': errors[`office_retail_retail_unit_details.${index}.ceiling_height_unit_id`] }"
                                    style="border: 1px solid #1c467b; border-left: 0;"
                                >
                                    <option value="">Ft.</option>
                                    <option 
                                        v-for="unitOption in heightWidthUnits" 
                                        :key="unitOption.id" 
                                        :value="String(unitOption.id)"
                                    >
                                        {{ unitOption.name }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="errors[`office_retail_retail_unit_details.${index}.ceiling_height`]" class="invalid-feedback d-block">
                                {{ errors[`office_retail_retail_unit_details.${index}.ceiling_height`][0] }}
                            </div>
                            <div v-if="errors[`office_retail_retail_unit_details.${index}.ceiling_height_unit_id`]" class="invalid-feedback d-block">
                                {{ errors[`office_retail_retail_unit_details.${index}.ceiling_height_unit_id`][0] }}
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div v-else class="text-muted mt-3">
                    Loading unit details...
                </div>
            </div>
            </div>
        </div>
    </div>
</template>

