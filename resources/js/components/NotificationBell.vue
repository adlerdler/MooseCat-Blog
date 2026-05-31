<script setup>
/**
 * NotificationBell.vue - 通知铃铛组件
 *
 * 功能说明：
 * - 读取 Inertia 共享的 notifications + unreadCount
 * - 点击展开通知列表
 * - 支持标记已读/全部已读
 * - 通知类型区分（info/warning/error/success）
 */
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { Bell, Check, CheckCheck, Trash2, Info, AlertTriangle, AlertCircle, CheckCircle } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { useTheme } from '../composables/useTheme';

const { t } = useI18n();
const { isDarkMode } = useTheme();
const page = usePage();

const isOpen = ref(false);
const dropdownRef = ref(null);

// 从 Inertia 共享 props 读取通知数据
const notifications = computed(() => page.props.notifications || []);
const unreadCount = computed(() => page.props.unreadCount ?? notifications.value.filter(n => !n.read).length);

const typeConfig = {
  info:    { icon: Info,          color: 'text-blue-500' },
  warning: { icon: AlertTriangle,  color: 'text-yellow-500' },
  error:   { icon: AlertCircle,    color: 'text-red-500' },
  success: { icon: CheckCircle,    color: 'text-green-500' },
};

const toggleDropdown = (e) => {
  if (e) e.stopPropagation();
  isOpen.value = !isOpen.value;
};

const handleClickOutside = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

const markAsRead = (id) => {
  router.patch(
    route('admin.notifications.mark-as-read', id),
    {},
    { preserveScroll: true, preserveState: true }
  );
};

const markAllAsRead = () => {
  router.post(
    route('admin.notifications.mark-all-as-read'),
    {},
    { preserveScroll: true, preserveState: true }
  );
};

const deleteNotification = (id) => {
  router.delete(
    route('admin.notifications.destroy', id),
    { preserveScroll: true, preserveState: true }
  );
};

const formatTime = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  const now = new Date();
  const diff = now - date;
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(diff / 3600000);
  const days = Math.floor(diff / 86400000);

  if (minutes < 1) return t('admin_just_now');
  if (minutes < 60) return `${minutes}${t('admin_minutes_ago')}`;
  if (hours < 24) return `${hours}${t('admin_hours_ago')}`;
  return `${days}${t('admin_days_ago')}`;
};
</script>

<template>
  <div ref="dropdownRef" class="relative">
    <button
      @click.stop="toggleDropdown($event)"
      class="relative p-2 rounded-lg transition-colors group"
      :class="[
        isOpen
          ? isDarkMode ? 'bg-gray-700 text-white' : 'bg-gray-200 text-gray-900'
          : isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'
      ]"
    >
      <Bell size="20" class="group-hover:scale-110 transition-transform" />
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 w-5 h-5 bg-construct-red text-white text-xs font-bold rounded-full flex items-center justify-center"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <Transition name="notification-dropdown">
      <div
        v-if="isOpen"
        class="absolute right-0 top-full mt-2 w-80 sm:w-96 rounded-xl shadow-xl z-50 overflow-hidden"
        :class="isDarkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'"
      >
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3"
          :class="isDarkMode ? 'border-b border-gray-700' : 'border-b border-gray-200'">
          <h3 class="font-display text-sm font-bold tracking-tight">
            {{ t('admin_notifications_title') }}
          </h3>
          <button
            v-if="unreadCount > 0"
            @click="markAllAsRead"
            class="text-xs font-bold tracking-widest text-construct-red hover:underline uppercase"
          >
            {{ t('admin_mark_all_read') }}
          </button>
        </div>

        <!-- Notification List -->
        <div class="max-h-96 overflow-y-auto">
          <div v-if="notifications.length === 0" class="p-8 text-center"
            :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">
            <Bell size="32" class="mx-auto mb-3 opacity-30" />
            <p class="text-xs font-bold tracking-widest uppercase">
              {{ t('admin_no_notifications') }}
            </p>
          </div>

          <div
            v-for="notification in notifications"
            :key="notification.id"
            class="group flex gap-3 px-4 py-3 transition-colors cursor-pointer"
            :class="[
              isDarkMode ? 'border-b border-gray-700/50 hover:bg-gray-700/50' : 'border-b border-gray-100 hover:bg-gray-50',
              !notification.read ? (isDarkMode ? 'bg-gray-700/30' : 'bg-gray-50/50') : ''
            ]"
          >
            <!-- Read / Unread indicator -->
            <button
              v-if="!notification.read"
              @click="markAsRead(notification.id)"
              class="shrink-0 mt-0.5 p-0.5 rounded hover:bg-green-500/20 transition-colors"
              title="Mark as read"
            >
              <div class="w-8 h-8 rounded-full flex items-center justify-center"
                :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-100'">
                <component
                  :is="typeConfig[notification.type]?.icon || typeConfig.info.icon"
                  :class="['w-4 h-4', typeConfig[notification.type]?.color || typeConfig.info.color]"
                />
              </div>
            </button>
            <button
              v-else
              @click="() => {}"
              class="shrink-0 mt-0.5 p-0.5 rounded"
            >
              <div class="w-8 h-8 rounded-full flex items-center justify-center opacity-50"
                :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-100'">
                <Check class="w-4 h-4 text-gray-400" />
              </div>
            </button>

            <!-- Content -->
            <div class="flex-1 min-w-0" @click="!notification.read && markAsRead(notification.id)">
              <div class="flex items-start justify-between gap-2">
                <h4 class="text-sm font-bold truncate"
                  :class="notification.read
                    ? (isDarkMode ? 'text-gray-400' : 'text-gray-600')
                    : (isDarkMode ? 'text-white' : 'text-gray-900')">
                  {{ notification.title }}
                </h4>
              </div>
              <p class="text-xs mt-1 line-clamp-2"
                :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                {{ notification.message }}
              </p>
              <div class="flex items-center justify-between mt-2">
                <span class="text-[10px] font-bold tracking-widest uppercase"
                  :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">
                  {{ formatTime(notification.created_at) }}
                </span>
                <button
                  @click.stop="deleteNotification(notification.id)"
                  class="opacity-0 group-hover:opacity-100 p-1 rounded transition-all"
                  :class="isDarkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-200'"
                >
                  <Trash2 size="12" class="text-gray-400 hover:text-red-500" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t"
          :class="isDarkMode ? 'border-gray-700 bg-gray-800/50' : 'border-gray-200 bg-gray-50'">
          <router-link
            to="/admin/notifications"
            @click="isOpen = false"
            class="text-xs font-bold tracking-widest text-construct-red hover:underline uppercase text-center block"
          >
            {{ t('admin_view_all_notifications') }}
          </router-link>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style lang="scss" scoped>
.notification-dropdown {
  &-enter-active,
  &-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
  }

  &-enter-from,
  &-leave-to {
    opacity: 0;
    transform: translateY(-8px);
  }
}
</style>
