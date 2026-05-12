<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { MotionComponent as Motion } from '@vueuse/motion';
import { useTheme } from '../composables/useTheme';
import { VIDEOS } from '../data/videos';
import SidebarMenu from '../components/SidebarMenu.vue';
import Footer from '../components/Footer.vue';

const { initTheme } = useTheme();
const isFooterVisible = ref(true);

onMounted(() => {
  initTheme();
  
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
            VIDEOS //
          </h1>
          <p class="text-sm font-medium opacity-60 uppercase tracking-widest">
            A COLLECTION OF DIGITAL BROADCASTS AND VIDEO LOGS.
          </p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <Motion
            v-for="(video, idx) in VIDEOS"
            :key="video.id"
            :initial="{ opacity: 0, y: 20 }"
            :visible="{ opacity: 1, y: 0 }"
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
                  {{ video.date }} // {{ video.platform.toUpperCase() }}
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
  font-family: system-ui, -apple-system, sans-serif;
}
</style>