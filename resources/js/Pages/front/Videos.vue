<script setup>
/**
 * Videos.vue - 视频列表页
 * 
 * 功能说明：
 * - 展示所有视频内容的列表
 * - 支持按平台筛选（YouTube/Bilibili）
 * - 视频卡片展示缩略图和基本信息
 * 
 * 页面特色：
 * - 响应式网格布局
 * - Hover 动画效果
 * - 点击跳转到视频详情页
 */
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Motion, AnimatePresence } from 'motion-v';
import { useTheme } from '../../composables/useTheme';
import { usePageSeo } from '../../composables/usePageSeo';
import { usePageSeoData } from '../../composables/usePageSeoData';
import { useI18n } from 'vue-i18n';
import { useAdSlot } from '../../composables/useAdSlot';
import SidebarMenu from '@/components/SidebarMenu.vue';
import Footer from '@/components/Footer.vue';
import AdSlot from '@/components/front/AdSlot.vue';

const props = defineProps({
  videos: { type: Array, default: () => [] }
});

const { getSeoByPageKey } = usePageSeoData();
const pageSeo = getSeoByPageKey('videos');

usePageSeo({
  title: pageSeo.title,
  description: pageSeo.description,
  keywords: pageSeo.keywords,
});

const { t } = useI18n();
const { initAccentTheme } = useTheme();
const isFooterVisible = ref(true);

const { getActiveAds } = useAdSlot();
const AD_INTERVAL = 3;

const mixedVideosWithAds = computed(() => {
  const result = [];
  let adIndex = 0;
  const ads = getActiveAds('between_posts');

  props.videos.forEach((video, index) => {
    result.push({ type: 'content', data: video, originalIndex: index });

    if ((index + 1) % AD_INTERVAL === 0 && index < props.videos.length - 1 && ads[adIndex]) {
      result.push({ type: 'ad', data: ads[adIndex], adIndex: adIndex });
      adIndex++;
    }
  });

  return result;
});

onMounted(() => {
  initAccentTheme();
  
  // 前台页面不受后台主题设置影响，移除 light class
  document.documentElement.classList.remove('light');
  
  const saved = sessionStorage.getItem('footer_visible');
  if (saved !== null) {
    isFooterVisible.value = saved === 'true';
  }
});
</script>

<template>
  <div class="min-h-screen bg-construct-paper selection:bg-construct-red selection:text-white">
    <!-- Sidebar Menu -->
    <SidebarMenu v-model:is-footer-visible="isFooterVisible" />

    <!-- Main Content with left margin for sidebar -->
    <div class="ml-16">
     
 <!-- Header Banner Ad -->
      <section class="bg-construct-black">
        <AdSlot position="header" />
      </section>
      <div class="container mx-auto px-4 md:px-8 py-16">
        <header class="mb-16">
          <h1 class="font-display text-6xl md:text-6xl lg:text-8xl tracking-tighter leading-none mb-6">
            {{ t('videos_title') }}
          </h1>
          <p class="text-sm font-medium opacity-60 uppercase tracking-widest">
            {{ t('videos_subtitle') }}
          </p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <template v-for="item in mixedVideosWithAds" :key="item.type === 'ad' ? `ad-${item.adIndex}` : item.data.id">
            <!-- Ad Card -->
            <Motion
              v-if="item.type === 'ad'"
              :initial="{ opacity: 0, y: 20 }"
              :animate="{ opacity: 1, y: 0 }"
              class="group block border-4 border-construct-black hover:border-construct-red transition-colors"
            >
              <a :href="item.data.link_url" target="_blank" rel="noopener noreferrer">
                <div class="aspect-video bg-construct-black relative overflow-hidden">
                  <img
                    :src="item.data.image_url"
                    :alt="item.data.title"
                    class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                  <div class="absolute bottom-4 left-4 right-4">
                    <span class="text-[10px] font-black tracking-widest text-construct-red uppercase">SPONSORED</span>
                    <h3 class="font-display text-lg tracking-tight text-white mt-2 leading-tight">
                      {{ item.data.title }}
                    </h3>
                    <span class="text-[10px] font-bold tracking-widest text-white/60 uppercase mt-2 block">
                      Learn More →
                    </span>
                  </div>
                </div>
              </a>
            </Motion>

            <!-- Content Card -->
            <Motion
              v-else
              :initial="{ opacity: 0, y: 20 }"
              :animate="{ opacity: 1, y: 0 }"
              :transition="{ delay: item.originalIndex * 0.1 }"
              class="group block border-4 border-construct-black hover:border-construct-red transition-colors"
            >
              <Link :href="`/videos/${item.data.id}`">
                <img
                  :src="item.data.thumbnail"
                  :alt="item.data.title"
                  class="w-full h-48 object-cover"
                />
                <div class="p-6">
                  <h3 class="font-display text-xl tracking-tighter mb-2 group-hover:text-construct-red transition-colors">
                    {{ item.data.title }}
                  </h3>
                  <p class="text-xs opacity-60 font-medium">
                    {{ item.data.published_at }} // {{ item.data.platform.toUpperCase() }}
                  </p>
                </div>
              </Link>
            </Motion>
          </template>
        </div>
      </div>

      <!-- Footer -->
      <Footer v-model="isFooterVisible" />
    </div><!-- End of ml-16 wrapper -->
  </div>
</template>

<style lang="scss" scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}
</style>