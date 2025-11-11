<script setup lang="ts">
import { ref, computed, watch, onMounted, nextTick, onBeforeUnmount } from 'vue';
import { Plus, X } from 'lucide-vue-next';

declare global {
    interface Window {
        $: any;
    }
}

interface Props {
    form: any;
    errors: any;
    measurementUnits: any[];
    subCategories: any[];
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

// Check if "5+ BHK" is selected
const is5PlusBHK = computed(() => {
    if (!Array.isArray(props.form.sub_category_id)) {
        return false;
    }
    // Find if any selected sub-category is "5+ BHK"
    return props.subCategories.some(subCat => 
        subCat.name === '5+ BHK' && props.form.sub_category_id.includes(String(subCat.id))
    );
});

// Get available tower names from tower details
const availableTowerNames = computed(() => {
    if (!props.form.category4_tower_details || props.form.category4_tower_details.length === 0) {
        return [];
    }
    return props.form.category4_tower_details
        .map((tower: any) => tower.tower_name)
        .filter((name: string) => name && name.trim() !== '');
});

// Category 4 tower details structure
interface Category4TowerDetail {
    id: number;
    tower_name: string;
    total_units: string;
    total_floor: string;
    sub_category_ids: string[];
}

// Initialize category 4 tower details
const initializeCategory4TowerDetails = () => {
    if (!props.form.category4_tower_details || props.form.category4_tower_details.length === 0) {
        props.form.category4_tower_details = [{
            id: 1,
            tower_name: '',
            total_units: '',
            total_floor: '',
            sub_category_ids: [],
        }];
    }
};

// Initialize on component mount
initializeCategory4TowerDetails();

let category4TowerIdCounter = Math.max(...(props.form.category4_tower_details || []).map((t: any) => t.id || 0), 0) + 1;

const addTower = async () => {
    props.form.category4_tower_details.push({
        id: category4TowerIdCounter++,
        tower_name: '',
        total_units: '',
        total_floor: '',
        sub_category_ids: [],
    });
    // Initialize Select2 for the new tower
    await nextTick();
    initializeSelect2();
};

const removeTower = (index: number) => {
    if (props.form.category4_tower_details.length > 1) {
        // Destroy Select2 instance before removing
        if (typeof window.$ !== 'undefined' && window.$.fn.select2) {
            const selectId = `category4_tower_sub_category_${index}`;
            const selectElement = window.$(`#${selectId}`);
            if (selectElement.length && selectElement.data('select2')) {
                selectElement.select2('destroy');
            }
        }
        props.form.category4_tower_details.splice(index, 1);
        // Reinitialize Select2 for remaining towers (IDs will shift)
        nextTick(() => {
            initializeSelect2();
        });
    }
};


// Get sub-category name by ID
const getSubCategoryName = (subCategoryId: string) => {
    const subCat = props.subCategories.find(sc => String(sc.id) === subCategoryId);
    return subCat ? subCat.name : '';
};

// Category 4 unit details structure
interface Category4UnitDetail {
    id: number;
    tower_name: string;
    saleable_from: string;
    saleable_to: string;
    saleable_unit_id: string;
    wash_area: string;
    wash_area_unit_id: string;
    balcony_area: string;
    balcony_area_unit_id: string;
    ceiling_height: string;
    ceiling_height_unit_id: string;
    servant_room: boolean;
    show_carpet_area: boolean;
    carpet_area_from: string;
    carpet_area_to: string;
    carpet_area_unit_id: string;
    show_builtup_area: boolean;
    builtup_area_from: string;
    builtup_area_to: string;
    builtup_area_unit_id: string;
}

// Initialize category 4 unit details
const initializeCategory4UnitDetails = () => {
    if (!props.form.category4_unit_details || props.form.category4_unit_details.length === 0) {
        props.form.category4_unit_details = [{
            id: 1,
            tower_name: '',
            saleable_from: '',
            saleable_to: '',
            saleable_unit_id: '',
            wash_area: '',
            wash_area_unit_id: '',
            balcony_area: '',
            balcony_area_unit_id: '',
            ceiling_height: '',
            ceiling_height_unit_id: '',
            servant_room: false,
            show_carpet_area: false,
            carpet_area_from: '',
            carpet_area_to: '',
            carpet_area_unit_id: '',
            show_builtup_area: false,
            builtup_area_from: '',
            builtup_area_to: '',
            builtup_area_unit_id: '',
        }];
    }
};

// Initialize on component mount
initializeCategory4UnitDetails();

let category4UnitIdCounter = Math.max(...(props.form.category4_unit_details || []).map((u: any) => u.id || 0), 0) + 1;

const addUnit = () => {
    props.form.category4_unit_details.push({
        id: category4UnitIdCounter++,
        tower_name: '',
        saleable_from: '',
        saleable_to: '',
        saleable_unit_id: '',
        wash_area: '',
        wash_area_unit_id: '',
        balcony_area: '',
        balcony_area_unit_id: '',
        ceiling_height: '',
        ceiling_height_unit_id: '',
        servant_room: false,
        show_carpet_area: false,
        carpet_area_from: '',
        carpet_area_to: '',
        carpet_area_unit_id: '',
        show_builtup_area: false,
        builtup_area_from: '',
        builtup_area_to: '',
        builtup_area_unit_id: '',
    });
};

// Toggle carpet area
const toggleCarpetArea = (index: number) => {
    props.form.category4_unit_details[index].show_carpet_area = !props.form.category4_unit_details[index].show_carpet_area;
    if (!props.form.category4_unit_details[index].show_carpet_area) {
        props.form.category4_unit_details[index].carpet_area_from = '';
        props.form.category4_unit_details[index].carpet_area_to = '';
        props.form.category4_unit_details[index].carpet_area_unit_id = '';
    }
};

// Toggle builtup area
const toggleBuiltupArea = (index: number) => {
    props.form.category4_unit_details[index].show_builtup_area = !props.form.category4_unit_details[index].show_builtup_area;
    if (!props.form.category4_unit_details[index].show_builtup_area) {
        props.form.category4_unit_details[index].builtup_area_from = '';
        props.form.category4_unit_details[index].builtup_area_to = '';
        props.form.category4_unit_details[index].builtup_area_unit_id = '';
    }
};

const removeUnit = (index: number) => {
    if (props.form.category4_unit_details.length > 1) {
        props.form.category4_unit_details.splice(index, 1);
    }
};

const handleIntegerInput = (event: Event, field: string) => {
    const target = event.target as HTMLInputElement;
    const value = target.value;
    const integerValue = value.replace(/[^0-9]/g, '');
    
    if (value !== integerValue) {
        target.value = integerValue;
        (props.form as any)[field] = integerValue;
    }
};

// Initialize Select2 for sub-category selects
const initializeSelect2 = async () => {
    await nextTick();
    
    if (typeof window.$ !== 'undefined' && window.$.fn.select2) {
        props.form.category4_tower_details.forEach((tower: any, index: number) => {
            const selectId = `category4_tower_sub_category_${index}`;
            const selectElement = window.$(`#${selectId}`);
            
            if (selectElement.length && !selectElement.data('select2')) {
                selectElement.select2({
                    placeholder: 'Select Sub Categories',
                    allowClear: true,
                    width: '100%',
                });
                
                // Set initial values if tower has sub_category_ids
                if (tower.sub_category_ids && tower.sub_category_ids.length > 0) {
                    selectElement.val(tower.sub_category_ids.map((id: string) => String(id))).trigger('change');
                }
                
                // Sync Select2 with Vue model
                selectElement.on('change', function(this: HTMLSelectElement) {
                    const selectedValues = window.$(this).val() || [];
                    tower.sub_category_ids = Array.isArray(selectedValues) 
                        ? selectedValues.map((val: string | number) => String(val))
                        : selectedValues ? [String(selectedValues)] : [];
                });
            }
        });
    }
};

// Watch for changes in tower details array to reinitialize Select2
watch(() => props.form.category4_tower_details.length, () => {
    initializeSelect2();
}, { flush: 'post' });

// Watch for sub-categories changes to update Select2
watch(() => props.subCategories, () => {
    initializeSelect2();
}, { deep: true });

onMounted(() => {
    initializeSelect2();
});

onBeforeUnmount(() => {
    // Destroy Select2 instances before component unmounts
    if (typeof window.$ !== 'undefined' && window.$.fn.select2) {
        props.form.category4_tower_details.forEach((tower: any, index: number) => {
            const selectId = `category4_tower_sub_category_${index}`;
            const selectElement = window.$(`#${selectId}`);
            if (selectElement.length && selectElement.data('select2')) {
                selectElement.select2('destroy');
            }
        });
    }
});
</script>

<template>
    <div>
        <!-- General Unit Type Details -->
        <div class="mb-4">
            <h5 class="mb-3 section-title">General Unit Type Details</h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="category4_no_of_towers" class="form-label">No. Of Tower</label>
                    <input
                        id="category4_no_of_towers"
                        v-model="form.no_of_towers"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.no_of_towers }"
                        placeholder="Enter number of towers"
                        @input="handleIntegerInput($event, 'no_of_towers')"
                    />
                    <div v-if="errors.no_of_towers" class="invalid-feedback d-block">
                        {{ errors.no_of_towers[0] }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="category4_no_of_floors" class="form-label">No. Of Floors</label>
                    <input
                        id="category4_no_of_floors"
                        v-model="form.no_of_floors"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.no_of_floors }"
                        placeholder="Enter number of floors"
                        @input="handleIntegerInput($event, 'no_of_floors')"
                    />
                    <div v-if="errors.no_of_floors" class="invalid-feedback d-block">
                        {{ errors.no_of_floors[0] }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="category4_total_units" class="form-label">Total Units</label>
                    <input
                        id="category4_total_units"
                        v-model="form.total_units"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.total_units }"
                        placeholder="Enter total units"
                        @input="handleIntegerInput($event, 'total_units')"
                    />
                    <div v-if="errors.total_units" class="invalid-feedback d-block">
                        {{ errors.total_units[0] }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="category4_no_of_lift" class="form-label">No. Of Elevator In Each Tower</label>
                    <input
                        id="category4_no_of_lift"
                        v-model="form.no_of_lift"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.no_of_lift }"
                        placeholder="Enter number of elevators"
                        @input="handleIntegerInput($event, 'no_of_lift')"
                    />
                    <div v-if="errors.no_of_lift" class="invalid-feedback d-block">
                        {{ errors.no_of_lift[0] }}
                    </div>
                </div>
            </div>
            <!-- Total Room (only shown if 5+ BHK is selected) -->
            <div v-if="is5PlusBHK" class="row mt-3">
                <div class="col-md-3 mb-3">
                    <label for="category4_total_room" class="form-label">Total Room</label>
                    <input
                        id="category4_total_room"
                        v-model="form.category4_total_room"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.category4_total_room }"
                        placeholder="Enter total rooms"
                        name="category4_total_room"
                        @input="handleIntegerInput($event, 'category4_total_room')"
                    />
                    <div v-if="errors.category4_total_room" class="invalid-feedback d-block">
                        {{ errors.category4_total_room[0] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Tower Details -->
        <div class="mb-4" style="border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
            <h5 class="mb-3 section-title">Tower Details</h5>
            <div v-for="(tower, index) in form.category4_tower_details" :key="tower.id" class="mb-4 pb-3" :class="{ 'border-bottom': index < form.category4_tower_details.length - 1 }" style="border-bottom: 1px solid #e5e7eb;">
                <!-- First Row: Tower Name, Total Units, Total Floor -->
                <div class="row">
                    <!-- Tower Name -->
                    <div class="col-md-3 mb-3">
                        <label :for="`category4_tower_name_${index}`" class="form-label">Tower Name</label>
                        <input
                            :id="`category4_tower_name_${index}`"
                            v-model="tower.tower_name"
                            type="text"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors[`category4_tower_details.${index}.tower_name`] }"
                            placeholder="Enter tower name"
                            name="tower_name"
                        />
                        <div v-if="errors[`category4_tower_details.${index}.tower_name`]" class="invalid-feedback d-block">
                            {{ errors[`category4_tower_details.${index}.tower_name`][0] }}
                        </div>
                    </div>

                    <!-- Total Units -->
                    <div class="col-md-2 mb-3">
                        <label :for="`category4_tower_total_units_${index}`" class="form-label">Total Units</label>
                        <input
                            :id="`category4_tower_total_units_${index}`"
                            v-model="tower.total_units"
                            type="number"
                            step="1"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors[`category4_tower_details.${index}.total_units`] }"
                            placeholder="Enter total units"
                            name="total_units"
                            @input="handleIntegerInput($event, 'total_units')"
                        />
                        <div v-if="errors[`category4_tower_details.${index}.total_units`]" class="invalid-feedback d-block">
                            {{ errors[`category4_tower_details.${index}.total_units`][0] }}
                        </div>
                    </div>

                    <!-- Total Floor -->
                    <div class="col-md-2 mb-3">
                        <label :for="`category4_tower_total_floor_${index}`" class="form-label">Total Floor</label>
                        <input
                            :id="`category4_tower_total_floor_${index}`"
                            v-model="tower.total_floor"
                            type="number"
                            step="1"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors[`category4_tower_details.${index}.total_floor`] }"
                            placeholder="Enter total floors"
                            name="total_floor"
                            @input="handleIntegerInput($event, 'total_floor')"
                        />
                        <div v-if="errors[`category4_tower_details.${index}.total_floor`]" class="invalid-feedback d-block">
                            {{ errors[`category4_tower_details.${index}.total_floor`][0] }}
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label :for="`category4_tower_sub_category_${index}`" class="form-label">Sub Category</label>
                        <select
                            :id="`category4_tower_sub_category_${index}`"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors[`category4_tower_details.${index}.sub_category_ids`] }"
                            :multiple="true"
                        >
                            <option 
                                v-for="subCat in subCategories" 
                                :key="subCat.id" 
                                :value="String(subCat.id)"
                            >
                                {{ subCat.name }}
                            </option>
                        </select>
                        <div v-if="errors[`category4_tower_details.${index}.sub_category_ids`]" class="invalid-feedback d-block">
                            {{ errors[`category4_tower_details.${index}.sub_category_ids`][0] }}
                        </div>
                    </div>
                    <div v-if="index > 0" class="col-md-1 mb-3 mt-4">
                        <button
                            type="button"
                            @click="removeTower(index)"
                            class="btn btn-sm btn-icon-square btn-icon-square-38"
                            style="background-color: #fa8b0c;"
                            title="Remove Tower"
                        >
                            <X :size="18" color="white" />
                        </button>
                    </div>
                    <div class="col-md-1 mb-3 mt-4" v-else>
                        <!-- Add Tower Button -->
                        <div class="mb-3">
                            <button
                                type="button"
                                @click="addTower"
                                class="btn btn-primary btn-sm btn-icon-square btn-icon-square-38"
                                style="background-color: #1c467b; border-color: #1c467b;"
                                title="Add Tower"
                            >
                                <Plus :size="18" color="white" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unit Details -->
        <div class="mb-4" style="padding-top: 1.5rem;">
            <h5 class="mb-3 section-title">Unit Details</h5>
            <div v-for="(unit, index) in form.category4_unit_details" :key="unit.id" class="mb-4 pb-3" :class="{ 'border-bottom': index < form.category4_unit_details.length - 1 }" style="border-bottom: 1px solid #e5e7eb;">
                <div class="row">
                    <!-- Tower Selection -->
                    <div class="col-md-4 mb-3">
                        <label :for="`category4_unit_tower_${index}`" class="form-label">Tower</label>
                        <select
                            :id="`category4_unit_tower_${index}`"
                            v-model="unit.tower_name"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': errors[`category4_unit_details.${index}.tower_name`] }"
                            style="height: 38px;"
                            name="tower_name"
                        >
                            <option value="">Select Tower</option>
                            <option 
                                v-for="towerName in availableTowerNames" 
                                :key="towerName" 
                                :value="towerName"
                            >
                                {{ towerName }}
                            </option>
                        </select>
                        <div v-if="errors[`category4_unit_details.${index}.tower_name`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.tower_name`][0] }}
                        </div>
                    </div>

                    <!-- Saleable From and To -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Saleable</label>
                        <div class="input-group input-group-sm">
                            <input
                                :id="`category4_saleable_from_${index}`"
                                v-model="unit.saleable_from"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.saleable_from`] }"
                                placeholder="From"
                                name="saleable_from"
                                style="border: 1px solid #1c467b;"
                            />
                            <input
                                :id="`category4_saleable_to_${index}`"
                                v-model="unit.saleable_to"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.saleable_to`] }"
                                placeholder="To"
                                name="saleable_to"
                                style="border: 1px solid #1c467b; border-left: 0;"
                            />
                            <select
                                :id="`category4_saleable_unit_${index}`"
                                v-model="unit.saleable_unit_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.saleable_unit_id`] }"
                                style="border: 1px solid #1c467b; border-left: 0;"
                                name="saleable_unit_id"
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
                        <div v-if="errors[`category4_unit_details.${index}.saleable_from`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.saleable_from`][0] }}
                        </div>
                        <div v-if="errors[`category4_unit_details.${index}.saleable_to`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.saleable_to`][0] }}
                        </div>
                        <div v-if="errors[`category4_unit_details.${index}.saleable_unit_id`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.saleable_unit_id`][0] }}
                        </div>
                    </div>

                    <!-- Ceiling Height -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ceiling Height</label>
                        <div class="input-group input-group-sm">
                            <input
                                :id="`category4_ceiling_height_${index}`"
                                v-model="unit.ceiling_height"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.ceiling_height`] }"
                                placeholder="Ceiling Height"
                                name="ceiling_height"
                                style="border: 1px solid #1c467b;"
                            />
                            <select
                                :id="`category4_ceiling_height_unit_${index}`"
                                v-model="unit.ceiling_height_unit_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.ceiling_height_unit_id`] }"
                                style="border: 1px solid #1c467b; border-left: 0;"
                                name="ceiling_height_unit_id"
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
                        <div v-if="errors[`category4_unit_details.${index}.ceiling_height`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.ceiling_height`][0] }}
                        </div>
                        <div v-if="errors[`category4_unit_details.${index}.ceiling_height_unit_id`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.ceiling_height_unit_id`][0] }}
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <!-- Wash Area -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Wash Area</label>
                        <div class="input-group input-group-sm">
                            <input
                                :id="`category4_wash_area_${index}`"
                                v-model="unit.wash_area"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.wash_area`] }"
                                placeholder="Wash Area"
                                name="wash_area"
                                style="border: 1px solid #1c467b;"
                            />
                            <select
                                :id="`category4_wash_area_unit_${index}`"
                                v-model="unit.wash_area_unit_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.wash_area_unit_id`] }"
                                style="border: 1px solid #1c467b; border-left: 0;"
                                name="wash_area_unit_id"
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
                        <div v-if="errors[`category4_unit_details.${index}.wash_area`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.wash_area`][0] }}
                        </div>
                        <div v-if="errors[`category4_unit_details.${index}.wash_area_unit_id`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.wash_area_unit_id`][0] }}
                        </div>
                    </div>

                    <!-- Balcony Area -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Balcony Area</label>
                        <div class="input-group input-group-sm">
                            <input
                                :id="`category4_balcony_area_${index}`"
                                v-model="unit.balcony_area"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.balcony_area`] }"
                                placeholder="Balcony Area"
                                name="balcony_area"
                                style="border: 1px solid #1c467b;"
                            />
                            <select
                                :id="`category4_balcony_area_unit_${index}`"
                                v-model="unit.balcony_area_unit_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.balcony_area_unit_id`] }"
                                style="border: 1px solid #1c467b; border-left: 0;"
                                name="balcony_area_unit_id"
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
                        <div v-if="errors[`category4_unit_details.${index}.balcony_area`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.balcony_area`][0] }}
                        </div>
                        <div v-if="errors[`category4_unit_details.${index}.balcony_area_unit_id`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.balcony_area_unit_id`][0] }}
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 mt-4">
                        <div class="form-check">
                            <input
                                :id="`category4_servant_room_${index}`"
                                v-model="unit.servant_room"
                                type="checkbox"
                                class="form-check-input"
                                name="servant_room"
                            />
                            <label :for="`category4_servant_room_${index}`" class="form-check-label">
                                Servant Room
                            </label>
                        </div>
                    </div>
                    <!-- Remove Unit Button (not for first row) -->
                    <div v-if="index > 0" class="mt-4 col-md-1">
                        <button
                            type="button"
                            @click="removeUnit(index)"
                            class="btn btn-sm btn-icon-square btn-icon-square-38"
                            style="background-color: #fa8b0c;"
                            title="Remove Unit"
                        >
                            <X :size="18" color="white" />
                        </button>
                    </div>
                </div>

                <!-- Action Links for Carpet Area and Built Up Area -->
                <div class="mb-3">
                    <a 
                        v-if="!unit.show_carpet_area"
                        href="javascript:void(0)" 
                        @click="toggleCarpetArea(index)"
                        class="text-primary text-decoration-none me-3"
                        style="font-size: 14px;"
                    >
                        + Add Carpet Area
                    </a>
                    <a 
                        v-else
                        href="javascript:void(0)" 
                        @click="toggleCarpetArea(index)"
                        class="text-danger text-decoration-none me-3"
                        style="font-size: 14px;"
                    >
                        Remove Carpet Area
                    </a>
                    <a 
                        v-if="!unit.show_builtup_area"
                        href="javascript:void(0)" 
                        @click="toggleBuiltupArea(index)"
                        class="text-primary text-decoration-none"
                        style="font-size: 14px;"
                    >
                        + Add Built Up Area
                    </a>
                    <a 
                        v-else
                        href="javascript:void(0)" 
                        @click="toggleBuiltupArea(index)"
                        class="text-danger text-decoration-none"
                        style="font-size: 14px;"
                    >
                        Remove Built Up Area
                    </a>
                </div>

                <!-- Carpet Area and Built Up Area (Conditional) -->
                <div v-if="unit.show_carpet_area || unit.show_builtup_area" class="row mb-3">
                    <!-- Carpet Area -->
                    <div v-if="unit.show_carpet_area" class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Carpet Area</label>
                        <div class="input-group input-group-sm">
                            <input
                                :id="`category4_carpet_area_from_${index}`"
                                v-model="unit.carpet_area_from"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.carpet_area_from`] }"
                                placeholder="Carpet Area"
                                name="carpet_area_from"
                                style="border: 1px solid #1c467b;"
                            />
                            <input
                                :id="`category4_carpet_area_to_${index}`"
                                v-model="unit.carpet_area_to"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.carpet_area_to`] }"
                                placeholder="Carpet Area"
                                name="carpet_area_to"
                                style="border: 1px solid #1c467b; border-left: 0;"
                            />
                            <select
                                :id="`category4_carpet_area_unit_${index}`"
                                v-model="unit.carpet_area_unit_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.carpet_area_unit_id`] }"
                                style="border: 1px solid #1c467b; border-left: 0;"
                                name="carpet_area_unit_id"
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
                        <div v-if="errors[`category4_unit_details.${index}.carpet_area_from`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.carpet_area_from`][0] }}
                        </div>
                        <div v-if="errors[`category4_unit_details.${index}.carpet_area_to`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.carpet_area_to`][0] }}
                        </div>
                        <div v-if="errors[`category4_unit_details.${index}.carpet_area_unit_id`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.carpet_area_unit_id`][0] }}
                        </div>
                    </div>

                    <!-- Built Up Area -->
                    <div v-if="unit.show_builtup_area" class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Built Up Area</label>
                        <div class="input-group input-group-sm">
                            <input
                                :id="`category4_builtup_area_from_${index}`"
                                v-model="unit.builtup_area_from"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.builtup_area_from`] }"
                                placeholder="Built Up Area"
                                name="builtup_area_from"
                                style="border: 1px solid #1c467b;"
                            />
                            <input
                                :id="`category4_builtup_area_to_${index}`"
                                v-model="unit.builtup_area_to"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.builtup_area_to`] }"
                                placeholder="Built Up Area"
                                name="builtup_area_to"
                                style="border: 1px solid #1c467b; border-left: 0;"
                            />
                            <select
                                :id="`category4_builtup_area_unit_${index}`"
                                v-model="unit.builtup_area_unit_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`category4_unit_details.${index}.builtup_area_unit_id`] }"
                                style="border: 1px solid #1c467b; border-left: 0;"
                                name="builtup_area_unit_id"
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
                        <div v-if="errors[`category4_unit_details.${index}.builtup_area_from`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.builtup_area_from`][0] }}
                        </div>
                        <div v-if="errors[`category4_unit_details.${index}.builtup_area_to`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.builtup_area_to`][0] }}
                        </div>
                        <div v-if="errors[`category4_unit_details.${index}.builtup_area_unit_id`]" class="invalid-feedback d-block">
                            {{ errors[`category4_unit_details.${index}.builtup_area_unit_id`][0] }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Add Unit Button -->
            <div class="mb-3">
                <button
                    type="button"
                    @click="addUnit"
                    class="btn btn-primary btn-sm btn-icon-square btn-icon-square-38"
                    style="background-color: #1c467b; border-color: #1c467b;"
                    title="Add Unit"
                >
                    <Plus :size="18" color="white" />
                </button>
            </div>
        </div>
    </div>
</template>

