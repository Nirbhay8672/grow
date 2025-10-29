<script setup lang="ts">
import { onMounted } from 'vue';

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
}

interface Locality {
    id: number;
    name: string;
    state_id: number;
    city_id: number;
    zip_code: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    state: State;
    city: City;
}

interface Props {
    localities: Locality[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    edit: [locality: Locality];
    delete: [locality: Locality];
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
    <div class="card">
        <div class="card-header color-dark fw-500">
            Localities Management
        </div>
        <div class="card-body p-0">
            <div class="table4 p-25 bg-white mb-30">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr class="userDatatable-header">
                                <th>
                                    <span class="userDatatable-title">Locality Name</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">City</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">State</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Zip Code</span>
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
                            <tr v-for="locality in localities" :key="locality.id">
                                <!-- Locality Name -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center">
                                            <div class="userDatatable-inline-title">
                                                <a href="#" class="text-dark fw-500">
                                                    {{ locality.name }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td macroscopic>

                                <!-- City -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span class="bg-opacity-primary color-primary rounded-pill userDatatable-content-status active">
                                            {{ locality.city.name }}
                                        </span>
                                    </div>
                                </td>

                                <!-- State -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span class="bg-opacity-info color-info rounded-pill userDatatable-content-status active">
                                            {{ locality.state.name }} ({{ locality.state.code }})
                                        </span>
                                    </div>
                                </td>

                                <!-- Zip Code -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span class="badge bg-primary">{{ locality.zip_code }}</span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span 
                                            class="rounded-pill userDatatable-content-status"
                                            :class="locality.is_active ? 'bg-opacity-success color-success active' : 'bg-opacity-danger color-danger'"
                                        >
                                            {{ locality.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Created Date -->
                                <td>
                                    <div class="userDatatable-content">
                                        {{ formatDate(locality.created_at) }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center gap-2">
                                            <button
                                                @click="emit('edit', locality)"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit Locality"
                                                style="padding: 4px 8px; min-width: 32px; height: 32px;"
                                            >
                                                ✏️
                                            </button>
                                            <button
                                                @click="emit('delete', locality)"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete Locality"
                                                style="padding: 4px 8px; min-width: 32px; height: 32px;"
                                            >
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        худо>
                    </table>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="localities.length === 0" class="text-center py-5">
            <div class="mb-3">
                <span style="font-size: 48px;">🏘️</span>
            </div>
            <h6 class="text-muted">No localities found</h6>
            <p class="text-muted mb-0">Get started by creating a new locality.</p>
        </div>
    </div>
</template>

