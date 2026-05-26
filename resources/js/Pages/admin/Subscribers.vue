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
import {
  Mail,
  Eye,
  Edit3,
  Trash2,
  Calendar,
  CheckCircle,
  Plus,
  User
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { findById, findIndexById } from '../../utils/typeConvert';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import SearchFilterModal from '../../components/admin/SearchFilterModal.vue';
import Pagination from '../../components/admin/Pagination.vue';
import { formatToShort } from '../../utils/dateUtils';

const props = defineProps({
  subscribers: {
    type: Array,
    default: () => []
  }
});

const { t } = useI18n();
const { isDarkMode } = useTheme();

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
const showSuccessMessage = ref(false);

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

const paginatedSubscribers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredSubscribers.value.slice(start, start + itemsPerPage);
});

const toggleStatus = (subscriber) => {
  subscriber.is_active = !subscriber.is_active;
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
    const index = findIndexById(subscribers.value, data.id);
    if (index !== -1) {
      subscribers.value[index] = { 
        ...subscribers.value[index], 
        ...data,
        updated_at: new Date().toISOString().replace('T', ' ').substring(0, 19)
      };
    }
  } else if (data.action === 'add') {
    const newId = Math.max(...subscribers.value.map(s => s.id), 0) + 1;
    const now = new Date().toISOString().replace('T', ' ').substring(0, 19);
    subscribers.value.push({
      id: newId,
      ...data,
      created_at: now,
      updated_at: now
    });
  }
  
  isFormVisible.value = false;
  editingSubscriber.value = null;
  showSuccessMessage.value = true;
  
  setTimeout(() => {
    showSuccessMessage.value = false;
  }, 3000);
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
    const index = findIndexById(subscribers.value, deletingSubscriberId.value);
    if (index !== -1) {
      subscribers.value.splice(index, 1);
    }
    showDeleteConfirm.value = false;
    deletingSubscriberId.value = null;
    showSuccessMessage.value = true;
    
    setTimeout(() => {
      showSuccessMessage.value = false;
    }, 3000);
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
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Mail class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_subscribers') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_subscribers_subtitle') }}</p>
        </div>
        <button
          @click="handleAdd"
          class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
        >
          <Plus size="18" />
          {{ t('admin_add_subscriber') }}
        </button>
      </div>
    </div>

    <!-- Success Message -->
    <div v-if="showSuccessMessage" :class="['mb-6 p-4 rounded-lg flex items-center gap-3', isDarkMode ? 'bg-green-900/30 text-green-400 border border-green-700' : 'bg-green-50 text-green-700 border border-green-200']">
      <CheckCircle size="20" />
      <span class="font-medium">{{ t('admin_save_success') }}</span>
    </div>

    <!-- Search and Filter -->
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

    <!-- Subscribers Table -->
    <div :class="['border overflow-hidden', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <table class="w-full">
        <thead :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-100'">
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
          <tr v-for="subscriber in paginatedSubscribers" :key="subscriber.id" :class="['border-t transition-colors', isDarkMode ? 'border-gray-700 hover:bg-gray-700/50' : 'border-gray-200 hover:bg-gray-50']">
            <td class="px-6 py-4">
              <div :class="['flex items-center gap-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                <Mail size="14" />
                <span>{{ subscriber.email }}</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="['w-10 h-10 rounded-full flex items-center justify-center', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
                  <User :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" size="18" />
                </div>
                <span :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ subscriber.name }}</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <button
                @click="toggleStatus(subscriber)"
                class="flex items-center gap-3 cursor-pointer"
              >
                <div :class="['w-12 h-6 rounded-full relative transition-colors', subscriber.is_active ? 'bg-green-500' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                  <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', subscriber.is_active ? 'left-7' : 'left-1']"></div>
                </div>
                <span :class="['text-sm font-bold tracking-wider', subscriber.is_active ? (isDarkMode ? 'text-green-400' : 'text-green-600') : (isDarkMode ? 'text-gray-500' : 'text-gray-400')]">
                  {{ subscriber.is_active ? t('admin_active') : t('admin_inactive') }}
                </span>
              </button>
            </td>
            <td :class="['px-6 py-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ getSourceLabel(subscriber.source) }}</td>
            <td :class="['px-6 py-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ formatToShort(subscriber.subscribed_at) }}</td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2">
                <button @click="handleView(subscriber)" :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-600' : 'text-gray-500 hover:bg-gray-100']">
                  <Eye size="16" />
                </button>
                <button @click="handleEdit(subscriber)" :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-600' : 'text-gray-500 hover:bg-gray-100']">
                  <Edit3 size="16" />
                </button>
                <button @click="handleDelete(subscriber.id)" :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-red-500/20' : 'text-gray-500 hover:bg-red-50']">
                  <Trash2 size="16" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty State -->
    <div v-if="filteredSubscribers.length === 0" :class="['text-center py-16', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
      <Mail size="64" class="mx-auto mb-4 opacity-50" />
      <p class="text-xl">{{ t('admin_no_subscribers') }}</p>
    </div>

    <!-- Pagination -->
    <Pagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredSubscribers.length"
      :items-per-page="itemsPerPage"
      @update:current-page="(page) => currentPage = page"
    />

    <!-- Edit Form -->
    <div
      v-if="isFormVisible"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
      <div :class="['rounded-lg shadow-xl w-full max-w-lg mx-4 p-6', isDarkMode ? 'bg-gray-800' : 'bg-white']">
        <h2 :class="['text-xl font-bold mb-4', isDarkMode ? 'text-white' : 'text-gray-900']">
          {{ editingSubscriber?.action === 'add' ? t('admin_add_subscriber') : editingSubscriber?.action === 'edit' ? t('admin_edit_subscriber') : t('admin_view_subscriber') }}
        </h2>
        <form @submit.prevent="handleSave(editingSubscriber)" class="space-y-4">
          <div>
            <label :class="['block text-sm font-medium mb-1', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_table_email') }}</label>
            <input
              v-model="editingSubscriber.email"
              type="email"
              required
              :readonly="editingSubscriber?.action === 'view'"
              :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
            />
          </div>
          <div>
            <label :class="['block text-sm font-medium mb-1', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_table_name') }}</label>
            <input
              v-model="editingSubscriber.name"
              type="text"
              required
              :readonly="editingSubscriber?.action === 'view'"
              :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
            />
          </div>
          <div>
            <label :class="['block text-sm font-medium mb-1', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_table_status') }}</label>
            <select
              v-model="editingSubscriber.is_active"
              :disabled="editingSubscriber?.action === 'view'"
              :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
            >
              <option :value="true">{{ t('admin_active') }}</option>
              <option :value="false">{{ t('admin_inactive') }}</option>
            </select>
          </div>
          <div>
            <label :class="['block text-sm font-medium mb-1', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ t('admin_table_source') }}</label>
            <select
              v-model="editingSubscriber.source"
              :disabled="editingSubscriber?.action === 'view'"
              :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
            >
              <option value="website">{{ getSourceLabel('website') }}</option>
              <option value="newsletter">{{ getSourceLabel('newsletter') }}</option>
              <option value="social">{{ getSourceLabel('social') }}</option>
            </select>
          </div>
          <div class="flex justify-end gap-3 pt-4">
            <button
              type="button"
              @click="handleCancel"
              :class="['px-4 py-2 border rounded-lg', isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 hover:bg-gray-100']"
            >
              {{ editingSubscriber?.action === 'view' ? t('admin_close') : t('admin_cancel') }}
            </button>
            <button
              v-if="editingSubscriber?.action !== 'view'"
              type="submit"
              class="px-4 py-2 bg-construct-red text-white rounded-lg hover:bg-construct-red/90"
            >
              {{ t('admin_save') }}
            </button>
          </div>
        </form>
      </div>
    </div>

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
