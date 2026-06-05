<script setup>
/**
 * Admin Dashboard Index Page
 *
 * Features:
 * - Displays key statistics (posts, videos, projects, resources)
 * - Traffic analytics charts using ECharts
 * - Recent posts and activities
 * - User growth statistics
 * - Category distribution pie chart
 * - Post trend line chart
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  FileText,
  Play,
  FolderKanban,
  TrendingUp,
  Activity,
  BarChart3,
  Users,
  Calendar,
  UserPlus,
  Bell
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
import { useTheme } from '../../composables/useTheme';
import { createAdminStats } from '../../composables/useAdminStats';

const props = defineProps({
  posts: { type: Array, default: () => [] },
  videos: { type: Array, default: () => [] },
  projects: { type: Array, default: () => [] },
  users: { type: Array, default: () => [] },
  logs: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
  comments: { type: Array, default: () => [] },
  resources: { type: Array, default: () => [] },
  visits: { type: Array, default: () => [] },
  dailyTraffic: { type: Array, default: () => [] },
  userLevels: { type: Array, default: () => [] },
  roles: { type: Array, default: () => [] },
  tags: { type: Array, default: () => [] },
  taggables: { type: Array, default: () => [] },
  periodChanges: { type: Object, default: () => ({}) },
});

const {
  getTrafficData,
  getUserStats,
  getPostTrendData,
  getCategoryDistribution,
  getUserGrowthData,
  getContentStats
} = createAdminStats({
  posts: props.posts,
  videos: props.videos,
  projects: props.projects,
  users: props.users,
  categories: props.categories,
  comments: props.comments,
  resources: props.resources,
  visits: props.visits,
  userLevels: props.userLevels,
  roles: props.roles,
  tags: props.tags,
  taggables: props.taggables,
  periodChanges: props.periodChanges
});

const timeRanges = [
  { value: '7d', label: '7 DAYS' },
  { value: '30d', label: '30 DAYS' },
  { value: '90d', label: '90 DAYS' },
  { value: '1y', label: '1 YEAR' },
];

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

const i18nLabels = {
  'Total Posts': 'admin_total_posts',
  'Total Videos': 'admin_total_videos',
  'Total Projects': 'admin_total_projects',
  'Total Resources': 'admin_total_resources',
};

const stats = computed(() =>
  getContentStats().map(stat => ({
    label: t(i18nLabels[stat.label] || stat.label),
    value: stat.value,
    change: stat.change,
    icon: iconMap[stat.icon_key] || FileText
  }))
);

const recentPosts = computed(() => (props.posts || []).slice(0, 5));

const iconMap = {
  fileText: FileText,
  play: Play,
  folderKanban: FolderKanban,
  users: Users,
  activity: Activity,
  userPlus: UserPlus,
  bell: Bell
};

const userStatsWithIcons = computed(() => {
  return getUserStats().map(item => ({
    ...item,
    icon: iconMap[item.icon_key] || Users
  }));
});

const trafficChartData = computed(() => {
  // 优先使用后端聚合数据（后端已用配置时区生成完整 90 天填充序列）
  if (props.dailyTraffic && props.dailyTraffic.length > 0) {
    const daysBack = timeRange.value === '1y' ? 365
      : timeRange.value === '90d' ? 90
      : timeRange.value === '30d' ? 30
      : 7;

    // 后端已按配置时区填充完整日期，前端直接切片，不再用浏览器时区生成
    const sliceStart = Math.max(0, props.dailyTraffic.length - daysBack - 1);
    return props.dailyTraffic.slice(sliceStart).map(row => ({
      day: row.day,
      visits: parseInt(row.visits) || 0,
      unique: parseInt(row.unique) || 0,
    }));
  }

  // 兜底：用原 visits 数据客户端计算
  return getTrafficData(timeRange.value);
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
    data: trafficChartData.value.map(d => d.day),
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
      data: trafficChartData.value.map(d => d.visits),
      itemStyle: {
        color: '#dc2626',
        borderRadius: [4, 4, 0, 0],
      },
      barWidth: '30%',
    },
    {
      name: 'Unique Visitors',
      type: 'bar',
      data: trafficChartData.value.map(d => d.unique),
      itemStyle: {
        color: isDarkMode.value ? '#4b5563' : '#9ca3af',
        borderRadius: [4, 4, 0, 0],
      },
      barWidth: '30%',
    },
  ],
}));

const postTrendChartData = computed(() => getPostTrendData());

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
    data: postTrendChartData.value.map(d => d.month),
    axisLine: {
      lineStyle: {
        color: isDarkMode.value ? '#374151' : '#e5e7eb',
      },
    },
    axisLabel: {
      color: isDarkMode.value ? '#9ca3af' : '#6b7280',
    },
  },
  yAxis: [
    {
      type: 'value',
      name: t('chart_posts'),
      nameTextStyle: { color: isDarkMode.value ? '#9ca3af' : '#6b7280' },
      axisLine: { lineStyle: { color: isDarkMode.value ? '#374151' : '#e5e7eb' } },
      axisLabel: { color: isDarkMode.value ? '#9ca3af' : '#6b7280' },
      splitLine: { lineStyle: { color: isDarkMode.value ? '#374151' : '#f3f4f6' } },
    },
    {
      type: 'value',
      name: t('chart_views'),
      nameTextStyle: { color: isDarkMode.value ? '#9ca3af' : '#6b7280' },
      axisLine: { lineStyle: { color: isDarkMode.value ? '#374151' : '#e5e7eb' } },
      axisLabel: { color: isDarkMode.value ? '#9ca3af' : '#6b7280' },
      splitLine: { show: false },
    },
  ],
  series: [
    {
      name: t('chart_posts'),
      type: 'bar',
      yAxisIndex: 0,
      data: postTrendChartData.value.map(d => d.posts),
      itemStyle: {
        color: '#dc2626',
        borderRadius: [4, 4, 0, 0],
      },
      barWidth: '30%',
    },
    {
      name: t('chart_views'),
      type: 'line',
      yAxisIndex: 1,
      data: postTrendChartData.value.map(d => d.views),
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

const userGrowthChartData = computed(() => getUserGrowthData());

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
    data: userGrowthChartData.value.map(d => d.month),
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
      data: userGrowthChartData.value.map(d => d.users),
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
      data: userGrowthChartData.value.map(d => d.newUsers),
      itemStyle: {
        color: '#16a34a',
        borderRadius: [4, 4, 0, 0],
      },
      barWidth: '40%',
    },
  ],
}));

const categoryPieChartData = computed(() => getCategoryDistribution());

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
      data: categoryPieChartData.value.map(d => ({
        name: d.name,
        value: d.value,
        itemStyle: { color: d.color },
      })),
    },
  ],
}));
</script>

<template>
  <div class="p-8 space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div
        v-for="stat in stats"
        :key="stat.label"
        class="group p-6 rounded-xl backdrop-blur-xl bg-white/60 dark:bg-gray-900/40 border border-white/30 dark:border-gray-700/30 shadow-lg hover:shadow-xl hover:border-construct-red/50 transition-all duration-300 hover:-translate-y-1"
      >
        <div class="flex items-center justify-between mb-4">
          <component
            :is="stat.icon"
            :class="[
              stat.icon === FileText ? 'text-construct-red' :
              stat.icon === Play ? 'text-blue-500' :
              stat.icon === FolderKanban ? 'text-green-500' :
              'text-purple-500'
            ]"
            size="24"
          />
          <span class="text-xs font-bold text-green-500">{{ stat.change }}</span>
        </div>
        <div class="font-display text-4xl mb-1 text-gray-900 dark:text-white">{{ stat.value }}</div>
        <div class="text-xs font-bold tracking-widest uppercase text-gray-500 dark:text-gray-400">{{ stat.label }}</div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Post Trend Chart -->
      <div class="p-6 rounded-xl backdrop-blur-xl bg-white/60 dark:bg-gray-900/40 border border-white/30 dark:border-gray-700/30 shadow-lg">
        <div class="flex items-center justify-between mb-6">
          <h3 class="font-display text-xl tracking-tighter flex items-center gap-3 text-gray-900 dark:text-white">
            <TrendingUp size="24" class="text-construct-red" />
            {{ t('chart_post_trends') }}
          </h3>
        </div>
        <div class="h-64">
          <v-chart :option="postTrendChartOption" autoresize />
        </div>
      </div>

      <!-- User Growth Chart -->
      <div class="p-6 rounded-xl backdrop-blur-xl bg-white/60 dark:bg-gray-900/40 border border-white/30 dark:border-gray-700/30 shadow-lg">
        <div class="flex items-center justify-between mb-6">
          <h3 class="font-display text-xl tracking-tighter flex items-center gap-3 text-gray-900 dark:text-white">
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Traffic Chart -->
      <div class="lg:col-span-2 p-6 rounded-xl backdrop-blur-xl bg-white/60 dark:bg-gray-900/40 border border-white/30 dark:border-gray-700/30 shadow-lg">
        <div class="flex items-center justify-between mb-6">
          <h3 class="font-display text-xl tracking-tighter flex items-center gap-3 text-gray-900 dark:text-white">
            <TrendingUp size="24" class="text-construct-red" />
            {{ t('chart_traffic_overview') }}
          </h3>
          <div class="flex items-center gap-2">
            <Calendar class="text-gray-500 dark:text-gray-400" size="16" />
            <select
              v-model="timeRange"
              class="px-3 py-1 text-sm rounded-lg backdrop-blur-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-600 focus:border-construct-red focus:outline-none text-gray-900 dark:text-white transition-colors"
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
      <div class="p-6 rounded-xl backdrop-blur-xl bg-white/60 dark:bg-gray-900/40 border border-white/30 dark:border-gray-700/30 shadow-lg">
        <h3 class="font-display text-xl tracking-tighter mb-6 flex items-center gap-3 text-gray-900 dark:text-white">
          <BarChart3 size="24" class="text-construct-red" />
          {{ t('chart_content_distribution') }}
        </h3>
        <div class="h-64">
          <v-chart :option="categoryPieChartOption" autoresize />
        </div>
      </div>
    </div>
  </div>
</template>