<script setup lang="ts">
interface Props {
    show: boolean;
    title: string;
    loading?: boolean;
    submitLabel?: string;
    cancelLabel?: string;
    size?: 'sm' | 'md' | 'lg' | 'xl';
}

const props = withDefaults(defineProps<Props>(), {
    loading: false,
    submitLabel: 'Save',
    cancelLabel: 'Cancel',
    size: 'lg',
});

const emit = defineEmits<{
    close: [];
    submit: [];
}>();

const sizeClasses = {
    sm: 'modal-sm',
    md: '',
    lg: 'modal-lg',
    xl: 'modal-xl',
};

const handleClose = () => {
    if (!props.loading) {
        emit('close');
    }
};

const handleSubmit = () => {
    if (!props.loading) {
        emit('submit');
    }
};
</script>

<template>
    <div
        v-if="show"
        class="modal fade show d-block"
        tabindex="-1"
        role="dialog"
        aria-hidden="false"
        style="background-color: rgba(0,0,0,0.5); z-index: 9999;"
        @click.self="handleClose"
    >
        <div 
            class="modal-dialog modal-dialog-centered"
            :class="sizeClasses[size]"
            role="document"
            @click.stop
        >
            <div class="modal-content">
                <div class="modal-header py-2 px-3">
                    <h5 class="modal-title mb-0" style="font-size: 16px; font-weight: 600;">
                        {{ title }}
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        @click="handleClose"
                        aria-label="Close"
                        :disabled="loading"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-3 px-3">
                    <slot />
                </div>
                <div class="modal-footer py-2 px-3">
                    <button
                        type="button"
                        class="btn btn-secondary btn-sm"
                        @click="handleClose"
                        :disabled="loading"
                        style="font-size: 13px;"
                    >
                        {{ cancelLabel }}
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        @click="handleSubmit"
                        :disabled="loading"
                        style="font-size: 13px;"
                    >
                        <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                        {{ submitLabel }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.modal {
    z-index: 9999;
}

.modal.show {
    display: block !important;
}

.modal-backdrop {
    z-index: 9998;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #000;
    opacity: 0.5;
    cursor: pointer;
    padding: 0.5rem 0.75rem;
}

.btn-close:hover {
    opacity: 0.75;
}

.btn-close:disabled {
    opacity: 0.25;
    cursor: not-allowed;
}

.modal-content {
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-header {
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
}
</style>

