<script setup>
/**
 * AdminAnalytics.vue - 数据分析页面
 * 
 * 功能说明：
 * - 展示网站流量和访问统计
 * - 数据可视化图表
 * - 内容统计（文章、视频、项目）
 * - 用户增长趋势
 * - 访问来源分析
 */
import {
  ref,
  computed,
  useI18n,
  useTheme,
  BarChart3,
  TrendingUp,
  Users,
  Eye,
  FileText,
  Play,
  FolderKanban,
  Calendar,
  Clock
} from '../../composables/useAdminImports';
import {
  timeRanges,
  analyticsStats,
  contentStats,
  trafficData,
  topPages,
  trafficSources
} from '../../data/admin';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const timeRange = ref('7d');

const iconMap = {
  eye: Eye,
  users: Users,
  barChart3: BarChart3,
  clock: Clock,
  fileText: FileText,
  play: Play,
  folderKanban: FolderKanban
};

const stats = computed(() => {
  return analyticsStats.map(stat => ({
    ...stat,
    icon: iconMap[stat.icon_key]
  }));
});

const contentStatsWithIcons = computed(() => {
  return contentStats.map(item => ({
    ...item,
    icon: iconMap[item.icon_key]
  }));
});
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <BarChart3 class="text-construct-red" size="32" />
          <div>
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_analytics') }}</h2>
            <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Traffic and performance metrics</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <Calendar :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="18" />
          <select
            v-model="timeRange"
            :class="[
              'px-4 py-2 border focus:border-construct-red focus:outline-none transition-colors',
              isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option v-for="range in timeRanges" :key="range.value" :value="range.value">
              {{ range.label }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div 
        v-for="stat in stats" 
        :key="stat.label"
        :class="[
          'border p-6 hover:border-construct-red transition-colors',
          isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <div class="flex items-center justify-between mb-4">
          <component 
            :is="stat.icon" 
            :class="[
              stat.icon === Eye ? (isDarkMode ? 'text-blue-400' : 'text-blue-600') :
              stat.icon === Users ? (isDarkMode ? 'text-green-400' : 'text-green-600') :
              stat.icon === BarChart3 ? (isDarkMode ? 'text-purple-400' : 'text-purple-600') :
              (isDarkMode ? 'text-yellow-400' : 'text-yellow-600')
            ]" 
            size="24" 
          />
          <span :class="['text-xs font-bold', isDarkMode ? 'text-green-400' : 'text-green-600']">{{ stat.change }}</span>
        </div>
        <div :class="['font-display text-3xl mb-1', isDarkMode ? 'text-white' : 'text-gray-900']">{{ stat.value }}</div>
        <div :class="['text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ stat.label }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <!-- Traffic Chart -->
      <div :class="['lg:col-span-2 border p-6', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <h3 :class="['font-display text-xl tracking-tighter mb-6 flex items-center gap-3', isDarkMode ? 'text-white' : 'text-gray-900']">
          <TrendingUp size="24" class="text-construct-red" />
          TRAFFIC OVERVIEW
        </h3>
        <div class="h-64 flex items-end gap-4">
          <div 
            v-for="(data, index) in trafficData" 
            :key="index"
            class="flex-1 flex flex-col items-center gap-2"
          >
            <div class="w-full flex gap-1">
              <div 
                class="flex-1 bg-construct-red rounded-t transition-all hover:opacity-80" 
                :style="{ height: `${(data.visits / 6500) * 200}px` }"
              ></div>
              <div 
                :class="['flex-1 rounded-t transition-all hover:opacity-80', isDarkMode ? 'bg-gray-600' : 'bg-gray-400']" 
                :style="{ height: `${(data.unique / 6500) * 200}px` }"
              ></div>
            </div>
            <span :class="['text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ data.day }}</span>
          </div>
        </div>
        <div :class="['flex items-center gap-6 mt-4 pt-4 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
          <div class="flex items-center gap-2">
            <div class="w-3 h-3 bg-construct-red rounded"></div>
            <span :class="['text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Total Visits</span>
          </div>
          <div class="flex items-center gap-2">
            <div :class="['w-3 h-3 rounded', isDarkMode ? 'bg-gray-600' : 'bg-gray-400']"></div>
            <span :class="['text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Unique Visitors</span>
          </div>
        </div>
      </div>

      <!-- Content Stats -->
      <div :class="['border p-6', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <h3 :class="['font-display text-xl tracking-tighter mb-6', isDarkMode ? 'text-white' : 'text-gray-900']">CONTENT STATS</h3>
        <div class="space-y-4">
          <div 
            v-for="item in contentStatsWithIcons" 
            :key="item.label"
            :class="['flex items-center justify-between p-4 rounded-lg', isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50']"
          >
            <div class="flex items-center gap-3">
              <div :class="['w-10 h-10 rounded-lg flex items-center justify-center', item.color]">
                <component :is="item.icon" size="18" class="text-white" />
              </div>
              <div>
                <div :class="['font-display text-2xl', isDarkMode ? 'text-white' : 'text-gray-900']">{{ item.value }}</div>
                <div :class="['text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ item.label }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Top Pages -->
      <div class="bg-gray-800 border border-gray-700 p-6">
        <h3 class="font-display text-xl tracking-tighter mb-6">TOP PAGES</h3>
        <div class="space-y-4">
          <div 
            v-for="(page, index) in topPages" 
            :key="page.page"
            class="flex items-center gap-4"
          >
            <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center text-sm font-bold">
              {{ index + 1 }}
            </div>
            <div class="flex-1">
              <div class="text-sm font-medium truncate">{{ page.page }}</div>
              <div class="text-xs text-gray-400">{{ page.views }} views</div>
            </div>
            <div class="text-sm font-bold text-construct-red">{{ page.percentage }}%</div>
          </div>
        </div>
      </div>

      <!-- Traffic Sources -->
      <div class="bg-gray-800 border border-gray-700 p-6">
        <h3 class="font-display text-xl tracking-tighter mb-6">TRAFFIC SOURCES</h3>
        <div class="space-y-4">
          <div 
            v-for="source in trafficSources" 
            :key="source.source"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium">{{ source.source }}</span>
              <span class="text-sm font-bold text-construct-red">{{ source.percentage }}%</span>
            </div>
            <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
              <div 
                :class="['h-full rounded-full', source.color]"
                :style="{ width: `${source.percentage}%` }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
