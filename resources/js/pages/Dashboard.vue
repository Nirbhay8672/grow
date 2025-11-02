<script setup lang="ts">
import { onMounted, ref, nextTick } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

interface Props {
    stats: {
        roles: number;
        users: number;
        states: number;
        cities: number;
        districts: number;
        localities: number;
        talukas: number;
        villages: number;
        builders: number;
        measurementUnits: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const lineChartRef = ref<HTMLCanvasElement | null>(null);
const barChartRef = ref<HTMLCanvasElement | null>(null);
const pieChartRef = ref<HTMLCanvasElement | null>(null);

// Initialize Feather icons and charts
onMounted(async () => {
    // Wait for Chart.js to be loaded
    const checkChart = setInterval(() => {
        if (typeof window !== 'undefined' && (window as any).Chart) {
            clearInterval(checkChart);
            if ((window as any).feather) {
                (window as any).feather.replace();
            }
            nextTick().then(() => {
                initCharts();
                // Replace icons again after charts are initialized
                if ((window as any).feather) {
                    setTimeout(() => {
                        (window as any).feather.replace();
                    }, 200);
                }
            });
        }
    }, 100);

    // Timeout after 5 seconds
    setTimeout(() => {
        clearInterval(checkChart);
        if (typeof window !== 'undefined' && (window as any).feather) {
            (window as any).feather.replace();
        }
        if (typeof window !== 'undefined' && (window as any).Chart) {
            nextTick().then(() => {
                initCharts();
                // Replace icons again after charts are initialized
                if ((window as any).feather) {
                    setTimeout(() => {
                        (window as any).feather.replace();
                    }, 200);
                }
            });
        }
    }, 5000);
    
    // Also replace icons immediately
    if (typeof window !== 'undefined' && (window as any).feather) {
        (window as any).feather.replace();
    }
});

function initCharts() {
    if (typeof window === 'undefined' || !(window as any).Chart) {
        console.warn('Chart.js not available');
        return;
    }

    const Chart = (window as any).Chart;

    // Line Chart
    if (lineChartRef.value) {
        const lineCtx = lineChartRef.value.getContext('2d');
        if (lineCtx) {
            lineChartRef.value.height = 300;
            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'Revenue',
                        data: [12, 19, 15, 25, 22, 30, 28],
                        borderColor: '#14345b',
                        backgroundColor: 'rgba(20, 52, 91, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false,
                    },
                    scales: {
                        yAxes: [{
                            beginAtZero: true,
                            gridLines: {
                                color: 'rgba(0, 0, 0, 0.05)',
                            },
                            ticks: {
                                beginAtZero: true,
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false,
                            },
                        }],
                    },
                },
            });
        }
    }

    // Bar Chart
    if (barChartRef.value) {
        const barCtx = barChartRef.value.getContext('2d');
        if (barCtx) {
            barChartRef.value.height = 350;
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: ['Users', 'States', 'Cities', 'Districts', 'Builders'],
                    datasets: [{
                        label: 'Count',
                        data: [
                            props.stats.users,
                            props.stats.states,
                            props.stats.cities,
                            props.stats.districts,
                            props.stats.builders,
                        ],
                        backgroundColor: [
                            '#14345b',
                            '#20C997',
                            '#2C99FF',
                            '#FF69A5',
                            '#FA8B0C',
                        ],
                        borderColor: [
                            '#14345b',
                            '#20C997',
                            '#2C99FF',
                            '#FF69A5',
                            '#FA8B0C',
                        ],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false,
                    },
                    scales: {
                        yAxes: [{
                            beginAtZero: true,
                            gridLines: {
                                color: 'rgba(0, 0, 0, 0.05)',
                            },
                            ticks: {
                                beginAtZero: true,
                            },
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false,
                            },
                        }],
                    },
                },
            });
        }
    }

    // Pie Chart
    if (pieChartRef.value) {
        const pieCtx = pieChartRef.value.getContext('2d');
        if (pieCtx) {
            pieChartRef.value.height = 300;
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: ['Users', 'Roles', 'Locations', 'Configuration'],
                    datasets: [{
                        data: [
                            props.stats.users,
                            props.stats.roles,
                            props.stats.states + props.stats.cities + props.stats.districts,
                            props.stats.builders + props.stats.measurementUnits,
                        ],
                        backgroundColor: [
                            '#14345b',
                            '#20C997',
                            '#2C99FF',
                            '#FF69A5',
                        ],
                        borderColor: '#fff',
                        borderWidth: 2,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 6,
                            usePointStyle: true,
                        },
                    },
                },
            });
        }
    }
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row mt-4">
            <!-- Stat Tiles -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Users</h6>
                                <h2 class="mb-0">{{ stats.users }}</h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i data-feather="users" class="text-primary" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Roles</h6>
                                <h2 class="mb-0">{{ stats.roles }}</h2>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i data-feather="shield" class="text-success" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">States</h6>
                                <h2 class="mb-0">{{ stats.states }}</h2>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i data-feather="map-pin" class="text-info" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Cities</h6>
                                <h2 class="mb-0">{{ stats.cities }}</h2>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i data-feather="map" class="text-warning" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Districts</h6>
                                <h2 class="mb-0">{{ stats.districts }}</h2>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i data-feather="layers" class="text-danger" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Localities</h6>
                                <h2 class="mb-0">{{ stats.localities }}</h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i data-feather="navigation" class="text-primary" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Talukas</h6>
                                <h2 class="mb-0">{{ stats.talukas }}</h2>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i data-feather="grid" class="text-success" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Villages</h6>
                                <h2 class="mb-0">{{ stats.villages }}</h2>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i data-feather="home" class="text-info" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Builders</h6>
                                <h2 class="mb-0">{{ stats.builders }}</h2>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i data-feather="tool" class="text-warning" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Measurement Units</h6>
                                <h2 class="mb-0">{{ stats.measurementUnits }}</h2>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i data-feather="maximize-2" class="text-danger" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mt-4">
            <!-- Line Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Revenue Overview</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas ref="lineChartRef"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie Chart -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas ref="pieChartRef"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bar Chart -->
        <div class="row mt-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Data Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 350px;">
                            <canvas ref="barChartRef"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.card-body h2 {
    font-size: 2rem;
    font-weight: 600;
    color: #14345b;
}

.card-body h6 {
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bg-opacity-10 {
    background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}
</style>
