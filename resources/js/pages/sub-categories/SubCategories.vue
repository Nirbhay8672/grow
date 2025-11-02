<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/custom/modal/Modal.vue';
import SubCategoryForm from '@/pages/sub-categories/components/SubCategoryForm.vue';
import SubCategoryTable from '@/pages/sub-categories/components/SubCategoryTable.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface ConstructionType {
    id: number;
    name: string;
}

interface Category {
    id: number;
    name: string;
    is_active: boolean;
    construction_type: ConstructionType | null;
}

interface SubCategory {
    id: number;
    name: string;
    category_id: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    category: Category | null;
}

interface Props {
    subCategories: SubCategory[];
    categories: Category[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Sub Categories',
        href: '/sub-categories',
    },
];

const showForm = ref(false);
const editingSubCategory = ref<SubCategory | null>(null);
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const toast = ref<{ show: boolean; message: string; type: 'success' | 'error' }>({
    show: false,
    message: '',
    type: 'success'
});

const form = reactive({
    name: '',
    category_id: '',
    is_active: true,
});

const resetForm = () => {
    form.name = '';
    form.category_id = '';
    form.is_active = true;
    errors.value = {};
    editingSubCategory.value = null;
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

const editSubCategory = (subCategory: SubCategory) => {
    editingSubCategory.value = subCategory;
    form.name = subCategory.name;
    form.category_id = subCategory.category_id.toString();
    form.is_active = subCategory.is_active;
    errors.value = {};
    showForm.value = true;
};

const createSubCategory = () => {
    resetForm();
    showForm.value = true;
};

const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};

    try {
        const formData = {
            ...form,
            category_id: parseInt(form.category_id),
        };

        if (editingSubCategory.value) {
            await axios.put(`/sub-categories/${editingSubCategory.value.id}`, formData);
            showToast('Sub Category updated successfully!');
        } else {
            await axios.post('/sub-categories', formData);
            showToast('Sub Category created successfully!');
        }
        
        resetForm();
        router.reload();
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            showToast('Please fix the validation errors.', 'error');
        } else {
            console.error('Error saving sub category:', error);
            showToast('An error occurred while saving the sub category.', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const deleteSubCategory = async (subCategory: SubCategory) => {
    const Swal = (window as any).Swal;
    
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the sub category "${subCategory.name}". This action cannot be undone!`,
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
            await axios.delete(`/sub-categories/${subCategory.id}`);
            showToast('Sub Category deleted successfully!');
            router.reload();
        } catch (error) {
            console.error('Error deleting sub category:', error);
            showToast('An error occurred while deleting the sub category.', 'error');
        }
    }
};
</script>

<template>
    <Head title="Sub Categories Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="row">
            <div class="col-12">
                <!-- Sub Category Table -->
                <SubCategoryTable
                    :sub-categories="subCategories"
                    @edit="editSubCategory"
                    @delete="deleteSubCategory"
                >
                    <template #header-action>
                        <button
                            @click="createSubCategory"
                            class="btn btn-primary btn-sm btn-default btn-squared text-capitalize lh-normal px-3 py-2"
                        >
                            <span data-feather="plus" class="me-1"></span>
                            Add Sub Category
                        </button>
                    </template>
                </SubCategoryTable>

                <!-- Sub Category Form Modal -->
                <Modal
                    :show="showForm"
                    :title="editingSubCategory ? 'Edit Sub Category' : 'Create Sub Category'"
                    :loading="loading"
                    :submit-label="editingSubCategory ? 'Update Sub Category' : 'Create Sub Category'"
                    size="sm"
                    @close="resetForm"
                    @submit="handleSubmit"
                >
                    <SubCategoryForm
                        :form="form"
                        :editing-sub-category="editingSubCategory"
                        :categories="categories"
                        :errors="errors"
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


