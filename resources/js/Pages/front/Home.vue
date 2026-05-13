<script setup>
/**
 * Home.vue - 首页/主页
 * 
 * 功能说明：
 * - 展示网站首页，包含全屏欢迎区域和最新内容预览
 * - 集成启动画面（Splash Screen）效果，首次访问显示动画
 * - 提供全局搜索功能
 * - 响应式布局，适配各种屏幕尺寸
 * 
 * 主要交互：
 * - 点击跳转至博客列表、视频页面、项目页面等
 * - 键盘快捷键打开搜索（需安装 SearchOverlay）
 * - 底部 Footer 显示控制
 */
import { ref, onMounted, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { Search, ArrowRight, Send, Mail } from 'lucide-vue-next'
import { Motion, AnimatePresence } from 'motion-v'
import { useTheme } from '../../composables/useTheme'
import { useI18n } from 'vue-i18n'
import { categories, marqueeText, techStack, featuredPosts } from '../../data/home'

const { t } = useI18n()
const { initAccentTheme } = useTheme()

const isFooterVisible = ref(true)
const isSearchOpen = ref(false)
const showSplash = ref(false)
const showContent = ref(false)

onMounted(() => {
  initAccentTheme()
  
  // 前台页面不受后台主题设置影响，移除 light class
  document.documentElement.classList.remove('light')

  const saved = sessionStorage.getItem('footer_visible')
  if (saved !== null) {
    isFooterVisible.value = saved === 'true'
  }

  const splashShown = sessionStorage.getItem('splash_shown')
  if (splashShown !== 'true') {
    showSplash.value = true
  } else {
    showContent.value = true
  }
})

watch(isFooterVisible, (newVal) => {
  sessionStorage.setItem('footer_visible', String(newVal))
})

const handleSplashComplete = () => {
  showSplash.value = false
  sessionStorage.setItem('splash_shown', 'true')
  setTimeout(() => {
    showContent.value = true
  }, 100)
}

const openSearch = () => {
  isSearchOpen.value = true
}

const closeSearch = () => {
  isSearchOpen.value = false
}

const searchQuery = ref('')
const activeCategory = ref('ALL')
</script>

<template>
  <div class="min-h-screen selection:bg-construct-red selection:text-white">
    <!-- Splash Screen -->
    <SplashScreen
      v-if="showSplash"
      @complete="handleSplashComplete"
    />

    <!-- Main Content -->
    <template v-if="showContent">
    <!-- Sidebar Menu -->
    <SidebarMenu
      v-model:is-footer-visible="isFooterVisible"
    />

    <!-- Main Content with left margin for sidebar -->
    <div class="ml-16">

    <!-- Hero Section -->
    <header class="relative min-h-screen bg-construct-paper overflow-hidden flex items-center py-32">
      <div class="absolute top-0 right-0 w-3/4 h-full bg-construct-red construct-diagonal z-0"></div>

      <div class="container mx-auto px-8 relative z-10">
        <div class="max-w-4xl">
          <Motion
            :initial="{ opacity: 0, x: -50 }"
            :animate="{ opacity: 1, x: 0 }"
          >
            <div class="inline-block bg-construct-black text-white px-4 py-1 text-sm font-bold tracking-tighter mb-4">
              EST. VOL.2026
            </div>
          </Motion>

          <Motion
            :initial="{ opacity: 0, y: 50 }"
            :animate="{ opacity: 1, y: 0 }"
            :transition="{ delay: 0.2 }"
          >
            <h1 class="font-display text-5xl sm:text-7xl md:text-8xl lg:text-9xl leading-[0.85] tracking-tighter text-construct-black mb-8 w-full">
              <div class="ml-4 sm:ml-8 md:ml-16">ARCHYX</div>
              <div class="text-right mt-2 sm:mt-4 pr-4 sm:pr-8 md:pr-0">
                <span class="text-white">BLOG</span>
              </div>
            </h1>
          </Motion>

          <Motion
            :initial="{ opacity: 0 }"
            :animate="{ opacity: 1 }"
            :transition="{ delay: 0.4 }"
          >
            <p class="text-xl md:text-2xl font-medium max-w-xl text-construct-black/80 mb-12">
              {{ t('footer_desc') }}
            </p>
          </Motion>

          <!-- Search Bar -->
          <Motion
            :initial="{ opacity: 0, y: 20 }"
            :animate="{ opacity: 1, y: 0 }"
            :transition="{ delay: 0.6 }"
          >
            <div 
              class="flex flex-col sm:flex-row items-stretch max-w-2xl group relative cursor-pointer"
              @click="openSearch"
            >
              <div class="relative flex-1">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-construct-black/40" />
                <div
                  class="w-full bg-white border-4 border-construct-black px-12 py-4 text-sm font-bold tracking-widest transition-all cursor-pointer"
                >
                  {{ t('subscribe_input') }}
                </div>
              </div>
              <div
                class="bg-construct-black text-white px-8 py-4 sm:py-0 flex items-center justify-center font-display tracking-widest text-sm font-bold hover:bg-construct-red cursor-pointer transition-colors active:translate-x-1 active:translate-y-1 whitespace-nowrap relative z-20"
              >
                {{ t('subscribe_btn') }}
              </div>
            </div>
          </Motion>
        </div>
      </div>

      <!-- Marquee -->
      <div class="absolute bottom-0 left-0 w-full overflow-hidden bg-construct-black text-white py-2">
        <div class="marquee-container">
          <div class="marquee-content">
            <span class="marquee-text">{{ marqueeText }}</span>
            <span class="marquee-text">{{ marqueeText }}</span>
          </div>
        </div>
      </div>
    </header>

    <!-- Featured Section -->
    <section class="bg-construct-black text-white min-h-screen py-32 px-8 overflow-hidden relative flex items-center">
      <div class="absolute top-0 right-0 w-1/2 h-full bg-white opacity-5 construct-diagonal pointer-events-none"></div>

      <div class="container mx-auto relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-end mb-24 gap-8">
          <div class="max-w-2xl">
            <h2 class="font-display text-5xl md:text-6xl lg:text-8xl tracking-tighter leading-none mb-8 whitespace-pre-line">
              精选文章
            </h2>
            <div class="text-[10px] font-bold tracking-[0.4em] opacity-40 uppercase">
              FEATURED ARTIFACTS // 最新发布的技术文章与研究
            </div>
          </div>
          <RouterLink to="/posts" class="group flex flex-col items-end gap-2">
            <div class="flex items-center gap-4 text-xs font-bold tracking-widest uppercase hover:text-construct-red transition-colors">
              <span>访问全部归档</span>
              <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-2" />
            </div>
          </RouterLink>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
          <Motion
            v-for="(post, idx) in featuredPosts"
            :key="post.id"
            :initial="{ opacity: 0, scale: 0.95 }"
            :while-in-view="{ opacity: 1, scale: 1 }"
            :viewport="{ once: true }"
            :transition="{ delay: idx * 0.1 }"
          >
            <div class="group relative">
              <div class="p-6 sm:p-10 border-4 border-white h-full flex flex-col justify-between bg-transparent hover:bg-construct-red transition-all duration-300 hover:-translate-y-2 hover:translate-x-2">
                <div>
                  <span class="text-[10px] font-bold tracking-widest opacity-40 mb-8 sm:mb-12 block">
                    {{ post.category }} // VOL_{{ post.id }}
                  </span>
                  <h3 class="font-display text-3xl sm:text-4xl tracking-tighter leading-[0.9] mb-6">
                    {{ post.title }}
                  </h3>
                  <p class="text-sm opacity-60 line-clamp-3 mb-12 font-medium leading-relaxed">
                    {{ post.excerpt }}
                  </p>
                </div>
                <a
                  :href="`/posts/${post.id}`"
                  class="inline-flex items-center gap-4 text-[10px] font-bold tracking-widest hover:translate-x-4 transition-transform duration-300 uppercase"
                >
                  查看详情
                  <ArrowRight class="w-4 h-4" />
                </a>
              </div>
              <!-- Decorative Background Card -->
              <div class="absolute -z-10 inset-0 border-4 border-white/10 translate-y-4 -translate-x-4 pointer-events-none group-hover:bg-white/5 transition-all"></div>
            </div>
          </Motion>
        </div>
      </div>
    </section>

    <!-- About The Author -->
    <section class="min-h-screen py-32 px-8 bg-construct-paper flex items-center relative overflow-hidden">
      <div class="container mx-auto relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-16 items-center">
          <div class="md:col-span-5 relative">
            <div class="aspect-square w-full bg-construct-black flex items-center justify-center p-8 border-4 border-construct-red relative group">
              <div class="absolute inset-0 bg-cover bg-center opacity-40 mix-blend-luminosity grayscale transition-all duration-700 group-hover:grayscale-0 group-hover:opacity-100" style="background-image: url('https://images.unsplash.com/photo-1549692520-acc6669e2f0c?q=80&w=1000')"></div>
              <span class="font-display text-9xl text-white text-center leading-none tracking-tighter relative z-10 opacity-80 transition-opacity duration-500 group-hover:opacity-0">
                A
              </span>
              <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-construct-red flex items-center justify-center font-display text-4xl text-white tracking-widest shadow-[-4px_4px_0px_#000]">
                JS
              </div>
            </div>
          </div>
          <div class="md:col-span-7 flex flex-col justify-center">
            <div class="inline-block bg-construct-red text-white px-3 py-1 text-[10px] font-black tracking-[0.3em] uppercase w-fit mb-8">
              作者简介
            </div>
            <h4 class="font-display text-5xl md:text-6xl lg:text-7xl tracking-tighter mb-8 leading-none whitespace-pre-line text-construct-black">
              结构诚实
            </h4>
            <p class="text-xl md:text-2xl font-medium tracking-wide opacity-80 max-w-2xl leading-relaxed">
              专注于探索建筑数字化与技术创新的边界，致力于将传统设计理念与现代计算方法相融合，创造更智能、更可持续的数字空间。
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Core Tech Stack -->
    <section class="py-32 px-8 bg-construct-black text-white relative border-y-8 border-construct-red">
      <div class="container mx-auto max-w-6xl">
        <div class="flex flex-col md:flex-row gap-16 items-start">
          <div class="md:w-1/3">
            <h2 class="font-display text-4xl md:text-5xl lg:text-6xl tracking-tighter leading-[0.85] mb-6 whitespace-pre-line">
              技术栈
            </h2>
            <p class="text-sm font-medium tracking-widest uppercase opacity-60 max-w-xs leading-relaxed">
              核心工具与技术选型
            </p>
          </div>
          <div class="md:w-2/3 grid grid-cols-2 sm:grid-cols-3 gap-8">
            <Motion
              v-for="(tech, idx) in techStack"
              :key="idx"
              :initial="{ opacity: 0, y: 20 }"
              :while-in-view="{ opacity: 1, y: 0 }"
              :viewport="{ once: true }"
              :transition="{ delay: idx * 0.1 }"
            >
              <div class="p-6 border-2 border-white/20 hover:border-construct-red hover:bg-construct-red transition-all duration-300 flex items-center justify-center min-h-[140px] group">
                <span class="font-display text-xl sm:text-2xl font-black tracking-widest text-center group-hover:scale-110 transition-transform">
                  {{ tech }}
                </span>
              </div>
            </Motion>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter -->
    <section class="py-32 px-8 bg-construct-paper">
      <div class="container mx-auto max-w-4xl text-center">
        <div class="inline-block bg-construct-black text-white px-3 py-1 text-[10px] font-black tracking-[0.3em] uppercase mb-8">
          TRANSMISSION
        </div>
        <h2 class="font-display text-5xl md:text-7xl tracking-tighter text-construct-black mb-8">
          {{ t('subscribe_title') }}
        </h2>
        <p class="text-lg font-medium opacity-60 mb-12 max-w-xl mx-auto">
          {{ t('subscribe_desc') }}
        </p>
        <form class="max-w-2xl mx-auto relative flex flex-col sm:flex-row items-stretch shadow-[-8px_8px_0px_#000] bg-white focus-within:ring-4 focus-within:ring-construct-black transition-all duration-300 hover:scale-[1.02]">
          <div class="flex-1 bg-construct-black/5 flex items-center pl-6">
            <Mail class="w-5 h-5 text-construct-black/40 mr-4" />
            <input
              type="email"
              placeholder="输入邮箱地址..."
              class="w-full bg-transparent py-6 outline-none font-display font-medium text-lg uppercase tracking-widest placeholder:text-construct-black/20 text-construct-black"
              required
            />
          </div>
          <button
            type="submit"
            class="bg-construct-red text-white px-12 py-6 font-display font-black tracking-widest text-lg hover:bg-construct-black transition-colors duration-300 flex items-center justify-center gap-2"
          >
            订阅
            <Send class="w-5 h-5" />
          </button>
        </form>
      </div>
    </section>

    <!-- Footer -->
    <Footer v-model="isFooterVisible" />

    <!-- Search Overlay -->
    <SearchOverlay :is-open="isSearchOpen" @close="closeSearch" />

    </div><!-- End of ml-16 wrapper -->

    </template><!-- End of showContent -->

  </div>
</template>

<style lang="scss" scoped>
// 基础变量
$font-display: 'Space Grotesk', system-ui, sans-serif;
$marquee-duration: 20s;
$marquee-spacing: 2rem;
$text-sm: 0.75rem;
$spacing-2: 0.5rem;

.font-display {
  font-family: $font-display;
}

// Marquee 动画
.marquee {
  &-container {
    overflow: hidden;
    width: 100%;
  }

  &-content {
    display: flex;
    animation: marquee $marquee-duration linear infinite;
  }

  &-text {
    display: inline-block;
    font-family: $font-display;
    font-size: $text-sm;
    font-weight: bold;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    white-space: nowrap;
    padding: 0 $marquee-spacing;
  }
}

@keyframes marquee {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
}

// 构造样式 - clip-path 由 Tailwind 处理
.construct-diagonal {
  clip-path: polygon(15% 0, 100% 0, 100% 100%, 0% 100%);
}
</style>
