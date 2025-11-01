<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/custom/modal/Modal.vue';
import StateForm from '@/pages/states/components/StateForm.vue';
import StateTable from '@/pages/states/components/StateTable.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface State {
    id: number;
    name: string;
    code: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    states: State[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'States',
        href: '/states',
    },
];

const showForm = ref(false);
const editingState = ref<State | null>(null);
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const toast = ref<{ show: boolean; message: string; type: 'success' | 'error' }>({
    show: false,
    message: '',
    type: 'success'
});

const form = reactive({
    name: '',
    code: '',
    is_active: true,
});

const resetForm = () => {
    form.name = '';
    form.code = '';
    form.is_active = true;
    errors.value = {};
    editingState.value = null;
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

const editState = (state: State) => {
    editingState.value = state;
    form.name = state.name;
    form.code = state.code;
    form.is_active = state.is_active;
    errors.value = {};
    showForm.value = true;
};

const createState = () => {
    resetForm();
    showForm.value = true;
};

const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};

    try {
        if (editingState.value) {
            await axios.put(`/states/${editingState.value.id}`, form);
            showToast('State updated successfully!');
        } else {
            await axios.post('/states', form);
            showToast('State created successfully!');
        }
        
        resetForm();
        router.reload();
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            showToast('Please fix the validation errors.', 'error');
        } else {
            console.error('Error saving state:', error);
            showToast('An error occurred while saving the state.', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const deleteState = async (state: State) => {
    const Swal = (window as any).Swal;
    
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the state "${state.name}". This action cannot be undone!`,
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
            await axios.delete(`/states/${state.id}`);
            showToast('State deleted successfully!');
            router.reload();
        } catch (error) {
            console.error('Error deleting state:', error);
            showToast('An error occurred while deleting the state.', 'error');
        }
    }
};
</script>

<template>
    <Head title="States Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <!-- State Table -->
                <StateTable
                    :states="states"
                    @edit="editState"
                    @delete="deleteState"
                >
                    <template #header-action>
                        <button
                            @click="createState"
                            class="btn btn-primary btn-sm btn-default btn-squared text-capitalize lh-normal px-3 py-2"
                        >
                            <span data-feather="plus" class="me-1"></span>
                            Add State
                        </button>
                    </template>
                </StateTable>

                <!-- State Form Modal -->
                <Modal
                    :show="showForm"
                    :title="editingState ? 'Edit State' : 'Create State'"
                    :loading="loading"
                    :submit-label="editingState ? 'Update State' : 'Create State'"
                    size="sm"
                    @close="resetForm"
                    @submit="handleSubmit"
                >
                    <StateForm
                        :form="form"
                        :editing-state="editingState"
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

