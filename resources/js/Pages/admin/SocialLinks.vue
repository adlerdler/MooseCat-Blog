<script setup>
/**
 * FooterManager.vue - 页脚管理页面
 * 
 * 功能说明：
 * - 管理前台页脚的所有可配置内容
 * - 社交链接管理（添加、编辑、删除、排序）
 * - 导航链接管理（分类、数据链接）
 * - 品牌信息管理
 * - 数据保存到 footer_config.js
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  LayoutPanelLeft,
  Plus,
  Search,
  Edit3,
  Trash2,
  ChevronUp,
  ChevronDown,
  Github,
  Twitter,
  Linkedin,
  Globe,
  Palette,
  Youtube,
  Facebook,
  Video,
  MessageCircle,
  Link as LinkIcon,
  Navigation,
  Database,
  Save,
  X
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { footerConfig, getFooterSocialLinks, getFooterNavLinks } from '../../data/footer_config';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import AdminPagination from '../../components/admin/AdminPagination.vue';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const activeTab = ref('social');

const socialLinksList = ref(getFooterSocialLinks().map(link => ({ ...link })));
const categoryLinksList = ref(getFooterNavLinks('categories').map(link => ({ ...link })));
const dataLinksList = ref(getFooterNavLinks('data').map(link => ({ ...link })));

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(6);

const showDeleteConfirm = ref(false);
const deletingItemId = ref(null);
const deletingItemType = ref('');

const showAddLinkModal = ref(false);
const newLinkPlatform = ref('');
const newLinkUrl = ref('');
const detectedPlatform = ref('');

const detectPlatformFromUrl = (url) => {
  if (!url) return '';
  const urlLower = url.toLowerCase();
  if (urlLower.includes('github.com')) return 'github';
  if (urlLower.includes('twitter.com') || urlLower.includes('x.com')) return 'twitter';
  if (urlLower.includes('linkedin.com')) return 'linkedin';
  if (urlLower.includes('instagram.com')) return 'instagram';
  if (urlLower.includes('youtube.com') || urlLower.includes('youtu.be')) return 'youtube';
  if (urlLower.includes('bilibili.com')) return 'bilibili';
  if (urlLower.includes('tiktok.com')) return 'tiktok';
  if (urlLower.includes('facebook.com')) return 'facebook';
  if (urlLower.includes('weibo.com')) return 'weibo';
  if (urlLower.includes('zhihu.com')) return 'zhihu';
  if (urlLower.includes('dribbble.com')) return 'dribbble';
  if (urlLower.includes('behance.net')) return 'behance';
  return '';
};

const handleUrlInput = () => {
  detectedPlatform.value = detectPlatformFromUrl(newLinkUrl.value);
  if (detectedPlatform.value && !newLinkPlatform.value) {
    newLinkPlatform.value = detectedPlatform.value;
  }
};

const getLinkIcon = (platform) => {
  const iconMap = {
    'github': Github,
    'twitter': Twitter,
    'linkedin': Linkedin,
    'website': Globe,
    'email': MessageCircle,
    'instagram': Palette,
    'youtube': Youtube,
    'bilibili': Video,
    'tiktok': Video,
    'facebook': Facebook,
    'weibo': MessageCircle,
    'zhihu': MessageCircle,
    'dribbble': Palette,
    'behance': Palette
  };
  return iconMap[platform] || LinkIcon;
};

const filteredSocialLinks = computed(() => {
  let result = [...socialLinksList.value];
  if (searchQuery.value && activeTab.value === 'social') {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(link =>
      link.label.toLowerCase().includes(query) ||
      link.platform.toLowerCase().includes(query) ||
      link.url.toLowerCase().includes(query)
    );
  }
  return result.sort((a, b) => a.sort_order - b.sort_order);
});

const filteredNavLinks = computed(() => {
  const list = activeTab.value === 'categories' ? categoryLinksList.value : dataLinksList.value;
  let result = [...list];
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(link =>
      (link.label_key || '').toLowerCase().includes(query) ||
      (link.label_default || '').toLowerCase().includes(query) ||
      (link.route || link.url || '').toLowerCase().includes(query)
    );
  }
  return result.sort((a, b) => a.sort_order - b.sort_order);
});

const filteredList = computed(() => {
  return activeTab.value === 'social' ? filteredSocialLinks.value : filteredNavLinks.value;
});

const totalPages = computed(() => Math.ceil(filteredList.value.length / itemsPerPage.value));

const paginatedLinks = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredList.value.slice(start, start + itemsPerPage.value);
});

const openAddLinkModal = () => {
  showAddLinkModal.value = true;
  newLinkPlatform.value = '';
  newLinkUrl.value = '';
  detectedPlatform.value = '';
};

const cancelAddLink = () => {
  showAddLinkModal.value = false;
  newLinkPlatform.value = '';
  newLinkUrl.value = '';
  detectedPlatform.value = '';
};

const confirmAddLink = () => {
  if (!newLinkPlatform.value.trim()) {
    alert('请输入平台名称');
    return;
  }
  if (!newLinkUrl.value.trim()) {
    alert('请输入链接地址');
    return;
  }
  
  const platform = newLinkPlatform.value.trim().toLowerCase().replace(/\s+/g, '_');
  const exists = socialLinksList.value.some(l => l.platform === platform);
  if (exists) {
    alert('该平台已存在');
    return;
  }
  
  const newId = Math.max(...socialLinksList.value.map(l => l.id), 0) + 1;
  socialLinksList.value.push({
    id: newId,
    platform,
    icon_name: platform.charAt(0).toUpperCase() + platform.slice(1),
    label: platform.toUpperCase(),
    url: newLinkUrl.value.trim(),
    sort_order: socialLinksList.value.length + 1,
    is_active: true,
    style: {
      bg: 'bg-construct-black',
      text: 'text-white',
      hover_bg: 'hover:bg-construct-red',
      hover_text: 'hover:text-white',
      border: 'border-black',
      hover_border: 'hover:border-construct-red'
    }
  });
  
  cancelAddLink();
};

const handleDelete = (id, type) => {
  deletingItemId.value = id;
  deletingItemType.value = type;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingItemType.value === 'social') {
    socialLinksList.value = socialLinksList.value.filter(l => l.id !== deletingItemId.value);
  } else if (deletingItemType.value === 'categories') {
    categoryLinksList.value = categoryLinksList.value.filter(l => l.id !== deletingItemId.value);
  } else if (deletingItemType.value === 'data') {
    dataLinksList.value = dataLinksList.value.filter(l => l.id !== deletingItemId.value);
  }
  showDeleteConfirm.value = false;
  deletingItemId.value = null;
  deletingItemType.value = '';
};

const moveUp = (link, list) => {
  const index = list.findIndex(l => l.id === link.id);
  if (index > 0) {
    [list[index], list[index - 1]] = [list[index - 1], list[index]];
    list[index].sort_order = index + 1;
    list[index - 1].sort_order = index;
  }
};

const moveDown = (link, list) => {
  const index = list.findIndex(l => l.id === link.id);
  if (index < list.length - 1) {
    [list[index], list[index + 1]] = [list[index + 1], list[index]];
    list[index].sort_order = index + 1;
    list[index + 1].sort_order = index + 2;
  }
};

const handleSaveAll = () => {
  footerConfig.social_links = socialLinksList.value;
  footerConfig.nav_links.categories = categoryLinksList.value;
  footerConfig.nav_links.data = dataLinksList.value;
  alert('页脚配置已保存');
};

const tabs = [
  { key: 'social', label: '社交链接', icon: LinkIcon },
  { key: 'categories', label: '分类导航', icon: Navigation },
  { key: 'data', label: '数据链接', icon: Database }
];

const getPlatformGradient = (platform) => {
  const gradientMap = {
    'github': isDarkMode ? 'bg-gradient-to-br from-gray-700 to-gray-800 text-white' : 'bg-gradient-to-br from-gray-50 to-gray-100 text-gray-900',
    'twitter': isDarkMode ? 'bg-gradient-to-br from-blue-900 to-blue-800 text-blue-300' : 'bg-gradient-to-br from-blue-50 to-blue-100 text-blue-700',
    'linkedin': isDarkMode ? 'bg-gradient-to-br from-blue-900 to-blue-800 text-blue-300' : 'bg-gradient-to-br from-blue-50 to-blue-100 text-blue-700',
    'website': isDarkMode ? 'bg-gradient-to-br from-green-900 to-green-800 text-green-300' : 'bg-gradient-to-br from-green-50 to-green-100 text-green-700',
    'instagram': isDarkMode ? 'bg-gradient-to-br from-pink-900 to-pink-800 text-pink-300' : 'bg-gradient-to-br from-pink-50 to-pink-100 text-pink-700',
    'youtube': isDarkMode ? 'bg-gradient-to-br from-red-900 to-red-800 text-red-300' : 'bg-gradient-to-br from-red-50 to-red-100 text-red-700',
    'bilibili': isDarkMode ? 'bg-gradient-to-br from-cyan-900 to-cyan-800 text-cyan-300' : 'bg-gradient-to-br from-cyan-50 to-cyan-100 text-cyan-700',
    'tiktok': isDarkMode ? 'bg-gradient-to-br from-purple-900 to-purple-800 text-purple-300' : 'bg-gradient-to-br from-purple-50 to-purple-100 text-purple-700',
    'facebook': isDarkMode ? 'bg-gradient-to-br from-blue-900 to-blue-800 text-blue-300' : 'bg-gradient-to-br from-blue-50 to-blue-100 text-blue-700',
    'weibo': isDarkMode ? 'bg-gradient-to-br from-red-900 to-red-800 text-red-300' : 'bg-gradient-to-br from-red-50 to-red-100 text-red-700',
    'zhihu': isDarkMode ? 'bg-gradient-to-br from-blue-900 to-blue-800 text-blue-300' : 'bg-gradient-to-br from-blue-50 to-blue-100 text-blue-700',
    'dribbble': isDarkMode ? 'bg-gradient-to-br from-pink-900 to-pink-800 text-pink-300' : 'bg-gradient-to-br from-pink-50 to-pink-100 text-pink-700',
    'behance': isDarkMode ? 'bg-gradient-to-br from-blue-900 to-blue-800 text-blue-300' : 'bg-gradient-to-br from-blue-50 to-blue-100 text-blue-700'
  };
  return gradientMap[platform] || (isDarkMode ? 'bg-gradient-to-br from-gray-700 to-gray-800 text-gray-300' : 'bg-gradient-to-br from-gray-50 to-gray-100 text-gray-600');
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <LayoutPanelLeft class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_footer_manager') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_footer_manager_subtitle') }}</p>
        </div>
        <button
          @click="handleSaveAll"
          class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
        >
          <Save size="18" class="!text-white" />
          {{ t('admin_save') }}
        </button>
      </div>
    </div>

    <!-- Tabs -->
    <div :class="['flex gap-2 mb-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        @click="activeTab = tab.key; currentPage = 1; searchQuery = ''"
        :class="[
          'flex items-center gap-2 px-6 py-3 font-bold text-sm uppercase tracking-wider transition-all border-b-2',
          activeTab === tab.key
            ? 'border-construct-red text-construct-red'
            : isDarkMode 
              ? 'border-transparent text-gray-400 hover:text-white' 
              : 'border-transparent text-gray-500 hover:text-gray-900'
        ]"
      >
        <component :is="tab.icon" size="16" />
        {{ tab.label }}
      </button>
    </div>

    <!-- Search and Add -->
    <div class="mb-6 flex items-center justify-between gap-4">
      <div :class="['flex items-center gap-3 px-4 py-3 border flex-1 max-w-md transition-colors', isDarkMode ? 'bg-gray-800/40 border-gray-700/50' : 'bg-white/80 border-gray-200/80']">
        <Search size="18" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" />
        <input
          v-model="searchQuery"
          :class="['flex-1 bg-transparent focus:outline-none text-sm', isDarkMode ? 'text-white placeholder-gray-500' : 'text-gray-900 placeholder-gray-400']"
          :placeholder="t('admin_search_placeholder')"
        />
      </div>
      <button
        v-if="activeTab === 'social'"
        @click="openAddLinkModal"
        class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
      >
        <Plus size="18" class="!text-white" />
        {{ t('admin_add') }}
      </button>
    </div>

    <!-- Links Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
      <div
        v-for="link in paginatedLinks"
        :key="link.id"
        :class="[
          'group relative border p-6 transition-all duration-500 hover:-translate-y-2',
          isDarkMode 
            ? 'bg-gray-800/40 border-gray-700/50 hover:border-construct-red/50 backdrop-blur-xl shadow-[0_8px_32px_0_rgba(0,0,0,0.3)]' 
            : 'bg-white/80 border-gray-200/80 hover:border-construct-red/30 backdrop-blur-md shadow-[0_8px_32px_0_rgba(31,38,135,0.07)]'
        ]"
      >
        <!-- Card Background Accent -->
        <div class="absolute top-0 right-0 p-8 opacity-0 group-hover:opacity-10 transition-opacity duration-500 pointer-events-none">
          <component 
            v-if="activeTab === 'social'" 
            :is="getLinkIcon(link.platform)" 
            size="80" 
            class="rotate-12" 
          />
          <Navigation v-else size="80" class="rotate-12" />
        </div>

        <div class="flex items-start justify-between mb-6">
          <div :class="[
            'w-14 h-14 rounded-2xl flex items-center justify-center transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-lg',
            getPlatformGradient(link.platform)
          ]">
            <component
              v-if="activeTab === 'social'"
              :is="getLinkIcon(link.platform)"
              size="28"
            />
            <Navigation v-else size="28" />
          </div>
          <div class="flex items-center gap-1">
            <button 
              @click="moveUp(link, activeTab === 'social' ? socialLinksList : activeTab === 'categories' ? categoryLinksList : dataLinksList)" 
              :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']"
            >
              <ChevronUp size="16" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
            <button 
              @click="moveDown(link, activeTab === 'social' ? socialLinksList : activeTab === 'categories' ? categoryLinksList : dataLinksList)" 
              :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']"
            >
              <ChevronDown size="16" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>
        </div>
        
        <div class="mb-4">
          <h3 :class="['font-bold text-lg mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">
            {{ link.label || link.label_default }}
          </h3>
          <p :class="['text-xs uppercase tracking-wider', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
            {{ activeTab === 'social' ? link.platform : (link.route || link.url || '') }}
          </p>
        </div>

        <div :class="['text-sm mb-4 truncate border-t pt-4', isDarkMode ? 'text-gray-500 border-gray-700/50' : 'text-gray-600 border-gray-100']">
          {{ link.url || link.route }}
        </div>

        <div class="flex items-center justify-between">
          <span :class="['text-xs font-mono', isDarkMode ? 'text-gray-600' : 'text-gray-400']">
            #{{ link.sort_order }}
          </span>
          <div class="flex items-center gap-2">
            <button
              :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-construct-red' : 'hover:bg-gray-100 text-gray-400 hover:text-construct-red']"
            >
              <Edit3 size="16" />
            </button>
            <button
              @click="handleDelete(link.id, activeTab)"
              :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-red-400' : 'hover:bg-gray-100 text-gray-400 hover:text-red-500']"
            >
              <Trash2 size="16" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <AdminPagination
      v-model:current-page="currentPage"
      :total-items="filteredList.length"
      :items-per-page="itemsPerPage"
    />

    <!-- Add Link Modal -->
    <Transition name="modal">
      <div v-if="showAddLinkModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="cancelAddLink">
        <div :class="['w-full max-w-md mx-4 shadow-2xl rounded-2xl border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
          <div :class="['flex justify-between items-center p-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">添加社交链接</h3>
            <button @click="cancelAddLink" :class="['p-2 rounded-full transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
              <X :size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>

          <div class="p-6 space-y-4">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">平台名称</label>
              <input 
                v-model="newLinkPlatform" 
                :class="['w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-construct-red/50 focus:border-construct-red transition-all', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-200 text-gray-900']" 
                placeholder="如：instagram, tiktok, bilibili..." 
              />
              <p v-if="detectedPlatform" class="text-xs mt-2 text-green-600 dark:text-green-400">
                ✓ 已自动识别为 {{ detectedPlatform }}
              </p>
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">链接地址</label>
              <input 
                v-model="newLinkUrl" 
                @input="handleUrlInput"
                :class="['w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-construct-red/50 focus:border-construct-red transition-all', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-200 text-gray-900']" 
                placeholder="https://..." 
              />
            </div>

            <div v-if="newLinkPlatform" :class="['p-4 border rounded-lg flex items-center gap-3', isDarkMode ? 'bg-gray-700/50 border-gray-600' : 'bg-gray-50 border-gray-200']">
              <component :is="getLinkIcon(newLinkPlatform)" :size="24" class="text-construct-red" />
              <div>
                <p :class="['text-sm font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ newLinkPlatform }}</p>
                <p :class="['text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">预览图标</p>
              </div>
            </div>
          </div>

          <div :class="['flex gap-3 p-6 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <button @click="cancelAddLink" :class="['flex-1 px-4 py-3 font-bold text-sm border rounded-lg transition-all', isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-200 text-gray-700 hover:bg-gray-50']">
              {{ t('admin_cancel') }}
            </button>
            <button @click="confirmAddLink" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-construct-red text-white font-bold text-sm rounded-lg hover:bg-red-700 transition-all">
              <Plus :size="16" />
              {{ t('admin_add') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Delete Confirmation -->
    <ConfirmDialog
      v-model:visible="showDeleteConfirm"
      :title="t('admin_confirm_delete')"
      :message="t('admin_delete_warning')"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
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

.modal-enter-active .modal-content,
.modal-leave-active .modal-content {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
  transform: scale(0.95);
  opacity: 0;
}
</style>
