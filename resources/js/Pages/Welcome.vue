<script setup>
import { ref, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';

const showSplash = ref(true);
const isTransitioning = ref(false);

const handleSplashComplete = async () => {
    isTransitioning.value = true;
    
    await nextTick();
    
    setTimeout(() => {
        router.visit('/home');
    }, 300);
};
</script>

<template>
    <div class="app-container">
        <Transition name="splash-fade">
            <SplashScreen 
                v-if="showSplash" 
                :class="{ 'splash-fade-out': isTransitioning }"
                @complete="handleSplashComplete" 
            />
        </Transition>
    </div>
</template>

<style lang="scss" scoped>
.app-container {
    min-height: 100vh;
    background: #000;
}

.splash-fade-out {
    animation: fadeOut 0.3s ease-out forwards;
}

@keyframes fadeOut {
    0% {
        opacity: 1;
    }
    100% {
        opacity: 0;
    }
}
</style>