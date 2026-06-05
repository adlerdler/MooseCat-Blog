<script setup>
/**
 * User Management Page
 * 
 * Features:
 * - User list display with pagination
 * - Search and filter functionality
 * - User CRUD operations (create, edit, delete)
 * - Role-based filtering
 * - Status toggle (active/inactive)
 */
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { Link, useForm, router } from '@inertiajs/vue3';
import { useTheme } from '../../composables/useTheme';
import {
  Users,
  Plus,
  Search,
  Edit3,
  Trash2,
  Eye,
  Filter,
  User,
  Mail,
  Shield,
  X,
  LayoutGrid,
  List,
  useToast,
  UserForm,
  ConfirmDialog,
  Pagination,
  SearchFilterModal,
  formatToShort
} from '../../composables/useAdminImports';
import { useRolePermissions } from '../../composables/useRolePermissions';

const props = defineProps({
  users: { type: Array, default: () => [] },
  roles: { type: Array, default: () => [] },
  total: { type: Number, default: 0 },
});

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success: toastSuccess, error: toastError } = useToast();
const { getRoleLabel, getRoleStyle } = useRolePermissions({
  roles: props.roles
});

const searchQuery = ref('');
const roleFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const isFormVisible = ref(false);
const editingUser = ref(null);
const serverErrors = ref({});
const showDeleteConfirm = ref(false);
const deletingUserId = ref(null);

// Load saved view mode from localStorage
const savedViewMode = localStorage.getItem('admin_users_view_mode');
const viewMode = ref(savedViewMode || 'card'); // 'card' or 'table'

// Save view mode when changed
watch(viewMode, (newMode) => {
  localStorage.setItem('admin_users_view_mode', newMode);
});

const users = computed(() => props.users || []);

const filteredUsers = computed(() => {
  return users.value.filter(user => {
    const matchesSearch = user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         user.email.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesRole = roleFilter.value === 'all' || 
      (user.roles && user.roles.length > 0 && user.roles.includes(roleFilter.value));
    return matchesSearch && matchesRole;
  });
});

const totalPages = computed(() => Math.max(1, Math.ceil(props.total / itemsPerPage.value)));

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredUsers.value.slice(start, start + itemsPerPage.value);
});

const toggleStatus = (user) => {
  const newStatus = user.status === 'active' ? 'inactive' : 'active';
  const form = useForm({ status: newStatus });
  form.patch(route('admin.users.toggle-status', user.id), {
    preserveState: true,
    onSuccess: () => {
      toastSuccess(`User ${newStatus === 'active' ? 'activated' : 'deactivated'} successfully`);
      user.status = newStatus;
    },
    onError: (errors) => {
      console.error('Status update error:', errors);
    }
  });
};

const handleEdit = (user) => {
  editingUser.value = { ...user };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingUser.value = null;
  isFormVisible.value = true;
};

const handleSave = (data) => {
  const formData = { ...data };
  
  if (editingUser.value) {
    const form = useForm(formData);
    form.put(route('admin.users.update', editingUser.value.id), {
      preserveState: true,
      onSuccess: () => {
        toastSuccess('User updated successfully');
        isFormVisible.value = false;
        editingUser.value = null;
        serverErrors.value = {};
      },
      onError: (errors) => {
        serverErrors.value = errors;
      }
    });
  } else {
    const form = useForm(formData);
    form.post(route('admin.users.store'), {
      preserveState: true,
      onSuccess: () => {
        toastSuccess('User created successfully');
        isFormVisible.value = false;
        editingUser.value = null;
        serverErrors.value = {};
      },
      onError: (errors) => {
        serverErrors.value = errors;
      }
    });
  }
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingUser.value = null;
  serverErrors.value = {};
};

const handleDelete = (id) => {
  deletingUserId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingUserId.value !== null) {
    const form = useForm({});
    form.delete(route('admin.users.destroy', deletingUserId.value), {
      preserveState: true,
      onSuccess: () => {
        toastSuccess('User deleted successfully');
        showDeleteConfirm.value = false;
        deletingUserId.value = null;
      },
      onError: (errors) => {
        const msg = Object.values(errors || {}).flat().join(', ') || 'Failed to delete user';
        toastError(msg);
        showDeleteConfirm.value = false;
        deletingUserId.value = null;
      }
    });
  } else {
    showDeleteConfirm.value = false;
  }
};

const handleFilterChange = ({ key, value }) => {
  if (key === 'role') {
    roleFilter.value = value;
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
          <Users class="text-construct-red" size="32" />
          <div>
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_users') }}</h2>
            <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_users_subtitle') }}</p>
          </div>
        </div>
        <button
          @click="handleAdd"
          class="flex items-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-wider rounded-xl shadow-sm hover:scale-105 active:scale-95 transition-all"
        >
          <Plus size="18" />
          {{ t('admin_add_user') }}
        </button>
      </div>
    </div>

    <!-- Search and Filter -->
    <div class="flex items-center gap-4 mt-6">
      <div class="flex-1">
        <div class="-mb-10">
          <SearchFilterModal
            v-model:search-query="searchQuery"
            :search-placeholder="t('admin_search_users')"
            :filters="[
              {
                key: 'role',
                options: [
                  { value: 'all', label: t('admin_all_roles') },
                  { value: '1', label: 'ADMIN' },
                  { value: '2', label: 'EDITOR' },
                  { value: '3', label: 'AUTHOR' },
                  { value: '4', label: 'MODERATOR' },
                  { value: '5', label: 'SUBSCRIBER' }
                ]
              }
            ]"
            :filter-values="{ role: roleFilter }"
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

    <!-- Users Cards -->
    <div v-if="viewMode === 'card'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-6">
      <div
        v-for="user in paginatedUsers"
        :key="user.id"
        :class="[
          'p-6 rounded-xl transition-all duration-200 hover:scale-[1.02]',
          isDarkMode ? 'bg-gray-800/80 hover:bg-gray-800' : 'bg-white hover:bg-gray-50'
        ]"
      >
        <!-- User Header -->
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div :class="['w-12 h-12 rounded-xl flex items-center justify-center overflow-hidden shadow-md', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
              <img v-if="user.avatar" :src="user.avatar" alt="" class="w-full h-full object-cover" />
              <User v-else :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" size="20" />
            </div>
            <div>
              <h4 :class="['font-bold text-lg', isDarkMode ? 'text-white' : 'text-gray-900']">{{ user.name }}</h4>
              <p :class="['text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ formatToShort(user.created_at) }}</p>
            </div>
          </div>
          <!-- Status Toggle -->
          <button
            @click="toggleStatus(user)"
            class="relative"
          >
            <div :class="['w-12 h-6 rounded-full relative transition-colors flex-shrink-0', user.status === 'active' ? 'bg-green-500' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
              <div :class="['absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform', user.status === 'active' ? 'translate-x-[1.625rem]' : 'translate-x-0.5']"></div>
            </div>
          </button>
        </div>

        <!-- Email -->
        <div :class="['flex items-center gap-2 mb-4 text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
          <Mail size="14" class="flex-shrink-0" />
          <span class="truncate" :title="user.email">{{ user.email }}</span>
        </div>

        <!-- Role Badge -->
        <div class="mb-4">
          <span v-if="user.roles && user.roles.length > 0" :class="['role-tag inline-block px-3 py-1 rounded-full text-xs font-bold', getRoleStyle(user.roles[0])]">{{ getRoleLabel(user.roles[0]) }}</span>
          <span v-else :class="['role-tag inline-block px-3 py-1 rounded-full text-xs font-bold bg-gray-500 text-white']">{{ t('admin_no_role') || '无角色' }}</span>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2">
          <Link :href="`/admin/users/${user.slug}`" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-700']">
            <Eye size="16" />
          </Link>
          <button @click="handleEdit(user)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-700']">
            <Edit3 size="16" />
          </button>
          <button @click="handleDelete(user.id)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-red-500/20 text-gray-400 hover:text-red-400' : 'hover:bg-red-50 text-gray-500 hover:text-red-500']">
            <Trash2 size="16" />
          </button>
          <span :class="['ml-auto text-xs font-bold', isDarkMode ? 'text-green-400' : 'text-green-600']" v-if="user.status === 'active'">
            {{ t('admin_active') }}
          </span>
          <span :class="['ml-auto text-xs font-bold', isDarkMode ? 'text-gray-500' : 'text-gray-400']" v-else>
            {{ t('admin_inactive') }}
          </span>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div v-if="viewMode === 'table'" :class="['mt-6 rounded-xl overflow-hidden', isDarkMode ? 'bg-gray-800/80' : 'bg-white']">
      <table class="w-full">
        <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-100/50'">
          <tr>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_user') }}</th>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_email') }}</th>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_role') }}</th>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_status') }}</th>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_joined') }}</th>
            <th :class="['px-6 py-4 text-center text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_table_actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in paginatedUsers" :key="user.id" :class="['transition-colors', isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50']">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="['w-10 h-10 rounded-xl flex items-center justify-center overflow-hidden', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
                  <img v-if="user.avatar" :src="user.avatar" alt="" class="w-full h-full object-cover" />
                  <User v-else :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" size="18" />
                </div>
                <span :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ user.name }}</span>
              </div>
            </td>
            <td class="px-6 py-4 max-w-[200px]">
              <div :class="['flex items-center gap-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                <Mail size="14" class="flex-shrink-0" />
                <span class="truncate" :title="user.email">{{ user.email }}</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <span v-if="user.roles && user.roles.length > 0" :class="['role-tag px-3 py-1 rounded-full text-xs font-bold', getRoleStyle(user.roles[0])]">{{ getRoleLabel(user.roles[0]) }}</span>
              <span v-else :class="['role-tag px-3 py-1 rounded-full text-xs font-bold bg-gray-500 text-white']">{{ t('admin_no_role') || '无角色' }}</span>
            </td>
            <td class="px-6 py-4">
              <button @click="toggleStatus(user)" class="flex items-center gap-2 cursor-pointer">
                <div :class="['w-12 h-6 rounded-full relative transition-colors', user.status === 'active' ? 'bg-green-500' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                  <div :class="['absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform', user.status === 'active' ? 'translate-x-[1.625rem]' : 'translate-x-0.5']"></div>
                </div>
                <span :class="['text-sm font-bold', user.status === 'active' ? (isDarkMode ? 'text-green-400' : 'text-green-600') : (isDarkMode ? 'text-gray-500' : 'text-gray-400')]">
                  {{ user.status === 'active' ? t('admin_active') : t('admin_inactive') }}
                </span>
              </button>
            </td>
            <td :class="['px-6 py-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ formatToShort(user.created_at) }}</td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-1">
                <Link :href="`/admin/users/${user.slug}`" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-700']">
                  <Eye size="16" />
                </Link>
                <button @click="handleEdit(user)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-700']">
                  <Edit3 size="16" />
                </button>
                <button @click="handleDelete(user.id)" :class="['p-2 rounded-lg transition-all', isDarkMode ? 'hover:bg-red-500/20 text-gray-400 hover:text-red-400' : 'hover:bg-red-50 text-gray-500 hover:text-red-500']">
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
      :total-items="props.total"
      :items-per-page="itemsPerPage"
      @update:current-page="(page) => currentPage = page"
      @update:items-per-page="(size) => { itemsPerPage = size; currentPage = 1; }"
    />

    <!-- User Detail View -->
    <UserForm
      :edit-data="editingUser"
      :visible="isFormVisible"
      :roles="props.roles"
      :server-errors="serverErrors"
      @save="handleSave"
      @cancel="handleCancel"
    />

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