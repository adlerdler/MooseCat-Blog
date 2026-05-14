<script setup>
/**
 * AdminUsers.vue - 用户管理页面
 * 
 * 功能说明：
 * - 管理系统用户账户
 * - 用户列表展示（用户名、邮箱、角色、状态）
 * - 用户搜索和筛选功能
 * - 添加、编辑、删除用户
 * - 用户状态管理（启用/禁用）
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Users,
  Plus,
  Search,
  Edit3,
  Trash2,
  Eye,
  ChevronLeft,
  ChevronRight,
  Filter,
  User,
  Mail,
  Shield,
  X
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { adminUsers } from '../../data/users';
import { getRoleLabel, getRoleStyle } from '../../data/roles';
import { formatToShort } from '../../utils/dateUtils';
import UserForm from '../../components/admin/UserForm.vue';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const roleFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = 6;
const isFormVisible = ref(false);
const editingUser = ref(null);
const showDeleteConfirm = ref(false);
const deletingUserId = ref(null);
const isDetailVisible = ref(false);
const viewingUser = ref(null);

const users = ref([...adminUsers]);

const filteredUsers = computed(() => {
  return users.value.filter(user => {
    const matchesSearch = user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         user.email.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesRole = roleFilter.value === 'all' || user.role === roleFilter.value;
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
      users.value[index] = { ...users.value[index], ...data };
    }
  } else {
    const newId = Math.max(...users.value.map(u => u.id), 0) + 1;
    const today = new Date().toISOString().split('T')[0];
    users.value.push({
      id: newId,
      ...data,
      joined: today
    });
  }
  isFormVisible.value = false;
  editingUser.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingUser.value = null;
};

const handleView = (user) => {
  viewingUser.value = { ...user };
  isDetailVisible.value = true;
};

const closeDetail = () => {
  isDetailVisible.value = false;
  viewingUser.value = null;
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
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center gap-4 mb-2">
        <Users class="text-construct-red" size="32" />
        <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_users') }}</h2>
      </div>
      <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_users_subtitle') }}</p>
    </div>

    <!-- Search and Filter -->
    <div class="flex flex-col md:flex-row gap-4 mb-8">
      <div class="flex-1 relative">
        <Search :class="['absolute left-4 top-1/2 -translate-y-1/2', isDarkMode ? 'text-gray-400' : 'text-gray-500']" size="20" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('admin_search_users')"
          :class="[
            'w-full pl-12 pr-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
        />
      </div>
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
          <Filter :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="18" />
          <select
            v-model="roleFilter"
            :class="[
              'px-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
              isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option value="all">{{ t('admin_all_roles') }}</option>
            <option value="admin">ADMIN</option>
            <option value="editor">EDITOR</option>
            <option value="author">AUTHOR</option>
            <option value="moderator">MODERATOR</option>
            <option value="subscriber">SUBSCRIBER</option>
          </select>
        </div>
        <button @click="handleAdd" class="flex items-center gap-2 px-6 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider transition-colors rounded">
          <Plus size="16" class="!text-white" /> {{ t('admin_add_user') }}
        </button>
      </div>
    </div>

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
              <span :class="['role-tag px-3 py-1 rounded-full text-xs font-bold border', getRoleStyle(user.role)]">{{ getRoleLabel(user.role) }}</span>
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
                <button @click="handleView(user)" :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-600' : 'text-gray-500 hover:bg-gray-100']">
                  <Eye size="16" />
                </button>
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
    <div class="flex items-center justify-between mt-6">
      <div class="text-sm text-gray-400">
        {{ t('admin_showing') }} {{ ((currentPage - 1) * itemsPerPage) + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredUsers.length) }} {{ t('admin_of') }} {{ filteredUsers.length }} {{ t('admin_users') }}
      </div>
      <div class="flex items-center gap-2">
        <button
          @click="currentPage = Math.max(1, currentPage - 1)"
          :disabled="currentPage === 1"
          class="p-2 border border-gray-700 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <ChevronLeft size="18" />
        </button>
        <span class="px-4 py-2 border border-gray-700">{{ currentPage }} / {{ totalPages }}</span>
        <button
          @click="currentPage = Math.min(totalPages, currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="p-2 border border-gray-700 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <ChevronRight size="18" />
        </button>
      </div>
    </div>

    <!-- User Form Modal -->
    <UserForm
      :edit-data="editingUser"
      :visible="isFormVisible"
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

    <!-- User Detail Modal -->
    <div v-if="isDetailVisible && viewingUser" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/50" @click="closeDetail"></div>
      <div :class="['relative w-full max-w-lg p-8 rounded-lg shadow-xl', isDarkMode ? 'bg-gray-800' : 'bg-white']">
        <button 
          @click="closeDetail" 
          :class="['absolute top-4 right-4 p-2 rounded-full transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']"
        >
          <X :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="20" />
        </button>
        
        <div class="text-center mb-8">
          <div :class="['w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-4', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
            <User :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" size="36" />
          </div>
          <h3 :class="['text-2xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ viewingUser.name }}</h3>
          <p :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ viewingUser.email }}</p>
        </div>

        <div :class="['space-y-4', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          <div class="flex items-center justify-between p-4 rounded-lg" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
            <span class="text-sm font-bold tracking-wider uppercase">{{ t('admin_table_role') }}</span>
            <span :class="['px-3 py-1 rounded-full text-xs font-bold border', getRoleStyle(viewingUser.role)]">{{ getRoleLabel(viewingUser.role) }}</span>
          </div>
          
          <div class="flex items-center justify-between p-4 rounded-lg" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
            <span class="text-sm font-bold tracking-wider uppercase">{{ t('admin_table_status') }}</span>
            <span :class="['px-3 py-1 rounded-full text-xs font-bold', viewingUser.status === 'active' ? (isDarkMode ? 'bg-green-500/20 text-green-400' : 'bg-green-100 text-green-600') : (isDarkMode ? 'bg-gray-600/50 text-gray-400' : 'bg-gray-100 text-gray-500')]">
              {{ viewingUser.status === 'active' ? t('admin_active') : t('admin_inactive') }}
            </span>
          </div>
          
          <div class="flex items-center justify-between p-4 rounded-lg" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
            <span class="text-sm font-bold tracking-wider uppercase">{{ t('admin_table_joined') }}</span>
            <span>{{ formatToShort(viewingUser.joined) }}</span>
          </div>
        </div>

        <div class="mt-8 flex gap-4">
          <button 
            @click="closeDetail" 
            :class="['flex-1 py-3 font-bold tracking-wider rounded-lg transition-colors', isDarkMode ? 'bg-gray-700 hover:bg-gray-600 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-900']"
          >
            {{ t('admin_close') }}
          </button>
          <button 
            @click="handleEdit(viewingUser); closeDetail()" 
            class="flex-1 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider rounded-lg transition-colors"
          >
            {{ t('admin_edit') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
