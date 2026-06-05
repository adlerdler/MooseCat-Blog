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
  Layers,
  Cpu,
  Globe,
  Terminal,
  ChevronRight,
  ShieldCheck,
  Zap
} from '../../composables/useAdminImports';
import { Motion, AnimatePresence } from 'motion-v';
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
    '响应式设计 & 适配移动端',
    '支持深色/浅色/系统跟随模式',
    '基于 RBAC 的精细化权限管理',
    '高度集成的多媒体资源管理库',
    '智能 SEO 优化 & SSR 友好'
  ],
  techStack: {
    frontend: ['Vue.js 3', 'Vite', 'Tailwind CSS', 'Motion-V', 'Pinia'],
    backend: ['Laravel 11', 'PHP 8.2', 'MySQL 8', 'Redis'],
    others: ['Nginx', 'Docker', 'Git', 'Actions']
  },
  openSource: {
    github: 'https://github.com/archyx/archyx-blog',
    issues: 'https://github.com/archyx/archyx-blog/issues',
    documentation: 'https://docs.archyx.com'
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
  <div class="p-8 max-w-7xl mx-auto">
    <!-- Page Header -->
    <Motion
      :initial="{ opacity: 0, y: -20 }"
      :animate="{ opacity: 1, y: 0 }"
      class="mb-12"
    >
      <div class="flex items-center gap-6">
        <div class="w-16 h-16 rounded-2xl bg-construct-red flex items-center justify-center shadow-lg shadow-construct-red/20 rotate-3">
          <Info class="text-white" size="32" />
        </div>
        <div>
          <h2 :class="['font-display text-5xl tracking-tighter mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_about') }}</h2>
          <p :class="['text-xs font-black tracking-[0.3em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_about_subtitle') }}</p>
        </div>
      </div>
    </Motion>

    <!-- System Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
      <!-- Basic Info Card -->
      <Motion
        :initial="{ opacity: 0, x: -20 }"
        :animate="{ opacity: 1, x: 0 }"
        :transition="{ delay: 0.1 }"
        :class="[
          'relative overflow-hidden p-8 rounded-3xl border lg:col-span-2 group transition-all duration-500',
          isDarkMode 
            ? 'bg-gray-800/40 border-gray-700/50 hover:border-construct-red/30 backdrop-blur-xl' 
            : 'bg-white border-gray-200 hover:border-construct-red/20 shadow-sm hover:shadow-xl'
        ]"
      >
        <div class="absolute top-0 right-0 p-12 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity duration-700">
          <Layers size="160" />
        </div>

        <div class="flex items-center gap-4 mb-8">
          <div :class="['p-3 rounded-xl', isDarkMode ? 'bg-construct-red/10' : 'bg-construct-red/5']">
            <Zap size="24" class="text-construct-red" />
          </div>
          <h3 :class="['font-display text-3xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ systemInfo.name }}</h3>
        </div>

        <div class="space-y-6 relative z-10">
          <div class="flex items-center justify-between">
            <span :class="['text-[10px] font-black tracking-widest uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_version') }}</span>
            <div class="flex items-center gap-2">
              <span :class="['px-4 py-1 text-xs font-black rounded-full border', isDarkMode ? 'bg-construct-red/10 text-construct-red border-construct-red/20' : 'bg-red-50 text-construct-red border-red-100']">
                VERSION {{ systemInfo.version }}
              </span>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <span :class="['text-[10px] font-black tracking-widest uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_release_date') }}</span>
            <span :class="['text-sm font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ systemInfo.releaseDate }}</span>
          </div>

          <div>
            <p :class="['text-base leading-relaxed font-medium', isDarkMode ? 'text-gray-400' : 'text-gray-600']">{{ systemInfo.description }}</p>
          </div>
        </div>
      </Motion>

      <!-- Features Card -->
      <Motion
        :initial="{ opacity: 0, x: 20 }"
        :animate="{ opacity: 1, x: 0 }"
        :transition="{ delay: 0.2 }"
        :class="[
          'relative overflow-hidden p-8 rounded-3xl border transition-all duration-500',
          isDarkMode 
            ? 'bg-gray-800/40 border-gray-700/50 hover:border-construct-red/30 backdrop-blur-xl' 
            : 'bg-white border-gray-200 hover:border-construct-red/20 shadow-sm hover:shadow-xl'
        ]"
      >
        <div class="flex items-center gap-4 mb-8">
          <div :class="['p-3 rounded-xl', isDarkMode ? 'bg-construct-red/10' : 'bg-construct-red/5']">
            <Star size="24" class="text-construct-red" />
          </div>
          <h3 :class="['font-display text-2xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_features') }}</h3>
        </div>

        <ul class="space-y-4">
          <li
            v-for="(feature, index) in systemInfo.features"
            :key="index"
            class="flex items-start gap-3 group/item"
          >
            <div class="mt-1 w-1.5 h-1.5 bg-construct-red rounded-full group-hover/item:scale-150 transition-transform"></div>
            <span :class="['text-sm font-medium', isDarkMode ? 'text-gray-300' : 'text-gray-600']">{{ feature }}</span>
          </li>
        </ul>
      </Motion>
    </div>

    <!-- Tech Stack -->
    <Motion
      :initial="{ opacity: 0, y: 20 }"
      :animate="{ opacity: 1, y: 0 }"
      :transition="{ delay: 0.3 }"
      :class="[
        'p-10 mb-8 rounded-3xl border transition-all duration-500',
        isDarkMode 
          ? 'bg-gray-800/40 border-gray-700/50 hover:border-construct-red/30 backdrop-blur-xl' 
          : 'bg-white border-gray-200 hover:border-construct-red/20 shadow-sm hover:shadow-xl'
      ]"
    >
      <div class="flex items-center gap-4 mb-10">
        <div :class="['p-3 rounded-xl', isDarkMode ? 'bg-construct-red/10' : 'bg-construct-red/5']">
          <Code size="24" class="text-construct-red" />
        </div>
        <h3 :class="['font-display text-3xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_tech_stack') }}</h3>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <!-- Frontend -->
        <div class="space-y-4">
          <div class="flex items-center gap-2 mb-2">
            <Terminal size="14" class="text-blue-500" />
            <h4 :class="['text-[10px] font-black tracking-widest uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_frontend') }}</h4>
          </div>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="tech in systemInfo.techStack.frontend"
              :key="tech"
              :class="[
                'px-4 py-2 text-xs font-bold rounded-xl border transition-all hover:scale-105',
                isDarkMode ? 'bg-blue-500/10 border-blue-500/20 text-blue-400' : 'bg-blue-50 border-blue-100 text-blue-600'
              ]"
            >
              {{ tech }}
            </span>
          </div>
        </div>

        <!-- Backend -->
        <div class="space-y-4">
          <div class="flex items-center gap-2 mb-2">
            <Cpu size="14" class="text-red-500" />
            <h4 :class="['text-[10px] font-black tracking-widest uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_backend') }}</h4>
          </div>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="tech in systemInfo.techStack.backend"
              :key="tech"
              :class="[
                'px-4 py-2 text-xs font-bold rounded-xl border transition-all hover:scale-105',
                isDarkMode ? 'bg-red-500/10 border-red-500/20 text-red-400' : 'bg-red-50 border-red-100 text-red-600'
              ]"
            >
              {{ tech }}
            </span>
          </div>
        </div>

        <!-- Infrastructure -->
        <div class="space-y-4">
          <div class="flex items-center gap-2 mb-2">
            <Globe size="14" class="text-green-500" />
            <h4 :class="['text-[10px] font-black tracking-widest uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_infrastructure') }}</h4>
          </div>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="tech in systemInfo.techStack.others"
              :key="tech"
              :class="[
                'px-4 py-2 text-xs font-bold rounded-xl border transition-all hover:scale-105',
                isDarkMode ? 'bg-green-500/10 border-green-500/20 text-green-400' : 'bg-green-50 border-green-100 text-green-600'
              ]"
            >
              {{ tech }}
            </span>
          </div>
        </div>
      </div>
    </Motion>

    <!-- Links & Developer -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
      <!-- Open Source -->
      <Motion
        :initial="{ opacity: 0, y: 20 }"
        :animate="{ opacity: 1, y: 0 }"
        :transition="{ delay: 0.4 }"
        :class="[
          'p-8 rounded-3xl border transition-all duration-500',
          isDarkMode 
            ? 'bg-gray-800/40 border-gray-700/50 hover:border-construct-red/30 backdrop-blur-xl' 
            : 'bg-white border-gray-200 hover:border-construct-red/20 shadow-sm hover:shadow-xl'
        ]"
      >
        <div class="flex items-center gap-4 mb-8">
          <div :class="['p-3 rounded-xl', isDarkMode ? 'bg-construct-red/10' : 'bg-construct-red/5']">
            <Github size="24" class="text-construct-red" />
          </div>
          <h3 :class="['font-display text-2xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_open_source') }}</h3>
        </div>

        <div class="space-y-4">
          <a
            v-for="(link, key) in [
              { label: t('admin_source_code'), url: systemInfo.openSource.github, icon: Github },
              { label: t('admin_docs_feedback'), url: systemInfo.openSource.issues, icon: BookOpen }
            ]"
            :key="key"
            :href="link.url"
            target="_blank"
            :class="[
              'flex items-center justify-between p-5 rounded-2xl border transition-all group',
              isDarkMode ? 'bg-gray-900/50 border-gray-700/50 hover:border-gray-500' : 'bg-gray-50 border-gray-100 hover:border-gray-300'
            ]"
          >
            <div class="flex items-center gap-4">
              <component :is="link.icon" size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
              <span :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ link.label }}</span>
            </div>
            <ExternalLink size="18" :class="['transition-transform group-hover:translate-x-1 group-hover:-translate-y-1', isDarkMode ? 'text-gray-500' : 'text-gray-400']" />
          </a>
        </div>
      </Motion>

      <!-- Developer Info -->
      <Motion
        :initial="{ opacity: 0, y: 20 }"
        :animate="{ opacity: 1, y: 0 }"
        :transition="{ delay: 0.5 }"
        :class="[
          'p-8 rounded-3xl border transition-all duration-500',
          isDarkMode 
            ? 'bg-gray-800/40 border-gray-700/50 hover:border-construct-red/30 backdrop-blur-xl' 
            : 'bg-white border-gray-200 hover:border-construct-red/20 shadow-sm hover:shadow-xl'
        ]"
      >
        <div class="flex items-center gap-4 mb-8">
          <div :class="['p-3 rounded-xl', isDarkMode ? 'bg-construct-red/10' : 'bg-construct-red/5']">
            <Heart size="24" class="text-construct-red" />
          </div>
          <h3 :class="['font-display text-2xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_developer') }}</h3>
        </div>

        <div class="space-y-6">
          <div class="flex items-center justify-between group/line">
            <span :class="['text-[10px] font-black tracking-widest uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_team') }}</span>
            <span :class="['text-sm font-bold group-hover/line:text-construct-red transition-colors', isDarkMode ? 'text-white' : 'text-gray-900']">{{ systemInfo.developer.name }}</span>
          </div>

          <div class="flex items-center justify-between group/line">
            <span :class="['text-[10px] font-black tracking-widest uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_website') }}</span>
            <a
              :href="systemInfo.developer.website"
              target="_blank"
              :class="['text-sm font-bold text-construct-red hover:underline decoration-2 underline-offset-4']"
            >
              {{ systemInfo.developer.website }}
            </a>
          </div>

          <div class="flex items-center justify-between group/line">
            <span :class="['text-[10px] font-black tracking-widest uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_email') }}</span>
            <a
              :href="'mailto:' + systemInfo.developer.email"
              :class="['text-sm font-bold text-construct-red hover:underline decoration-2 underline-offset-4']"
            >
              {{ systemInfo.developer.email }}
            </a>
          </div>
        </div>
      </Motion>
    </div>

    <!-- Copyright Footer -->
    <Motion
      :initial="{ opacity: 0 }"
      :animate="{ opacity: 1 }"
      :transition="{ delay: 0.6 }"
      :class="[
        'p-12 text-center rounded-3xl border relative overflow-hidden',
        isDarkMode ? 'bg-gray-800/20 border-gray-700/50' : 'bg-gray-50/50 border-gray-100'
      ]"
    >
      <div class="absolute inset-0 bg-gradient-to-b from-construct-red/5 to-transparent opacity-20 pointer-events-none"></div>
      
      <div class="flex items-center justify-center gap-3 mb-6">
        <Coffee size="24" class="text-construct-red animate-bounce" />
        <span :class="['text-xs font-black uppercase tracking-[0.4em] opacity-40', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
          {{ t('admin_made_with_passion') }}
        </span>
      </div>

      <p :class="['text-sm font-black uppercase tracking-widest mb-3', isDarkMode ? 'text-gray-300' : 'text-gray-600']">
        © {{ currentYear }} <span class="text-construct-red tracking-tighter">{{ systemInfo.developer.name }}</span>.
        <span class="opacity-40 ml-2">{{ t('admin_all_rights_reserved') }}.</span>
      </p>

      <div class="flex items-center justify-center gap-4 text-[10px] font-bold uppercase tracking-widest opacity-30 group">
        <ShieldCheck size="12" class="text-construct-red" />
        <span>{{ t('admin_copyright_notice') }}</span>
      </div>
    </Motion>
  </div>
</template>

<style scoped>
.font-display {
  font-family: 'Outfit', sans-serif;
}
</style>
