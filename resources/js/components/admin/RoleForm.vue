<script setup>
/**
 * RoleForm.vue - 角色管理表单组件
 *
 * 基于 Spatie/laravel-permission 方案 + 扩展字段。
 *
 * 功能说明：
 * - 支持角色的新增/编辑
 * - 包含角色名称(name)、标签(label)、描述(description)、颜色(color)、守卫(guard_name)字段
 * - 权限支持多选，按 program_id 分组显示
 *
 * 使用示例：
 * <RoleForm :edit-data="editingRole" :permissions="permissions" :visible="isFormVisible" @save="handleSave" @cancel="handleCancel" />
 */
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { X, Save, Check, Palette } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { useRolePermissions } from '../../composables/useRolePermissions';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const props = defineProps({
  editData: {
    type: Object,
    default: null
  },
  visible: {
    type: Boolean,
    default: false
  },
  permissions: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['save', 'cancel']);

const { availablePermissions, GUARD_LABELS, COLOR_MAP } = useRolePermissions({
  permissions: props.permissions,
});

const PAGE_GROUPS = [
  { key: 'dashboard', label: '仪表盘', permissions: ['view_analytics'] },
  { key: 'posts', label: '文章管理', permissions: ['manage_posts'] },
  { key: 'videos', label: '视频管理', permissions: ['manage_videos'] },
  { key: 'projects', label: '项目管理', permissions: ['manage_projects'] },
  { key: 'resources', label: '资源管理', permissions: ['manage_resources'] },
  { key: 'journals', label: '日志管理', permissions: ['manage_journals'] },
  { key: 'categories', label: '分类管理', permissions: ['manage_categories'] },
  { key: 'tags', label: '标签管理', permissions: ['manage_tags'] },
  { key: 'comments', label: '评论管理', permissions: ['manage_comments'] },
  { key: 'ads', label: '广告管理', permissions: ['manage_ads'] },
  { key: 'users', label: '用户管理', permissions: ['manage_users'] },
  { key: 'subscribers', label: '订阅者管理', permissions: ['manage_subscribers'] },
  { key: 'user_levels', label: '用户等级', permissions: ['manage_user_levels'] },
  { key: 'roles', label: '角色权限', permissions: ['manage_roles'] },
  { key: 'settings', label: '基本设置', permissions: ['manage_settings'] },
  { key: 'social_links', label: '社交链接', permissions: ['manage_social_links'] },
  { key: 'seo', label: 'SEO管理', permissions: ['manage_seo'] },
  { key: 'i18n', label: '国际化', permissions: ['manage_i18n'] },
  { key: 'media', label: '媒体管理', permissions: ['manage_media'] },
  { key: 'email_templates', label: '邮件模板', permissions: ['manage_email_templates'] },
  { key: 'menu', label: '菜单管理', permissions: ['manage_menu'] },
  { key: 'notifications', label: '通知管理', permissions: ['manage_notifications'] },
  { key: 'mail_config', label: '邮件配置', permissions: ['manage_mail_config'] },
  { key: 'logs', label: '系统日志', permissions: ['manage_logs'] },
  { key: 'backup', label: '备份管理', permissions: ['manage_backup'] },
  { key: 'restore', label: '恢复管理', permissions: ['manage_restore'] },
];

const getPermissionsByPage = (pageKey) => {
  const page = PAGE_GROUPS.find(p => p.key === pageKey);
  if (!page) return [];
  return availablePermissions.filter(p => page.permissions.includes(p.name));
};

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

const guardOptions = ['web', 'admin'];

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

watch(() => props.visible, (newVal) => {
  if (newVal) {
    initFormData();
    if (props.editData) {
      const permIds = props.editData.permissions || [];
      formData.value = {
        ...props.editData,
        guard_name: guardOptions.includes(props.editData.guard_name) ? props.editData.guard_name : 'web',
        permissions: [...permIds]
      };
    }
  }
});

const togglePermission = (permissionId) => {
  const index = formData.value.permissions.indexOf(permissionId);
  if (index === -1) {
    formData.value.permissions.push(permissionId);
  } else {
    formData.value.permissions.splice(index, 1);
  }
};

const hasPermission = (permissionId) => {
  return (formData.value.permissions || []).includes(permissionId);
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

        <div class="p-6 space-y-4 max-h-[calc(90vh-200px)] overflow-y-auto">
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
            <label :class="['block text-sm font-bold mb-3', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_role_form_permissions') }}
            </label>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
              <button
                v-for="permission in availablePermissions"
                :key="permission.id"
                type="button"
                @click="togglePermission(permission.id)"
                :class="[
                  'flex items-center gap-2 px-3 py-2.5 rounded-lg border text-sm font-medium transition-all duration-200',
                  hasPermission(permission.id)
                    ? 'bg-construct-red/10 border-construct-red text-construct-red hover:bg-construct-red/20'
                    : isDarkMode
                      ? 'bg-gray-700/50 border-gray-600 text-gray-300 hover:border-gray-500 hover:bg-gray-700'
                      : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50'
                ]"
              >
                <div :class="[
                  'w-4 h-4 rounded flex items-center justify-center border transition-colors',
                  hasPermission(permission.id)
                    ? 'bg-construct-red border-construct-red'
                    : isDarkMode
                      ? 'border-gray-500'
                      : 'border-gray-300'
                ]">
                  <Check v-if="hasPermission(permission.id)" class="w-3 h-3 text-white" />
                </div>
                <span class="truncate">{{ permission.label }}</span>
              </button>
            </div>

            <p :class="['text-xs mt-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              {{ t('admin_role_form_selected') }}: {{ formData.permissions?.length || 0 }}
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
