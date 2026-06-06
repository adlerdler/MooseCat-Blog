<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Search, ArrowRight, Send, Mail, CheckCircle, AlertCircle } from 'lucide-vue-next'
import axios from 'axios'
import { Motion, AnimatePresence } from 'motion-v'
import { useTheme } from '../../composables/useTheme'
import { useI18n } from 'vue-i18n'
import { usePageSeo } from '../../composables/usePageSeo'
import { usePageSeoData } from '../../composables/usePageSeoData'
import { useSiteConfig } from '../../composables/useSiteConfig'

import SplashScreen from '../../components/SplashScreen.vue'
import SidebarMenu from '../../components/SidebarMenu.vue'
import Footer from '../../components/Footer.vue'
import SearchOverlay from '../../components/SearchOverlay.vue'
import AdPopup from '../../components/front/AdPopup.vue'

const props = defineProps({
  posts: { type: Array, default: () => [] },
  projects: { type: Array, default: () => [] },
  videos: { type: Array, default: () => [] },
  menus: { type: Array, default: () => [] },
  siteConfig: { type: Object, default: () => ({}) },
  footerConfig: { type: Object, default: () => ({}) },
  themes: { type: Array, default: () => [] }
})

console.log('[DEBUG] Home.vue props received:', {
  posts: props.posts,
  postsCount: props.posts?.length || 0,
  projects: props.projects,
  projectsCount: props.projects?.length || 0,
  videos: props.videos,
  videosCount: props.videos?.length || 0
})

const marqueeText = 'ARCHYX VOL. 2026 // BUILDING SYSTEM // MINIMALISM //'
const techStack = ['TYPESCRIPT', 'VUE', 'LARAVEL', 'TAILWIND', 'NODE.JS', 'POSTGRES']

const { getSeoByPageKey } = usePageSeoData();
const homeSeo = getSeoByPageKey('home') || {};

const { SeoHead } = usePageSeo({
  title: homeSeo.title || 'Archyx - Design & Technology Blog',
  description: homeSeo.description || 'Exploring the intersection of design, technology, and human experience.',
  keywords: homeSeo.keywords || '',
})

const { t } = useI18n()
const { initAccentTheme } = useTheme()
const { isSearchVisible } = useSiteConfig()

const isFooterVisible = ref(true)
const isSearchOpen = ref(false)
const SPLASH_KEY = 'archyx_splash_shown'

/**
 * 双重存储策略：
 * 1. Cookie (max-age 1年) — 扛硬刷新、扛localStorage被清
 * 2. localStorage — 快速读取兜底
 * 只有 Cookie 和 localStorage 都找不到才放启动页
 */
const getCookie = (name) => {
  const prefix = name + '='
  const cookies = document.cookie.split(';')
  for (let i = 0; i < cookies.length; i++) {
    let c = cookies[i]
    while (c.charAt(0) === ' ') c = c.substring(1)
    if (c.indexOf(prefix) === 0) return c.substring(prefix.length)
  }
  return null
}

const setSplashCookie = () => {
  const d = new Date()
  d.setFullYear(d.getFullYear() + 1) // 1年后过期
  document.cookie = `${SPLASH_KEY}=1; expires=${d.toUTCString()}; path=/; SameSite=Lax`
}

const hasSplashShown = () => {
  return getCookie(SPLASH_KEY) === '1' || localStorage.getItem(SPLASH_KEY) === 'true'
}

const markSplashShown = () => {
  setSplashCookie()
  localStorage.setItem(SPLASH_KEY, 'true')
}

const showSplash = ref(false)
const showContent = ref(false)
let splashTimer = null

// 订阅表单
const subscribeEmail = ref('')
const subscribeLoading = ref(false)
const subscribeMessage = ref('')
const subscribeError = ref(false)

const handleSubscribe = async () => {
  if (!subscribeEmail.value.trim()) return
  
  subscribeLoading.value = true
  subscribeMessage.value = ''
  subscribeError.value = false
  
  try {
    const { data } = await axios.post('/subscribe', {
      email: subscribeEmail.value
    })
    subscribeMessage.value = data.message || t('subscribe_success')
    subscribeError.value = false
    subscribeEmail.value = ''
  } catch (err) {
    subscribeError.value = true
    if (err.response?.status === 422) {
      subscribeMessage.value = err.response.data.errors?.email?.[0] || t('subscribe_duplicate')
    } else {
      subscribeMessage.value = t('subscribe_error')
    }
  } finally {
    subscribeLoading.value = false
  }
}

const featuredPosts = computed(() => {
  // 随机打乱后取前 3 篇
  const shuffled = [...props.posts].sort(() => Math.random() - 0.5)
  return shuffled.slice(0, 3).map(post => ({
    id: post.id,
    title: post.title,
    slug: post.slug,
    excerpt: post.excerpt,
    category: post.category?.name || 'UNCATEGORIZED',
    categoryLabel: post.category?.name || 'UNCATEGORIZED',
    views_count: post.views_count || 0
  }))
})

console.log('[DEBUG] Home.vue script setup completed')

onMounted(() => {
  console.log('[DEBUG] Home.vue onMounted, props.posts:', props.posts)
  initAccentTheme()

  // 前台页面不受后台主题设置影响，移除 light class
  document.documentElement.classList.remove('light')

  const saved = sessionStorage.getItem('footer_visible')
  if (saved !== null) {
    isFooterVisible.value = saved === 'true'
  }

  // Cookie + localStorage 双重检查，任一存在就跳过启动页
  if (hasSplashShown()) {
    showContent.value = true
    showSplash.value = false
  } else {
    showSplash.value = true
  }
  console.log('[DEBUG] Home.vue onMounted complete, showSplash:', showSplash.value, 'showContent:', showContent.value)
})

watch(isFooterVisible, (newVal) => {
  sessionStorage.setItem('footer_visible', String(newVal))
})

watch(() => props.posts, (newPosts) => {
  console.log('[DEBUG] posts prop changed:', newPosts)
}, { immediate: true })

const handleSplashComplete = () => {
  showSplash.value = false
  markSplashShown()  // Cookie + localStorage 双写
  splashTimer = setTimeout(() => {
    showContent.value = true
    splashTimer = null
  }, 100)
}

const openSearch = () => {
  isSearchOpen.value = true
}

const closeSearch = () => {
  isSearchOpen.value = false
}

onUnmounted(() => {
  if (splashTimer) {
    clearTimeout(splashTimer)
    splashTimer = null
  }
})
</script>

<template>
  <SeoHead />
  <div class="min-h-screen selection:bg-construct-red selection:text-white">


    <!-- Splash Screen -->
    <SplashScreen
      v-if="showSplash"
      @complete="handleSplashComplete"
    />

    <!-- 弹窗广告 -->
    <AdPopup />

    <!-- Main Content -->
    <template v-if="showContent">
    <!-- Sidebar Menu -->
    <SidebarMenu
      v-model:is-footer-visible="isFooterVisible"
      :menus="menus"
      :site-config="siteConfig"
      :footer-config="footerConfig"
      :posts="posts"
      :videos="videos"
      :projects="projects"
      :themes="themes"
    />

    <!-- Main Content with left margin for sidebar -->
    <div class="md:ml-16 pt-16 md:pt-0">

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
            v-if="isSearchVisible()"
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
                  {{ t('hero_search_placeholder') }}
                </div>
              </div>
              <div
                class="bg-construct-black text-white px-8 py-4 sm:py-0 flex items-center justify-center font-display tracking-widest text-sm font-bold hover:bg-construct-red cursor-pointer transition-colors active:translate-x-1 active:translate-y-1 whitespace-nowrap relative z-20"
              >
                {{ t('hero_search_btn') }}
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
              {{ t('home_featured_posts') }}
            </h2>
            <div class="text-[10px] font-bold tracking-[0.4em] opacity-40 uppercase">
              FEATURED ARTIFACTS // {{ t('home_featured_subtitle') }}
            </div>
          </div>
          <Link href="/blog" class="group flex flex-col items-end gap-2">
            <div class="flex items-center gap-4 text-xs font-bold tracking-widest uppercase hover:text-construct-red transition-colors">
              <span>{{ t('home_view_all') }}</span>
              <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-2" />
            </div>
          </Link>
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
                  <p class="text-sm opacity-60 line-clamp-2 mb-12 font-medium leading-relaxed">
                    {{ post.excerpt }}
                  </p>
                </div>
                <Link
                  :href="`/blog/${post.slug}`"
                  class="inline-flex items-center gap-4 text-[10px] font-bold tracking-widest hover:translate-x-4 transition-transform duration-300 uppercase"
                >
                  VIEW DETAILS
                  <ArrowRight class="w-4 h-4" />
                </Link>
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
              {{ t('home_about_author') }}
            </div>
            <h4 class="font-display text-5xl md:text-6xl lg:text-7xl tracking-tighter mb-8 leading-none whitespace-pre-line text-construct-black">
              {{ t('home_structural_honesty') }}
            </h4>
            <p class="text-xl md:text-2xl font-medium tracking-wide opacity-80 max-w-2xl leading-relaxed">
              {{ t('home_author_desc') }}
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
              {{ t('home_tech_stack') }}
            </h2>
            <p class="text-sm font-medium tracking-widest uppercase opacity-60 max-w-xs leading-relaxed">
              {{ t('home_tech_stack_subtitle') }}
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
        <form @submit.prevent="handleSubscribe" class="max-w-2xl mx-auto relative flex flex-col sm:flex-row items-stretch shadow-[-8px_8px_0px_#000] bg-white focus-within:ring-4 focus-within:ring-construct-black transition-all duration-300 hover:scale-[1.02]">
          <div class="flex-1 bg-construct-black/5 flex items-center pl-6">
            <Mail class="w-5 h-5 text-construct-black/40 mr-4" />
            <input
              v-model="subscribeEmail"
              type="email"
              :placeholder="t('home_email_placeholder')"
              class="w-full bg-transparent py-6 outline-none font-display font-medium text-lg uppercase tracking-widest placeholder:text-construct-black/20 text-construct-black"
              required
            />
          </div>
          <button
            type="submit"
            :disabled="subscribeLoading"
            class="bg-construct-red text-white px-12 py-6 font-display font-black tracking-widest text-lg hover:bg-construct-black transition-colors duration-300 flex items-center justify-center gap-2 disabled:opacity-60"
          >
            <span v-if="subscribeLoading" class="inline-block w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin" />
            <template v-else>
              {{ t('home_subscribe_btn') }}
              <Send class="w-5 h-5" />
            </template>
          </button>
        </form>
        <Transition name="fade">
          <p v-if="subscribeMessage" :class="['mt-4 text-sm font-bold tracking-wider', subscribeError ? 'text-red-600' : 'text-green-600']">
            <CheckCircle v-if="!subscribeError" class="w-4 h-4 inline mr-1" />
            <AlertCircle v-else class="w-4 h-4 inline mr-1" />
            {{ subscribeMessage }}
          </p>
        </Transition>
      </div>
    </section>

    <!-- Footer -->
    <Footer 
      v-model="isFooterVisible" 
      :footer-config="footerConfig"
      :site-config="siteConfig"
    />

    <!-- Search Overlay -->
    <SearchOverlay v-if="isSearchVisible()" :is-open="isSearchOpen" @close="closeSearch" />

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

// 订阅反馈消息淡入淡出
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
