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
                <div class="d-flex justify-content-center align-items-center mb-4" style="min-height: 40vh;">
                    <img
                        src="https://growedgerealty.com/assets/images/logo/logo_1.png"
                        alt="Logo"
                        style="max-height: 160px; height: auto; width: auto;"
                    />
                </div>
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
