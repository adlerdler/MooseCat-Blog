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
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Motion, AnimatePresence } from 'motion-v';
import { useTheme } from '../../composables/useTheme';
import { PROJECTS } from '../../data/projects';

const { initTheme } = useTheme();
const router = useRouter();
const isFooterVisible = ref(true);

onMounted(() => {
  initTheme();

  const saved = sessionStorage.getItem('footer_visible');
  if (saved !== null) {
    isFooterVisible.value = saved === 'true';
  }
});

const goToProject = (projectId) => {
  router.push(`/projects/${projectId}`);
};
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
            PROJECTS //
          </h1>
          <p class="text-sm font-medium opacity-60 uppercase tracking-widest">
            A CATALOGUE OF STRUCTURAL EXPERIMENTS AND DIGITAL INFRASTRUCTURES.
          </p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <Motion
            v-for="(project, idx) in PROJECTS"
            :key="project.id"
            :initial="{ opacity: 0, y: 20 }"
            :animate="{ opacity: 1, y: 0 }"
            :transition="{ delay: idx * 0.1 }"
            @click="goToProject(project.id)"
            class="group cursor-pointer border-4 border-construct-black bg-white hover:bg-construct-red transition-colors p-4"
          >
            <img
              :src="project.image"
              :alt="project.title"
              class="w-full h-64 object-cover mb-4"
            />
            <h3 class="font-display text-2xl tracking-tighter group-hover:text-white">
              {{ project.title }}
            </h3>
            <div class="flex gap-2 mt-4 flex-wrap">
              <span
                v-for="tag in project.tags"
                :key="tag"
                class="text-[10px] font-black tracking-tighter px-2 py-1 bg-construct-black text-white"
              >
                {{ tag }}
              </span>
            </div>
          </Motion>
        </div>
      </div>

      <!-- Footer -->
      <Footer v-model="isFooterVisible" />
    </div>
  </div>
</template>

<style lang="scss" scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}
</style>