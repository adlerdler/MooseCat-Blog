<script setup>
/**
 * Index.vue - 管理后台首页（Dashboard）
 * 
 * 功能说明：
 * - 网站内容管理后台的首页/控制面板
 * - 展示关键统计数据（文章数、视频数、项目数、资源数）
 * - 显示最近活动日志
 * - 展示流量分析图表
 * - 展示内容统计和热门页面
 */
import { ref, computed, onMounted, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  FileText,
  Play,
  FolderKanban,
  Eye,
  Plus,
  Edit3,
  Trash2,
  TrendingUp,
  Activity,
  BarChart3,
  Users,
  Calendar
} from 'lucide-vue-next';
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { BarChart, LineChart } from 'echarts/charts';
import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  GridComponent
} from 'echarts/components';
import VChart from 'vue-echarts';
import { POSTS } from '../../data/posts';
import { VIDEOS } from '../../data/videos';
import { PROJECTS } from '../../data/projects';
import { useTheme } from '../../composables/useTheme';

use([
  CanvasRenderer,
  BarChart,
  LineChart,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  GridComponent
]);

const { t } = useI18n();
const { isDarkMode } = useTheme();

const timeRange = ref('7d');

const stats = [
  { 
    label: t('admin_total_posts'), 
    value: POSTS.length, 
    change: '+12%',
    icon: FileText
  },
  { 
    label: t('admin_total_videos'), 
    value: VIDEOS.length, 
    change: '+8%',
    icon: Play
  },
  { 
    label: t('admin_total_projects'), 
    value: PROJECTS.length, 
    change: '+15%',
    icon: FolderKanban
  },
  { 
    label: t('admin_total_resources'), 
    value: '2.4K', 
    change: '+23%',
    icon: Eye
  }
];

const recentPosts = computed(() => POSTS.slice(0, 5));

const activityLog = [
  { action: 'New post published', target: 'THE GEOMETRY OF PERCEPTION', time: '2 hours ago', type: 'post' },
  { action: 'Video uploaded', target: 'Constructivist Design Principles', time: '5 hours ago', type: 'video' },
  { action: 'Project updated', target: 'Digital Archive System', time: '1 day ago', type: 'project' },
  { action: 'New comment approved', target: 'By Anonymous', time: '1 day ago', type: 'comment' },
  { action: 'User registered', target: 'New Member', time: '2 days ago', type: 'user' },
];

const trafficData = [
  { day: 'Mon', visits: 3200, unique: 2800 },
  { day: 'Tue', visits: 3800, unique: 3400 },
  { day: 'Wed', visits: 4500, unique: 3900 },
  { day: 'Thu', visits: 5200, unique: 4600 },
  { day: 'Fri', visits: 4800, unique: 4200 },
  { day: 'Sat', visits: 6100, unique: 5400 },
  { day: 'Sun', visits: 5600, unique: 4900 },
];

const contentStats = [
  { label: 'Total Posts', value: 156, icon: FileText, color: 'bg-construct-red' },
  { label: 'Total Videos', value: 48, icon: Play, color: 'bg-blue-500' },
  { label: 'Total Projects', value: 32, icon: FolderKanban, color: 'bg-green-500' },
  { label: 'Total Comments', value: 892, icon: Users, color: 'bg-purple-500' },
];

const topPages = [
  { page: '/blog/the-geometry-of-perception', views: 1234, percentage: 35 },
  { page: '/projects/digital-archive', views: 892, percentage: 25 },
  { page: '/videos/constructivist-design', views: 654, percentage: 18 },
  { page: '/blog/computational-thinking', views: 432, percentage: 12 },
  { page: '/resources/code-snippets', views: 321, percentage: 10 },
];

const trafficSources = [
  { source: 'Direct', percentage: 45, color: 'bg-construct-red' },
  { source: 'Google', percentage: 30, color: 'bg-blue-500' },
  { source: 'Social Media', percentage: 15, color: 'bg-green-500' },
  { source: 'Referral', percentage: 10, color: 'bg-purple-500' },
];

const trafficChartOption = computed(() => ({
  tooltip: {
    trigger: 'axis',
    backgroundColor: isDarkMode.value ? '#1f2937' : '#ffffff',
    borderColor: isDarkMode.value ? '#374151' : '#e5e7eb',
    textStyle: {
      color: isDarkMode.value ? '#ffffff' : '#111827',
    },
  },
  legend: {
    data: ['Total Visits', 'Unique Visitors'],
    textStyle: {
      color: isDarkMode.value ? '#9ca3af' : '#6b7280',
    },
    bottom: 0,
  },
  grid: {
    left: '3%',
    right: '4%',
    bottom: '15%',
    top: '10%',
    containLabel: true,
  },
  xAxis: {
    type: 'category',
    data: trafficData.map(d => d.day),
    axisLine: {
      lineStyle: {
        color: isDarkMode.value ? '#374151' : '#e5e7eb',
      },
    },
    axisLabel: {
      color: isDarkMode.value ? '#9ca3af' : '#6b7280',
    },
  },
  yAxis: {
    type: 'value',
    axisLine: {
      lineStyle: {
        color: isDarkMode.value ? '#374151' : '#e5e7eb',
      },
    },
    axisLabel: {
      color: isDarkMode.value ? '#9ca3af' : '#6b7280',
    },
    splitLine: {
      lineStyle: {
        color: isDarkMode.value ? '#374151' : '#f3f4f6',
      },
    },
  },
  series: [
    {
      name: 'Total Visits',
      type: 'bar',
      data: trafficData.map(d => d.visits),
      itemStyle: {
        color: '#dc2626',
        borderRadius: [4, 4, 0, 0],
      },
      barWidth: '30%',
    },
    {
      name: 'Unique Visitors',
      type: 'bar',
      data: trafficData.map(d => d.unique),
      itemStyle: {
        color: isDarkMode.value ? '#4b5563' : '#9ca3af',
        borderRadius: [4, 4, 0, 0],
      },
      barWidth: '30%',
    },
  ],
}));
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <h2 :class="['font-display text-4xl tracking-tighter mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">CONTROL PANEL</h2>
      <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">System Overview & Analytics</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div 
        v-for="stat in stats" 
        :key="stat.label"
        :class="[
          'p-6 hover:border-construct-red transition-colors',
          isDarkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'
        ]"
      >
        <div class="flex items-center justify-between mb-4">
          <component 
            :is="stat.icon" 
            :class="[
              stat.icon === FileText ? (isDarkMode ? 'text-construct-red' : 'text-red-600') :
              stat.icon === Play ? (isDarkMode ? 'text-blue-400' : 'text-blue-600') :
              stat.icon === FolderKanban ? (isDarkMode ? 'text-green-400' : 'text-green-600') :
              (isDarkMode ? 'text-purple-400' : 'text-purple-600')
            ]" 
            size="24" 
          />
          <span :class="['text-xs font-bold', isDarkMode ? 'text-green-400' : 'text-green-600']">{{ stat.change }}</span>
        </div>
        <div :class="['font-display text-4xl mb-1', isDarkMode ? 'text-white' : 'text-gray-900']">{{ stat.value }}</div>
        <div :class="['text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ stat.label }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent Posts -->
      <div :class="['lg:col-span-2 p-6 border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <div class="flex items-center justify-between mb-6">
          <h3 :class="['font-display text-xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">RECENT POSTS</h3>
          <button class="flex items-center gap-2 px-4 py-2 bg-construct-red text-white font-bold tracking-widest uppercase text-xs hover:bg-red-700 transition-colors rounded">
            <Plus size="16" class="!text-white" /> ADD NEW
          </button>
        </div>
        <div class="space-y-4">
          <div 
            v-for="post in recentPosts" 
            :key="post.id"
            :class="[
              'flex items-center justify-between p-4 transition-colors',
              isDarkMode ? 'bg-gray-700/50 hover:bg-gray-700' : 'bg-gray-50 hover:bg-gray-100'
            ]"
          >
            <div class="flex-1">
              <h4 :class="['font-display text-lg tracking-tighter mb-1', isDarkMode ? 'text-white' : 'text-gray-900']">{{ post.title }}</h4>
              <div :class="['flex items-center gap-4 text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                <span>{{ post.category }}</span>
                <span>{{ post.date }}</span>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button :class="['p-2 transition-colors', isDarkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-200']">
                <Edit3 :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'" size="16" />
              </button>
              <button :class="['p-2 transition-colors', isDarkMode ? 'hover:bg-red-500/20' : 'hover:bg-red-50']">
                <Trash2 :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'" size="16" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Activity Log -->
      <div :class="['p-6 border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <h3 :class="['font-display text-xl tracking-tighter mb-6', isDarkMode ? 'text-white' : 'text-gray-900']">ACTIVITY LOG</h3>
        <div class="space-y-4">
          <div 
            v-for="(activity, index) in activityLog" 
            :key="index"
            class="flex items-start gap-3"
          >
            <div class="w-2 h-2 rounded-full mt-2" :class="{
              'bg-construct-red': activity.type === 'post',
              'bg-blue-500': activity.type === 'video',
              'bg-green-500': activity.type === 'project',
              'bg-yellow-500': activity.type === 'comment',
              'bg-purple-500': activity.type === 'user'
            }"></div>
            <div>
              <p :class="['text-sm font-medium', isDarkMode ? 'text-white' : 'text-gray-900']">{{ activity.action }}</p>
              <p :class="['text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ activity.target }} - {{ activity.time }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Analytics Chart Placeholder -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <!-- Traffic Chart -->
      <div :class="['lg:col-span-2 p-6 border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <div class="flex items-center justify-between mb-6">
          <h3 :class="['font-display text-xl tracking-tighter flex items-center gap-3', isDarkMode ? 'text-white' : 'text-gray-900']">
            <TrendingUp size="24" class="text-construct-red" />
            TRAFFIC OVERVIEW
          </h3>
          <div class="flex items-center gap-2">
            <Calendar :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="16" />
            <select
              v-model="timeRange"
              :class="[
                'px-3 py-1 text-sm focus:border-construct-red focus:outline-none border',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            >
              <option value="7d">7 DAYS</option>
              <option value="30d">30 DAYS</option>
              <option value="90d">90 DAYS</option>
            </select>
          </div>
        </div>
        <div class="h-64">
          <v-chart :option="trafficChartOption" autoresize />
        </div>
      </div>

      <!-- Content Stats -->
      <div :class="['p-6 border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <h3 :class="['font-display text-xl tracking-tighter mb-6 flex items-center gap-3', isDarkMode ? 'text-white' : 'text-gray-900']">
          <BarChart3 size="24" class="text-construct-red" />
          CONTENT STATS
        </h3>
        <div class="space-y-4">
          <div 
            v-for="item in contentStats" 
            :key="item.label"
            :class="['flex items-center justify-between p-3 rounded-lg', isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50']"
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
      <div :class="['p-6 border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <h3 :class="['font-display text-xl tracking-tighter mb-6', isDarkMode ? 'text-white' : 'text-gray-900']">TOP PAGES</h3>
        <div class="space-y-4">
          <div 
            v-for="(page, index) in topPages" 
            :key="page.page"
            class="flex items-center gap-4"
          >
            <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold', isDarkMode ? 'bg-gray-700 text-white' : 'bg-gray-100 text-gray-900']">
              {{ index + 1 }}
            </div>
            <div class="flex-1">
              <div :class="['text-sm font-medium truncate', isDarkMode ? 'text-white' : 'text-gray-900']">{{ page.page }}</div>
              <div :class="['text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ page.views }} views</div>
            </div>
            <div class="text-sm font-bold text-construct-red">{{ page.percentage }}%</div>
          </div>
        </div>
      </div>

      <!-- Traffic Sources -->
      <div :class="['p-6 border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <h3 :class="['font-display text-xl tracking-tighter mb-6', isDarkMode ? 'text-white' : 'text-gray-900']">TRAFFIC SOURCES</h3>
        <div class="space-y-4">
          <div 
            v-for="source in trafficSources" 
            :key="source.source"
          >
            <div class="flex items-center justify-between mb-2">
              <span :class="['text-sm font-medium', isDarkMode ? 'text-white' : 'text-gray-900']">{{ source.source }}</span>
              <span class="text-sm font-bold text-construct-red">{{ source.percentage }}%</span>
            </div>
            <div :class="['h-2 rounded-full overflow-hidden', isDarkMode ? 'bg-gray-700' : 'bg-gray-200']">
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
