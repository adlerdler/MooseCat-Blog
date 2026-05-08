<script setup>
import { ref, computed, watch, onUnmounted } from 'vue';
import { Menu, Search, User, ChevronDown, ChevronUp, X, ArrowRight, Globe } from 'lucide-vue-next';
import SettingsPanel from './SettingsPanel.vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
  isFooterVisible: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['toggle-search', 'toggle-menu', 'update:is-footer-visible']);

const { t, locale } = useI18n();

const isMenuOpen = ref(false);
const isSettingsOpen = ref(false);
const footerVisible = ref(props.isFooterVisible);

watch(() => props.isFooterVisible, (val) => {
  footerVisible.value = val;
});

watch(isMenuOpen, (newValue) => {
  if (newValue) {
    document.body.classList.add('overflow-hidden');
    isSettingsOpen.value = false;
  } else {
    document.body.classList.remove('overflow-hidden');
  }
});

onUnmounted(() => {
  document.body.classList.remove('overflow-hidden');
});

const toggleFooter = () => {
  footerVisible.value = !footerVisible.value;
  emit('update:is-footer-visible', footerVisible.value);
};

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
  emit('toggle-menu', isMenuOpen.value);
};

const closeMenu = () => {
  isMenuOpen.value = false;
  emit('toggle-menu', false);
};

const toggleSettings = () => {
  isSettingsOpen.value = !isSettingsOpen.value;
};

const menuLinks = computed(() => [
  { name: t('nav_home'), path: '/' },
  { name: t('nav_blog'), path: '/blog' },
  { name: t('nav_videos'), path: '/videos' },
  { name: t('nav_projects'), path: '/projects' },
  { name: t('nav_resources'), path: '/resources' },
  { name: t('nav_about'), path: '/author' },
]);

const showSidebar = computed(() => true);
</script>

<template>
  <!-- Navigation Rail (PC/Tablet) -->
  <nav v-if="showSidebar" class="fixed top-0 left-0 h-screen w-16 bg-black text-white flex flex-col items-center justify-between py-4 z-50 hidden md:flex">
    <!-- Top: Branding -->
    <div class="flex flex-col items-center gap-8">
      <a href="/" class="font-display font-black text-2xl tracking-tighter hover:text-accent transition-colors">
        AS
      </a>
    </div>

    <!-- Middle: Systematic Tools -->
    <div class="flex flex-col gap-10 items-center">
      <a href="/blog">
        <Menu class="w-6 h-6 cursor-pointer transition-colors hover:text-accent" />
      </a>
      <Search
        class="w-6 h-6 cursor-pointer transition-colors hover:text-accent"
        @click="emit('toggle-search')"
      />
      <a href="/author">
        <User class="w-6 h-6 cursor-pointer hover:text-accent transition-colors" />
      </a>
    </div>

    <!-- Bottom: Menu Toggle -->
    <div class="pb-4 flex flex-col items-center gap-6">
      <button
        @click="toggleFooter"
        class="hover:text-accent transition-colors"
        title="Toggle Footer"
      >
        <ChevronDown v-if="footerVisible" class="w-6 h-6" />
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
    <a href="/" class="font-display font-black text-xl tracking-tighter">
      AS
    </a>
    <div class="flex items-center gap-3">
      <Search class="w-5 h-5 cursor-pointer" @click="emit('toggle-search')" />
      <Menu class="w-5 h-5 cursor-pointer" @click="toggleMenu" />
    </div>
  </header>

  <!-- Fullscreen Menu Overlay -->
  <Teleport to="body">
    <Transition name="slide">
      <div
        v-if="isMenuOpen"
        class="fixed inset-0 bg-accent z-[60] flex flex-col md:flex-row"
      >
        <!-- Mobile Header -->
        <div class="flex md:hidden items-center justify-between p-4 bg-black shrink-0">
          <span class="font-display text-xl text-white tracking-widest">
            ARCHYX
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
              <a
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
              </a>
            </nav>

            <!-- Mobile Footer Info -->
            <div class="mt-auto pt-8 md:hidden">
              <p class="text-white font-display text-xs tracking-[0.3em] uppercase">
                Archyx v0.1
              </p>
              <p class="text-white/40 text-[10px] uppercase tracking-[0.2em] mt-2">
                © 2026 BLDG_SYSTEM
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
            <SettingsPanel />
          </div>
        </div>

        <!-- Mobile Settings Panel (Overlay) -->
        <Transition name="fade">
          <div
            v-if="isSettingsOpen"
            class="md:hidden fixed inset-0 bg-black/95 z-[70] flex items-center justify-center p-8"
          >
            <div class="w-full max-w-md">
              <div class="flex justify-between items-center mb-8">
                <span class="font-display text-xl text-white tracking-widest">
                  ARCHYX
                </span>
                <button @click="toggleSettings" class="p-2 text-white">
                  <X class="w-6 h-6" />
                </button>
              </div>
              <SettingsPanel :is-mobile="true" />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.font-display {
  font-family: system-ui, -apple-system, sans-serif;
}

.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
  transform: translateX(-100%);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@supports (height: 100dvh) {
  .min-h-full {
    min-height: 100dvh;
  }
}
</style>
