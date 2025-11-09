<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Plus, X } from 'lucide-vue-next';
import axios from 'axios';

// Get CSRF token from meta tag
const getCsrfToken = (): string => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    return token || '';
};

// Set default CSRF token for axios
axios.defaults.headers.common['X-CSRF-TOKEN'] = getCsrfToken();
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

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
    id?: number;
    category: string;
    files: File[];
    uploaded_files?: Array<{
        id?: number;
        name: string;
        url?: string;
    }>;
    existing_file_path?: string;
    existing_file_name?: string;
    existing_document_id?: number;
    existing_documents?: Array<{
        id: number;
        file_path: string;
        file_name: string;
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
const removeDocumentUpload = async (id: number) => {
    // Find the document index
    const docIndex = props.form.document_uploads.findIndex((doc: DocumentUpload) => doc.id === id);
    if (docIndex === -1) return;
    
    const doc = props.form.document_uploads[docIndex];
    
    // If it has existing documents, delete them from database first
    if (doc.existing_documents && doc.existing_documents.length > 0) {
        if (!confirm('Are you sure you want to delete this document row and all its files? This action cannot be undone.')) {
            return;
        }
        
        // Delete all existing documents in this row
        const deletePromises = doc.existing_documents.map((existingDoc: any) => 
            axios.delete(`/projects/documents/${existingDoc.id}`)
        );
        
        try {
            await Promise.all(deletePromises);
            // Remove the document from the array
            props.form.document_uploads.splice(docIndex, 1);
            
            // If no documents left, ensure at least one empty entry
            if (props.form.document_uploads.length === 0) {
                props.form.document_uploads.push({
                    id: documentUploadIdCounter++,
                    category: '',
                    files: [],
                    uploaded_files: [],
                });
            }
        } catch (error: any) {
            console.error('Error deleting documents:', error);
            const errorMessage = error.response?.data?.message || error.message || 'Failed to delete some documents. Please try again.';
            alert(errorMessage);
        }
    } else if (doc.existing_document_id) {
        // Single existing document
        if (!confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
            return;
        }
        
        try {
            await axios.delete(`/projects/documents/${doc.existing_document_id}`);
            
            // Remove the document from the array
            props.form.document_uploads.splice(docIndex, 1);
            
            // If no documents left, ensure at least one empty entry
            if (props.form.document_uploads.length === 0) {
                props.form.document_uploads.push({
                    id: documentUploadIdCounter++,
                    category: '',
                    files: [],
                    uploaded_files: [],
                });
            }
        } catch (error: any) {
            console.error('Error deleting document:', error);
            const errorMessage = error.response?.data?.message || error.message || 'Failed to delete document. Please try again.';
            alert(errorMessage);
        }
    } else {
        // New document (not saved yet), just remove it from the array
        if (props.form.document_uploads.length > 1) {
            props.form.document_uploads.splice(docIndex, 1);
        } else {
            // If it's the last one, just clear it
            props.form.document_uploads[docIndex] = {
                id: documentUploadIdCounter++,
                category: '',
                files: [],
                uploaded_files: [],
            };
        }
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

// Delete existing document from database and storage (single document display)
const deleteExistingDocument = async (docIndex: number) => {
    const doc = props.form.document_uploads[docIndex];
    
    if (!confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        return;
    }
    
    // If it's an existing document with an ID, delete it from the database
    if (doc.existing_document_id) {
        try {
            await axios.delete(`/projects/documents/${doc.existing_document_id}`);
            
            // Clear the existing document data but keep the row
            delete doc.existing_document_id;
            delete doc.existing_file_path;
            delete doc.existing_file_name;
            
            // If the row has no files and no new files, and there are other rows, remove it
            if ((!doc.files || doc.files.length === 0) && 
                (!doc.existing_documents || doc.existing_documents.length === 0) &&
                props.form.document_uploads.length > 1) {
                props.form.document_uploads.splice(docIndex, 1);
            }
            
            // If no documents left, ensure at least one empty entry
            if (props.form.document_uploads.length === 0) {
                props.form.document_uploads.push({
                    id: documentUploadIdCounter++,
                    category: '',
                    files: [],
                    uploaded_files: [],
                });
            }
        } catch (error: any) {
            console.error('Error deleting document:', error);
            const errorMessage = error.response?.data?.message || error.message || 'Failed to delete document. Please try again.';
            alert(errorMessage);
        }
    }
};

// Delete existing document by ID (for multiple documents in one entry)
const deleteExistingDocumentById = async (documentId: number, docIndex: number, existingIndex: number) => {
    if (!confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        return;
    }
    
    try {
        await axios.delete(`/projects/documents/${documentId}`);
        
        // Remove from existing_documents array
        const doc = props.form.document_uploads[docIndex];
        if (doc.existing_documents) {
            doc.existing_documents.splice(existingIndex, 1);
            
            // Update the main display if we deleted the first document
            if (doc.existing_documents.length > 0) {
                doc.existing_file_path = doc.existing_documents[0].file_path;
                doc.existing_file_name = doc.existing_documents[0].file_name;
                doc.existing_document_id = doc.existing_documents[0].id;
            } else {
                // No more documents in this group
                delete doc.existing_file_path;
                delete doc.existing_file_name;
                delete doc.existing_document_id;
                
                // If the row has no files and no new files, and there are other rows, remove it
                if ((!doc.files || doc.files.length === 0) && 
                    props.form.document_uploads.length > 1) {
                    props.form.document_uploads.splice(docIndex, 1);
                }
            }
            
            // If no documents left, ensure at least one empty entry
            if (props.form.document_uploads.length === 0) {
                props.form.document_uploads.push({
                    id: documentUploadIdCounter++,
                    category: '',
                    files: [],
                    uploaded_files: [],
                });
            }
        }
    } catch (error: any) {
        console.error('Error deleting document:', error);
        const errorMessage = error.response?.data?.message || error.message || 'Failed to delete document. Please try again.';
        alert(errorMessage);
    }
};

// Check if file is an image
const isImageFile = (fileName: string): boolean => {
    const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp'];
    const lowerFileName = fileName.toLowerCase();
    return imageExtensions.some(ext => lowerFileName.endsWith(ext));
};

// Get file URL for display
const getFileUrl = (filePath: string): string => {
    // Assuming files are stored in public storage
    return `/storage/${filePath}`;
};

// Get preview URL for File objects
const getFilePreviewUrl = (file: File): string => {
    return window.URL.createObjectURL(file);
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
        // Clear existing brochure when new one is selected
        props.form.existing_brochure_file = null;
        props.form.brochure_deleted = false;
    }
};

// Delete existing brochure file
const deleteExistingBrochure = () => {
    if (!confirm('Are you sure you want to delete the brochure file? This action cannot be undone.')) {
        return;
    }
    
    if (!props.form.existing_brochure_file) {
        return;
    }
    
    // Mark brochure as deleted and clear the existing file reference
    props.form.brochure_deleted = true;
    props.form.existing_brochure_file = null;
    props.form.brochure_file = null;
};

// Get brochure file URL
const getBrochureUrl = (filePath: string): string => {
    return `/storage/${filePath}`;
};

// Get brochure file name from path
const getBrochureFileName = (filePath: string): string => {
    if (!filePath) return '';
    const parts = filePath.split('/');
    return parts[parts.length - 1] || filePath;
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
                        <!-- Display selected files (new files being uploaded) -->
                        <div v-if="doc.files && doc.files.length > 0" class="mt-2">
                            <div 
                                v-for="(file, fileIndex) in doc.files" 
                                :key="fileIndex"
                                class="d-flex align-items-center justify-content-between mb-2 p-2 rounded border" 
                                style="background-color: #f8f9fa;"
                            >
                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                    <!-- Image preview for image files -->
                                    <div v-if="isImageFile(file.name)" class="flex-shrink-0">
                                        <img 
                                            :src="getFilePreviewUrl(file)" 
                                            :alt="file.name"
                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                                            @error="(e: any) => { const target = e.target as HTMLImageElement; if (target) target.style.display='none' }"
                                        />
                                    </div>
                                    <!-- File icon for non-image files -->
                                    <div v-else class="flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #1c467b; border-radius: 4px;">
                                        <span class="text-white" style="font-size: 20px;">📄</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="small fw-bold">{{ file.name }}</div>
                                        <div class="small text-muted">{{ (file.size / 1024).toFixed(2) }} KB</div>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click="removeDocumentFile(docIndex, fileIndex)"
                                    class="btn btn-danger btn-sm ms-2 btn-icon-square btn-icon-square-36"
                                    title="Remove File"
                                >
                                    <X :size="18" color="white" />
                                </button>
                            </div>
                        </div>
                        <!-- Display existing documents from database -->
                        <!-- Only show single document display if existing_documents array doesn't exist -->
                        <div v-if="doc.existing_file_path && doc.existing_file_name && (!doc.existing_documents || doc.existing_documents.length === 0)" class="mt-2">
                            <div class="d-flex align-items-center justify-content-between mb-2 p-2 rounded border" style="background-color: #f8f9fa;">
                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                    <!-- Image preview for image files -->
                                    <div v-if="isImageFile(doc.existing_file_name)" class="flex-shrink-0">
                                        <img 
                                            :src="getFileUrl(doc.existing_file_path)" 
                                            :alt="doc.existing_file_name"
                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                                            @error="(e: any) => { const target = e.target as HTMLImageElement; if (target) target.style.display='none' }"
                                        />
                                    </div>
                                    <!-- File icon for non-image files -->
                                    <div v-else class="flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #1c467b; border-radius: 4px;">
                                        <span class="text-white" style="font-size: 20px;">📄</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="small fw-bold">{{ doc.existing_file_name }}</div>
                                        <div class="small text-muted">{{ doc.category || 'No category' }}</div>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click.stop="deleteExistingDocument(docIndex)"
                                    class="btn btn-sm ms-2 btn-icon-square btn-icon-square-32"
                                    style="background-color: #fa8b0c;"
                                    title="Delete Document"
                                >
                                    <X :size="16" color="white" />
                                </button>
                            </div>
                        </div>
                        <!-- Display multiple existing documents if they exist -->
                        <div v-if="doc.existing_documents && doc.existing_documents.length > 0" class="mt-2">
                            <div 
                                v-for="(existingDoc, existingIndex) in doc.existing_documents" 
                                :key="existingDoc.id"
                                class="d-flex align-items-center justify-content-between mb-2 p-2 rounded border" 
                                style="background-color: #f8f9fa;"
                            >
                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                    <!-- Image preview for image files -->
                                    <div v-if="isImageFile(existingDoc.file_name)" class="flex-shrink-0">
                                        <img 
                                            :src="getFileUrl(existingDoc.file_path)" 
                                            :alt="existingDoc.file_name"
                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                                            @error="(e: any) => { const target = e.target as HTMLImageElement; if (target) target.style.display='none' }"
                                        />
                                    </div>
                                    <!-- File icon for non-image files -->
                                    <div v-else class="flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #1c467b; border-radius: 4px;">
                                        <span class="text-white" style="font-size: 20px;">📄</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="small fw-bold">{{ existingDoc.file_name }}</div>
                                        <div class="small text-muted">{{ doc.category || 'No category' }}</div>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click.stop="deleteExistingDocumentById(existingDoc.id, docIndex, existingIndex)"
                                    class="btn btn-sm ms-2 btn-icon-square btn-icon-square-32"
                                    style="background-color: #fa8b0c;"
                                    title="Delete Document"
                                >
                                    <X :size="16" color="white" />
                                </button>
                            </div>
                        </div>
                        <!-- Display uploaded files (new files being added) -->
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
                                    class="btn btn-danger btn-sm btn-icon-square btn-icon-square-36"
                                    title="Remove File"
                                >
                                    <X :size="18" color="white" />
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label hidden-label">Action</label>
                        <div class="d-flex gap-1">
                            <!-- Show add button only for first row -->
                            <button
                                v-if="docIndex === 0"
                                type="button"
                                @click="addDocumentUpload"
                                class="btn btn-primary btn-sm btn-icon-square btn-icon-square-38"
                                title="Add Document Upload"
                            >
                                <Plus :size="18" color="white" />
                            </button>
                            <!-- Show remove button only for rows after first row -->
                            <button
                                v-if="docIndex > 0"
                                type="button"
                                @click.stop="removeDocumentUpload(doc.id)"
                                class="btn btn-sm btn-icon-square btn-icon-square-38"
                                style="background-color: #fa8b0c;"
                                title="Remove Document Row"
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
                    <!-- Display new selected file -->
                    <div v-if="form.brochure_file && !form.existing_brochure_file" class="mt-2">
                        <div class="d-flex align-items-center justify-content-between p-2 rounded border" style="background-color: #f8f9fa;">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                <div class="flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #1c467b; border-radius: 4px;">
                                    <span class="text-white" style="font-size: 20px;">📄</span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small fw-bold">{{ form.brochure_file.name }}</div>
                                    <div class="small text-muted">{{ (form.brochure_file.size / 1024).toFixed(2) }} KB</div>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="form.brochure_file = null"
                                class="btn btn-danger btn-sm ms-2 btn-icon-square btn-icon-square-36"
                                title="Remove File"
                            >
                                <X :size="18" color="white" />
                            </button>
                        </div>
                    </div>
                    <!-- Display existing brochure file -->
                    <div v-if="form.existing_brochure_file" class="mt-2">
                        <div class="d-flex align-items-center justify-content-between p-2 rounded border" style="background-color: #f8f9fa;">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                <div class="flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #1c467b; border-radius: 4px;">
                                    <span class="text-white" style="font-size: 20px;">📄</span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small fw-bold">{{ getBrochureFileName(form.existing_brochure_file) }}</div>
                                    <div class="small text-muted">Existing brochure file</div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 ms-2">
                                <a
                                    :href="getBrochureUrl(form.existing_brochure_file)"
                                    target="_blank"
                                    class="btn btn-primary btn-sm btn-icon-square btn-icon-square-36"
                                    style="text-decoration: none;"
                                    title="View/Download Brochure"
                                >
                                    <span style="font-size: 18px;">👁️</span>
                                </a>
                                <button
                                    type="button"
                                    @click="deleteExistingBrochure"
                                    class="btn btn-danger btn-sm btn-icon-square btn-icon-square-36"
                                    title="Delete Brochure"
                                >
                                    <X :size="18" color="white" />
                                </button>
                            </div>
                        </div>
                        <div class="mt-2 small text-muted">
                            <em>Select a new file to replace the existing brochure</em>
                        </div>
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

