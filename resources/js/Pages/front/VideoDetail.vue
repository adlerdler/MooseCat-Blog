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
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Motion, AnimatePresence } from 'motion-v';
import { useTheme } from '../../composables/useTheme';
import { useAdSlot } from '../../composables/useAdSlot';
import AdSlot from '../../components/front/AdSlot.vue';
import { ArrowLeft, ArrowUp } from 'lucide-vue-next';

const props = defineProps({
  video: { type: Object, default: null }
});

const { initAccentTheme } = useTheme();
const pageProps = usePage().props;
const { hasActiveAd } = useAdSlot({ ads: pageProps.frontAds ?? [], adPositions: pageProps.frontAdPositions ?? [] });
const showBackToTop = ref(false);

const embedUrl = computed(() => {
  if (!props.video) return '';
  if (props.video.platform === 'youtube') {
    const origin = encodeURIComponent(window.location.origin);
    return `https://www.youtube.com/embed/${props.video.video_id}`;
  }
  if (props.video.platform === 'bilibili') {
    return `https://player.bilibili.com/player.html?bvid=${props.video.video_id}`;
  }
  // local 平台或无 video_id 时，使用完整视频 URL
  return props.video.video_url || '';
});

onMounted(() => {
  initAccentTheme();
  // 前台页面不受后台主题设置影响，移除 light class
  document.documentElement.classList.remove('light');
  window.addEventListener('scroll', handleScroll);
});

const handleScroll = () => {
  showBackToTop.value = window.scrollY > 300;
};

const goBack = () => {
  window.history.back();
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

          <!-- 视频下方广告位 -->
          <div class="mt-8">
            <AdSlot position="video_bottom" />
          </div>

          <div class="mt-8 p-8 border-2 border-construct-black">
            <h4 class="font-display text-sm tracking-widest text-construct-red mb-4 uppercase">
              DESCRIPTION //
            </h4>
            <p class="text-sm opacity-80 leading-relaxed font-medium">
              {{ video.description }}
            </p>
            <p class="text-xs opacity-60 font-medium mt-4">
              {{ video.published_at }} // {{ video.platform === 'youtube' ? 'YOUTUBE' : video.platform === 'bilibili' ? 'BILIBILI' : '本站' }}
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