<script setup lang="ts">
import { onMounted } from 'vue';

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
    created_at: string;
    updated_at: string;
    state?: State;
    city?: City;
    roles?: Role[];
}

interface Props {
    users: User[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    edit: [user: User];
    delete: [user: User];
    'toggle-active': [user: User];
}>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

// Initialize Feather icons
onMounted(() => {
    if (typeof window !== 'undefined' && (window as any).feather) {
        (window as any).feather.replace();
    }
});
</script>

<template>
    <div class="card mb-4 mt-4">
        <div class="card-header color-dark fw-500 d-flex justify-content-between align-items-center">
            <span>Users Management</span>
            <slot name="header-action" />
        </div>
        <div class="card-body p-0">
            <div class="table4 p-25 bg-white mb-30">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr class="userDatatable-header">
                                <th>
                                    <span class="userDatatable-title">Name</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Username</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Email</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Location</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Mobile</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Status</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Created</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users" :key="user.id">
                                <!-- Name -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center">
                                            <div class="userDatatable-inline-title">
                                                <a href="#" class="text-dark fw-500">
                                                    {{ user.name }}
                                                </a>
                                                <p class="d-block mb-0">
                                                    <span class="text-muted">{{ user.first_name }} {{ user.middle_name || '' }} {{ user.last_name }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Username -->
                                <td>
                                    <div class="userDatatable-content">
                                        {{ user.username }}
                                    </div>
                                </td>

                                <!-- Email -->
                                <td>
                                    <div class="userDatatable-content">
                                        {{ user.email }}
                                    </div>
                                </td>

                                <!-- Location -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span v-if="user.city && user.state" class="text-muted">
                                            {{ user.city.name }}, {{ user.state.name }}
                                        </span>
                                        <span v-else class="text-muted">-</span>
                                    </div>
                                </td>

                                <!-- Mobile -->
                                <td>
                                    <div class="userDatatable-content">
                                        {{ user.mobile || '-' }}
                                    </div>
                                </td>

                                <!-- Status Toggle -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                :checked="user.is_active"
                                                @change="emit('toggle-active', user)"
                                                role="switch"
                                                :id="`status-${user.id}`"
                                            />
                                            <label
                                                class="form-check-label"
                                                :for="`status-${user.id}`"
                                            >
                                                <span
                                                    class="rounded-pill userDatatable-content-status ms-2"
                                                    :class="user.is_active ? 'bg-opacity-success color-success active' : 'bg-opacity-danger color-danger'"
                                                >
                                                    {{ user.is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </td>

                                <!-- Created Date -->
                                <td>
                                    <div class="userDatatable-content">
                                        {{ formatDate(user.created_at) }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center gap-2">
                                            <button
                                                @click="emit('edit', user)"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit User"
                                                style="padding: 4px 8px; min-width: 32px; height: 32px;"
                                            >
                                                ✏️
                                            </button>
                                            <button
                                                @click="emit('delete', user)"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete User"
                                                style="padding: 4px 8px; min-width: 32px; height: 32px;"
                                            >
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="users.length === 0" class="text-center py-5">
            <div class="mb-3">
                <span style="font-size: 48px;">👥</span>
            </div>
            <h6 class="text-muted">No users found</h6>
            <p class="text-muted mb-0">Get started by creating a new user.</p>
        </div>
    </div>
</template>

