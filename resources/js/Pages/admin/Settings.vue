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
  Zap,
  Image,
  useToast,
  ConfirmDialog
} from '../../composables/useAdminImports';
import { Plus, Edit2, Trash2 } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  siteConfig: { type: Object, default: () => ({}) },
  seoConfig: { type: Object, default: () => ({}) },
  commentConfig: { type: Object, default: () => ({}) },
  userNotifications: { type: Object, default: () => ({}) },
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
const { success, error: toastError } = useToast();

const tabsConfig = [
  { id: 'site', label_key: 'admin_settings_site', icon_key: 'globe' },
  { id: 'appearance', label_key: 'admin_settings_appearance', icon_key: 'palette' },
  { id: 'notifications', label_key: 'admin_settings_notifications', icon_key: 'bell' },
  { id: 'seo', label_key: 'admin_settings_seo', icon_key: 'search' },
  { id: 'performance', label_key: 'admin_settings_performance', icon_key: 'zap' }
];

const userNotifications = ref({
  email_notifications: props.userNotifications?.email_notifications ?? true,
  comment_approval_alert: props.userNotifications?.comment_approval_alert ?? true,
  new_user_alert: props.userNotifications?.new_user_alert ?? true,
  weekly_report: props.userNotifications?.weekly_report ?? false,
  digest_email: props.userNotifications?.digest_email ?? true,
  digest_frequency: props.userNotifications?.digest_frequency ?? 'weekly'
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
const seo = ref({
  rss_feed: true,
  ...(props.seoConfig || {}),
});
const activeTab = ref(localStorage.getItem('settings_active_tab') || 'site');

watch(activeTab, (val) => {
  localStorage.setItem('settings_active_tab', val);
});
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
      preserveState: true,
      onSuccess: () => {
        isSavingTheme.value = false;
        success(t('admin_update') + ' ' + t('confirm'));
        showThemeForm.value = false;
      },
      onError: (errors) => {
        isSavingTheme.value = false;
        toastError(t('admin_update_failed') || 'Update failed');
      }
    });
  } else {
    router.post(route('admin.settings.themes.store'), themeForm.value, {
      preserveState: true,
      onSuccess: () => {
        isSavingTheme.value = false;
        success(t('admin_create') + ' ' + t('confirm'));
        showThemeForm.value = false;
      },
      onError: (errors) => {
        isSavingTheme.value = false;
        toastError(t('admin_create_failed') || 'Create failed');
      }
    });
  }
};

const deleteTheme = (theme) => {
  if (confirm(`确定要删除主题 "${theme.label}" 吗？`)) {
    router.delete(route('admin.settings.themes.destroy', theme.id), {
      preserveState: true,
      onSuccess: () => {
        success(t('admin_delete') + ' ' + t('confirm'));
      },
      onError: (errors) => {
        toastError(t('admin_delete_failed') || 'Delete failed');
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
    rss_feed: seo.value.rss_feed,
    // 用户通知设置
    email_notifications: userNotifications.value.email_notifications,
    comment_approval_alert: userNotifications.value.comment_approval_alert,
    new_user_alert: userNotifications.value.new_user_alert,
    weekly_report: userNotifications.value.weekly_report,
    digest_email: userNotifications.value.digest_email,
    digest_frequency: userNotifications.value.digest_frequency,
  }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      isSaving.value = false;
      success(t('admin_save') + ' ' + t('confirm'));
    },
    onError: (errors) => {
      isSaving.value = false;
      toastError(t('admin_save_failed') || 'Save failed');
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
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Settings class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_settings') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_settings_subtitle') }}</p>
        </div>
        <button
          @click="saveSettings"
          :disabled="isSaving"
          class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl disabled:opacity-50"
        >
          <Save size="18" /> {{ isSaving ? t('admin_save') + '...' : t('admin_save') }}
        </button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-2 mb-8 overflow-x-auto pb-2">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="activeTab = tab.id"
        :class="[
          'inline-flex items-center gap-2 px-6 py-3 font-bold tracking-wider rounded-lg transition-all whitespace-nowrap cursor-pointer',
          activeTab === tab.id
            ? 'bg-construct-red text-white shadow-md'
            : isDarkMode
              ? 'bg-gray-800 text-gray-400 hover:bg-gray-700'
              : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100'
        ]"
      >
        <component :is="tab.icon" size="18" />
        {{ tab.label }}
      </button>
    </div>

    <!-- Settings Content -->
    <div :class="['rounded-xl border p-8 backdrop-blur-xl', isDarkMode ? 'bg-gray-900/40 border-gray-700/30' : 'bg-white/40 border-white/20 shadow-sm']">
      <fieldset class="border-none p-0 m-0">
        <!-- Site Settings -->
        <div v-if="activeTab === 'site'">
          <h3 :class="['flex items-center gap-3 text-sm font-bold tracking-widest uppercase mb-6', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
            <Globe size="24" class="text-construct-red" />
            {{ t('admin_settings_site') }}
          </h3>

          <!-- Basic Info Card -->
          <div :class="['rounded-xl p-6 mb-8', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_name') }} &amp; {{ t('admin_site_url') }}</h4>
            <div class="grid grid-cols-2 gap-8 max-md:grid-cols-1">
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_name') }}</label>
                <input v-model="site.name" type="text" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
              </div>
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_url') }}</label>
                <input v-model="site.site_url" type="url" :placeholder="t('admin_site_url_placeholder')" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
              </div>
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_description') }}</label>
                <input v-model="site.description" type="text" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
              </div>
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_timezone') }}</label>
                <select v-model="site.timezone" :class="['w-full px-4 py-3 border rounded-lg transition-all appearance-none focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900']" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 1rem center; padding-right: 2.5rem;">
                  <option value="UTC">UTC</option>
                  <option value="UTC+8">UTC+8 (China)</option>
                  <option value="UTC-5">UTC-5 (US East)</option>
                  <option value="UTC+1">UTC+1 (Central Europe)</option>
                </select>
              </div>
              <div class="col-span-2 max-md:col-span-1">
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_copyright') }}</label>
                <input v-model="site.copyright" type="text" :placeholder="t('admin_site_copyright_placeholder')" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
              </div>
            </div>
          </div>

          <!-- Branding Card -->
          <div :class="['rounded-xl p-6 mb-8', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Branding</h4>
            <div class="grid grid-cols-2 gap-8 max-md:grid-cols-1">
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_logo') }}</label>
                <div class="flex items-start gap-4">
                  <div @click="openLogoPicker" :class="['w-16 h-16 border-2 border-dashed rounded-xl flex items-center justify-center flex-shrink-0 transition-all cursor-pointer', isDarkMode ? 'border-gray-600 bg-gray-700 hover:border-construct-red hover:bg-gray-600' : 'border-gray-300 bg-gray-50 hover:border-construct-red hover:bg-gray-100']">
                    <img v-if="site.logo" :src="site.logo" alt="Site Logo" class="w-12 h-12 object-contain" />
                    <Image v-else size="24" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" />
                  </div>
                  <div class="flex-1">
                    <input v-model="site.logo" type="text" :placeholder="t('admin_site_logo_placeholder')" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none mb-2', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
                    <p :class="['text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ t('admin_site_logo_hint') }}</p>
                  </div>
                </div>
              </div>
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_icon') }}</label>
                <div class="flex items-start gap-4">
                  <div @click="openFaviconPicker" :class="['w-16 h-16 border-2 border-dashed rounded-xl flex items-center justify-center flex-shrink-0 transition-all cursor-pointer', isDarkMode ? 'border-gray-600 bg-gray-700 hover:border-construct-red hover:bg-gray-600' : 'border-gray-300 bg-gray-50 hover:border-construct-red hover:bg-gray-100']">
                    <img v-if="site.favicon" :src="site.favicon" alt="Site Icon" class="w-10 h-10 object-contain" />
                    <Image v-else size="24" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" />
                  </div>
                  <div class="flex-1">
                    <input v-model="site.favicon" type="text" :placeholder="t('admin_site_icon_placeholder')" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none mb-2', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
                    <p :class="['text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ t('admin_site_icon_hint') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Content Toggles Card -->
          <div :class="['rounded-xl p-6 mb-8', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_content_settings') }}</h4>
            <div class="grid grid-cols-2 gap-6 max-md:grid-cols-1">
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="site.maintenance ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="site.maintenance ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_maintenance_mode') }}</span>
                <input v-model="site.maintenance" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="site.author_bio ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="site.author_bio ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_show_author_bio') }}</span>
                <input v-model="site.author_bio" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="site.comments ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="site.comments ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_show_comments') }}</span>
                <input v-model="site.comments" type="checkbox" class="hidden" />
              </label>
            </div>
          </div>

          <!-- Feature Toggles Card -->
          <div :class="['rounded-xl p-6', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_feature_toggles') }}</h4>
            <div class="grid grid-cols-2 gap-6 max-md:grid-cols-1">
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="site.registration ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="site.registration ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_allow_registration') }}</span>
                <input v-model="site.registration" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="site.comment_approval ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="site.comment_approval ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_require_comment_approval') }}</span>
                <input v-model="site.comment_approval" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="site.newsletter ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="site.newsletter ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_newsletter') }}</span>
                <input v-model="site.newsletter" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="site.social_login ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="site.social_login ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_social_login') }}</span>
                <input v-model="site.social_login" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="site.search ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="site.search ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_search') }}</span>
                <input v-model="site.search" type="checkbox" class="hidden" />
              </label>
            </div>
          </div>
        </div>

        <!-- Appearance Settings -->
        <div v-if="activeTab === 'appearance'">
          <h3 :class="['flex items-center gap-3 text-sm font-bold tracking-widest uppercase mb-6', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
            <Palette size="24" class="text-construct-red" />
            {{ t('admin_settings_appearance') }}
          </h3>
          
          <!-- Theme Management Card -->
          <div :class="['rounded-xl p-6', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <div class="flex items-center justify-between mb-4">
              <span :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_management') }}</span>
              <button
                @click="openAddTheme"
                class="flex items-center gap-2 px-4 py-2 bg-construct-red hover:bg-red-600 text-white text-xs font-bold tracking-wider transition-all rounded-lg shadow-sm"
              >
                <Plus size="14" /> {{ t('admin_add_theme') }}
              </button>
            </div>
            
            <!-- Theme List Table -->
            <div :class="['border rounded-lg overflow-hidden', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
              <table class="w-full">
                <thead>
                  <tr :class="[isDarkMode ? 'bg-gray-700/50' : 'bg-gray-100/70']">
                    <th :class="['px-4 py-3 text-left text-xs font-bold tracking-wider uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_color') }}</th>
                    <th :class="['px-4 py-3 text-left text-xs font-bold tracking-wider uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_name') }}</th>
                    <th :class="['px-4 py-3 text-left text-xs font-bold tracking-wider uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_label') }}</th>
                    <th :class="['px-4 py-3 text-center text-xs font-bold tracking-wider uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_status') }}</th>
                    <th :class="['px-4 py-3 text-center text-xs font-bold tracking-wider uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_default') }}</th>
                    <th :class="['px-4 py-3 text-center text-xs font-bold tracking-wider uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="theme in themeList" :key="theme.id" :class="['border-t transition-colors', isDarkMode ? 'border-gray-700 hover:bg-gray-700/50' : 'border-gray-200 hover:bg-gray-50']">
                    <td class="px-4 py-3">
                      <div class="w-8 h-8 rounded-full shadow-inner" :style="{ backgroundColor: theme.color }"></div>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ theme.name }}</td>
                    <td class="px-4 py-3" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ theme.label }}</td>
                    <td class="px-4 py-3 text-center">
                      <span :class="['inline-block px-3 py-0.5 text-xs font-bold rounded-full tracking-wider', theme.is_active ? 'bg-green-500/15 text-green-500' : (isDarkMode ? 'bg-gray-500/15 text-gray-400' : 'bg-gray-500/15 text-gray-400')]">
                        {{ theme.is_active ? t('admin_enabled') : t('admin_disabled') }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                      <span :class="['inline-block px-3 py-0.5 text-xs font-bold rounded-full tracking-wider', theme.is_default ? 'bg-construct-red/15 text-construct-red' : (isDarkMode ? 'bg-gray-500/15 text-gray-400' : 'bg-gray-500/15 text-gray-400')]">
                        {{ theme.is_default ? t('admin_yes') : t('admin_no') }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                      <div class="flex items-center justify-center gap-2">
                        <button
                          @click="openEditTheme(theme)"
                          :class="['p-1.5 rounded transition-colors', isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-400 hover:text-gray-700 hover:bg-gray-200']"
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

        <!-- Notifications Settings -->
        <div v-if="activeTab === 'notifications'">
          <h3 :class="['flex items-center gap-3 text-sm font-bold tracking-widest uppercase mb-6', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
            <Bell size="24" class="text-construct-red" />
            {{ t('admin_settings_notifications') }}
          </h3>
          
          <!-- Notification Preferences Card -->
          <div :class="['rounded-xl p-6 mb-8', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_notification_preferences') }}</h4>
            <div class="grid grid-cols-2 gap-6 max-md:grid-cols-1">
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="userNotifications.email_notifications ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="userNotifications.email_notifications ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_email_notifications', '邮件通知') }}</span>
                <input v-model="userNotifications.email_notifications" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="userNotifications.comment_approval_alert ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="userNotifications.comment_approval_alert ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_comment_approval', '评论审核通知') }}</span>
                <input v-model="userNotifications.comment_approval_alert" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="userNotifications.new_user_alert ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="userNotifications.new_user_alert ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_new_user_alert', '新用户注册通知') }}</span>
                <input v-model="userNotifications.new_user_alert" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="userNotifications.weekly_report ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="userNotifications.weekly_report ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_weekly_report', '每周报告') }}</span>
                <input v-model="userNotifications.weekly_report" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="userNotifications.digest_email ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="userNotifications.digest_email ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_digest_email') }}</span>
                <input v-model="userNotifications.digest_email" type="checkbox" class="hidden" />
              </label>
            </div>
            <div v-if="userNotifications.digest_email" class="mt-6 max-w-xs">
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_digest_frequency') }}</label>
              <select v-model="userNotifications.digest_frequency" :class="['w-full px-4 py-3 border rounded-lg transition-all appearance-none focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900']" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 1rem center; padding-right: 2.5rem;">
                <option value="daily">{{ t('admin_frequency_daily') }}</option>
                <option value="weekly">{{ t('admin_frequency_weekly') }}</option>
                <option value="monthly">{{ t('admin_frequency_monthly') }}</option>
              </select>
            </div>
          </div>
        </div>
        <!-- SEO Settings -->
        <div v-if="activeTab === 'seo'">
          <h3 :class="['flex items-center gap-3 text-sm font-bold tracking-widest uppercase mb-6', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
            <Search size="24" class="text-construct-red" />
            {{ t('admin_settings_seo') }}
          </h3>

          <!-- Meta Tags Card -->
          <div :class="['rounded-xl p-6 mb-8', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_meta_settings') }}</h4>
            <div class="grid grid-cols-2 gap-8 max-md:grid-cols-1">
              <div class="col-span-2 max-md:col-span-1">
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_meta_title') }}</label>
                <input v-model="seo.meta_title" type="text" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
                <p :class="['text-xs mt-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ t('admin_meta_title_hint') }}</p>
              </div>
              <div class="col-span-2 max-md:col-span-1">
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_meta_description') }}</label>
                <textarea v-model="seo.meta_description" rows="3" :class="['w-full px-4 py-3 border rounded-lg transition-all resize-y focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']"></textarea>
                <p :class="['text-xs mt-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ t('admin_meta_description_hint') }}</p>
              </div>
              <div class="col-span-2 max-md:col-span-1">
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_meta_keywords') }}</label>
                <input v-model="seo.meta_keywords" type="text" :placeholder="t('admin_meta_keywords_placeholder')" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
                <p :class="['text-xs mt-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ t('admin_meta_keywords_hint') }}</p>
              </div>
            </div>
          </div>

          <!-- Analytics Card -->
          <div :class="['rounded-xl p-6 mb-8', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_analytics_tracking') }}</h4>
            <div class="grid grid-cols-2 gap-8 max-md:grid-cols-1">
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_google_analytics') }}</label>
                <input v-model="seo.google_analytics" type="text" :placeholder="t('admin_google_analytics_placeholder')" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
              </div>
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_baidu_analytics') }}</label>
                <input v-model="seo.baidu_analytics" type="text" :placeholder="t('admin_baidu_analytics_placeholder')" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
              </div>
            </div>
          </div>

          <!-- Social Graph Card -->
          <div :class="['rounded-xl p-6 mb-8', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Open Graph / Social</h4>
            <div class="grid grid-cols-2 gap-8 max-md:grid-cols-1">
              <div class="col-span-2 max-md:col-span-1">
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_canonical_url') }}</label>
                <input v-model="seo.canonical_url" type="url" :placeholder="t('admin_canonical_url_placeholder')" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
              </div>
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_og_image') }}</label>
                <input v-model="seo.og_image" type="text" :placeholder="t('admin_og_image_placeholder')" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
              </div>
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_og_type') }}</label>
                <select v-model="seo.og_type" :class="['w-full px-4 py-3 border rounded-lg transition-all appearance-none focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900']" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 1rem center; padding-right: 2.5rem;">
                  <option value="website">Website</option>
                  <option value="article">Article</option>
                  <option value="profile">Profile</option>
                  <option value="book">Book</option>
                </select>
              </div>
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_twitter_card') }}</label>
                <select v-model="seo.twitter_card" :class="['w-full px-4 py-3 border rounded-lg transition-all appearance-none focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900']" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 1rem center; padding-right: 2.5rem;">
                  <option value="summary_large_image">Summary Large Image</option>
                  <option value="summary">Summary</option>
                  <option value="player">Player</option>
                  <option value="app">App</option>
                </select>
              </div>
            </div>
          </div>

          <!-- SEO Features Card -->
          <div :class="['rounded-xl p-6', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_seo_features') }}</h4>
            <div class="flex flex-wrap items-center gap-8">
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="seo.sitemap ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="seo.sitemap ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_sitemap') }}</span>
                <input v-model="seo.sitemap" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="seo.robots ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="seo.robots ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_robots') }}</span>
                <input v-model="seo.robots" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="seo.llm_txt ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="seo.llm_txt ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_llm_txt') }}</span>
                <input v-model="seo.llm_txt" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="seo.rss_feed ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="seo.rss_feed ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_rss_feed') }}</span>
                <input v-model="seo.rss_feed" type="checkbox" class="hidden" />
              </label>
            </div>
          </div>
        </div>

        <!-- Performance Settings -->
        <div v-if="activeTab === 'performance'">
          <h3 :class="['flex items-center gap-3 text-sm font-bold tracking-widest uppercase mb-6', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
            <Zap size="24" class="text-construct-red" />
            {{ t('admin_settings_performance') }}
          </h3>

          <!-- Cache Settings Card -->
          <div :class="['rounded-xl p-6 mb-6', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_cache_settings') }}</h4>
            <div class="grid grid-cols-2 gap-8 max-md:grid-cols-1">
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="site.cache ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="site.cache ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_cache') }}</span>
                <input v-model="site.cache" type="checkbox" class="hidden" />
              </label>
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_cache_duration') }} ({{ t('admin_seconds') }})</label>
                <input v-model.number="site.cache_duration" type="number" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
              </div>
            </div>
          </div>

          <!-- Code Optimization Card -->
          <div :class="['rounded-xl p-6 mb-6', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_code_optimization') }}</h4>
            <div class="grid grid-cols-2 gap-6 max-md:grid-cols-1">
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="site.minification ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="site.minification ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_minification') }}</span>
                <input v-model="site.minification" type="checkbox" class="hidden" />
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="site.lazy_load ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="site.lazy_load ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_lazy_load_images') }}</span>
                <input v-model="site.lazy_load" type="checkbox" class="hidden" />
              </label>
            </div>
          </div>

          <!-- CDN Settings Card -->
          <div :class="['rounded-xl p-6 mb-6', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_cdn_acceleration') }}</h4>
            <div class="grid grid-cols-2 gap-8 max-md:grid-cols-1">
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="site.cdn ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="site.cdn ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enable_cdn') }}</span>
                <input v-model="site.cdn" type="checkbox" class="hidden" />
              </label>
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_cdn_url') }}</label>
                <input v-model="site.cdn_url" type="url" :placeholder="t('admin_cdn_url_placeholder')" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
              </div>
            </div>
          </div>

          <!-- Upload Settings Card -->
          <div :class="['rounded-xl p-6', isDarkMode ? 'bg-gray-700/30 border border-gray-700' : 'bg-gray-50 border border-gray-200']">
            <h4 :class="['text-sm font-bold tracking-widest uppercase mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_upload_settings') }}</h4>
            <div class="grid grid-cols-2 gap-8 max-md:grid-cols-1">
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_max_upload_size') }} (MB)</label>
                <input v-model.number="site.max_upload_size" type="number" min="1" max="100" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
              </div>
              <div>
                <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_allowed_file_types') }}</label>
                <input v-model="fileTypesInput" type="text" :placeholder="t('admin_allowed_file_types_placeholder')" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
                <p :class="['text-xs mt-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ t('admin_allowed_file_types_hint') }}</p>
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
        <div class="w-full max-w-lg border rounded-xl shadow-2xl" :class="isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'">
          <div class="flex items-center justify-between px-6 py-4" :class="isDarkMode ? 'border-b border-gray-700' : 'border-b border-gray-200'">
            <h3 class="font-display text-xl tracking-tighter" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
              {{ editingTheme ? t('admin_edit_theme') : t('admin_add_theme') }}
            </h3>
            <button @click="cancelThemeForm" class="p-2 rounded transition-colors" :class="[isDarkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-500 hover:bg-gray-100']">
              <X size="20" />
            </button>
          </div>

          <div class="p-6 space-y-4">
            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_name') }}</label>
              <input v-model="themeForm.name" type="text" placeholder="e.g. construct-red" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
            </div>

            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_label') }}</label>
              <input v-model="themeForm.label" type="text" placeholder="e.g. 建筑红" :class="['w-full px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
            </div>

            <div>
              <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_theme_color') }}</label>
              <div class="flex items-center gap-4">
                <input v-model="themeForm.color" type="color" :class="['w-16 h-12 rounded-lg cursor-pointer border p-1', isDarkMode ? 'bg-gray-700 border-gray-600' : 'bg-white border-gray-300']" />
                <input v-model="themeForm.color" type="text" placeholder="#CF202E" :class="['flex-1 px-4 py-3 border rounded-lg transition-all focus:border-construct-red focus:ring-1 focus:ring-construct-red focus:outline-none', isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400']" />
              </div>
            </div>

            <div class="flex items-center gap-6">
              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="themeForm.is_active ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="themeForm.is_active ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_enabled') }}</span>
                <input v-model="themeForm.is_active" type="checkbox" class="hidden" />
              </label>

              <label class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-6 rounded-full relative transition-colors flex-shrink-0" :class="themeForm.is_default ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')">
                  <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="themeForm.is_default ? 'translate-x-[1.625rem]' : 'translate-x-0.5'"></div>
                </div>
                <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_default') }}</span>
                <input v-model="themeForm.is_default" type="checkbox" class="hidden" />
              </label>
            </div>
          </div>

          <div class="flex items-center justify-end gap-4 px-6 py-4" :class="isDarkMode ? 'border-t border-gray-700' : 'border-t border-gray-200'">
            <button @click="cancelThemeForm" :class="['px-6 py-3 border font-bold tracking-wider rounded-lg transition-colors', isDarkMode ? 'border-gray-700 text-white hover:bg-gray-700' : 'border-gray-300 text-gray-900 hover:bg-gray-100']">
              {{ t('admin_cancel') }}
            </button>
            <button
              @click="saveTheme"
              class="px-8 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider transition-all rounded-lg shadow-md hover:shadow-lg"
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

<style scoped>
/* ─── Transition ─── */
.fade-enter-active,
.fade-leave-active {
  @apply transition-opacity duration-200;
}
.fade-enter-from,
.fade-leave-to {
  @apply opacity-0;
}
</style>
