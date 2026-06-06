<script setup>
/**
 * System Logs Page
 * 
 * Features:
 * - System operation logs display
 * - Search and filter functionality (module, action)
 * - Log details modal
 * - Pagination support
 * - IP address and user tracking
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useTheme } from '../../composables/useTheme';
import { Copy, Check } from 'lucide-vue-next';
import {
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
  Pagination,
  EmptyState,
  SearchFilterModal,
  Inbox,
  formatToShort
} from '../../composables/useAdminImports';

const props = defineProps({
  logs: { type: Array, default: () => [] },
});

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const actionFilter = ref('all');
const moduleFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const selectedLog = ref(null);
const showDetailModal = ref(false);

const logList = computed(() => props.logs || []);

// Get all module types
const moduleTypes = computed(() => {
  return ['all', ...new Set(logList.value.map(log => log.module))];
});

// Get action types based on module filter
const actionTypes = computed(() => {
  let sourceLogs = logList.value;
  if (moduleFilter.value !== 'all') {
    sourceLogs = sourceLogs.filter(log => log.module === moduleFilter.value);
  }
  return ['all', ...new Set(sourceLogs.map(log => log.action))];
});

const filteredLogs = computed(() => {
  return logList.value.filter(log => {
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

const copied = ref(false);

const openDetails = (log) => {
  if (log.changes) {
    selectedLog.value = log;
    showDetailModal.value = true;
    copied.value = false;
  }
};

const contextText = computed(() => {
  if (!selectedLog.value?.changes) return '';
  return JSON.stringify(selectedLog.value.changes, null, 2);
});

const copyContext = async () => {
  try {
    await navigator.clipboard.writeText(contextText.value);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
  } catch {
    // Fallback for older browsers
    const textarea = document.createElement('textarea');
    textarea.value = contextText.value;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
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

const handleFilterChange = ({ key, value }) => {
  if (key === 'module') {
    moduleFilter.value = value;
  } else if (key === 'action') {
    actionFilter.value = value;
  }
  currentPage.value = 1;
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-10">
      <div class="flex items-center gap-4 mb-2">
        <FileText class="text-construct-red" size="32" />
        <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_logs') }}</h2>
      </div>
      <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">System operation logs</p>
    </div>

    <!-- Search and Filter -->
    <SearchFilterModal
      v-model:search-query="searchQuery"
      :search-placeholder="t('admin_search_logs')"
      :filters="[
        {
          key: 'module',
          options: moduleTypes.map(mod => ({
            value: mod,
            label: mod === 'all' ? t('admin_all_actions') : mod.toUpperCase()
          }))
        },
        {
          key: 'action',
          options: actionTypes.map(action => ({
            value: action,
            label: action === 'all' ? t('admin_all_actions') : t(action)
          }))
        }
      ]"
      :filter-values="{ module: moduleFilter, action: actionFilter }"
      @filter-change="handleFilterChange"
    />

    <!-- Content Area -->
    <template v-if="filteredLogs.length > 0">
      <!-- Logs List -->
      <div class="space-y-4">
        <div 
          v-for="log in paginatedLogs" 
          :key="log.id"
          :class="[
            'p-6 border rounded-xl transition-all hover:border-construct-red',
            isDarkMode ? 'bg-gray-800/50 border-gray-700' : 'bg-white border-gray-200'
          ]"
        >
          <div class="flex items-start gap-4">
            <div :class="['p-3 rounded-full', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
              <component :is="getActionIcon(log.action)" :class="getActionColor(log.action)" size="24" />
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2 gap-4">
                <div class="flex items-center gap-3 flex-wrap">
                  <span :class="['font-bold text-lg', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t(log.action) }}</span>
                  <span :class="['px-2 py-1 text-xs font-bold uppercase rounded', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']">
                    {{ log.user }}
                  </span>
                  <span :class="['px-2 py-0.5 rounded border text-[10px] font-bold uppercase', isDarkMode ? 'border-gray-700 text-gray-500' : 'border-gray-200 text-gray-400']">
                    {{ log.module }}
                  </span>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                  <Clock :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="16" />
                  <span :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ formatToShort(log.created_at) }}</span>
                </div>
              </div>
              <p :class="['mb-3', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ log.details }}</p>
              <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-6 text-sm min-w-0">
                  <div class="flex items-center gap-2 flex-shrink-0">
                    <Monitor :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="14" />
                    <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ log.ip }}</span>
                  </div>
                  <div :class="['hidden lg:flex items-center gap-2 flex-1 min-w-0', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                    <span class="truncate">{{ log.user_agent }}</span>
                  </div>
                </div>
                
                <button
                  if="log.changes"
                  @click="openDetails(log)"
                  class="flex items-center gap-2 px-3 py-1 bg-construct-red text-white text-xs font-bold transition-colors hover:bg-red-700 rounded-lg flex-shrink-0"
                >
                  <Eye size="14" /> {{ t('admin_view') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <Pagination
        :current-page="currentPage"
        :total-pages="totalPages"
        :total-items="filteredLogs.length"
        v-model:items-per-page="itemsPerPage"
        @update:current-page="currentPage = $event"
      />
    </template>

    <!-- Empty State -->
    <div v-else class="mt-6">
      <EmptyState 
        :title="t('admin_no_logs_found') || 'No logs found'"
        :description="t('admin_no_logs_description') || 'Try adjusting your search or filters to find what you are looking for'"
        :icon="FileText"
      />
    </div>

    <!-- Detail Modal -->
    <Teleport to="body">
      <Transition name="fade">
        <div 
          v-if="showDetailModal" 
          class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
          @click.self="showDetailModal = false"
        >
          <div :class="['w-full max-w-2xl rounded-xl shadow-2xl', isDarkMode ? 'bg-gray-800 text-white border border-gray-700' : 'bg-white text-gray-900 border border-gray-200']">
            <div class="flex items-center justify-between px-8 py-6" :class="isDarkMode ? 'border-b border-gray-700' : 'border-b border-gray-200'">
              <h3 class="font-display text-2xl tracking-tighter">{{ t('admin_view') }} - {{ selectedLog?.action }}</h3>
              <button @click="showDetailModal = false" class="p-2 rounded-lg transition-colors" :class="isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500'">
                <X size="24" />
              </button>
            </div>

            <div class="p-8">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                  <h4 class="text-xs font-bold uppercase tracking-widest mb-4 opacity-50">BEFORE</h4>
                  <pre :class="['p-4 border rounded-lg font-mono text-xs overflow-auto max-h-64', isDarkMode ? 'bg-gray-900 border-gray-700 text-rose-400' : 'bg-gray-50 border-gray-200 text-red-600']">{{ JSON.stringify(selectedLog?.changes.before, null, 2) }}</pre>
                </div>
                <div>
                  <h4 class="text-xs font-bold uppercase tracking-widest mb-4 opacity-50">AFTER</h4>
                  <pre :class="['p-4 border rounded-lg font-mono text-xs overflow-auto max-h-64', isDarkMode ? 'bg-gray-900 border-gray-700 text-emerald-400' : 'bg-gray-50 border-gray-200 text-green-600']">{{ JSON.stringify(selectedLog?.changes.after, null, 2) }}</pre>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-8 py-6" :class="isDarkMode ? 'border-t border-gray-700' : 'border-t border-gray-200'">
              <button 
                @click="copyContext"
                class="px-6 py-2 flex items-center gap-2 border font-bold text-xs uppercase tracking-wider transition-all rounded-lg"
                :class="copied 
                  ? 'border-emerald-500 text-emerald-500 bg-emerald-500/10' 
                  : isDarkMode 
                    ? 'border-gray-600 text-gray-300 hover:border-gray-400 hover:text-white' 
                    : 'border-gray-300 text-gray-600 hover:border-gray-500 hover:text-gray-900'"
              >
                <Check v-if="copied" size="16" />
                <Copy v-else size="16" />
                {{ copied ? t('admin_copied') : t('admin_copy') }}
              </button>
              <button 
                @click="showDetailModal = false"
                class="px-8 py-2 bg-construct-red text-white font-bold transition-colors hover:bg-red-700 rounded-lg"
              >
                {{ t('admin_close') }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
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
