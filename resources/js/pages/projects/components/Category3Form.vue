<script setup lang="ts">
import { ref, computed } from 'vue';
import { Plus, X } from 'lucide-vue-next';

interface Props {
    form: any;
    errors: any;
    measurementUnits: any[];
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

// Check if sub category is 9 (hide Constructed Area)
const shouldHideConstructedArea = computed(() => {
    return props.form.sub_category_id === '9';
});

// Category 3 unit details structure
interface Category3UnitDetail {
    id: number;
    plot_area_from: string;
    plot_area_to: string;
    plot_area_unit_id: string;
    constructed_area_from: string;
    constructed_area_to: string;
    constructed_area_unit_id: string;
    road_width_from: string;
    road_width_to: string;
    road_width_unit_id: string;
    ceiling_height: string;
    ceiling_height_unit_id: string;
    total_no_of_units: string;
}

// Initialize category 3 unit details
const initializeCategory3UnitDetails = () => {
    if (!props.form.category3_unit_details || props.form.category3_unit_details.length === 0) {
        props.form.category3_unit_details = [{
            id: 1,
            plot_area_from: '',
            plot_area_to: '',
            plot_area_unit_id: '',
            constructed_area_from: '',
            constructed_area_to: '',
            constructed_area_unit_id: '',
            road_width_from: '',
            road_width_to: '',
            road_width_unit_id: '',
            ceiling_height: '',
            ceiling_height_unit_id: '',
            total_no_of_units: '',
        }];
    }
};

// Initialize on component mount
initializeCategory3UnitDetails();

let category3UnitIdCounter = Math.max(...(props.form.category3_unit_details || []).map((u: any) => u.id || 0), 0) + 1;

const addSection = () => {
    // Add new section after the first row (at index 1)
    props.form.category3_unit_details.splice(1, 0, {
        id: category3UnitIdCounter++,
        plot_area_from: '',
        plot_area_to: '',
        plot_area_unit_id: '',
        constructed_area_from: '',
        constructed_area_to: '',
        constructed_area_unit_id: '',
        road_width_from: '',
        road_width_to: '',
        road_width_unit_id: '',
        ceiling_height: '',
        ceiling_height_unit_id: '',
        total_no_of_units: '',
    });
};

const removeSection = (index: number) => {
    // Only allow removing if there's more than one section
    if (props.form.category3_unit_details.length > 1 && index > 0) {
        props.form.category3_unit_details.splice(index, 1);
    }
};

// Dynamic facilities structure (checkbox + input fields added after Water)
interface DynamicFacility {
    id: number;
    label: string;
    checked: boolean;
    value: string;
}

// Initialize dynamic facilities
const initializeDynamicFacilities = () => {
    if (!props.form.dynamic_facilities) {
        props.form.dynamic_facilities = [];
    }
};

initializeDynamicFacilities();

let dynamicFacilityIdCounter = Math.max(...(props.form.dynamic_facilities || []).map((f: any) => f.id || 0), 0) + 1;

// Field name for new facility
const newFacilityFieldName = ref('');

const addFacility = () => {
    if (!newFacilityFieldName.value.trim()) {
        return;
    }
    
    if (!props.form.dynamic_facilities) {
        props.form.dynamic_facilities = [];
    }
    
    props.form.dynamic_facilities.push({
        id: dynamicFacilityIdCounter++,
        label: newFacilityFieldName.value.trim(),
        checked: false,
        value: '',
    });
    
    newFacilityFieldName.value = '';
};

const removeFacility = (index: number) => {
    if (props.form.dynamic_facilities) {
        props.form.dynamic_facilities.splice(index, 1);
    }
};

// Utility and Board Information structure
interface UtilityBoard {
    pollution_control_board: {
        checked: boolean;
        value: string;
    };
    ec: {
        checked: boolean;
        value: string;
    };
    gas: {
        checked: boolean;
        value: string;
    };
    power: {
        checked: boolean;
        value: string;
    };
    water: {
        checked: boolean;
        value: string;
    };
}

// Initialize utility board information
const initializeUtilityBoard = () => {
    if (!props.form.utility_board) {
        props.form.utility_board = {
            pollution_control_board: {
                checked: false,
                value: '',
            },
            ec: {
                checked: false,
                value: '',
            },
            gas: {
                checked: false,
                value: '',
            },
            power: {
                checked: false,
                value: '',
            },
            water: {
                checked: false,
                value: '',
            },
        };
    }
};

initializeUtilityBoard();

const handleIntegerInput = (event: Event, field: string) => {
    const target = event.target as HTMLInputElement;
    const value = target.value;
    const integerValue = value.replace(/[^0-9]/g, '');
    
    if (value !== integerValue) {
        target.value = integerValue;
        (props.form as any)[field] = integerValue;
    }
};
</script>

<template>
    <div>
        <!-- Unit Details Sections (First Section - Dynamic) -->
        <div class="mb-4">
            <div v-for="(unit, index) in form.category3_unit_details" :key="unit.id" class="mb-4 pb-3" :class="{ 'border-bottom': index < form.category3_unit_details.length - 1 }" style="border-bottom: 1px solid #e5e7eb;">
                <!-- First Row: Total No. Of Units, Ceiling Height, Plot Area -->
                <div class="row">
                    <!-- Total No. Of Units (First) -->
                    <div class="col-md-4 mb-3">
                        <label :for="`total_no_of_units_${index}`" class="form-label">Total No. Of Units</label>
                        <input
                            :id="`total_no_of_units_${index}`"
                            v-model="unit.total_no_of_units"
                            type="number"
                            step="1"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors[`category3_unit_details.${index}.total_no_of_units`] }"
                            placeholder="Total No. Of Units"
                            @input="handleIntegerInput($event, 'total_no_of_units')"
                        />
                        <div v-if="errors[`category3_unit_details.${index}.total_no_of_units`]" class="invalid-feedback d-block">
                            {{ errors[`category3_unit_details.${index}.total_no_of_units`][0] }}
                        </div>
                    </div>

                    <!-- Ceiling Height (Second) -->
                    <div class="col-md-4 mb-3">
                        <label :for="`ceiling_height_${index}`" class="form-label">Ceiling Height</label>
                        <div class="input-group input-group-sm">
                            <input
                                :id="`ceiling_height_${index}`"
                                v-model="unit.ceiling_height"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category3_unit_details.${index}.ceiling_height`] }"
                                placeholder="Ceiling Height"
                                style="border: 1px solid #1c467b;"
                            />
                            <select
                                :id="`ceiling_height_unit_${index}`"
                                v-model="unit.ceiling_height_unit_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`category3_unit_details.${index}.ceiling_height_unit_id`] }"
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
                        <div v-if="errors[`category3_unit_details.${index}.ceiling_height`]" class="invalid-feedback d-block">
                            {{ errors[`category3_unit_details.${index}.ceiling_height`][0] }}
                        </div>
                        <div v-if="errors[`category3_unit_details.${index}.ceiling_height_unit_id`]" class="invalid-feedback d-block">
                            {{ errors[`category3_unit_details.${index}.ceiling_height_unit_id`][0] }}
                        </div>
                    </div>

                    <!-- Plot Area (Third) -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Plot Area</label>
                        <div class="input-group input-group-sm">
                            <input
                                :id="`plot_area_from_${index}`"
                                v-model="unit.plot_area_from"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category3_unit_details.${index}.plot_area_from`] }"
                                placeholder="Size"
                                style="border: 1px solid #1c467b;"
                            />
                            <input
                                :id="`plot_area_to_${index}`"
                                v-model="unit.plot_area_to"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category3_unit_details.${index}.plot_area_to`] }"
                                placeholder="Size"
                                style="border: 1px solid #1c467b; border-left: 0;"
                            />
                            <select
                                :id="`plot_area_unit_${index}`"
                                v-model="unit.plot_area_unit_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`category3_unit_details.${index}.plot_area_unit_id`] }"
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
                        <div v-if="errors[`category3_unit_details.${index}.plot_area_from`]" class="invalid-feedback d-block">
                            {{ errors[`category3_unit_details.${index}.plot_area_from`][0] }}
                        </div>
                        <div v-if="errors[`category3_unit_details.${index}.plot_area_to`]" class="invalid-feedback d-block">
                            {{ errors[`category3_unit_details.${index}.plot_area_to`][0] }}
                        </div>
                        <div v-if="errors[`category3_unit_details.${index}.plot_area_unit_id`]" class="invalid-feedback d-block">
                            {{ errors[`category3_unit_details.${index}.plot_area_unit_id`][0] }}
                        </div>
                    </div>
                </div>

                <!-- Second Row: Road Width, Constructed Area (Last) -->
                <div class="row mt-3">
                    <!-- Road Width Of Front Side Area -->
                    <div :class="shouldHideConstructedArea ? 'col-md-12 mb-3' : 'col-md-6 mb-3'">
                        <label class="form-label">Road Width Of Front Side Area</label>
                        <div class="input-group input-group-sm">
                            <input
                                :id="`road_width_from_${index}`"
                                v-model="unit.road_width_from"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category3_unit_details.${index}.road_width_from`] }"
                                placeholder="Size"
                                style="border: 1px solid #1c467b;"
                            />
                            <input
                                :id="`road_width_to_${index}`"
                                v-model="unit.road_width_to"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category3_unit_details.${index}.road_width_to`] }"
                                placeholder="Size"
                                style="border: 1px solid #1c467b; border-left: 0;"
                            />
                            <select
                                :id="`road_width_unit_${index}`"
                                v-model="unit.road_width_unit_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`category3_unit_details.${index}.road_width_unit_id`] }"
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
                        <div v-if="errors[`category3_unit_details.${index}.road_width_from`]" class="invalid-feedback d-block">
                            {{ errors[`category3_unit_details.${index}.road_width_from`][0] }}
                        </div>
                        <div v-if="errors[`category3_unit_details.${index}.road_width_to`]" class="invalid-feedback d-block">
                            {{ errors[`category3_unit_details.${index}.road_width_to`][0] }}
                        </div>
                        <div v-if="errors[`category3_unit_details.${index}.road_width_unit_id`]" class="invalid-feedback d-block">
                            {{ errors[`category3_unit_details.${index}.road_width_unit_id`][0] }}
                        </div>
                    </div>

                    <!-- Constructed Area (Last - Hide when sub_category_id === 9) -->
                    <div v-if="!shouldHideConstructedArea" class="col-md-6 mb-3">
                        <label class="form-label">Constructed Area</label>
                        <div class="input-group input-group-sm">
                            <input
                                :id="`constructed_area_from_${index}`"
                                v-model="unit.constructed_area_from"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category3_unit_details.${index}.constructed_area_from`] }"
                                placeholder="Size"
                                style="border: 1px solid #1c467b;"
                            />
                            <input
                                :id="`constructed_area_to_${index}`"
                                v-model="unit.constructed_area_to"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`category3_unit_details.${index}.constructed_area_to`] }"
                                placeholder="Size"
                                style="border: 1px solid #1c467b; border-left: 0;"
                            />
                            <select
                                :id="`constructed_area_unit_${index}`"
                                v-model="unit.constructed_area_unit_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`category3_unit_details.${index}.constructed_area_unit_id`] }"
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
                        <div v-if="errors[`category3_unit_details.${index}.constructed_area_from`]" class="invalid-feedback d-block">
                            {{ errors[`category3_unit_details.${index}.constructed_area_from`][0] }}
                        </div>
                        <div v-if="errors[`category3_unit_details.${index}.constructed_area_to`]" class="invalid-feedback d-block">
                            {{ errors[`category3_unit_details.${index}.constructed_area_to`][0] }}
                        </div>
                        <div v-if="errors[`category3_unit_details.${index}.constructed_area_unit_id`]" class="invalid-feedback d-block">
                            {{ errors[`category3_unit_details.${index}.constructed_area_unit_id`][0] }}
                        </div>
                    </div>
                </div>

                <!-- Remove Section Button (not for first row) -->
                <div v-if="index > 0" class="mb-3">
                    <button
                        type="button"
                        @click="removeSection(index)"
                        class="btn btn-sm btn-icon-square btn-icon-square-38"
                        style="background-color: #fa8b0c;"
                        title="Remove Section"
                    >
                        <X :size="18" color="white" />
                    </button>
                </div>
            </div>
            
            <!-- Add Section Button (always at bottom) -->
            <div class="mb-3">
                <button
                    type="button"
                    @click="addSection"
                    class="btn btn-primary btn-sm btn-icon-square btn-icon-square-38"
                    style="background-color: #1c467b; border-color: #1c467b;"
                    title="Add Section"
                >
                    <Plus :size="18" color="white" />
                </button>
            </div>
            
            <!-- Separator Line after Plus Button -->
            <div style="border-top: 1px solid #e5e7eb; margin-top: 1rem; margin-bottom: 1.5rem;"></div>
        </div>

        <!-- Facilities Section (Second Section) -->
        <div class="mb-4" style="padding-top: 1.5rem;">
            <h5 class="mb-3 section-title">Facilities</h5>
            
            <!-- Add Field Input -->
            <div class="mb-3">
                <div class="input-group input-group-sm">
                    <input
                        id="new_facility_field_name"
                        v-model="newFacilityFieldName"
                        type="text"
                        class="form-control form-control-sm"
                        placeholder="Field Name"
                        @keyup.enter="addFacility"
                        style="border: 1px solid #1c467b;"
                    />
                    <button
                        type="button"
                        @click="addFacility"
                        class="btn btn-secondary btn-sm btn-icon-square btn-icon-square-38"
                        style="background-color: #6c757d; border-color: #6c757d; border-left: 0;"
                        title="Add Field"
                    >
                        <Plus :size="18" color="white" />
                    </button>
                </div>
            </div>

            <!-- Pre-defined Utility and Board Information -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check mb-2">
                        <input
                            id="pollution_control_board_check"
                            v-model="form.utility_board.pollution_control_board.checked"
                            type="checkbox"
                            class="form-check-input"
                        />
                        <label for="pollution_control_board_check" class="form-check-label">
                            Pollution Control Board
                        </label>
                    </div>
                    <input
                        id="pollution_control_board_value"
                        v-model="form.utility_board.pollution_control_board.value"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors['utility_board.pollution_control_board.value'] }"
                        placeholder="Pollution Control Board"
                        :disabled="!form.utility_board.pollution_control_board.checked"
                    />
                    <div v-if="errors['utility_board.pollution_control_board.value']" class="invalid-feedback d-block">
                        {{ errors['utility_board.pollution_control_board.value'][0] }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check mb-2">
                        <input
                            id="ec_check"
                            v-model="form.utility_board.ec.checked"
                            type="checkbox"
                            class="form-check-input"
                        />
                        <label for="ec_check" class="form-check-label">
                            EC
                        </label>
                    </div>
                    <input
                        id="ec_value"
                        v-model="form.utility_board.ec.value"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors['utility_board.ec.value'] }"
                        placeholder="EC"
                        :disabled="!form.utility_board.ec.checked"
                    />
                    <div v-if="errors['utility_board.ec.value']" class="invalid-feedback d-block">
                        {{ errors['utility_board.ec.value'][0] }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check mb-2">
                        <input
                            id="gas_check"
                            v-model="form.utility_board.gas.checked"
                            type="checkbox"
                            class="form-check-input"
                        />
                        <label for="gas_check" class="form-check-label">
                            Gas
                        </label>
                    </div>
                    <input
                        id="gas_value"
                        v-model="form.utility_board.gas.value"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors['utility_board.gas.value'] }"
                        placeholder="Gas"
                        :disabled="!form.utility_board.gas.checked"
                    />
                    <div v-if="errors['utility_board.gas.value']" class="invalid-feedback d-block">
                        {{ errors['utility_board.gas.value'][0] }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check mb-2">
                        <input
                            id="power_check"
                            v-model="form.utility_board.power.checked"
                            type="checkbox"
                            class="form-check-input"
                        />
                        <label for="power_check" class="form-check-label">
                            Power
                        </label>
                    </div>
                    <input
                        id="power_value"
                        v-model="form.utility_board.power.value"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors['utility_board.power.value'] }"
                        placeholder="Power"
                        :disabled="!form.utility_board.power.checked"
                    />
                    <div v-if="errors['utility_board.power.value']" class="invalid-feedback d-block">
                        {{ errors['utility_board.power.value'][0] }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check mb-2">
                        <input
                            id="water_check"
                            v-model="form.utility_board.water.checked"
                            type="checkbox"
                            class="form-check-input"
                        />
                        <label for="water_check" class="form-check-label">
                            Water
                        </label>
                    </div>
                    <input
                        id="water_value"
                        v-model="form.utility_board.water.value"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors['utility_board.water.value'] }"
                        placeholder="Water"
                        :disabled="!form.utility_board.water.checked"
                    />
                    <div v-if="errors['utility_board.water.value']" class="invalid-feedback d-block">
                        {{ errors['utility_board.water.value'][0] }}
                    </div>
                </div>
            </div>

            <!-- Dynamically Added Facilities (after Water) -->
            <div v-if="form.dynamic_facilities && form.dynamic_facilities.length > 0" class="row">
                <div 
                    v-for="(facility, index) in form.dynamic_facilities" 
                    :key="facility.id" 
                    class="col-md-6 mb-3"
                >
                    <div class="form-check mb-2">
                        <input
                            :id="`dynamic_facility_check_${facility.id}`"
                            v-model="facility.checked"
                            type="checkbox"
                            class="form-check-input"
                        />
                        <label :for="`dynamic_facility_check_${facility.id}`" class="form-check-label">
                            {{ facility.label }}
                        </label>
                    </div>
                    <div class="input-group input-group-sm">
                        <input
                            :id="`dynamic_facility_value_${facility.id}`"
                            v-model="facility.value"
                            type="text"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors[`dynamic_facilities.${index}.value`] }"
                            :placeholder="facility.label"
                            :disabled="!facility.checked"
                        />
                        <button
                            type="button"
                            @click="removeFacility(index)"
                            class="btn btn-sm btn-icon-square btn-icon-square-38"
                            style="background-color: #fa8b0c;"
                            title="Remove Field"
                        >
                            <X :size="18" color="white" />
                        </button>
                    </div>
                    <div v-if="errors[`dynamic_facilities.${index}.value`]" class="invalid-feedback d-block">
                        {{ errors[`dynamic_facilities.${index}.value`][0] }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

