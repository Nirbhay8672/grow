<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Plus, Eye, Edit, Trash2 } from 'lucide-vue-next';

interface Project {
    id: number;
    project_name: string;
    builder_website?: string;
    address?: string;
    pincode?: string;
    rera_no?: string;
    project_status?: string;
    is_active: boolean;
    created_at: string;
    builder?: {
        id: number;
        name: string;
    };
    state?: {
        id: number;
        name: string;
    };
    city?: {
        id: number;
        name: string;
    };
    locality?: {
        id: number;
        name: string;
    };
    contacts?: Array<{
        id: number;
        name: string;
        mobile: string;
        email?: string;
        designation?: string;
    }>;
}

interface Props {
    projects: {
        data: Project[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Projects',
        href: '/projects',
    },
];

const deleteProject = (projectId: number) => {
    if (confirm('Are you sure you want to delete this project?')) {
        router.delete(`/projects/${projectId}`, {
            onSuccess: () => {
                // Project deleted successfully
            },
        });
    }
};
</script>

<template>
    <Head title="Projects" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mt-4">
                    <div class="card-header color-dark fw-500 d-flex justify-content-between align-items-center">
                        <span>Projects Management</span>
                        <Link
                            href="/projects/create"
                            class="btn btn-primary btn-sm btn-default btn-squared text-capitalize lh-normal px-3 py-2"
                        >
                            <Plus :size="16" class="me-1" />
                            Create Project
                        </Link>
                    </div>
                    <div class="card-body p-0">
                        <div class="table4 p-25 bg-white mb-30">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th>
                                                <span class="userDatatable-title">Project Name</span>
                                            </th>
                                            <th>
                                                <span class="userDatatable-title">Builder</span>
                                            </th>
                                            <th>
                                                <span class="userDatatable-title">Location</span>
                                            </th>
                                            <th>
                                                <span class="userDatatable-title">Contacts</span>
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
                                        <tr v-if="projects.data.length === 0">
                                            <td colspan="7" class="text-center py-4">
                                                <p class="text-muted mb-0">No projects found. <Link href="/projects/create" class="text-primary">Create your first project</Link></p>
                                            </td>
                                        </tr>
                                        <tr v-for="project in projects.data" :key="project.id">
                                            <td>
                                                <div class="userDatatable-content">
                                                    <strong>{{ project.project_name }}</strong>
                                                    <div v-if="project.rera_no" class="text-muted" style="font-size: 12px;">
                                                        RERA: {{ project.rera_no }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="userDatatable-content">
                                                    {{ project.builder?.name || 'N/A' }}
                                                    <div v-if="project.builder_website" class="text-muted" style="font-size: 12px;">
                                                        <a :href="project.builder_website" target="_blank" class="text-primary">
                                                            Website
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="userDatatable-content">
                                                    <div v-if="project.locality">
                                                        {{ project.locality.name }}
                                                        <span v-if="project.city">, {{ project.city.name }}</span>
                                                        <span v-if="project.state">, {{ project.state.name }}</span>
                                                    </div>
                                                    <div v-else class="text-muted">N/A</div>
                                                    <div v-if="project.pincode" class="text-muted" style="font-size: 12px;">
                                                        {{ project.pincode }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="userDatatable-content">
                                                    <div v-if="project.contacts && project.contacts.length > 0">
                                                        <div v-for="(contact, index) in project.contacts.slice(0, 2)" :key="contact.id" style="font-size: 13px;">
                                                            <strong>{{ contact.name }}</strong>
                                                            <div class="text-muted" style="font-size: 11px;">
                                                                {{ contact.mobile }}
                                                                <span v-if="contact.email"> | {{ contact.email }}</span>
                                                            </div>
                                                        </div>
                                                        <div v-if="project.contacts.length > 2" class="text-muted" style="font-size: 11px; margin-top: 4px;">
                                                            +{{ project.contacts.length - 2 }} more
                                                        </div>
                                                    </div>
                                                    <div v-else class="text-muted">No contacts</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span 
                                                    class="badge"
                                                    :class="project.is_active ? 'badge-success' : 'badge-danger'"
                                                >
                                                    {{ project.is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                                <div v-if="project.project_status" class="text-muted" style="font-size: 12px; margin-top: 4px;">
                                                    {{ project.project_status }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="userDatatable-content">
                                                    {{ new Date(project.created_at).toLocaleDateString() }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="userDatatable-content">
                                                    <ul class="orderDatatable_actions mb-0 d-flex flex-wrap gap-2">
                                                        <li>
                                                            <Link
                                                                :href="`/projects/${project.id}`"
                                                                class="btn btn-sm btn-primary"
                                                                title="View"
                                                            >
                                                                <Eye :size="14" />
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <Link
                                                                :href="`/projects/${project.id}/edit`"
                                                                class="btn btn-sm btn-warning"
                                                                title="Edit"
                                                            >
                                                                <Edit :size="14" />
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <button
                                                                @click="deleteProject(project.id)"
                                                                class="btn btn-sm btn-danger"
                                                                title="Delete"
                                                            >
                                                                <Trash2 :size="14" />
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div v-if="projects.last_page > 1" class="d-flex justify-content-between align-items-center p-3 border-top">
                                <div class="text-muted">
                                    Showing {{ ((projects.current_page - 1) * projects.per_page) + 1 }} to 
                                    {{ Math.min(projects.current_page * projects.per_page, projects.total) }} of 
                                    {{ projects.total }} entries
                                </div>
                                <div class="d-flex gap-2">
                                    <Link
                                        v-if="projects.current_page > 1"
                                        :href="`/projects?page=${projects.current_page - 1}`"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        Previous
                                    </Link>
                                    <Link
                                        v-if="projects.current_page < projects.last_page"
                                        :href="`/projects?page=${projects.current_page + 1}`"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        Next
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.userDatatable-content {
    font-size: 14px;
}

.orderDatatable_actions {
    list-style: none;
    padding: 0;
    margin: 0;
}

.orderDatatable_actions li {
    display: inline-block;
}

.orderDatatable_actions .btn {
    padding: 4px 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>

