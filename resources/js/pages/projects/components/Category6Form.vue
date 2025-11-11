<script setup lang="ts">
import { ref, computed } from 'vue';
import { Plus } from 'lucide-vue-next';

interface Props {
    form: any;
    errors: any;
    measurementUnits: any[];
}

const props = defineProps<Props>();

// Filter measurement units for area fields (all except Ft. and Mt.)
const areaUnits = computed(() => {
    return props.measurementUnits.filter(unit => 
        unit.name !== 'Ft.' && unit.name !== 'Mt.'
    );
});

// Category 6 form structure
interface Category6Data {
    total_open_area: string;
    total_open_area_unit_id: string;
    total_no_of_plots: string;
    project_with_multiple_theme_phase: boolean;
    phase_name: string;
    plots_with_construction: boolean;
    saleable_plot_from: string;
    saleable_plot_to: string;
    saleable_plot_unit_id: string;
    show_carpet_plot_size: boolean;
    carpet_plot_from: string;
    carpet_plot_to: string;
    carpet_plot_unit_id: string;
    constructed_saleable_area_from: string;
    constructed_saleable_area_to: string;
    constructed_saleable_area_unit_id: string;
}

// Initialize category 6 data
const initializeCategory6Data = () => {
    if (!props.form.category6_data) {
        props.form.category6_data = {
            total_open_area: '',
            total_open_area_unit_id: '',
            total_no_of_plots: '',
            project_with_multiple_theme_phase: false,
            phase_name: '',
            plots_with_construction: false,
            saleable_plot_from: '',
            saleable_plot_to: '',
            saleable_plot_unit_id: '',
            show_carpet_plot_size: false,
            carpet_plot_from: '',
            carpet_plot_to: '',
            carpet_plot_unit_id: '',
            constructed_saleable_area_from: '',
            constructed_saleable_area_to: '',
            constructed_saleable_area_unit_id: '',
        };
    }
};

// Initialize on component mount
initializeCategory6Data();

// Toggle carpet plot size
const toggleCarpetPlotSize = () => {
    props.form.category6_data.show_carpet_plot_size = !props.form.category6_data.show_carpet_plot_size;
    if (!props.form.category6_data.show_carpet_plot_size) {
        props.form.category6_data.carpet_plot_from = '';
        props.form.category6_data.carpet_plot_to = '';
        props.form.category6_data.carpet_plot_unit_id = '';
    }
};
</script>

<template>
    <div>
        <!-- General Details -->
        <div class="mb-4" style="padding-top: 1rem;">
            <div class="row">
                <!-- Total Open Area -->
                <div class="col-md-4 mb-3">
                    <label for="category6_total_open_area" class="form-label">Total Open Area</label>
                    <div class="input-group input-group-sm">
                        <input
                            id="category6_total_open_area"
                            v-model="form.category6_data.total_open_area"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['category6_data.total_open_area'] }"
                            placeholder="Total Open Area"
                            name="total_open_area"
                            style="border: 1px solid #1c467b;"
                        />
                        <select
                            id="category6_total_open_area_unit"
                            v-model="form.category6_data.total_open_area_unit_id"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': errors['category6_data.total_open_area_unit_id'] }"
                            style="border: 1px solid #1c467b; border-left: 0;"
                            name="total_open_area_unit_id"
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
                    <div v-if="errors['category6_data.total_open_area']" class="invalid-feedback d-block">
                        {{ errors['category6_data.total_open_area'][0] }}
                    </div>
                    <div v-if="errors['category6_data.total_open_area_unit_id']" class="invalid-feedback d-block">
                        {{ errors['category6_data.total_open_area_unit_id'][0] }}
                    </div>
                </div>

                <!-- Total No. Of Plots -->
                <div class="col-md-4 mb-3">
                    <label for="category6_total_no_of_plots" class="form-label">Total No. Of Plots</label>
                    <input
                        id="category6_total_no_of_plots"
                        v-model="form.category6_data.total_no_of_plots"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors['category6_data.total_no_of_plots'] }"
                        placeholder="Total No. Of Plots"
                        name="total_no_of_plots"
                    />
                    <div v-if="errors['category6_data.total_no_of_plots']" class="invalid-feedback d-block">
                        {{ errors['category6_data.total_no_of_plots'][0] }}
                    </div>
                </div>
            </div>

            <!-- Checkboxes -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check">
                        <input
                            id="category6_project_with_multiple_theme_phase"
                            v-model="form.category6_data.project_with_multiple_theme_phase"
                            type="checkbox"
                            class="form-check-input"
                            name="project_with_multiple_theme_phase"
                        />
                        <label for="category6_project_with_multiple_theme_phase" class="form-check-label">
                            Project with multiple theme/phase
                        </label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check">
                        <input
                            id="category6_plots_with_construction"
                            v-model="form.category6_data.plots_with_construction"
                            type="checkbox"
                            class="form-check-input"
                            name="plots_with_construction"
                        />
                        <label for="category6_plots_with_construction" class="form-check-label">
                            Plots With Construction
                        </label>
                    </div>
                </div>
            </div>

            <!-- Phase Name (shown when first checkbox is checked) -->
            <div v-if="form.category6_data.project_with_multiple_theme_phase" class="row">
                <div class="col-md-4 mb-3">
                    <label for="category6_phase_name" class="form-label">Phase Name</label>
                    <input
                        id="category6_phase_name"
                        v-model="form.category6_data.phase_name"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors['category6_data.phase_name'] }"
                        placeholder="Phase Name"
                        name="phase_name"
                    />
                    <div v-if="errors['category6_data.phase_name']" class="invalid-feedback d-block">
                        {{ errors['category6_data.phase_name'][0] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Saleable Plot Size (shown when "Project with multiple theme/phase" is checked) -->
        <div v-if="form.category6_data.project_with_multiple_theme_phase" class="mb-4" style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Saleable Plot</label>
                    <div class="input-group input-group-sm">
                        <input
                            id="category6_saleable_plot_from"
                            v-model="form.category6_data.saleable_plot_from"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['category6_data.saleable_plot_from'] }"
                            placeholder="Saleable Plot From"
                            name="saleable_plot_from"
                            style="border: 1px solid #1c467b;"
                        />
                        <input
                            id="category6_saleable_plot_to"
                            v-model="form.category6_data.saleable_plot_to"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['category6_data.saleable_plot_to'] }"
                            placeholder="Saleable Plot To"
                            name="saleable_plot_to"
                            style="border: 1px solid #1c467b; border-left: 0;"
                        />
                        <select
                            id="category6_saleable_plot_unit"
                            v-model="form.category6_data.saleable_plot_unit_id"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': errors['category6_data.saleable_plot_unit_id'] }"
                            style="border: 1px solid #1c467b; border-left: 0;"
                            name="saleable_plot_unit_id"
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
                    <div v-if="errors['category6_data.saleable_plot_from']" class="invalid-feedback d-block">
                        {{ errors['category6_data.saleable_plot_from'][0] }}
                    </div>
                    <div v-if="errors['category6_data.saleable_plot_to']" class="invalid-feedback d-block">
                        {{ errors['category6_data.saleable_plot_to'][0] }}
                    </div>
                    <div v-if="errors['category6_data.saleable_plot_unit_id']" class="invalid-feedback d-block">
                        {{ errors['category6_data.saleable_plot_unit_id'][0] }}
                    </div>
                </div>
            </div>

            <!-- Carpet Plot Size Link (shown when first checkbox is checked) -->
            <div class="mb-3">
                <a 
                    v-if="!form.category6_data.show_carpet_plot_size"
                    href="javascript:void(0)" 
                    @click="toggleCarpetPlotSize"
                    class="text-primary text-decoration-none"
                    style="font-size: 14px;"
                >
                    + Carpet Plot Size
                </a>
                <a 
                    v-else
                    href="javascript:void(0)" 
                    @click="toggleCarpetPlotSize"
                    class="text-danger text-decoration-none"
                    style="font-size: 14px;"
                >
                    Remove Carpet Plot Size
                </a>
            </div>

            <!-- Carpet Plot Size Fields (shown when link is clicked) -->
            <div v-if="form.category6_data.show_carpet_plot_size" class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Carpet Plot Size</label>
                    <div class="input-group input-group-sm">
                        <input
                            id="category6_carpet_plot_from"
                            v-model="form.category6_data.carpet_plot_from"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['category6_data.carpet_plot_from'] }"
                            placeholder="Carpet Plot From"
                            name="carpet_plot_from"
                            style="border: 1px solid #1c467b;"
                        />
                        <input
                            id="category6_carpet_plot_to"
                            v-model="form.category6_data.carpet_plot_to"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['category6_data.carpet_plot_to'] }"
                            placeholder="Carpet Plot To"
                            name="carpet_plot_to"
                            style="border: 1px solid #1c467b; border-left: 0;"
                        />
                        <select
                            id="category6_carpet_plot_unit"
                            v-model="form.category6_data.carpet_plot_unit_id"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': errors['category6_data.carpet_plot_unit_id'] }"
                            style="border: 1px solid #1c467b; border-left: 0;"
                            name="carpet_plot_unit_id"
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
                    <div v-if="errors['category6_data.carpet_plot_from']" class="invalid-feedback d-block">
                        {{ errors['category6_data.carpet_plot_from'][0] }}
                    </div>
                    <div v-if="errors['category6_data.carpet_plot_to']" class="invalid-feedback d-block">
                        {{ errors['category6_data.carpet_plot_to'][0] }}
                    </div>
                    <div v-if="errors['category6_data.carpet_plot_unit_id']" class="invalid-feedback d-block">
                        {{ errors['category6_data.carpet_plot_unit_id'][0] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Constructed Saleable Area (shown when second checkbox is checked) -->
        <div v-if="form.category6_data.plots_with_construction" class="mb-4" style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Constructed Saleable Area</label>
                    <div class="input-group input-group-sm">
                        <input
                            id="category6_constructed_saleable_area_from"
                            v-model="form.category6_data.constructed_saleable_area_from"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['category6_data.constructed_saleable_area_from'] }"
                            placeholder="Constructed Saleable Area From"
                            name="constructed_saleable_area_from"
                            style="border: 1px solid #1c467b;"
                        />
                        <input
                            id="category6_constructed_saleable_area_to"
                            v-model="form.category6_data.constructed_saleable_area_to"
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors['category6_data.constructed_saleable_area_to'] }"
                            placeholder="Constructed Saleable Area To"
                            name="constructed_saleable_area_to"
                            style="border: 1px solid #1c467b; border-left: 0;"
                        />
                        <select
                            id="category6_constructed_saleable_area_unit"
                            v-model="form.category6_data.constructed_saleable_area_unit_id"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': errors['category6_data.constructed_saleable_area_unit_id'] }"
                            style="border: 1px solid #1c467b; border-left: 0;"
                            name="constructed_saleable_area_unit_id"
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
                    <div v-if="errors['category6_data.constructed_saleable_area_from']" class="invalid-feedback d-block">
                        {{ errors['category6_data.constructed_saleable_area_from'][0] }}
                    </div>
                    <div v-if="errors['category6_data.constructed_saleable_area_to']" class="invalid-feedback d-block">
                        {{ errors['category6_data.constructed_saleable_area_to'][0] }}
                    </div>
                    <div v-if="errors['category6_data.constructed_saleable_area_unit_id']" class="invalid-feedback d-block">
                        {{ errors['category6_data.constructed_saleable_area_unit_id'][0] }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

