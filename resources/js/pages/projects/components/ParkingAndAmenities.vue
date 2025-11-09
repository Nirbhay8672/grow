<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Plus, X } from 'lucide-vue-next';

interface Amenity {
    id: number;
    name: string;
    is_active: boolean;
}

interface BasementParking {
    floor_no: string;
    ev_charging_point: string;
    hydraulic_parking: string;
    height_of_basement: string;
    height_of_basement_unit_id: string;
}

interface DocumentUpload {
    id: number;
    category: string;
    files: File[];
    uploaded_files?: Array<{
        id?: number;
        name: string;
        url?: string;
    }>;
}

interface Props {
    form: any;
    errors: any;
    amenities: Amenity[];
    measurementUnits: any[];
}

const props = defineProps<Props>();

// Create local reactive errors
const localErrors = ref<Record<string, string[]>>({});

// Merge parent errors with local validation errors
const allErrors = computed(() => {
    return { ...props.errors, ...localErrors.value };
});

// Document categories
const documentCategories = [
    { value: 'Fact sheet', label: 'Fact sheet' },
    { value: 'Area sheet', label: 'Area sheet' },
    { value: 'Price sheet', label: 'Price sheet' },
    { value: 'Images', label: 'Images' },
    { value: 'Other', label: 'Other' },
];

// Initialize document uploads array
if (!props.form.document_uploads || props.form.document_uploads.length === 0) {
    props.form.document_uploads = [{
        id: 1,
        category: '',
        files: [],
        uploaded_files: [],
    }];
}

let documentUploadIdCounter = props.form.document_uploads.length > 0 
    ? Math.max(...props.form.document_uploads.map((d: DocumentUpload) => d.id)) + 1 
    : 2;

// Initialize basement parking array
if (!props.form.basement_parking || props.form.basement_parking.length === 0) {
    props.form.basement_parking = [{
        floor_no: 'Floor 1',
        ev_charging_point: '',
        hydraulic_parking: '',
        height_of_basement: '',
        height_of_basement_unit_id: '',
    }];
}

// Initialize amenity_ids if not exists
if (!props.form.amenity_ids) {
    props.form.amenity_ids = [];
}

// Watch for total floor for parking changes
watch(() => props.form.total_floor_for_parking, (newValue) => {
    const numFloors = parseInt(newValue) || 1;
    const currentLength = props.form.basement_parking.length;
    
    if (numFloors > currentLength) {
        // Add new basement parking entries
        for (let i = currentLength; i < numFloors; i++) {
            props.form.basement_parking.push({
                floor_no: `Floor ${i + 1}`,
                ev_charging_point: '',
                hydraulic_parking: '',
                height_of_basement: '',
                height_of_basement_unit_id: '',
            });
        }
    } else if (numFloors < currentLength && numFloors > 0) {
        // Remove excess entries
        props.form.basement_parking = props.form.basement_parking.slice(0, numFloors);
    }
    
    // Update floor numbers for all entries
    props.form.basement_parking.forEach((parking: any, index: number) => {
        if (!parking.floor_no || parking.floor_no === '') {
            parking.floor_no = `Floor ${index + 1}`;
        }
    });
    
    // Always ensure at least one entry
    if (props.form.basement_parking.length === 0) {
        props.form.basement_parking.push({
            floor_no: 'Floor 1',
            ev_charging_point: '',
            hydraulic_parking: '',
            height_of_basement: '',
            height_of_basement_unit_id: '',
        });
    }
}, { immediate: false });

// Add document upload field
const addDocumentUpload = () => {
    props.form.document_uploads.push({
        id: documentUploadIdCounter++,
        category: '',
        files: [],
        uploaded_files: [],
    });
};

// Remove document upload field
const removeDocumentUpload = (id: number) => {
    if (props.form.document_uploads.length > 1) {
        props.form.document_uploads = props.form.document_uploads.filter((doc: DocumentUpload) => doc.id !== id);
    }
};

// Handle file selection for document uploads
const handleDocumentFiles = (event: Event, docIndex: number) => {
    const target = event.target as HTMLInputElement;
    if (target.files) {
        const filesArray = Array.from(target.files);
        props.form.document_uploads[docIndex].files = [
            ...(props.form.document_uploads[docIndex].files || []),
            ...filesArray
        ];
    }
};

// Remove file from document upload
const removeDocumentFile = (docIndex: number, fileIndex: number) => {
    props.form.document_uploads[docIndex].files.splice(fileIndex, 1);
};

// Remove uploaded file
const removeUploadedFile = (docIndex: number, fileIndex: number) => {
    if (props.form.document_uploads[docIndex].uploaded_files) {
        props.form.document_uploads[docIndex].uploaded_files.splice(fileIndex, 1);
    }
};

// Handle brochure file selection
const handleBrochureFile = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB in bytes
        
        if (file.size > maxSize) {
            localErrors.value.brochure_file = ['Brochure file size must be less than 2MB'];
            target.value = '';
            props.form.brochure_file = null;
            return;
        }
        
        // Clear error if file is valid
        if (localErrors.value.brochure_file) {
            delete localErrors.value.brochure_file;
        }
        
        props.form.brochure_file = file;
    }
};

// Handle integer input
const handleIntegerInput = (event: Event, field: string) => {
    const target = event.target as HTMLInputElement;
    const value = target.value;
    const integerValue = value.replace(/[^0-9]/g, '');
    
    // Enforce max value of 5 for total_floor_for_parking
    if (field === 'total_floor_for_parking') {
        const numValue = parseInt(integerValue) || 0;
        if (numValue > 5) {
            target.value = '5';
            (props.form as any)[field] = '5';
            return;
        }
    }
    
    if (value !== integerValue) {
        target.value = integerValue;
        (props.form as any)[field] = integerValue;
    }
};
</script>

<template>
    <div class="mb-4">
        <!-- Parking Details -->
        <div class="mb-4">
            <h5 class="mb-3 section-title">Parking Details</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-check">
                        <input
                            id="free_allotted_parking_four_wheeler"
                            v-model="form.free_allotted_parking_four_wheeler"
                            type="checkbox"
                            class="form-check-input"
                        />
                        <label for="free_allotted_parking_four_wheeler" class="form-check-label">
                            Free Allotted Parking For Four Wheeler
                        </label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-check">
                        <input
                            id="free_allotted_parking_two_wheeler"
                            v-model="form.free_allotted_parking_two_wheeler"
                            type="checkbox"
                            class="form-check-input"
                        />
                        <label for="free_allotted_parking_two_wheeler" class="form-check-label">
                            Free Allotted Parking For Two Wheeler
                        </label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-check">
                        <input
                            id="available_for_purchase"
                            v-model="form.available_for_purchase"
                            type="checkbox"
                            class="form-check-input"
                        />
                        <label for="available_for_purchase" class="form-check-label">
                            Available For Purchase
                        </label>
                    </div>
                </div>
            </div>
            <!-- No. Of Parking - Show only when Available For Purchase is checked -->
            <div v-if="form.available_for_purchase" class="row mt-3">
                <div class="col-md-4">
                    <label for="no_of_parking" class="form-label">No. Of Parking</label>
                    <input
                        id="no_of_parking"
                        v-model="form.no_of_parking"
                        type="number"
                        step="1"
                        min="0"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': allErrors.no_of_parking }"
                        placeholder="Enter number of parking"
                        @input="handleIntegerInput($event, 'no_of_parking')"
                    />
                    <div v-if="allErrors.no_of_parking" class="invalid-feedback d-block">
                        {{ allErrors.no_of_parking[0] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Basement Parking -->
        <div class="mb-4">
            <h5 class="mb-3 section-title">Basement Parking</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="total_floor_for_parking" class="form-label">Total Floor For Parking</label>
                    <input
                        id="total_floor_for_parking"
                        v-model="form.total_floor_for_parking"
                        type="number"
                        step="1"
                        min="1"
                        max="5"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': allErrors.total_floor_for_parking }"
                        placeholder="Enter total floors (max 5)"
                        @input="handleIntegerInput($event, 'total_floor_for_parking')"
                    />
                    <div v-if="allErrors.total_floor_for_parking" class="invalid-feedback d-block">
                        {{ allErrors.total_floor_for_parking[0] }}
                    </div>
                </div>
            </div>

            <!-- Basement Parking Array -->
            <div 
                v-for="(parking, index) in form.basement_parking" 
                :key="index" 
                class="row mb-3 pb-3"
                :class="{ 'border-bottom': index < form.basement_parking.length - 1 }"
                style="border-bottom: 1px solid #e5e7eb;"
            >
                <div class="col-md-3 mb-3">
                    <label :for="`floor_no_${index}`" class="form-label">Floor No.</label>
                    <input
                        :id="`floor_no_${index}`"
                        v-model="parking.floor_no"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': allErrors[`basement_parking.${index}.floor_no`] }"
                        :placeholder="`Floor ${index + 1}`"
                    />
                    <div v-if="allErrors[`basement_parking.${index}.floor_no`]" class="invalid-feedback d-block">
                        {{ allErrors[`basement_parking.${index}.floor_no`][0] }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label :for="`ev_charging_point_${index}`" class="form-label">EV Charging Point</label>
                    <input
                        :id="`ev_charging_point_${index}`"
                        v-model="parking.ev_charging_point"
                        type="number"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': allErrors[`basement_parking.${index}.ev_charging_point`] }"
                        placeholder="Enter number"
                    />
                    <div v-if="allErrors[`basement_parking.${index}.ev_charging_point`]" class="invalid-feedback d-block">
                        {{ allErrors[`basement_parking.${index}.ev_charging_point`][0] }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label :for="`hydraulic_parking_${index}`" class="form-label">Hydraulic Parking</label>
                    <input
                        :id="`hydraulic_parking_${index}`"
                        v-model="parking.hydraulic_parking"
                        type="number"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': allErrors[`basement_parking.${index}.hydraulic_parking`] }"
                        placeholder="Enter number"
                    />
                    <div v-if="allErrors[`basement_parking.${index}.hydraulic_parking`]" class="invalid-feedback d-block">
                        {{ allErrors[`basement_parking.${index}.hydraulic_parking`][0] }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label :for="`height_of_basement_${index}`" class="form-label">Height Of Basement</label>
                    <div class="input-group input-group-sm">
                        <input
                            :id="`height_of_basement_${index}`"
                            v-model="parking.height_of_basement"
                            type="number"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': allErrors[`basement_parking.${index}.height_of_basement`] }"
                            placeholder="Enter height"
                            style="border: 1px solid #1c467b;"
                        />
                        <select
                            :id="`height_of_basement_unit_${index}`"
                            v-model="parking.height_of_basement_unit_id"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': allErrors[`basement_parking.${index}.height_of_basement_unit_id`] }"
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
                    <div v-if="allErrors[`basement_parking.${index}.height_of_basement`]" class="invalid-feedback d-block">
                        {{ allErrors[`basement_parking.${index}.height_of_basement`][0] }}
                    </div>
                    <div v-if="allErrors[`basement_parking.${index}.height_of_basement_unit_id`]" class="invalid-feedback d-block">
                        {{ allErrors[`basement_parking.${index}.height_of_basement_unit_id`][0] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Amenities -->
        <div class="mb-4">
            <h5 class="mb-3 section-title">Amenities</h5>
            <div class="row">
                <div 
                    v-for="amenity in amenities" 
                    :key="amenity.id" 
                    class="col-md-3 mb-3"
                >
                    <div class="form-check">
                        <input
                            :id="`amenity_${amenity.id}`"
                            v-model="form.amenity_ids"
                            type="checkbox"
                            :value="String(amenity.id)"
                            class="form-check-input"
                        />
                        <label :for="`amenity_${amenity.id}`" class="form-check-label">
                            {{ amenity.name }}
                        </label>
                    </div>
                </div>
            </div>
            <div v-if="allErrors.amenity_ids" class="invalid-feedback d-block">
                {{ allErrors.amenity_ids[0] }}
            </div>
        </div>

        <!-- Images/Documents -->
        <div class="mb-4">
            <h5 class="mb-3 section-title">Images/Documents</h5>
            <div 
                v-for="(doc, docIndex) in form.document_uploads" 
                :key="doc.id" 
                class="mb-4 pb-3"
                :class="{ 'border-bottom': docIndex < form.document_uploads.length - 1 }"
                style="border-bottom: 1px solid #e5e7eb;"
            >
                <div class="row align-items-start">
                    <div class="col-md-4 mb-3">
                        <label :for="`doc_category_${doc.id}`" class="form-label">Category</label>
                        <select
                            :id="`doc_category_${doc.id}`"
                            v-model="doc.category"
                            class="form-select form-select-sm"
                            :class="{ 'is-invalid': allErrors[`document_uploads.${docIndex}.category`] }"
                            style="height: 38px;"
                        >
                            <option value="">Select Category</option>
                            <option 
                                v-for="category in documentCategories" 
                                :key="category.value" 
                                :value="category.value"
                            >
                                {{ category.label }}
                            </option>
                        </select>
                        <div v-if="allErrors[`document_uploads.${docIndex}.category`]" class="invalid-feedback d-block">
                            {{ allErrors[`document_uploads.${docIndex}.category`][0] }}
                        </div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label :for="`doc_files_${doc.id}`" class="form-label">Files</label>
                        <input
                            :id="`doc_files_${doc.id}`"
                            type="file"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': allErrors[`document_uploads.${docIndex}.files`] }"
                            style="height: 38px;"
                            multiple
                            @change="handleDocumentFiles($event, docIndex)"
                        />
                        <div v-if="allErrors[`document_uploads.${docIndex}.files`]" class="invalid-feedback d-block">
                            {{ allErrors[`document_uploads.${docIndex}.files`][0] }}
                        </div>
                        <!-- Display selected files -->
                        <div v-if="doc.files && doc.files.length > 0" class="mt-2">
                            <div 
                                v-for="(file, fileIndex) in doc.files" 
                                :key="fileIndex"
                                class="d-flex align-items-center justify-content-between mb-1 p-2 rounded"
                                style="background-color: #1c467b; color: white;"
                            >
                                <span class="small text-white">{{ file.name }}</span>
                                <button
                                    type="button"
                                    @click="removeDocumentFile(docIndex, fileIndex)"
                                    class="btn btn-remove-square"
                                >
                                    <X :size="14" color="white" />
                                </button>
                            </div>
                        </div>
                        <!-- Display uploaded files -->
                        <div v-if="doc.uploaded_files && doc.uploaded_files.length > 0" class="mt-2">
                            <div 
                                v-for="(uploadedFile, fileIndex) in doc.uploaded_files" 
                                :key="fileIndex"
                                class="d-flex align-items-center justify-content-between mb-1 p-2 rounded"
                                style="background-color: #1c467b; color: white;"
                            >
                                <span class="small text-white">{{ uploadedFile.name }}</span>
                                <button
                                    type="button"
                                    @click="removeUploadedFile(docIndex, fileIndex)"
                                    class="btn btn-remove-square"
                                >
                                    <X :size="14" color="white" />
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label hidden-label">Action</label>
                        <div class="d-flex gap-1">
                            <button
                                v-if="docIndex === 0"
                                type="button"
                                @click="addDocumentUpload"
                                class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                                style="min-width: 40px; height: 38px; padding: 0 12px;"
                                title="Add Document Upload"
                            >
                                <Plus :size="18" color="white" />
                            </button>
                            <button
                                v-if="docIndex > 0"
                                type="button"
                                @click="removeDocumentUpload(doc.id)"
                                class="btn btn-sm d-flex align-items-center justify-content-center"
                                style="min-width: 40px; height: 38px; padding: 0 12px; background-color: #fa8b0c; border: none;"
                                title="Remove Document Upload"
                            >
                                <X :size="18" color="white" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brochure File -->
        <div class="mb-4">
            <h5 class="mb-3 section-title">Brochure File</h5>
            <div class="row">
                <div class="col-md-6">
                    <label for="brochure_file" class="form-label">Brochure File (Max 2MB)</label>
                    <input
                        id="brochure_file"
                        type="file"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': allErrors.brochure_file }"
                        accept=".pdf,.doc,.docx"
                        @change="handleBrochureFile"
                    />
                    <div v-if="allErrors.brochure_file" class="invalid-feedback d-block">
                        {{ allErrors.brochure_file[0] }}
                    </div>
                    <div v-if="form.brochure_file" class="mt-2 small text-muted">
                        Selected: {{ form.brochure_file.name }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Remark -->
        <div class="mb-4">
            <h5 class="mb-3 section-title">Enter Remark</h5>
            <div class="row">
                <div class="col-md-12">
                    <label for="remark" class="form-label">Remark</label>
                    <textarea
                        id="remark"
                        v-model="form.remark"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': allErrors.remark }"
                        rows="3"
                        placeholder="Enter any remarks or notes"
                    ></textarea>
                    <div v-if="allErrors.remark" class="invalid-feedback d-block">
                        {{ allErrors.remark[0] }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.hidden-label {
    visibility: hidden;
}
</style>

