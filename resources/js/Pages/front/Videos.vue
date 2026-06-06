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
import { ref, computed, onMounted, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import { Play } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { usePageSeo } from '../../composables/usePageSeo';
import { usePageSeoData } from '../../composables/usePageSeoData';
import { useI18n } from 'vue-i18n';
import { useAdSlot } from '../../composables/useAdSlot';
import SidebarMenu from '@/components/SidebarMenu.vue';
import Footer from '@/components/Footer.vue';
import AdSlot from '@/components/front/AdSlot.vue';
import LazyImage from '@/components/LazyImage.vue';

const props = defineProps({
  videos: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
  menus: { type: Array, default: () => [] },
  siteConfig: { type: Object, default: () => ({}) },
  footerConfig: { type: Object, default: () => ({}) },
  themes: { type: Array, default: () => [] }
});

const { getSeoByPageKey } = usePageSeoData();
const pageSeo = getSeoByPageKey('videos') || { title: '', description: '', keywords: '' };

const { SeoHead } = usePageSeo({
  title: pageSeo.title || '',
  description: pageSeo.description || '',
  keywords: pageSeo.keywords || '',
});

const { t } = useI18n();
const { initAccentTheme } = useTheme();
const isFooterVisible = ref(true);
const activeFilter = ref('all');

const hasCategories = computed(() => props.categories.length > 0);
const categoryNames = computed(() => {
  if (!hasCategories.value) return [];
  const usedCategoryIds = new Set(props.videos.map(v => v.category_id));
  return ['all', ...props.categories.filter(c => usedCategoryIds.has(c.id)).map(c => c.name)];
});

const filteredVideos = computed(() => {
  if (activeFilter.value === 'all') return props.videos;
  const selectedCategoryData = props.categories.find(c => c.name === activeFilter.value);
  if (!selectedCategoryData) return props.videos;
  return props.videos.filter(v => v.category_id === selectedCategoryData.id);
});

const pageProps = usePage().props;
const { getActiveAds } = useAdSlot({ ads: pageProps.frontAds ?? [], adPositions: pageProps.frontAdPositions ?? [] });
const AD_INTERVAL = 3;

const mixedVideosWithAds = computed(() => {
  const result = [];
  let adIndex = 0;
  const ads = getActiveAds('between_posts');

  filteredVideos.value.forEach((video, index) => {
    result.push({ type: 'content', data: video, originalIndex: index });

    if ((index + 1) % AD_INTERVAL === 0 && index < filteredVideos.value.length - 1 && ads[adIndex]) {
      result.push({ type: 'ad', data: ads[adIndex], adIndex: adIndex });
      adIndex++;
    }
  });

  return result;
});

const handleFilterChange = (filter) => {
  activeFilter.value = filter;
};

onMounted(() => {
  initAccentTheme();
  
  // 前台页面不受后台主题设置影响，移除 light class
  document.documentElement.classList.remove('light');
  
  const saved = sessionStorage.getItem('footer_visible');
  if (saved !== null) {
    isFooterVisible.value = saved === 'true';
  }
});

watch(isFooterVisible, (newVal) => {
  sessionStorage.setItem('footer_visible', String(newVal));
});
</script>

<template>
  <SeoHead />
  <div class="min-h-screen bg-construct-paper selection:bg-construct-red selection:text-white">
    <!-- Sidebar Menu -->
    <SidebarMenu 
      v-model:is-footer-visible="isFooterVisible" 
      :menus="menus"
      :site-config="siteConfig"
      :footer-config="footerConfig"
      :themes="themes"
    />

    <!-- Main Content with left margin for sidebar -->
    <div class="md:ml-16 pt-16 md:pt-0">
     
 <!-- Header Banner Ad -->
      <section class="bg-construct-black">
        <AdSlot position="header" />
      </section>
      <div class="container mx-auto px-4 md:px-8 py-16">
        <header class="mb-16">
          <h1 class="font-display text-6xl md:text-6xl lg:text-8xl tracking-tighter leading-none mb-6">
            {{ t('videos_title') }}
          </h1>
          <p class="text-sm font-medium opacity-60 uppercase tracking-widest mb-12 max-w-xl">
            {{ t('videos_subtitle') }}
          </p>

          <!-- Categories -->
          <div v-if="hasCategories" class="flex flex-wrap gap-4 mb-4">
            <button
              v-for="filter in categoryNames"
              :key="filter"
              @click="handleFilterChange(filter)"
              :class="[
                'px-4 py-2 border-2 text-xs font-bold tracking-widest uppercase transition-all',
                activeFilter === filter
                  ? 'border-construct-red bg-construct-red text-white'
                  : 'border-construct-black/20 text-construct-black hover:border-construct-black hover:bg-construct-black hover:text-white'
              ]"
            >
              {{ filter === 'all' ? t('filter_all') : filter }}
            </button>
          </div>
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
                  <LazyImage
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
            >
              <Link
                :href="`/videos/${item.data.slug || item.data.id}`"
                class="group block border-4 border-construct-black hover:border-construct-red transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
              >
                <!-- Thumbnail -->
                <div class="relative aspect-video bg-construct-black overflow-hidden">
                  <LazyImage
                    v-if="item.data.thumbnail"
                    :src="item.data.thumbnail"
                    :alt="item.data.title"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center">
                    <Play class="w-16 h-16 text-white/20" />
                  </div>

                  <!-- Hover Play Overlay -->
                  <div class="absolute inset-0 bg-construct-red/0 group-hover:bg-construct-red/60 transition-all duration-300 flex items-center justify-center">
                    <div class="w-16 h-16 rounded-full border-2 border-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:scale-100 scale-75">
                      <Play class="w-6 h-6 text-white ml-1" />
                    </div>
                  </div>

                  <!-- Platform Badge -->
                  <span
                    v-if="item.data.platform"
                    class="absolute top-3 left-3 px-3 py-1 text-[10px] font-black uppercase tracking-widest bg-construct-black text-white"
                  >
                    {{ item.data.platform === 'local' ? '本站' : item.data.platform }}
                  </span>

                  <!-- Duration Badge -->
                  <span
                    v-if="item.data.duration"
                    class="absolute bottom-3 right-3 px-2 py-0.5 text-xs font-bold bg-black/80 text-white"
                  >
                    {{ item.data.duration >= 3600
                      ? `${Math.floor(item.data.duration / 3600)}:${String(Math.floor((item.data.duration % 3600) / 60)).padStart(2, '0')}:${String(item.data.duration % 60).padStart(2, '0')}`
                      : `${Math.floor(item.data.duration / 60)}:${String(item.data.duration % 60).padStart(2, '0')}` }}
                  </span>
                </div>

                <!-- Info -->
                <div class="p-5 md:p-6">
                  <h3
                    class="font-display text-lg md:text-xl tracking-tighter leading-tight mb-3 group-hover:text-construct-red transition-colors"
                    style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; overflow: hidden;"
                  >
                    {{ item.data.title }}
                  </h3>

                  <p
                    class="text-xs opacity-50 font-medium leading-relaxed mb-4 min-h-[2.5rem]"
                    style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; overflow: hidden;"
                  >
                    {{ item.data.description || '' }}
                  </p>

                  <!-- Meta Footer -->
                  <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest opacity-40">
                    <span>{{ item.data.created_at || '' }}</span>
                    <span class="flex items-center gap-1">
                      {{ item.data.platform === 'youtube' ? 'YouTube' : item.data.platform === 'bilibili' ? 'Bilibili' : item.data.platform === 'local' ? '本站' : '' }}
                    </span>
                  </div>
                </div>
              </Link>
            </Motion>
          </template>
        </div>
      </div>

      <!-- Footer -->
      <Footer 
        v-model="isFooterVisible" 
        :footer-config="footerConfig"
        :site-config="siteConfig"
      />
    </div><!-- End of ml-16 wrapper -->
  </div>
</template>

<style lang="scss" scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}
</style>