<script setup>
/**
 * Projects.vue - 项目列表页
 * 
 * 功能说明：
 * - 展示所有已完成项目的列表
 * - 卡片形式展示项目信息
 * - 点击进入项目详情页
 * 
 * 页面特色：
 * - 不对称网格布局
 * - 渐变背景效果
 * - 项目技术栈标签展示
 */
import { ref, computed, onMounted, watch } from 'vue';
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
  projects: { type: Array, default: () => [] },
  menus: { type: Array, default: () => [] },
  siteConfig: { type: Object, default: () => ({}) },
  footerConfig: { type: Object, default: () => ({}) },
  themes: { type: Array, default: () => [] }
});

const { getSeoByPageKey } = usePageSeoData();
const pageSeo = getSeoByPageKey('projects') || { title: '', description: '', keywords: '' };

const { SeoHead } = usePageSeo({
  title: pageSeo.title || '',
  description: pageSeo.description || '',
  keywords: pageSeo.keywords || '',
});

const { t } = useI18n();
const { initAccentTheme } = useTheme();
const isFooterVisible = ref(true);
const completedProjects = props.projects.filter(p => p.status === 'completed');

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

const { getActiveAds } = useAdSlot();
const AD_INTERVAL = 3;

const mixedProjectsWithAds = computed(() => {
  const result = [];
  let adIndex = 0;
  const ads = getActiveAds('between_posts');

  completedProjects.forEach((project, index) => {
    result.push({ type: 'content', data: project, originalIndex: index });

    if ((index + 1) % AD_INTERVAL === 0 && index < completedProjects.length - 1 && ads[adIndex]) {
      result.push({ type: 'ad', data: ads[adIndex], adIndex: adIndex });
      adIndex++;
    }
  });

  return result;
});

const getSpanClass = (idx) => {
  const mod = idx % 5;
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
    default:
      return 'md:col-span-12 xl:col-span-4';
  }
};

const getAdSpanClass = () => {
  return 'md:col-span-12 xl:col-span-4';
};
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
    <div class="ml-16">
      
      <!-- Avant-garde Header -->
      <header class="relative w-full overflow-hidden bg-construct-black text-white pt-32 pb-24 border-b-8 border-construct-red">
        <div class="absolute top-0 right-0 w-[50vw] h-[150vh] bg-white/5 -skew-x-12 translate-x-[20vw] z-0"></div>
        <div class="container mx-auto px-6 md:px-12 relative z-10">
          <div class="max-w-4xl">
            <h1 class="font-display text-7xl md:text-[8rem] lg:text-[10rem] tracking-tighter leading-[0.8] mb-8 uppercase">
              {{ t('projects_title') }}
            </h1>
            <div class="flex items-center gap-4 md:gap-6">
              <div class="w-16 md:w-32 h-2 bg-construct-red shrink-0"></div>
              <p class="text-xs md:text-sm font-bold tracking-[0.3em] uppercase max-w-xl opacity-80">
                {{ t('projects_subtitle') }}
              </p>
            </div>
          </div>
        </div>
      </header>

      <!-- Header Banner Ad -->
      <section class="bg-construct-black">
        <AdSlot position="header" />
      </section>

      <div class="container mx-auto px-4 md:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8">
          <template v-for="(item, idx) in mixedProjectsWithAds" :key="item.type === 'ad' ? `ad-${item.adIndex}` : item.data.id">
            <!-- Ad Card -->
            <Motion
              v-if="item.type === 'ad'"
              :initial="{ opacity: 0, y: 20 }"
              :animate="{ opacity: 1, y: 0 }"
              :class="['group cursor-pointer border-4 border-construct-black bg-construct-black transition-all duration-500 h-full flex flex-col hover:border-construct-red hover:shadow-[-12px_12px_0px_theme(colors.construct-red)] hover:-translate-y-1 hover:translate-x-1', getAdSpanClass()]"
            >
              <a :href="item.data.link_url" target="_blank" rel="noopener noreferrer" class="block relative h-full flex flex-col flex-1">
                <div class="aspect-video relative overflow-hidden">
                  <img
                    :src="item.data.image_url"
                    :alt="item.data.title"
                    class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                </div>
                <div class="p-5 flex flex-col justify-between flex-1 mt-auto">
                  <div>
                    <span class="text-[10px] font-black tracking-widest text-construct-red uppercase">SPONSORED</span>
                    <h3 class="font-display text-2xl md:text-3xl tracking-tighter text-white group-hover:text-white mt-2 leading-tight">
                      {{ item.data.title }}
                    </h3>
                  </div>
                  <div class="flex items-center justify-between mt-6 pt-4 border-t border-white/20">
                    <span class="text-[10px] font-bold tracking-widest text-white/60 uppercase">Learn More</span>
                    <span class="text-construct-red group-hover:text-white transition-colors transform group-hover:translate-x-0.5">→</span>
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
              :class="[
                'group cursor-pointer border-4 border-construct-black overflow-hidden transition-all duration-500 h-full flex flex-col',
                item.originalIndex === 1 
                  ? 'project-card-featured bg-construct-black text-white' 
                  : 'project-card-normal bg-white text-construct-black hover:bg-construct-red hover:text-white',
                getSpanClass(item.originalIndex)
              ]"
            >
              <Link :href="`/projects/${item.data.id}`" class="relative w-full h-full flex flex-col">
                <!-- Special layout for second card (idx 1) -->
                <div v-if="item.originalIndex === 1" class="relative overflow-hidden w-full h-full">
                  <!-- Background Image with Grayscale & Scale Transitions -->
                  <img
                    :src="item.data.image"
                    :alt="item.data.title"
                    class="absolute inset-0 w-full h-full object-cover grayscale mix-blend-luminosity opacity-40 group-hover:grayscale-0 group-hover:mix-blend-normal group-hover:opacity-60 group-hover:scale-110 transition-all duration-700 ease-out"
                  />
                  
                  <!-- Base Dark Gradient for readability in normal state -->
                  <div class="absolute inset-0 bg-gradient-to-t from-construct-black via-construct-black/80 to-transparent transition-opacity duration-700"></div>
                  
                  <!-- Constructivist Red Glow Overlay on Hover (duotone red-and-black blend) -->
                  <div class="absolute inset-0 bg-construct-red opacity-0 group-hover:opacity-85 mix-blend-multiply transition-opacity duration-700"></div>
                  <div class="absolute inset-0 bg-gradient-to-t from-construct-black via-transparent to-transparent opacity-0 group-hover:opacity-60 transition-opacity duration-700"></div>
                  
                  <!-- 内容容器 -->
                  <div class="relative p-6 md:p-8 h-full min-h-[350px] flex flex-col justify-between z-10">
                    <!-- 顶部装饰区域 -->
                    <div class="flex justify-between items-start">
                      <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-construct-red group-hover:bg-white transition-colors duration-300"></div>
                        <span class="text-[9px] font-bold tracking-[0.3em] text-white/60 group-hover:text-white uppercase transition-colors duration-300">FEATURED</span>
                      </div>
                      <span class="font-display text-5xl md:text-6xl text-white/10 group-hover:text-white/30 leading-none transition-all duration-500 transform group-hover:scale-110">
                        {{ String(item.originalIndex + 1).padStart(2, '0') }}
                      </span>
                    </div>
                    
                    <!-- 下半部分内容（包含中间和底部，形成自然的紧凑感，消除大面积留白） -->
                    <div class="flex flex-col gap-6 mt-auto pt-6">
                      <div>
                        <span class="text-[10px] font-black tracking-[0.3em] text-construct-red group-hover:text-white uppercase mb-2 block transition-colors duration-300">
                          {{ item.data.year }} / {{ item.data.role }}
                        </span>
                        <h3 class="font-display text-2xl md:text-3xl tracking-tighter text-white leading-tight mb-3 group-hover:translate-x-1 transition-transform duration-500">
                          {{ item.data.title }}
                        </h3>
                        <p class="text-xs md:text-sm text-white/70 group-hover:text-white/90 mb-4 transition-colors duration-300 leading-relaxed line-clamp-3">
                          {{ item.data.description }}
                        </p>
                        
                        <div class="flex flex-wrap gap-1.5">
                          <span
                            v-for="tech in item.data.technologies"
                            :key="tech"
                            class="text-[9px] font-black tracking-[0.15em] px-2.5 py-1 bg-white/10 text-white border border-white/20 group-hover:bg-white group-hover:text-construct-black group-hover:border-white transition-all duration-300"
                          >
                            {{ tech }}
                          </span>
                        </div>
                      </div>
                      
                      <!-- 底部操作区域 -->
                      <div class="flex items-center justify-between pt-4 border-t border-white/10">
                        <div class="flex items-center gap-3">
                          <div class="w-8 h-8 border-2 border-white flex items-center justify-center shrink-0 group-hover:bg-white group-hover:border-white transition-all duration-300">
                            <span class="text-white text-sm group-hover:text-construct-black transition-colors duration-300 transform group-hover:translate-x-0.5">→</span>
                          </div>
                          <span class="text-[9px] font-bold tracking-[0.2em] text-white uppercase">VIEW PROJECT</span>
                        </div>
                        <div class="w-12 h-[1px] bg-white/20 group-hover:w-20 group-hover:bg-white/40 transition-all duration-500"></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Normal layout for other cards -->
                <div v-else class="p-5 flex flex-col h-full flex-1">
                  <div class="aspect-video overflow-hidden mb-4 border-2 border-construct-black/10 group-hover:border-white/15 transition-colors">
                    <img
                      :src="item.data.image"
                      :alt="item.data.title"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                  </div>
                  <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                      <span class="text-[10px] font-black tracking-widest text-construct-red group-hover:text-white uppercase mb-1.5 block transition-colors duration-300">
                        {{ item.data.year }} / {{ item.data.role }}
                      </span>
                      <h3 class="font-display text-2xl md:text-3xl tracking-tighter group-hover:text-white leading-tight">
                        {{ item.data.title }}
                      </h3>
                    </div>
                    <div class="w-8 h-8 border-2 border-construct-black group-hover:border-white flex items-center justify-center shrink-0 group-hover:bg-construct-red transition-all duration-300 transform group-hover:translate-x-0.5">
                      <span class="text-construct-black group-hover:text-white text-lg">→</span>
                    </div>
                  </div>
                  <p class="text-xs md:text-sm text-gray-600 group-hover:text-white/80 line-clamp-3 mb-6 transition-colors leading-relaxed">
                    {{ item.data.description }}
                  </p>
                  
                  <!-- 技术标签与操作区靠底对齐 -->
                  <div class="mt-auto pt-4 border-t border-construct-black/10 group-hover:border-white/10 flex flex-wrap gap-1.5 transition-colors duration-300">
                    <span
                      v-for="tech in item.data.technologies"
                      :key="tech"
                      class="text-[9px] font-black tracking-[0.15em] px-2.5 py-1 group-hover:bg-white group-hover:text-construct-black bg-construct-black text-white transition-colors"
                    >
                      {{ tech }}
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
    </div>
  </div>
</template>

<style lang="scss" scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}

.project-card-featured {
  background-color: #000000;
  color: #ffffff;
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  
  &:hover {
    border-color: var(--accent, #CF202E) !important;
    box-shadow: -12px 12px 0px var(--accent, #CF202E);
    transform: translate(4px, -4px);
  }
}

.project-card-normal {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  
  &:hover {
    box-shadow: -12px 12px 0px #000000;
    transform: translate(4px, -4px);
  }
}
</style>