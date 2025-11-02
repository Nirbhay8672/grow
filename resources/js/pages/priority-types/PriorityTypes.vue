<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/custom/modal/Modal.vue';
import PriorityTypeForm from '@/pages/priority-types/components/PriorityTypeForm.vue';
import PriorityTypeTable from '@/pages/priority-types/components/PriorityTypeTable.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface PriorityType {
    id: number;
    name: string;
    is_active: boolean;
    user_id: number;
    created_at: string;
    updated_at: string;
}

interface Props {
    priorityTypes: PriorityType[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Priority Types',
        href: '/priority-types',
    },
];

const showForm = ref(false);
const editingPriorityType = ref<PriorityType | null>(null);
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
    editingPriorityType.value = null;
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

const editPriorityType = (priorityType: PriorityType) => {
    editingPriorityType.value = priorityType;
    form.name = priorityType.name;
    form.is_active = priorityType.is_active;
    errors.value = {};
    showForm.value = true;
};

const createPriorityType = () => {
    resetForm();
    showForm.value = true;
};

const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};

    try {
        if (editingPriorityType.value) {
            await axios.put(`/priority-types/${editingPriorityType.value.id}`, form);
            showToast('Priority Type updated successfully!');
        } else {
            await axios.post('/priority-types', form);
            showToast('Priority Type created successfully!');
        }
        
        resetForm();
        router.reload();
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            showToast('Please fix the validation errors.', 'error');
        } else {
            console.error('Error saving priority type:', error);
            showToast('An error occurred while saving the priority type.', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const deletePriorityType = async (priorityType: PriorityType) => {
    const Swal = (window as any).Swal;
    
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the priority type "${priorityType.name}". This action cannot be undone!`,
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
            await axios.delete(`/priority-types/${priorityType.id}`);
            showToast('Priority Type deleted successfully!');
            router.reload();
        } catch (error) {
            console.error('Error deleting priority type:', error);
            showToast('An error occurred while deleting the priority type.', 'error');
        }
    }
};
</script>

<template>
    <Head title="Priority Types Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <!-- Priority Type Table -->
                <PriorityTypeTable
                    :priority-types="priorityTypes"
                    @edit="editPriorityType"
                    @delete="deletePriorityType"
                >
                    <template #header-action>
                        <button
                            @click="createPriorityType"
                            class="btn btn-primary btn-sm btn-default btn-squared text-capitalize lh-normal px-3 py-2"
                        >
                            <span data-feather="plus" class="me-1"></span>
                            Add Priority Type
                        </button>
                    </template>
                </PriorityTypeTable>

                <!-- Priority Type Form Modal -->
                <Modal
                    :show="showForm"
                    :title="editingPriorityType ? 'Edit Priority Type' : 'Create Priority Type'"
                    :loading="loading"
                    :submit-label="editingPriorityType ? 'Update Priority Type' : 'Create Priority Type'"
                    size="sm"
                    @close="resetForm"
                    @submit="handleSubmit"
                >
                    <PriorityTypeForm
                        :form="form"
                        :editing-priority-type="editingPriorityType"
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

