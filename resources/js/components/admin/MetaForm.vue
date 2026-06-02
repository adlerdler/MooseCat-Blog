<script setup>
/**
 * MetaForm.vue - 分类/标签统一表单组件
 * 
 * 功能说明：
 * - 支持分类和标签的新增/编辑
 * - 根据类型动态显示不同字段
 * - 支持状态切换
 * 
 * 使用示例：
 * <MetaForm type="category" :edit-data="editingCategory" @save="handleSave" @cancel="handleCancel" />
 */
import { ref, computed, watch } from 'vue';
import { X, Save } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { useI18n } from 'vue-i18n';

const props = defineProps({
  type: {
    type: String,
    required: true,
    validator: (value) => ['category', 'tag'].includes(value)
  },
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
const { t } = useI18n();

const isEditMode = computed(() => props.editData !== null);

const formTitle = computed(() => {
  if (props.type === 'category') {
    return isEditMode.value ? t('admin_edit_category') : t('admin_add_category');
  }
  return isEditMode.value ? t('admin_edit_tag') : t('admin_add_tag');
});

const formData = ref({});
const error = ref('');

const initFormData = () => {
  error.value = '';
  if (props.type === 'category') {
    formData.value = {
      name: '',
      description: '',
      status: 'active'
    };
  } else {
    formData.value = {
      name: ''
    };
  }
};

watch(() => props.visible, (newVal) => {
  if (newVal) {
    initFormData();
    if (props.editData) {
      Object.assign(formData.value, props.editData);
    }
  }
});

watch(() => formData.value.name, () => {
  if (error.value) error.value = '';
});

const handleSubmit = () => {
  if (!formData.value.name.trim()) {
    error.value = props.type === 'category' ? '请输入分类名称' : '请输入标签名称';
    return;
  }
  error.value = '';
  emit('save', { ...formData.value });
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
          'relative w-full max-w-sm mx-4 rounded-2xl shadow-2xl overflow-hidden',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]"
      >
        <!-- Header -->
        <div class="flex items-center justify-between p-5 pb-3">
          <h3 :class="['text-lg font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">
            {{ formTitle }}
          </h3>
          <button
            @click="handleCancel"
            :class="[
              'p-1.5 rounded-full transition-colors',
              isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-400 hover:text-gray-900 hover:bg-gray-100'
            ]"
          >
            <X class="w-4 h-4" />
          </button>
        </div>

        <!-- Body -->
        <div class="px-5 pb-5">
          <!-- Name -->
          <div>
            <label :class="['block text-xs font-bold mb-1.5', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ type === 'category' ? t('admin_category_name') : t('admin_tag_name') }} <span class="text-construct-red">*</span>
            </label>
            <input
              v-model="formData.name"
              type="text"
              :class="[
                'w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 transition-all',
                error
                  ? 'border-red-400 focus:ring-red-400/50 focus:border-red-500'
                  : 'focus:ring-construct-red/50 focus:border-construct-red',
                isDarkMode 
                  ? 'bg-gray-700/50 border-gray-600 text-white placeholder-gray-500' 
                  : 'bg-gray-50 border-gray-200 text-gray-900 placeholder-gray-400'
              ]"
              :placeholder="type === 'category' ? t('admin_enter_category_name') : t('admin_enter_tag_name')"
            />
            <p v-if="!error" :class="['mt-1.5 text-[11px]', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              {{ type === 'category' ? '例如: 技术文章、设计理论' : '例如: JavaScript、Vue.js、前端开发' }}
            </p>
            <p v-else class="mt-1.5 text-[11px] text-red-500 font-medium">{{ error }}</p>
          </div>

          <!-- Description (仅分类) -->
          <div v-if="type === 'category'" class="mt-4">
            <label :class="['block text-xs font-bold mb-1.5', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ t('admin_description') }}
            </label>
            <textarea
              v-model="formData.description"
              rows="3"
              :class="[
                'w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-construct-red/50 focus:border-construct-red resize-none transition-all',
                isDarkMode 
                  ? 'bg-gray-700/50 border-gray-600 text-white placeholder-gray-500' 
                  : 'bg-gray-50 border-gray-200 text-gray-900 placeholder-gray-400'
              ]"
              :placeholder="t('admin_enter_category_description')"
            ></textarea>
          </div>

          <!-- Status (仅分类) -->
          <div v-if="type === 'category'" class="mt-4">
            <label :class="['block text-xs font-bold mb-1.5', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ t('admin_status') }}
            </label>
            <div class="flex items-center gap-3">
              <button
                type="button"
                @click="toggleStatus"
                :class="[
                  'w-11 h-6 rounded-full relative transition-colors',
                  formData.status === 'active' ? 'bg-green-500' : (isDarkMode ? 'bg-gray-600' : 'bg-gray-300')
                ]"
              >
                <div :class="['absolute top-0.5 w-5 h-5 rounded-full bg-white shadow-sm transition-transform', formData.status === 'active' ? 'left-[22px]' : 'left-0.5']"></div>
              </button>
              <span :class="['text-sm font-medium', formData.status === 'active' ? 'text-green-500' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]">
                {{ formData.status === 'active' ? t('admin_active') : t('admin_inactive') }}
              </span>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex gap-2 p-5 pt-0">
          <button
            @click="handleCancel"
            :class="[
              'flex-1 px-4 py-2.5 text-sm font-bold rounded-xl transition-colors',
              isDarkMode 
                ? 'bg-gray-700/50 text-gray-300 hover:bg-gray-700' 
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
            ]"
          >
            {{ t('admin_cancel') }}
          </button>
          <button
            @click="handleSubmit"
            class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2.5 bg-construct-red text-white text-sm font-bold rounded-xl hover:bg-red-600 transition-colors"
          >
            <Save class="w-3.5 h-3.5" />
            {{ isEditMode ? t('admin_save') : t('admin_add') }}
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
