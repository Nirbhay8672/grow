<script setup lang="ts">
import { onMounted } from 'vue';

interface District {
    id: number;
    name: string;
    state_id: number;
    is_active: boolean;
}

interface Taluka {
    id: number;
    name: string;
    district_id: number;
    is_active: boolean;
}

interface Village {
    id: number;
    name: string;
    district_id: number;
    taluka_id: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    district: District;
    taluka: Taluka;
}

interface Props {
    villages: Village[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    edit: [village: Village];
    delete: [village: Village];
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
            <span>Villages Management</span>
            <slot name="header-action" />
        </div>
        <div class="card-body p-0">
            <div class="table4 p-25 bg-white mb-30">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr class="userDatatable-header">
                                <th>
                                    <span class="userDatatable-title">Village Name</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Taluka</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">District</span>
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
                            <tr v-for="village in villages" :key="village.id">
                                <!-- Village Name -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center">
                                            <div class="userDatatable-inline-title">
                                                <a href="#" class="text-dark fw-500">
                                                    {{ village.name }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Taluka -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span class="bg-opacity-primary color-primary rounded-pill userDatatable-content-status active">
                                            {{ village.taluka.name }}
                                        </span>
                                    </div>
                                </td>

                                <!-- District -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span class="bg-opacity-info color-info rounded-pill userDatatable-content-status active">
                                            {{ village.district.name }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span 
                                            class="rounded-pill userDatatable-content-status"
                                            :class="village.is_active ? 'bg-opacity-success color-success active' : 'bg-opacity-danger color-danger'"
                                        >
                                            {{ village.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Created Date -->
                                <td>
                                    <div class="userDatatable-content">
                                        {{ formatDate(village.created_at) }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center gap-2">
                                            <button
                                                @click="emit('edit', village)"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit Village"
                                                style="padding: 4px 8px; min-width: 32px; height: 32px;"
                                            >
                                                ✏️
                                            </button>
                                            <button
                                                @click="emit('delete', village)"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete Village"
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
        <div v-if="villages.length === 0" class="text-center py-5">
            <div class="mb-3">
                <span style="font-size: 48px;">🏘️</span>
            </div>
            <h6 class="text-muted">No villages found</h6>
            <p class="text-muted mb-0">Get started by creating a new village.</p>
        </div>
    </div>
</template>

