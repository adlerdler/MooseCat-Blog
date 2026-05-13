<script setup>
/**
 * AdminRoles.vue - 角色管理页面
 * 
 * 功能说明：
 * - 管理系统角色和权限
 * - 角色列表展示（名称、描述、用户数、权限）
 * - 角色搜索
 * - 添加、编辑、删除角色
 * - 权限配置
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Shield,
  Plus,
  Search,
  Edit3,
  Trash2,
  ChevronLeft,
  ChevronRight,
  Users,
  Check,
  X
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { adminRoles } from '../../data/roles';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 6;

const roles = ref([...adminRoles]);

const filteredRoles = computed(() => {
  return roles.value.filter(role => {
    return role.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
           role.description.toLowerCase().includes(searchQuery.value.toLowerCase());
  });
});

const totalPages = computed(() => Math.ceil(filteredRoles.value.length / itemsPerPage));

const paginatedRoles = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredRoles.value.slice(start, start + itemsPerPage);
});
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center gap-4 mb-2">
        <Shield class="text-construct-red" size="32" />
        <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_roles') }}</h2>
      </div>
      <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Manage system roles and permissions</p>
    </div>

    <!-- Search -->
    <div class="flex flex-col md:flex-row gap-4 mb-8">
      <div class="flex-1 relative">
        <Search :class="['absolute left-4 top-1/2 -translate-y-1/2', isDarkMode ? 'text-gray-400' : 'text-gray-500']" size="20" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('admin_search_roles')"
          :class="[
            'w-full pl-12 pr-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
        />
      </div>
      <button class="flex items-center gap-2 px-6 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider transition-colors">
        <Plus size="18" /> {{ t('admin_add_role') }}
      </button>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="role in paginatedRoles" 
        :key="role.id"
        :class="[
          'border p-6 hover:border-construct-red transition-colors',
          isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <div class="flex items-start justify-between mb-4">
          <div :class="['w-12 h-12 rounded-lg flex items-center justify-center', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
            <Shield :class="['size-24', isDarkMode ? 'text-gray-400' : 'text-gray-600']" />
          </div>
          <div class="flex items-center gap-2">
            <button :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-500 hover:bg-gray-100']">
              <Edit3 size="16" />
            </button>
            <button :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-red-500/20' : 'text-gray-500 hover:bg-red-50']">
              <Trash2 size="16" />
            </button>
          </div>
        </div>
        
        <h3 :class="['font-display text-xl tracking-tighter mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">{{ role.name }}</h3>
        <p :class="['text-sm mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-600']">{{ role.description }}</p>
        
        <div :class="['flex items-center gap-2 mb-4 text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
          <Users size="14" />
          <span>{{ role.userCount }} {{ t('admin_users') }}</span>
        </div>
        
        <div :class="['pt-4 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
          <div :class="['text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_permissions') }}</div>
          <div class="flex flex-wrap gap-2">
            <span 
              v-for="(permission, index) in role.permissions" 
              :key="index"
              :class="['px-2 py-1 text-xs rounded', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']"
            >
              {{ permission }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between mt-8">
      <div :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
        {{ t('admin_showing') }} {{ ((currentPage - 1) * itemsPerPage) + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredRoles.length) }} {{ t('admin_of') }} {{ filteredRoles.length }} {{ t('admin_roles') }}
      </div>
      <div class="flex items-center gap-2">
        <button
          @click="currentPage = Math.max(1, currentPage - 1)"
          :disabled="currentPage === 1"
          :class="[
            'p-2 border disabled:opacity-50 disabled:cursor-not-allowed transition-colors',
            isDarkMode ? 'border-gray-700 hover:bg-gray-700' : 'border-gray-300 hover:bg-gray-100'
          ]"
        >
          <ChevronLeft :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="18" />
        </button>
        <span :class="['px-4 py-2 border', isDarkMode ? 'border-gray-700 text-gray-400' : 'border-gray-300 text-gray-500']">{{ currentPage }} / {{ totalPages }}</span>
        <button
          @click="currentPage = Math.min(totalPages, currentPage + 1)"
          :disabled="currentPage === totalPages"
          :class="[
            'p-2 border disabled:opacity-50 disabled:cursor-not-allowed transition-colors',
            isDarkMode ? 'border-gray-700 hover:bg-gray-700' : 'border-gray-300 hover:bg-gray-100'
          ]"
        >
          <ChevronRight :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="18" />
        </button>
      </div>
    </div>
  </div>
</template>
