<script setup lang="ts">
import { onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    stats: {
        constructionTypes: number;
        categories: number;
        subCategories: number;
        priorityTypes: number;
        propertySources: number;
        ownerTypes: number;
        furnitureTypes: number;
        propertyZones: number;
        amenities: number;
        propertyConstructionDocuments: number;
    };
}

defineProps<Props>();

onMounted(() => {
    if (typeof window !== 'undefined' && (window as any).feather) {
        (window as any).feather.replace();
    }
});
</script>

<template>
    <Head title="Property Configuration Management" />

    <AppLayout :breadcrumbs="[{ title: 'Property Configuration Management', href: '/management/property-configuration' }]">
        <div class="row mt-4">
            <div class="col-12">
                <h4 class="color-dark fw-500 mb-4">Property Configuration Management</h4>
            </div>
        </div>

        <div class="row">
            <!-- Construction Types Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/construction-types" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Construction Types</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.constructionTypes }}</h3>
                                </div>
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="grid" class="text-info" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-info small">Manage construction types →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Categories Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/categories" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Categories</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.categories }}</h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="layers" class="text-warning" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-warning small">Manage categories →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Sub Categories Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/sub-categories" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Sub Categories</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.subCategories }}</h3>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="folder" class="text-success" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-success small">Manage sub categories →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Priority Types Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/priority-types" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Priority Types</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.priorityTypes }}</h3>
                                </div>
                                <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="flag" class="text-danger" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-danger small">Manage priority types →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Property Sources Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/property-sources" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Property Sources</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.propertySources }}</h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="link" class="text-primary" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-primary small">Manage property sources →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Owner Types Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/owner-types" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Owner Types</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.ownerTypes }}</h3>
                                </div>
                                <div class="bg-secondary bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="user-check" class="text-secondary" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-secondary small">Manage owner types →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Furniture Types Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/furniture-types" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Furniture Types</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.furnitureTypes }}</h3>
                                </div>
                                <div class="bg-dark bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="box" class="text-dark" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-dark small">Manage furniture types →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Property Zones Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/property-zones" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Property Zones</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.propertyZones }}</h3>
                                </div>
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="map" class="text-info" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-info small">Manage property zones →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Amenities Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/amenities" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Amenities</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.amenities }}</h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="star" class="text-warning" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-warning small">Manage amenities →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Property Construction Documents Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/property-construction-documents" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Construction Documents</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.propertyConstructionDocuments }}</h3>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="file-text" class="text-success" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-success small">Manage documents →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.transition {
    transition: all 0.3s ease;
}

.card {
    cursor: pointer;
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card:hover {
    border-color: rgba(0, 0, 0, 0.2);
}
</style>


