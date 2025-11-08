<script setup lang="ts">
interface Props {
    currentStep: number;
}

defineProps<Props>();
</script>

<template>
    <div class="step-indicator-vertical">
        <div class="d-flex align-items-start step-item">
            <div class="d-flex flex-column align-items-center step-content">
                <div 
                    class="rounded-circle d-flex align-items-center justify-content-center step-circle"
                    :class="currentStep >= 1 ? 'active' : 'inactive'"
                >
                    1
                </div>
                <div 
                    class="step-connector-vertical"
                    :class="currentStep >= 2 ? 'active' : ''"
                ></div>
            </div>
            <div class="step-label-wrapper">
                <span 
                    class="step-label"
                    :class="currentStep >= 1 ? 'active' : 'inactive'"
                >
                    Project & Builder Information
                </span>
            </div>
        </div>
        <div class="d-flex align-items-start step-item">
            <div class="d-flex flex-column align-items-center step-content">
                <div 
                    class="rounded-circle d-flex align-items-center justify-content-center step-circle"
                    :class="currentStep >= 2 ? 'active' : 'inactive'"
                >
                    2
                </div>
                <div 
                    class="step-connector-vertical"
                    :class="currentStep >= 3 ? 'active' : ''"
                ></div>
            </div>
            <div class="step-label-wrapper">
                <span 
                    class="step-label"
                    :class="currentStep >= 2 ? 'active' : 'inactive'"
                >
                    Project Type & Basic Information   
                </span>
            </div>
        </div>
        <div class="d-flex align-items-start step-item">
            <div class="d-flex flex-column align-items-center step-content">
                <div 
                    class="rounded-circle d-flex align-items-center justify-content-center step-circle"
                    :class="currentStep >= 3 ? 'active' : 'inactive'"
                >
                    3
                </div>
            </div>
            <div class="step-label-wrapper">
                <span 
                    class="step-label"
                    :class="currentStep >= 3 ? 'active' : 'inactive'"
                >
                    Parking & Amenities
                </span>
            </div>
        </div>
    </div>
</template>

