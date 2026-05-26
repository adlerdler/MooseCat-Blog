<script setup>
/**
 * Blog.vue - 博客列表页
 * 
 * 功能说明：
 * - 展示所有博客文章的列表，采用不对称网格布局
 * - 支持按分类筛选（Theory/Design/Technology/Culture）
 * - 实现分页功能，每页显示14篇文章
 * 
 * 页面特色：
 * - 卡片大小根据索引变化，形成视觉节奏感
 * - Hover 时卡片背景变色效果
 * - 装饰性编号背景增强品牌感
 */
import { ref, computed, h, onMounted, watch } from 'vue';
import { ArrowUpRight, Hash, BookOpen, Lightbulb, Users, Cog } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useTheme } from '../../composables/useTheme';
import { usePageSeo } from '../../composables/usePageSeo';
import { usePageSeoData } from '../../composables/usePageSeoData';
import { formatToEnglish } from '../../utils/dateUtils';
import { formatId } from '../../utils/typeConvert';
import { getCategoryNameById } from '../../utils/categoryUtils';
import { useAdSlot } from '../../composables/useAdSlot';

import SidebarMenu from '../../components/SidebarMenu.vue';
import Footer from '../../components/Footer.vue';
import AdSlot from '../../components/front/AdSlot.vue';

const props = defineProps({
  posts: { type: Object, default: () => ({ data: [] }) },
  categories: { type: Array, default: () => [] },
  authors: { type: Array, default: () => [] }
});

const { getSeoByPageKey } = usePageSeoData();
const pageSeo = getSeoByPageKey('blog') || { title: '', description: '', keywords: '' };

usePageSeo({
  title: pageSeo.title || '',
  description: pageSeo.description || '',
  keywords: pageSeo.keywords || '',
});

const { t } = useI18n();
const { initAccentTheme } = useTheme();
const activeFilter = ref('all');
const currentPage = ref(props.posts.current_page || 1);
const itemsPerPage = props.posts.per_page || 14;
const categoryNames = ['all', ...props.categories.map(c => c.name)];
const isFooterVisible = ref(true);

const getAuthorName = (authorId) => {
  const user = props.authors.find(u => u.id === authorId);
  return user ? (user.penName || user.name) : 'Unknown';
};
const filteredPosts = computed(() => {
  if (activeFilter.value === 'all') {
    return props.posts.data || [];
  }
  return (props.posts.data || []).filter(post => getCategoryNameById(props.categories, post.category_id) === activeFilter.value);
});
const totalPages = computed(() => props.posts.last_page || 1);
const paginatedPosts = computed(() => {
  return filteredPosts.value;
});
const handleFilterChange = (filter) => {
  activeFilter.value = filter;
  currentPage.value = 1;
};
const handlePageChange = (pageNum) => {
  currentPage.value = pageNum;
};
const getCatIcon = (cat) => {
  switch (cat) {
    case 'Theory':
      return h(BookOpen, { size: 16 });
    case 'Design':
      return h(Lightbulb, { size: 16 });
    case 'Technology':
      return h(Cog, { size: 16 });
    case 'Culture':
      return h(Users, { size: 16 });
    default:
      return h(Hash, { size: 16 });
  }
};
const getSpanClass = (idx) => {
  const mod = idx % 7;
  switch (mod) {
    case 0:
      return 'md:col-span-12 xl:col-span-8';
    case 1:
      return 'md:col-span-12 xl:col-span-4';
    case 2:
      return 'md:col-span-6 xl:col-span-4';
    case 3:
      return 'md:col-span-6 xl:col-span-4';
    case 4:
      return 'md:col-span-12 xl:col-span-4';
    case 5:
      return 'md:col-span-12 xl:col-span-6';
    case 6:
      return 'md:col-span-12 xl:col-span-6';
    default:
      return 'md:col-span-12 xl:col-span-4';
  }
};

const { getActiveAds } = useAdSlot();
const AD_INTERVAL = 4;

const mixedPostsWithAds = computed(() => {
  const result = [];
  let adIndex = 0;
  const ads = getActiveAds('between_posts');

  paginatedPosts.value.forEach((post, index) => {
    result.push({ type: 'content', data: post, originalIndex: index });

    if ((index + 1) % AD_INTERVAL === 0 && index < paginatedPosts.value.length - 1 && ads[adIndex]) {
      result.push({ type: 'ad', data: ads[adIndex], adIndex: adIndex });
      adIndex++;
    }
  });

  return result;
});

const getSpanClassForMixed = (idx, originalIdx) => {
  const mod = originalIdx % 7;
  switch (mod) {
    case 0:
      return 'md:col-span-12 xl:col-span-8';
    case 1:
      return 'md:col-span-12 xl:col-span-4';
    case 2:
      return 'md:col-span-6 xl:col-span-4';
    case 3:
      return 'md:col-span-6 xl:col-span-4';
    case 4:
      return 'md:col-span-12 xl:col-span-4';
    case 5:
      return 'md:col-span-12 xl:col-span-6';
    case 6:
      return 'md:col-span-12 xl:col-span-6';
    default:
      return 'md:col-span-12 xl:col-span-4';
  }
};

const getAdSpanClass = () => {
  return 'md:col-span-12 xl:col-span-4';
};

onMounted(() => {
  initAccentTheme();

  // 前台页面不受后台主题设置影响，移除 light class
  document.documentElement.classList.remove('light');

  // 从 sessionStorage 读取页脚可见性状态
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
  <div class="min-h-screen bg-construct-paper">
    <!-- Sidebar Menu -->
    <SidebarMenu v-model:is-footer-visible="isFooterVisible" />
    <!-- Main Content -->
    <div class="ml-16">
      <!-- Avant-garde Header -->
      <header class="relative w-full overflow-hidden bg-construct-black text-white pt-32 pb-24 border-b-8 border-construct-red">
        <div class="absolute top-0 right-0 w-[50vw] h-[150vh] bg-white/5 -skew-x-12 translate-x-[20vw] z-0"></div>
        <div class="container mx-auto px-6 md:px-12 relative z-10">
          <div class="max-w-4xl">
            <h1 class="font-display text-7xl md:text-[8rem] lg:text-[10rem] tracking-tighter leading-[0.8] mb-8 uppercase">
              {{ t('blog_title') || 'THE BLOG' }}
            </h1>
            <div class="flex items-center gap-4 md:gap-6">
              <div class="w-16 md:w-32 h-2 bg-construct-red shrink-0"></div>
              <p class="text-xs md:text-sm font-bold tracking-[0.3em] uppercase max-w-xl opacity-80">
                {{ t('blog_subtitle') || 'ARCHITECTURAL RESEARCH AND DESIGN METHODOLOGIES' }}
              </p>
            </div>
          </div>
        </div>
      </header>

      <!-- Header Banner Ad -->
      <section class="bg-construct-black">
        <AdSlot position="header" />
      </section>
      <!-- Radical Filter Strip -->
      <div class="sticky top-0 z-50 bg-construct-paper/90 backdrop-blur-md border-b-4 border-construct-black overflow-hidden">
        <div class="container mx-auto px-0">
          <div class="flex overflow-x-auto items-center">
            <div class="px-6 py-4 bg-construct-black text-white font-bold text-xs tracking-widest flex items-center shrink-0 h-full uppercase gap-2 hidden md:flex">
              <Hash size="14" /> {{ t('filter_label') }}
            </div>
            <div class="flex flex-1 px-4 md:px-6 gap-2 py-2 md:py-0">
              <button
                v-for="filter in categoryNames"
                :key="filter"
                @click="handleFilterChange(filter)"
                class="px-4 md:px-6 py-3 text-[10px] md:text-xs font-bold tracking-[0.2em] transition-all whitespace-nowrap uppercase"
                :class="activeFilter === filter ? 'bg-construct-red text-white' : 'text-construct-black hover:bg-black/5'"
              >
                {{ filter === 'all' ? t('filter_all') : filter }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Masonry-like Asymmetric Grid Content -->
      <main class="container mx-auto px-4 md:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-8 auto-rows-min">
          <template v-for="(item, idx) in mixedPostsWithAds" :key="item.type === 'ad' ? `ad-${item.adIndex}` : item.data.id">
            <!-- Ad Card -->
            <div
              v-if="item.type === 'ad'"
              :class="[
                'group flex flex-col justify-between border-4 border-construct-black relative overflow-hidden transition-colors duration-500 bg-construct-black hover:bg-construct-red hover:border-construct-red',
                'p-6 md:p-10',
                getAdSpanClass()
              ]"
            >
              <a :href="item.data.link_url" target="_blank" rel="noopener noreferrer" class="absolute inset-0 z-20"></a>
              <div class="relative z-10 flex flex-col justify-between h-full">
                <div>
                  <span class="text-[10px] font-black tracking-widest text-construct-red uppercase mb-4 block">
                    SPONSORED
                  </span>
                  <h3 class="font-display text-3xl md:text-4xl tracking-tight text-white group-hover:text-white leading-tight">
                    {{ item.data.title }}
                  </h3>
                </div>
                <div class="flex items-center justify-between mt-8 pt-6 border-t border-white/20">
                  <span class="text-[10px] font-bold tracking-widest text-white/60 uppercase">
                    Learn More
                  </span>
                  <ArrowUpRight class="w-5 h-5 text-white/60 group-hover:text-white transition-all group-hover:translate-x-1 group-hover:-translate-y-1" />
                </div>
              </div>
            </div>

            <!-- Content Card -->
            <div
              v-else
              class="group flex flex-col justify-between border-4 border-construct-black relative overflow-hidden transition-colors duration-500 bg-white"
              :class="[
                item.data.color === 'red' ? 'hover:bg-construct-red hover:border-construct-red' : 'hover:bg-construct-black',
                item.originalIndex % 7 === 0 ? 'p-8 md:p-16 lg:p-20' : 'p-6 md:p-10',
                getSpanClassForMixed(idx, item.originalIndex)
              ]"
            >
              <Link :href="`/blog/${item.data.id}`" class="absolute inset-0 z-20" :aria-label="`Read ${item.data.title}`"></Link>

              <!-- Decorative ID Background -->
              <div
                class="absolute -bottom-8 -right-8 font-display font-black leading-none pointer-events-none transition-all duration-700 opacity-5 group-hover:text-white group-hover:opacity-10 group-hover:scale-110 group-hover:-rotate-6"
                :class="item.originalIndex % 7 === 0 ? 'text-[150px] md:text-[250px]' : 'text-[100px] md:text-[150px]'"
              >
                {{ formatId(item.data.id, 2) }}
              </div>

              <!-- Top Bar: Category & Date -->
              <div class="flex justify-between items-start mb-8 md:mb-12 relative z-10" :class="item.data.color === 'red' ? 'group-hover:text-white' : 'group-hover:text-white'">
                <div class="flex items-center gap-3">
                  <span class="p-2 transition-colors duration-500" :class="item.data.color === 'red' ? 'bg-construct-red text-white group-hover:bg-white group-hover:text-construct-red' : 'bg-construct-black text-white group-hover:bg-white group-hover:text-construct-black'">
                    <component :is="getCatIcon(getCategoryNameById(props.categories, item.data.category_id))" />
                  </span>
                  <span class="text-[10px] md:text-xs font-bold tracking-[0.3em] uppercase group-hover:text-white">
                    {{ getCategoryNameById(props.categories, item.data.category_id) }}
                  </span>
                </div>
                <span class="text-[10px] font-bold tracking-widest opacity-60 group-hover:text-white">
                  {{ formatToEnglish(item.data.published_at) }}
                </span>
              </div>

              <!-- Middle: Title & Excerpt -->
              <div class="relative z-10 flex-1 flex flex-col justify-center mb-8 md:mb-12">
                <h3
                  class="font-display leading-[0.9] tracking-tighter mb-4 md:mb-6 transition-all duration-500 group-hover:text-white group-hover:-translate-y-2"
                  :class="item.originalIndex % 7 === 0 ? 'text-5xl md:text-7xl lg:text-8xl' : 'text-4xl md:text-5xl'"
                >
                  {{ item.data.title }}
                </h3>
                <p
                  class="font-medium leading-relaxed transition-all duration-500 group-hover:text-white/80 group-hover:translate-x-2"
                  :class="item.originalIndex % 7 === 0 ? 'text-base md:text-xl max-w-2xl opacity-80' : 'text-sm opacity-70'"
                >
                  {{ item.data.excerpt }}
                </p>
              </div>

              <!-- Bottom: Tags & Author -->
              <div class="relative z-10 pt-6 md:pt-8 border-t-2 border-construct-black/10 group-hover:border-white/20 transition-colors duration-500 flex flex-col xl:flex-row justify-between items-start xl:items-end gap-4 md:gap-6">
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="tag in item.data.tags"
                    :key="tag"
                    class="text-[8px] md:text-[9px] font-black tracking-widest px-3 py-1 border border-current uppercase group-hover:text-white"
                  >
                    #{{ tag }}
                  </span>
                </div>
                <div class="flex items-center gap-4 text-[10px] md:text-xs font-bold tracking-widest uppercase shrink-0">
                  <span class="opacity-60 group-hover:opacity-100 group-hover:text-white transition-opacity">
                    AUTHOR: {{ getAuthorName(item.data.author_id) }}
                  </span>
                  <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center transition-all duration-500 group-hover:rotate-45 shrink-0" :class="item.data.color === 'red' ? 'border-construct-black group-hover:border-white' : 'border-construct-black group-hover:border-white'">
                    <ArrowUpRight size="16" class="group-hover:text-white" />
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="mt-24 border-t-8 border-construct-black pt-16 flex flex-col items-center">
          <span class="text-[10px] font-bold tracking-[0.5em] uppercase opacity-40 mb-8 text-center bg-construct-paper px-4 -mt-20">
            SYSTEM LOG PAGINATION
          </span>
          <div class="flex flex-wrap justify-center gap-4">
            <button
              v-for="pageNum in totalPages"
              :key="pageNum"
              @click="handlePageChange(pageNum)"
              class="w-12 h-12 md:w-16 md:h-16 font-display text-xl md:text-2xl border-4 transition-all hover:-translate-y-2"
              :class="currentPage === pageNum ? 'bg-construct-black text-white border-construct-black' : 'bg-white text-construct-black border-construct-black hover:shadow-[0_8px_0_0_#1a1919]'"
            >
              {{ pageNum.toString().padStart(2, '0') }}
            </button>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredPosts.length === 0" class="py-32 text-center border-4 border-dashed border-construct-black/20 mt-12 bg-white">
          <p class="font-display text-xl md:text-4xl opacity-40 uppercase tracking-widest">
            {{ t('no_artifacts') }}
          </p>
        </div>
      </main>

      <!-- Footer -->
      <Footer v-model="isFooterVisible" />
    </div>
  </div>
</template>

<style lang="scss" scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}

::-webkit-scrollbar {
  display: none;
}

.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
