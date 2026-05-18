<script setup>
/**
 * RoleForm.vue - 角色管理表单组件
 * 
 * 功能说明：
 * - 支持角色的新增/编辑
 * - 包含角色名称、描述、权限字段
 * - 权限支持多选
 * 
 * 使用示例：
 * <RoleForm :edit-data="editingRole" :visible="isFormVisible" @save="handleSave" @cancel="handleCancel" />
 */
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { X, Save, Check } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { useRolePermissions } from '../../composables/useRolePermissions';

const { availablePermissions, permissions, getPermissionIdsByRoleId } = useRolePermissions();

const { t } = useI18n();

const props = defineProps({
  editData: {
    type: Object,
    default: null
  },
  visible: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['save', 'cancel']);

const { isDarkMode } = useTheme();

const isEditMode = computed(() => props.editData !== null);

const formTitle = computed(() => {
  return isEditMode.value ? t('admin_edit') + ' ' + t('admin_role') : t('admin_add') + ' ' + t('admin_role');
});

const formData = ref({});

const initFormData = () => {
  formData.value = {
    name: '',
    description: '',
    permissions: []
  };
};

/**
 * 根据角色ID获取角色的权限名称列表
 * @param {number} roleId - 角色ID
 * @returns {string[]} 权限名称数组
 */
const getRolePermissionNames = (roleId) => {
  const permissionIds = getPermissionIdsByRoleId(roleId);
  return permissionIds.map(id => {
    const permission = permissions.find(p => p.id === id);
    return permission ? permission.label : '';
  }).filter(Boolean);
};

watch(() => props.visible, (newVal) => {
  if (newVal) {
    initFormData();
    if (props.editData) {
      formData.value = {
        ...props.editData,
        permissions: [...getRolePermissionNames(props.editData.id)]
      };
    }
  }
});

const togglePermission = (permission) => {
  const index = formData.value.permissions.indexOf(permission);
  if (index === -1) {
    formData.value.permissions.push(permission);
  } else {
    formData.value.permissions.splice(index, 1);
  }
};

const hasPermission = (permission) => {
  return formData.value.permissions.includes(permission);
};

const handleSubmit = () => {
  if (!formData.value.name.trim() || !formData.value.description.trim()) {
    return;
  }
  
  emit('save', { ...formData.value });
};

const handleCancel = () => {
  emit('cancel');
};
</script>

<template>
  <Transition name="modal">
    <div v-if="visible" class="fixed inset-0 z-[100] flex items-center justify-center">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="handleCancel" />

      <!-- Modal -->
      <div
        :class="[
          'relative w-full max-w-2xl mx-4 rounded-xl shadow-2xl overflow-hidden',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]"
      >
        <!-- Header -->
        <div
          :class="[
            'flex items-center justify-between p-6 border-b',
            isDarkMode ? 'border-gray-700' : 'border-gray-200'
          ]"
        >
          <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">
            {{ formTitle }}
          </h3>
          <button
            @click="handleCancel"
            :class="[
              'p-2 rounded-lg transition-colors',
              isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'
            ]"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
          <!-- Role Name -->
          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_role_form_name') }} *
            </label>
            <input
              v-model="formData.name"
              type="text"
              :class="[
                'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                isDarkMode 
                  ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                  : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
              ]"
              placeholder="输入角色名称..."
            />
          </div>

          <!-- Description -->
          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_role_form_description') }} *
            </label>
            <textarea
              v-model="formData.description"
              rows="3"
              :class="[
                'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red resize-none',
                isDarkMode 
                  ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                  : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
              ]"
              placeholder="输入角色描述..."
            ></textarea>
          </div>

          <!-- Permissions -->
          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_role_form_permissions') }}
            </label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <button
                v-for="permission in availablePermissions"
                :key="permission"
                type="button"
                @click="togglePermission(permission)"
                :class="[
                  'flex items-center gap-2 px-3 py-2 rounded-lg border text-sm font-bold transition-colors',
                  hasPermission(permission)
                    ? 'bg-construct-red border-construct-red text-white'
                    : isDarkMode 
                      ? 'bg-gray-700 border-gray-600 text-gray-300 hover:border-gray-500' 
                      : 'bg-white border-gray-300 text-gray-700 hover:border-gray-400'
                ]"
              >
                <Check v-if="hasPermission(permission)" class="w-4 h-4" />
                <div v-else class="w-4 h-4" />
                {{ t('admin_permission_' + permission.toLowerCase().replace(' ', '_')) }}
              </button>
            </div>
            <p :class="['text-xs mt-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              {{ t('admin_role_form_selected') }} {{ formData.permissions.length }}
            </p>
          </div>
        </div>

        <!-- Footer -->
        <div
          :class="[
            'flex gap-3 p-6 border-t',
            isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
          ]"
        >
          <button
            @click="handleCancel"
            :class="[
              'flex-1 px-6 py-3 font-bold tracking-widest uppercase text-sm transition-colors rounded border',
              isDarkMode 
                ? 'border-gray-600 text-gray-300 hover:bg-gray-700' 
                : 'border-gray-300 text-gray-700 hover:bg-gray-100'
            ]"
          >
            {{ t('admin_cancel') }}
          </button>
          <button
            @click="handleSubmit"
            class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-700 transition-colors rounded"
          >
            <Save class="w-4 h-4" />
            {{ isEditMode ? t('admin_save') : t('admin_create') }}
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
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
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
  transform: scale(0.95);
  opacity: 0;
}
</style>
