<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/custom/modal/Modal.vue';
import PropertyZoneForm from '@/pages/property-zones/components/PropertyZoneForm.vue';
import PropertyZoneTable from '@/pages/property-zones/components/PropertyZoneTable.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface PropertyZone {
    id: number;
    name: string;
    is_active: boolean;
    user_id: number;
    created_at: string;
    updated_at: string;
}

interface Props {
    propertyZones: PropertyZone[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Property Zones',
        href: '/property-zones',
    },
];

const showForm = ref(false);
const editingPropertyZone = ref<PropertyZone | null>(null);
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const toast = ref<{ show: boolean; message: string; type: 'success' | 'error' }>({
    show: false,
    message: '',
    type: 'success'
});

const form = reactive({
    name: '',
    is_active: true,
});

const resetForm = () => {
    form.name = '';
    form.is_active = true;
    errors.value = {};
    editingPropertyZone.value = null;
    showForm.value = false;
};

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
    toast.value = {
        show: true,
        message,
        type
    };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const editPropertyZone = (propertyZone: PropertyZone) => {
    editingPropertyZone.value = propertyZone;
    form.name = propertyZone.name;
    form.is_active = propertyZone.is_active;
    errors.value = {};
    showForm.value = true;
};

const createPropertyZone = () => {
    resetForm();
    showForm.value = true;
};

const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};

    try {
        if (editingPropertyZone.value) {
            await axios.put(`/property-zones/${editingPropertyZone.value.id}`, form);
            showToast('Property Zone updated successfully!');
        } else {
            await axios.post('/property-zones', form);
            showToast('Property Zone created successfully!');
        }
        
        resetForm();
        router.reload();
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            showToast('Please fix the validation errors.', 'error');
        } else {
            console.error('Error saving property zone:', error);
            showToast('An error occurred while saving the property zone.', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const deletePropertyZone = async (propertyZone: PropertyZone) => {
    const Swal = (window as any).Swal;
    
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the property zone "${propertyZone.name}". This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    });

    if (result.isConfirmed) {
        try {
            await axios.delete(`/property-zones/${propertyZone.id}`);
            showToast('Property Zone deleted successfully!');
            router.reload();
        } catch (error) {
            console.error('Error deleting property zone:', error);
            showToast('An error occurred while deleting the property zone.', 'error');
        }
    }
};
</script>

<template>
    <Head title="Property Zones Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <!-- Property Zone Table -->
                <PropertyZoneTable
                    :property-zones="propertyZones"
                    @edit="editPropertyZone"
                    @delete="deletePropertyZone"
                >
                    <template #header-action>
                        <button
                            @click="createPropertyZone"
                            class="btn btn-primary btn-sm btn-default btn-squared text-capitalize lh-normal px-3 py-2"
                        >
                            <span data-feather="plus" class="me-1"></span>
                            Add Property Zone
                        </button>
                    </template>
                </PropertyZoneTable>

                <!-- Property Zone Form Modal -->
                <Modal
                    :show="showForm"
                    :title="editingPropertyZone ? 'Edit Property Zone' : 'Create Property Zone'"
                    :loading="loading"
                    :submit-label="editingPropertyZone ? 'Update Property Zone' : 'Create Property Zone'"
                    size="sm"
                    @close="resetForm"
                    @submit="handleSubmit"
                >
                    <PropertyZoneForm
                        :form="form"
                        :editing-property-zone="editingPropertyZone"
                        :errors="errors"
                        @submit="handleSubmit"
                        @cancel="resetForm"
                    />
                </Modal>
            </div>
        </div>

        <!-- Toast Notification -->
        <div
            v-if="toast.show"
            class="toast-notification"
            :class="toast.type === 'success' ? 'toast-success' : 'toast-error'"
            style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; padding: 12px 16px; border-radius: 8px; color: white; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.15);"
        >
            <div class="d-flex align-items-center">
                <span class="me-2" style="font-size: 18px;">
                    {{ toast.type === 'success' ? '✅' : '❌' }}
                </span>
                <span>{{ toast.message }}</span>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.toast-success {
    background-color: #28a745;
}

.toast-error {
    background-color: #dc3545;
}

.toast-notification {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
