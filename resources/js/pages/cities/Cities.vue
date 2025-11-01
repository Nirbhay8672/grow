<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/custom/modal/Modal.vue';
import CityForm from '@/pages/cities/components/CityForm.vue';
import CityTable from '@/pages/cities/components/CityTable.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface State {
    id: number;
    name: string;
    code: string;
    is_active: boolean;
}

interface City {
    id: number;
    name: string;
    state_id: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    state: State;
}

interface Props {
    cities: City[];
    states: State[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Cities',
        href: '/cities',
    },
];

const showForm = ref(false);
const editingCity = ref<City | null>(null);
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const toast = ref<{ show: boolean; message: string; type: 'success' | 'error' }>({
    show: false,
    message: '',
    type: 'success'
});

const form = reactive({
    name: '',
    state_id: '',
    is_active: true,
});

const resetForm = () => {
    form.name = '';
    form.state_id = '';
    form.is_active = true;
    errors.value = {};
    editingCity.value = null;
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

const editCity = (city: City) => {
    editingCity.value = city;
    form.name = city.name;
    form.state_id = city.state_id.toString();
    form.is_active = city.is_active;
    errors.value = {};
    showForm.value = true;
};

const createCity = () => {
    resetForm();
    showForm.value = true;
};

const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};

    try {
        const formData = {
            ...form,
            state_id: parseInt(form.state_id),
        };

        if (editingCity.value) {
            await axios.put(`/cities/${editingCity.value.id}`, formData);
            showToast('City updated successfully!');
        } else {
            await axios.post('/cities', formData);
            showToast('City created successfully!');
        }
        
        resetForm();
        router.reload();
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            showToast('Please fix the validation errors.', 'error');
        } else {
            console.error('Error saving city:', error);
            showToast('An error occurred while saving the city.', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const deleteCity = async (city: City) => {
    const Swal = (window as any).Swal;
    
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the city "${city.name}". This action cannot be undone!`,
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
            await axios.delete(`/cities/${city.id}`);
            showToast('City deleted successfully!');
            router.reload();
        } catch (error) {
            console.error('Error deleting city:', error);
            showToast('An error occurred while deleting the city.', 'error');
        }
    }
};
</script>

<template>
    <Head title="Cities Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <!-- City Table -->
                <CityTable
                    :cities="cities"
                    @edit="editCity"
                    @delete="deleteCity"
                >
                    <template #header-action>
                        <button
                            @click="createCity"
                            class="btn btn-primary btn-sm btn-default btn-squared text-capitalize lh-normal px-3 py-2"
                        >
                            <span data-feather="plus" class="me-1"></span>
                            Add City
                        </button>
                    </template>
                </CityTable>

                <!-- City Form Modal -->
                <Modal
                    :show="showForm"
                    :title="editingCity ? 'Edit City' : 'Create City'"
                    :loading="loading"
                    :submit-label="editingCity ? 'Update City' : 'Create City'"
                    size="sm"
                    @close="resetForm"
                    @submit="handleSubmit"
                >
                    <CityForm
                        :form="form"
                        :states="states"
                        :editing-city="editingCity"
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

