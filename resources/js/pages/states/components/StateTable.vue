<script setup lang="ts">
import { onMounted } from 'vue';

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

const emit = defineEmits<{
    edit: [state: State];
    delete: [state: State];
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
            <span>States Management</span>
            <slot name="header-action" />
        </div>
        <div class="card-body p-0">
            <div class="table4 p-25 bg-white mb-30">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr class="userDatatable-header">
                                <th>
                                    <span class="userDatatable-title">State Name</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Code</span>
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
                            <tr v-for="state in states" :key="state.id">
                                <!-- State Name -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center">
                                            <div class="userDatatable-inline-title">
                                                <a href="#" class="text-dark fw-500">
                                                    {{ state.name }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- State Code -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span class="bg-opacity-primary color-primary rounded-pill userDatatable-content-status active">
                                            {{ state.code }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span 
                                            class="rounded-pill userDatatable-content-status"
                                            :class="state.is_active ? 'bg-opacity-success color-success active' : 'bg-opacity-danger color-danger'"
                                        >
                                            {{ state.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Created Date -->
                                <td>
                                    <div class="userDatatable-content">
                                        {{ formatDate(state.created_at) }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center gap-2">
                                            <button
                                                @click="emit('edit', state)"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit State"
                                                style="padding: 4px 8px; min-width: 32px; height: 32px;"
                                            >
                                                ✏️
                                            </button>
                                            <button
                                                @click="emit('delete', state)"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete State"
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
        <div v-if="states.length === 0" class="text-center py-5">
            <div class="mb-3">
                <span style="font-size: 48px;">🗺️</span>
            </div>
            <h6 class="text-muted">No states found</h6>
            <p class="text-muted mb-0">Get started by creating a new state.</p>
        </div>
    </div>
</template>

