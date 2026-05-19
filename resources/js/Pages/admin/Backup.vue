<script setup>
/**
 * AdminBackup.vue - 备份管理页面
 * 
 * 功能说明：
 * - 管理系统备份记录
 * - 备份列表展示（名称、类型、状态、大小、时间）
 * - 创建新备份（完整备份、数据库备份、文件备份、增量备份）
 * - 下载和删除备份
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Database,
  FileText,
  Download,
  Trash2,
  Plus,
  Search,
  Filter,
  Clock,
  HardDrive,
  Archive,
  CheckCircle,
  XCircle,
  Loader
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { backupRecords } from '../../data/backup';
import { formatToShort } from '../../utils/dateUtils';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import AdminPagination from '../../components/admin/AdminPagination.vue';
import AdminSearchFilter from '../../components/admin/AdminSearchFilter.vue';

const { t, locale } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const typeFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const showCreateModal = ref(false);
const newBackupType = ref('full');
const newBackupNote = ref('');
const showDeleteConfirm = ref(false);
const deletingBackup = ref(null);

const backups = ref([...backupRecords]);

const filteredBackups = computed(() => {
  return backups.value.filter(backup => {
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

const getStatusIcon = (status) => {
  const icons = {
    completed: CheckCircle,
    in_progress: Loader,
    failed: XCircle
  };
  return icons[status] || Loader;
};

const getStatusColor = (status) => {
  if (isDarkMode.value) {
    const colors = {
      completed: 'text-green-400',
      in_progress: 'text-blue-400',
      failed: 'text-red-400'
    };
    return colors[status] || 'text-gray-400';
  } else {
    const colors = {
      completed: 'text-green-600',
      in_progress: 'text-blue-600',
      failed: 'text-red-600'
    };
    return colors[status] || 'text-gray-600';
  }
};

const handleCreateBackup = () => {
  const now = new Date();
  const timestamp = now.toISOString().replace(/[-:T]/g, '').slice(0, 14);
  const newId = Math.max(...backups.value.map(b => b.id), 0) + 1;
  const typeLabels = { full: 'full', database: 'db', files: 'files', incremental: 'incremental' };

  const newBackup = {
    id: newId,
    name: `${typeLabels[newBackupType.value]}_backup_${timestamp}`,
    type: newBackupType.value,
    status: 'completed',
    size: newBackupType.value === 'full' ? '256 MB' : newBackupType.value === 'database' ? '48 MB' : newBackupType.value === 'files' ? '180 MB' : '12 MB',
    path: `/backups/${typeLabels[newBackupType.value]}_backup_${timestamp}.zip`,
    createdAt: now.toISOString(),
    completedAt: now.toISOString(),
    note: newBackupNote.value || t('admin_backup_' + newBackupType.value)
  };

  backups.value.unshift(newBackup);
  showCreateModal.value = false;
  newBackupNote.value = '';
};

const handleDownload = (backup) => {
  console.log('Download backup:', backup.path);
};

const handleDelete = (backup) => {
  deletingBackup.value = backup;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingBackup.value !== null) {
    const index = backups.value.findIndex(b => b.id === deletingBackup.value.id);
    if (index !== -1) {
      backups.value.splice(index, 1);
    }
    deletingBackup.value = null;
  }
  showDeleteConfirm.value = false;
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
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Archive class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_backup') }}</h2>
          </div>
          <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">System backup management</p>
        </div>
        <button
          @click="showCreateModal = true"
          :class="[
            'flex items-center gap-2 px-6 py-3 font-bold uppercase tracking-wider transition-all hover:opacity-90',
            isDarkMode ? 'bg-construct-red text-white' : 'bg-construct-red text-white'
          ]"
        >
          <Plus class="text-white" size="20" />
          {{ t('admin_create_backup') }}
        </button>
      </div>
    </div>

    <!-- Search and Filter -->
    <AdminSearchFilter
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

    <!-- Backup List -->
    <div class="space-y-4">
      <div 
        v-for="backup in paginatedBackups" 
        :key="backup.id"
        :class="[
          'p-6 border transition-all hover:border-construct-red',
          isDarkMode ? 'bg-gray-800/50 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <div class="flex items-start gap-4">
          <div :class="['p-3 rounded-full', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
            <component :is="getTypeIcon(backup.type)" :class="getTypeColor(backup.type)" size="24" />
          </div>
          <div class="flex-1">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center gap-3">
                <span :class="['font-bold text-lg', isDarkMode ? 'text-white' : 'text-gray-900']">{{ backup.name }}</span>
                <span :class="['px-2 py-1 text-xs font-bold uppercase rounded', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']">
                  {{ t('admin_backup_' + backup.type) }}
                </span>
                <span :class="['flex items-center gap-1 text-sm', getStatusColor(backup.status)]">
                  <component :is="getStatusIcon(backup.status)" size="16" :class="{ 'animate-spin': backup.status === 'in_progress' }" />
                  {{ t('admin_backup_status_' + backup.status) }}
                </span>
              </div>
              <div class="flex items-center gap-4">
                <span :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ backup.size }}</span>
              </div>
            </div>
            <p :class="['mb-3', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ backup.note }}</p>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2 text-sm">
                <Clock :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="14" />
                <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                  {{ t('admin_created') }}: {{ formatToShort(backup.createdAt) }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <button
                  @click="handleDownload(backup)"
                  :class="[
                    'p-2 transition-colors',
                    isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-900'
                  ]"
                  :title="t('admin_download')"
                >
                  <Download size="18" />
                </button>
                <button
                  @click="handleDelete(backup)"
                  :class="[
                    'p-2 transition-colors',
                    isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-red-400' : 'hover:bg-gray-100 text-gray-500 hover:text-red-600'
                  ]"
                  :title="t('admin_delete')"
                >
                  <Trash2 size="18" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="filteredBackups.length === 0" :class="['text-center py-12', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
      <Archive :class="['mx-auto mb-4 opacity-50', isDarkMode ? 'text-gray-600' : 'text-gray-400']" size="48" />
      <p>{{ t('admin_no_backups') }}</p>
    </div>

    <!-- Pagination -->
    <AdminPagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredBackups.length"
      v-model:items-per-page="itemsPerPage"
      @update:current-page="currentPage = $event"
    />

    <!-- Create Backup Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showCreateModal" class="fixed inset-0 z-[200] flex items-center justify-center">
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showCreateModal = false" />

          <!-- Modal -->
          <div
            :class="[
              'relative w-full max-w-md mx-4 rounded-xl shadow-2xl overflow-hidden',
              isDarkMode ? 'bg-gray-800' : 'bg-white'
            ]"
          >
            <!-- Header -->
            <div class="flex items-center justify-between p-6">
              <div class="flex items-center gap-3">
                <div :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center',
                  isDarkMode ? 'bg-construct-red/20' : 'bg-construct-red/10'
                ]">
                  <Archive :class="['w-5 h-5', isDarkMode ? 'text-construct-red' : 'text-construct-red']" />
                </div>
                <h3 :class="['text-lg font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">
                  {{ t('admin_create_backup') }}
                </h3>
              </div>
              <button
                @click="showCreateModal = false"
                :class="[
                  'p-2 rounded-lg transition-colors',
                  isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'
                ]"
              >
                <X class="w-5 h-5" />
              </button>
            </div>

            <!-- Body -->
            <div class="px-6 pb-6">
              <div class="mb-6">
                <label :class="['block mb-2 text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_backup_type') }}</label>
                <div class="grid grid-cols-2 gap-3">
                  <button
                    v-for="type in ['full', 'database', 'files', 'incremental']"
                    :key="type"
                    @click="newBackupType = type"
                    :class="[
                      'p-4 border rounded-lg transition-all text-left',
                      newBackupType === type
                        ? 'border-construct-red bg-construct-red/10'
                        : (isDarkMode ? 'border-gray-700 hover:border-gray-600' : 'border-gray-200 hover:border-gray-300')
                    ]"
                  >
                    <component :is="getTypeIcon(type)" :class="['mb-2', getTypeColor(type)]" size="24" />
                    <p :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_backup_' + type) }}</p>
                  </button>
                </div>
              </div>

              <div class="mb-6">
                <label :class="['block mb-2 text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_backup_note') }}</label>
                <textarea
                  v-model="newBackupNote"
                  rows="3"
                  :class="[
                    'w-full px-4 py-3 border rounded-lg focus:border-construct-red focus:outline-none transition-colors resize-none',
                    isDarkMode ? 'bg-gray-900 border-gray-700 text-white' : 'bg-gray-50 border-gray-300 text-gray-900'
                  ]"
                  :placeholder="t('admin_backup_note_placeholder')"
                ></textarea>
              </div>
            </div>

            <!-- Footer -->
            <div class="flex gap-3 px-6 pb-6">
              <button
                @click="showCreateModal = false"
                :class="[
                  'flex-1 px-6 py-3 font-bold tracking-widest uppercase text-sm transition-colors rounded-lg',
                  isDarkMode 
                    ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' 
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
              >
                {{ t('admin_cancel') }}
              </button>
              <button
                @click="handleCreateBackup"
                :class="[
                  'flex-1 px-6 py-3 font-bold tracking-widest uppercase text-sm transition-colors rounded-lg',
                  'bg-construct-red text-white hover:bg-red-700'
                ]"
              >
                {{ t('confirm') }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      :title="t('admin_confirm_delete')"
      :content="t('admin_delete_warning')"
      :confirm-text="t('admin_delete')"
      confirm-variant="danger"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>
