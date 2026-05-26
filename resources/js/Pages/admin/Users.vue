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
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';
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
});

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { getRoleLabel, getRoleStyle } = useRolePermissions({
  roles: props.roles
});

const searchQuery = ref('');
const roleFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = 6;
const isFormVisible = ref(false);
const editingUser = ref(null);
const showDeleteConfirm = ref(false);
const deletingUserId = ref(null);

const users = computed(() => props.users || []);

const filteredUsers = computed(() => {
  return users.value.filter(user => {
    const matchesSearch = user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         user.email.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesRole = roleFilter.value === 'all' || user.role_id === parseInt(roleFilter.value);
    return matchesSearch && matchesRole;
  });
});

const totalPages = computed(() => Math.ceil(filteredUsers.value.length / itemsPerPage));

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredUsers.value.slice(start, start + itemsPerPage);
});

const toggleStatus = (user) => {
  user.status = user.status === 'active' ? 'inactive' : 'active';
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
  if (editingUser.value) {
    const index = users.value.findIndex(u => u.id === editingUser.value.id);
    if (index !== -1) {
      users.value[index] = { 
        ...users.value[index], 
        ...data
      };
    }
  } else {
    const newId = Math.max(...users.value.map(u => u.id), 0) + 1;
    const today = new Date().toISOString().split('T')[0];
    users.value.push({
      id: newId,
      ...data,
      joined: today,
      avatar: null,
      bio: null,
      github: null,
      twitter: null,
      linkedin: null,
      last_login_at: null,
      created_at: today,
      updated_at: today
    });
  }
  isFormVisible.value = false;
  editingUser.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingUser.value = null;
};

const handleDelete = (id) => {
  deletingUserId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingUserId.value !== null) {
    users.value = users.value.filter(u => u.id !== deletingUserId.value);
    deletingUserId.value = null;
  }
  showDeleteConfirm.value = false;
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
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Users class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_users') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_users_subtitle') }}</p>
        </div>
        <button
          @click="handleAdd"
          class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
        >
          <Plus size="18" />
          {{ t('admin_add_user') }}
        </button>
      </div>
    </div>

    <!-- Search and Filter -->
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

    <!-- Users Table -->
    <div :class="['border overflow-hidden', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <table class="w-full">
        <thead :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-100'">
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
          <tr v-for="user in paginatedUsers" :key="user.id" :class="['border-t transition-colors', isDarkMode ? 'border-gray-700 hover:bg-gray-700/50' : 'border-gray-200 hover:bg-gray-50']">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="['w-10 h-10 rounded-full flex items-center justify-center', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
                  <User :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" size="18" />
                </div>
                <span :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ user.name }}</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <div :class="['flex items-center gap-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                <Mail size="14" />
                <span>{{ user.email }}</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <span :class="['role-tag px-3 py-1 rounded-full text-xs font-bold border', getRoleStyle(user.role_id)]">{{ getRoleLabel(user.role_id) }}</span>
            </td>
            <td class="px-6 py-4">
              <button
                @click="toggleStatus(user)"
                class="flex items-center gap-3 cursor-pointer"
              >
                <div :class="['w-12 h-6 rounded-full relative transition-colors', user.status === 'active' ? 'bg-green-500' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')]">
                  <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', user.status === 'active' ? 'left-7' : 'left-1']"></div>
                </div>
                <span :class="['text-sm font-bold tracking-wider', user.status === 'active' ? (isDarkMode ? 'text-green-400' : 'text-green-600') : (isDarkMode ? 'text-gray-500' : 'text-gray-400')]">
                  {{ user.status === 'active' ? t('admin_active') : t('admin_inactive') }}
                </span>
              </button>
            </td>
            <td :class="['px-6 py-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ formatToShort(user.joined) }}</td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2">
                <Link :href="`/admin/users/${user.id}`" :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-600' : 'text-gray-500 hover:bg-gray-100']">
                  <Eye size="16" />
                </Link>
                <button @click="handleEdit(user)" :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-600' : 'text-gray-500 hover:bg-gray-100']">
                  <Edit3 size="16" />
                </button>
                <button @click="handleDelete(user.id)" :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-red-500/20' : 'text-gray-500 hover:bg-red-50']">
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
      :total-items="filteredUsers.length"
      :items-per-page="itemsPerPage"
      @update:current-page="(page) => currentPage = page"
    />

    <!-- User Detail View -->
    <UserForm
      :edit-data="editingUser"
      :visible="isFormVisible"
      :roles="props.roles"
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