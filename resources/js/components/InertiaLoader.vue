<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

const isLoading = ref(false);
let timeoutId: ReturnType<typeof setTimeout> | null = null;

const showLoader = () => {
    // Small delay to avoid flicker on fast navigation
    if (timeoutId) {
        clearTimeout(timeoutId);
    }
    timeoutId = setTimeout(() => {
        isLoading.value = true;
    }, 150);
};

const hideLoader = () => {
    if (timeoutId) {
        clearTimeout(timeoutId);
        timeoutId = null;
    }
    isLoading.value = false;
};

const handleStart = () => {
    showLoader();
};

const handleFinish = () => {
    hideLoader();
};

const handleError = () => {
    hideLoader();
};

onMounted(() => {
    // Listen to Inertia DOM events
    document.addEventListener('inertia:start', handleStart);
    document.addEventListener('inertia:finish', handleFinish);
    document.addEventListener('inertia:error', handleError);
});

onUnmounted(() => {
    if (timeoutId) {
        clearTimeout(timeoutId);
    }
    document.removeEventListener('inertia:start', handleStart);
    document.removeEventListener('inertia:finish', handleFinish);
    document.removeEventListener('inertia:error', handleError);
});
</script>

<template>
    <Transition name="fade">
        <div
            v-if="isLoading"
            class="inertia-loader-overlay"
            style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            "
        >
            <div class="d-flex flex-column align-items-center justify-content-center">
                <div class="atbd-spin-dots spin-lg">
                    <span class="spin-dot badge-dot dot-primary"></span>
                    <span class="spin-dot badge-dot dot-primary"></span>
                    <span class="spin-dot badge-dot dot-primary"></span>
                    <span class="spin-dot badge-dot dot-primary"></span>
                </div>
                <p class="text-white mt-3 mb-0">Loading...</p>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

