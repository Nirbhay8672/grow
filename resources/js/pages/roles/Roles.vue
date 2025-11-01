<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import RoleForm from '@/pages/roles/components/RoleForm.vue';
import RoleTable from '@/pages/roles/components/RoleTable.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface Permission {
    id: number;
    name: string;
    guard_name: string;
}

interface Role {
    id: number;
    name: string;
    guard_name: string;
    permissions: Permission[];
    created_at: string;
    updated_at: string;
}

interface Props {
    roles: Role[];
    permissions: Permission[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Roles',
        href: '/roles',
    },
];

const showForm = ref(false);
const editingRole = ref<Role | null>(null);
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const toast = ref<{ show: boolean; message: string; type: 'success' | 'error' }>({
    show: false,
    message: '',
    type: 'success'
});

const form = reactive({
    name: '',
    permissions: [] as string[],
});

const resetForm = () => {
    form.name = '';
    form.permissions = [];
    errors.value = {};
    editingRole.value = null;
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

const editRole = (role: Role) => {
    editingRole.value = role;
    form.name = role.name;
    form.permissions = role.permissions.map(p => p.name);
    errors.value = {};
    showForm.value = true;
};

const createRole = () => {
    resetForm();
    showForm.value = true;
};

const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};

    try {
        if (editingRole.value) {
            await axios.put(`/roles/${editingRole.value.id}`, form);
            showToast('Role updated successfully!');
        } else {
            await axios.post('/roles', form);
            showToast('Role created successfully!');
        }
        
        resetForm();
        router.reload();
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            showToast('Please fix the validation errors.', 'error');
        } else {
            console.error('Error saving role:', error);
            showToast('An error occurred while saving the role.', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const deleteRole = async (role: Role) => {
    const Swal = (window as any).Swal;
    
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the role "${role.name}". This action cannot be undone!`,
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
            await axios.delete(`/roles/${role.id}`);
            showToast('Role deleted successfully!');
            router.reload();
        } catch (error) {
            console.error('Error deleting role:', error);
            showToast('An error occurred while deleting the role.', 'error');
        }
    }
};
</script>

<template>
    <Head title="Roles Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <!-- Role Table -->
                <RoleTable
                    :roles="roles"
                    @edit="editRole"
                    @delete="deleteRole"
                >
                    <template #header-action>
                        <button
                            @click="createRole"
                            class="btn btn-primary btn-sm btn-default btn-squared text-capitalize lh-normal px-3 py-2"
                        >
                            <span data-feather="plus" class="me-1"></span>
                            Add Role
                        </button>
                    </template>
                </RoleTable>

                <!-- Role Form Modal -->
                <div
                    v-if="showForm"
                    class="modal fade show d-block"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="roleModalLabel"
                    aria-hidden="false"
                    style="background-color: rgba(0,0,0,0.5);"
                >
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header py-2 px-3">
                                <h5 class="modal-title mb-0" id="roleModalLabel" style="font-size: 16px; font-weight: 600;">
                                    {{ editingRole ? 'Edit Role' : 'Create Role' }}
                                </h5>
                                <button
                                    type="button"
                                    class="btn-close"
                                    @click="resetForm"
                                    aria-label="Close"
                                >
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body py-3 px-3">
                                <RoleForm
                                    :form="form"
                                    :permissions="permissions"
                                    :editing-role="editingRole"
                                    :errors="errors"
                                    @submit="handleSubmit"
                                    @cancel="resetForm"
                                />
                            </div>
                            <div class="modal-footer py-2 px-3">
                                <button
                                    type="button"
                                    class="btn btn-secondary btn-sm"
                                    @click="resetForm"
                                    style="font-size: 13px;"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-primary btn-sm"
                                    @click="handleSubmit"
                                    :disabled="loading"
                                    style="font-size: 13px;"
                                >
                                    <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                                    {{ editingRole ? 'Update Role' : 'Create Role' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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

