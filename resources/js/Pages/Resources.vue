<script setup>import { ref, computed, onMounted, TransitionGroup } from 'vue';
import { MotionComponent as Motion } from '@vueuse/motion';
import { useTheme } from '../composables/useTheme';
import { RESOURCES } from '../data/resources';
import SidebarMenu from '../components/SidebarMenu.vue';
import Footer from '../components/Footer.vue';
import ResourceModal from '../components/ResourceModal.vue';
import { Download } from 'lucide-vue-next';
const { initTheme } = useTheme();
const isFooterVisible = ref(true);
const selectedResource = ref(null);
const selectedCategory = ref('ALL');
const categories = computed(() => {
 return ['ALL', ...new Set(RESOURCES.map(r => r.category))];
});
const filteredResources = computed(() => {
 return RESOURCES.filter(r => selectedCategory.value === 'ALL' || r.category === selectedCategory.value);
});
onMounted(() => {
 initTheme();
 const saved = sessionStorage.getItem('footer_visible');
 if (saved !== null) {
 isFooterVisible.value = saved === 'true';
 }
});
const selectResource = (resource) => {
 selectedResource.value = resource;
};
const closeModal = () => {
 selectedResource.value = null;
};
</script>

<template>
  <div class="min-h-screen bg-construct-paper selection:bg-construct-red selection:text-white">
    <!-- Sidebar Menu -->
    <SidebarMenu v-model:is-footer-visible="isFooterVisible" />

    <!-- Main Content with left margin for sidebar -->
    <div class="ml-16">
      <div class="container flex-1 mx-auto px-4 md:px-8 py-16">
        <header class="mb-16">
          <h1 class="font-display text-6xl md:text-6xl lg:text-8xl tracking-tighter leading-none mb-6">
            RESOURCES //
          </h1>
          <p class="text-sm font-medium opacity-60 uppercase tracking-widest mb-12 max-w-xl">
            A COLLECTION OF DESIGN ASSETS, CODE SNIPPETS AND STRUCTURAL BLUEPRINTS.
          </p>

          <!-- Categories -->
          <div class="flex flex-wrap gap-4 mb-4">
            <button
              v-for="cat in categories"
              :key="cat"
              @click="selectedCategory = cat"
              :class="[
                'px-4 py-2 border-2 text-xs font-bold tracking-widest uppercase transition-all',
                selectedCategory === cat
                  ? 'border-construct-red bg-construct-red text-white'
                  : 'border-construct-black/20 text-construct-black hover:border-construct-black hover:bg-construct-black hover:text-white'
              ]"
            >
              {{ cat }}
            </button>
          </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <TransitionGroup name="resource-list">
            <Motion
              v-for="(resource, idx) in filteredResources"
              :key="resource.id"
              :initial="{ opacity: 0, y: 20 }"
              :visible="{ opacity: 1, y: 0 }"
              :transition="{ duration: 0.3 }"
              @click="selectResource(resource)"
              class="group cursor-pointer flex flex-col"
            >
              <div class="aspect-[4/3] bg-construct-black mb-4 relative overflow-hidden">
                <img
                  :src="resource.image"
                  :alt="resource.title"
                  class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-80 group-hover:opacity-100"
                />
                <div class="absolute inset-x-0 bottom-0 p-4 flex justify-between items-end">
                  <div class="bg-construct-black text-white px-3 py-1 text-[10px] font-bold tracking-widest uppercase">
                    {{ resource.format }}
                  </div>
                  <div class="bg-white text-construct-black px-3 py-1 text-[10px] font-bold tracking-widest uppercase">
                    {{ resource.fileSize }}
                  </div>
                </div>
              </div>
              <div class="flex justify-between items-start">
                <div>
                  <div class="text-[10px] font-bold tracking-widest opacity-40 uppercase mb-2">[{{ resource.category }}]</div>
                  <h3 class="font-display text-2xl tracking-tight mb-2 group-hover:text-construct-red transition-colors">
                    {{ resource.title }}
                  </h3>
                  <div class="flex gap-2">
                    <span
                      v-for="d in resource.drives"
                      :key="d.type"
                      class="text-[10px] font-bold tracking-widest opacity-40 uppercase"
                    >
                      {{ d.type }}
                    </span>
                  </div>
                </div>
                <Download class="w-5 h-5 opacity-0 group-hover:opacity-100 transition-opacity text-construct-red mt-6" />
              </div>
            </Motion>
          </TransitionGroup>
        </div>
      </div>

      <!-- Footer -->
      <Footer v-model="isFooterVisible" />
    </div>

    <!-- Resource Modal -->
    <ResourceModal
      v-if="selectedResource"
      :resource="selectedResource"
      @close="closeModal"
    />
  </div>
</template>

<style lang="scss" scoped>
.font-display {
  font-family: system-ui, -apple-system, sans-serif;
}

.resource-list {
  &-enter-active,
  &-leave-active {
    transition: all 0.3s ease;
  }

  &-enter-from {
    opacity: 0;
    transform: translateY(20px);
  }

  &-leave-to {
    opacity: 0;
    transform: scale(0.95);
  }

  &-move {
    transition: transform 0.3s ease;
  }
}
</style>