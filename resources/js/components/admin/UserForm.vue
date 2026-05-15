<script setup>
/**
 * UserForm.vue - 用户管理表单组件
 * 
 * 功能说明：
 * - 支持用户的新增/编辑
 * - 包含用户名、邮箱、密码、角色、状态字段
 * - 编辑时密码可选
 * 
 * 使用示例：
 * <UserForm :edit-data="editingUser" :visible="isFormVisible" @save="handleSave" @cancel="handleCancel" />
 */
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { X, Save } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { roles } from '../../data/roles';

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
  return isEditMode.value ? t('admin_edit') + ' ' + t('admin_users') : t('admin_add') + ' ' + t('admin_users');
});

const formData = ref({});

const initFormData = () => {
  formData.value = {
    name: '',
    email: '',
    password: '',
    role: 'subscriber',
    status: 'active'
  };
};

watch(() => props.visible, (newVal) => {
  if (newVal) {
    initFormData();
    if (props.editData) {
      formData.value = {
        ...props.editData,
        password: ''
      };
    }
  }
});

const handleSubmit = () => {
  if (!formData.value.name.trim() || !formData.value.email.trim()) {
    return;
  }
  
  if (!isEditMode.value && !formData.value.password.trim()) {
    return;
  }
  
  const data = { ...formData.value };
  if (isEditMode.value && !data.password) {
    delete data.password;
  }
  
  emit('save', data);
};

const handleCancel = () => {
  emit('cancel');
};

const toggleStatus = () => {
  formData.value.status = formData.value.status === 'active' ? 'inactive' : 'active';
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
          'relative w-full max-w-lg mx-4 rounded-xl shadow-2xl overflow-hidden',
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
          <!-- Username -->
          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_user_form_name') }} *
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
              placeholder="输入用户名..."
            />
          </div>

          <!-- Email -->
          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_user_form_email') }} *
            </label>
            <input
              v-model="formData.email"
              type="email"
              :class="[
                'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                isDarkMode 
                  ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                  : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
              ]"
              placeholder="输入邮箱地址..."
            />
          </div>

          <!-- Password -->
          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_user_form_password') }} {{ isEditMode ? t('admin_user_form_password_hint') : '*' }}
            </label>
            <input
              v-model="formData.password"
              type="password"
              :class="[
                'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                isDarkMode 
                  ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                  : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
              ]"
              :placeholder="isEditMode ? '留空则不修改密码' : '输入密码...'"
            />
          </div>

          <!-- Role -->
          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_user_form_role') }}
            </label>
            <select
              v-model="formData.role"
              :class="[
                'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                isDarkMode 
                  ? 'bg-gray-700 border-gray-600 text-white' 
                  : 'bg-white border-gray-300 text-gray-900'
              ]"
            >
              <option v-for="role in roles" :key="role.value" :value="role.value">
                {{ role.label }}
              </option>
            </select>
          </div>

          <!-- Status -->
          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_user_form_status') }}
            </label>
            <div class="flex items-center gap-3">
              <button
                type="button"
                @click="toggleStatus"
                :class="[
                  'w-12 h-6 rounded-full relative transition-colors',
                  formData.status === 'active' ? 'bg-green-500' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-400')
                ]"
              >
                <div :class="['absolute top-1 w-4 h-4 rounded-full bg-white transition-transform', formData.status === 'active' ? 'left-7' : 'left-1']"></div>
              </button>
              <span :class="['text-sm font-bold', formData.status === 'active' ? 'text-green-500' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]">
                {{ formData.status === 'active' ? t('admin_user_form_active') : t('admin_user_form_inactive') }}
              </span>
            </div>
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
