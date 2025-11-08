<script setup lang="ts">
interface Props {
    currentStep: number;
}

defineProps<Props>();
</script>

<template>
    <div class="d-flex justify-content-center mb-4">
        <div class="d-flex align-items-center step-indicator-container">
            <div class="d-flex flex-column align-items-center">
                <div 
                    class="rounded-circle d-flex align-items-center justify-content-center step-circle"
                    :class="currentStep >= 1 ? 'active' : 'inactive'"
                >
                    1
                </div>
                <span 
                    class="mt-2 step-label"
                    :class="currentStep >= 1 ? 'active' : 'inactive'"
                >
                    Project & Builder Information
                </span>
            </div>
            <div class="step-connector"></div>
            <div class="d-flex flex-column align-items-center">
                <div 
                    class="rounded-circle d-flex align-items-center justify-content-center step-circle"
                    :class="currentStep >= 2 ? 'active' : 'inactive'"
                >
                    2
                </div>
                <span 
                    class="mt-2 step-label"
                    :class="currentStep >= 2 ? 'active' : 'inactive'"
                >
                    Construction Type
                </span>
            </div>
            <div class="step-connector"></div>
            <div class="d-flex flex-column align-items-center">
                <div 
                    class="rounded-circle d-flex align-items-center justify-content-center step-circle"
                    :class="currentStep >= 3 ? 'active' : 'inactive'"
                >
                    3
                </div>
                <span 
                    class="mt-2 step-label"
                    :class="currentStep >= 3 ? 'active' : 'inactive'"
                >
                    Parking & Amenities
                </span>
            </div>
        </div>
    </div>
</template>

