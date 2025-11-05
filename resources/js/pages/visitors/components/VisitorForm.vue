<script setup lang="ts">
interface Visitor {
    id: number;
    name: string;
    mobile: string;
    barcode: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    form: any;
    editingVisitor: Visitor | null;
    errors: Record<string, string[]>;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    submit: [];
    cancel: [];
}>();
</script>

<template>
    <div>
        <!-- Visitor Name -->
        <div class="form-group mb-2">
            <label for="name" class="form-label mb-1" style="font-size: 14px;">
                Visitor Name <span class="text-danger">*</span>
            </label>
            <input
                id="name"
                v-model="form.name"
                type="text"
                class="form-control form-control-sm"
                :class="{ 'is-invalid': errors.name }"
                placeholder="Enter visitor name"
                required
            />
            <div v-if="errors.name" class="invalid-feedback" style="font-size: 12px;">
                {{ errors.name[0] }}
            </div>
        </div>

        <!-- Mobile Number -->
        <div class="form-group mb-0">
            <label for="mobile" class="form-label mb-1" style="font-size: 14px;">
                Mobile Number <span class="text-danger">*</span>
            </label>
            <input
                id="mobile"
                v-model="form.mobile"
                type="tel"
                class="form-control form-control-sm"
                :class="{ 'is-invalid': errors.mobile }"
                placeholder="Enter mobile number"
                required
            />
            <div v-if="errors.mobile" class="invalid-feedback" style="font-size: 12px;">
                {{ errors.mobile[0] }}
            </div>
            <small class="text-muted" style="font-size: 11px;">
                Barcode will be sent to this number via WhatsApp
            </small>
        </div>
    </div>
</template>


