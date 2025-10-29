<script setup lang="ts">
import { onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    stats: {
        roles: number;
        users: number;
        states: number;
        cities: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// Initialize Feather icons
onMounted(() => {
    if (typeof window !== 'undefined' && (window as any).feather) {
        (window as any).feather.replace();
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row mt-4">
            <div class="col-12">
                <h4 class="color-dark fw-500 mb-4">Dashboard Overview</h4>
            </div>
        </div>

        <div class="row">
            <!-- Roles Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <Link href="/roles" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Total Roles</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.roles }}</h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="shield" class="text-primary" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-primary small">View all roles →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Users Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <Link href="/users" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Total Users</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.users }}</h3>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="users" class="text-success" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-success small">View all users →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- States Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <Link href="/states" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Total States</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.states }}</h3>
                                </div>
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="map-pin" class="text-info" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-info small">View all states →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Cities Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <Link href="/cities" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Total Cities</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.cities }}</h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="map" class="text-warning" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-warning small">View all cities →</span>
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
