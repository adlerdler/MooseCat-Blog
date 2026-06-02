<script setup>
/**
 * SocialLinks.vue - 社交链接管理页面
 * 
 * 功能说明：
 * - 管理社交链接（添加、编辑、删除、排序）
 * - 管理分类导航链接
 * - 管理数据链接
 */
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import {
  LayoutPanelLeft,
  Plus,
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
import { useToast } from '../../composables/useToast';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import Pagination from '../../components/admin/Pagination.vue';

const props = defineProps({
  socialLinks: { type: Array, default: () => [] },
  navLinks: { type: Object, default: () => ({}) },
});

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success, error } = useToast();

const activeTab = ref('social');

const socialLinksList = ref([...props.socialLinks]);
const categoryLinksList = ref(props.navLinks.categories || []);
const dataLinksList = ref(props.navLinks.data || []);

const currentPage = ref(1);
const itemsPerPage = ref(6);

const showDeleteConfirm = ref(false);
const deletingItemId = ref(null);
const deletingItemType = ref('');

const showSaveConfirm = ref(false);

const showAddLinkModal = ref(false);
const newLinkPlatform = ref('');
const newLinkUrl = ref('');
const newLinkIcon = ref('');
const detectedPlatform = ref('');

const showEditModal = ref(false);
const editingLink = ref(null);
const editForm = ref({});

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
  return [...socialLinksList.value].sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0));
});

const filteredNavLinks = computed(() => {
  const list = activeTab.value === 'categories' ? categoryLinksList.value : dataLinksList.value;
  return [...list].sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0));
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
  newLinkIcon.value = '';
  detectedPlatform.value = '';
};

const cancelAddLink = () => {
  showAddLinkModal.value = false;
  newLinkPlatform.value = '';
  newLinkUrl.value = '';
  newLinkIcon.value = '';
  detectedPlatform.value = '';
};

const confirmAddLink = () => {
  if (activeTab.value === 'social') {
    if (!newLinkPlatform.value.trim()) {
      error('错误', '请输入平台名称');
      return;
    }
    if (!newLinkUrl.value.trim()) {
      error('错误', '请输入链接地址');
      return;
    }
    
    const platform = newLinkPlatform.value.trim().toLowerCase().replace(/\s+/g, '_');
    const exists = socialLinksList.value.some(l => l.platform === platform);
    if (exists) {
      error('错误', '该平台已存在');
      return;
    }
    
    router.post('/admin/social-links', {
      platform,
      url: newLinkUrl.value.trim(),
      icon: newLinkIcon.value || platform,
      sort_order: socialLinksList.value.length + 1,
      is_active: true,
    }, {
      onSuccess: () => {
        cancelAddLink();
        success('成功', '链接已创建');
        window.location.reload();
      },
      onError: () => {
        error('错误', '创建链接失败');
      }
    });
  } else {
    if (!newLinkPlatform.value.trim()) {
      error('错误', '请输入名称');
      return;
    }
    if (!newLinkUrl.value.trim()) {
      error('错误', '请输入链接地址');
      return;
    }
    
    const list = activeTab.value === 'categories' ? categoryLinksList.value : dataLinksList.value;
    router.post('/admin/social-links', {
      type: activeTab.value,
      label: newLinkPlatform.value.trim(),
      url: newLinkUrl.value.trim(),
      sort_order: list.length + 1,
      is_active: true,
    }, {
      onSuccess: () => {
        cancelAddLink();
        success('成功', '链接已创建');
        window.location.reload();
      },
      onError: () => {
        error('错误', '创建链接失败');
      }
    });
  }
};

const handleDelete = (id, type) => {
  deletingItemId.value = id;
  deletingItemType.value = type;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingItemType.value === 'social') {
    router.delete(`/admin/social-links/${deletingItemId.value}`, {
      onSuccess: () => {
        showDeleteConfirm.value = false;
        deletingItemId.value = null;
        deletingItemType.value = '';
        success('成功', '链接已删除');
        window.location.reload();
      },
      onError: () => {
        error('错误', '删除链接失败');
      }
    });
  } else {
    router.delete(`/admin/social-links/${deletingItemId.value}`, {
      data: { type: deletingItemType.value },
      onSuccess: () => {
        showDeleteConfirm.value = false;
        deletingItemId.value = null;
        deletingItemType.value = '';
        success('成功', '链接已删除');
        window.location.reload();
      },
      onError: () => {
        error('错误', '删除链接失败');
      }
    });
  }
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
  showSaveConfirm.value = true;
};

const confirmSave = () => {
  showSaveConfirm.value = false;
  
  const data = {
    social_links: socialLinksList.value,
    nav_links: {
      categories: categoryLinksList.value,
      data: dataLinksList.value,
    }
  };
  
  router.put('/admin/social-links', data, {
    preserveScroll: true,
  });
};

const openEditModal = (link) => {
  editingLink.value = link;
  editForm.value = {
    label: link.label || link.label_default || '',
    url: link.url || '',
    route: link.route || '',
    platform: link.platform || '',
    icon: link.icon || '',
    is_active: link.is_active !== undefined ? link.is_active : true
  };
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  editingLink.value = null;
  editForm.value = {};
};

const saveEdit = () => {
  const link = editingLink.value;
  if (!link) return;
  if (!editForm.value.label.trim()) {
    alert('请输入名称');
    return;
  }
  if (!editForm.value.url.trim() && !editForm.value.route?.trim()) {
    alert('请输入链接地址或路由');
    return;
  }
  
  const data = {
    label: editForm.value.label,
    url: editForm.value.url,
    route: editForm.value.route,
    is_active: editForm.value.is_active,
  };
  
  if (activeTab.value === 'social') {
    data.platform = editForm.value.platform;
    data.icon = editForm.value.icon;
  }
  
  router.put(`/admin/social-links/${link.id}`, data, {
    onSuccess: () => {
      closeEditModal();
    }
  });
};

const tabs = [
  { key: 'social', label: '社交链接', icon: LinkIcon },
  { key: 'categories', label: '分类导航', icon: Navigation },
  { key: 'data', label: '数据链接', icon: Database }
];

const getPlatformGradient = (platform) => {
  const dark = isDarkMode.value;
  const gradientMap = {
    'github': dark ? 'bg-gradient-to-br from-gray-700 to-gray-800 text-gray-200' : 'bg-gradient-to-br from-gray-100 to-gray-200 text-gray-800',
    'twitter': dark ? 'bg-gradient-to-br from-sky-900 to-sky-800 text-sky-200' : 'bg-gradient-to-br from-sky-50 to-sky-100 text-sky-600',
    'linkedin': dark ? 'bg-gradient-to-br from-blue-900 to-blue-800 text-blue-200' : 'bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600',
    'website': dark ? 'bg-gradient-to-br from-emerald-900 to-emerald-800 text-emerald-200' : 'bg-gradient-to-br from-emerald-50 to-emerald-100 text-emerald-600',
    'instagram': dark ? 'bg-gradient-to-br from-fuchsia-900 to-pink-800 text-pink-200' : 'bg-gradient-to-br from-fuchsia-50 to-pink-100 text-fuchsia-600',
    'youtube': dark ? 'bg-gradient-to-br from-red-900 to-red-800 text-red-200' : 'bg-gradient-to-br from-red-50 to-red-100 text-red-600',
    'bilibili': dark ? 'bg-gradient-to-br from-cyan-900 to-cyan-800 text-cyan-200' : 'bg-gradient-to-br from-cyan-50 to-cyan-100 text-cyan-600',
    'tiktok': dark ? 'bg-gradient-to-br from-purple-900 to-purple-800 text-purple-200' : 'bg-gradient-to-br from-purple-50 to-purple-100 text-purple-600',
    'facebook': dark ? 'bg-gradient-to-br from-indigo-900 to-indigo-800 text-indigo-200' : 'bg-gradient-to-br from-indigo-50 to-indigo-100 text-indigo-600',
    'weibo': dark ? 'bg-gradient-to-br from-orange-900 to-orange-800 text-orange-200' : 'bg-gradient-to-br from-orange-50 to-orange-100 text-orange-600',
    'zhihu': dark ? 'bg-gradient-to-br from-blue-900 to-blue-800 text-blue-200' : 'bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600',
    'dribbble': dark ? 'bg-gradient-to-br from-pink-900 to-pink-800 text-pink-200' : 'bg-gradient-to-br from-pink-50 to-pink-100 text-pink-600',
    'behance': dark ? 'bg-gradient-to-br from-blue-900 to-blue-800 text-blue-200' : 'bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600'
  };
  return gradientMap[platform] || (dark ? 'bg-gradient-to-br from-gray-700 to-gray-800 text-gray-300' : 'bg-gradient-to-br from-gray-100 to-gray-200 text-gray-600');
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
          @click="openAddLinkModal"
          class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
        >
          <Plus size="18" :style="{ color: '#ffffff' }" />
          {{ t('admin_add') }}
        </button>
      </div>
    </div>

    <!-- Tabs -->
    <div :class="['flex gap-2 mb-8', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        @click="activeTab = tab.key; currentPage = 1"
        :class="[
          'relative flex items-center gap-2.5 px-5 py-2.5 font-bold text-sm rounded-xl transition-all duration-300',
          activeTab === tab.key
            ? isDarkMode
              ? 'bg-construct-red/15 text-construct-red shadow-sm'
              : 'bg-construct-red text-white shadow-md shadow-construct-red/25 scale-105'
            : isDarkMode
              ? 'text-gray-500 hover:text-gray-200 hover:bg-gray-800'
              : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'
        ]"
      >
        <component :is="tab.icon" size="16" />
        {{ tab.label }}
      </button>
    </div>

    <!-- Links Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="link in paginatedLinks"
        :key="link.id"
        :class="[
          'group border transition-all duration-300 rounded-xl overflow-hidden',
          isDarkMode
            ? 'bg-gray-800/80 border-gray-700/50 hover:border-construct-red/50 hover:shadow-xl hover:shadow-construct-red/5 hover:-translate-y-1.5'
            : 'bg-white border-gray-200 hover:border-construct-red/30 hover:shadow-xl hover:shadow-construct-red/10 hover:-translate-y-1.5'
        ]"
      >
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <div :class="[
              'w-12 h-12 rounded-xl flex items-center justify-center shadow-md transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg',
              activeTab === 'social' ? getPlatformGradient(link.platform) : (isDarkMode ? 'bg-gradient-to-br from-amber-900 to-amber-800 text-amber-200' : 'bg-gradient-to-br from-amber-50 to-amber-100 text-amber-600')
            ]">
              <component
                v-if="activeTab === 'social'"
                :is="getLinkIcon(link.platform)"
                size="24"
              />
              <component v-else-if="activeTab === 'categories'" :is="Navigation" size="24" />
              <component v-else :is="Database" size="24" />
            </div>
            <div class="flex items-center gap-1">
              <button
                @click="moveUp(link, activeTab === 'social' ? socialLinksList : activeTab === 'categories' ? categoryLinksList : dataLinksList)"
                :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']"
              >
                <ChevronUp size="14" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
              </button>
              <button
                @click="moveDown(link, activeTab === 'social' ? socialLinksList : activeTab === 'categories' ? categoryLinksList : dataLinksList)"
                :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']"
              >
                <ChevronDown size="14" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
              </button>
            </div>
          </div>

          <h4 :class="['font-bold text-lg mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">
            {{ link.label || link.label_default }}
          </h4>
          <p :class="['text-xs font-bold uppercase tracking-wider mb-1', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
            {{ activeTab === 'social' ? link.platform : (link.route || link.url || '') }}
          </p>
          <p :class="['text-sm truncate', isDarkMode ? 'text-gray-500' : 'text-gray-600']">
            {{ link.url || link.route }}
          </p>

          <div :class="['flex items-center justify-between mt-4 pt-3', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <span :class="['text-xs font-mono font-bold', isDarkMode ? 'text-gray-600' : 'text-gray-400']">
              #{{ link.sort_order }}
            </span>
            <div class="flex items-center gap-2">
              <button
                @click="openEditModal(link)"
                :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-construct-red' : 'text-gray-400 hover:bg-gray-100 hover:text-construct-red']"
              >
                <Edit3 size="14" />
              </button>
              <button
                @click="handleDelete(link.id, activeTab)"
                :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-red-400' : 'text-gray-400 hover:bg-gray-100 hover:text-red-500']"
              >
                <Trash2 size="14" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <Pagination
      v-model:current-page="currentPage"
      :total-items="filteredList.length"
      :items-per-page="itemsPerPage"
    />

    <!-- Add Link Modal -->
    <Transition name="modal">
      <div v-if="showAddLinkModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="cancelAddLink">
        <div :class="['w-full max-w-md mx-4 rounded-lg shadow-xl border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
          <div :class="['flex justify-between items-center p-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_add') }} 社交链接</h3>
            <button @click="cancelAddLink" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
              <X :size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>

          <div class="p-6 space-y-4">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">平台名称</label>
              <input
                v-model="newLinkPlatform"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
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
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
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
            <button @click="cancelAddLink" :class="['flex-1 px-4 py-3 font-bold text-sm border rounded-lg transition-colors', isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 hover:bg-gray-100']">
              {{ t('admin_cancel') }}
            </button>
            <button @click="confirmAddLink" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-construct-red text-white font-bold text-sm rounded-lg hover:bg-red-700 transition-colors">
              <Plus :size="16" :style="{ color: '#ffffff' }" />
              {{ t('admin_add') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Edit Link Modal -->
    <Transition name="modal">
      <div v-if="showEditModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="closeEditModal">
        <div :class="['w-full max-w-md mx-4 rounded-lg shadow-xl border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
          <div :class="['flex justify-between items-center p-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">编辑链接</h3>
            <button @click="closeEditModal" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
              <X :size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>

          <div class="p-6 space-y-4">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">名称</label>
              <input
                v-model="editForm.label"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="链接显示名称"
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">链接地址</label>
              <input
                v-model="editForm.url"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="https://..."
              />
            </div>

            <div v-if="activeTab !== 'social'">
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">路由 (可选)</label>
              <input
                v-model="editForm.route"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="如：/blog"
              />
            </div>

            <div class="flex items-center gap-2">
              <input
                type="checkbox"
                id="edit-link-active"
                v-model="editForm.is_active"
                class="w-4 h-4 rounded"
              />
              <label for="edit-link-active" :class="['text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">启用</label>
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

    <!-- Delete Confirmation -->
    <ConfirmDialog
      v-model:visible="showDeleteConfirm"
      type="delete"
      :title="t('admin_confirm_delete')"
      :content="t('admin_delete_warning')"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />

    <!-- Save Confirmation Dialog -->
    <ConfirmDialog
      v-model:visible="showSaveConfirm"
      :title="t('admin_save_confirm_title')"
      :content="t('admin_save_confirm_content')"
      :confirm-text="t('admin_save')"
      :cancel-text="t('admin_cancel')"
      confirm-variant="primary"
      @confirm="confirmSave"
      @cancel="showSaveConfirm = false"
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
