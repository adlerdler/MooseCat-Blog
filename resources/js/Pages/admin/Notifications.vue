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
  Pagination,
  SearchFilterModal
} from '../../composables/useAdminImports';
import { formatToShort } from '../../utils/dateUtils';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import NotificationForm from '../../components/admin/NotificationForm.vue';

const props = defineProps({
  notifications: { type: Array, default: () => [] },
  unreadCount:  { type: Number, default: 0 },
  pagination:   { type: Object, default: () => ({}) },
});

const { t } = useI18n();
const { isDarkMode } = useTheme();

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
    },
    onError: (errors) => {
      console.error('Notification create error:', errors);
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
      },
      onError: (errors) => {
        console.error('Delete error:', errors);
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
    { preserveScroll: true, preserveState: true }
  );
};

// 全部标记已读
const markAllAsRead = () => {
  router.post(
    route('admin.notifications.mark-all-as-read'),
    {},
    { preserveScroll: true, preserveState: true }
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
      <span v-if="props.unreadCount > 0" :class="['text-xs font-bold', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
        ({{ props.unreadCount }} {{ t('admin_pending').toLowerCase() }})
      </span>
    </div>

    <!-- Notification List -->
    <div class="space-y-4">
      <div
        v-for="notification in paginatedNotifications"
        :key="notification.id"
        :class="[
          'p-6 border transition-all hover:border-construct-red',
          isDarkMode ? 'bg-gray-800/50 border-gray-700' : 'bg-white border-gray-200',
          !notification.read && 'border-l-4 border-l-construct-red'
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
                  {{ notification.type }}
                </span>
                <a
                  v-if="notification.link"
                  :href="notification.link"
                  target="_blank"
                  :class="['flex items-center gap-1 text-xs', isDarkMode ? 'text-blue-400 hover:text-blue-300' : 'text-blue-600 hover:text-blue-500']"
                >
                  <Link size="12" />
                  {{ t('admin_view_link') }}
                </a>
              </div>

              <div class="flex items-center gap-2">
                <button
                  v-if="!notification.read"
                  @click="markAsRead(notification.id)"
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
              {{ t('admin_add_notification') }}
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
                {{ viewingNotification?.type }}
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
              @click="markAsRead(viewingNotification.id); showDetailModal = false"
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
