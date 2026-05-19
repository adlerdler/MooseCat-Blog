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
import {
  ref,
  computed,
  useI18n,
  useTheme,
  Shield,
  Plus,
  Search,
  Edit3,
  Trash2,
  Users,
  Check,
  X,
  RoleForm,
  ConfirmDialog,
  AdminPagination,
  AdminSearchFilter
} from '../../composables/useAdminImports';
import { useRolePermissions } from '../../composables/useRolePermissions';

const { adminRoles, permissions, getPermissionIdsByRoleId } = useRolePermissions();

/**
 * 获取角色的权限列表
 * @param {number} roleId - 角色ID
 * @returns {string[]} 权限名称数组
 */
const getRolePermissions = (roleId) => {
  const permissionIds = getPermissionIdsByRoleId(roleId);
  return permissionIds.map(id => {
    const permission = permissions.find(p => p.id === id);
    return permission ? permission.label : 'Unknown';
  });
};

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 6;
const isFormVisible = ref(false);
const editingRole = ref(null);
const showDeleteConfirm = ref(false);
const deletingRoleId = ref(null);

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

const handleEdit = (role) => {
  editingRole.value = { ...role };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingRole.value = null;
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (editingRole.value) {
    const index = roles.value.findIndex(r => r.id === editingRole.value.id);
    if (index !== -1) {
      roles.value[index] = { ...roles.value[index], ...data };
    }
  } else {
    const newId = Math.max(...roles.value.map(r => r.id), 0) + 1;
    roles.value.push({
      id: newId,
      ...data,
      userCount: 0
    });
  }
  isFormVisible.value = false;
  editingRole.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingRole.value = null;
};

const handleDelete = (id) => {
  deletingRoleId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingRoleId.value !== null) {
    roles.value = roles.value.filter(r => r.id !== deletingRoleId.value);
    deletingRoleId.value = null;
  }
  showDeleteConfirm.value = false;
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Shield class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_roles') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Manage system roles and permissions</p>
        </div>
        <button @click="handleAdd" class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl">
          <Plus size="18" /> {{ t('admin_add_role') }}
        </button>
      </div>
    </div>

    <!-- Search -->
    <AdminSearchFilter
      v-model:search-query="searchQuery"
      :search-placeholder="t('admin_search_roles')"
      :filters="[]"
      :filter-values="{}"
    />

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
            <button @click="handleEdit(role)" :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-500 hover:bg-gray-100']">
              <Edit3 size="16" />
            </button>
            <button @click="handleDelete(role.id)" :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-red-500/20' : 'text-gray-500 hover:bg-red-50']">
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
              v-for="(permission, index) in getRolePermissions(role.id)" 
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
    <AdminPagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredRoles.length"
      :items-per-page="itemsPerPage"
      @update:current-page="(page) => currentPage = page"
    />

    <!-- Role Form Modal -->
    <RoleForm
      :edit-data="editingRole"
      :visible="isFormVisible"
      @save="handleSave"
      @cancel="handleCancel"
    />

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      title="确认删除"
      content="确定要删除这个角色吗？此操作不可撤销。"
      confirm-text="删除"
      confirm-variant="danger"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>
