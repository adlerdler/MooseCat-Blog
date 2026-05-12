<script setup>
/**
 * Welcome.vue - 欢迎页面
 * 
 * 功能说明：
 * - 网站启动画面的中转页面
 * - 显示加载动画，完成后跳转到首页
 * 
 * 使用场景：
 * - 作为 SPA 首次加载的入口
 * - 配合路由守卫实现加载过渡效果
 */
import { ref, nextTick } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const showSplash = ref(true);
const isTransitioning = ref(false);

const handleSplashComplete = async () => {
    isTransitioning.value = true;
    
    await nextTick();
    
    setTimeout(() => {
        router.push('/home');
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