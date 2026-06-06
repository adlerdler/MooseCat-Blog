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
  Edit3,
  Trash2,
  ShieldCheck,
  Inbox,
  useToast,
  RoleForm,
  ConfirmDialog,
  Pagination,
  EmptyState,
  SearchFilterModal
} from '../../composables/useAdminImports';
import { useRolePermissions } from '../../composables/useRolePermissions';

const props = defineProps({
  roles: { type: Array, default: () => [] },
  permissions: { type: Array, default: () => [] },
  rolePermissions: { type: Array, default: () => [] },
});

const { permissions: permData, GUARD_LABELS } = useRolePermissions({
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
        toastSuccess(t('toast.update_success'));
        isFormVisible.value = false;
        editingRole.value = null;
      },
      onError: (errors) => {
        console.error('Role update error:', errors);
        toastError(t('toast.update_error'));
      }
    });
  } else {
    const form = useForm(formData);
    form.post(route('admin.roles.store'), {
      preserveState: true,
      onSuccess: () => {
        toastSuccess(t('toast.create_success'));
        isFormVisible.value = false;
        editingRole.value = null;
      },
      onError: (errors) => {
        console.error('Role create error:', errors);
        toastError(t('toast.create_error'));
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
        toastSuccess(t('toast.delete_success'));
        showDeleteConfirm.value = false;
        deletingRoleId.value = null;
      },
      onError: (errors) => {
        console.error('Role delete error:', errors);
        toastError(t('toast.delete_error'));
        showDeleteConfirm.value = false;
        deletingRoleId.value = null;
      }
    });
  } else {
    showDeleteConfirm.value = false;
  }
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

    <!-- Content Area -->
    <template v-if="filteredRoles.length > 0">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="role in paginatedRoles"
          :key="role.id"
          :class="[
            'rounded-2xl shadow-md ring-1 transition-all duration-300 hover:shadow-xl hover:ring-construct-red overflow-hidden',
            isDarkMode ? 'bg-gray-800/80 ring-gray-700' : 'bg-white ring-gray-200/80'
          ]"
        >
          <!-- Card Header: Gradient Icon + Title -->
          <div class="flex items-start justify-between p-5 pb-3">
            <div class="flex items-center gap-3.5">
              <div
                :class="[
                  'w-11 h-11 rounded-xl shadow-md flex items-center justify-center flex-shrink-0',
                  role.color === 'red' ? 'bg-gradient-to-br from-red-500 to-rose-600' :
                  role.color === 'blue' ? 'bg-gradient-to-br from-blue-500 to-indigo-600' :
                  role.color === 'green' ? 'bg-gradient-to-br from-emerald-500 to-green-600' :
                  role.color === 'purple' ? 'bg-gradient-to-br from-violet-500 to-purple-600' :
                  role.color === 'yellow' ? 'bg-gradient-to-br from-amber-400 to-yellow-500' :
                  role.color === 'cyan' ? 'bg-gradient-to-br from-cyan-500 to-teal-600' :
                  role.color === 'orange' ? 'bg-gradient-to-br from-orange-500 to-red-500' :
                  role.color === 'pink' ? 'bg-gradient-to-br from-pink-500 to-rose-500' :
                  'bg-gradient-to-br from-gray-500 to-gray-700'
                ]"
              >
                <ShieldCheck size="20" class="text-white" />
              </div>
              <div class="min-w-0">
                <h3 :class="['font-display text-lg tracking-tighter truncate', isDarkMode ? 'text-white' : 'text-gray-900']">
                  {{ role.name }}
                </h3>
                <p :class="['text-[11px] font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                  {{ role.label || '—' }}
                </p>
              </div>
            </div>

            <div class="flex items-center gap-1 flex-shrink-0">
              <button
                @click="handleEdit(role)"
                :class="[
                  'p-2 rounded-xl transition-colors',
                  isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-white' : 'text-gray-400 hover:bg-gray-100 hover:text-gray-700'
                ]"
                title="Edit"
              >
                <Edit3 size="15" />
              </button>
              <button
                @click="handleDelete(role.id)"
                :class="[
                  'p-2 rounded-xl transition-colors',
                  isDarkMode ? 'text-gray-400 hover:bg-red-500/20 hover:text-red-400' : 'text-gray-400 hover:bg-red-50 hover:text-red-600'
                ]"
                title="Delete"
              >
                <Trash2 size="15" />
              </button>
            </div>
          </div>

          <!-- Badges: Color + Guard -->
          <div class="flex items-center gap-2 px-5 mb-2 flex-wrap">
            <span
              :class="[
                'inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold tracking-wider uppercase rounded-lg',
                isDarkMode ? 'bg-gray-700/80 text-gray-300' : 'bg-gray-100 text-gray-600'
              ]"
            >
              <div
                :class="[
                  'w-2.5 h-2.5 rounded-full',
                  role.color === 'red' ? 'bg-red-500' :
                  role.color === 'blue' ? 'bg-blue-500' :
                  role.color === 'green' ? 'bg-green-500' :
                  role.color === 'purple' ? 'bg-purple-500' :
                  role.color === 'yellow' ? 'bg-yellow-500' :
                  role.color === 'cyan' ? 'bg-cyan-500' :
                  role.color === 'orange' ? 'bg-orange-500' :
                  role.color === 'pink' ? 'bg-pink-500' :
                  'bg-gray-500'
                ]"
              />
              {{ role.color }}
            </span>
            <span
              :class="[
                'px-2.5 py-1 text-[10px] font-bold tracking-wider uppercase rounded-lg border',
                role.guard_name === 'admin'
                  ? (isDarkMode ? 'border-purple-500/40 text-purple-400 bg-purple-500/10' : 'border-purple-300 text-purple-700 bg-purple-50')
                  : (isDarkMode ? 'border-blue-500/40 text-blue-400 bg-blue-500/10' : 'border-blue-300 text-blue-700 bg-blue-50')
              ]"
            >
              {{ GUARD_LABELS[role.guard_name] || role.guard_name }}
            </span>
          </div>

          <!-- Description -->
          <p :class="['text-sm px-5 mb-3 leading-relaxed', isDarkMode ? 'text-gray-400' : 'text-gray-600']">
            {{ role.description || '—' }}
          </p>

          <!-- Permissions Section -->
          <div class="px-5 pt-1 pb-4">
            <p :class="['text-[10px] font-bold tracking-[0.15em] uppercase mb-2.5', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              {{ t('admin_permissions') }}
              <span :class="['ml-1.5', isDarkMode ? 'text-gray-600' : 'text-gray-300']">
                ({{ getRolePermissions(role).length }})
              </span>
            </p>
            <div class="flex flex-wrap gap-1.5">
              <template v-if="getRolePermissions(role).length > 0">
                <span
                  v-for="(permission, index) in getRolePermissions(role).slice(0, 4)"
                  :key="index"
                  :class="['px-2 py-0.5 text-[10px] font-medium rounded-md', isDarkMode ? 'bg-gray-700/70 text-gray-300' : 'bg-gray-100 text-gray-600']"
                >
                  {{ permission }}
                </span>
                <span
                  v-if="getRolePermissions(role).length > 4"
                  :class="['px-2 py-0.5 text-[10px] font-medium rounded-md', isDarkMode ? 'bg-gray-700/40 text-gray-500' : 'bg-gray-50 text-gray-400']"
                >
                  +{{ getRolePermissions(role).length - 4 }} more
                </span>
              </template>
              <span v-else :class="['text-[10px] italic', isDarkMode ? 'text-gray-600' : 'text-gray-400']">
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
    </template>

    <!-- Empty State -->
    <div v-else class="mt-6">
      <EmptyState 
        :title="t('admin_no_roles_found') || 'No roles found'"
        :description="t('admin_no_roles_description') || 'Try adjusting your search or filters to find what you are looking for'"
        :icon="Shield"
      />
    </div>

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