<script setup>
/**
 * AdminLogs.vue - 操作日志页面
 * 
 * 功能说明：
 * - 查看系统操作日志
 * - 日志列表展示（操作类型、用户、IP、详情、时间）
 * - 日志搜索和筛选
 */
import {
  ref,
  computed,
  useI18n,
  useTheme,
  formatToShort,
  FileText,
  Search,
  ChevronLeft,
  ChevronRight,
  Filter,
  User,
  Clock,
  Monitor,
  Info,
  adminLogs
} from '../../composables/useAdminImports';

const { t, locale } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const actionFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = 10;

const logs = ref([...adminLogs]);

const actionTypes = computed(() => {
  const types = [...new Set(logs.value.map(log => log.action))];
  return types;
});

const filteredLogs = computed(() => {
  return logs.value.filter(log => {
    const matchesSearch = log.user.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         log.details.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         log.ip.includes(searchQuery.value);
    const matchesAction = actionFilter.value === 'all' || log.action === actionFilter.value;
    return matchesSearch && matchesAction;
  });
});

const totalPages = computed(() => Math.ceil(filteredLogs.value.length / itemsPerPage));

const paginatedLogs = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredLogs.value.slice(start, start + itemsPerPage);
});

const getActionColor = (action) => {
  if (isDarkMode.value) {
    const colors = {
      user_login: 'text-green-400',
      post_create: 'text-blue-400',
      post_edit: 'text-yellow-400',
      post_delete: 'text-red-400',
      user_update: 'text-purple-400',
      role_change: 'text-pink-400',
      comment_delete: 'text-red-400',
      settings_update: 'text-cyan-400',
      category_create: 'text-blue-400',
      media_upload: 'text-orange-400'
    };
    return colors[action] || 'text-gray-400';
  } else {
    const colors = {
      user_login: 'text-green-600',
      post_create: 'text-blue-600',
      post_edit: 'text-yellow-600',
      post_delete: 'text-red-600',
      user_update: 'text-purple-600',
      role_change: 'text-pink-600',
      comment_delete: 'text-red-600',
      settings_update: 'text-cyan-600',
      category_create: 'text-blue-600',
      media_upload: 'text-orange-600'
    };
    return colors[action] || 'text-gray-600';
  }
};

const getActionIcon = (action) => {
  const icons = {
    user_login: User,
    post_create: FileText,
    post_edit: FileText,
    post_delete: FileText,
    user_update: User,
    role_change: Info,
    comment_delete: FileText,
    settings_update: Info,
    category_create: FileText,
    media_upload: Monitor
  };
  return icons[action] || Info;
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center gap-4 mb-2">
        <FileText class="text-construct-red" size="32" />
        <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_logs') }}</h2>
      </div>
      <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">System operation logs</p>
    </div>

    <!-- Search and Filter -->
    <div class="flex flex-col md:flex-row gap-4 mb-8">
      <div class="flex-1 relative">
        <Search :class="['absolute left-4 top-1/2 -translate-y-1/2', isDarkMode ? 'text-gray-400' : 'text-gray-500']" size="20" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('admin_search_logs')"
          :class="[
            'w-full pl-12 pr-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
        />
      </div>
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
          <Filter :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="18" />
          <select
            v-model="actionFilter"
            :class="[
              'px-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
              isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option value="all">{{ t('admin_all_actions') }}</option>
            <option v-for="action in actionTypes" :key="action" :value="action">
              {{ t(action) }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Logs List -->
    <div class="space-y-4">
      <div 
        v-for="log in paginatedLogs" 
        :key="log.id"
        :class="[
          'p-6 border transition-all hover:border-construct-red',
          isDarkMode ? 'bg-gray-800/50 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <div class="flex items-start gap-4">
          <div :class="['p-3 rounded-full', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
            <component :is="getActionIcon(log.action)" :class="getActionColor(log.action)" size="24" />
          </div>
          <div class="flex-1">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center gap-3">
                <span :class="['font-bold text-lg', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t(log.action) }}</span>
                <span :class="['px-2 py-1 text-xs font-bold uppercase rounded', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']">
                  {{ log.user }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <Clock :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="16" />
                <span :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ formatToShort(log.createdAt) }}</span>
              </div>
            </div>
            <p :class="['mb-3', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ log.details }}</p>
            <div class="flex items-center gap-6 text-sm">
              <div class="flex items-center gap-2">
                <Monitor :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="14" />
                <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ log.ip }}</span>
              </div>
              <div :class="['flex items-center gap-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                <span class="truncate max-w-xs">{{ log.userAgent }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between mt-8">
      <p :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
        {{ t('admin_showing') }} {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredLogs.length) }} {{ t('admin_of') }} {{ filteredLogs.length }}
      </p>
      <div class="flex items-center gap-2">
        <button
          @click="currentPage = Math.max(1, currentPage - 1)"
          :disabled="currentPage === 1"
          :class="[
            'p-3 border transition-colors',
            currentPage === 1
              ? (isDarkMode ? 'border-gray-700 text-gray-600 cursor-not-allowed' : 'border-gray-200 text-gray-400 cursor-not-allowed')
              : (isDarkMode ? 'border-gray-600 text-white hover:border-construct-red hover:text-construct-red' : 'border-gray-300 text-gray-900 hover:border-construct-red hover:text-construct-red')
          ]"
        >
          <ChevronLeft size="20" />
        </button>
        <button
          @click="currentPage = Math.min(totalPages, currentPage + 1)"
          :disabled="currentPage === totalPages || totalPages === 0"
          :class="[
            'p-3 border transition-colors',
            currentPage === totalPages || totalPages === 0
              ? (isDarkMode ? 'border-gray-700 text-gray-600 cursor-not-allowed' : 'border-gray-200 text-gray-400 cursor-not-allowed')
              : (isDarkMode ? 'border-gray-600 text-white hover:border-construct-red hover:text-construct-red' : 'border-gray-300 text-gray-900 hover:border-construct-red hover:text-construct-red')
          ]"
        >
          <ChevronRight size="20" />
        </button>
      </div>
    </div>
  </div>
</template>
