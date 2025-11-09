<script setup lang="ts">
interface Props {
    form: any;
    errors: any;
    measurementUnits: any[];
    condition: {
        constructionTypeId: string;
        categoryId: string;
        subCategoryId: string;
    };
}

const props = defineProps<Props>();

const toggleCarpetArea = (index: number) => {
    props.form.tower_details[index].show_carpet_area = !props.form.tower_details[index].show_carpet_area;
    if (!props.form.tower_details[index].show_carpet_area) {
        props.form.tower_details[index].carpet_area_from = '';
        props.form.tower_details[index].carpet_area_to = '';
        props.form.tower_details[index].carpet_area_unit_id = '';
    }
};

const toggleBuiltupArea = (index: number) => {
    props.form.tower_details[index].show_builtup_area = !props.form.tower_details[index].show_builtup_area;
    if (!props.form.tower_details[index].show_builtup_area) {
        props.form.tower_details[index].builtup_area_from = '';
        props.form.tower_details[index].builtup_area_to = '';
        props.form.tower_details[index].builtup_area_unit_id = '';
    }
};

const handleIntegerInput = (event: Event, field: string) => {
    const target = event.target as HTMLInputElement;
    const value = target.value;
    const integerValue = value.replace(/[^0-9]/g, '');
    
    // Enforce max value of 15 for no_of_towers
    if (field === 'no_of_towers') {
        const numValue = parseInt(integerValue) || 0;
        if (numValue > 15) {
            target.value = '15';
            (props.form as any)[field] = '15';
            return;
        }
    }
    
    if (value !== integerValue) {
        target.value = integerValue;
        (props.form as any)[field] = integerValue;
    }
};

// Check if condition matches
const isConditionMatch = () => {
    return props.form.construction_type_id === props.condition.constructionTypeId &&
           props.form.category_id === props.condition.categoryId &&
           props.form.sub_category_id === props.condition.subCategoryId;
};

// Get displayed towers based on checkbox
const getDisplayedTowers = () => {
    if (props.form.towers_different_specification) {
        return props.form.tower_details;
    }
    return props.form.tower_details.slice(0, 1);
};
</script>

<template>
    <div v-if="isConditionMatch()">
        <!-- Tower Details -->
        <div class="mb-3">
            <h5 class="mb-3 section-title">Tower Details</h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="no_of_towers" class="form-label">No. Of Towers</label>
                    <input
                        id="no_of_towers"
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
                    <label for="no_of_floors" class="form-label">No. Of Floors</label>
                    <input
                        id="no_of_floors"
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
                    <label for="total_units" class="form-label">Total Units</label>
                    <input
                        id="total_units"
                        v-model="form.total_units"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.total_units }"
                        placeholder="Enter total units"
                        @input="form.total_units = form.total_units.replace(/[^0-9]/g, '')"
                    />
                    <div v-if="errors.total_units" class="invalid-feedback d-block">
                        {{ errors.total_units[0] }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="no_of_unit_each_tower" class="form-label">No. Of Unit Each Tower</label>
                    <input
                        id="no_of_unit_each_tower"
                        v-model="form.no_of_unit_each_tower"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.no_of_unit_each_tower }"
                        placeholder="Enter units per tower"
                        @input="form.no_of_unit_each_tower = form.no_of_unit_each_tower.replace(/[^0-9]/g, '')"
                    />
                    <div v-if="errors.no_of_unit_each_tower" class="invalid-feedback d-block">
                        {{ errors.no_of_unit_each_tower[0] }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="no_of_lift" class="form-label">No. Of Lift</label>
                    <input
                        id="no_of_lift"
                        v-model="form.no_of_lift"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.no_of_lift }"
                        placeholder="Enter number of lifts"
                        @input="form.no_of_lift = form.no_of_lift.replace(/[^0-9]/g, '')"
                    />
                    <div v-if="errors.no_of_lift" class="invalid-feedback d-block">
                        {{ errors.no_of_lift[0] }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="front_road_width" class="form-label">Front Road Width <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <input
                            id="front_road_width"
                            v-model="form.front_road_width"
                            type="number"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors.front_road_width }"
                            placeholder="Enter road width"
                            required
                            style="border: 1px solid #1c467b;"
                        />
                        <select
                            id="front_road_width_measurement_unit_id"
                            v-model="form.front_road_width_measurement_unit_id"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': errors.front_road_width_measurement_unit_id }"
                            style="border: 1px solid #1c467b;"
                        >
                            <option value="">Select Unit</option>
                            <option 
                                v-for="unit in measurementUnits" 
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
                <div class="d-flex flex-wrap gap-3">
                    <div class="form-check form-check-inline construction-type-radio">
                        <input
                            id="washroom_private"
                            v-model="form.washroom"
                            type="radio"
                            value="Private"
                            class="form-check-input"
                            :class="{ 'is-invalid': errors.washroom }"
                        />
                        <label 
                            for="washroom_private"
                            class="form-check-label construction-type-label"
                        >
                            Private
                        </label>
                    </div>
                    <div class="form-check form-check-inline construction-type-radio">
                        <input
                            id="washroom_public"
                            v-model="form.washroom"
                            type="radio"
                            value="Public"
                            class="form-check-input"
                            :class="{ 'is-invalid': errors.washroom }"
                        />
                        <label 
                            for="washroom_public"
                            class="form-check-label construction-type-label"
                        >
                            Public
                        </label>
                    </div>
                </div>
                <div v-if="errors.washroom" class="invalid-feedback d-block">
                    {{ errors.washroom[0] }}
                </div>
            </div>

            <!-- Tower Details Form (shown after washroom is selected) -->
            <div v-if="form.washroom && form.tower_details.length > 0" class="mt-4" style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                <div v-for="(tower, index) in getDisplayedTowers()" :key="index" class="mb-4 pb-3" :class="{ 'border-bottom': index < getDisplayedTowers().length - 1 }" style="border-bottom: 1px solid #e5e7eb;">
                    <h6 v-if="form.towers_different_specification" class="mb-3 text-primary">Tower {{ index + 1 }}</h6>
                    
                    <div class="row">
                        <!-- Tower Name -->
                        <div class="col-md-3 mb-3">
                            <label :for="`tower_name_${index}`" class="form-label">Tower Name</label>
                            <input
                                :id="`tower_name_${index}`"
                                v-model="tower.tower_name"
                                type="text"
                                class="form-control form-control-sm"
                                :class="{ 'is-invalid': errors[`tower_details.${index}.tower_name`] }"
                                placeholder="Enter tower name"
                            />
                            <div v-if="errors[`tower_details.${index}.tower_name`]" class="invalid-feedback d-block">
                                {{ errors[`tower_details.${index}.tower_name`][0] }}
                            </div>
                        </div>

                        <!-- Saleable Area -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Saleable Area</label>
                            <div class="input-group input-group-sm">
                                <input
                                    :id="`saleable_area_from_${index}`"
                                    v-model="tower.saleable_area_from"
                                    type="number"
                                    class="form-control form-control-sm"
                                    :class="{ 'is-invalid': errors[`tower_details.${index}.saleable_area_from`] }"
                                    placeholder="From"
                                    style="border: 1px solid #1c467b;"
                                />
                                <input
                                    :id="`saleable_area_to_${index}`"
                                    v-model="tower.saleable_area_to"
                                    type="number"
                                    class="form-control form-control-sm"
                                    :class="{ 'is-invalid': errors[`tower_details.${index}.saleable_area_to`] }"
                                    placeholder="To"
                                    style="border: 1px solid #1c467b;"
                                />
                                <select
                                    :id="`saleable_area_unit_${index}`"
                                    v-model="tower.saleable_area_unit_id"
                                    class="form-select form-select-sm"
                                    :class="{ 'is-invalid': errors[`tower_details.${index}.saleable_area_unit_id`] }"
                                    style="border: 1px solid #1c467b;"
                                >
                                    <option value="">Select Unit</option>
                                    <option 
                                        v-for="unit in measurementUnits" 
                                        :key="unit.id" 
                                        :value="String(unit.id)"
                                    >
                                        {{ unit.name }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="errors[`tower_details.${index}.saleable_area_from`]" class="invalid-feedback d-block">
                                {{ errors[`tower_details.${index}.saleable_area_from`][0] }}
                            </div>
                            <div v-if="errors[`tower_details.${index}.saleable_area_to`]" class="invalid-feedback d-block">
                                {{ errors[`tower_details.${index}.saleable_area_to`][0] }}
                            </div>
                            <div v-if="errors[`tower_details.${index}.saleable_area_unit_id`]" class="invalid-feedback d-block">
                                {{ errors[`tower_details.${index}.saleable_area_unit_id`][0] }}
                            </div>
                        </div>

                        <!-- Ceiling Height -->
                        <div class="col-md-3 mb-3">
                            <label :for="`ceiling_height_${index}`" class="form-label">Ceiling Height</label>
                            <div class="input-group input-group-sm">
                                <input
                                    :id="`ceiling_height_${index}`"
                                    v-model="tower.ceiling_height"
                                    type="number"
                                    class="form-control form-control-sm"
                                    :class="{ 'is-invalid': errors[`tower_details.${index}.ceiling_height`] }"
                                    placeholder="Height"
                                    style="border: 1px solid #1c467b;"
                                />
                                <select
                                    :id="`ceiling_height_unit_${index}`"
                                    v-model="tower.ceiling_height_unit_id"
                                    class="form-select form-select-sm"
                                    :class="{ 'is-invalid': errors[`tower_details.${index}.ceiling_height_unit_id`] }"
                                    style="border: 1px solid #1c467b;"
                                >
                                    <option value="">Select Unit</option>
                                    <option 
                                        v-for="unit in measurementUnits" 
                                        :key="unit.id" 
                                        :value="String(unit.id)"
                                    >
                                        {{ unit.name }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="errors[`tower_details.${index}.ceiling_height`]" class="invalid-feedback d-block">
                                {{ errors[`tower_details.${index}.ceiling_height`][0] }}
                            </div>
                            <div v-if="errors[`tower_details.${index}.ceiling_height_unit_id`]" class="invalid-feedback d-block">
                                {{ errors[`tower_details.${index}.ceiling_height_unit_id`][0] }}
                            </div>
                        </div>
                    </div>

                    <!-- Action Links -->
                    <div class="mb-3">
                        <a 
                            v-if="!tower.show_carpet_area"
                            href="javascript:void(0)" 
                            @click="toggleCarpetArea(index)"
                            class="text-primary text-decoration-none me-3"
                            style="font-size: 14px;"
                        >
                            Add Carpet Area
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
                            v-if="!tower.show_builtup_area"
                            href="javascript:void(0)" 
                            @click="toggleBuiltupArea(index)"
                            class="text-primary text-decoration-none"
                            style="font-size: 14px;"
                        >
                            Add Builtup Area
                        </a>
                        <a 
                            v-else
                            href="javascript:void(0)" 
                            @click="toggleBuiltupArea(index)"
                            class="text-danger text-decoration-none"
                            style="font-size: 14px;"
                        >
                            Remove Builtup Area
                        </a>
                    </div>

                    <!-- Carpet Area and Builtup Area (Conditional) -->
                    <div v-if="tower.show_carpet_area || tower.show_builtup_area" class="row mb-3">
                        <!-- Carpet Area -->
                        <div v-if="tower.show_carpet_area" class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Carpet Area</label>
                            <div class="input-group input-group-sm">
                                <input
                                    :id="`carpet_area_from_${index}`"
                                    v-model="tower.carpet_area_from"
                                    type="number"
                                    class="form-control form-control-sm"
                                    :class="{ 'is-invalid': errors[`tower_details.${index}.carpet_area_from`] }"
                                    placeholder="Size"
                                    style="border: 1px solid #1c467b;"
                                />
                                <input
                                    :id="`carpet_area_to_${index}`"
                                    v-model="tower.carpet_area_to"
                                    type="number"
                                    class="form-control form-control-sm"
                                    :class="{ 'is-invalid': errors[`tower_details.${index}.carpet_area_to`] }"
                                    placeholder="Size"
                                    style="border: 1px solid #1c467b;"
                                />
                                <select
                                    :id="`carpet_area_unit_${index}`"
                                    v-model="tower.carpet_area_unit_id"
                                    class="form-select form-select-sm"
                                    :class="{ 'is-invalid': errors[`tower_details.${index}.carpet_area_unit_id`] }"
                                    style="border: 1px solid #1c467b;"
                                >
                                    <option value="">Select Unit</option>
                                    <option 
                                        v-for="unit in measurementUnits" 
                                        :key="unit.id" 
                                        :value="String(unit.id)"
                                    >
                                        {{ unit.name }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="errors[`tower_details.${index}.carpet_area_from`]" class="invalid-feedback d-block">
                                {{ errors[`tower_details.${index}.carpet_area_from`][0] }}
                            </div>
                            <div v-if="errors[`tower_details.${index}.carpet_area_to`]" class="invalid-feedback d-block">
                                {{ errors[`tower_details.${index}.carpet_area_to`][0] }}
                            </div>
                            <div v-if="errors[`tower_details.${index}.carpet_area_unit_id`]" class="invalid-feedback d-block">
                                {{ errors[`tower_details.${index}.carpet_area_unit_id`][0] }}
                            </div>
                        </div>

                        <!-- Builtup Area -->
                        <div v-if="tower.show_builtup_area" class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Builtup Area</label>
                            <div class="input-group input-group-sm">
                                <input
                                    :id="`builtup_area_from_${index}`"
                                    v-model="tower.builtup_area_from"
                                    type="number"
                                    class="form-control form-control-sm"
                                    :class="{ 'is-invalid': errors[`tower_details.${index}.builtup_area_from`] }"
                                    placeholder="Size"
                                    style="border: 1px solid #1c467b;"
                                />
                                <input
                                    :id="`builtup_area_to_${index}`"
                                    v-model="tower.builtup_area_to"
                                    type="number"
                                    class="form-control form-control-sm"
                                    :class="{ 'is-invalid': errors[`tower_details.${index}.builtup_area_to`] }"
                                    placeholder="Size"
                                    style="border: 1px solid #1c467b;"
                                />
                                <select
                                    :id="`builtup_area_unit_${index}`"
                                    v-model="tower.builtup_area_unit_id"
                                    class="form-select form-select-sm"
                                    :class="{ 'is-invalid': errors[`tower_details.${index}.builtup_area_unit_id`] }"
                                    style="border: 1px solid #1c467b;"
                                >
                                    <option value="">Select Unit</option>
                                    <option 
                                        v-for="unit in measurementUnits" 
                                        :key="unit.id" 
                                        :value="String(unit.id)"
                                    >
                                        {{ unit.name }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="errors[`tower_details.${index}.builtup_area_from`]" class="invalid-feedback d-block">
                                {{ errors[`tower_details.${index}.builtup_area_from`][0] }}
                            </div>
                            <div v-if="errors[`tower_details.${index}.builtup_area_to`]" class="invalid-feedback d-block">
                                {{ errors[`tower_details.${index}.builtup_area_to`][0] }}
                            </div>
                            <div v-if="errors[`tower_details.${index}.builtup_area_unit_id`]" class="invalid-feedback d-block">
                                {{ errors[`tower_details.${index}.builtup_area_unit_id`][0] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Towers with different specification checkbox -->
            <div class="mb-3 mt-4">
                <div class="form-check">
                    <input
                        id="towers_different_specification"
                        v-model="form.towers_different_specification"
                        type="checkbox"
                        class="form-check-input"
                    />
                    <label for="towers_different_specification" class="form-check-label">
                        Towers with a different specification.
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>

