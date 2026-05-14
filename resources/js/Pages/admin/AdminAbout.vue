<script setup>
/**
 * AdminAbout.vue - 关于系统页面
 *
 * 功能说明：
 * - 显示系统基本信息
 * - 开源地址和项目信息
 * - 版权信息
 */
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Info,
  Github,
  Heart,
  Code,
  Star,
  ExternalLink,
  Coffee,
  BookOpen,
  Layers
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const systemInfo = ref({
  name: 'Archyx Blog',
  version: '2.0.0',
  releaseDate: '2026-05-14',
  description: '基于 Laravel + Vue.js 构建的现代化博客系统',
  features: [
    '前后端分离架构',
    '响应式设计',
    '深色/浅色模式切换',
    '角色权限管理',
    '评论系统',
    '多媒体资源管理'
  ],
  techStack: {
    frontend: ['Vue.js 3', 'Vite', 'Tailwind CSS', 'Vue Router', 'Pinia'],
    backend: ['Laravel 11', 'PHP 8.2', 'MySQL', 'Redis'],
    others: ['Nginx', 'Docker', 'Git']
  },
  openSource: {
    github: 'https://github.com/your-username/archyx-blog',
    issues: 'https://github.com/your-username/archyx-blog/issues',
    documentation: 'https://docs.archyx-blog.com'
  },
  developer: {
    name: 'Archyx Team',
    website: 'https://www.archyx.com',
    email: 'admin@archyx.com'
  }
});

const currentYear = new Date().getFullYear();
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center gap-4">
        <Info class="text-construct-red" size="32" />
        <div>
          <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_about') }}</h2>
          <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_about_subtitle') }}</p>
        </div>
      </div>
    </div>

    <!-- System Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
      <!-- Basic Info Card -->
      <div :class="['border p-8', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <div class="flex items-center gap-3 mb-6">
          <Layers size="24" class="text-construct-red" />
          <h3 :class="['font-display text-2xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ systemInfo.name }}</h3>
        </div>

        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <span :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_version') }}</span>
            <span :class="['px-3 py-1 text-sm font-bold', isDarkMode ? 'bg-gray-700 text-construct-red' : 'bg-gray-100 text-construct-red']">
              v{{ systemInfo.version }}
            </span>
          </div>

          <div class="flex items-center justify-between">
            <span :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_release_date') }}</span>
            <span :class="['text-sm', isDarkMode ? 'text-white' : 'text-gray-900']">{{ systemInfo.releaseDate }}</span>
          </div>

          <div class="pt-4 border-t" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
            <p :class="['text-sm leading-relaxed', isDarkMode ? 'text-gray-300' : 'text-gray-600']">{{ systemInfo.description }}</p>
          </div>
        </div>
      </div>

      <!-- Features Card -->
      <div :class="['border p-8', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <div class="flex items-center gap-3 mb-6">
          <Star size="24" class="text-construct-red" />
          <h3 :class="['font-display text-2xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_features') }}</h3>
        </div>

        <ul class="space-y-3">
          <li
            v-for="(feature, index) in systemInfo.features"
            :key="index"
            class="flex items-center gap-3"
          >
            <span class="w-2 h-2 bg-construct-red rounded-full"></span>
            <span :class="['text-sm', isDarkMode ? 'text-gray-300' : 'text-gray-600']">{{ feature }}</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Tech Stack -->
    <div :class="['border p-8 mb-8', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <div class="flex items-center gap-3 mb-6">
        <Code size="24" class="text-construct-red" />
        <h3 :class="['font-display text-2xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_tech_stack') }}</h3>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Frontend -->
        <div>
          <h4 :class="['text-sm font-bold tracking-widest uppercase mb-3', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_frontend') }}</h4>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="tech in systemInfo.techStack.frontend"
              :key="tech"
              :class="['px-3 py-1 text-xs font-bold', isDarkMode ? 'bg-blue-900 text-blue-300' : 'bg-blue-100 text-blue-700']"
            >
              {{ tech }}
            </span>
          </div>
        </div>

        <!-- Backend -->
        <div>
          <h4 :class="['text-sm font-bold tracking-widest uppercase mb-3', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_backend') }}</h4>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="tech in systemInfo.techStack.backend"
              :key="tech"
              :class="['px-3 py-1 text-xs font-bold', isDarkMode ? 'bg-red-900 text-red-300' : 'bg-red-100 text-red-700']"
            >
              {{ tech }}
            </span>
          </div>
        </div>

        <!-- Others -->
        <div>
          <h4 :class="['text-sm font-bold tracking-widest uppercase mb-3', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_infrastructure') }}</h4>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="tech in systemInfo.techStack.others"
              :key="tech"
              :class="['px-3 py-1 text-xs font-bold', isDarkMode ? 'bg-green-900 text-green-300' : 'bg-green-100 text-green-700']"
            >
              {{ tech }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Open Source & Links -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
      <!-- Open Source -->
      <div :class="['border p-8', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <div class="flex items-center gap-3 mb-6">
          <Github size="24" class="text-construct-red" />
          <h3 :class="['font-display text-2xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_open_source') }}</h3>
        </div>

        <div class="space-y-4">
          <a
            :href="systemInfo.openSource.github"
            target="_blank"
            rel="noopener noreferrer"
            :class="[
              'flex items-center justify-between px-4 py-3 border transition-colors group',
              isDarkMode ? 'border-gray-700 hover:border-gray-500 bg-gray-700/50' : 'border-gray-200 hover:border-gray-400 bg-gray-50'
            ]"
          >
            <div class="flex items-center gap-3">
              <Github size="20" />
              <span :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_source_code') }}</span>
            </div>
            <ExternalLink size="18" :class="['transition-transform group-hover:translate-x-1', isDarkMode ? 'text-gray-400' : 'text-gray-500']" />
          </a>

          <a
            :href="systemInfo.openSource.issues"
            target="_blank"
            rel="noopener noreferrer"
            :class="[
              'flex items-center justify-between px-4 py-3 border transition-colors group',
              isDarkMode ? 'border-gray-700 hover:border-gray-500 bg-gray-700/50' : 'border-gray-200 hover:border-gray-400 bg-gray-50'
            ]"
          >
            <div class="flex items-center gap-3">
              <BookOpen size="20" />
              <span :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_docs_feedback') }}</span>
            </div>
            <ExternalLink size="18" :class="['transition-transform group-hover:translate-x-1', isDarkMode ? 'text-gray-400' : 'text-gray-500']" />
          </a>
        </div>
      </div>

      <!-- Developer Info -->
      <div :class="['border p-8', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <div class="flex items-center gap-3 mb-6">
          <Heart size="24" class="text-construct-red" />
          <h3 :class="['font-display text-2xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_developer') }}</h3>
        </div>

        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <span :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_team') }}</span>
            <span :class="['text-sm', isDarkMode ? 'text-white' : 'text-gray-900']">{{ systemInfo.developer.name }}</span>
          </div>

          <div class="flex items-center justify-between">
            <span :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_website') }}</span>
            <a
              :href="systemInfo.developer.website"
              target="_blank"
              rel="noopener noreferrer"
              :class="['text-sm text-construct-red hover:underline']"
            >
              {{ systemInfo.developer.website }}
            </a>
          </div>

          <div class="flex items-center justify-between">
            <span :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_email') }}</span>
            <a
              :href="'mailto:' + systemInfo.developer.email"
              :class="['text-sm text-construct-red hover:underline']"
            >
              {{ systemInfo.developer.email }}
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Copyright -->
    <div :class="['border p-8 text-center', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <div class="flex items-center justify-center gap-2 mb-4">
        <Coffee size="20" class="text-construct-red" />
        <span :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
          Made with passion and code
        </span>
      </div>

      <p :class="['text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-600']">
        © {{ currentYear }} <span class="text-construct-red">{{ systemInfo.developer.name }}</span>.
        {{ t('admin_all_rights_reserved') }}.
      </p>

      <p :class="['text-xs mt-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
        {{ t('admin_copyright_notice') }}
      </p>
    </div>
  </div>
</template>
