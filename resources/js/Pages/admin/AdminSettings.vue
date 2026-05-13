<script setup>
/**
 * AdminSettings.vue - 系统设置页面
 * 
 * 功能说明：
 * - 管理系统全局配置
 * - 网站基本信息设置
 * - 主题和外观配置
 * - 通知和邮件设置
 * - SEO和性能优化
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Settings,
  Globe,
  Palette,
  Bell,
  Search,
  Save,
  RefreshCw,
  Check,
  Moon,
  Sun,
  Mail,
  Shield,
  Zap
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { defaultSettings, tabsConfig } from '../../data/admin';

const { t } = useI18n();
const { isDarkMode, toggleTheme } = useTheme();

const settings = ref({ ...defaultSettings });

const activeTab = ref('site');

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
    label: t(tab.labelKey),
    icon: iconMap[tab.iconKey]
  }));
});

const saveSettings = () => {
  console.log('Settings saved:', settings.value);
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
            <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Configure system settings</p>
          </div>
        </div>
        <div class="flex items-center gap-4">
          <button
            @click="resetSettings"
            :class="[
              'flex items-center gap-2 px-6 py-3 border font-bold tracking-wider transition-colors',
              isDarkMode ? 'border-gray-700 hover:bg-gray-700 text-white' : 'border-gray-300 hover:bg-gray-100 text-gray-900'
            ]"
          >
            <RefreshCw size="18" /> {{ t('admin_reset') }}
          </button>
          <button
            @click="saveSettings"
            class="flex items-center gap-2 px-6 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider transition-colors"
          >
            <Save size="18" :style="{ color: '#ffffff' }" /> {{ t('admin_save') }}
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
              v-model="settings.site.name"
              type="text"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
          </div>
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_site_description') }}</label>
            <input
              v-model="settings.site.description"
              type="text"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
          </div>
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_default_language') }}</label>
            <select
              v-model="settings.site.defaultLanguage"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            >
              <option value="en">English</option>
              <option value="zh">中文</option>
              <option value="zh-TW">繁體中文</option>
            </select>
          </div>
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_timezone') }}</label>
            <select
              v-model="settings.site.timezone"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none',
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
            <label class="flex items-center gap-3 cursor-pointer">
              <div :class="['w-12 h-6 rounded-full relative transition-colors', settings.site.maintenanceMode ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', settings.site.maintenanceMode ? 'left-7' : 'left-1']"></div>
              </div>
              <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_maintenance_mode') }}</span>
              <input
                v-model="settings.site.maintenanceMode"
                type="checkbox"
                class="hidden"
              />
            </label>
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
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_color_scheme') }}</label>
            <div class="flex gap-4">
              <button
                @click="toggleTheme()"
                class="flex items-center gap-2 px-4 py-2 border transition-colors"
                :class="isDarkMode ? 'border-construct-red bg-gray-700 text-white' : 'border-gray-300 bg-white text-gray-700'"
              >
                <Moon size="18" /> Dark
              </button>
              <button
                @click="toggleTheme()"
                class="flex items-center gap-2 px-4 py-2 border transition-colors"
                :class="!isDarkMode ? 'border-construct-red bg-white text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-400'"
              >
                <Sun size="18" /> Light
              </button>
            </div>
          </div>
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_accent_color') }}</label>
            <div class="flex items-center gap-4">
              <input
                v-model="settings.appearance.accentColor"
                type="color"
                :class="[
                  'w-12 h-10 border cursor-pointer',
                  isDarkMode ? 'border-gray-600 bg-gray-700' : 'border-gray-300 bg-white'
                ]"
              />
              <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ settings.appearance.accentColor }}</span>
            </div>
          </div>
          <div>
            <label class="flex items-center gap-3 cursor-pointer">
              <div :class="['w-12 h-6 rounded-full relative transition-colors', settings.appearance.showAuthorBio ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', settings.appearance.showAuthorBio ? 'left-7' : 'left-1']"></div>
              </div>
              <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_show_author_bio') }}</span>
              <input
                v-model="settings.appearance.showAuthorBio"
                type="checkbox"
                class="hidden"
              />
            </label>
          </div>
          <div>
            <label class="flex items-center gap-3 cursor-pointer">
              <div :class="['w-12 h-6 rounded-full relative transition-colors', settings.appearance.showComments ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', settings.appearance.showComments ? 'left-7' : 'left-1']"></div>
              </div>
              <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_show_comments') }}</span>
              <input
                v-model="settings.appearance.showComments"
                type="checkbox"
                class="hidden"
              />
            </label>
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
            <label class="flex items-center gap-3 cursor-pointer">
              <div :class="['w-12 h-6 rounded-full relative transition-colors', settings.notifications.emailNotifications ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', settings.notifications.emailNotifications ? 'left-7' : 'left-1']"></div>
              </div>
              <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_email_notifications') }}</span>
              <input
                v-model="settings.notifications.emailNotifications"
                type="checkbox"
                class="hidden"
              />
            </label>
          </div>
          <div>
            <label class="flex items-center gap-3 cursor-pointer">
              <div :class="['w-12 h-6 rounded-full relative transition-colors', settings.notifications.commentApproval ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', settings.notifications.commentApproval ? 'left-7' : 'left-1']"></div>
              </div>
              <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_comment_approval') }}</span>
              <input
                v-model="settings.notifications.commentApproval"
                type="checkbox"
                class="hidden"
              />
            </label>
          </div>
          <div>
            <label class="flex items-center gap-3 cursor-pointer">
              <div :class="['w-12 h-6 rounded-full relative transition-colors', settings.notifications.newUserAlert ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', settings.notifications.newUserAlert ? 'left-7' : 'left-1']"></div>
              </div>
              <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_new_user_alert') }}</span>
              <input
                v-model="settings.notifications.newUserAlert"
                type="checkbox"
                class="hidden"
              />
            </label>
          </div>
          <div>
            <label class="flex items-center gap-3 cursor-pointer">
              <div :class="['w-12 h-6 rounded-full relative transition-colors', settings.notifications.weeklyReport ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', settings.notifications.weeklyReport ? 'left-7' : 'left-1']"></div>
              </div>
              <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_weekly_report') }}</span>
              <input
                v-model="settings.notifications.weeklyReport"
                type="checkbox"
                class="hidden"
              />
            </label>
          </div>
        </div>
      </div>

      <!-- SEO Settings -->
      <div v-if="activeTab === 'seo'">
        <h3 class="font-display text-2xl tracking-tighter mb-6 flex items-center gap-3">
          <Search size="24" class="text-construct-red" />
          {{ t('admin_settings_seo') }}
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_meta_title') }}</label>
            <input
              v-model="settings.seo.metaTitle"
              type="text"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
          </div>
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_meta_description') }}</label>
            <textarea
              v-model="settings.seo.metaDescription"
              rows="2"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none resize-none',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            ></textarea>
          </div>
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_google_analytics') }}</label>
            <input
              v-model="settings.seo.googleAnalytics"
              type="text"
              placeholder="UA-XXXXXXXXX-X"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
              ]"
            />
          </div>
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_canonical_url') }}</label>
            <input
              v-model="settings.seo.canonicalUrl"
              type="text"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
          </div>
        </div>
      </div>

      <!-- Performance Settings -->
      <div v-if="activeTab === 'performance'">
        <h3 class="font-display text-2xl tracking-tighter mb-6 flex items-center gap-3">
          <Zap size="24" class="text-construct-red" />
          {{ t('admin_settings_performance') }}
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <label class="flex items-center gap-3 cursor-pointer">
              <div :class="['w-12 h-6 rounded-full relative transition-colors', settings.performance.enableCache ? 'bg-construct-red' : 'bg-gray-600']">
                <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', settings.performance.enableCache ? 'left-7' : 'left-1']"></div>
              </div>
              <span class="font-bold tracking-wider">{{ t('admin_enable_cache') }}</span>
              <input
                v-model="settings.performance.enableCache"
                type="checkbox"
                class="hidden"
              />
            </label>
          </div>
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_cache_duration') }} ({{ t('admin_seconds') }})</label>
            <input
              v-model.number="settings.performance.cacheDuration"
              type="number"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
          </div>
          <div>
            <label class="flex items-center gap-3 cursor-pointer">
              <div :class="['w-12 h-6 rounded-full relative transition-colors', settings.performance.enableMinification ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', settings.performance.enableMinification ? 'left-7' : 'left-1']"></div>
              </div>
              <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_minification') }}</span>
              <input
                v-model="settings.performance.enableMinification"
                type="checkbox"
                class="hidden"
              />
            </label>
          </div>
          <div>
            <label class="flex items-center gap-3 cursor-pointer">
              <div :class="['w-12 h-6 rounded-full relative transition-colors', settings.performance.lazyLoadImages ? 'bg-construct-red' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', settings.performance.lazyLoadImages ? 'left-7' : 'left-1']"></div>
              </div>
              <span :class="['font-bold tracking-wider', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_lazy_load_images') }}</span>
              <input
                v-model="settings.performance.lazyLoadImages"
                type="checkbox"
                class="hidden"
              />
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
