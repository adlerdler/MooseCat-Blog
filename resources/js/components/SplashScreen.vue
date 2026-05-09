<script setup>
import { ref, onMounted } from 'vue';
import { Hexagon, Sparkles } from 'lucide-vue-next';

const emit = defineEmits(['complete']);

const isVisible = ref(true);

onMounted(() => {
    setTimeout(() => {
        isVisible.value = false;
        setTimeout(() => {
            emit('complete');
        }, 500);
    }, 2500);
});
</script>

<template>
    <Teleport to="body">
        <div
            v-if="isVisible"
            class="fixed inset-0 z-[9999] bg-black flex items-center justify-center overflow-hidden"
        >
            <div class="absolute inset-0">
                <div class="absolute bottom-0 left-0 w-32 h-32 md:w-40 md:h-40 opacity-20 animate-spin-slow">
                    <Hexagon
                        :size="160"
                        class="text-accent rotate-45 -translate-x-1/2 translate-y-1/3"
                        :stroke-width="12"
                    />
                </div>

                <div class="absolute top-5 right-5 opacity-10 animate-pulse">
                    <Sparkles :size="48" class="text-white" />
                </div>
            </div>

            <div class="relative z-10 w-full">
                <div class="w-full h-40 md:h-52 bg-accent flex items-center justify-center animate-banner">
                    <div class="text-center">
                        <h1 class="font-black text-5xl md:text-7xl lg:text-8xl text-white tracking-tighter leading-none animate-title">
                            ARCHYX
                        </h1>

                        <div class="mt-4 inline-block bg-white px-4 py-1 animate-subtitle">
                            <span class="font-bold text-lg md:text-xl text-black tracking-[0.3em]">
                                VOL. 2026
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex gap-2 animate-dots">
                <span class="w-2 h-2 bg-white rounded-full animate-bounce"></span>
                <span class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                <span class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
            </div>
        </div>
    </Teleport>
</template>

<style lang="scss" scoped>
// 动画变量
$animation-duration: 0.8s;
$ease-out: ease-out;
$forwards: forwards;

// 动画关键帧
@keyframes banner {
    from { transform: scaleX(0); }
    to { transform: scaleX(1); }
}

@keyframes title {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes subtitle {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes dots {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

// 动画类
.animate-banner {
    animation: banner $animation-duration $ease-out $forwards;
    transform-origin: left;
}

.animate-title {
    animation: title 0.6s $ease-out 0.3s $forwards;
    opacity: 0;
}

.animate-subtitle {
    animation: subtitle 0.6s $ease-out 0.6s $forwards;
    opacity: 0;
}

.animate-dots {
    animation: dots 0.5s $ease-out 1s $forwards;
    opacity: 0;
}

.animate-spin-slow {
    animation: spin-slow 20s linear infinite;
}
</style>