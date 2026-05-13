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
  Calendar,
  UserPlus,
  Bell,
  Lightbulb,
  TrendingDown
} from 'lucide-vue-next';
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { BarChart, LineChart, PieChart } from 'echarts/charts';
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
import {
  activityLog,
  trafficData,
  contentStats,
  topPages,
  trafficSources,
  userStats,
  postTrendData,
  categoryDistribution,
  userGrowthData,
  designRecommendations
} from '../../data/admin';

use([
  CanvasRenderer,
  BarChart,
  LineChart,
  PieChart,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  GridComponent
]);

const { t } = useI18n();
const { isDarkMode } = useTheme();

const timeRange = ref('7d');

const stats = computed(() => [
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
]);

const recentPosts = computed(() => POSTS.slice(0, 5));

const iconMap = {
  fileText: FileText,
  play: Play,
  folderKanban: FolderKanban,
  users: Users,
  activity: Activity,
  userPlus: UserPlus,
  bell: Bell
};

const contentStatsWithIcons = computed(() => {
  return contentStats.map(item => ({
    ...item,
    icon: iconMap[item.iconKey]
  }));
});

const userStatsWithIcons = computed(() => {
  return userStats.map(item => ({
    ...item,
    icon: iconMap[item.iconKey] || Users
  }));
});

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
    outerBounds: { containLabel: true },
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

const postTrendChartOption = computed(() => ({
  tooltip: {
    trigger: 'axis',
    backgroundColor: isDarkMode.value ? '#1f2937' : '#ffffff',
    borderColor: isDarkMode.value ? '#374151' : '#e5e7eb',
    textStyle: {
      color: isDarkMode.value ? '#ffffff' : '#111827',
    },
  },
  legend: {
    data: [t('chart_posts'), t('chart_views')],
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
    outerBounds: { containLabel: true },
  },
  xAxis: {
    type: 'category',
    data: postTrendData.map(d => d.month),
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
      name: t('chart_posts'),
      type: 'bar',
      data: postTrendData.map(d => d.posts),
      itemStyle: {
        color: '#dc2626',
        borderRadius: [4, 4, 0, 0],
      },
      barWidth: '30%',
    },
    {
      name: t('chart_views'),
      type: 'line',
      data: postTrendData.map(d => d.views),
      smooth: true,
      itemStyle: {
        color: '#f59e0b',
      },
      lineStyle: {
        width: 3,
      },
      areaStyle: {
        color: isDarkMode.value ? 'rgba(245, 158, 11, 0.2)' : 'rgba(245, 158, 11, 0.1)',
      },
    },
  ],
}));

const userGrowthChartOption = computed(() => ({
  tooltip: {
    trigger: 'axis',
    backgroundColor: isDarkMode.value ? '#1f2937' : '#ffffff',
    borderColor: isDarkMode.value ? '#374151' : '#e5e7eb',
    textStyle: {
      color: isDarkMode.value ? '#ffffff' : '#111827',
    },
  },
  legend: {
    data: [t('chart_total_users'), t('chart_new_users')],
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
    outerBounds: { containLabel: true },
  },
  xAxis: {
    type: 'category',
    data: userGrowthData.map(d => d.month),
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
      name: t('chart_total_users'),
      type: 'line',
      data: userGrowthData.map(d => d.users),
      smooth: true,
      itemStyle: {
        color: '#dc2626',
      },
      lineStyle: {
        width: 3,
      },
      areaStyle: {
        color: isDarkMode.value ? 'rgba(220, 38, 38, 0.2)' : 'rgba(220, 38, 38, 0.1)',
      },
    },
    {
      name: t('chart_new_users'),
      type: 'bar',
      data: userGrowthData.map(d => d.newUsers),
      itemStyle: {
        color: '#16a34a',
        borderRadius: [4, 4, 0, 0],
      },
      barWidth: '40%',
    },
  ],
}));

const categoryPieChartOption = computed(() => ({
  tooltip: {
    trigger: 'item',
    backgroundColor: isDarkMode.value ? '#1f2937' : '#ffffff',
    borderColor: isDarkMode.value ? '#374151' : '#e5e7eb',
    textStyle: {
      color: isDarkMode.value ? '#ffffff' : '#111827',
    },
    formatter: '{b}: {c}%',
  },
  legend: {
    orient: 'vertical',
    right: '5%',
    top: 'center',
    textStyle: {
      color: isDarkMode.value ? '#9ca3af' : '#6b7280',
    },
  },
  series: [
    {
      type: 'pie',
      radius: ['45%', '70%'],
      center: ['35%', '50%'],
      avoidLabelOverlap: false,
      itemStyle: {
        borderRadius: 4,
        borderColor: isDarkMode.value ? '#1f2937' : '#ffffff',
        borderWidth: 2,
      },
      label: {
        show: false,
        position: 'center',
      },
      emphasis: {
        label: {
          show: true,
          fontSize: 16,
          fontWeight: 'bold',
          color: isDarkMode.value ? '#ffffff' : '#111827',
        },
      },
      labelLine: {
        show: false,
      },
      data: categoryDistribution.map(d => ({
        name: d.name,
        value: d.value,
        itemStyle: { color: d.color },
      })),
    },
  ],
}));
</script>

<template>
  <div class="p-8">
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

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Post Trend Chart -->
      <div :class="['p-6 border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <div class="flex items-center justify-between mb-6">
          <h3 :class="['font-display text-xl tracking-tighter flex items-center gap-3', isDarkMode ? 'text-white' : 'text-gray-900']">
            <TrendingUp size="24" class="text-construct-red" />
            {{ t('chart_post_trends') }}
          </h3>
        </div>
        <div class="h-64">
          <v-chart :option="postTrendChartOption" autoresize />
        </div>
      </div>

      <!-- User Growth Chart -->
      <div :class="['p-6 border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <div class="flex items-center justify-between mb-6">
          <h3 :class="['font-display text-xl tracking-tighter flex items-center gap-3', isDarkMode ? 'text-white' : 'text-gray-900']">
            <Users size="24" class="text-construct-red" />
            {{ t('chart_user_growth') }}
          </h3>
        </div>
        <div class="h-64">
          <v-chart :option="userGrowthChartOption" autoresize />
        </div>
      </div>
    </div>

    <!-- Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <!-- Traffic Chart -->
      <div :class="['lg:col-span-2 p-6 border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <div class="flex items-center justify-between mb-6">
          <h3 :class="['font-display text-xl tracking-tighter flex items-center gap-3', isDarkMode ? 'text-white' : 'text-gray-900']">
            <TrendingUp size="24" class="text-construct-red" />
            {{ t('chart_traffic_overview') }}
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
              <option value="7d">{{ t('chart_7_days') }}</option>
              <option value="30d">{{ t('chart_30_days') }}</option>
              <option value="90d">{{ t('chart_90_days') }}</option>
            </select>
          </div>
        </div>
        <div class="h-64">
          <v-chart :option="trafficChartOption" autoresize />
        </div>
      </div>

      <!-- Category Distribution -->
      <div :class="['p-6 border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <h3 :class="['font-display text-xl tracking-tighter mb-6 flex items-center gap-3', isDarkMode ? 'text-white' : 'text-gray-900']">
          <BarChart3 size="24" class="text-construct-red" />
          {{ t('chart_content_distribution') }}
        </h3>
        <div class="h-64">
          <v-chart :option="categoryPieChartOption" autoresize />
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Top Pages -->
      <div :class="['p-6 border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <h3 :class="['font-display text-xl tracking-tighter mb-6', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('chart_top_pages') }}</h3>
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
              <div :class="['text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ page.views }} {{ t('chart_views') }}</div>
            </div>
            <div class="text-sm font-bold text-construct-red">{{ page.percentage }}%</div>
          </div>
        </div>
      </div>

      <!-- Design Recommendations -->
      <div :class="['p-6 border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <h3 :class="['font-display text-xl tracking-tighter mb-6 flex items-center gap-3', isDarkMode ? 'text-white' : 'text-gray-900']">
          <Lightbulb size="24" class="text-yellow-500" />
          {{ t('chart_recommendations') }}
        </h3>
        <div class="space-y-4">
          <div 
            v-for="rec in designRecommendations" 
            :key="rec.title"
            :class="[
              'p-4 rounded-lg border-l-4',
              rec.priority === 'high' ? 'border-l-red-500' :
              rec.priority === 'medium' ? 'border-l-yellow-500' :
              'border-l-green-500'
            ]"
          >
            <div class="flex items-start gap-3">
              <div :class="[
                'w-2 h-2 rounded-full mt-2',
                rec.priority === 'high' ? 'bg-red-500' :
                rec.priority === 'medium' ? 'bg-yellow-500' :
                'bg-green-500'
              ]"></div>
              <div>
                <h4 :class="['font-bold text-sm mb-1', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t(rec.titleKey) }}</h4>
                <p :class="['text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t(rec.descKey) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
