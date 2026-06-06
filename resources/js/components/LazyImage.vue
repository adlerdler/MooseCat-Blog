<script setup>
/**
 * LazyImage.vue - 懒加载图片组件
 *
 * 功能说明：
 * - 根据 siteConfig.lazy_load 配置决定是否启用懒加载
 * - 使用 IntersectionObserver API 实现懒加载
 * - 提供加载占位符和淡入效果
 *
 * 使用方式：
 * <LazyImage src="..." alt="..." class="absolute inset-0 w-full h-full object-cover opacity-50" />
 */
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useOptimizationConfig } from '@/composables/useOptimizationConfig';

const props = defineProps({
  src: { type: String, required: true },
  alt: { type: String, default: '' },
  class: { type: String, default: '' },
  width: { type: [String, Number], default: undefined },
  height: { type: [String, Number], default: undefined },
});

const { lazyLoadEnabled } = useOptimizationConfig();
const containerRef = ref(null);
const isVisible = ref(!lazyLoadEnabled.value);
const isLoaded = ref(false);
let observer = null;

const handleIntersect = (entries) => {
  const entry = entries[0];
  if (entry.isIntersecting) {
    isVisible.value = true;
    if (observer) {
      observer.disconnect();
    }
  }
};

onMounted(() => {
  // 如果懒加载被禁用，直接显示图片
  if (!lazyLoadEnabled.value) {
    isVisible.value = true;
    return;
  }

  observer = new IntersectionObserver(handleIntersect, {
    rootMargin: '50px',
    threshold: 0.1,
  });

  if (containerRef.value) {
    observer.observe(containerRef.value);
  }
});

onBeforeUnmount(() => {
  if (observer) {
    observer.disconnect();
  }
});
</script>

<template>
  <!-- 容器只接收定位样式，不添加额外的 relative/absolute -->
  <div ref="containerRef" :class="class">
    <!-- 占位符 -->
    <div
      v-if="lazyLoadEnabled && !isLoaded"
      class="absolute inset-0 bg-gray-100 dark:bg-gray-800 animate-pulse"
    ></div>

    <!-- 图片 -->
    <img
      v-if="isVisible"
      :src="src"
      :alt="alt"
      :width="width"
      :height="height"
      @load="isLoaded = true"
      class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
      :class="isLoaded ? 'opacity-100' : 'opacity-0'"
    />
  </div>
</template>
