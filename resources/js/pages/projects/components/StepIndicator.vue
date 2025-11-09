<script setup lang="ts">
interface Props {
    currentStep: number;
}

defineProps<Props>();

const emit = defineEmits<{
    'step-click': [step: number];
}>();

const handleStepClick = (step: number) => {
    emit('step-click', step);
};
</script>

<template>
    <div class="step-indicator-vertical">
        <div 
            class="d-flex align-items-start step-item step-item-clickable"
            @click="handleStepClick(1)"
        >
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
        <div 
            class="d-flex align-items-start step-item step-item-clickable"
            @click="handleStepClick(2)"
        >
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
        <div 
            class="d-flex align-items-start step-item step-item-clickable"
            @click="handleStepClick(3)"
        >
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

<style scoped>
.step-item-clickable {
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 8px;
    margin: -8px;
    border-radius: 4px;
}

.step-item-clickable:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.step-item-clickable:hover .step-circle {
    transform: scale(1.1);
}

.step-item-clickable:hover .step-label {
    color: #1c467b;
    font-weight: 500;
}
</style>

