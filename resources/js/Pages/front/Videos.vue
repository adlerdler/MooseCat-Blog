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
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { Motion, AnimatePresence } from 'motion-v';
import { useTheme } from '../../composables/useTheme';
import { usePageSeo } from '../../composables/usePageSeo';
import { usePageSeoData } from '../../composables/usePageSeoData';
import { VIDEOS } from '../../data/videos';
import { useI18n } from 'vue-i18n';

const { getSeoByRoute } = usePageSeoData();
const pageSeo = getSeoByRoute('videos')

usePageSeo({
  title: pageSeo.title,
  description: pageSeo.description,
  keywords: pageSeo.keywords,
  url: `${window.location.origin}${pageSeo.path}`,
  type: pageSeo.schemaType
})

const { t } = useI18n();
const { initAccentTheme } = useTheme();
const isFooterVisible = ref(true);

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
          <Motion
            v-for="(video, idx) in VIDEOS"
            :key="video.id"
            :initial="{ opacity: 0, y: 20 }"
            :animate="{ opacity: 1, y: 0 }"
            :transition="{ delay: idx * 0.1 }"
            class="group block border-4 border-construct-black hover:border-construct-red transition-colors"
          >
            <RouterLink :to="`/videos/${video.id}`">
              <img
                :src="video.thumbnail"
                :alt="video.title"
                class="w-full h-48 object-cover"
              />
              <div class="p-6">
                <h3 class="font-display text-xl tracking-tighter mb-2 group-hover:text-construct-red transition-colors">
                  {{ video.title }}
                </h3>
                <p class="text-xs opacity-60 font-medium">
                  {{ video.published_at }} // {{ video.platform.toUpperCase() }}
                </p>
              </div>
            </RouterLink>
          </Motion>
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