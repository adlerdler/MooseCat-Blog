<script setup>
/**
 * System Settings Page
 * 
 * Features:
 * - Site configuration management
 * - Appearance/theme settings
 * - Notification preferences
 * - SEO settings management
 * - Performance optimization options
 * - Media file management
 */
import {
  ref,
  computed,
  watch,
  useI18n,
  useTheme,
  Settings,
  Globe,
  Palette,
  Bell,
  Search,
  Save,
  RefreshCw,
  Edit3,
  Check,
  X,
  Moon,
  Sun,
  Shield,
  Zap,
  Image,
  Upload,
  ConfirmDialog,
  MediaPickerModal,
  useToast
} from '../../composables/useAdminImports';
import { Plus, Edit2, Trash2 } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  siteConfig: { type: Object, default: () => ({}) },
  seoConfig: { type: Object, default: () => ({}) },
  commentConfig: { type: Object, default: () => ({}) },
  media: { type: Array, default: () => [] },
  themes: { type: Array, default: () => [] },
});

const { t: originalT } = useI18n();
const t = (key, fallback = '') => {
  if (!key) return fallback || '';
  try {
    return originalT(key) || fallback;
  } catch (e) {
    return fallback;
  }
};
const { isDarkMode, toggleTheme } = useTheme({ themesData: props.themes });

const getThemes = () => props.themes || [];
const getDefaultTheme = () => {
  const themes = props.themes || [];
  return themes.find(t => t.is_default) || null;
};
const { success } = useToast();

const tabsConfig = [
  { id: 'site', label_key: 'admin_settings_site', icon_key: 'globe' },
  { id: 'appearance', label_key: 'admin_settings_appearance', icon_key: 'palette' },
  { id: 'notifications', label_key: 'admin_settings_notifications', icon_key: 'bell' },
  { id: 'seo', label_key: 'admin_settings_seo', icon_key: 'search' },
  { id: 'performance', label_key: 'admin_settings_performance', icon_key: 'zap' }
];

const userNotifications = ref({
  email_notifications: true,
  comment_approval_alert: true,
  new_user_alert: true,
  weekly_report: false,
  digest_email: true,
  digest_frequency: 'weekly'
});
const site = ref({
  name: '',
  title: '',
  description: '',
  keywords: '',
  logo: '',
  favicon: '',
  site_url: '',
  copyright: '',
  timezone: 'Asia/Shanghai',
  maintenance: false,
  author_bio: false,
  comments: true,
  registration: true,
  comment_approval: false,
  newsletter: true,
  social_login: false,
  search: true,
  cache: true,
  cache_duration: 3600,
  minification: true,
  lazy_load: true,
  cdn: false,
  cdn_url: '',
  max_upload_size: 10,
  file_types: ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx'],
  ...(props.siteConfig || {}),
});
const seo = ref({ ...(props.seoConfig || {}) });
const activeTab = ref('site');
const isSaving = ref(false);
const showSaveConfirm = ref(false);
const showMediaPicker = ref(false);
const mediaFiles = ref([...(props.media || [])]);

const fileTypesInput = computed({
  get: () => {
    const ft = site.value.file_types;
    return Array.isArray(ft) ? ft.join(', ') : ft;
  },
  set: (value) => {
    site.value.file_types = value.split(',').map(t => t.trim()).filter(t => t);
  }
});

const handleMediaSelect = (file) => {
  if (file.url) {
    const url = new URL(file.url, window.location.origin);
    const relativePath = url.pathname;
    if (pickingField.value === 'logo') {
      site.value.logo = relativePath;
    } else {
      site.value.favicon = relativePath;
    }
  }
  showMediaPicker.value = false;
};

const pickingField = ref('favicon');

const openLogoPicker = () => {
  pickingField.value = 'logo';
  showMediaPicker.value = true;
};

const openFaviconPicker = () => {
  pickingField.value = 'favicon';
  showMediaPicker.value = true;
};

const iconMap = {
  globe: Globe,
  palette: Palette,
  bell: Bell,
  search: Search,
  zap: Zap
};

const tabs = computed(() => {
  return tabsConfig.map(tab => ({
    ...tab,
    label: (tab.label_key ? t(tab.label_key) : tab.label_key) || String(tab.label_key || tab.id),
    icon: iconMap[tab.icon_key]
  }));
});

const availableThemes = computed(() => {
  return getThemes().filter(t => t.is_active);
});

const themeList = ref([]);

watch(() => props.themes, (newThemes) => {
  themeList.value = (newThemes || []).map(t => ({ ...t }));
}, { immediate: true, deep: true });
const showThemeForm = ref(false);
const editingTheme = ref(null);
const themeForm = ref({ id: null, name: '', label: '', color: '#CF202E', sort_order: 1, is_active: true, is_default: false });
const isSavingTheme = ref(false);

const openAddTheme = () => {
  editingTheme.value = null;
  themeForm.value = {
    id: Date.now(),
    name: '',
    label: '',
    color: '#CF202E',
    sort_order: themeList.value.length + 1,
    is_active: true,
    is_default: false
  };
  showThemeForm.value = true;
};

const openEditTheme = (theme) => {
  editingTheme.value = theme;
  themeForm.value = { ...theme };
  showThemeForm.value = true;
};

const saveTheme = () => {
  if (!themeForm.value.name || !themeForm.value.label) {
    return;
  }
  
  isSavingTheme.value = true;
  if (editingTheme.value) {
    router.put(route('admin.settings.themes.update', editingTheme.value.id), themeForm.value, {
      onSuccess: () => {
        isSavingTheme.value = false;
        success(t('admin_update') + ' ' + t('confirm'));
        showThemeForm.value = false;
      },
      onError: (errors) => {
        isSavingTheme.value = false;
        console.error('更新失败:', errors);
      }
    });
  } else {
    router.post(route('admin.settings.themes.store'), themeForm.value, {
      onSuccess: () => {
        isSavingTheme.value = false;
        success(t('admin_create') + ' ' + t('confirm'));
        showThemeForm.value = false;
      },
      onError: (errors) => {
        isSavingTheme.value = false;
        console.error('创建失败:', errors);
      }
    });
  }
};

const deleteTheme = (theme) => {
  if (confirm(`确定要删除主题 "${theme.label}" 吗？`)) {
    router.delete(route('admin.settings.themes.destroy', theme.id), {
      onSuccess: () => {
        success(t('admin_delete') + ' ' + t('confirm'));
      },
      onError: (errors) => {
        console.error('删除失败:', errors);
      }
    });
  }
};

const cancelThemeForm = () => {
  showThemeForm.value = false;
};

const saveSettings = () => {
  showSaveConfirm.value = true;
};

const confirmSave = () => {
  showSaveConfirm.value = false;
  isSaving.value = true;
  
  router.put(route('admin.settings.update'), {
    name: site.value.name,
    description: site.value.description,
    site_url: site.value.site_url,
    copyright: site.value.copyright,
    logo: site.value.logo,
    favicon: site.value.favicon,
    timezone: site.value.timezone,
    maintenance: site.value.maintenance,
    author_bio: site.value.author_bio,
    comments: site.value.comments,
    registration: site.value.registration,
    comment_approval: site.value.comment_approval,
    newsletter: site.value.newsletter,
    social_login: site.value.social_login,
    search: site.value.search,
    cache: site.value.cache,
    cache_duration: site.value.cache_duration,
    minification: site.value.minification,
    lazy_load: site.value.lazy_load,
    cdn: site.value.cdn,
    cdn_url: site.value.cdn_url,
    max_upload_size: site.value.max_upload_size,
    file_types: site.value.file_types,
    // SEO fields
    meta_title: seo.value.meta_title,
    meta_description: seo.value.meta_description,
    meta_keywords: seo.value.meta_keywords,
    google_analytics: seo.value.google_analytics,
    baidu_analytics: seo.value.baidu_analytics,
    canonical_url: seo.value.canonical_url,
    og_image: seo.value.og_image,
    og_type: seo.value.og_type,
    twitter_card: seo.value.twitter_card,
    sitemap: seo.value.sitemap,
    robots: seo.value.robots,
    llm_txt: seo.value.llm_txt,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      isSaving.value = false;
      success(t('admin_save') + ' ' + t('confirm'));
    },
    onError: (errors) => {
      isSaving.value = false;
      console.error('保存失败:', errors);
    },
  });
};

const resetSettings = () => {
  console.log('Resetting settings');
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <Settings class="text-construct-red" size="32" />
          <div>
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_settings') }}</h2>
            <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_settings_subtitle') }}</p>
          </div>
        </div>
        <div class="flex items-center gap-4">
          <button
            @click="saveSettings"
            :disabled="isSaving"
            class="flex items-center gap-2 px-8 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider transition-colors rounded shadow-sm disabled:opacity-50"
          >
            <Save size="18" :style="{ color: '#ffffff' }" /> {{ isSaving ? t('admin_save') + '...' : t('admin_save') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-2 mb-8 overflow-x-auto pb-2">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="activeTab = tab.id"
        :class="[
          'flex items-center gap-2 px-6 py-3 font-bold tracking-wider transition-colors whitespace-nowrap',
          activeTab === tab.id ? 'bg-construct-red text-white' : isDarkMode ? 'bg-gray-800 text-gray-400 hover:bg-gray-700' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'
        ]"
      >
        <component :is="tab.icon" size="18" :style="activeTab === tab.id ? { color: '#ffffff' } : {}" />
        {{ tab.label }}
      </button>
    </div>

    <!-- Settings Content -->
    <div :class="['border p-8', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <fieldset class="border-none p-0 m-0">
        <!-- Site Settings -->
        <div v-if="activeTab === 'site'">
          <h3 class="font-display text-2xl tracking-tighter mb-6 flex items-center gap-3">
            <Globe size="24" class="text-construct-red" />
            {{ t('admin_settings_site') }}
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_name') }}</label>
              <input
                v-model="site.name"
                type="text"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_url') }}</label>
              <input
                v-model="site.site_url"
                type="url"
                :placeholder="t('admin_site_url_placeholder')"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_description') }}</label>
              <input
                v-model="site.description"
                type="text"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_logo') }}</label>
              <div class="flex items-start gap-4">
                <div 
                  @click="openLogoPicker"
                  :class="[
                    'w-16 h-16 border-2 border-dashed rounded-xl flex items-center justify-center flex-shrink-0 transition-all cursor-pointer hover:border-construct-red',
                    isDarkMode ? 'border-gray-600 bg-gray-700 hover:bg-gray-600' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'
                  ]"
                >
                  <img
                    v-if="site.logo"
                    :src="site.logo"
                    alt="Site Logo"
                    class="w-12 h-12 object-contain"
                  />
                  <Image v-else size="24" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" />
                </div>
                <div class="flex-1">
                  <input
                    v-model="site.logo"
                    type="text"
                    :placeholder="t('admin_site_logo_placeholder')"
                    :class="[
                      'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all mb-2',
                      isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                    ]"
                  />
                  <p :class="['text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ t('admin_site_logo_hint') }}</p>
                </div>
              </div>
            </div>
            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_icon') }}</label>
              <div class="flex items-start gap-4">
                <div 
                  @click="openFaviconPicker"
                  :class="[
                    'w-16 h-16 border-2 border-dashed rounded-xl flex items-center justify-center flex-shrink-0 transition-all cursor-pointer hover:border-construct-red',
                    isDarkMode ? 'border-gray-600 bg-gray-700 hover:bg-gray-600' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'
                  ]"
                >
                  <img
                    v-if="site.favicon"
                    :src="site.favicon"
                    alt="Site Icon"
                    class="w-10 h-10 object-contain"
                  />
                  <Image v-else size="24" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" />
                </div>
                <div class="flex-1">
                  <input
                    v-model="site.favicon"
                    type="text"
                    :placeholder="t('admin_site_icon_placeholder')"
                    :class="[
                      'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all mb-2',
                      isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                    ]"
                  />
                  <p :class="['text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ t('admin_site_icon_hint') }}</p>
                </div>
              </div>
            </div>
            <div class="md:col-span-2">
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_copyright') }}</label>
              <input
                v-model="site.copyright"
                type="text"
                :placeholder="t('admin_site_copyright_placeholder')"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_timezone') }}</label>
              <select
                v-model="site.timezone"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
              >
                <option value="UTC">UTC</option>
                <option value="UTC+8">UTC+8 (China)</option>
                <option value="UTC-5">UTC-5 (US East)</option>
                <option value="UTC+1">UTC+1 (Central Europe)</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <label :class="['flex items-center gap-3 cursor-pointer']">
                <div :class="['w-12 h-6 rounded-full relative transition-colors', site.maintenance ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                  <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', site.maintenance ? 'left-7' : 'left-1']"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_maintenance_mode') }}</span>
                <input
                  v-model="site.maintenance"
                  type="checkbox"
                  class="hidden"
                />
              </label>
            </div>
            <div>
              <label :class="['flex items-center gap-3 cursor-pointer']">
                <div :class="['w-12 h-6 rounded-full relative transition-colors', site.author_bio ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                  <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', site.author_bio ? 'left-7' : 'left-1']"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_show_author_bio') }}</span>
                <input
                  v-model="site.author_bio"
                  type="checkbox"
                  class="hidden"
                />
              </label>
            </div>
            <div>
              <label :class="['flex items-center gap-3 cursor-pointer']">
                <div :class="['w-12 h-6 rounded-full relative transition-colors', site.comments ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                  <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', site.comments ? 'left-7' : 'left-1']"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_show_comments') }}</span>
                <input
                  v-model="site.comments"
                  type="checkbox"
                  class="hidden"
                />
              </label>
            </div>

            <!-- Feature Toggles -->
            <div class="md:col-span-2">
              <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4 pt-4 border-t', isDarkMode ? 'text-gray-400 border-gray-700' : 'text-gray-500 border-gray-200']">{{ t('admin_feature_toggles') }}</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label :class="['flex items-center gap-3 cursor-pointer']">
                    <div :class="['w-12 h-6 rounded-full relative transition-colors', site.registration ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                      <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', site.registration ? 'left-7' : 'left-1']"></div>
                    </div>
                    <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_allow_registration') }}</span>
                    <input
                      v-model="site.registration"
                      type="checkbox"
                      class="hidden"
                    />
                  </label>
                </div>
                <div>
                  <label :class="['flex items-center gap-3 cursor-pointer']">
                    <div :class="['w-12 h-6 rounded-full relative transition-colors', site.comment_approval ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                      <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', site.comment_approval ? 'left-7' : 'left-1']"></div>
                    </div>
                    <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_require_comment_approval') }}</span>
                    <input
                      v-model="site.comment_approval"
                      type="checkbox"
                      class="hidden"
                    />
                  </label>
                </div>
                <div>
                  <label :class="['flex items-center gap-3 cursor-pointer']">
                    <div :class="['w-12 h-6 rounded-full relative transition-colors', site.newsletter ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                      <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', site.newsletter ? 'left-7' : 'left-1']"></div>
                    </div>
                    <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_newsletter') }}</span>
                    <input
                      v-model="site.newsletter"
                      type="checkbox"
                      class="hidden"
                    />
                  </label>
                </div>
                <div>
                  <label :class="['flex items-center gap-3 cursor-pointer']">
                    <div :class="['w-12 h-6 rounded-full relative transition-colors', site.social_login ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                      <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', site.social_login ? 'left-7' : 'left-1']"></div>
                    </div>
                    <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_social_login') }}</span>
                    <input
                      v-model="site.social_login"
                      type="checkbox"
                      class="hidden"
                    />
                  </label>
                </div>
                <div>
                  <label :class="['flex items-center gap-3 cursor-pointer']">
                    <div :class="['w-12 h-6 rounded-full relative transition-colors', site.search ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                      <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', site.search ? 'left-7' : 'left-1']"></div>
                    </div>
                    <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_search') }}</span>
                    <input
                      v-model="site.search"
                      type="checkbox"
                      class="hidden"
                    />
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Appearance Settings -->
        <div v-if="activeTab === 'appearance'">
          <h3 class="font-display text-2xl tracking-tighter mb-6 flex items-center gap-3">
            <Palette size="24" class="text-construct-red" />
            {{ t('admin_settings_appearance') }}
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="md:col-span-2">
              <!-- Theme Management -->
              <div :class="['border-t pt-4', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
                <div class="flex items-center justify-between mb-4">
                  <span :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_management') }}</span>
                  <button
                    @click="openAddTheme"
                    class="flex items-center gap-2 px-4 py-2 bg-construct-red hover:bg-red-600 text-white text-xs font-bold tracking-wider transition-colors rounded"
                  >
                    <Plus size="14" /> {{ t('admin_add_theme') }}
                  </button>
                </div>
                
                <!-- Theme List Table -->
                <div :class="['border rounded-lg overflow-hidden', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
                  <table class="w-full text-sm">
                    <thead :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
                      <tr>
                        <th :class="['px-4 py-3 text-left font-bold tracking-wider uppercase text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_color') }}</th>
                        <th :class="['px-4 py-3 text-left font-bold tracking-wider uppercase text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_name') }}</th>
                        <th :class="['px-4 py-3 text-left font-bold tracking-wider uppercase text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_label') }}</th>
                        <th :class="['px-4 py-3 text-center font-bold tracking-wider uppercase text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_status') }}</th>
                        <th :class="['px-4 py-3 text-center font-bold tracking-wider uppercase text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_default') }}</th>
                        <th :class="['px-4 py-3 text-center font-bold tracking-wider uppercase text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_actions') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="theme in themeList" :key="theme.id" :class="['border-t', isDarkMode ? 'border-gray-700 hover:bg-gray-700/50' : 'border-gray-200 hover:bg-gray-50']">
                        <td class="px-4 py-3">
                          <div class="w-8 h-8 rounded-full" :style="{ backgroundColor: theme.color }"></div>
                        </td>
                        <td :class="['px-4 py-3 font-mono text-xs', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ theme.name }}</td>
                        <td :class="['px-4 py-3', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ theme.label }}</td>
                        <td class="px-4 py-3 text-center">
                          <span :class="['px-2 py-1 text-xs font-bold rounded', theme.is_active ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400']">
                            {{ theme.is_active ? t('admin_enabled') : t('admin_disabled') }}
                          </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <span :class="['px-2 py-1 text-xs font-bold rounded', theme.is_default ? 'bg-construct-red/20 text-construct-red' : 'bg-gray-500/20 text-gray-400']">
                            {{ theme.is_default ? t('admin_yes') : t('admin_no') }}
                          </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <div class="flex items-center justify-center gap-2">
                            <button
                              @click="openEditTheme(theme)"
                              class="p-1.5 rounded transition-colors"
                              :class="isDarkMode ? 'hover:bg-gray-600 text-gray-400' : 'hover:bg-gray-200 text-gray-600'"
                            >
                              <Edit2 size="14" />
                            </button>
                            <button
                              @click="deleteTheme(theme)"
                              class="p-1.5 rounded transition-colors text-red-500 hover:bg-red-500/10"
                            >
                              <Trash2 size="14" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Notifications Settings -->
        <div v-if="activeTab === 'notifications'">
          <h3 class="font-display text-2xl tracking-tighter mb-6 flex items-center gap-3">
            <Bell size="24" class="text-construct-red" />
            {{ t('admin_settings_notifications') }}
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
              <label :class="['flex items-center gap-3 cursor-pointer']">
                <div :class="['w-12 h-6 rounded-full relative transition-colors', userNotifications.email_notifications ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                  <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', userNotifications.email_notifications ? 'left-7' : 'left-1']"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_email_notifications') }}</span>
                <input
                  v-model="userNotifications.email_notifications"
                  type="checkbox"
                  class="hidden"
                />
              </label>
            </div>
            <div>
              <label :class="['flex items-center gap-3 cursor-pointer']">
                <div :class="['w-12 h-6 rounded-full relative transition-colors', userNotifications.comment_approval_alert ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                  <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', userNotifications.comment_approval_alert ? 'left-7' : 'left-1']"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_comment_approval') }}</span>
                <input
                  v-model="userNotifications.comment_approval_alert"
                  type="checkbox"
                  class="hidden"
                />
              </label>
            </div>
            <div>
              <label :class="['flex items-center gap-3 cursor-pointer']">
                <div :class="['w-12 h-6 rounded-full relative transition-colors', userNotifications.new_user_alert ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                  <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', userNotifications.new_user_alert ? 'left-7' : 'left-1']"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_new_user_alert') }}</span>
                <input
                  v-model="userNotifications.new_user_alert"
                  type="checkbox"
                  class="hidden"
                />
              </label>
            </div>
            <div>
              <label :class="['flex items-center gap-3 cursor-pointer']">
                <div :class="['w-12 h-6 rounded-full relative transition-colors', userNotifications.weekly_report ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                  <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', userNotifications.weekly_report ? 'left-7' : 'left-1']"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_weekly_report') }}</span>
                <input
                  v-model="userNotifications.weekly_report"
                  type="checkbox"
                  class="hidden"
                />
              </label>
            </div>
            <div>
              <label :class="['flex items-center gap-3 cursor-pointer']">
                <div :class="['w-12 h-6 rounded-full relative transition-colors', userNotifications.digest_email ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                  <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', userNotifications.digest_email ? 'left-7' : 'left-1']"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_digest_email') }}</span>
                <input
                  v-model="userNotifications.digest_email"
                  type="checkbox"
                  class="hidden"
                />
              </label>
            </div>
            <div v-if="userNotifications.digest_email">
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_digest_frequency') }}</label>
              <select
                v-model="userNotifications.digest_frequency"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
              >
                <option value="daily">{{ t('admin_frequency_daily') }}</option>
                <option value="weekly">{{ t('admin_frequency_weekly') }}</option>
                <option value="monthly">{{ t('admin_frequency_monthly') }}</option>
              </select>
            </div>
          </div>
        </div>
        <div v-if="activeTab === 'seo'">
          <h3 class="font-display text-2xl tracking-tighter mb-6 flex items-center gap-3">
            <Search size="24" class="text-construct-red" />
            {{ t('admin_settings_seo') }}
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="md:col-span-2">
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_meta_title') }}</label>
              <input
                v-model="seo.meta_title"
                type="text"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
              />
              <p :class="['text-xs mt-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ t('admin_meta_title_hint') }}</p>
            </div>
            <div class="md:col-span-2">
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_meta_description') }}</label>
              <textarea
                v-model="seo.meta_description"
                rows="3"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none resize-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
              ></textarea>
              <p :class="['text-xs mt-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ t('admin_meta_description_hint') }}</p>
            </div>
            <div class="md:col-span-2">
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_meta_keywords') }}</label>
              <input
                v-model="seo.meta_keywords"
                type="text"
                :placeholder="t('admin_meta_keywords_placeholder')"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
              />
              <p :class="['text-xs mt-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ t('admin_meta_keywords_hint') }}</p>
            </div>
            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_google_analytics') }}</label>
              <input
                v-model="seo.google_analytics"
                type="text"
                :placeholder="t('admin_google_analytics_placeholder')"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_baidu_analytics') }}</label>
              <input
                v-model="seo.baidu_analytics"
                type="text"
                :placeholder="t('admin_baidu_analytics_placeholder')"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
              />
            </div>
            <div class="md:col-span-2">
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_canonical_url') }}</label>
              <input
                v-model="seo.canonical_url"
                type="url"
                :placeholder="t('admin_canonical_url_placeholder')"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_og_image') }}</label>
              <input
                v-model="seo.og_image"
                type="text"
                :placeholder="t('admin_og_image_placeholder')"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_og_type') }}</label>
              <select
                v-model="seo.og_type"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
              >
                <option value="website">Website</option>
                <option value="article">Article</option>
                <option value="profile">Profile</option>
                <option value="book">Book</option>
              </select>
            </div>
            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_twitter_card') }}</label>
              <select
                v-model="seo.twitter_card"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
              >
                <option value="summary_large_image">Summary Large Image</option>
                <option value="summary">Summary</option>
                <option value="player">Player</option>
                <option value="app">App</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <div class="flex items-center gap-8">
                <label :class="['flex items-center gap-3 cursor-pointer']">
                  <div :class="['w-12 h-6 rounded-full relative transition-colors', seo.sitemap ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                    <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', seo.sitemap ? 'left-7' : 'left-1']"></div>
                  </div>
                  <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_sitemap') }}</span>
                  <input v-model="seo.sitemap" type="checkbox" class="hidden" />
                </label>

                <label :class="['flex items-center gap-3 cursor-pointer']">
                  <div :class="['w-12 h-6 rounded-full relative transition-colors', seo.robots ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                    <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', seo.robots ? 'left-7' : 'left-1']"></div>
                  </div>
                  <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_robots') }}</span>
                  <input v-model="seo.robots" type="checkbox" class="hidden" />
                </label>

                <label :class="['flex items-center gap-3 cursor-pointer']">
                  <div :class="['w-12 h-6 rounded-full relative transition-colors', seo.llm_txt ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                    <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', seo.llm_txt ? 'left-7' : 'left-1']"></div>
                  </div>
                  <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_llm_txt') }}</span>
                  <input v-model="seo.llm_txt" type="checkbox" class="hidden" />
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Performance Settings -->
        <div v-if="activeTab === 'performance'">
          <h3 class="font-display text-2xl tracking-tighter mb-6 flex items-center gap-3">
            <Zap size="24" class="text-construct-red" />
            {{ t('admin_settings_performance') }}
          </h3>
          
          <div class="space-y-8">
            <!-- Cache Settings -->
            <div :class="['border rounded-lg p-6', isDarkMode ? 'border-gray-700 bg-gray-700/30' : 'border-gray-200 bg-gray-50']">
              <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">缓存设置</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label :class="['flex items-center gap-3 cursor-pointer']">
                    <div :class="['w-12 h-6 rounded-full relative transition-colors', site.cache ? 'bg-construct-red' : 'bg-gray-600']">
                      <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', site.cache ? 'left-7' : 'left-1']"></div>
                    </div>
                    <span class="font-bold tracking-wider">{{ t('admin_enable_cache') }}</span>
                    <input
                      v-model="site.cache"
                      type="checkbox"
                      class="hidden"
                    />
                  </label>
                </div>
                <div>
                  <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_cache_duration') }} ({{ t('admin_seconds') }})</label>
                  <input
                    v-model.number="site.cache_duration"
                    type="number"
                    :class="[
                      'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                      isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                    ]"
                  />
                </div>
              </div>
            </div>

            <!-- Code Optimization -->
            <div :class="['border rounded-lg p-6', isDarkMode ? 'border-gray-700 bg-gray-700/30' : 'border-gray-200 bg-gray-50']">
              <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">代码优化</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label :class="['flex items-center gap-3 cursor-pointer']">
                    <div :class="['w-12 h-6 rounded-full relative transition-colors', site.minification ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                      <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', site.minification ? 'left-7' : 'left-1']"></div>
                    </div>
                    <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_minification') }}</span>
                    <input
                      v-model="site.minification"
                      type="checkbox"
                      class="hidden"
                    />
                  </label>
                </div>
                <div>
                  <label :class="['flex items-center gap-3 cursor-pointer']">
                    <div :class="['w-12 h-6 rounded-full relative transition-colors', site.lazy_load ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                      <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', site.lazy_load ? 'left-7' : 'left-1']"></div>
                    </div>
                    <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_lazy_load_images') }}</span>
                    <input
                      v-model="site.lazy_load"
                      type="checkbox"
                      class="hidden"
                    />
                  </label>
                </div>
              </div>
            </div>

            <!-- CDN Settings -->
            <div :class="['border rounded-lg p-6', isDarkMode ? 'border-gray-700 bg-gray-700/30' : 'border-gray-200 bg-gray-50']">
              <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">CDN 加速</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label :class="['flex items-center gap-3 cursor-pointer']">
                    <div :class="['w-12 h-6 rounded-full relative transition-colors', site.cdn ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                      <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', site.cdn ? 'left-7' : 'left-1']"></div>
                    </div>
                    <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_cdn') }}</span>
                    <input
                      v-model="site.cdn"
                      type="checkbox"
                      class="hidden"
                    />
                  </label>
                </div>
                <div>
                  <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_cdn_url') }}</label>
                  <input
                    v-model="site.cdn_url"
                    type="url"
                    :placeholder="t('admin_cdn_url_placeholder')"
                    :class="[
                      'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                      isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                    ]"
                  />
                </div>
              </div>
            </div>

            <!-- Upload Settings -->
            <div :class="['border rounded-lg p-6', isDarkMode ? 'border-gray-700 bg-gray-700/30' : 'border-gray-200 bg-gray-50']">
              <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_upload_settings') }}</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_max_upload_size') }} (MB)</label>
                  <input
                    v-model.number="site.max_upload_size"
                    type="number"
                    min="1"
                    max="100"
                    :class="[
                      'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                      isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                    ]"
                  />
                </div>
                <div>
                  <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_allowed_file_types') }}</label>
                  <input
                    v-model="fileTypesInput"
                    type="text"
                    :placeholder="t('admin_allowed_file_types_placeholder')"
                    :class="[
                      'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                      isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                    ]"
                  />
                  <p :class="['text-xs mt-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ t('admin_allowed_file_types_hint') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </fieldset>
    </div>

    <MediaPickerModal
      :visible="showMediaPicker"
      :media="mediaFiles"
      @close="showMediaPicker = false"
      @select="handleMediaSelect"
    />

    <!-- Theme Form Dialog -->
    <Transition name="fade">
      <div
        v-if="showThemeForm"
        class="fixed inset-0 bg-black/80 z-[100] flex items-center justify-center p-4"
        @click.self="cancelThemeForm"
      >
        <div :class="['w-full max-w-lg border rounded-xl shadow-2xl', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
          <div :class="['flex items-center justify-between px-6 py-4 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <h3 :class="['font-display text-xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">
              {{ editingTheme ? t('admin_edit_theme') : t('admin_add_theme') }}
            </h3>
            <button @click="cancelThemeForm" :class="['p-2 rounded transition-colors', isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600']">
              <X size="20" />
            </button>
          </div>

          <div class="p-6 space-y-4">
            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_name') }}</label>
              <input
                v-model="themeForm.name"
                type="text"
                placeholder="e.g. construct-red"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_label') }}</label>
              <input
                v-model="themeForm.label"
                type="text"
                placeholder="e.g. 建筑红"
                :class="[
                  'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_color') }}</label>
              <div class="flex items-center gap-4">
                <input
                  v-model="themeForm.color"
                  type="color"
                  class="w-16 h-12 border rounded cursor-pointer"
                  :class="isDarkMode ? 'border-gray-600 bg-gray-700' : 'border-gray-300 bg-white'"
                />
                <input
                  v-model="themeForm.color"
                  type="text"
                  placeholder="#CF202E"
                  :class="[
                    'flex-1 px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                  ]"
                />
              </div>
            </div>

            <div class="flex items-center gap-6">
              <label class="flex items-center gap-3 cursor-pointer">
                <div :class="['w-12 h-6 rounded-full relative transition-colors', themeForm.is_active ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                  <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', themeForm.is_active ? 'left-7' : 'left-1']"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enabled') }}</span>
                <input v-model="themeForm.is_active" type="checkbox" class="hidden" />
              </label>

              <label class="flex items-center gap-3 cursor-pointer">
                <div :class="['w-12 h-6 rounded-full relative transition-colors', themeForm.is_default ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                  <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', themeForm.is_default ? 'left-7' : 'left-1']"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_default') }}</span>
                <input v-model="themeForm.is_default" type="checkbox" class="hidden" />
              </label>
            </div>
          </div>

          <div :class="['flex items-center justify-end gap-4 px-6 py-4 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <button
              @click="cancelThemeForm"
              :class="[
                'px-6 py-3 border font-bold tracking-wider transition-colors rounded',
                isDarkMode ? 'border-gray-700 hover:bg-gray-700 text-white' : 'border-gray-300 hover:bg-gray-100 text-gray-900'
              ]"
            >
              {{ t('admin_cancel') }}
            </button>
            <button
              @click="saveTheme"
              class="px-8 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider transition-colors rounded shadow-sm"
            >
              {{ t('admin_save') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

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
