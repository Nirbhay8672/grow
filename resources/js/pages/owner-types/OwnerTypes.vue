<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/custom/modal/Modal.vue';
import OwnerTypeForm from '@/pages/owner-types/components/OwnerTypeForm.vue';
import OwnerTypeTable from '@/pages/owner-types/components/OwnerTypeTable.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface OwnerType {
    id: number;
    name: string;
    is_active: boolean;
    user_id: number;
    created_at: string;
    updated_at: string;
}

interface Props {
    ownerTypes: OwnerType[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Owner Types',
        href: '/owner-types',
    },
];

const showForm = ref(false);
const editingOwnerType = ref<OwnerType | null>(null);
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
    editingOwnerType.value = null;
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

const editOwnerType = (ownerType: OwnerType) => {
    editingOwnerType.value = ownerType;
    form.name = ownerType.name;
    form.is_active = ownerType.is_active;
    errors.value = {};
    showForm.value = true;
};

const createOwnerType = () => {
    resetForm();
    showForm.value = true;
};

const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};

    try {
        if (editingOwnerType.value) {
            await axios.put(`/owner-types/${editingOwnerType.value.id}`, form);
            showToast('Owner Type updated successfully!');
        } else {
            await axios.post('/owner-types', form);
            showToast('Owner Type created successfully!');
        }
        
        resetForm();
        router.reload();
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            showToast('Please fix the validation errors.', 'error');
        } else {
            console.error('Error saving owner type:', error);
            showToast('An error occurred while saving the owner type.', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const deleteOwnerType = async (ownerType: OwnerType) => {
    const Swal = (window as any).Swal;
    
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the owner type "${ownerType.name}". This action cannot be undone!`,
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
            await axios.delete(`/owner-types/${ownerType.id}`);
            showToast('Owner Type deleted successfully!');
            router.reload();
        } catch (error) {
            console.error('Error deleting owner type:', error);
            showToast('An error occurred while deleting the owner type.', 'error');
        }
    }
};
</script>

<template>
    <Head title="Owner Types Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <!-- Owner Type Table -->
                <OwnerTypeTable
                    :owner-types="ownerTypes"
                    @edit="editOwnerType"
                    @delete="deleteOwnerType"
                >
                    <template #header-action>
                        <button
                            @click="createOwnerType"
                            class="btn btn-primary btn-sm btn-default btn-squared text-capitalize lh-normal px-3 py-2"
                        >
                            <span data-feather="plus" class="me-1"></span>
                            Add Owner Type
                        </button>
                    </template>
                </OwnerTypeTable>

                <!-- Owner Type Form Modal -->
                <Modal
                    :show="showForm"
                    :title="editingOwnerType ? 'Edit Owner Type' : 'Create Owner Type'"
                    :loading="loading"
                    :submit-label="editingOwnerType ? 'Update Owner Type' : 'Create Owner Type'"
                    size="sm"
                    @close="resetForm"
                    @submit="handleSubmit"
                >
                    <OwnerTypeForm
                        :form="form"
                        :editing-owner-type="editingOwnerType"
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
