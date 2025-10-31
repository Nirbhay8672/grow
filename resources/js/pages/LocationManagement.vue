<script setup lang="ts">
import { onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    stats: {
        states: number;
        cities: number;
        districts: number;
        localities: number;
        talukas: number;
        villages: number;
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
    <Head title="Location Management" />

    <AppLayout :breadcrumbs="[{ title: 'Location Management', href: '/management/locations' }]">
        <div class="row mt-4">
            <div class="col-12">
                <h4 class="color-dark fw-500 mb-4">Location Management</h4>
            </div>
        </div>

        <div class="row">
            <!-- States Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/states" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">States</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.states }}</h3>
                                </div>
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="map-pin" class="text-info" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-info small">Manage states →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Cities Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/cities" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Cities</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.cities }}</h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="map" class="text-warning" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-warning small">Manage cities →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Localities Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/localities" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Localities</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.localities }}</h3>
                                </div>
                                <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="map-pin" class="text-danger" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-danger small">Manage localities →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Districts Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/districts" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Districts</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.districts }}</h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="layers" class="text-primary" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-primary small">Manage districts →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Talukas Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/talukas" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Talukas</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.talukas ?? 0 }}</h3>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="layers" class="text-success" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-success small">Manage talukas →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Villages Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <Link href="/villages" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Villages</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.villages ?? 0 }}</h3>
                                </div>
                                <div class="bg-secondary bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="home" class="text-secondary" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-secondary small">Manage villages →</span>
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

