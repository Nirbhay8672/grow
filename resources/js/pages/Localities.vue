<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import LocalityForm from '@/components/LocalityForm.vue';
import LocalityTable from '@/components/LocalityTable.vue';
import { type BreadcrumbItem } from '@/types';

interface State { id: number; name: string; code?: string; is_active?: boolean }
interface City { id: number; name: string; state_id: number; }
interface Locality {
    id: number;
    name: string;
    zip_code: string;
    state_id: number;
    city_id: number;
    is_active: boolean;
    state: State;
    city: City;
}

interface Props {
    localities: Locality[];
    states: State[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Localities', href: '/localities' },
];

const showForm = ref(false);
const editingLocality = ref<Locality | null>(null);
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const toast = ref<{ show: boolean; message: string; type: 'success' | 'error' }>({ show: false, message: '', type: 'success' });
// cities are handled inside LocalityForm component

const form = reactive({
    name: '',
    zip_code: '',
    state_id: '',
    city_id: '',
    is_active: true,
});

// fetching of cities occurs within LocalityForm based on state selection

const resetForm = () => {
    form.name = '';
    form.zip_code = '';
    form.state_id = '';
    form.city_id = '';
    form.is_active = true;
    errors.value = {};
    editingLocality.value = null;
    showForm.value = false;
};

const openCreate = () => { resetForm(); showForm.value = true; };

const openEdit = (row: Locality) => {
    editingLocality.value = row;
    form.name = row.name;
    form.zip_code = row.zip_code;
    form.state_id = row.state_id.toString();
    form.city_id = row.city_id.toString();
    form.is_active = row.is_active;
    errors.value = {};
    showForm.value = true;
};

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => { toast.value.show = false }, 3000);
};

const submit = async () => {
    loading.value = true;
    errors.value = {};
    try {
        const payload = {
            name: form.name,
            zip_code: form.zip_code,
            state_id: parseInt(form.state_id),
            city_id: parseInt(form.city_id),
            is_active: form.is_active,
        };
        if (editingLocality.value) {
            await axios.put(`/localities/${editingLocality.value.id}`, payload);
            showToast('Locality updated successfully!');
        } else {
            await axios.post('/localities', payload);
            showToast('Locality created successfully!');
        }
        resetForm();
        router.reload();
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            showToast('Please fix the validation errors.', 'error');
        } else {
            showToast('An error occurred while saving the locality.', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const removeRow = async (row: Locality) => {
    const Swal = (window as any).Swal;
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the locality "${row.name}". This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
    });
    if (!result.isConfirmed) return;
    try {
        await axios.delete(`/localities/${row.id}`);
        showToast('Locality deleted successfully!');
        router.reload();
    } catch (error) {
        showToast('An error occurred while deleting the locality.', 'error');
    }
};
</script>

<template>
    <Head title="Localities Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <LocalityTable :localities="props.localities" @edit="openEdit" @delete="removeRow">
                    <template #header-action>
                        <button @click="openCreate" class="btn btn-primary btn-sm btn-default btn-squared text-capitalize lh-normal px-3 py-2">
                            <span data-feather="plus" class="me-1"></span>
                            Add Locality
                        </button>
                    </template>
                </LocalityTable>

                <!-- Modal -->
                <div v-if="showForm" class="modal fade show d-block" tabindex="-1" role="dialog" aria-hidden="false" style="background-color: rgba(0,0,0,0.5);">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header py-2 px-3">
                                <h5 class="modal-title mb-0" style="font-size: 16px; font-weight: 600;">{{ editingLocality ? 'Edit Locality' : 'Create Locality' }}</h5>
                                <button type="button" class="btn-close" @click="resetForm" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body py-3 px-3">
                                <LocalityForm :form="form" :states="props.states" :editing-locality="editingLocality" :errors="errors" />
                            </div>
                            <div class="modal-footer py-2 px-3">
                                <button type="button" class="btn btn-secondary btn-sm" @click="resetForm" style="font-size: 13px;">Cancel</button>
                                <button type="button" class="btn btn-primary btn-sm" @click="submit" :disabled="loading" style="font-size: 13px;">
                                    <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                                    {{ editingLocality ? 'Update Locality' : 'Create Locality' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
.toast-success { background-color: #28a745; }
.toast-error { background-color: #dc3545; }
.toast-notification { animation: slideIn 0.3s ease-out; }
@keyframes slideIn {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}
</style>
