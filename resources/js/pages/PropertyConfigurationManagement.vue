<script setup lang="ts">
import { onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    stats: {
        constructionTypes: number;
        categories: number;
        subCategories: number;
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


