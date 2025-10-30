<script setup lang="ts">
import { onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    stats: {
        users: number;
        roles: number;
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
    <Head title="User Management" />

    <AppLayout :breadcrumbs="[{ title: 'User Management', href: '/management/users' }]">
        <div class="row mt-4">
            <div class="col-12">
                <h4 class="color-dark fw-500 mb-4">User Management</h4>
            </div>
        </div>

        <div class="row">
            <!-- Users Card -->
            <div class="col-md-6 col-lg-6 mb-4">
                <Link href="/users" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Users</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.users }}</h3>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="users" class="text-success" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-success small">Manage users →</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Roles Card -->
            <div class="col-md-6 col-lg-6 mb-4">
                <Link href="/roles" class="text-decoration-none">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-2">Roles</h6>
                                    <h3 class="mb-0 color-dark fw-bold">{{ stats.roles }}</h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <span data-feather="shield" class="text-primary" style="width: 32px; height: 32px;"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-primary small">Manage roles →</span>
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

