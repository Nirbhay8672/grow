<script setup lang="ts">
import { onMounted } from 'vue';

interface MeasurementUnit {
    id: number;
    name: string;
    is_active: boolean;
    user_id: number;
    user?: {
        id: number;
        name: string;
    };
    created_at: string;
    updated_at: string;
}

interface Props {
    measurementUnits: MeasurementUnit[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    edit: [measurementUnit: MeasurementUnit];
    delete: [measurementUnit: MeasurementUnit];
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
            <span>Measurement Units Management</span>
            <slot name="header-action" />
        </div>
        <div class="card-body p-0">
            <div class="table4 p-25 bg-white mb-30">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr class="userDatatable-header">
                                <th>
                                    <span class="userDatatable-title">Unit Name</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Created By</span>
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
                            <tr v-for="measurementUnit in measurementUnits" :key="measurementUnit.id">
                                <!-- Unit Name -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center">
                                            <div class="userDatatable-inline-title">
                                                <a href="#" class="text-dark fw-500">
                                                    {{ measurementUnit.name }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Created By -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span class="text-muted">
                                            {{ measurementUnit.user?.name || 'N/A' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span 
                                            class="rounded-pill userDatatable-content-status"
                                            :class="measurementUnit.is_active ? 'bg-opacity-success color-success active' : 'bg-opacity-danger color-danger'"
                                        >
                                            {{ measurementUnit.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Created Date -->
                                <td>
                                    <div class="userDatatable-content">
                                        {{ formatDate(measurementUnit.created_at) }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center gap-2">
                                            <button
                                                @click="emit('edit', measurementUnit)"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit Measurement Unit"
                                                style="padding: 4px 8px; min-width: 32px; height: 32px;"
                                            >
                                                ✏️
                                            </button>
                                            <button
                                                @click="emit('delete', measurementUnit)"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete Measurement Unit"
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
        <div v-if="measurementUnits.length === 0" class="text-center py-5">
            <div class="mb-3">
                <span style="font-size: 48px;">📏</span>
            </div>
            <h6 class="text-muted">No measurement units found</h6>
            <p class="text-muted mb-0">Get started by creating a new measurement unit.</p>
        </div>
    </div>
</template>

