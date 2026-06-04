<script setup>
/**
 * Role Management Page
 *
 * Features:
 * - Role list display with pagination
 * - Search functionality
 * - Role CRUD operations (create, edit, delete)
 * - Permission management per role
 * - Role color configuration
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useForm, router } from '@inertiajs/vue3';
import { useTheme } from '../../composables/useTheme';
import {
  Shield,
  Plus,
  Search,
  Edit3,
  Trash2,
  Check,
  X,
  ShieldCheck,
  Palette,
  useToast,
  RoleForm,
  ConfirmDialog,
  Pagination,
  SearchFilterModal
} from '../../composables/useAdminImports';
import { useRolePermissions } from '../../composables/useRolePermissions';

const props = defineProps({
  roles: { type: Array, default: () => [] },
  permissions: { type: Array, default: () => [] },
  rolePermissions: { type: Array, default: () => [] },
});

const { permissions: permData, getPermissionIdsByRoleId, getRoleGuardName, GUARD_LABELS, getRoleColorConfig } = useRolePermissions({
  roles: props.roles,
  permissions: props.permissions,
  rolePermissions: props.rolePermissions
});

const roles = computed(() => props.roles || []);
const permissions = computed(() => props.permissions || permData);

const getRolePermissions = (role) => {
  const permissionIds = role.permissions || [];
  return permissionIds.map(id => {
    const permission = permissions.value.find(p => p.id === id);
    return permission ? permission.label : 'Unknown';
  });
};

const getRolePermissionsByGuard = (roleId) => {
  const permissionIds = getPermissionIdsByRoleId(roleId);
  const grouped = { web: [], api: [], admin: [] };

  permissionIds.forEach(id => {
    const permission = permissions.find(p => p.id === id);
    if (permission && grouped[permission.guard_name]) {
      grouped[permission.guard_name].push(permission.label);
    }
  });

  return grouped;
};

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success: toastSuccess, error: toastError } = useToast();

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 6;
const isFormVisible = ref(false);
const editingRole = ref(null);
const showDeleteConfirm = ref(false);
const deletingRoleId = ref(null);

const filteredRoles = computed(() => {
  if (!searchQuery.value) return roles.value;
  return roles.value.filter(role => {
    return role.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
           role.description?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
           role.label?.toLowerCase().includes(searchQuery.value.toLowerCase());
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
  const formData = { ...data };
  
  if (editingRole.value) {
    const form = useForm(formData);
    form.put(route('admin.roles.update', editingRole.value.id), {
      preserveState: true,
      onSuccess: () => {
        toastSuccess('Role updated successfully');
        isFormVisible.value = false;
        editingRole.value = null;
      },
      onError: (errors) => {
        const msg = Object.values(errors || {}).flat().join(', ') || 'Failed to update role';
        toastError(msg);
      }
    });
  } else {
    const form = useForm(formData);
    form.post(route('admin.roles.store'), {
      preserveState: true,
      onSuccess: () => {
        toastSuccess('Role created successfully');
        isFormVisible.value = false;
        editingRole.value = null;
      },
      onError: (errors) => {
        const msg = Object.values(errors || {}).flat().join(', ') || 'Failed to create role';
        toastError(msg);
      }
    });
  }
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
    const form = useForm({});
    form.delete(route('admin.roles.destroy', deletingRoleId.value), {
      preserveState: true,
      onSuccess: () => {
        toastSuccess('Role deleted successfully');
        showDeleteConfirm.value = false;
        deletingRoleId.value = null;
      },
      onError: (errors) => {
        const msg = Object.values(errors || {}).flat().join(', ') || 'Failed to delete role';
        toastError(msg);
        showDeleteConfirm.value = false;
        deletingRoleId.value = null;
      }
    });
  } else {
    showDeleteConfirm.value = false;
  }
};

const getColorStyle = (role) => {
  const config = getRoleColorConfig(role.name);
  return {
    backgroundColor: config.bg.replace('bg-', 'var(--color-'),
    borderColor: config.border.replace('border-', 'var(--color-')
  };
};
</script>

<template>
  <div class="p-8">
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

    <SearchFilterModal
      v-model:search-query="searchQuery"
      :search-placeholder="t('admin_search_roles')"
      :filters="[]"
      :filter-values="{}"
    />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="role in paginatedRoles"
        :key="role.id"
        :class="[
          'border p-6 hover:border-construct-red transition-all hover:shadow-lg',
          isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div
              :class="[
                'w-12 h-12 rounded-lg flex items-center justify-center',
                role.color === 'yellow' ? 'bg-yellow-500 text-gray-900' : `bg-${role.color}-600 text-white`
              ]"
              :style="{ backgroundColor: role.color === 'yellow' ? '#eab308' : role.color === 'red' ? '#dc2626' : role.color === 'blue' ? '#2563eb' : role.color === 'green' ? '#16a34a' : role.color === 'purple' ? '#9333ea' : role.color === 'gray' ? '#4b5563' : role.color === 'cyan' ? '#0891b2' : '#6b7280' }"
            >
              <ShieldCheck class="size-6" />
            </div>
            <div>
              <h3 :class="['font-display text-xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ role.name }}</h3>
              <span :class="['text-xs font-bold uppercase tracking-wider', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                {{ role.label }}
              </span>
            </div>
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

        <div class="flex items-center gap-3 mb-3">
          <span
            :class="[
              'px-2 py-1 text-xs font-bold rounded',
              isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'
            ]"
          >
            <Palette class="inline w-3 h-3 mr-1" />
            {{ role.color }}
          </span>
          <span
            :class="[
              'px-2 py-1 text-xs font-bold rounded',
              role.guard_name === 'api' ? (isDarkMode ? 'bg-yellow-500/20 text-yellow-400' : 'bg-yellow-100 text-yellow-700') :
              role.guard_name === 'admin' ? (isDarkMode ? 'bg-purple-500/20 text-purple-400' : 'bg-purple-100 text-purple-700') :
              (isDarkMode ? 'bg-blue-500/20 text-blue-400' : 'bg-blue-100 text-blue-700')
            ]"
          >
            Guard: {{ GUARD_LABELS[role.guard_name] || role.guard_name }}
          </span>
        </div>

        <p :class="['text-sm mb-4', isDarkMode ? 'text-gray-400' : 'text-gray-600']">{{ role.description }}</p>

        <div :class="['pt-4 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
          <div :class="['text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_permissions') }}</div>
          <div class="flex flex-wrap gap-1">
            <span
              v-for="(permission, index) in getRolePermissions(role)"
              :key="index"
              :class="['px-2 py-0.5 text-xs rounded', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']"
            >
              {{ permission }}
            </span>
            <span v-if="getRolePermissions(role).length === 0" :class="['text-xs italic', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              No permissions
            </span>
          </div>
        </div>
      </div>
    </div>

    <Pagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredRoles.length"
      :items-per-page="itemsPerPage"
      @update:current-page="(page) => currentPage = page"
    />

    <RoleForm
      :edit-data="editingRole"
      :permissions="permissions"
      :visible="isFormVisible"
      @save="handleSave"
      @cancel="handleCancel"
    />

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