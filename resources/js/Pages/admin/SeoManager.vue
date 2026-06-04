<script setup>
/**
 * SeoManager.vue - 页面 SEO 管理
 * 
 * 功能说明：
 * - 管理各页面的 SEO 配置（title、description、keywords）
 * - 支持编辑、启用/禁用页面 SEO
 * - 数据保存到 page_seo.js
 */
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import {
  Search,
  Edit3,
  Save,
  X
} from 'lucide-vue-next';
import { Motion } from 'motion-v';
import { useTheme } from '../../composables/useTheme';
import { useToast } from '../../composables/useToast';

const props = defineProps({
  pageSeo: {
    type: Array,
    default: () => []
  }
});

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success: toastSuccess, error: toastError } = useToast();

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
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      closeEditModal();
      toastSuccess(t('admin_seo_updated') || 'SEO updated');
    },
    onError: (errors) => {
      toastError(t('admin_update_failed') || 'Update failed');
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
      </div>
    </div>

    <!-- Page SEO List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <Motion
        v-for="(page, idx) in pageSeoListRef"
        :key="page.id"
        :initial="{ opacity: 0, y: 20 }"
        :animate="{ opacity: 1, y: 0 }"
        :transition="{ delay: idx * 0.05, duration: 0.35 }"
      >
        <div
          :class="[
            'group border transition-all duration-300 rounded-xl overflow-hidden hover:-translate-y-1.5 hover:shadow-xl',
            isDarkMode
              ? 'bg-gray-800/80 border-gray-700/50 hover:border-construct-red/50 hover:shadow-construct-red/5'
              : 'bg-white border-gray-200 hover:border-construct-red/30 hover:shadow-construct-red/10'
          ]"
        >
          <div class="p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
              <div :class="[
                'px-3 py-1.5 rounded-xl flex items-center gap-2 bg-gradient-to-r transition-all duration-300 group-hover:scale-105',
                getRouteColor(page.page_key)
              ]">
                <span class="text-xs font-black uppercase tracking-wider">{{ page.page_key }}</span>
              </div>
            </div>

            <!-- Content -->
            <h4 class="font-bold text-base mb-2 truncate" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ page.title }}</h4>
            <p class="text-sm mb-3 line-clamp-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ page.description }}</p>
            <p class="text-xs truncate" :class="isDarkMode ? 'text-gray-600' : 'text-gray-400'">
              <span class="font-bold">关键词：</span>{{ page.keywords }}
            </p>
            <p class="text-xs mt-2" :class="isDarkMode ? 'text-gray-600' : 'text-gray-400'">
              <span class="font-bold">OG图片：</span>{{ page.og_image || '无' }}
            </p>

            <!-- Actions (no border-t) -->
            <div class="flex items-center justify-between mt-4">
              <span class="text-xs font-mono font-bold opacity-40" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">
                SEO · {{ page.page_key }}
              </span>
              <div class="flex items-center gap-2">
                <button
                  @click="openEditModal(page)"
                  :class="[
                    'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all',
                    isDarkMode
                      ? 'bg-construct-red/20 text-construct-red hover:bg-construct-red/30'
                      : 'bg-construct-red/10 text-construct-red hover:bg-construct-red/20'
                  ]"
                  :title="'编辑 ' + page.title"
                >
                  <Edit3 size="15" />
                  编辑
                </button>
              </div>
            </div>
          </div>
        </div>
      </Motion>
    </div>

    <!-- Edit Modal -->
    <Transition name="modal">
      <div v-if="showEditModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="closeEditModal">
        <div :class="['w-full max-w-lg mx-4 rounded-2xl shadow-2xl ring-1 overflow-hidden', isDarkMode ? 'bg-gray-800 ring-gray-700' : 'bg-white ring-gray-200/80']">
          <!-- Header -->
          <div :class="['flex items-center justify-between p-6 pb-4', isDarkMode ? 'text-white' : 'text-gray-900']">
            <div class="flex items-center gap-3">
              <div :class="[
                'w-10 h-10 rounded-xl flex items-center justify-center shadow-md bg-gradient-to-br',
                getRouteColor(editingPage?.page_key)
              ]">
                <Search size="18" />
              </div>
              <div>
                <h3 :class="['font-display text-xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">编辑 SEO 配置</h3>
                <span :class="['text-xs font-black px-2 py-0.5 rounded-md uppercase tracking-wider bg-gradient-to-r', getRouteColor(editingPage?.page_key)]">
                  {{ editingPage?.page_key }}
                </span>
              </div>
            </div>
            <button @click="closeEditModal" :class="['p-2 rounded-xl transition-colors', isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500']">
              <X size="20" />
            </button>
          </div>

          <!-- Body -->
          <div class="px-6 pb-6 space-y-5">
            <div>
              <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">标题 (Title)</label>
              <input
                v-model="editForm.title"
                :class="[
                  'w-full px-4 py-3 border rounded-xl text-sm focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
                :placeholder="'Home — ' + (editingPage?.title || 'My Site')"
              />
            </div>

            <div>
              <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">描述 (Description)</label>
              <textarea
                v-model="editForm.description"
                rows="3"
                :class="[
                  'w-full px-4 py-3 border rounded-xl text-sm resize-none focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
                placeholder="页面的 meta description，建议 150 字以内"
              />
              <p :class="['text-[10px] mt-1.5 font-medium', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                搜索引擎摘要中显示的描述文字，留空则使用站点全局描述
              </p>
            </div>

            <div>
              <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">关键词 (Keywords)</label>
              <input
                v-model="editForm.keywords"
                :class="[
                  'w-full px-4 py-3 border rounded-xl text-sm focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
                placeholder="关键词以逗号分隔，如：博客, 技术, 前端"
              />
            </div>

            <div>
              <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">OG 图片 URL</label>
              <input
                v-model="editForm.ogImage"
                :class="[
                  'w-full px-4 py-3 border rounded-xl text-sm focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
                placeholder="https://example.com/og-image.jpg"
              />
              <p :class="['text-[10px] mt-1.5 font-medium', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                社交分享时展示的预览图，留空使用站点默认 OG 图片
              </p>
            </div>
          </div>

          <!-- Footer -->
          <div :class="['flex items-center gap-3 px-6 pb-6', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            <button
              @click="closeEditModal"
              :class="[
                'flex-1 px-4 py-3 font-bold text-sm tracking-wider uppercase border rounded-xl transition-colors',
                isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-600 hover:bg-gray-100'
              ]"
            >
              取消
            </button>
            <button
              @click="saveEdit"
              class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-construct-red text-white font-bold text-sm tracking-wider uppercase rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-construct-red/20"
            >
              <Save size="16" />
              保存
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-active > div,
.modal-leave-active > div {
  transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.3s ease;
}
.modal-enter-from > div {
  transform: scale(0.95) translateY(10px);
  opacity: 0;
}
.modal-leave-to > div {
  transform: scale(0.95) translateY(10px);
  opacity: 0;
}
</style>
