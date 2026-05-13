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
  ToggleLeft,
  ToggleRight
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const roleFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = 6;

const users = ref([
  { id: 1, name: 'Admin User', email: 'admin@archyx.com', role: 'admin', status: 'active', joined: '2024-01-15' },
  { id: 2, name: 'Content Editor', email: 'editor@archyx.com', role: 'editor', status: 'active', joined: '2024-03-20' },
  { id: 3, name: 'Author Writer', email: 'author@archyx.com', role: 'author', status: 'active', joined: '2024-05-10' },
  { id: 4, name: 'Guest User', email: 'guest@example.com', role: 'guest', status: 'inactive', joined: '2024-06-01' },
  { id: 5, name: 'Contributor', email: 'contributor@archyx.com', role: 'author', status: 'active', joined: '2024-07-15' },
  { id: 6, name: 'Moderator', email: 'moderator@archyx.com', role: 'moderator', status: 'active', joined: '2024-08-20' },
  { id: 7, name: 'Subscriber', email: 'subscriber@example.com', role: 'subscriber', status: 'inactive', joined: '2024-09-01' },
  { id: 8, name: 'API User', email: 'api@archyx.com', role: 'api', status: 'active', joined: '2024-10-15' },
]);

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

const getRoleLabel = (role) => {
  const roles = {
    admin: 'ADMIN',
    editor: 'EDITOR',
    author: 'AUTHOR',
    moderator: 'MODERATOR',
    subscriber: 'SUBSCRIBER',
    api: 'API'
  };
  return roles[role] || role.toUpperCase();
};

const getRoleColor = (role) => {
  const colors = {
    admin: 'bg-red-500',
    editor: 'bg-blue-500',
    author: 'bg-green-500',
    moderator: 'bg-purple-500',
    subscriber: 'bg-gray-500',
    api: 'bg-yellow-500'
  };
  return colors[role] || 'bg-gray-500';
};

const toggleStatus = (user) => {
  user.status = user.status === 'active' ? 'inactive' : 'active';
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
      <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Manage system users and permissions</p>
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
        <button class="flex items-center gap-2 px-6 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider transition-colors rounded">
          <Plus size="16" class="!text-white" /> {{ t('admin_add_user') }}
        </button>
      </div>
    </div>

    <!-- Users Table -->
    <div :class="['border overflow-hidden', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <table class="w-full">
        <thead :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-100'">
          <tr>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">USER</th>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">EMAIL</th>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">ROLE</th>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">STATUS</th>
            <th :class="['px-6 py-4 text-left text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">JOINED</th>
            <th :class="['px-6 py-4 text-center text-xs font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">ACTIONS</th>
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
              <span :class="['px-3 py-1 rounded-full text-xs font-bold', getRoleColor(user.role)]">{{ getRoleLabel(user.role) }}</span>
            </td>
            <td class="px-6 py-4">
              <button
                @click="toggleStatus(user)"
                class="flex items-center gap-2"
              >
                <ToggleRight v-if="user.status === 'active'" :class="isDarkMode ? 'text-green-400' : 'text-green-600'" size="20" />
                <ToggleLeft v-else :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="20" />
                <span :class="['text-sm font-bold', user.status === 'active' ? (isDarkMode ? 'text-green-400' : 'text-green-600') : (isDarkMode ? 'text-gray-500' : 'text-gray-400')]">
                  {{ user.status === 'active' ? t('admin_active') : t('admin_inactive') }}
                </span>
              </button>
            </td>
            <td :class="['px-6 py-4', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ user.joined }}</td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2">
                <button :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-600' : 'text-gray-500 hover:bg-gray-100']">
                  <Eye size="16" />
                </button>
                <button :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-600' : 'text-gray-500 hover:bg-gray-100']">
                  <Edit3 size="16" />
                </button>
                <button :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-red-500/20' : 'text-gray-500 hover:bg-red-50']">
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
  </div>
</template>
