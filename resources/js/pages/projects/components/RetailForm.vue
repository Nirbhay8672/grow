<script setup lang="ts">
import { ref, computed } from 'vue';

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

// Initialize retail unit details
const initializeRetailUnitDetails = () => {
    if (!props.form.retail_unit_details || props.form.retail_unit_details.length === 0) {
        props.form.retail_unit_details = [{
            id: 1,
            tower_name: '',
            sub_category_id: '',
            size_from: '',
            size_to: '',
            size_unit_id: '',
            front_opening: '',
            front_opening_unit_id: '',
            no_of_unit_each_floor: '',
            ceiling_height: '',
            ceiling_height_unit_id: '',
        }];
    }
};

// Initialize on component mount
initializeRetailUnitDetails();

let retailUnitIdCounter = Math.max(...props.form.retail_unit_details.map((u: any) => u.id || 0), 0) + 1;

const addFloor = () => {
    props.form.retail_unit_details.push({
        id: retailUnitIdCounter++,
        tower_name: '',
        sub_category_id: '',
        size_from: '',
        size_to: '',
        size_unit_id: '',
        front_opening: '',
        front_opening_unit_id: '',
        no_of_unit_each_floor: '',
        ceiling_height: '',
        ceiling_height_unit_id: '',
    });
};

const removeFloor = (index: number) => {
    if (props.form.retail_unit_details.length > 1) {
        props.form.retail_unit_details.splice(index, 1);
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
</script>

<template>
    <div>
        <!-- Tower Details -->
        <div class="mb-4">
            <h5 class="mb-3 section-title">Tower Details</h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="retail_no_of_towers" class="form-label">No. Of Towers</label>
                    <input
                        id="retail_no_of_towers"
                        v-model="form.no_of_towers"
                        type="number"
                        step="1"
                        min="0"
                        max="15"
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
                    <label for="retail_no_of_floors" class="form-label">No. Of Floors</label>
                    <input
                        id="retail_no_of_floors"
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
                    <label for="retail_total_units" class="form-label">Total Units</label>
                    <input
                        id="retail_total_units"
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
                    <label for="retail_no_of_unit_each_tower" class="form-label">No. Of Unit Each Tower</label>
                    <input
                        id="retail_no_of_unit_each_tower"
                        v-model="form.no_of_unit_each_tower"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.no_of_unit_each_tower }"
                        placeholder="Enter units per tower"
                        @input="handleIntegerInput($event, 'no_of_unit_each_tower')"
                    />
                    <div v-if="errors.no_of_unit_each_tower" class="invalid-feedback d-block">
                        {{ errors.no_of_unit_each_tower[0] }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="retail_no_of_lift" class="form-label">No. Of Lift</label>
                    <input
                        id="retail_no_of_lift"
                        v-model="form.no_of_lift"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.no_of_lift }"
                        placeholder="Enter number of lifts"
                        @input="handleIntegerInput($event, 'no_of_lift')"
                    />
                    <div v-if="errors.no_of_lift" class="invalid-feedback d-block">
                        {{ errors.no_of_lift[0] }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="retail_front_road_width" class="form-label">Front Road Width</label>
                    <div class="input-group input-group-sm">
                        <input
                            id="retail_front_road_width"
                            v-model="form.front_road_width"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors.front_road_width }"
                            placeholder="Enter road width"
                            style="border: 1px solid #1c467b;"
                        />
                        <select
                            id="retail_front_road_width_measurement_unit_id"
                            v-model="form.front_road_width_measurement_unit_id"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': errors.front_road_width_measurement_unit_id }"
                            style="border: 1px solid #1c467b;"
                        >
                            <option value="">Select Unit</option>
                            <option 
                                v-for="unit in heightWidthUnits" 
                                :key="unit.id" 
                                :value="String(unit.id)"
                            >
                                {{ unit.name }}
                            </option>
                        </select>
                    </div>
                    <div v-if="errors.front_road_width" class="invalid-feedback d-block">
                        {{ errors.front_road_width[0] }}
                    </div>
                    <div v-if="errors.front_road_width_measurement_unit_id" class="invalid-feedback d-block">
                        {{ errors.front_road_width_measurement_unit_id[0] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Washroom -->
        <div class="mb-4">
            <h5 class="mb-3 section-title">Washroom</h5>
            <div class="form-group">
                <div class="d-flex gap-4">
                    <div class="form-check">
                        <input
                            id="retail_washroom_private"
                            v-model="form.washroom"
                            type="radio"
                            value="Private"
                            class="form-check-input"
                            :class="{ 'is-invalid': errors.washroom }"
                        />
                        <label for="retail_washroom_private" class="form-check-label">
                            Private
                        </label>
                    </div>
                    <div class="form-check">
                        <input
                            id="retail_washroom_public"
                            v-model="form.washroom"
                            type="radio"
                            value="Public"
                            class="form-check-input"
                            :class="{ 'is-invalid': errors.washroom }"
                        />
                        <label for="retail_washroom_public" class="form-check-label">
                            Public
                        </label>
                    </div>
                </div>
                <div class="form-check mt-3">
                    <input
                        id="retail_two_road_corner"
                        v-model="form.two_road_corner"
                        type="checkbox"
                        class="form-check-input"
                    />
                    <label for="retail_two_road_corner" class="form-check-label">
                        Two Road Corner.
                    </label>
                </div>
                <div v-if="errors.washroom" class="invalid-feedback d-block">
                    {{ errors.washroom[0] }}
                </div>
            </div>
        </div>

        <!-- Unit Details -->
        <div class="mb-4" style="border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
            <h5 class="mb-3 section-title">Unit Details</h5>
            <div v-for="(unit, index) in form.retail_unit_details" :key="unit.id" class="mb-4 pb-3" :class="{ 'border-bottom': index < form.retail_unit_details.length - 1 }" style="border-bottom: 1px solid #e5e7eb;">
                <div class="row">
                    <!-- Tower Name -->
                    <div class="col-md-3 mb-3">
                        <label :for="`retail_tower_name_${index}`" class="form-label">Tower Name</label>
                        <input
                            :id="`retail_tower_name_${index}`"
                            v-model="unit.tower_name"
                            type="text"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors[`retail_unit_details.${index}.tower_name`] }"
                            placeholder="Enter tower name"
                        />
                        <div v-if="errors[`retail_unit_details.${index}.tower_name`]" class="invalid-feedback d-block">
                            {{ errors[`retail_unit_details.${index}.tower_name`][0] }}
                        </div>
                    </div>

                    <!-- Sub Category -->
                    <div class="col-md-3 mb-3">
                        <label :for="`retail_sub_category_${index}`" class="form-label">Sub Category</label>
                        <select
                            :id="`retail_sub_category_${index}`"
                            v-model="unit.sub_category_id"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': errors[`retail_unit_details.${index}.sub_category_id`] }"
                            style="height: 38px;"
                        >
                            <option value="">Select Sub Category</option>
                            <option 
                                v-for="subCat in props.subCategories" 
                                :key="subCat.id" 
                                :value="String(subCat.id)"
                            >
                                {{ subCat.name }}
                            </option>
                        </select>
                        <div v-if="errors[`retail_unit_details.${index}.sub_category_id`]" class="invalid-feedback d-block">
                            {{ errors[`retail_unit_details.${index}.sub_category_id`][0] }}
                        </div>
                    </div>

                    <!-- Size From -->
                    <div class="col-md-3 mb-3">
                        <label :for="`retail_size_from_${index}`" class="form-label">Size</label>
                        <input
                            :id="`retail_size_from_${index}`"
                            v-model="unit.size_from"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors[`retail_unit_details.${index}.size_from`] }"
                            placeholder="From"
                        />
                        <div v-if="errors[`retail_unit_details.${index}.size_from`]" class="invalid-feedback d-block">
                            {{ errors[`retail_unit_details.${index}.size_from`][0] }}
                        </div>
                    </div>

                    <!-- Size To -->
                    <div class="col-md-3 mb-3">
                        <label :for="`retail_size_to_${index}`" class="form-label">&nbsp;</label>
                        <div class="input-group input-group-sm">
                            <input
                                :id="`retail_size_to_${index}`"
                                v-model="unit.size_to"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`retail_unit_details.${index}.size_to`] }"
                                placeholder="To"
                                style="border: 1px solid #1c467b;"
                            />
                            <select
                                :id="`retail_size_unit_${index}`"
                                v-model="unit.size_unit_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`retail_unit_details.${index}.size_unit_id`] }"
                                style="border: 1px solid #1c467b;"
                            >
                                <option value="">Select Unit</option>
                                <option 
                                    v-for="unitOption in areaUnits" 
                                    :key="unitOption.id" 
                                    :value="String(unitOption.id)"
                                >
                                    {{ unitOption.name }}
                                </option>
                            </select>
                        </div>
                        <div v-if="errors[`retail_unit_details.${index}.size_to`]" class="invalid-feedback d-block">
                            {{ errors[`retail_unit_details.${index}.size_to`][0] }}
                        </div>
                        <div v-if="errors[`retail_unit_details.${index}.size_unit_id`]" class="invalid-feedback d-block">
                            {{ errors[`retail_unit_details.${index}.size_unit_id`][0] }}
                        </div>
                    </div>

                    <!-- Front Opening -->
                    <div class="col-md-3 mb-3">
                        <label :for="`retail_front_opening_${index}`" class="form-label">Front Opening</label>
                        <div class="input-group input-group-sm">
                            <input
                                :id="`retail_front_opening_${index}`"
                                v-model="unit.front_opening"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`retail_unit_details.${index}.front_opening`] }"
                                placeholder="Enter front opening"
                                style="border: 1px solid #1c467b;"
                            />
                            <select
                                :id="`retail_front_opening_unit_${index}`"
                                v-model="unit.front_opening_unit_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`retail_unit_details.${index}.front_opening_unit_id`] }"
                                style="border: 1px solid #1c467b;"
                            >
                                <option value="">Select Unit</option>
                                <option 
                                    v-for="unitOption in heightWidthUnits" 
                                    :key="unitOption.id" 
                                    :value="String(unitOption.id)"
                                >
                                    {{ unitOption.name }}
                                </option>
                            </select>
                        </div>
                        <div v-if="errors[`retail_unit_details.${index}.front_opening`]" class="invalid-feedback d-block">
                            {{ errors[`retail_unit_details.${index}.front_opening`][0] }}
                        </div>
                        <div v-if="errors[`retail_unit_details.${index}.front_opening_unit_id`]" class="invalid-feedback d-block">
                            {{ errors[`retail_unit_details.${index}.front_opening_unit_id`][0] }}
                        </div>
                    </div>

                    <!-- No. Of Unit Each Floor -->
                    <div class="col-md-3 mb-3">
                        <label :for="`retail_no_of_unit_each_floor_${index}`" class="form-label">No. Of Unit Each Floor</label>
                        <input
                            :id="`retail_no_of_unit_each_floor_${index}`"
                            v-model="unit.no_of_unit_each_floor"
                            type="number"
                            step="1"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors[`retail_unit_details.${index}.no_of_unit_each_floor`] }"
                            placeholder="Enter units per floor"
                            @input="handleIntegerInput($event, 'no_of_unit_each_floor')"
                        />
                        <div v-if="errors[`retail_unit_details.${index}.no_of_unit_each_floor`]" class="invalid-feedback d-block">
                            {{ errors[`retail_unit_details.${index}.no_of_unit_each_floor`][0] }}
                        </div>
                    </div>

                    <!-- Ceiling Height -->
                    <div class="col-md-3 mb-3">
                        <label :for="`retail_ceiling_height_${index}`" class="form-label">Ceiling Height</label>
                        <div class="input-group input-group-sm">
                            <input
                                :id="`retail_ceiling_height_${index}`"
                                v-model="unit.ceiling_height"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`retail_unit_details.${index}.ceiling_height`] }"
                                placeholder="Height"
                                style="border: 1px solid #1c467b;"
                            />
                            <select
                                :id="`retail_ceiling_height_unit_${index}`"
                                v-model="unit.ceiling_height_unit_id"
                                class="form-select form-select-sm"
                                :class="{ 'is-invalid': errors[`retail_unit_details.${index}.ceiling_height_unit_id`] }"
                                style="border: 1px solid #1c467b;"
                            >
                                <option value="">Select Unit</option>
                                <option 
                                    v-for="unitOption in heightWidthUnits" 
                                    :key="unitOption.id" 
                                    :value="String(unitOption.id)"
                                >
                                    {{ unitOption.name }}
                                </option>
                            </select>
                        </div>
                        <div v-if="errors[`retail_unit_details.${index}.ceiling_height`]" class="invalid-feedback d-block">
                            {{ errors[`retail_unit_details.${index}.ceiling_height`][0] }}
                        </div>
                        <div v-if="errors[`retail_unit_details.${index}.ceiling_height_unit_id`]" class="invalid-feedback d-block">
                            {{ errors[`retail_unit_details.${index}.ceiling_height_unit_id`][0] }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Floor Button -->
            <button
                type="button"
                @click="addFloor"
                class="btn btn-primary btn-sm"
                style="background-color: #1c467b; border-color: #1c467b;"
            >
                + FLOOR
            </button>
        </div>
    </div>
</template>

