<script setup>
/**
 * RoleForm.vue - 角色管理表单组件
 *
 * 基于 Spatie/laravel-permission 方案 + 扩展字段。
 *
 * 功能说明：
 * - 支持角色的新增/编辑
 * - 包含角色名称(name)、标签(label)、描述(description)、颜色(color)、守卫(guard_name)字段
 * - 权限支持多选，按守卫分组显示
 *
 * 使用示例：
 * <RoleForm :edit-data="editingRole" :visible="isFormVisible" @save="handleSave" @cancel="handleCancel" />
 */
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { X, Save, Check, Palette } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { useRolePermissions } from '../../composables/useRolePermissions';

const { availablePermissions, permissions, getPermissionIdsByRoleId, GUARD_LABELS, COLOR_MAP } = useRolePermissions();

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

const colorOptions = [
  { value: 'red', label: 'Red', class: 'bg-red-600 border-red-500' },
  { value: 'blue', label: 'Blue', class: 'bg-blue-600 border-blue-500' },
  { value: 'green', label: 'Green', class: 'bg-green-600 border-green-500' },
  { value: 'purple', label: 'Purple', class: 'bg-purple-600 border-purple-500' },
  { value: 'gray', label: 'Gray', class: 'bg-gray-600 border-gray-500' },
  { value: 'yellow', label: 'Yellow', class: 'bg-yellow-500 border-yellow-400' },
  { value: 'cyan', label: 'Cyan', class: 'bg-cyan-600 border-cyan-500' },
  { value: 'orange', label: 'Orange', class: 'bg-orange-600 border-orange-500' },
  { value: 'pink', label: 'Pink', class: 'bg-pink-600 border-pink-500' }
];

const guardOptions = ['web', 'api', 'admin'];

const initFormData = () => {
  formData.value = {
    name: '',
    label: '',
    description: '',
    color: 'blue',
    guard_name: 'web',
    permissions: []
  };
};

const getRolePermissionIds = (roleId) => {
  return getPermissionIdsByRoleId(roleId);
};

const getRolePermissionNames = (roleId) => {
  const permissionIds = getRolePermissionIds(roleId);
  return permissionIds.map(id => {
    const permission = permissions.find(p => p.id === id);
    return permission ? permission.name : '';
  }).filter(Boolean);
};

const getPermissionsByGuard = (guardName) => {
  return availablePermissions.filter(p => p.guard_name === guardName);
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
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="handleCancel" />

      <div
        :class="[
          'relative w-full max-w-2xl mx-4 rounded-xl shadow-2xl overflow-hidden max-h-[90vh]',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]"
      >
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

        <div class="p-6 space-y-4 max-h-[calc(90vh-140px)] overflow-y-auto">
          <div class="grid grid-cols-2 gap-4">
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
                placeholder="e.g., admin, editor"
              />
              <p :class="['text-xs mt-1', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                Unique identifier, use lowercase
              </p>
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_role_form_label') }}
              </label>
              <input
                v-model="formData.label"
                type="text"
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red uppercase',
                  isDarkMode
                    ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400'
                    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                ]"
                placeholder="e.g., ADMIN, EDITOR"
              />
              <p :class="['text-xs mt-1', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                For i18n display
              </p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                <Palette class="inline w-4 h-4 mr-1" />
                {{ t('admin_role_form_color') }}
              </label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="color in colorOptions"
                  :key="color.value"
                  type="button"
                  @click="formData.color = color.value"
                  :class="[
                    'w-8 h-8 rounded-full border-2 transition-all',
                    formData.color === color.value ? 'border-gray-900 scale-110' : 'border-transparent hover:scale-105',
                    color.class
                  ]"
                />
              </div>
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_role_form_guard') }}
              </label>
              <select
                v-model="formData.guard_name"
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                  isDarkMode
                    ? 'bg-gray-700 border-gray-600 text-white'
                    : 'bg-white border-gray-300 text-gray-900'
                ]"
              >
                <option v-for="guard in guardOptions" :key="guard" :value="guard">
                  {{ GUARD_LABELS[guard] || guard }}
                </option>
              </select>
              <p :class="['text-xs mt-1', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                Controls which guard this role applies to
              </p>
            </div>
          </div>

          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_role_form_description') }} *
            </label>
            <textarea
              v-model="formData.description"
              rows="2"
              :class="[
                'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red resize-none',
                isDarkMode
                  ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400'
                  : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
              ]"
              placeholder="Describe this role's purpose..."
            ></textarea>
          </div>

          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_role_form_permissions') }}
            </label>

            <div v-for="guard in guardOptions" :key="guard" class="mb-4">
              <div :class="['text-xs font-bold uppercase tracking-wider mb-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                {{ GUARD_LABELS[guard] || guard }} Permissions
              </div>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                <button
                  v-for="permission in getPermissionsByGuard(guard)"
                  :key="permission.name"
                  type="button"
                  @click="togglePermission(permission.name)"
                  :class="[
                    'flex items-center gap-2 px-3 py-2 rounded-lg border text-sm font-medium transition-colors',
                    hasPermission(permission.name)
                      ? 'bg-construct-red border-construct-red text-white'
                      : isDarkMode
                        ? 'bg-gray-700 border-gray-600 text-gray-300 hover:border-gray-500'
                        : 'bg-white border-gray-300 text-gray-700 hover:border-gray-400'
                  ]"
                >
                  <Check v-if="hasPermission(permission.name)" class="w-4 h-4 flex-shrink-0" />
                  <div v-else class="w-4 h-4 flex-shrink-0" />
                  <span class="truncate">{{ permission.label }}</span>
                </button>
              </div>
            </div>

            <p :class="['text-xs mt-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              {{ t('admin_role_form_selected') }}: {{ formData.permissions.length }}
            </p>
          </div>
        </div>

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
