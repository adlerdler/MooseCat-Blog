<script setup>
/**
 * SplashScreen.vue - 启动画面组件（含数据加载进度）
 * 
 * 功能说明：
 * - 网站首次访问的品牌展示动画
 * - 由父组件通过 v-if 控制挂载/卸载
 * - 2.5秒后自动触发 @complete 事件
 * - 期间显示数据加载进度条
 * 
 * 控制逻辑由父组件（Home.vue）通过 localStorage 管理：
 * - localStorage key: 'archyx_splash_shown'
 * - 仅根路径 "/" 首次访问时显示，之后永久不再显示
 * 
 * 使用示例：
 * <SplashScreen v-if="showSplash" :progress="dataProgress" @complete="handleSplashComplete" />
 */
import { ref, onMounted, watch } from 'vue';
import { Hexagon, Sparkles } from 'lucide-vue-next';

const props = defineProps({
  progress: { type: Number, default: 0 }
});

const emit = defineEmits(['complete']);

const isVisible = ref(true);
const displayProgress = ref(0);

// 同步父组件传入的进度
watch(() => props.progress, (val) => {
  displayProgress.value = val;
}, { immediate: true });

onMounted(() => {
    // 2.5秒后自动关闭并通知父组件
    setTimeout(() => {
        // 如果进度还没到100%，强制到100%
        if (displayProgress.value < 100) {
            displayProgress.value = 100;
        }
        setTimeout(() => {
            isVisible.value = false;
            setTimeout(() => {
                emit('complete');
            }, 300);
        }, 200);
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
                            ARKHYX
                        </h1>

                        <div class="mt-4 inline-block bg-white px-4 py-1 animate-subtitle">
                            <span class="font-bold text-lg md:text-xl text-black tracking-[0.3em]">
                                VOL. 2026
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 加载进度条 -->
                <div class="w-full h-1 bg-white/20 mt-2">
                    <div
                        class="h-full bg-white transition-all duration-300 ease-out"
                        :style="{ width: `${displayProgress}%` }"
                    />
                </div>

                <!-- 加载状态文字 -->
                <div class="text-center mt-3">
                    <span class="text-white/40 text-[10px] font-mono tracking-[0.3em] uppercase">
                        INITIALIZING SYSTEM... {{ displayProgress }}%
                    </span>
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