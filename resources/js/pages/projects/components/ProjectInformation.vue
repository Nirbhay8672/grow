<script setup lang="ts">
interface State {
    id: number;
    name: string;
}

interface City {
    id: number;
    name: string;
}

interface Locality {
    id: number;
    name: string;
    zip_code?: string;
}

interface MeasurementUnit {
    id: number;
    name: string;
}

interface Props {
    states: State[];
    cities: City[];
    localities: Locality[];
    measurementUnits: MeasurementUnit[];
    form: {
        project_name: string;
        address: string;
        state_id: string;
        city_id: string;
        locality_id: string;
        pincode: string;
        location_link: string;
        land_size: string;
        measurement_unit_id: string;
        rera_no: string;
        project_status: string;
    };
    errors: Record<string, string[]>;
    validateForm: () => void;
}

defineProps<Props>();
</script>

<template>
    <div class="mb-4">
        <h5 class="mb-3 section-title">Project Information</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="project_name" class="form-label">
                        Project Name <span class="text-danger">*</span>
                    </label>
                    <input
                        id="project_name"
                        v-model="form.project_name"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.project_name }"
                        placeholder="Enter project name"
                        @blur="validateForm"
                        required
                    />
                    <div v-if="errors.project_name" class="invalid-feedback">
                        {{ errors.project_name[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="address" class="form-label">Address</label>
                    <textarea
                        id="address"
                        v-model="form.address"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.address }"
                        placeholder="Enter address"
                        rows="2"
                    ></textarea>
                    <div v-if="errors.address" class="invalid-feedback">
                        {{ errors.address[0] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="state_id" class="form-label">State</label>
                    <select
                        id="state_id"
                        v-model="form.state_id"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.state_id }"
                    >
                        <option value="">Select State</option>
                        <option 
                            v-for="state in states" 
                            :key="state.id" 
                            :value="state.id"
                        >
                            {{ state.name }}
                        </option>
                    </select>
                    <div v-if="errors.state_id" class="invalid-feedback">
                        {{ errors.state_id[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="city_id" class="form-label">City</label>
                    <select
                        id="city_id"
                        v-model="form.city_id"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.city_id }"
                        :disabled="!form.state_id"
                    >
                        <option value="">Select City</option>
                        <option 
                            v-for="city in cities" 
                            :key="city.id" 
                            :value="city.id"
                        >
                            {{ city.name }}
                        </option>
                    </select>
                    <div v-if="errors.city_id" class="invalid-feedback">
                        {{ errors.city_id[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="locality_id" class="form-label">
                        Locality <span class="text-danger">*</span>
                    </label>
                    <select
                        id="locality_id"
                        v-model="form.locality_id"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.locality_id }"
                        :disabled="!form.city_id"
                        @blur="validateForm"
                        @change="validateForm"
                        required
                    >
                        <option value="">Select Locality</option>
                        <option 
                            v-for="locality in localities" 
                            :key="locality.id" 
                            :value="locality.id"
                        >
                            {{ locality.name }}
                        </option>
                    </select>
                    <div v-if="errors.locality_id" class="invalid-feedback">
                        {{ errors.locality_id[0] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="pincode" class="form-label">Pincode</label>
                    <input
                        id="pincode"
                        v-model="form.pincode"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.pincode }"
                        placeholder="Enter pincode"
                    />
                    <div v-if="errors.pincode" class="invalid-feedback">
                        {{ errors.pincode[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="location_link" class="form-label">Location Link</label>
                    <input
                        id="location_link"
                        v-model="form.location_link"
                        type="url"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.location_link }"
                        placeholder="https://maps.google.com/..."
                        @blur="validateForm"
                    />
                    <div v-if="errors.location_link" class="invalid-feedback">
                        {{ errors.location_link[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="land_size" class="form-label">Land Size</label>
                    <div class="input-group input-group-sm">
                        <input
                            id="land_size"
                            v-model="form.land_size"
                            type="text"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors.land_size }"
                            placeholder="Enter land size"
                        />
                        <select
                            id="measurement_unit_id"
                            v-model="form.measurement_unit_id"
                            class="form-control form-control-sm measurement-unit-select"
                            :class="{ 'is-invalid': errors.measurement_unit_id }"
                        >
                            <option value="">Select Unit</option>
                            <option 
                                v-for="unit in measurementUnits" 
                                :key="unit.id" 
                                :value="unit.id"
                            >
                                {{ unit.name }}
                            </option>
                        </select>
                        <div v-if="errors.land_size || errors.measurement_unit_id" class="invalid-feedback d-block w-100">
                            <span v-if="errors.land_size">{{ errors.land_size[0] }}</span>
                            <span v-if="errors.measurement_unit_id">{{ errors.measurement_unit_id[0] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="rera_no" class="form-label">Rera No</label>
                    <input
                        id="rera_no"
                        v-model="form.rera_no"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.rera_no }"
                        placeholder="Enter RERA number"
                    />
                    <div v-if="errors.rera_no" class="invalid-feedback">
                        {{ errors.rera_no[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="project_status" class="form-label">Project Status</label>
                    <select
                        id="project_status"
                        v-model="form.project_status"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.project_status }"
                    >
                        <option value="">Select Project Status</option>
                        <option value="Ready Possession">Ready Possession</option>
                        <option value="Under Construction">Under Construction</option>
                    </select>
                    <div v-if="errors.project_status" class="invalid-feedback">
                        {{ errors.project_status[0] }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

