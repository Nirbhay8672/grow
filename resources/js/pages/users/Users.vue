<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/custom/modal/Modal.vue';
import UserForm from '@/pages/users/components/UserForm.vue';
import UserTable from '@/pages/users/components/UserTable.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface State {
    id: number;
    name: string;
    code: string;
}

interface City {
    id: number;
    name: string;
    state_id: number;
}

interface Role {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    username: string;
    first_name: string;
    middle_name?: string;
    last_name: string;
    mobile?: string;
    email: string;
    company_name?: string;
    birth_date?: string;
    state_id?: number;
    city_id?: number;
    is_active: boolean;
    state?: State;
    city?: City;
    roles?: Role[];
}

interface Props {
    users: User[];
    states: State[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Users',
        href: '/users',
    },
];

const showForm = ref(false);
const editingUser = ref<User | null>(null);
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const userFormRef = ref<any>(null);
const toast = ref<{ show: boolean; message: string; type: 'success' | 'error' }>({
    show: false,
    message: '',
    type: 'success'
});

const form = reactive({
    name: '',
    username: '',
    first_name: '',
    middle_name: '',
    last_name: '',
    mobile: '',
    email: '',
    company_name: '',
    birth_date: '',
    state_id: '',
    city_id: '',
    is_active: true,
    password: '',
});

const resetForm = () => {
    form.name = '';
    form.username = '';
    form.first_name = '';
    form.middle_name = '';
    form.last_name = '';
    form.mobile = '';
    form.email = '';
    form.company_name = '';
    form.birth_date = '';
    form.state_id = '';
    form.city_id = '';
    form.is_active = true;
    form.password = '';
    errors.value = {};
    editingUser.value = null;
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

const editUser = (user: User) => {
    editingUser.value = user;
    form.name = user.name;
    form.username = user.username;
    form.first_name = user.first_name;
    form.middle_name = user.middle_name || '';
    form.last_name = user.last_name;
    form.mobile = user.mobile || '';
    form.email = user.email;
    form.company_name = user.company_name || '';
    form.birth_date = user.birth_date || '';
    form.state_id = user.state_id?.toString() || '';
    form.city_id = user.city_id?.toString() || '';
    form.is_active = user.is_active;
    form.password = '';
    errors.value = {};
    showForm.value = true;
};

const createUser = () => {
    resetForm();
    showForm.value = true;
};

const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};

    try {
        const formData: any = {
            name: form.name,
            username: form.username,
            first_name: form.first_name,
            last_name: form.last_name,
            middle_name: form.middle_name || null,
            mobile: form.mobile || null,
            email: form.email,
            company_name: form.company_name || null,
            birth_date: form.birth_date || null,
            state_id: form.state_id ? parseInt(form.state_id) : null,
            city_id: form.city_id ? parseInt(form.city_id) : null,
            is_active: form.is_active,
        };
        
        if (form.password) {
            formData.password = form.password;
        }

        if (editingUser.value) {
            if (!formData.password) {
                delete formData.password;
            }
            await axios.put(`/users/${editingUser.value.id}`, formData);
            showToast('User updated successfully!');
        } else {
            await axios.post('/users', formData);
            showToast('User created successfully!');
        }
        
        resetForm();
        router.reload();
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            showToast('Please fix the validation errors.', 'error');
        } else {
            console.error('Error saving user:', error);
            showToast('An error occurred while saving the user.', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const deleteUser = async (user: User) => {
    const Swal = (window as any).Swal;
    
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the user "${user.name}". This action cannot be undone!`,
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
            await axios.delete(`/users/${user.id}`);
            showToast('User deleted successfully!');
            router.reload();
        } catch (error) {
            console.error('Error deleting user:', error);
            showToast('An error occurred while deleting the user.', 'error');
        }
    }
};

const toggleActive = async (user: User) => {
    try {
        await axios.patch(`/users/${user.id}/toggle-active`);
        showToast(`User ${user.is_active ? 'deactivated' : 'activated'} successfully!`);
        router.reload();
    } catch (error) {
        console.error('Error toggling user status:', error);
        showToast('An error occurred while updating user status.', 'error');
    }
};
</script>

<template>
    <Head title="Users Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <!-- User Table -->
                <UserTable
                    :users="users"
                    @edit="editUser"
                    @delete="deleteUser"
                    @toggle-active="toggleActive"
                >
                    <template #header-action>
                        <button
                            @click="createUser"
                            class="btn btn-primary btn-sm btn-default btn-squared text-capitalize lh-normal px-3 py-2"
                        >
                            <span data-feather="plus" class="me-1"></span>
                            Add User
                        </button>
                    </template>
                </UserTable>

                <!-- User Form Modal -->
                <Modal
                    :show="showForm"
                    :title="editingUser ? 'Edit User' : 'Create User'"
                    :loading="loading"
                    :submit-label="editingUser ? 'Update User' : 'Create User'"
                    size="xl"
                    @close="resetForm"
                    @submit="handleSubmit"
                >
                    <UserForm
                        ref="userFormRef"
                        :form="form"
                        :states="states"
                        :editing-user="editingUser"
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

