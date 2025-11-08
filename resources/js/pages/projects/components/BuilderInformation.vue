<script setup lang="ts">
interface Builder {
    id: number;
    name: string;
}

interface Props {
    builders: Builder[];
    form: {
        builder_id: string;
        builder_website: string;
    };
    errors: Record<string, string[]>;
    validateForm: () => void;
}

defineProps<Props>();
</script>

<template>
    <div class="mb-4">
        <h5 class="mb-3 section-title">Builder Information</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="builder_id" class="form-label">
                        Builder Name <span class="text-danger">*</span>
                    </label>
                    <select
                        id="builder_id"
                        v-model="form.builder_id"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.builder_id }"
                        @blur="validateForm"
                        required
                    >
                        <option value="">Select Builder</option>
                        <option 
                            v-for="builder in builders" 
                            :key="builder.id" 
                            :value="builder.id"
                        >
                            {{ builder.name }}
                        </option>
                    </select>
                    <div v-if="errors.builder_id" class="invalid-feedback">
                        {{ errors.builder_id[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="builder_website" class="form-label">Website</label>
                    <input
                        id="builder_website"
                        v-model="form.builder_website"
                        type="url"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.builder_website }"
                        placeholder="https://example.com"
                        @blur="validateForm"
                    />
                    <div v-if="errors.builder_website" class="invalid-feedback">
                        {{ errors.builder_website[0] }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

