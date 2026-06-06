<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Database,
  FileText,
  RotateCcw,
  Search,
  Filter,
  Clock,
  HardDrive,
  Archive,
  CheckCircle,
  XCircle,
  Loader,
  Eye,
  AlertTriangle,
  ChevronDown,
  ChevronRight,
  Table,
  File,
  Image
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { useToast } from '../../composables/useToast';
import { formatToShort } from '../../utils/dateUtils';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import Pagination from '../../components/admin/Pagination.vue';
import EmptyState from '../../components/admin/EmptyState.vue';
import SearchFilterModal from '../../components/admin/SearchFilterModal.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  backups: { type: Array, default: () => [] },
});

const { t, locale } = useI18n();
const { isDarkMode } = useTheme();
const { success: toastSuccess, error: toastError } = useToast();

const searchQuery = ref('');
const typeFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const showRestoreConfirm = ref(false);
const restoringBackup = ref(null);
const restoreProgress = ref(0);
const isRestoring = ref(false);
const expandedBackup = ref(null);
const selectedItems = ref([]);
const previewLoading = ref({});
const previewData = ref({});
const previewError = ref({});

const backupList = computed(() => (props.backups || []).filter(b => b.status === 'completed'));

const filteredBackups = computed(() => {
  return backupList.value.filter(backup => {
    const matchesSearch = backup.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         backup.note.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesType = typeFilter.value === 'all' || backup.type === typeFilter.value;
    return matchesSearch && matchesType;
  });
});

const totalPages = computed(() => Math.ceil(filteredBackups.value.length / itemsPerPage.value));

const paginatedBackups = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredBackups.value.slice(start, start + itemsPerPage.value);
});

const getTypeIcon = (type) => {
  const icons = {
    full: Archive,
    database: Database,
    files: FileText,
    incremental: HardDrive
  };
  return icons[type] || Archive;
};

const getTypeColor = (type) => {
  if (isDarkMode.value) {
    const colors = {
      full: 'text-purple-400',
      database: 'text-blue-400',
      files: 'text-green-400',
      incremental: 'text-yellow-400'
    };
    return colors[type] || 'text-gray-400';
  } else {
    const colors = {
      full: 'text-purple-600',
      database: 'text-blue-600',
      files: 'text-green-600',
      incremental: 'text-yellow-600'
    };
    return colors[type] || 'text-gray-600';
  }
};

const getPreviewData = (backupId) => {
  return previewData.value[backupId] || null;
};

const fetchPreview = async (backup) => {
  if (previewData.value[backup.id] || previewLoading.value[backup.id]) {
    return;
  }
  previewLoading.value[backup.id] = true;
  previewError.value[backup.id] = null;
  try {
    const res = await axios.get(route('admin.restore.preview', { id: backup.id }));
    previewData.value[backup.id] = res.data;
  } catch {
    previewError.value[backup.id] = '无法读取备份内容';
  } finally {
    previewLoading.value[backup.id] = false;
  }
};

const toggleExpand = (backup) => {
  if (expandedBackup.value === backup.id) {
    expandedBackup.value = null;
  } else {
    expandedBackup.value = backup.id;
    fetchPreview(backup);
  }
};

const handleRestore = (backup) => {
  restoringBackup.value = backup;
  selectedItems.value = [];
  showRestoreConfirm.value = true;
};

const confirmRestore = async () => {
  if (!restoringBackup.value) return;
  
  isRestoring.value = true;
  restoreProgress.value = 0;
  showRestoreConfirm.value = false;
  
  // 使用 Inertia 发送恢复请求
  router.post(route('admin.restore.execute', { id: restoringBackup.value.id }), {}, {
    onSuccess: () => {
      isRestoring.value = false;
      restoreProgress.value = 0;
      restoringBackup.value = null;
      toastSuccess(t('admin_restore_success') || 'Restore completed');
    },
    onError: () => {
      isRestoring.value = false;
      restoreProgress.value = 0;
      restoringBackup.value = null;
      toastError(t('admin_restore_failed') || 'Restore failed');
    },
  });
  
  // 模拟进度条（保留原有的进度条效果）
  const interval = setInterval(() => {
    restoreProgress.value += Math.random() * 15;
    if (restoreProgress.value >= 100) {
      restoreProgress.value = 100;
      clearInterval(interval);
      setTimeout(() => {
        isRestoring.value = false;
        restoreProgress.value = 0;
        restoringBackup.value = null;
      }, 1000);
    }
  }, 500);
};

const handlePreview = (backup) => {
  toggleExpand(backup);
};

const handleFilterChange = ({ key, value }) => {
  if (key === 'type') {
    typeFilter.value = value;
  }
  currentPage.value = 1;
};
</script>

<template>
  <div class="p-8">
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <RotateCcw class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_restore') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">System data restoration</p>
        </div>
      </div>
    </div>

    <div :class="['mb-6 p-4 border-l-4 border-yellow-500 rounded-r-lg', isDarkMode ? 'bg-yellow-900/20' : 'bg-yellow-50']">
      <div class="flex items-start gap-3">
        <AlertTriangle :class="isDarkMode ? 'text-yellow-400' : 'text-yellow-600'" size="20" />
        <div>
          <h4 :class="['font-bold mb-1', isDarkMode ? 'text-yellow-400' : 'text-yellow-800']">{{ t('admin_restore_warning_title') }}</h4>
          <p :class="['text-sm', isDarkMode ? 'text-yellow-300' : 'text-yellow-700']">{{ t('admin_restore_warning_desc') }}</p>
        </div>
      </div>
    </div>

    <SearchFilterModal
      v-model:search-query="searchQuery"
      :search-placeholder="t('admin_search_backup')"
      :filters="[
        {
          key: 'type',
          options: [
            { value: 'all', label: t('admin_all_types') },
            { value: 'full', label: t('admin_backup_full') },
            { value: 'database', label: t('admin_backup_database') },
            { value: 'files', label: t('admin_backup_files') },
            { value: 'incremental', label: t('admin_backup_incremental') }
          ]
        }
      ]"
      :filter-values="{ type: typeFilter }"
      @filter-change="handleFilterChange"
    />

    <div v-if="isRestoring" class="mb-8">
      <div :class="['p-6 border rounded-xl', isDarkMode ? 'bg-gray-800/50 border-gray-700' : 'bg-white border-gray-200']">
        <div class="flex items-center gap-4 mb-4">
          <Loader class="animate-spin text-construct-red" size="24" />
          <div>
            <h4 :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_restoring') }}: {{ restoringBackup?.name }}</h4>
            <p :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_restore_progress') }}: {{ Math.round(restoreProgress) }}%</p>
          </div>
        </div>
        <div :class="['w-full h-2 rounded-full overflow-hidden', isDarkMode ? 'bg-gray-700' : 'bg-gray-200']">
          <div 
            class="h-full bg-construct-red transition-all duration-300"
            :style="{ width: `${restoreProgress}%` }"
          ></div>
        </div>
      </div>
    </div>

    <template v-if="filteredBackups.length > 0">
      <div class="space-y-4">
        <div 
          v-for="backup in paginatedBackups" 
          :key="backup.id"
          :class="[
            'border rounded-xl transition-all',
            isDarkMode ? 'bg-gray-800/50 border-gray-700' : 'bg-white border-gray-200',
            expandedBackup === backup.id ? 'ring-2 ring-construct-red' : ''
          ]"
        >
          <div class="p-6">
            <div class="flex items-start gap-4">
              <div :class="['p-3 rounded-full', isDarkMode ? 'bg-gray-700' : 'bg-white shadow-sm border border-gray-100']">
                <component :is="getTypeIcon(backup.type)" :class="getTypeColor(backup.type)" size="24" />
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2 gap-4">
                  <div class="flex items-center gap-3 flex-wrap">
                    <span :class="['font-bold text-lg', isDarkMode ? 'text-white' : 'text-gray-900']">{{ backup.name }}</span>
                    <span :class="['px-2 py-1 text-xs font-bold uppercase rounded', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']">
                      {{ t('admin_backup_' + backup.type) }}
                    </span>
                  </div>
                  <div class="flex items-center gap-4 flex-shrink-0">
                    <span :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ backup.size }}</span>
                  </div>
                </div>
                <p :class="['mb-3', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ backup.note }}</p>
                <div class="flex items-center justify-between gap-4">
                  <div class="flex items-center gap-2 text-sm flex-shrink-0">
                    <Clock :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="14" />
                    <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                      {{ t('admin_created') }}: {{ formatToShort(backup.createdAt) }}
                    </span>
                  </div>
                  <div class="flex items-center gap-2 flex-shrink-0">
                    <button
                      @click="handlePreview(backup)"
                      :class="[
                        'flex items-center gap-1 px-3 py-1.5 text-sm font-bold transition-colors rounded-lg',
                        isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-900'
                      ]"
                    >
                      <Eye size="16" />
                      {{ t('admin_preview') }}
                      <component :is="expandedBackup === backup.id ? ChevronDown : ChevronRight" size="16" />
                    </button>
                    <button
                      @click="handleRestore(backup)"
                      class="flex items-center gap-1 px-4 py-1.5 text-sm font-bold text-white bg-construct-red hover:bg-red-700 transition-colors rounded-lg"
                    >
                      <RotateCcw size="16" />
                      {{ t('admin_restore_btn') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div 
            v-if="expandedBackup === backup.id"
            :class="['border-t p-6 rounded-b-xl', isDarkMode ? 'border-gray-700 bg-gray-900/50' : 'border-gray-200 bg-gray-50']"
          >
            <h4 :class="['font-bold mb-4', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_backup_contents') }}</h4>

            <!-- 加载中 -->
            <div v-if="previewLoading[backup.id]" class="flex items-center gap-3 py-4">
              <Loader class="animate-spin text-construct-red" size="20" />
              <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">读取备份内容中...</span>
            </div>

            <!-- 加载失败 -->
            <div v-else-if="previewError[backup.id]" :class="['text-sm py-4', isDarkMode ? 'text-red-400' : 'text-red-600']">
              {{ previewError[backup.id] }}
            </div>

            <!-- 真实数据 -->
            <template v-else-if="getPreviewData(backup.id)">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div :class="['p-4 rounded-xl', isDarkMode ? 'bg-gray-800' : 'bg-white border border-gray-200']">
                  <div class="flex items-center gap-2 mb-2">
                    <Table :class="isDarkMode ? 'text-blue-400' : 'text-blue-600'" size="18" />
                    <span :class="['font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_database_tables') }}</span>
                  </div>
                  <p :class="['text-2xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ getPreviewData(backup.id).tables.length }}</p>
                  <p :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_tables_count') }}</p>
                </div>
                <div :class="['p-4 rounded-xl', isDarkMode ? 'bg-gray-800' : 'bg-white border border-gray-200']">
                  <div class="flex items-center gap-2 mb-2">
                    <File :class="isDarkMode ? 'text-green-400' : 'text-green-600'" size="18" />
                    <span :class="['font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_files') }}</span>
                  </div>
                  <p :class="['text-2xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ getPreviewData(backup.id).files }}</p>
                  <p :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_files_count') }}</p>
                </div>
                <div :class="['p-4 rounded-xl', isDarkMode ? 'bg-gray-800' : 'bg-white border border-gray-200']">
                  <div class="flex items-center gap-2 mb-2">
                    <Image :class="isDarkMode ? 'text-purple-400' : 'text-purple-600'" size="18" />
                    <span :class="['font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_images') }}</span>
                  </div>
                  <p :class="['text-2xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ getPreviewData(backup.id).images }}</p>
                  <p :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_images_count') }}</p>
                </div>
              </div>

              <div v-if="getPreviewData(backup.id).tables.length > 0">
                <h5 :class="['font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_included_tables') }}:</h5>
                <div class="flex flex-wrap gap-2 mb-3">
                  <span 
                    v-for="table in getPreviewData(backup.id).tables" 
                    :key="table"
                    :class="['px-3 py-1 text-sm rounded-full', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200 text-gray-700']"
                  >
                    {{ table }}
                  </span>
                </div>
              </div>

              <div :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                {{ t('admin_total_records') }}: {{ getPreviewData(backup.id).totalRecords }}
              </div>
            </template>

            <!-- 无数据（空备份） -->
            <div v-else :class="['text-sm py-4', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              备份包为空或无可识别内容
            </div>
          </div>
        </div>
      </div>

      <Pagination
        :current-page="currentPage"
        :total-pages="totalPages"
        :total-items="filteredBackups.length"
        :items-per-page="itemsPerPage"
        @update:currentPage="currentPage = $event"
        @update:itemsPerPage="itemsPerPage = $event"
      />
    </template>

    <!-- Empty State -->
    <template v-else>
      <div class="mt-8">
        <EmptyState
          :title="t('admin_no_backups_found')"
          :description="t('admin_no_backups_description')"
          :icon="Archive"
        />
      </div>
    </template>

    <ConfirmDialog
      :visible="showRestoreConfirm"
      :title="t('admin_restore_confirm_title')"
      :content="t('admin_restore_confirm_desc', { name: restoringBackup?.name })"
      :confirm-text="t('admin_restore_btn')"
      :cancel-text="t('admin_cancel')"
      confirm-variant="danger"
      @confirm="confirmRestore"
      @cancel="showRestoreConfirm = false"
    />
  </div>
</template>
