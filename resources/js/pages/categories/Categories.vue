<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/custom/modal/Modal.vue';
import CategoryForm from '@/pages/categories/components/CategoryForm.vue';
import CategoryTable from '@/pages/categories/components/CategoryTable.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface ConstructionType {
    id: number;
    name: string;
    is_active: boolean;
}

interface ConstructionTypeRelation {
    id: number;
    name: string;
}

interface Category {
    id: number;
    name: string;
    is_active: boolean;
    user_id: number;
    construction_type_id: number | null;
    construction_type: ConstructionTypeRelation | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    categories: Category[];
    constructionTypes: ConstructionType[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Categories',
        href: '/categories',
    },
];

const showForm = ref(false);
const editingCategory = ref<Category | null>(null);
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const toast = ref<{ show: boolean; message: string; type: 'success' | 'error' }>({
    show: false,
    message: '',
    type: 'success'
});

const form = reactive({
    name: '',
    construction_type_id: null as number | null,
    is_active: true,
});

const resetForm = () => {
    form.name = '';
    form.construction_type_id = null;
    form.is_active = true;
    errors.value = {};
    editingCategory.value = null;
    showForm.value = false;
};

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
    toast.value = {
        show: true,
        message,
        type
    };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const editCategory = (category: Category) => {
    editingCategory.value = category;
    form.name = category.name;
    form.construction_type_id = category.construction_type_id;
    form.is_active = category.is_active;
    errors.value = {};
    showForm.value = true;
};

const createCategory = () => {
    resetForm();
    showForm.value = true;
};

const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};

    try {
        if (editingCategory.value) {
            await axios.put(`/categories/${editingCategory.value.id}`, form);
            showToast('Category updated successfully!');
        } else {
            await axios.post('/categories', form);
            showToast('Category created successfully!');
        }
        
        resetForm();
        router.reload();
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            showToast('Please fix the validation errors.', 'error');
        } else {
            console.error('Error saving category:', error);
            showToast('An error occurred while saving the category.', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const deleteCategory = async (category: Category) => {
    const Swal = (window as any).Swal;
    
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the category "${category.name}". This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    });

    if (result.isConfirmed) {
        try {
            await axios.delete(`/categories/${category.id}`);
            showToast('Category deleted successfully!');
            router.reload();
        } catch (error) {
            console.error('Error deleting category:', error);
            showToast('An error occurred while deleting the category.', 'error');
        }
    }
};
</script>

<template>
    <Head title="Categories Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <!-- Category Table -->
                <CategoryTable
                    :categories="categories"
                    @edit="editCategory"
                    @delete="deleteCategory"
                >
                    <template #header-action>
                        <button
                            @click="createCategory"
                            class="btn btn-primary btn-sm btn-default btn-squared text-capitalize lh-normal px-3 py-2"
                        >
                            <span data-feather="plus" class="me-1"></span>
                            Add Category
                        </button>
                    </template>
                </CategoryTable>

                <!-- Category Form Modal -->
                <Modal
                    :show="showForm"
                    :title="editingCategory ? 'Edit Category' : 'Create Category'"
                    :loading="loading"
                    :submit-label="editingCategory ? 'Update Category' : 'Create Category'"
                    size="sm"
                    @close="resetForm"
                    @submit="handleSubmit"
                >
                    <CategoryForm
                        :form="form"
                        :editing-category="editingCategory"
                        :errors="errors"
                        :construction-types="constructionTypes"
                        @submit="handleSubmit"
                        @cancel="resetForm"
                    />
                </Modal>
            </div>
        </div>

        <!-- Toast Notification -->
        <div
            v-if="toast.show"
            class="toast-notification"
            :class="toast.type === 'success' ? 'toast-success' : 'toast-error'"
            style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; padding: 12px 16px; border-radius: 8px; color: white; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.15);"
        >
            <div class="d-flex align-items-center">
                <span class="me-2" style="font-size: 18px;">
                    {{ toast.type === 'success' ? '✅' : '❌' }}
                </span>
                <span>{{ toast.message }}</span>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.toast-success {
    background-color: #28a745;
}

.toast-error {
    background-color: #dc3545;
}

.toast-notification {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>


