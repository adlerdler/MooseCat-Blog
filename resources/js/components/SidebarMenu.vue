<script setup>
/**
 * SidebarMenu.vue - 侧边栏菜单组件
 *
 * 功能说明：
 * - 网站主导航侧边栏
 * - 响应式设计，移动端可折叠
 * - 集成设置面板和搜索功能
 *
 * 功能模块：
 * - 主导航链接（首页/博客/视频/项目/资源/关于）
 * - 设置面板（主题/语言）
 * - 搜索覆盖层
 * - Footer 显示控制
 *
 * 使用示例：
 * <SidebarMenu 
 *   v-model:is-footer-visible="isFooterVisible" 
 *   :posts="posts"
 *   :videos="videos"
 *   :projects="projects"
 *   :resources="resources"
 * />
 */
import { computed, watch, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, Search, User, LogIn, ChevronDown, ChevronUp, X, ArrowRight, Globe, FileText } from 'lucide-vue-next';
import SettingsPanel from './SettingsPanel.vue';
import SearchOverlay from './SearchOverlay.vue';
import { useI18n } from 'vue-i18n';
import { useSiteConfig } from '../composables/useSiteConfig';
import { useMenuItems } from '../composables/useMenuItems';
import { useUIStore } from '../stores/ui';

const props = defineProps({
  isFooterVisible: {
    type: Boolean,
    default: true
  },
  posts: {
    type: Array,
    default: () => []
  },
  videos: {
    type: Array,
    default: () => []
  },
  projects: {
    type: Array,
    default: () => []
  },
  resources: {
    type: Array,
    default: () => []
  },
  menus: {
    type: Array,
    default: () => []
  },
  siteConfig: {
    type: Object,
    default: () => ({})
  },
  footerConfig: {
    type: Object,
    default: () => ({})
  },
  themes: {
    type: Array,
    default: () => []
  }
});

const { frontMenuItems } = useMenuItems({ menus: props.menus });
const { getSiteName, getSiteCopyright, isAuthorBioVisible, isSearchVisible } = useSiteConfig({ config: props.siteConfig });

const siteName = computed(() => getSiteName());
const siteCopyright = computed(() => getSiteCopyright());

// 从菜单数据中获取作者页路径（如 /author/{slug}）
const authorPath = computed(() => {
  const authorMenu = props.menus?.find(m => m.component_name === 'Author' && m.type === 'front');
  return authorMenu?.path || '/author/adler-decht';
});

const emit = defineEmits(['toggle-search', 'toggle-menu', 'update:is-footer-visible']);

const { t, locale } = useI18n();
const ui = useUIStore();

// 初始化并保持 footerVisible 与父组件同步
if (props.isFooterVisible !== undefined) {
  ui.footerVisible = props.isFooterVisible;
}
watch(() => props.isFooterVisible, (val) => {
  if (val !== undefined) ui.footerVisible = val;
});

// 当菜单打开时禁止 body 滚动
watch(() => ui.isMenuOpen, (newValue) => {
  if (newValue) {
    document.body.classList.add('overflow-hidden');
  } else {
    document.body.classList.remove('overflow-hidden');
  }
});

onUnmounted(() => {
  document.body.classList.remove('overflow-hidden');
});

const toggleFooter = () => {
  ui.toggleFooter();
  emit('update:is-footer-visible', ui.footerVisible);
};

const toggleMenu = () => {
  ui.toggleMenu();
  emit('toggle-menu', ui.isMenuOpen);
};

const closeMenu = () => {
  ui.closeMenu();
  emit('toggle-menu', false);
};

const toggleSettings = () => {
  ui.toggleSettings();
};

const toggleSearch = () => {
  ui.toggleSearch();
};

const closeSearch = () => {
  ui.closeSearch();
};

const menuLinks = computed(() => {
  return frontMenuItems.map(item => ({
    name: t(item.label_key),
    path: item.path
  }));
});

const showSidebar = computed(() => true);

// 认证状态
const authUser = computed(() => usePage().props.auth?.user ?? null);
const isAuthenticated = computed(() => authUser.value !== null);
const userAvatar = computed(() => authUser.value?.avatar ?? null);
const userInitial = computed(() => authUser.value?.name?.charAt(0)?.toUpperCase() ?? '?');

// 注册开关（禁止注册时隐藏登录/个人中心图标）
const isRegistrationEnabled = computed(() => props.siteConfig?.registration !== false);
</script>

<template>
  <!-- Navigation Rail (PC/Tablet) -->
  <nav v-if="showSidebar" class="fixed top-0 left-0 h-screen w-16 bg-black text-white flex flex-col items-center justify-between py-4 z-50 hidden md:flex">
    <!-- Top: Branding -->
    <div class="flex flex-col items-center gap-8">
      <Link href="/">
        <img
          v-if="siteConfig?.logo"
          :src="siteConfig.logo"
          :alt="siteName"
          class="w-8 h-8 object-contain"
        />
        <span v-else class="font-display font-black text-2xl tracking-tighter hover:text-accent transition-colors">
          {{ siteName.substring(0, 2).toUpperCase() }}
        </span>
      </Link>
    </div>

    <!-- Middle: Systematic Tools -->
    <div class="flex flex-col gap-10 items-center">
      <Link href="/blog">
        <FileText class="w-6 h-6 cursor-pointer transition-colors hover:text-accent" />
      </Link>
      <Search
        v-if="isSearchVisible()"
        class="w-6 h-6 cursor-pointer transition-colors hover:text-accent"
        :class="{ 'text-accent': ui.isSearchOpen }"
        @click="toggleSearch"
      />
      <Link v-if="isAuthorBioVisible()" :href="authorPath">
        <User class="w-6 h-6 cursor-pointer hover:text-accent transition-colors" />
      </Link>
      <!-- Auth: 未登录 → 登录图标指向登录页；已登录 → 头像指向个人中心 -->
      <template v-if="isRegistrationEnabled">
        <Link v-if="!isAuthenticated" href="/login" title="Login">
          <LogIn class="w-6 h-6 cursor-pointer hover:text-accent transition-colors" />
        </Link>
        <Link v-else :href="'/profile/' + authUser?.slug" title="Profile">
          <img
            v-if="userAvatar"
            :src="userAvatar"
            :alt="authUser?.name"
            class="w-7 h-7 rounded-full object-cover border border-accent/30 hover:border-accent transition-colors"
          />
          <div
            v-else
            class="w-7 h-7 rounded-full bg-accent/10 border border-accent/30 flex items-center justify-center hover:border-accent transition-colors"
          >
            <span class="text-xs font-bold text-accent">{{ userInitial }}</span>
          </div>
        </Link>
      </template>
    </div>

    <!-- Bottom: Menu Toggle -->
    <div class="pb-4 flex flex-col items-center gap-6">
      <button
        @click="toggleFooter"
        class="hover:text-accent transition-colors"
        title="Toggle Footer"
      >
        <ChevronDown v-if="ui.footerVisible" class="w-6 h-6" />
        <ChevronUp v-else class="w-6 h-6" />
      </button>
      <Menu
        class="w-6 h-6 cursor-pointer transition-colors hover:text-accent"
        @click="toggleMenu"
      />
    </div>
  </nav>

  <!-- Mobile Header -->
  <header v-if="showSidebar" class="fixed top-0 left-0 w-full h-16 bg-black text-white px-4 flex items-center justify-between z-50 md:hidden">
    <Link href="/" class="font-display font-black text-xl tracking-tighter">
      {{ siteName }}
    </Link>
    <div class="flex items-center gap-3">
      <Search v-if="isSearchVisible()" class="w-5 h-5 cursor-pointer" @click="toggleSearch" />
      <Menu class="w-5 h-5 cursor-pointer" @click="toggleMenu" />
    </div>
  </header>

  <!-- Fullscreen Menu Overlay -->
  <Teleport to="body">
    <Transition name="slide">
      <div
        v-if="ui.isMenuOpen"
        class="fixed inset-0 bg-accent z-[60] flex flex-col md:flex-row"
      >
        <!-- Mobile Header -->
        <div class="flex md:hidden items-center justify-between p-4 bg-black shrink-0">
          <span class="font-display text-xl text-white tracking-widest">
            {{ siteName }}
          </span>
          <div class="flex items-center gap-2">
            <button @click="toggleSettings" class="p-2 text-white">
              <Globe class="w-5 h-5" />
            </button>
            <button @click="closeMenu" class="p-2 text-white">
              <X class="w-6 h-6" />
            </button>
          </div>
        </div>

        <!-- Menu Content - Scrollable Area (60%) -->
        <div class="flex-1 overflow-y-auto overflow-x-hidden md:min-h-screen md:w-[60%]">
          <div class="flex flex-col justify-center min-h-full md:h-screen md:px-8 lg:px-16 xl:px-24 py-8 px-4">

            <!-- Navigation Links -->
            <nav class="space-y-2 sm:space-y-3 md:space-y-4">
              <Link
                v-for="(link, idx) in menuLinks"
                :key="link.name"
                :href="link.path"
                @click="closeMenu"
                class="group flex items-center gap-2 sm:gap-4 md:gap-6 py-2 sm:py-3 md:py-4"
              >
                <span class="font-display text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl text-white tracking-tighter transition-all group-hover:italic group-hover:translate-x-2 sm:group-hover:translate-x-4 leading-none">
                  {{ link.name }}
                </span>
                <ArrowRight class="w-4 h-4 sm:w-6 sm:h-6 md:w-8 md:h-8 text-white opacity-0 group-hover:opacity-100 transition-all -translate-x-4 sm:-translate-x-8 group-hover:translate-x-0" />
              </Link>
            </nav>

            <!-- Mobile Footer Info -->
            <div class="mt-auto pt-8 md:hidden">
              <p class="text-white font-display text-xs tracking-[0.3em] uppercase">
                {{ siteName }}
              </p>
              <p class="text-white/40 text-[10px] uppercase tracking-[0.2em] mt-2">
                {{ siteCopyright }}
              </p>
            </div>
          </div>
        </div>

        <!-- Desktop Right Panel - Settings & Decorative (40%) -->
        <div class="hidden md:flex md:w-[40%] bg-black flex-col justify-between p-6 lg:p-8 xl:p-12 shrink-0">
          <div class="flex justify-end">
            <button @click="closeMenu" class="p-2 text-white hover:text-accent transition-colors">
              <X class="w-8 h-8 lg:w-10 lg:h-10" />
            </button>
          </div>

          <div class="space-y-6">
            <SettingsPanel :themes="themes" />
          </div>
        </div>

        <!-- Mobile Settings Panel (Overlay) -->
        <Transition name="fade">
          <div
            v-if="ui.isSettingsOpen"
            class="md:hidden fixed inset-0 bg-black/95 z-[70] flex items-center justify-center p-8"
          >
            <div class="w-full max-w-md">
              <div class="flex justify-between items-center mb-8">
                <span class="font-display text-xl text-white tracking-widest">
                  {{ siteName }}
                </span>
                <button @click="toggleSettings" class="p-2 text-white">
                  <X class="w-6 h-6" />
                </button>
              </div>
              <SettingsPanel :is-mobile="true" :themes="themes" />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>

    <!-- Search Overlay -->
    <SearchOverlay v-if="isSearchVisible()" :is-open="ui.isSearchOpen" @close="closeSearch" />
  </Teleport>
</template>

<style lang="scss" scoped>
// 字体变量
$font-display: 'Space Grotesk', system-ui, sans-serif;
$transition-ease: ease;
$transition-duration: 0.3s;

.font-display {
  font-family: $font-display;
}

// Slide 过渡动画
.slide {
  &-enter-active,
  &-leave-active {
    transition: transform $transition-duration $transition-ease;
  }

  &-enter-from,
  &-leave-to {
    transform: translateX(-100%);
  }
}

// Fade 过渡动画
.fade {
  &-enter-active,
  &-leave-active {
    transition: opacity 0.2s $transition-ease;
  }

  &-enter-from,
  &-leave-to {
    opacity: 0;
  }
}

// 现代高度支持
@supports (height: 100dvh) {
  .min-h-full {
    min-height: 100dvh;
  }
}
</style>