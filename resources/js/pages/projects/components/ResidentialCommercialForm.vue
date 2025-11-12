<script setup lang="ts">
import Category4Form from './Category4Form.vue';
import Category5Form from './Category5Form.vue';
import Category6Form from './Category6Form.vue';
import RetailSection from './RetailSection.vue';

interface Props {
    form: any;
    errors: any;
    measurementUnits: any[];
    category4SubCategories: any[];
    category4AllSubCategories: any[];
    category5SubCategories: any[];
    category5AllSubCategories: any[];
    retailSubCategories: any[];
}

const props = defineProps<Props>();
</script>

<template>
    <div>
        <!-- Residential Category Forms (same as construction type 2) -->
        <!-- Show forms only when a category is selected -->
        <div v-if="form.category_id">

            <!-- Category 4 Form (FLAT - when category_id === '4' or '7' for VILA/BUNGLOW) -->
            <Category4Form
                v-if="form.category_id === '4' || form.category_id === '7'"
                :form="form"
                :errors="errors"
                :measurement-units="measurementUnits"
                :sub-categories="category4SubCategories"
                :all-sub-categories="category4AllSubCategories"
            />

            <!-- Category 5 Form (PENTHOUSE - when category_id === '5') -->
            <Category5Form
                v-else-if="form.category_id === '5'"
                :form="form"
                :errors="errors"
                :measurement-units="measurementUnits"
                :sub-categories="category5SubCategories"
                :all-sub-categories="category5AllSubCategories"
            />

            <!-- Category 6 Form (PLOT - when category_id === '6') -->
            <Category6Form
                v-else-if="form.category_id === '6'"
                :form="form"
                :errors="errors"
                :measurement-units="measurementUnits"
            />
        </div>

        <!-- Message when no category is selected -->
        <div v-else class="alert alert-info">
            Please select a category to continue.
        </div>

        <!-- Retail Section (always shown for construction type 4, after category form) -->
        <RetailSection
            v-if="form.category_id"
            :form="form"
            :errors="errors"
            :measurement-units="measurementUnits"
            :retail-sub-categories="retailSubCategories"
        />
    </div>
</template>

