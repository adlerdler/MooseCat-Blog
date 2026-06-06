<script setup>
/**
 * Notification Management Page
 *
 * Features:
 * - Notification list display with pagination
 * - Search and filter functionality
 * - Create new system notifications
 * - Mark notifications as read (single / all)
 * - Delete notifications (single)
 * - Connected to Laravel's database notification system
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { router, useForm } from '@inertiajs/vue3';
import { useTheme } from '../../composables/useTheme';
import {
  Bell,
  Eye,
  Trash2,
  CheckCircle,
  Plus,
  Info,
  AlertTriangle,
  AlertCircle,
  X,
  Clock,
  Link,
  ExternalLink,
  Pagination,
  SearchFilterModal
} from '../../composables/useAdminImports';
import { useToast } from '../../composables/useToast';
import { formatToShort } from '../../utils/dateUtils';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import EmptyState from '../../components/admin/EmptyState.vue';
import NotificationForm from '../../components/admin/NotificationForm.vue';

const props = defineProps({
  notifications: { type: Array, default: () => [] },
  unreadCount:  { type: Number, default: 0 },
  pagination:   { type: Object, default: () => ({}) },
});

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success: toastSuccess, error: toastError } = useToast();

const typeOptions = [
  { value: 'info',    label: t('admin_notification_type_info') },
  { value: 'warning', label: t('admin_notification_type_warning') },
  { value: 'error',   label: t('admin_notification_type_error') },
  { value: 'success', label: t('admin_notification_type_success') },
];

const getTypeIcon = (type) => {
  const icons = { info: Info, warning: AlertTriangle, error: AlertCircle, success: CheckCircle };
  return icons[type] || Info;
};

const getTypeColor = (type) => {
  const colors = { info: 'text-blue-500', warning: 'text-yellow-500', error: 'text-red-500', success: 'text-green-500' };
  if (isDarkMode.value) {
    const dc = { info: 'text-blue-400', warning: 'text-yellow-400', error: 'text-red-400', success: 'text-green-400' };
    return dc[type] || 'text-blue-400';
  }
  return colors[type] || 'text-blue-500';
};

const getTypeBgColor = (type) => {
  if (isDarkMode.value) {
    const colors = { info: 'bg-blue-400/20', warning: 'bg-yellow-400/20', error: 'bg-red-400/20', success: 'bg-green-400/20' };
    return colors[type] || 'bg-blue-400/20';
  }
  const colors = { info: 'bg-blue-100', warning: 'bg-yellow-100', error: 'bg-red-100', success: 'bg-green-100' };
  return colors[type] || 'bg-blue-100';
};

const getTypeBgGradient = (type) => {
  const gradients = {
    info: 'bg-gradient-to-br from-blue-400 to-cyan-600',
    warning: 'bg-gradient-to-br from-yellow-400 to-amber-600',
    error: 'bg-gradient-to-br from-red-400 to-rose-600',
    success: 'bg-gradient-to-br from-green-400 to-emerald-600',
  };
  return gradients[type] || gradients.info;
};

const getTypeBadgeStyle = (type) => {
  if (isDarkMode.value) {
    const styles = { info: 'bg-blue-400/20 text-blue-300', warning: 'bg-yellow-400/20 text-yellow-300', error: 'bg-red-400/20 text-red-300', success: 'bg-green-400/20 text-green-300' };
    return styles[type] || styles.info;
  }
  const styles = { info: 'bg-blue-100 text-blue-700', warning: 'bg-yellow-100 text-yellow-700', error: 'bg-red-100 text-red-700', success: 'bg-green-100 text-green-700' };
  return styles[type] || styles.info;
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
  return notifications.value.filter(n => {
    const matchesSearch = n.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         n.message.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesType = typeFilter.value === 'all' || n.type === typeFilter.value;
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

const handleAdd = () => {
  editingNotification.value = {
    action: 'add',
    title: '',
    message: '',
    type: 'info',
    link: '',
  };
  isFormVisible.value = true;
};

// 创建通知 → POST 到后端
const handleSave = (data) => {
  const form = useForm({
    title:   data.title,
    message: data.message,
    type:    data.type,
    link:    data.link || '',
  });

  form.post(route('admin.notifications.store'), {
    onSuccess: () => {
      isFormVisible.value = false;
      editingNotification.value = null;
      toastSuccess(t('admin_notification_created') || 'Notification created');
    },
    onError: (errors) => {
      toastError(Object.values(errors).flat()[0] || t('admin_create_failed') || 'Create failed');
    },
  });
};

// 删除通知 → DELETE 到后端
const handleDelete = (id) => {
  deletingNotificationId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingNotificationId.value !== null) {
    router.delete(route('admin.notifications.destroy', deletingNotificationId.value), {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteConfirm.value = false;
        deletingNotificationId.value = null;
        toastSuccess(t('admin_notification_deleted') || 'Notification deleted');
      },
      onError: (errors) => {
        toastError(Object.values(errors).flat()[0] || t('admin_delete_failed') || 'Delete failed');
        showDeleteConfirm.value = false;
        deletingNotificationId.value = null;
      },
    });
  } else {
    showDeleteConfirm.value = false;
  }
};

// 标记单条已读
const markAsRead = (id) => {
  router.patch(
    route('admin.notifications.mark-as-read', id),
    {},
    { preserveScroll: true, preserveState: true,
      onSuccess: () => {
        toastSuccess(t('admin_notification_read') || 'Marked as read');
      },
    }
  );
};

// 全部标记已读
const markAllAsRead = () => {
  router.post(
    route('admin.notifications.mark-all-as-read'),
    {},
    { preserveScroll: true, preserveState: true,
      onSuccess: () => {
        toastSuccess(t('admin_all_notifications_read') || 'All marked as read');
      },
    }
  );
};

const handleFilterChange = ({ key, value }) => {
  if (key === 'type') {
    typeFilter.value = value;
  }
  currentPage.value = 1;
};
</script>

<template>
  <div class="notifications-page p-8" :class="isDarkMode ? 'is-dark' : 'is-light'">
    <!-- Page Header -->
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Bell class="text-construct-red" size="32" />
            <h2 class="page-title" :class="isDarkMode ? 'page-title--dark' : 'page-title--light'">{{ t('admin_notifications') }}</h2>
          </div>
          <p class="page-subtitle" :class="isDarkMode ? 'page-subtitle--dark' : 'page-subtitle--light'">Manage system notifications</p>
        </div>
        <button @click="handleAdd" class="btn-primary">
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

    <!-- Notification List -->
    <div class="space-y-4">
      <!-- List Toolbar -->
      <div v-if="props.unreadCount > 0 && filteredNotifications.length > 0" class="list-toolbar">
        <div class="flex items-center gap-2">
          <span class="list-toolbar-dot"></span>
          <span class="list-toolbar-count">{{ props.unreadCount }} {{ t('admin_pending').toLowerCase() }}</span>
        </div>
        <button @click="markAllAsRead" class="list-toolbar-action">
          <CheckCircle size="15" />
          {{ t('admin_mark_all_read') }}
        </button>
      </div>

      <template v-if="filteredNotifications.length > 0">
        <div
          v-for="notification in paginatedNotifications"
          :key="notification.id"
          class="notification-card"
          :class="{ 'notification-card--unread': !notification.read }"
        >
          <div class="flex items-start gap-4">
            <!-- Icon -->
            <div class="notification-icon" :class="'notification-icon--' + notification.type">
              <component :is="getTypeIcon(notification.type)" class="w-6 h-6" :class="getTypeColor(notification.type)" />
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                  <span class="notification-title" :class="{ 'notification-title--unread': !notification.read }">
                    {{ notification.title }}
                  </span>
                  <span v-if="!notification.read" class="badge-new">NEW</span>
                </div>
                <div class="flex items-center gap-2">
                  <Clock size="16" class="notification-meta" style="margin-bottom: 0;" />
                  <span class="notification-meta" style="margin-bottom: 0;">{{ formatToShort(notification.created_at) }}</span>
                </div>
              </div>

              <p class="notification-body">{{ notification.message }}</p>

              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <span class="type-badge" :class="'type-badge--' + notification.type">
                    {{ notification.type }}
                  </span>
                  <a
                    v-if="notification.link"
                    :href="notification.link"
                    target="_blank"
                    class="flex items-center gap-1 text-xs font-medium transition-colors"
                    :style="{ color: isDarkMode ? '#60a5fa' : '#2563eb' }"
                  >
                    <Link size="12" />
                    {{ t('admin_view_link') }}
                  </a>
                </div>

                <div class="flex items-center gap-2">
                  <button
                    v-if="!notification.read"
                    @click="markAsRead(notification.id)"
                    class="action-btn action-btn--read"
                    :title="t('admin_mark_read')"
                  >
                    <CheckCircle size="16" />
                  </button>
                  <button
                    @click="handleView(notification)"
                    class="action-btn action-btn--view"
                    :title="t('admin_view')"
                  >
                    <Eye size="16" />
                  </button>
                  <button
                    @click="handleDelete(notification.id)"
                    class="action-btn action-btn--delete"
                    :title="t('admin_delete')"
                  >
                    <Trash2 size="16" />
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
          :total-items="filteredNotifications.length"
          v-model:items-per-page="itemsPerPage"
          @update:current-page="currentPage = $event"
        />
      </template>

      <!-- Empty State -->
      <template v-else>
        <div class="mt-8">
          <EmptyState
            :title="t('admin_no_notifications_found')"
            :description="t('admin_no_notifications_description')"
            :icon="Bell"
          />
        </div>
      </template>
    </div>

    <!-- Notification Form Modal -->
    <Transition name="modal">
      <div
        v-if="isFormVisible"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay"
        @click.self="isFormVisible = false"
      >
        <div class="modal-box modal-box--ring w-full max-w-lg flex flex-col max-h-[85vh]">
          <!-- Header (sticky) -->
          <div class="modal-header">
            <div class="flex items-center gap-4">
              <div class="modal-header-icon modal-header-icon--gradient-info">
                <Bell :size="18" style="color: #ffffff;" />
              </div>
              <div>
                <h2 class="modal-title" :class="isDarkMode ? 'modal-title--dark' : 'modal-title--light'">
                  {{ t('admin_add_notification') }}
                </h2>
                <p class="modal-subtitle">创建新的系统通知</p>
              </div>
            </div>
            <button @click="isFormVisible = false" class="btn-close">
              <X :size="20" />
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
    <Transition name="modal">
      <div
        v-if="showDetailModal"
        class="fixed inset-0 z-50 flex items-center justify-center modal-overlay"
        @click.self="showDetailModal = false"
      >
        <div class="modal-box modal-box--ring w-full max-w-xl mx-4 flex flex-col max-h-[85vh]">
          <!-- Header (sticky) -->
          <div class="modal-header">
            <div class="flex items-center gap-4">
              <div class="modal-header-icon" :class="'modal-header-icon--gradient-' + (viewingNotification?.type || 'info')">
                <component :is="getTypeIcon(viewingNotification?.type)" :size="18" style="color: #ffffff;" />
              </div>
              <div>
                <h3 class="modal-title" :class="isDarkMode ? 'modal-title--dark' : 'modal-title--light'">
                  {{ viewingNotification?.title }}
                </h3>
                <p class="modal-subtitle">通知详情</p>
              </div>
            </div>
            <button @click="showDetailModal = false" class="btn-close">
              <X :size="20" />
            </button>
          </div>

          <!-- Body (scrollable) -->
          <div class="modal-body" :class="isDarkMode ? 'modal-body--dark' : 'modal-body--light'" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <!-- Tags row -->
            <div class="flex items-center gap-2 flex-wrap">
              <span class="detail-type-badge" :class="'detail-type-badge--' + (viewingNotification?.type || 'info')">
                {{ viewingNotification?.type }}
              </span>
              <span v-if="!viewingNotification?.read" class="detail-unread-badge">UNREAD</span>
            </div>

            <!-- Message -->
            <div>
              <label class="detail-label">通知内容</label>
              <div class="detail-message-box">{{ viewingNotification?.message }}</div>
            </div>

            <!-- Link -->
            <div v-if="viewingNotification?.link">
              <label class="detail-label">关联链接</label>
              <a :href="viewingNotification?.link" target="_blank" class="detail-link-box group">
                <Link :size="16" class="detail-link-icon--left" />
                <span class="truncate flex-1">{{ viewingNotification?.link }}</span>
                <ExternalLink :size="14" class="detail-link-icon--right" />
              </a>
            </div>

            <!-- Time -->
            <div class="detail-time">
              <Clock :size="14" class="flex-shrink-0" />
              <span>{{ formatToShort(viewingNotification?.created_at) }}</span>
            </div>
          </div>

          <!-- Footer (sticky) -->
          <div class="modal-footer">
            <button @click="showDetailModal = false" class="btn-cancel">
              {{ t('admin_close') }}
            </button>
            <button
              v-if="!viewingNotification?.read"
              @click="markAsRead(viewingNotification.id); showDetailModal = false"
              class="btn-mark-read"
            >
              <CheckCircle :size="16" style="color: #ffffff;" />
              {{ t('admin_mark_read') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

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
/* ========== Dark/Light Mode Context ========== */
.notifications-page.is-dark {
  --n-bg: #111827;
  --n-surface: #1f2937;
  --n-surface-hover: rgba(55, 65, 81, 0.5);
  --n-surface-alt: rgba(55, 65, 81, 0.5);
  --n-border: #374151;
  --n-text: #d1d5db;
  --n-text-secondary: #9ca3af;
  --n-text-muted: #6b7280;
  --n-card-bg: rgba(31, 41, 55, 0.5);
  --n-card-hover-border: #CF202E;
  --n-input-bg: #374151;
  --n-input-border: #4b5563;
  --n-input-text: #ffffff;
  --n-input-placeholder: #6b7280;
  --n-modal-bg: #1f2937;
  --n-modal-ring: #374151;
  --n-modal-overlay: rgba(0, 0, 0, 0.6);
  --n-icon-bg-info: rgba(96, 165, 250, 0.2);
  --n-icon-bg-warning: rgba(250, 204, 21, 0.2);
  --n-icon-bg-error: rgba(248, 113, 113, 0.2);
  --n-icon-bg-success: rgba(74, 222, 128, 0.2);
  --n-type-color-inactive: #374151;
  --n-type-text-inactive: #d1d5db;
  --n-link-color: #60a5fa;
  --n-link-hover: #93bbfd;
  --n-action-hover: rgba(55, 65, 81, 0.7);
  --n-empty-icon: #4b5563;
  --n-unread-bar: #CF202E;
  --n-divider: #374151;
}

.notifications-page.is-light {
  --n-bg: #f9fafb;
  --n-surface: #ffffff;
  --n-surface-hover: #f3f4f6;
  --n-surface-alt: #f9fafb;
  --n-border: #e5e7eb;
  --n-text: #374151;
  --n-text-secondary: #6b7280;
  --n-text-muted: #9ca3af;
  --n-card-bg: #ffffff;
  --n-card-hover-border: #CF202E;
  --n-input-bg: #ffffff;
  --n-input-border: #d1d5db;
  --n-input-text: #111827;
  --n-input-placeholder: #9ca3af;
  --n-modal-bg: #ffffff;
  --n-modal-ring: rgba(229, 231, 235, 0.8);
  --n-modal-overlay: rgba(0, 0, 0, 0.6);
  --n-icon-bg-info: #dbeafe;
  --n-icon-bg-warning: #fef9c3;
  --n-icon-bg-error: #fee2e2;
  --n-icon-bg-success: #dcfce7;
  --n-type-color-inactive: #f3f4f6;
  --n-type-text-inactive: #4b5563;
  --n-link-color: #2563eb;
  --n-link-hover: #1d4ed8;
  --n-action-hover: #f3f4f6;
  --n-empty-icon: #d1d5db;
  --n-unread-bar: #CF202E;
  --n-divider: #e5e7eb;
}

.font-display {
  font-family: 'Outfit', sans-serif;
}

/* ========== Page Header ========== */
.page-title {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 2.25rem;
  letter-spacing: -0.05em;
}
.page-title--dark { color: #ffffff; }
.page-title--light { color: #111827; }

.page-subtitle {
  font-size: 0.875rem;
  font-weight: 900;
  letter-spacing: 0.2em;
  text-transform: uppercase;
}
.page-subtitle--dark { color: rgba(156, 163, 175, 0.5); }
.page-subtitle--light { color: rgba(107, 114, 128, 0.5); }

/* ========== Buttons ========== */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: var(--accent, #CF202E);
  color: #ffffff;
  font-weight: 900;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  transition: all 0.2s ease;
  border-radius: 0.75rem;
  box-shadow: 0 4px 16px rgba(207, 32, 46, 0.2);
}
.btn-primary:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 24px rgba(207, 32, 46, 0.3);
}
.btn-primary:active {
  transform: scale(0.95);
}

/* ========== Notification Card ========== */
.notification-card {
  padding: 1.5rem;
  background: var(--n-card-bg);
  border: 1px solid var(--n-border);
  border-radius: 1rem;
  transition: all 0.2s ease;
  position: relative;
  overflow: hidden;
}
.notification-card:hover {
  border-color: var(--n-card-hover-border);
}
.notification-card--unread {
  border-left: 4px solid var(--n-unread-bar);
}

.notification-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.notification-icon--info    { background: var(--n-icon-bg-info); }
.notification-icon--warning { background: var(--n-icon-bg-warning); }
.notification-icon--error   { background: var(--n-icon-bg-error); }
.notification-icon--success { background: var(--n-icon-bg-success); }

.notification-title {
  font-weight: 700;
  font-size: 1.125rem;
  color: var(--n-text);
}
.notification-title--unread {
  color: var(--accent, #CF202E);
}

.notification-body {
  color: var(--n-text-secondary);
  margin-bottom: 1rem;
}

.notification-meta {
  color: var(--n-text-muted);
  font-size: 0.875rem;
}

/* ========== Badges ========== */
.badge-new {
  display: inline-block;
  padding: 0.125rem 0.5rem;
  font-size: 0.75rem;
  font-weight: 700;
  background: var(--accent, #CF202E);
  color: #ffffff;
  border-radius: 0.25rem;
  text-transform: uppercase;
}

.type-badge {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  border-radius: 0.25rem;
  background: var(--n-type-color-inactive);
  color: var(--n-type-text-inactive);
}
.type-badge--info    { background: var(--n-icon-bg-info);    color: #3b82f6; }
.type-badge--warning { background: var(--n-icon-bg-warning); color: #eab308; }
.type-badge--error   { background: var(--n-icon-bg-error);   color: #ef4444; }
.type-badge--success { background: var(--n-icon-bg-success); color: #22c55e; }

/* ========== Action Buttons ========== */
.action-btn {
  padding: 0.5rem;
  border-radius: 0.5rem;
  transition: all 0.15s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.action-btn:hover {
  background: var(--n-action-hover);
}
.action-btn--read   { color: #22c55e; }
.action-btn--view   { color: var(--n-text-muted); }
.action-btn--delete { color: #ef4444; }
.action-btn--delete:hover { background: rgba(239, 68, 68, 0.1); }

/* ========== List Toolbar ========== */
.list-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.625rem 1rem;
  background: var(--n-surface-alt);
  border: 1px solid var(--n-border);
  border-radius: 0.75rem;
  margin-bottom: 0.25rem;
}
.list-toolbar-dot {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 50%;
  background: var(--accent, #CF202E);
  animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}
.list-toolbar-count {
  font-size: 0.8125rem;
  font-weight: 700;
  color: var(--n-text-muted);
}
.list-toolbar-action {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.375rem 0.875rem;
  font-size: 0.75rem;
  font-weight: 700;
  color: #22c55e;
  border-radius: 0.5rem;
  background: transparent;
  transition: all 0.15s ease;
}
.list-toolbar-action:hover {
  background: rgba(34, 197, 94, 0.1);
}

/* ========== Empty State ========== */
.empty-state {
  padding: 3rem;
  text-align: center;
  border: 1px solid var(--n-border);
  border-radius: 1rem;
  background: var(--n-surface-alt);
}
.empty-state-icon {
  color: var(--n-empty-icon);
  margin: 0 auto 1rem;
}
.empty-state-text {
  font-size: 0.875rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--n-text-muted);
}

/* ========== Modal ========== */
.modal-overlay {
  background: var(--n-modal-overlay);
  backdrop-filter: blur(4px);
}
.modal-box {
  background: var(--n-modal-bg);
  border-radius: 1rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}
.modal-box--ring {
  box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25), 0 0 0 1px var(--n-modal-ring);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem 1.5rem 0.75rem;
  flex-shrink: 0;
}
.modal-title {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 1.25rem;
  letter-spacing: -0.025em;
}
.modal-title--dark  { color: #ffffff; }
.modal-title--light { color: #111827; }
.modal-subtitle {
  font-size: 0.75rem;
  font-weight: 500;
  margin-top: 0.125rem;
  color: var(--n-text-muted);
}

.modal-body {
  padding: 0.5rem 1.5rem;
  overflow-y: auto;
  flex: 1;
  scroll-behavior: smooth;
}
.modal-body--dark  { color: #d1d5db; }
.modal-body--light { color: #374151; }

.modal-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem 1.5rem;
  flex-shrink: 0;
}

.btn-close {
  padding: 0.5rem;
  border-radius: 0.75rem;
  transition: background 0.15s ease, color 0.15s ease;
  flex-shrink: 0;
  color: var(--n-text-muted);
}
.btn-close:hover {
  background: var(--n-action-hover);
}

.btn-cancel {
  padding: 0.75rem 1.5rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  font-size: 0.875rem;
  border-radius: 0.75rem;
  border: 1px solid var(--n-border);
  color: var(--n-text-secondary);
  background: transparent;
  transition: all 0.15s ease;
}
.btn-cancel:hover {
  background: var(--n-action-hover);
}

.btn-mark-read {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: #16a34a;
  color: #ffffff;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  font-size: 0.875rem;
  border-radius: 0.75rem;
  box-shadow: 0 4px 16px rgba(22, 163, 74, 0.2);
  transition: all 0.15s ease;
}
.btn-mark-read:hover {
  background: #15803d;
}

/* ========== Detail Content ========== */
.detail-label {
  display: block;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  margin-bottom: 0.75rem;
  color: var(--n-text-muted);
}
.detail-message-box {
  padding: 1rem;
  border-radius: 0.75rem;
  font-size: 0.875rem;
  line-height: 1.625;
  background: var(--n-surface-alt);
  color: var(--n-text-secondary);
}
.detail-link-box {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  border-radius: 0.75rem;
  border: 1px solid var(--n-border);
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.15s ease;
  text-decoration: none;
  background: var(--n-surface-alt);
  color: var(--n-link-color);
}
.detail-link-box:hover {
  border-color: var(--n-link-color);
  background: var(--n-action-hover);
}
.detail-link-icon--left {
  flex-shrink: 0;
  color: var(--n-text-muted);
}
.detail-link-icon--right {
  flex-shrink: 0;
  opacity: 0;
  transition: opacity 0.15s ease;
  color: var(--n-text-muted);
}
.detail-link-box:hover .detail-link-icon--right {
  opacity: 1;
}
.detail-time {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: var(--n-text-muted);
}

/* ========== Modal Header Icon ========== */
.modal-header-icon {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.modal-header-icon--gradient-info    { background: linear-gradient(135deg, #60a5fa, #4f46e5); }
.modal-header-icon--gradient-warning { background: linear-gradient(135deg, #facc15, #d97706); }
.modal-header-icon--gradient-error   { background: linear-gradient(135deg, #f87171, #e11d48); }
.modal-header-icon--gradient-success { background: linear-gradient(135deg, #4ade80, #059669); }

/* ========== Detail Badge ========== */
.detail-type-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  font-size: 0.625rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  border-radius: 0.5rem;
}
.detail-type-badge--info    { background: var(--n-icon-bg-info);    color: #3b82f6; }
.detail-type-badge--warning { background: var(--n-icon-bg-warning); color: #eab308; }
.detail-type-badge--error   { background: var(--n-icon-bg-error);   color: #ef4444; }
.detail-type-badge--success { background: var(--n-icon-bg-success); color: #22c55e; }

.detail-unread-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  font-size: 0.625rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  background: var(--accent, #CF202E);
  color: #ffffff;
  border-radius: 0.5rem;
}

/* ========== Modal Transitions ========== */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-active .modal-box,
.modal-leave-active .modal-box {
  transition: transform 0.35s ease, opacity 0.3s ease;
}
.modal-enter-from .modal-box,
.modal-leave-to .modal-box {
  transform: scale(0.95) translateY(10px);
  opacity: 0;
}
</style>
