<script setup>
/**
 * Subscribers.vue - 订阅者管理页面
 * 
 * 功能说明：
 * - 管理订阅者列表
 * - 订阅者列表展示（邮箱、姓名、状态、来源、订阅时间）
 * - 订阅者搜索和筛选功能
 * - 查看、编辑、删除订阅者
 * - 订阅者状态管理（激活/未激活）
 */
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import {
  Mail,
  Eye,
  Edit3,
  Trash2,
  Calendar,
  CheckCircle,
  Plus,
  User,
  X,
  Save,
  LayoutGrid,
  List
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { useToast } from '../../composables/useToast';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import SearchFilterModal from '../../components/admin/SearchFilterModal.vue';
import Pagination from '../../components/admin/Pagination.vue';
import EmptyState from '../../components/admin/EmptyState.vue';
import { formatToShort } from '../../utils/dateUtils';

const props = defineProps({
  subscribers: {
    type: Array,
    default: () => []
  }
});

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success: toastSuccess, error: toastError } = useToast();

const getSourceLabel = (source) => {
  const labels = {
    website: '网站',
    newsletter: '邮件订阅',
    social: '社交媒体',
  };
  return labels[source] || source;
};

const getSourceOptions = () => ['website', 'newsletter', 'social'];

const searchQuery = ref('');
const statusFilter = ref('all');
const sourceFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = 8;
const isFormVisible = ref(false);
const editingSubscriber = ref(null);
const showDeleteConfirm = ref(false);
const deletingSubscriberId = ref(null);

// Load saved view mode from localStorage
const savedViewMode = localStorage.getItem('admin_subscribers_view_mode');
const viewMode = ref(savedViewMode || 'card'); // 'card' or 'table'

// Save view mode when changed
watch(viewMode, (newMode) => {
  localStorage.setItem('admin_subscribers_view_mode', newMode);
});

const subscribers = ref([...props.subscribers]);

watch(() => props.subscribers, (newSubscribers) => {
  subscribers.value = [...newSubscribers];
});

const filteredSubscribers = computed(() => {
  return subscribers.value.filter(subscriber => {
    const matchesSearch = subscriber.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         subscriber.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = statusFilter.value === 'all' || 
                         (statusFilter.value === 'active' && subscriber.is_active) ||
                         (statusFilter.value === 'inactive' && !subscriber.is_active);
    const matchesSource = sourceFilter.value === 'all' || subscriber.source === sourceFilter.value;
    return matchesSearch && matchesStatus && matchesSource;
  });
});

const totalPages = computed(() => Math.ceil(filteredSubscribers.value.length / itemsPerPage));

const sourceSelectArrow = computed(() => {
  const color = isDarkMode.value ? '%239ca3af' : '%236b7280';
  return `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='${color}' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E")`;
});

const paginatedSubscribers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredSubscribers.value.slice(start, start + itemsPerPage);
});

const toggleStatus = (subscriber) => {
  router.put(`/admin/subscribers/${subscriber.id}`, {
    ...subscriber,
    is_active: !subscriber.is_active,
  }, {
    preserveState: false,
    onSuccess: () => {
      toastSuccess(subscriber.is_active ? t('admin_subscriber_activated') || '已激活' : t('admin_subscriber_deactivated') || '已停用');
    },
  });
};

const handleView = (subscriber) => {
  editingSubscriber.value = { ...subscriber, action: 'view' };
  isFormVisible.value = true;
};

const handleEdit = (subscriber) => {
  editingSubscriber.value = { ...subscriber, action: 'edit' };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingSubscriber.value = { action: 'add' };
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (data.action === 'edit') {
    router.put(`/admin/subscribers/${data.id}`, data, {
      preserveState: false,
      onSuccess: () => {
        isFormVisible.value = false;
        editingSubscriber.value = null;
        toastSuccess(t('admin_save_success') || '已更新');
      },
      onError: (errors) => {
        toastError(errors?.email || t('admin_save_error') || '保存失败');
      },
    });
  } else if (data.action === 'add') {
    router.post('/admin/subscribers', data, {
      preserveState: false,
      onSuccess: () => {
        isFormVisible.value = false;
        editingSubscriber.value = null;
        toastSuccess(t('admin_save_success') || '已添加');
      },
      onError: (errors) => {
        toastError(errors?.email || t('admin_save_error') || '保存失败');
      },
    });
  }
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingSubscriber.value = null;
};

const handleDelete = (id) => {
  deletingSubscriberId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingSubscriberId.value !== null) {
    router.delete(`/admin/subscribers/${deletingSubscriberId.value}`, {
      preserveState: false,
      onSuccess: () => {
        showDeleteConfirm.value = false;
        deletingSubscriberId.value = null;
        toastSuccess(t('admin_delete_success') || '已删除');
      },
      onError: () => {
        showDeleteConfirm.value = false;
        deletingSubscriberId.value = null;
        toastError(t('admin_delete_error') || '删除失败');
      },
    });
  }
};

const handleFilterChange = ({ key, value }) => {
  if (key === 'status') {
    statusFilter.value = value;
  } else if (key === 'source') {
    sourceFilter.value = value;
  }
  currentPage.value = 1;
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <Mail class="text-construct-red" size="32" />
          <div>
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_subscribers') }}</h2>
            <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_subscribers_subtitle') }}</p>
          </div>
        </div>
        <button
          @click="handleAdd"
          class="flex items-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-wider rounded-xl shadow-sm hover:scale-105 active:scale-95 transition-all"
        >
          <Plus size="18" />
          {{ t('admin_add_subscriber') }}
        </button>
      </div>
    </div>

    <!-- Search and Filter -->
    <div class="flex items-center gap-4 mt-6">
      <div class="flex-1">
        <div class="-mb-10">
          <SearchFilterModal
            v-model:search-query="searchQuery"
            :search-placeholder="t('admin_search_subscribers')"
            :filters="[
              {
                key: 'status',
                options: [
                  { value: 'all', label: t('admin_all_status') },
                  { value: 'active', label: t('admin_active') },
                  { value: 'inactive', label: t('admin_inactive') }
                ]
              },
              {
                key: 'source',
                options: [
                  { value: 'all', label: t('admin_all_sources') },
                  ...getSourceOptions().map(source => ({ value: source, label: getSourceLabel(source) }))
                ]
              }
            ]"
            :filter-values="{ status: statusFilter, source: sourceFilter }"
            @filter-change="handleFilterChange"
          />
        </div>
      </div>
      <!-- View Toggle -->
      <div class="flex items-center gap-1 p-1 rounded-xl flex-shrink-0 h-14" :class="isDarkMode ? 'bg-gray-800' : 'bg-gray-100'">
        <button
          @click="viewMode = 'card'"
          :class="['p-3 rounded-lg transition-all h-full flex items-center justify-center', viewMode === 'card' ? 'bg-white dark:bg-gray-700 shadow-sm text-construct-red' : 'text-gray-400 hover:text-gray-600']"
          title="Card View"
        >
          <LayoutGrid size="18" />
        </button>
        <button
          @click="viewMode = 'table'"
          :class="['p-3 rounded-lg transition-all h-full flex items-center justify-center', viewMode === 'table' ? 'bg-white dark:bg-gray-700 shadow-sm text-construct-red' : 'text-gray-400 hover:text-gray-600']"
          title="Table View"
        >
          <List size="18" />
        </button>
      </div>
    </div>

    <!-- Content Area -->
    <template v-if="filteredSubscribers.length > 0">
      <!-- Subscribers Cards -->
      <div v-if="viewMode === 'card'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-6">
        <div
          v-for="subscriber in paginatedSubscribers"
          :key="subscriber.id"
          :class="['p-6 rounded-xl transition-all duration-200 hover:scale-[1.02]', isDarkMode ? 'bg-gray-800/80 hover:bg-gray-800' : 'bg-white hover:bg-gray-50']"
        >
          <!-- Subscriber Header -->
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div :class="['w-12 h-12 rounded-xl flex items-center justify-center overflow-hidden shadow-md', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
                <User :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" size="20" />
              </div>
              <div>
                <h4 :class="['font-bold text-lg', isDarkMode ? 'text-white' : 'text-gray-900']">{{ subscriber.name }}</h4>
                <p :class="['text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ formatToShort(subscriber.subscribed_at) }}</p>
              </div>
            </div>
            <!-- Status Toggle -->
            <button
              @click="toggleStatus(subscriber)"
              class="relative"
            >
              <div :class="['w-12 h-6 rounded-full relative transition-colors flex-shrink-0', subscriber.is_active ? 'bg-green-500' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                <div :class="['absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform', subscriber.is_active ? 'translate-x-[1.625rem]' : 'translate-x-0.5']"></div>
              </div>
            </button>
          </div>

          <!-- Email -->
          <div :class="['flex items-center gap-2 mb-4 text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
            <Mail size="14" class="flex-shrink-0" />
            <span class="truncate" :title="subscriber.email">{{ subscriber.email }}</span>
          </div>

          <!-- Source -->
          <div class="mb-4">
            <span :class="['inline-block px-3 py-1 rounded-full text-xs font-bold', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']">{{ getSourceLabel(subscriber.source) }}</span>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2">
            <button @click="handleView(subscriber)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-700']">
              <Eye size="16" />
            </button>
            <button @click="handleEdit(subscriber)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-700']">
              <Edit3 size="16" />
            </button>
            <button @click="handleDelete(subscriber.id)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-red-500/20 text-gray-400 hover:text-red-400' : 'hover:bg-red-50 text-gray-500 hover:text-red-500']">
              <Trash2 size="16" />
            </button>
            <span :class="['ml-auto text-xs font-bold', isDarkMode ? 'text-green-400' : 'text-green-600']" v-if="subscriber.is_active">
              {{ t('admin_active') }}
            </span>
            <span :class="['ml-auto text-xs font-bold', isDarkMode ? 'text-gray-500' : 'text-gray-400']" v-else>
              {{ t('admin_inactive') }}
            </span>
          </div>
        </div>
      </div>

      <!-- Subscribers Table -->
      <div v-if="viewMode === 'table'" :class="['mt-6 rounded-xl overflow-hidden', isDarkMode ? 'bg-gray-800/80' : 'bg-white']">
        <table class="w-full">
          <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-100/50'">
            <tr>
              <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_email') }}</th>
              <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_name') }}</th>
              <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_status') }}</th>
              <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_source') }}</th>
              <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_subscribed_at') }}</th>
              <th :class="['px-6 py-4 text-center text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="subscriber in paginatedSubscribers" :key="subscriber.id" :class="['transition-colors', isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50']">
              <td class="px-6 py-4">
                <div :class="['flex items-center gap-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                  <Mail size="14" />
                  <span>{{ subscriber.email }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div :class="['w-10 h-10 rounded-xl flex items-center justify-center', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
                    <User :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" size="18" />
                  </div>
                  <span :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ subscriber.name }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <button
                  @click="toggleStatus(subscriber)"
                  class="flex items-center gap-2 cursor-pointer"
                >
                  <div :class="['w-12 h-6 rounded-full relative transition-colors', subscriber.is_active ? 'bg-green-500' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                    <div :class="['absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform', subscriber.is_active ? 'translate-x-[1.625rem]' : 'translate-x-0.5']"></div>
                  </div>
                  <span :class="['text-sm font-bold', subscriber.is_active ? (isDarkMode ? 'text-green-400' : 'text-green-600') : (isDarkMode ? 'text-gray-500' : 'text-gray-400')]">
                    {{ subscriber.is_active ? t('admin_active') : t('admin_inactive') }}
                  </span>
                </button>
              </td>
              <td :class="['px-6 py-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ getSourceLabel(subscriber.source) }}</td>
              <td :class="['px-6 py-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ formatToShort(subscriber.subscribed_at) }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-1">
                  <button @click="handleView(subscriber)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-700']">
                    <Eye size="16" />
                  </button>
                  <button @click="handleEdit(subscriber)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-700']">
                    <Edit3 size="16" />
                  </button>
                  <button @click="handleDelete(subscriber.id)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-red-500/20 text-gray-400 hover:text-red-400' : 'hover:bg-red-50 text-gray-500 hover:text-red-500']">
                    <Trash2 size="16" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination
        :current-page="currentPage"
        :total-pages="totalPages"
        :total-items="filteredSubscribers.length"
        :items-per-page="itemsPerPage"
        @update:current-page="(page) => currentPage = page"
      />
    </template>

    <!-- Empty State -->
    <div v-else class="mt-6">
      <EmptyState 
        :title="t('admin_no_subscribers_found') || 'No subscribers found'"
        :description="t('admin_no_subscribers_description') || 'Try adjusting your search or filters to find what you are looking for'"
        :icon="Mail"
      />
    </div>

    <!-- Add/Edit/View Form -->
    <Transition name="modal">
      <div
        v-if="isFormVisible"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50"
        @click.self="handleCancel"
      >
        <div :class="['modal-content w-full max-w-lg mx-4 flex flex-col max-h-[85vh] rounded-2xl shadow-2xl ring-1', isDarkMode ? 'bg-gray-800 ring-gray-700' : 'bg-white ring-gray-200/80']">
          <!-- Header (sticky top) -->
          <div class="flex items-center justify-between p-6 pb-3 flex-shrink-0">
            <div class="flex items-center gap-4">
              <div :class="['w-10 h-10 rounded-xl shadow-md flex items-center justify-center flex-shrink-0', editingSubscriber?.action === 'view' ? 'bg-gradient-to-br from-sky-500 to-cyan-600' : editingSubscriber?.action === 'edit' ? 'bg-gradient-to-br from-amber-500 to-orange-600' : 'bg-gradient-to-br from-emerald-500 to-teal-600']">
                <component :is="editingSubscriber?.action === 'view' ? Eye : editingSubscriber?.action === 'edit' ? Edit3 : Plus" :size="18" :style="{ color: '#ffffff' }" />
              </div>
              <div>
                <h3 :class="['font-display text-xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">
                  {{ editingSubscriber?.action === 'add' ? t('admin_add_subscriber') : editingSubscriber?.action === 'edit' ? t('admin_edit_subscriber') : t('admin_view_subscriber') }}
                </h3>
                <p :class="['text-xs font-medium mt-0.5', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                  {{ editingSubscriber?.action === 'add' ? '创建新的订阅者' : editingSubscriber?.action === 'edit' ? '编辑订阅者信息' : '查看订阅者详情' }}
                </p>
              </div>
            </div>
            <button @click="handleCancel" :class="['p-2 rounded-xl transition-colors flex-shrink-0', isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500']">
              <X :size="20" />
            </button>
          </div>

          <form @submit.prevent="handleSave(editingSubscriber)" class="flex-1 flex flex-col overflow-hidden min-h-0">
            <!-- Body (scrollable) -->
            <div class="px-6 py-2 space-y-4 overflow-y-auto flex-1 scroll-smooth">
              <!-- Email -->
              <div>
                <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                  {{ t('admin_table_email') }} <span v-if="editingSubscriber?.action !== 'view'" class="text-construct-red">*</span>
                </label>
                <input
                  v-model="editingSubscriber.email"
                  type="email"
                  required
                  :readonly="editingSubscriber?.action === 'view'"
                  :placeholder="editingSubscriber?.action === 'view' ? '' : 'subscriber@example.com'"
                  :class="[
                    'w-full px-4 py-3 rounded-xl border transition-all',
                    editingSubscriber?.action === 'view'
                      ? (isDarkMode ? 'bg-gray-800 border-gray-700 text-gray-300 cursor-default' : 'bg-gray-50 border-gray-200 text-gray-600 cursor-default')
                      : [isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-gray-50 border-gray-300 text-gray-900 placeholder-gray-400', 'focus:border-construct-red focus:outline-none']
                  ]"
                />
              </div>

              <!-- Name -->
              <div>
                <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                  {{ t('admin_table_name') }} <span v-if="editingSubscriber?.action !== 'view'" class="text-construct-red">*</span>
                </label>
                <input
                  v-model="editingSubscriber.name"
                  type="text"
                  required
                  :readonly="editingSubscriber?.action === 'view'"
                  :placeholder="editingSubscriber?.action === 'view' ? '' : '输入订阅者姓名...'"
                  :class="[
                    'w-full px-4 py-3 rounded-xl border transition-all',
                    editingSubscriber?.action === 'view'
                      ? (isDarkMode ? 'bg-gray-800 border-gray-700 text-gray-300 cursor-default' : 'bg-gray-50 border-gray-200 text-gray-600 cursor-default')
                      : [isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-gray-50 border-gray-300 text-gray-900 placeholder-gray-400', 'focus:border-construct-red focus:outline-none']
                  ]"
                />
              </div>

              <!-- Status + Source (side by side) -->
              <div class="grid grid-cols-2 gap-4">
                <!-- Status Toggle -->
                <div>
                  <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                    {{ t('admin_table_status') }}
                  </label>
                  <div class="flex items-center gap-3 py-1">
                    <button
                      type="button"
                      @click="editingSubscriber?.action !== 'view' && (editingSubscriber.is_active = !editingSubscriber.is_active)"
                      :disabled="editingSubscriber?.action === 'view'"
                      :class="[
                        'w-12 h-7 rounded-full relative transition-colors',
                        editingSubscriber?.action === 'view' ? 'cursor-default opacity-60' : 'cursor-pointer',
                        editingSubscriber.is_active ? 'bg-green-500' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')
                      ]"
                    >
                      <div :class="['absolute top-1 w-5 h-5 rounded-full bg-white shadow transition-transform', editingSubscriber.is_active ? 'left-6' : 'left-1']"></div>
                    </button>
                    <span :class="['text-sm font-semibold', editingSubscriber.is_active ? 'text-green-500' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]">
                      {{ editingSubscriber.is_active ? t('admin_active') : t('admin_inactive') }}
                    </span>
                  </div>
                </div>

                <!-- Source (polished select) -->
                <div>
                  <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                    {{ t('admin_table_source') }}
                  </label>
                  <select
                    v-model="editingSubscriber.source"
                    :disabled="editingSubscriber?.action === 'view'"
                    :class="[
                      'w-full px-4 py-3 rounded-xl border transition-all appearance-none',
                      editingSubscriber?.action === 'view'
                        ? (isDarkMode ? 'bg-gray-800 border-gray-700 text-gray-300 cursor-default opacity-60' : 'bg-gray-50 border-gray-200 text-gray-600 cursor-default opacity-60')
                        : (isDarkMode ? 'bg-gray-700 border-gray-600 text-white focus:border-construct-red focus:outline-none' : 'bg-gray-50 border-gray-300 text-gray-900 focus:border-construct-red focus:outline-none')
                    ]"
                    :style="{
                      backgroundImage: sourceSelectArrow,
                      backgroundRepeat: 'no-repeat',
                      backgroundPosition: 'right 12px center',
                      backgroundSize: '16px 16px',
                      paddingRight: '40px'
                    }"
                  >
                    <option value="website">{{ getSourceLabel('website') }}</option>
                    <option value="newsletter">{{ getSourceLabel('newsletter') }}</option>
                    <option value="social">{{ getSourceLabel('social') }}</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Footer (sticky bottom) -->
            <div class="flex items-center justify-end gap-3 p-6 pt-3 flex-shrink-0">
              <button
                type="button"
                @click="handleCancel"
                :class="['px-6 py-3 font-bold text-sm tracking-wider uppercase rounded-xl border transition-colors', isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-500 hover:bg-gray-100']"
              >
                {{ editingSubscriber?.action === 'view' ? t('admin_close') : t('admin_cancel') }}
              </button>
              <button
                v-if="editingSubscriber?.action !== 'view'"
                type="submit"
                class="flex items-center gap-2 px-6 py-3 bg-construct-red text-white font-bold text-sm tracking-wider uppercase rounded-xl shadow-lg shadow-construct-red/20 hover:bg-red-700 transition-all"
              >
                <Save :size="16" :style="{ color: '#ffffff' }" />
                {{ t('admin_save') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      :title="t('admin_confirm_delete')"
      :content="t('admin_delete_subscriber_warning')"
      :confirm-text="t('admin_delete')"
      confirm-variant="danger"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>

<style scoped>
.font-display {
  font-family: 'Outfit', sans-serif;
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .modal-content,
.modal-leave-active .modal-content {
  transition: transform 0.35s ease, opacity 0.3s ease;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
  transform: scale(0.95) translateY(10px);
  opacity: 0;
}
</style>
