<script setup>
/**
 * AdminLogs.vue - 操作日志页面
 * 
 * 功能说明：
 * - 查看系统操作日志
 * - 日志列表展示（操作类型、用户、IP、详情、时间）
 * - 日志搜索和筛选（支持模块和动作）
 */
import {
  ref,
  computed,
  useI18n,
  useTheme,
  formatToShort,
  FileText,
  Search,
  Filter,
  User,
  Clock,
  Monitor,
  Info,
  Eye,
  X,
  Zap,
  adminLogs,
  AdminPagination
} from '../../composables/useAdminImports';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const actionFilter = ref('all');
const moduleFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const selectedLog = ref(null);
const showDetailModal = ref(false);

const logs = ref([...adminLogs]);

// 获取所有模块类型
const moduleTypes = computed(() => {
  return ['all', ...new Set(logs.value.map(log => log.module))];
});

// 获取基于模块筛选后的动作类型
const actionTypes = computed(() => {
  let sourceLogs = logs.value;
  if (moduleFilter.value !== 'all') {
    sourceLogs = sourceLogs.filter(log => log.module === moduleFilter.value);
  }
  return ['all', ...new Set(sourceLogs.map(log => log.action))];
});

const filteredLogs = computed(() => {
  return logs.value.filter(log => {
    const matchesSearch = log.user.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         log.details.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         log.ip.includes(searchQuery.value);
    const matchesAction = actionFilter.value === 'all' || log.action === actionFilter.value;
    const matchesModule = moduleFilter.value === 'all' || log.module === moduleFilter.value;
    return matchesSearch && matchesAction && matchesModule;
  });
});

const totalPages = computed(() => Math.ceil(filteredLogs.value.length / itemsPerPage.value));

const paginatedLogs = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredLogs.value.slice(start, start + itemsPerPage.value);
});

const openDetails = (log) => {
  if (log.changes) {
    selectedLog.value = log;
    showDetailModal.value = true;
  }
};

const getActionColor = (action) => {
  if (isDarkMode.value) {
    const colors = {
      user_login: 'text-green-400',
      post_create: 'text-blue-400',
      post_update: 'text-yellow-400',
      post_delete: 'text-red-400',
      user_update: 'text-purple-400',
      role_change: 'text-pink-400',
      comment_delete: 'text-red-400',
      settings_update: 'text-cyan-400',
      ad_update: 'text-orange-400',
      media_delete: 'text-red-400'
    };
    return colors[action] || 'text-gray-400';
  } else {
    const colors = {
      user_login: 'text-green-600',
      post_create: 'text-blue-600',
      post_update: 'text-yellow-600',
      post_delete: 'text-red-600',
      user_update: 'text-purple-600',
      role_change: 'text-pink-600',
      comment_delete: 'text-red-600',
      settings_update: 'text-cyan-600',
      ad_update: 'text-orange-600',
      media_delete: 'text-red-600'
    };
    return colors[action] || 'text-gray-600';
  }
};

const getActionIcon = (action) => {
  const icons = {
    user_login: User,
    post_create: FileText,
    post_update: FileText,
    post_delete: FileText,
    user_update: User,
    role_change: Info,
    comment_delete: FileText,
    settings_update: Info,
    ad_update: Zap,
    media_delete: FileText
  };
  return icons[action] || Info;
};

const formatValue = (val) => {
  if (typeof val === 'boolean') return val ? 'TRUE' : 'FALSE';
  if (val === null || val === undefined) return 'NULL';
  return val;
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
            v-model="moduleFilter"
            :class="[
              'px-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
              isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option v-for="mod in moduleTypes" :key="mod" :value="mod">
              {{ mod === 'all' ? t('admin_all_actions') : mod.toUpperCase() }}
            </option>
          </select>
        </div>
        <div class="flex items-center gap-2">
          <Zap :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="18" />
          <select
            v-model="actionFilter"
            :class="[
              'px-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
              isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option v-for="action in actionTypes" :key="action" :value="action">
              {{ action === 'all' ? t('admin_all_actions') : t(action) }}
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
                <span :class="['px-2 py-0.5 rounded border text-[10px] font-bold uppercase', isDarkMode ? 'border-gray-700 text-gray-500' : 'border-gray-200 text-gray-400']">
                  {{ log.module }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <Clock :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="16" />
                <span :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ formatToShort(log.createdAt) }}</span>
              </div>
            </div>
            <p :class="['mb-3', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ log.details }}</p>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-6 text-sm">
                <div class="flex items-center gap-2">
                  <Monitor :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="14" />
                  <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ log.ip }}</span>
                </div>
                <div :class="['hidden sm:flex items-center gap-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                  <span class="truncate max-w-xs">{{ log.userAgent }}</span>
                </div>
              </div>
              
              <button
                v-if="log.changes"
                @click="openDetails(log)"
                class="flex items-center gap-2 px-3 py-1 bg-construct-red text-white text-xs font-bold transition-colors hover:bg-red-700"
              >
                <Eye size="14" /> {{ t('admin_view') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <AdminPagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredLogs.length"
      v-model:items-per-page="itemsPerPage"
      @update:current-page="currentPage = $event"
    />

    <!-- Detail Modal -->
    <Teleport to="body">
      <div 
        v-if="showDetailModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="showDetailModal = false"
      >
        <div :class="['w-full max-w-2xl mx-4 p-8 rounded shadow-xl', isDarkMode ? 'bg-gray-800 text-white' : 'bg-white text-gray-900']">
          <div class="flex items-center justify-between mb-8 border-b pb-4" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
            <h3 class="text-2xl font-bold">{{ t('admin_view') }} - {{ selectedLog?.action }}</h3>
            <button @click="showDetailModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
              <X size="24" />
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
              <h4 class="text-xs font-bold uppercase tracking-widest mb-4 opacity-50">BEFORE</h4>
              <pre :class="['p-4 border font-mono text-xs overflow-auto max-h-64', isDarkMode ? 'bg-gray-900 border-gray-700 text-rose-400' : 'bg-gray-50 border-gray-200 text-red-600']">{{ JSON.stringify(selectedLog?.changes.before, null, 2) }}</pre>
            </div>
            <div>
              <h4 class="text-xs font-bold uppercase tracking-widest mb-4 opacity-50">AFTER</h4>
              <pre :class="['p-4 border font-mono text-xs overflow-auto max-h-64', isDarkMode ? 'bg-gray-900 border-gray-700 text-emerald-400' : 'bg-gray-50 border-gray-200 text-green-600']">{{ JSON.stringify(selectedLog?.changes.after, null, 2) }}</pre>
            </div>
          </div>

          <div class="mt-8 pt-6 border-t flex justify-end" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
            <button 
              @click="showDetailModal = false"
              class="px-8 py-2 bg-construct-red text-white font-bold transition-colors hover:bg-red-700"
            >
              {{ t('admin_close') }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
