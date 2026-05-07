<script setup>
import { ref, onMounted } from 'vue';

const emit = defineEmits(['complete']);

const isVisible = ref(true);

onMounted(() => {
    setTimeout(() => {
        isVisible.value = false;
        setTimeout(() => {
            emit('complete');
        }, 500);
    }, 3000);
});
</script>

<template>
    <Teleport to="body">
        <Transition name="splash">
            <div
                v-if="isVisible"
                class="fixed inset-0 z-[9999] bg-black flex items-center justify-center overflow-hidden"
            >
                <div class="absolute inset-0">
                    <div class="absolute bottom-0 left-0 w-32 h-32 border-[12px] border-[#CF202E] opacity-20 rotate-45 -translate-x-1/2 translate-y-1/3 md:w-40 md:h-40"></div>
                    <div class="absolute top-5 right-5 w-12 h-12 bg-white opacity-10"></div>
                </div>

                <div class="relative z-10 w-full">
                    <div class="relative">
                        <Transition name="banner">
                            <div
                                v-if="isVisible"
                                class="w-full h-40 md:h-52 bg-[#CF202E] flex items-center justify-center"
                            >
                                <div class="text-center">
                                    <Transition name="title">
                                        <h1
                                            v-if="isVisible"
                                            class="font-black text-5xl md:text-7xl lg:text-8xl text-white tracking-tighter leading-none"
                                        >
                                            ARCHYX
                                        </h1>
                                    </Transition>
                                    <Transition name="subtitle">
                                        <div
                                            v-if="isVisible"
                                            class="mt-4 inline-block bg-white px-4 py-1"
                                        >
                                            <span class="font-bold text-lg md:text-xl text-black tracking-[0.3em]">
                                                VOL. 2026
                                            </span>
                                        </div>
                                    </Transition>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style>
.splash-enter-active {
    opacity: 1;
}

.splash-leave-active {
    transition: opacity 0.5s ease-out;
}

.splash-leave-to {
    opacity: 0;
}

.banner-enter-active {
    transition: transform 0.8s ease-out;
}

.banner-enter-from {
    transform: scaleX(0);
    transform-origin: left;
}

.title-enter-active {
    transition: all 0.6s ease-out 0.3s;
}

.title-enter-from {
    opacity: 0;
    transform: translateY(20px);
}

.subtitle-enter-active {
    transition: all 0.6s ease-out 0.6s;
}

.subtitle-enter-from {
    opacity: 0;
    transform: translateX(-20px);
}
</style>