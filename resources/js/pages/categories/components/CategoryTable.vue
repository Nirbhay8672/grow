<script setup lang="ts">
import { onMounted } from 'vue';

interface ConstructionType {
    id: number;
    name: string;
}

interface Category {
    id: number;
    name: string;
    is_active: boolean;
    user_id: number;
    construction_type_id: number | null;
    construction_type: ConstructionType | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    categories: Category[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    edit: [category: Category];
    delete: [category: Category];
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
            <span>Categories Management</span>
            <slot name="header-action" />
        </div>
        <div class="card-body p-0">
            <div class="table4 p-25 bg-white mb-30">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr class="userDatatable-header">
                                <th>
                                    <span class="userDatatable-title">Category Name</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Construction Type</span>
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
                            <tr v-for="category in categories" :key="category.id">
                                <!-- Category Name -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center">
                                            <div class="userDatatable-inline-title">
                                                <a href="#" class="text-dark fw-500">
                                                    {{ category.name }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Construction Type -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span class="text-muted">
                                            {{ category.construction_type ? category.construction_type.name : '—' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span 
                                            class="rounded-pill userDatatable-content-status"
                                            :class="category.is_active ? 'bg-opacity-success color-success active' : 'bg-opacity-danger color-danger'"
                                        >
                                            {{ category.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Created Date -->
                                <td>
                                    <div class="userDatatable-content">
                                        {{ formatDate(category.created_at) }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center gap-2">
                                            <button
                                                @click="emit('edit', category)"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit Category"
                                                style="padding: 4px 8px; min-width: 32px; height: 32px;"
                                            >
                                                ✏️
                                            </button>
                                            <button
                                                @click="emit('delete', category)"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete Category"
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
        <div v-if="categories.length === 0" class="text-center py-5">
            <div class="mb-3">
                <span style="font-size: 48px;">📁</span>
            </div>
            <h6 class="text-muted">No categories found</h6>
            <p class="text-muted mb-0">Get started by creating a new category.</p>
        </div>
    </div>
</template>


