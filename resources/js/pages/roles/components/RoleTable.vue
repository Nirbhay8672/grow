<script setup lang="ts">
import { computed, onMounted } from 'vue';

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
}

const props = defineProps<Props>();

const emit = defineEmits<{
    edit: [role: Role];
    delete: [role: Role];
}>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getPermissionBadgeClass = (permission: string) => {
    const [resource] = permission.split('.');
    const classes = {
        role: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        user: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    };
    return classes[resource as keyof typeof classes] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
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
            <span>Roles Management</span>
            <slot name="header-action" />
        </div>
        <div class="card-body p-0">
            <div class="table4 p-25 bg-white mb-30">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr class="userDatatable-header">
                                <th>
                                    <span class="userDatatable-title">Role Name</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Permissions</span>
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
                            <tr v-for="role in roles" :key="role.id">
                                <!-- Role Name -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center">
                                            <div class="userDatatable-inline-title">
                                                <a href="#" class="text-dark fw-500">
                                                    {{ role.name }} <span class="text-muted">( {{ role.guard_name }} )</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Permissions -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex flex-wrap">
                                            <span
                                                v-for="permission in role.permissions.slice(0, 3)"
                                                :key="permission.id"
                                                class="bg-opacity-primary color-primary rounded-pill userDatatable-content-status active me-1 mb-1"
                                            >
                                                {{ permission.name.split('.')[1] }}
                                            </span>
                                            <span
                                                v-if="role.permissions.length > 3"
                                                class="bg-opacity-secondary color-secondary rounded-pill userDatatable-content-status me-1 mb-1"
                                            >
                                                +{{ role.permissions.length - 3 }} more
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Created Date -->
                                <td>
                                    <div class="userDatatable-content">
                                        {{ formatDate(role.created_at) }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center gap-2">
                                            <button
                                                @click="emit('edit', role)"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit Role"
                                                style="padding: 4px 8px; min-width: 32px; height: 32px;"
                                            >
                                                ✏️
                                            </button>
                                            <button
                                                @click="emit('delete', role)"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete Role"
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
        <div v-if="roles.length === 0" class="text-center py-5">
            <div class="mb-3">
                <span style="font-size: 48px;">🛡️</span>
            </div>
            <h6 class="text-muted">No roles found</h6>
            <p class="text-muted mb-0">Get started by creating a new role.</p>
        </div>
    </div>
</template>

