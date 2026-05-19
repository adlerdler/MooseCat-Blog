<script setup>
/**
 * Resources.vue - 资源下载页
 * 
 * 功能说明：
 * - 展示可供下载的设计资源列表
 * - 支持按分类筛选资源
 * - 点击资源卡片可查看详情并下载
 * 
 * 页面特色：
 * - 分类标签横向滚动筛选
 * - 资源卡片网格布局
 * - 模态框展示资源详情
 */
import { ref, computed, onMounted, TransitionGroup } from 'vue';
import { Motion, AnimatePresence } from 'motion-v';
import { useTheme } from '../../composables/useTheme';
import { resourcesData } from '../../data/resources';
import { categories } from '../../data/categories';
import { getCategoryNameById } from '../../utils/categoryUtils';
import { Download, HardDrive } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const { initAccentTheme } = useTheme();
const isFooterVisible = ref(true);
const selectedResource = ref(null);
const selectedCategory = ref('ALL');

const driveLogos = {
  'Local': { bg: 'bg-gray-600', text: 'LOCAL', color: 'text-white' },
  'Google Drive': { bg: 'bg-[#4285F4]', text: 'GDRIVE', color: 'text-white' },
  'Dropbox': { bg: 'bg-[#0061FF]', text: 'DROPBOX', color: 'text-white' },
  'OneDrive': { bg: 'bg-[#0078D4]', text: 'ONEDRIVE', color: 'text-white' },
  '115': { bg: 'bg-[#00B787]', text: '115', color: 'text-white' },
  'Baidu': { bg: 'bg-[#2932E1]', text: 'BAIDU', color: 'text-white' },
  'AliCloud': { bg: 'bg-[#FF6A00]', text: 'ALI', color: 'text-white' },
  'Quark': { bg: 'bg-[#00B9F1]', text: 'QUARK', color: 'text-white' },
  'Thunder': { bg: 'bg-[#C85223]', text: 'XUNLEI', color: 'text-white' },
  'Vimeo': { bg: 'bg-[#1AB7EA]', text: 'VIMEO', color: 'text-white' },
  'YouTube': { bg: 'bg-[#FF0000]', text: 'YT', color: 'text-white' }
};

const getDriveLogo = (type) => {
  return driveLogos[type] || { bg: 'bg-gray-500', text: type.slice(0, 6).toUpperCase(), color: 'text-white' };
};

const categoryList = computed(() => {
  // 只显示有资源的分类
  const usedCategoryIds = [...new Set(resourcesData.map(r => r.category_id))];
  const usedCategories = categories.filter(c => usedCategoryIds.includes(c.id));
  return ['ALL', ...usedCategories.map(c => c.name)];
});
const filteredResources = computed(() => {
  if (selectedCategory.value === 'ALL') {
    return resourcesData;
  }
  // 根据分类名称找到对应的分类ID，然后筛选资源
  const selectedCategoryData = categories.find(c => c.name === selectedCategory.value);
  if (!selectedCategoryData) return [];
  return resourcesData.filter(r => r.category_id === selectedCategoryData.id);
});
const getResourceCategoryName = (categoryId) => {
  return getCategoryNameById(categories, categoryId) || '';
};
onMounted(() => {
  initAccentTheme();
  // 前台页面不受后台主题设置影响，移除 light class
  document.documentElement.classList.remove('light');
  const saved = sessionStorage.getItem('footer_visible');
  if (saved !== null) {
    isFooterVisible.value = saved === 'true';
  }
});
const selectResource = (resource) => {
  // 创建资源副本并添加 categoryName 字段供模态框使用
  selectedResource.value = {
    ...resource,
    category: getResourceCategoryName(resource.category_id)
  };
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
            {{ t('resources_title') }} //
          </h1>
          <p class="text-sm font-medium opacity-60 uppercase tracking-widest mb-12 max-w-xl">
            {{ t('resources_desc') }}
          </p>

          <!-- Categories -->
          <div class="flex flex-wrap gap-4 mb-4">
            <button
              v-for="cat in categoryList"
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
              :animate="{ opacity: 1, y: 0 }"
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
                    {{ resource.file_size }}
                  </div>
                </div>
              </div>
              <div class="flex justify-between items-start">
                <div>
                  <div class="text-[10px] font-bold tracking-widest opacity-40 uppercase mb-2">[{{ getResourceCategoryName(resource.category_id) }}]</div>
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
  font-family: 'Space Grotesk', system-ui, sans-serif;
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