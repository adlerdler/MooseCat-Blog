<script setup>
import { ref, computed, onMounted } from 'vue'
import { Search, ArrowRight, Send, Mail, Twitter, Linkedin, Github } from 'lucide-vue-next'
import { MotionComponent as Motion, fade, slideBottom } from '@vueuse/motion'
import SidebarMenu from '../components/SidebarMenu.vue'
import Footer from '../components/Footer.vue'
import { useTheme } from '../composables/useTheme'

const { initTheme } = useTheme()

const isFooterVisible = ref(true)

onMounted(() => {
  initTheme()

  const saved = sessionStorage.getItem('footer_visible')
  if (saved !== null) {
    isFooterVisible.value = saved === 'true'
  }
})

const searchQuery = ref('')
const activeCategory = ref('ALL')

const categories = ['ALL', 'THEORY', 'DESIGN', 'HISTORY', 'CULTURE']

const marqueeText = 'ARCHYX VOL. 2026 // BUILDING SYSTEM // MINIMALISM //'

const techStack = ['TYPESCRIPT', 'VUE', 'LARAVEL', 'TAILWIND', 'NODE.JS', 'POSTGRES']

const featuredPosts = computed(() => {
  return [
    {
      id: 1,
      title: 'ARCHITECTURAL PRINCIPLES',
      excerpt: 'Exploring the fundamental concepts that define modern structural design and digital innovation.',
      category: 'THEORY',
      categoryLabel: '理论',
      views_count: 1234
    },
    {
      id: 2,
      title: 'DIGITAL FABRICATION',
      excerpt: 'How computational design is revolutionizing the way we approach construction and manufacturing.',
      category: 'DESIGN',
      categoryLabel: '设计',
      views_count: 987
    },
    {
      id: 3,
      title: 'SUSTAINABLE MATERIALS',
      excerpt: 'An investigation into eco-friendly building materials and their impact on environmental architecture.',
      category: 'HISTORY',
      categoryLabel: '历史',
      views_count: 756
    }
  ]
})
</script>

<template>
  <div class="min-h-screen selection:bg-construct-red selection:text-white">
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
            :initial="{ opacity: 0, y: 50 }"
            :enter="{ opacity: 1, y: 0 }"
            :transition="{ duration: 0.8, ease: 'easeOut' }"
          >
            <div class="inline-block bg-construct-black text-white px-4 py-1 text-sm font-bold tracking-tighter mb-4">
              EST. VOL.2026
            </div>
          </Motion>

          <Motion
            :initial="{ opacity: 0, y: 50 }"
            :enter="{ opacity: 1, y: 0 }"
            :transition="{ duration: 0.8, ease: 'easeOut', delay: 0.2 }"
          >
            <h1 class="font-display text-5xl sm:text-7xl md:text-8xl lg:text-9xl leading-[0.85] tracking-tighter text-construct-black mb-8 w-full">
              <div class="ml-4 sm:ml-8 md:ml-16">ARCHYX</div>
              <div class="text-right mt-2 sm:mt-4 pr-4 sm:pr-8 md:pr-0">
                <span class="text-white">BLOG</span>
              </div>
            </h1>
          </Motion>

          <Motion
            :initial="{ opacity: 0, y: 50 }"
            :enter="{ opacity: 1, y: 0 }"
            :transition="{ duration: 0.8, ease: 'easeOut', delay: 0.4 }"
          >
            <p class="text-xl md:text-2xl font-medium max-w-xl text-construct-black/80 mb-12">
              探索建筑与技术的边界，构建未来的数字空间体验
            </p>
          </Motion>

          <!-- Search Bar -->
          <Motion
            :initial="{ opacity: 0, y: 50 }"
            :enter="{ opacity: 1, y: 0 }"
            :transition="{ duration: 0.8, ease: 'easeOut', delay: 0.6 }"
          >
            <div class="flex flex-col sm:flex-row items-stretch max-w-2xl group relative">
              <div class="relative flex-1">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-construct-black/40" />
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="搜索文章..."
                  class="w-full bg-white border-4 border-construct-black px-12 py-4 text-sm font-bold tracking-widest focus:outline-none transition-all focus:bg-construct-paper relative z-20"
                />
              </div>
              <a
                href="/posts"
                class="bg-construct-black text-white px-8 py-4 sm:py-0 flex items-center justify-center font-display tracking-widest text-sm font-bold hover:bg-construct-red cursor-pointer transition-colors active:translate-x-1 active:translate-y-1 whitespace-nowrap relative z-20"
              >
                搜索
              </a>
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
          <a href="/posts" class="group flex flex-col items-end gap-2">
            <div class="flex items-center gap-4 text-xs font-bold tracking-widest uppercase hover:text-construct-red transition-colors">
              <span>访问全部归档</span>
              <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-2" />
            </div>
          </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
          <Motion
            v-for="(post, idx) in featuredPosts"
            :key="post.id"
            :initial="{ opacity: 0, y: 30 }"
            :enter="{ opacity: 1, y: 0 }"
            :hovered="{ scale: 1.02, x: 8, y: -8 }"
            :transition="{ duration: 0.3, delay: idx * 0.1 }"
          >
            <div class="p-6 sm:p-10 border-4 border-white h-full flex flex-col justify-between bg-transparent hover:bg-construct-red transition-colors duration-300">
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
              :hovered="{ scale: 1.05 }"
              :transition="{ duration: 0.2 }"
            >
              <div class="p-6 border-2 border-white/20 hover:border-construct-red hover:bg-construct-red transition-all duration-300 flex items-center justify-center min-h-[140px] group">
                <span class="font-display text-xl sm:text-2xl font-black tracking-widest text-center">
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
          订阅更新
        </h2>
        <p class="text-lg font-medium opacity-60 mb-12 max-w-xl mx-auto">
          订阅我们的Newsletter，获取最新文章和技术见解
        </p>
        <Motion
          :hovered="{ scale: 1.02 }"
          :transition="{ duration: 0.2 }"
        >
          <form class="max-w-2xl mx-auto relative flex flex-col sm:flex-row items-stretch shadow-[-8px_8px_0px_#000] bg-white focus-within:ring-4 focus-within:ring-construct-black transition-all duration-300">
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
        </Motion>
      </div>
    </section>

    <!-- Footer -->
    <Footer v-model="isFooterVisible" />

    </div><!-- End of ml-16 wrapper -->

  </div>
</template>

<style scoped>
.font-display {
  font-family: system-ui, -apple-system, sans-serif;
}

/* Marquee Animation */
.marquee-container {
  overflow: hidden;
  width: 100%;
}

.marquee-content {
  display: flex;
  animation: marquee 20s linear infinite;
}

.marquee-text {
  display: inline-block;
  font-family: system-ui, -apple-system, sans-serif;
  font-size: 0.75rem;
  font-weight: bold;
  letter-spacing: 0.3em;
  text-transform: uppercase;
  white-space: nowrap;
  padding: 0 2rem;
}

@keyframes marquee {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
}

.construct-diagonal {
  clip-path: polygon(15% 0, 100% 0, 100% 100%, 0% 100%);
}

.shadow-\[-4px_4px_0px_\#000\] {
  box-shadow: -4px 4px 0px #000;
}

.shadow-\[8px_8px_0px_\#000\] {
  box-shadow: 8px 8px 0px #000;
}
</style>
