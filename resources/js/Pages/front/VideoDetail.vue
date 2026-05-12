<script setup>
/**
 * VideoDetail.vue - 视频详情页
 * 
 * 功能说明：
 * - 展示单个视频的播放页面
 * - 支持 YouTube 和 Bilibili 嵌入式播放器
 * - 动态生成嵌入 URL
 * 
 * 交互功能：
 * - 返回上一页按钮
 * - 返回顶部按钮
 */
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { Motion, AnimatePresence } from 'motion-v';
import { useTheme } from '../../composables/useTheme';
import { VIDEOS } from '../../data/videos';
import { ArrowLeft, ArrowUp } from 'lucide-vue-next';

const { initTheme } = useTheme();
const route = useRoute();
const router = useRouter();
const showBackToTop = ref(false);

const video = computed(() => {
  return VIDEOS.find((v) => v.id === route.params.id);
});

// 动态更新页面标题
const updatePageTitle = () => {
  if (video.value?.title) {
    document.title = `VIDEO // ${video.value.title}`;
  }
};

// 监听video变化更新标题
watch(video, () => {
  updatePageTitle();
}, { immediate: true });

const embedUrl = computed(() => {
  if (!video.value) return '';
  return video.value.platform === 'youtube'
    ? `https://www.youtube.com/embed/${video.value.videoId}`
    : `https://player.bilibili.com/player.html?bvid=${video.value.videoId}`;
});

onMounted(() => {
  initTheme();
  updatePageTitle();

  window.addEventListener('scroll', handleScroll);
});

const handleScroll = () => {
  showBackToTop.value = window.scrollY > 300;
};

const goBack = () => {
  router.back();
};

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>

<template>
  <div class="min-h-screen bg-construct-paper selection:bg-construct-red selection:text-white">
    <!-- Back Button -->
    <button
      @click="goBack"
      class="fixed top-4 left-4 z-50 w-12 h-12 bg-construct-black text-white flex items-center justify-center hover:bg-construct-red transition-colors"
    >
      <ArrowLeft class="w-6 h-6" />
    </button>

    <!-- Main Content -->
    <div class="container mx-auto px-4 md:px-8 py-32">
      <div v-if="video">
        <Motion
          :initial="{ opacity: 0, y: 20 }"
          :animate="{ opacity: 1, y: 0 }"
        >
          <h1 class="font-display text-4xl md:text-6xl tracking-tighter mb-8">
            {{ video.title }}
          </h1>

          <div class="aspect-video bg-construct-black">
            <iframe
              :src="embedUrl"
              class="w-full h-full"
              :title="video.title"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            ></iframe>
          </div>

          <div class="mt-8 p-8 border-2 border-construct-black">
            <h4 class="font-display text-sm tracking-widest text-construct-red mb-4 uppercase">
              DESCRIPTION //
            </h4>
            <p class="text-sm opacity-80 leading-relaxed font-medium">
              {{ video.description }}
            </p>
            <p class="text-xs opacity-60 font-medium mt-4">
              {{ video.date }} // {{ video.platform.toUpperCase() }}
            </p>
          </div>
        </Motion>
      </div>

      <div v-else class="py-32 text-center">
        <p class="font-display text-2xl tracking-tighter">Video not found.</p>
      </div>
    </div>

    <!-- Back to Top Button -->
    <Transition name="fade">
      <button
        v-if="showBackToTop"
        @click="scrollToTop"
        class="fixed bottom-4 right-4 z-50 w-12 h-12 bg-construct-red text-white flex items-center justify-center hover:bg-construct-black transition-colors"
      >
        <ArrowUp class="w-6 h-6" />
      </button>
    </Transition>
  </div>
</template>

<style lang="scss" scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}

.fade {
  &-enter-active,
  &-leave-active {
    transition: opacity 0.3s ease;
  }

  &-enter-from,
  &-leave-to {
    opacity: 0;
  }
}
</style>