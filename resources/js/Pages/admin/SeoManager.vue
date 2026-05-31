<script setup>
/**
 * SeoManager.vue - 页面 SEO 管理
 * 
 * 功能说明：
 * - 管理各页面的 SEO 配置（title、description、keywords）
 * - 支持编辑、启用/禁用页面 SEO
 * - 数据保存到 page_seo.js
 */
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import {
  Search,
  Edit3,
  Save,
  X,
  Eye,
  EyeOff
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import SearchFilterModal from '../../components/admin/SearchFilterModal.vue';

const props = defineProps({
  pageSeo: {
    type: Array,
    default: () => []
  }
});

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const showEditModal = ref(false);
const editingPage = ref(null);
const editForm = ref({});

const getAllPageSeo = () => [...props.pageSeo];

const updatePageSeo = (id, data) => {
  const index = props.pageSeo.findIndex(p => p.id === id);
  if (index === -1) return null;
  return { ...props.pageSeo[index], ...data, updatedAt: new Date().toISOString() };
};

const pageSeoListRef = ref(getAllPageSeo().map(page => ({ ...page })));

watch(() => props.pageSeo, (newPageSeo) => {
  pageSeoListRef.value = newPageSeo.map(page => ({ ...page }));
}, { deep: true });

const filteredPages = computed(() => {
  if (!searchQuery.value) return pageSeoListRef.value;
  const query = searchQuery.value.toLowerCase();
  return pageSeoListRef.value.filter(
    p => p.page_key.toLowerCase().includes(query) ||
         p.title.toLowerCase().includes(query) ||
         p.description.toLowerCase().includes(query)
  );
});

const openEditModal = (page) => {
  editingPage.value = page;
  editForm.value = {
    title: page.title,
    description: page.description,
    keywords: page.keywords,
    ogImage: page.og_image
  };
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  editingPage.value = null;
  editForm.value = {};
};

const saveEdit = () => {
  const page = editingPage.value;
  if (!page) return;
  
  router.put(route('admin.seo.page-seo.update', page.id), {
    page_key: page.page_key,
    title: editForm.value.title,
    description: editForm.value.description,
    keywords: editForm.value.keywords,
    og_image: editForm.value.ogImage,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      closeEditModal();
    },
    onError: (errors) => {
      console.error('保存失败:', errors);
    },
  });
};

const getRouteColor = (pageKey) => {
  const colorMap = {
    'home': isDarkMode ? 'from-amber-900/50 to-orange-900/50 text-amber-300' : 'from-amber-50 to-orange-50 text-amber-700',
    'blog': isDarkMode ? 'from-blue-900/50 to-indigo-900/50 text-blue-300' : 'from-blue-50 to-indigo-50 text-blue-700',
    'projects': isDarkMode ? 'from-purple-900/50 to-pink-900/50 text-purple-300' : 'from-purple-50 to-pink-50 text-purple-700',
    'videos': isDarkMode ? 'from-red-900/50 to-rose-900/50 text-red-300' : 'from-red-50 to-rose-50 text-red-700',
    'resources': isDarkMode ? 'from-green-900/50 to-emerald-900/50 text-green-300' : 'from-green-50 to-emerald-50 text-green-700',
    'author': isDarkMode ? 'from-cyan-900/50 to-teal-900/50 text-cyan-300' : 'from-cyan-50 to-teal-50 text-cyan-700'
  };
  return colorMap[pageKey] || (isDarkMode ? 'from-gray-700 to-gray-800 text-gray-300' : 'from-gray-100 to-gray-200 text-gray-600');
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Search class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_seo_manager') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">管理各页面 SEO 配置</p>
        </div>
        <SearchFilterModal
          v-model:search-query="searchQuery"
          :search-placeholder="'搜索页面...'"
        />
      </div>
    </div>

    <!-- Page SEO List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="page in filteredPages"
        :key="page.id"
        :class="[
          'border transition-all duration-500 rounded-lg overflow-hidden',
          isDarkMode
            ? 'bg-gray-800 border-gray-700 hover:border-construct-red/50 hover:shadow-lg'
            : 'bg-white border-gray-200 hover:border-construct-red/30 hover:shadow-lg'
        ]"
      >
        <div class="p-6">
          <!-- Header -->
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-2">
                <span :class="['text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider bg-gradient-to-r', getRouteColor(page.page_key)]">
                  {{ page.page_key }}
                </span>
              </div>
            </div>
          </div>

          <!-- Content (always visible) -->
          <h4 class="font-bold text-base mb-2 truncate" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ page.title }}</h4>
          <p class="text-sm mb-3 line-clamp-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ page.description }}</p>
          <p class="text-xs truncate" :class="isDarkMode ? 'text-gray-600' : 'text-gray-400'">
            <span class="font-bold">关键词：</span>{{ page.keywords }}
          </p>
          <p class="text-xs mt-2" :class="isDarkMode ? 'text-gray-600' : 'text-gray-400'">
            <span class="font-bold">OG图片：</span>{{ page.og_image || '无' }}
          </p>

          <!-- Actions -->
          <div :class="['flex items-center gap-2 mt-4 pt-4 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <button
              @click="openEditModal(page)"
              :class="[
                'w-full flex items-center justify-center gap-1 px-3 py-2 rounded-lg text-xs font-bold transition-colors',
                isDarkMode ? 'bg-construct-red/10 text-construct-red hover:bg-construct-red/20' : 'bg-construct-red/10 text-construct-red hover:bg-construct-red/20'
              ]"
            >
              <Edit3 size="14" />
              编辑
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <Transition name="modal">
      <div v-if="showEditModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="closeEditModal">
        <div :class="['w-full max-w-lg mx-4 rounded-lg shadow-xl border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
          <div :class="['flex justify-between items-center p-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <div class="flex items-center gap-3">
              <span :class="['text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider bg-gradient-to-r', getRouteColor(editingPage?.page_key)]">
                {{ editingPage?.page_key }}
              </span>
              <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">编辑 SEO 配置</h3>
            </div>
            <button @click="closeEditModal" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
              <X :size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>

          <div class="p-6 space-y-5">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">标题 (Title)</label>
              <input
                v-model="editForm.title"
                :class="['w-full px-3 py-2 border rounded-lg text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">描述 (Description)</label>
              <textarea
                v-model="editForm.description"
                rows="3"
                :class="['w-full px-3 py-2 border rounded-lg text-sm resize-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">关键词 (Keywords)</label>
              <input
                v-model="editForm.keywords"
                :class="['w-full px-3 py-2 border rounded-lg text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="关键词以逗号分隔"
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">OG 图片 URL</label>
              <input
                v-model="editForm.ogImage"
                :class="['w-full px-3 py-2 border rounded-lg text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="https://example.com/og-image.jpg"
              />
            </div>
          </div>

          <div :class="['flex gap-3 p-6 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <button @click="closeEditModal" :class="['flex-1 px-4 py-3 font-bold text-sm border rounded-lg transition-colors', isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 hover:bg-gray-100']">
              取消
            </button>
            <button @click="saveEdit" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-construct-red text-white font-bold text-sm rounded-lg hover:bg-red-700 transition-colors">
              <Save :size="16" :style="{ color: '#ffffff' }" />
              保存
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>
