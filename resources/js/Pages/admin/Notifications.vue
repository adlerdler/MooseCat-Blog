<script setup>
/**
 * Notification Management Page
 * 
 * Features:
 * - Notification list display with pagination
 * - Search and filter functionality
 * - Notification CRUD operations
 * - Notification types (info, warning, error, success)
 * - Mark notifications as read
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useTheme } from '../../composables/useTheme';
import {
  Bell,
  Eye,
  Edit3,
  Trash2,
  Calendar,
  CheckCircle,
  Plus,
  Info,
  AlertTriangle,
  AlertCircle,
  X,
  Check,
  Clock,
  Link,
  FileText,
  Pagination,
  SearchFilterModal
} from '../../composables/useAdminImports';
import { findById, findIndexById } from '../../utils/typeConvert';
import { formatToShort } from '../../utils/dateUtils';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import NotificationForm from '../../components/admin/NotificationForm.vue';

const props = defineProps({
  notifications: { type: Array, default: () => [] },
});

const { t } = useI18n();
const { isDarkMode } = useTheme();

const typeOptions = [
  { value: 'info', label: 'Info' },
  { value: 'warning', label: 'Warning' },
  { value: 'error', label: 'Error' },
  { value: 'success', label: 'Success' }
];

const getTypeLabel = (type) => {
  const labels = {
    info: 'Info',
    warning: 'Warning',
    error: 'Error',
    success: 'Success'
  };
  return labels[type] || type;
};

const getTypeIcon = (type) => {
  const icons = {
    info: Info,
    warning: AlertTriangle,
    error: AlertCircle,
    success: CheckCircle
  };
  return icons[type] || Info;
};

const getTypeColor = (type) => {
  if (isDarkMode.value) {
    const colors = {
      info: 'text-blue-400',
      warning: 'text-yellow-400',
      error: 'text-red-400',
      success: 'text-green-400'
    };
    return colors[type] || 'text-blue-400';
  } else {
    const colors = {
      info: 'text-blue-500',
      warning: 'text-yellow-500',
      error: 'text-red-500',
      success: 'text-green-500'
    };
    return colors[type] || 'text-blue-500';
  }
};

const getTypeBgColor = (type) => {
  if (isDarkMode.value) {
    const colors = {
      info: 'bg-blue-400/20',
      warning: 'bg-yellow-400/20',
      error: 'bg-red-400/20',
      success: 'bg-green-400/20'
    };
    return colors[type] || 'bg-blue-400/20';
  } else {
    const colors = {
      info: 'bg-blue-100',
      warning: 'bg-yellow-100',
      error: 'bg-red-100',
      success: 'bg-green-100'
    };
    return colors[type] || 'bg-blue-100';
  }
};

const searchQuery = ref('');
const typeFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const isFormVisible = ref(false);
const editingNotification = ref(null);
const showDeleteConfirm = ref(false);
const deletingNotificationId = ref(null);
const showDetailModal = ref(false);
const viewingNotification = ref(null);

const notifications = computed(() => (props.notifications || []).map(n => ({ ...n })));

const filteredNotifications = computed(() => {
  return notifications.value.filter(notification => {
    const matchesSearch = notification.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         notification.message.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesType = typeFilter.value === 'all' || notification.type === typeFilter.value;
    return matchesSearch && matchesType;
  });
});

const totalPages = computed(() => Math.ceil(filteredNotifications.value.length / itemsPerPage.value));

const paginatedNotifications = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredNotifications.value.slice(start, start + itemsPerPage.value);
});

const handleView = (notification) => {
  viewingNotification.value = notification;
  showDetailModal.value = true;
};

const handleEdit = (notification) => {
  editingNotification.value = { ...notification, action: 'edit' };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingNotification.value = {
    action: 'add',
    title: '',
    message: '',
    type: 'info',
    read: false,
    link: ''
  };
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (data.action === 'edit') {
    const index = findIndexById(notifications.value, data.id);
    if (index !== -1) {
      notifications.value[index] = { ...data };
    }
  } else if (data.action === 'add') {
    const newId = notifications.value.length > 0
      ? Math.max(...notifications.value.map(n => n.id)) + 1
      : 1;
    notifications.value.unshift({
      ...data,
      id: newId,
      created_at: new Date().toISOString()
    });
  }
  isFormVisible.value = false;
  editingNotification.value = null;
};

const handleDelete = (id) => {
  deletingNotificationId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  notifications.value = notifications.value.filter(n => n.id !== deletingNotificationId.value);
  showDeleteConfirm.value = false;
  deletingNotificationId.value = null;
};

const markAsRead = (notification) => {
  const index = findIndexById(notifications.value, notification.id);
  if (index !== -1) {
    notifications.value[index].read = true;
  }
};

const markAllAsRead = () => {
  notifications.value.forEach(n => {
    n.read = true;
  });
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
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Bell class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_notifications') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Manage system notifications</p>
        </div>
        <button @click="handleAdd" class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl">
          <Plus size="18" /> {{ t('admin_add_notification') }}
        </button>
      </div>
    </div>

    <!-- Search and Filter -->
    <SearchFilterModal
      v-model:search-query="searchQuery"
      :search-placeholder="t('admin_search_notifications')"
      :filters="[
        {
          key: 'type',
          options: [
            { value: 'all', label: t('admin_all_types') },
            ...typeOptions.map(opt => ({ value: opt.value, label: opt.label }))
          ]
        }
      ]"
      :filter-values="{ type: typeFilter }"
      @filter-change="handleFilterChange"
    />

    <!-- Quick Actions -->
    <div class="flex items-center gap-4 mb-6">
      <button
        @click="markAllAsRead"
        class="text-xs font-bold tracking-widest text-construct-red hover:underline uppercase"
      >
        {{ t('admin_mark_all_read') }}
      </button>
    </div>

    <!-- Notification List -->
    <div class="space-y-4">
      <div
        v-for="notification in paginatedNotifications"
        :key="notification.id"
        :class="[
          'p-6 border transition-all hover:border-construct-red',
          isDarkMode ? 'bg-gray-800/50 border-gray-700' : 'bg-white border-gray-200',
          !notification.read && (isDarkMode ? 'border-l-4 border-l-construct-red' : 'border-l-4 border-l-construct-red')
        ]"
      >
        <div class="flex items-start gap-4">
          <!-- Icon -->
          <div :class="['p-3 rounded-full shrink-0', getTypeBgColor(notification.type)]">
            <component
              :is="getTypeIcon(notification.type)"
              :class="['w-6 h-6', getTypeColor(notification.type)]"
            />
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center gap-3">
                <span :class="['font-bold text-lg', isDarkMode ? 'text-white' : 'text-gray-900', !notification.read && 'text-construct-red']">
                  {{ notification.title }}
                </span>
                <span v-if="!notification.read" class="px-2 py-0.5 text-xs font-bold bg-construct-red text-white rounded">
                  NEW
                </span>
              </div>
              <div class="flex items-center gap-2">
                <Clock :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="16" />
                <span :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ formatToShort(notification.created_at) }}</span>
              </div>
            </div>

            <p :class="['mb-4', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ notification.message }}</p>

            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <span :class="['px-2 py-1 text-xs font-bold uppercase rounded', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']">
                  {{ getTypeLabel(notification.type) }}
                </span>
                <a
                  v-if="notification.link"
                  :href="notification.link"
                  target="_blank"
                  :class="['flex items-center gap-1 text-xs', isDarkMode ? 'text-blue-400 hover:text-blue-300' : 'text-blue-600 hover:text-blue-500']"
                >
                  <Link size="12" />
                  {{ t('admin_view_link') || 'View Link' }}
                </a>
              </div>

              <div class="flex items-center gap-2">
                <button
                  v-if="!notification.read"
                  @click="markAsRead(notification)"
                  :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700 text-green-400' : 'hover:bg-gray-100 text-green-600']"
                  :title="t('admin_mark_read')"
                >
                  <CheckCircle size="16" />
                </button>
                <button
                  @click="handleView(notification)"
                  :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500']"
                  :title="t('admin_view')"
                >
                  <Eye size="16" />
                </button>
                <button
                  @click="handleEdit(notification)"
                  :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500']"
                  :title="t('admin_edit')"
                >
                  <Edit3 size="16" />
                </button>
                <button
                  @click="handleDelete(notification.id)"
                  :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-red-500/20 text-red-400' : 'hover:bg-red-50 text-red-500']"
                  :title="t('admin_delete')"
                >
                  <Trash2 size="16" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="paginatedNotifications.length === 0" :class="['p-12 text-center border rounded-xl', isDarkMode ? 'border-gray-700 bg-gray-800/50' : 'border-gray-200 bg-white']">
        <Bell :size="48" :class="['mx-auto mb-4', isDarkMode ? 'text-gray-600' : 'text-gray-300']" />
        <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
          {{ t('admin_no_notifications') }}
        </p>
      </div>
    </div>

    <!-- Pagination -->
    <Pagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredNotifications.length"
      v-model:items-per-page="itemsPerPage"
      @update:current-page="currentPage = $event"
    />

    <!-- Notification Form Modal -->
    <Transition name="modal">
      <div
        v-if="isFormVisible"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        @click.self="isFormVisible = false"
      >
        <div :class="['w-full max-w-lg rounded-xl shadow-xl', isDarkMode ? 'bg-gray-800' : 'bg-white']">
          <div :class="['flex items-center justify-between px-6 py-4 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <h2 :class="['font-display text-lg tracking-tight', isDarkMode ? 'text-white' : 'text-gray-900']">
              {{ editingNotification?.action === 'add' ? t('admin_add_notification') : t('admin_edit_notification') }}
            </h2>
            <button
              @click="isFormVisible = false"
              :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500']"
            >
              <X size="20" />
            </button>
          </div>

          <NotificationForm
            :notification="editingNotification"
            @save="handleSave"
            @close="isFormVisible = false"
          />
        </div>
      </div>
    </Transition>

    <!-- Detail Modal -->
    <Teleport to="body">
      <div
        v-if="showDetailModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="showDetailModal = false"
      >
        <div :class="['w-full max-w-xl mx-4 p-8 rounded-xl shadow-xl', isDarkMode ? 'bg-gray-800 text-white' : 'bg-white text-gray-900']">
          <div class="flex items-center justify-between mb-6 border-b pb-4" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
            <h3 class="text-2xl font-bold flex items-center gap-3">
              <component
                :is="getTypeIcon(viewingNotification?.type)"
                :class="getTypeColor(viewingNotification?.type)"
                size="24"
              />
              {{ viewingNotification?.title }}
            </h3>
            <button
              @click="showDetailModal = false"
              :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500']"
            >
              <X size="24" />
            </button>
          </div>

          <div class="space-y-4">
            <div>
              <span :class="['px-2 py-1 text-xs font-bold uppercase rounded', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']">
                {{ getTypeLabel(viewingNotification?.type) }}
              </span>
              <span v-if="!viewingNotification?.read" class="ml-2 px-2 py-1 text-xs font-bold bg-construct-red text-white rounded">
                UNREAD
              </span>
            </div>

            <p :class="['text-sm', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ viewingNotification?.message }}
            </p>

            <div v-if="viewingNotification?.link" class="flex items-center gap-2">
              <Link :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="14" />
              <a
                :href="viewingNotification?.link"
                target="_blank"
                :class="['text-sm', isDarkMode ? 'text-blue-400 hover:text-blue-300' : 'text-blue-600 hover:text-blue-500']"
              >
                {{ viewingNotification?.link }}
              </a>
            </div>

            <div class="flex items-center gap-2 text-sm" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">
              <Clock size="14" />
              <span>{{ formatToShort(viewingNotification?.created_at) }}</span>
            </div>
          </div>

          <div class="mt-8 pt-6 border-t flex justify-end gap-3" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
            <button
              @click="showDetailModal = false"
              :class="[
                'px-6 py-2 font-bold tracking-widest uppercase text-sm transition-colors rounded border',
                isDarkMode
                  ? 'border-gray-600 text-gray-300 hover:bg-gray-700'
                  : 'border-gray-300 text-gray-700 hover:bg-gray-100'
              ]"
            >
              {{ t('admin_close') }}
            </button>
            <button
              v-if="!viewingNotification?.read"
              @click="markAsRead(viewingNotification); showDetailModal = false"
              class="flex items-center gap-2 px-6 py-2 bg-green-600 text-white font-bold tracking-widest uppercase text-sm hover:bg-green-700 transition-colors rounded"
            >
              <CheckCircle size="16" />
              {{ t('admin_mark_read') }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      :title="t('admin_confirm_delete')"
      :content="t('admin_delete_notification_warning')"
      confirm-text="Delete"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
