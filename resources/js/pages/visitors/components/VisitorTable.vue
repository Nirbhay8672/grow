<script setup lang="ts">
import { onMounted } from 'vue';

interface Visitor {
    id: number;
    name: string;
    mobile: string;
    barcode: string;
    barcode_image: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    visitors: Visitor[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    edit: [visitor: Visitor];
    delete: [visitor: Visitor];
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

const copyBarcode = (barcode: string) => {
    navigator.clipboard.writeText(barcode).then(() => {
        const Swal = (window as any).Swal;
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Barcode copied to clipboard',
            timer: 1500,
            showConfirmButton: false,
        });
    });
};

const getBarcodeUrl = (visitor: Visitor): string => {
    // Try storage URL first, fallback to controller route
    if (visitor.barcode_image) {
        return `/storage/${visitor.barcode_image}`;
    }
    return `/visitors/${visitor.id}/barcode`;
};

const handleImageError = (event: Event, visitor: Visitor) => {
    const img = event.target as HTMLImageElement;
    // Fallback to controller route if storage URL fails
    if (img.src.includes('/storage/')) {
        img.src = `/visitors/${visitor.id}/barcode`;
    }
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
            <span>Visitors Management</span>
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
                                    <span class="userDatatable-title">Mobile</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Barcode</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Registered</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="visitor in visitors" :key="visitor.id">
                                <!-- Visitor Name -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center">
                                            <div class="userDatatable-inline-title">
                                                <a href="#" class="text-dark fw-500">
                                                    {{ visitor.name }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Mobile Number -->
                                <td>
                                    <div class="userDatatable-content">
                                        <span class="text-muted">
                                            {{ visitor.mobile }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Barcode -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <code class="bg-light px-2 py-1 rounded" style="font-size: 12px;">
                                                    {{ visitor.barcode }}
                                                </code>
                                                <button
                                                    @click="copyBarcode(visitor.barcode)"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    title="Copy Barcode"
                                                    style="padding: 2px 6px; min-width: 28px; height: 28px; font-size: 12px;"
                                                >
                                                    📋
                                                </button>
                                            </div>
                                            <div v-if="visitor.barcode_image" class="mt-1">
                                                <a 
                                                    :href="getBarcodeUrl(visitor)" 
                                                    target="_blank"
                                                    class="d-inline-block"
                                                    title="View QR Code"
                                                    style="display: block;"
                                                >
                                                    <img 
                                                        :src="getBarcodeUrl(visitor)" 
                                                        :alt="visitor.barcode"
                                                        class="img-thumbnail"
                                                        style="width: 120px; height: 120px; object-fit: contain; cursor: pointer; border: 2px solid #e0e0e0; border-radius: 8px; background: white; padding: 8px;"
                                                        @error="($event) => handleImageError($event, visitor)"
                                                    />
                                                </a>
                                                <small class="text-muted d-block mt-1" style="font-size: 10px;">Click to view full size</small>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Created Date -->
                                <td>
                                    <div class="userDatatable-content">
                                        {{ formatDate(visitor.created_at) }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="userDatatable-content">
                                        <div class="d-flex align-items-center gap-2">
                                            <button
                                                @click="emit('edit', visitor)"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit Visitor"
                                                style="padding: 4px 8px; min-width: 32px; height: 32px;"
                                            >
                                                ✏️
                                            </button>
                                            <button
                                                @click="emit('delete', visitor)"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete Visitor"
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
        <div v-if="visitors.length === 0" class="text-center py-5">
            <div class="mb-3">
                <span style="font-size: 48px;">👥</span>
            </div>
            <h6 class="text-muted">No visitors found</h6>
            <p class="text-muted mb-0">Get started by registering a new visitor.</p>
        </div>
    </div>
</template>

