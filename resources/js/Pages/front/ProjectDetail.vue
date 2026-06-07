<script setup>
/**
 * ProjectDetail.vue - 项目详情页
 * 
 * 功能说明：
 * - 展示单个项目的详细信息
 * - 包含项目描述、技术栈、使用技术图标展示
 * - 外部链接和 GitHub 仓库跳转
 * 
 * 页面特色：
 * - 响应式技术栈网格
 * - 外部链接图标动画
 * - 项目元数据展示
 */
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Motion, AnimatePresence } from 'motion-v';
import { useTheme } from '../../composables/useTheme';
import { usePageSeo } from '../../composables/usePageSeo';
import { formatId } from '../../utils/typeConvert';
import { ExternalLink, Github, ArrowLeft, ArrowUp, Terminal, Cpu, Layers, Globe, Code } from 'lucide-vue-next';
import AdSlot from '../../components/front/AdSlot.vue';

const props = defineProps({
  project: { type: Object, default: null }
});

const { initAccentTheme } = useTheme();

const { SeoHead } = usePageSeo({
  title: computed(() => props.project?.meta_title || (props.project?.title ? `PROJECT // ${props.project.title} - ARKHYX` : 'PROJECT - ARKHYX')),
  description: computed(() => props.project?.meta_description || props.project?.description || ''),
  keywords: computed(() => props.project?.meta_keywords || (props.project?.technologies?.join(', ') || '')),
  image: computed(() => props.project?.image || ''),
  url: computed(() => `${window.location.origin}/projects/${props.project?.slug || props.project?.id}`),
  type: 'CreativeWork',
})

// 检查项目是否存在且为已完成状态
const isValidProject = computed(() => {
  return props.project && props.project.status === 'completed';
});

const iframeError = ref(false);
const iframeLoading = ref(true);
const showBackToTop = ref(false);
const showHeader = ref(true);

const handleScroll = () => {
  showBackToTop.value = window.scrollY > 300;
  showHeader.value = window.scrollY < 10;
};

onMounted(() => {
  initAccentTheme();
  // 前台页面不受后台主题设置影响，移除 light class
  document.documentElement.classList.remove('light');
  window.addEventListener('scroll', handleScroll);

  // 超时检测：如果 iframe 5 秒后还在 loading，认为加载失败
  setTimeout(() => {
    if (iframeLoading.value) {
      iframeError.value = true;
      iframeLoading.value = false;
    }
  }, 5000);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});

const goBack = () => {
  window.history.back();
};

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>

<template>
  <SeoHead />
  <div class="min-h-screen bg-construct-paper text-construct-black pb-32">
    <!-- 浮动返回按钮 -->
    <button
      @click="goBack"
      class="fixed top-4 left-4 z-50 w-12 h-12 bg-construct-black text-white flex items-center justify-center hover:bg-construct-red transition-colors shadow-lg"
    >
      <ArrowLeft class="w-6 h-6" />
    </button>

    <!-- 回到顶部按钮 -->
    <Transition name="fade">
      <button
        v-if="showBackToTop"
        @click="scrollToTop"
        class="fixed bottom-4 right-4 z-50 w-12 h-12 bg-construct-red text-white flex items-center justify-center hover:bg-construct-black transition-colors shadow-lg"
      >
        <ArrowUp class="w-6 h-6" />
      </button>
    </Transition>

    <!-- 404状态 -->
    <div v-if="!project" class="min-h-screen flex items-center justify-center bg-construct-paper">
      <div class="text-center">
        <h1 class="font-display text-4xl mb-4 uppercase">Node not found</h1>
        <Link
          href="/projects"
          class="font-bold tracking-widest text-construct-red hover:underline"
        >
          RETURN TO REPOSITORY
        </Link>
      </div>
    </div>

    <!-- 项目详情内容 -->
    <template v-else>
      <!-- 头部 / 顶部导航 -->
      <Transition name="fade">
        <header
          v-if="showHeader"
          class="fixed top-0 left-0 right-0 h-16 bg-white/80 backdrop-blur-md border-b border-construct-black/10 z-40 flex items-center justify-between pl-16 pr-6 md:pl-20 md:pr-12"
        >
        <div class="flex items-center gap-4">
          <span class="text-[10px] font-black tracking-[0.4em] uppercase opacity-40">
            PROJ_{{ formatId(project.id, 2) }} // ARCHIVE
          </span>
        </div>
        <div class="flex items-center gap-6">
          <a
            v-if="project.github_url"
            :href="project.github_url"
            target="_blank"
            rel="noopener noreferrer"
            class="hover:text-construct-red transition-colors"
          >
            <Github size="20" />
          </a>
          <a
            v-if="project.url"
            :href="project.url"
            target="_blank"
            rel="noopener noreferrer"
            class="bg-construct-black text-white px-4 py-1.5 text-[10px] font-bold tracking-widest uppercase hover:bg-construct-red transition-colors flex items-center gap-2"
          >
            LIVE VIEW
            <ExternalLink size="12" />
          </a>
        </div>
      </header>
      </Transition>

      <!-- 主内容布局 -->
      <div class="pt-24 px-6 md:px-12 max-w-[1700px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 xl:gap-24">
        <!-- 左列：标题和介绍 -->
        <div class="lg:col-span-8">
          <Motion
            :initial="{ opacity: 0, x: -30 }"
            :animate="{ opacity: 1, x: 0 }"
            :transition="{ duration: 0.6 }"
          >
            <div class="flex items-center gap-4 mb-4">
              <div class="w-12 h-1 bg-construct-red" />
              <span class="text-xs font-bold tracking-[0.5em] text-construct-red uppercase">
                {{ project.year || '2026' }} DEPLOYMENT
              </span>
            </div>
            <h1 class="font-display text-5xl md:text-7xl lg:text-8xl xl:text-[10rem] leading-[0.8] tracking-tighter mb-12 uppercase italic whitespace-pre-line">
              {{ project.title.replace('_', '_\n') }}
            </h1>
          </Motion>

          <!-- 主图 / 可视化 -->
          <Motion
            :initial="{ opacity: 0, y: 50 }"
            :animate="{ opacity: 1, y: 0 }"
            :transition="{ delay: 0.2, duration: 0.8 }"
            class="relative mb-24 group"
          >
            <div class="absolute -top-4 -left-4 w-24 h-24 border-t-4 border-l-4 border-construct-red z-10" />
            <div class="absolute -bottom-4 -right-4 w-24 h-24 border-b-4 border-r-4 border-construct-red z-10" />
            <div class="overflow-hidden border-4 border-construct-black aspect-video bg-construct-black">
              <img
                :src="project.image"
                :alt="project.title"
                class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700 grayscale hover:grayscale-0"
              />
            </div>
            <!-- 技术覆盖层 -->
            <div class="absolute top-4 right-4 text-[8px] font-mono text-white tracking-widest leading-loose flex flex-col items-end pointer-events-none opacity-40">
              <span>RESOLUTION: AUTO</span>
              <span>GEOMETRY: OPTIMIZED</span>
              <span>SYSTEM: OK</span>
            </div>
          </Motion>

          <!-- 长描述 -->
          <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-24">
            <div class="md:col-span-4">
              <h3 class="font-display text-2xl uppercase tracking-tighter mb-4 border-b-2 border-construct-black pb-2">
                Abstract //
              </h3>
              <p class="text-sm font-bold tracking-widest uppercase opacity-40 leading-relaxed">
                Structural analysis and deployment parameters.
              </p>
            </div>
            <div class="md:col-span-8">
              <p class="font-medium text-xl md:text-2xl leading-relaxed text-construct-black italic">
                {{ project.long_description || project.description }}
              </p>
            </div>
          </div>

          <!-- 交互查看器 / 沙箱 -->
          <Motion
            v-if="project.url"
            :initial="{ opacity: 0 }"
            :animate="{ opacity: 1 }"
            class="mb-24"
          >
            <div class="bg-construct-black text-white p-4 flex items-center justify-between border-x-4 border-t-4 border-construct-black">
              <div class="flex items-center gap-3">
                <Globe size="16" class="text-construct-red" />
                <span class="text-[10px] font-bold tracking-[0.2em] uppercase">VIEWPORT_LIVE_PROJECTION</span>
              </div>
              <div class="flex gap-2">
                <div class="w-2 h-2 rounded-full bg-white/20" />
                <div class="w-2 h-2 rounded-full bg-white/20" />
                <div class="w-2 h-2 rounded-full bg-construct-red" />
              </div>
            </div>
            <div class="w-full aspect-video border-4 border-construct-black bg-white shadow-2xl relative overflow-hidden">
              <div v-if="iframeLoading && !iframeError" class="absolute inset-0 flex items-center justify-center bg-gray-100 z-10">
                <div class="text-center">
                  <div class="w-8 h-8 border-4 border-construct-black border-t-construct-red animate-spin mx-auto mb-4"></div>
                  <p class="text-xs font-bold tracking-widest uppercase opacity-60">Loading...</p>
                </div>
              </div>
              <iframe
                v-if="!iframeError"
                :src="project.url"
                class="w-full h-full"
                :title="project.title"
                @load="iframeLoading = false"
                @error="iframeError = true"
                sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-popups-to-escape-sandbox"
              />
              <div v-if="iframeError" class="w-full h-full flex flex-col items-center justify-center bg-gray-900 text-white">
                <Globe size="48" class="mb-4 opacity-40" />
                <p class="text-sm font-bold tracking-widest uppercase mb-2">Preview Blocked</p>
                <p class="text-xs opacity-60 mb-4">This site prevents iframe embedding</p>
                <a
                  :href="project.url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex items-center gap-2 bg-construct-red text-white px-4 py-2 text-xs font-bold tracking-widest uppercase hover:bg-white hover:text-construct-black transition-colors"
                >
                  <ExternalLink size="14" />
                  Open in New Tab
                </a>
              </div>
              <div class="absolute bottom-4 right-4 pointer-events-none">
                <div class="bg-construct-red text-white text-[8px] font-black tracking-widest px-2 py-1 flex items-center gap-2">
                  <Terminal size="10" /> LINKED_IFRAME_PROTOCOL
                </div>
              </div>
            </div>
          </Motion>
        </div>

        <!-- 右列：技术规格 / 固定 -->
        <aside class="lg:col-span-4 lg:sticky lg:top-24 xl:top-32 lg:h-fit">
          <!-- Sidebar Ad -->
          <AdSlot position="sidebar" class="mb-8" />

          <Motion
            :initial="{ opacity: 0, y: 30 }"
            :animate="{ opacity: 1, y: 0 }"
            :transition="{ delay: 0.4 }"
            class="border-4 md:border-8 border-construct-black p-6 md:p-10 lg:p-12 bg-white relative overflow-hidden"
          >
            <!-- 背景图形 -->
            <div class="absolute top-0 right-0 font-display text-[200px] text-construct-black/5 leading-none translate-x-1/4 -translate-y-1/4 pointer-events-none">
              #
            </div>

            <h2 class="font-display text-4xl mb-12 tracking-tighter uppercase relative z-10">Specs //</h2>

            <div class="space-y-10 relative z-10">
              <div class="group">
                <div class="flex items-center gap-3 text-xs font-black tracking-widest opacity-30 mb-2 uppercase group-hover:text-construct-red group-hover:opacity-100 transition-all">
                  <div class="w-4 h-[1px] bg-current" />
                  Role / Contribution
                </div>
                <div class="text-xl font-display uppercase tracking-tight">
                  {{ project.role || 'Lead Designer' }}
                </div>
              </div>

              <div class="group">
                <div class="flex items-center gap-3 text-xs font-black tracking-widest opacity-30 mb-2 uppercase group-hover:text-construct-red group-hover:opacity-100 transition-all">
                  <div class="w-4 h-[1px] bg-current" />
                  Technologies
                </div>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="tech in project.technologies"
                    :key="tech"
                    class="bg-gray-100 px-2 py-1 text-[10px] font-bold tracking-widest uppercase flex items-center gap-2 border border-construct-black/10"
                  >
                    <Code size="10" /> {{ tech }}
                  </span>
                </div>
              </div>

              <div class="group">
                <div class="flex items-center gap-3 text-xs font-black tracking-widest opacity-30 mb-2 uppercase group-hover:text-construct-red group-hover:opacity-100 transition-all">
                  <div class="w-4 h-[1px] bg-current" />
                  Category Tags
                </div>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="tag in project.tags"
                    :key="tag"
                    class="bg-construct-black text-white px-3 py-1.5 text-[9px] font-black tracking-widest uppercase hover:bg-construct-red transition-colors cursor-default"
                  >
                    {{ tag }}
                  </span>
                </div>
              </div>

              <div class="pt-8 border-t border-construct-black/10 flex flex-col gap-4">
                <a
                  v-if="project.github_url"
                  :href="project.github_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex items-center justify-between group/link"
                >
                  <span class="text-xs font-bold tracking-widest uppercase flex items-center gap-3">
                    <Github size="16" /> Source Code
                  </span>
                  <ArrowLeft size="16" class="rotate-180 opacity-0 group-hover/link:opacity-100 transition-all group-hover/link:translate-x-2" />
                </a>
                <a
                  v-if="project.url"
                  :href="project.url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex items-center justify-between group/link"
                >
                  <span class="text-xs font-bold tracking-widest uppercase flex items-center gap-3 text-construct-red">
                    <Globe size="16" /> Production Live
                  </span>
                  <ArrowLeft size="16" class="rotate-180 text-construct-red opacity-0 group-hover/link:opacity-100 transition-all group-hover/link:translate-x-2" />
                </a>
              </div>
            </div>
          </Motion>

          <div class="mt-8 p-6 bg-construct-black text-white flex flex-col gap-4">
            <div class="flex items-center gap-3">
              <div class="w-2 h-2 bg-construct-red animate-pulse" />
              <span class="text-[10px] font-bold tracking-[0.5em] uppercase">SYSTEM_STATUS_ONLINE</span>
            </div>
            <p class="text-[9px] font-mono opacity-40 leading-relaxed uppercase">
              ARCHIVING COMPLETE. DATA CLUSTERS VERIFIED. NODE STABILITY 99.4%.
            </p>
          </div>
        </aside>
      </div>
    </template>
  </div>
</template>

<style lang="scss" scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>