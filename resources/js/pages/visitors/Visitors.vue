<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/custom/modal/Modal.vue';
import VisitorForm from '@/pages/visitors/components/VisitorForm.vue';
import VisitorTable from '@/pages/visitors/components/VisitorTable.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface Visitor {
    id: number;
    name: string;
    mobile: string;
    barcode: string;
    barcode_image: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    visitors: Visitor[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Visitors',
        href: '/visitors',
    },
];

const showForm = ref(false);
const editingVisitor = ref<Visitor | null>(null);
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const toast = ref<{ show: boolean; message: string; type: 'success' | 'error' }>({
    show: false,
    message: '',
    type: 'success'
});

const form = reactive({
    name: '',
    mobile: '',
});

const resetForm = () => {
    form.name = '';
    form.mobile = '';
    errors.value = {};
    editingVisitor.value = null;
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

const editVisitor = (visitor: Visitor) => {
    editingVisitor.value = visitor;
    form.name = visitor.name;
    form.mobile = visitor.mobile;
    errors.value = {};
    showForm.value = true;
};

const createVisitor = () => {
    resetForm();
    showForm.value = true;
};

const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};

    try {
        if (editingVisitor.value) {
            await axios.put(`/visitors/${editingVisitor.value.id}`, form);
            showToast('Visitor updated successfully!');
        } else {
            await axios.post('/visitors', form);
            showToast('Visitor created successfully! Barcode has been sent via WhatsApp.');
        }
        
        resetForm();
        router.reload();
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            showToast('Please fix the validation errors.', 'error');
        } else {
            console.error('Error saving visitor:', error);
            showToast('An error occurred while saving the visitor.', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const deleteVisitor = async (visitor: Visitor) => {
    const Swal = (window as any).Swal;
    
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete visitor "${visitor.name}". This action cannot be undone!`,
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
            await axios.delete(`/visitors/${visitor.id}`);
            showToast('Visitor deleted successfully!');
            router.reload();
        } catch (error) {
            console.error('Error deleting visitor:', error);
            showToast('An error occurred while deleting the visitor.', 'error');
        }
    }
};
</script>

<template>
    <Head title="Visitors Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <!-- Visitors Table -->
                <VisitorTable
                    :visitors="visitors"
                    @edit="editVisitor"
                    @delete="deleteVisitor"
                >
                    <template #header-action>
                        <button
                            @click="createVisitor"
                            class="btn btn-primary btn-sm btn-default btn-squared text-capitalize lh-normal px-3 py-2"
                        >
                            <span data-feather="plus" class="me-1"></span>
                            Add Visitor
                        </button>
                    </template>
                </VisitorTable>

                <!-- Visitor Form Modal -->
                <Modal
                    :show="showForm"
                    :title="editingVisitor ? 'Edit Visitor' : 'Create Visitor'"
                    :loading="loading"
                    :submit-label="editingVisitor ? 'Update Visitor' : 'Create Visitor'"
                    size="sm"
                    @close="resetForm"
                    @submit="handleSubmit"
                >
                    <VisitorForm
                        :form="form"
                        :editing-visitor="editingVisitor"
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

