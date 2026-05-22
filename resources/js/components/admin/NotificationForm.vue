<script setup>
/**
 * NotificationForm.vue - 通知表单组件
 *
 * 功能说明：
 * - 支持通知的新增/编辑/查看
 * - 包含标题、内容、类型、链接字段
 */
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useTheme } from '../../composables/useTheme';
import { Save, X } from 'lucide-vue-next';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const props = defineProps({
  notification: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['save', 'close']);

const isViewMode = computed(() => props.notification?.action === 'view');
const isEditMode = computed(() => props.notification?.action === 'edit');
const isAddMode = computed(() => props.notification?.action === 'add');

const formData = ref({});

const typeOptions = [
  { value: 'info', label: t('admin_notification_type_info') || '信息' },
  { value: 'warning', label: t('admin_notification_type_warning') || '警告' },
  { value: 'error', label: t('admin_notification_type_error') || '错误' },
  { value: 'success', label: t('admin_notification_type_success') || '成功' }
];

watch(() => props.notification, (newVal) => {
  if (newVal) {
    formData.value = {
      id: newVal.id || null,
      title: newVal.title || '',
      message: newVal.message || '',
      type: newVal.type || 'info',
      link: newVal.link || '',
      action: newVal.action || 'add'
    };
  }
}, { immediate: true });

const handleSave = () => {
  if (!formData.value.title.trim() || !formData.value.message.trim()) {
    return;
  }
  emit('save', { ...formData.value });
};

const handleClose = () => {
  emit('close');
};
</script>

<template>
  <div :class="['p-6 space-y-6', isDarkMode ? 'bg-gray-800' : 'bg-white']">
    <!-- Title -->
    <div>
      <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
        {{ t('admin_notification_form_title') || '通知标题' }} *
      </label>
      <input
        v-model="formData.title"
        type="text"
        :disabled="isViewMode"
        :class="[
          'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
          isDarkMode
            ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400 disabled:opacity-50'
            : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500 disabled:opacity-50'
        ]"
        :placeholder="t('admin_notification_form_title_placeholder') || '输入通知标题...'"
      />
    </div>

    <!-- Type -->
    <div>
      <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
        {{ t('admin_notification_form_type') || '通知类型' }} *
      </label>
      <select
        v-model="formData.type"
        :disabled="isViewMode"
        :class="[
          'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
          isDarkMode
            ? 'bg-gray-700 border-gray-600 text-white disabled:opacity-50'
            : 'bg-white border-gray-300 text-gray-900 disabled:opacity-50'
        ]"
      >
        <option v-for="option in typeOptions" :key="option.value" :value="option.value">
          {{ option.label }}
        </option>
      </select>
    </div>

    <!-- Message -->
    <div>
      <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
        {{ t('admin_notification_form_message') || '通知内容' }} *
      </label>
      <textarea
        v-model="formData.message"
        :disabled="isViewMode"
        rows="4"
        :class="[
          'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red resize-none',
          isDarkMode
            ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400 disabled:opacity-50'
            : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500 disabled:opacity-50'
        ]"
        :placeholder="t('admin_notification_form_message_placeholder') || '输入通知内容...'"
      />
    </div>

    <!-- Link -->
    <div>
      <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
        {{ t('admin_notification_form_link') || '关联链接' }}
      </label>
      <input
        v-model="formData.link"
        type="text"
        :disabled="isViewMode"
        :class="[
          'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
          isDarkMode
            ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400 disabled:opacity-50'
            : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500 disabled:opacity-50'
        ]"
        :placeholder="t('admin_notification_form_link_placeholder') || '输入关联链接（可选）...'"
      />
    </div>

    <!-- Actions -->
    <div :class="['flex items-center justify-end gap-3 pt-4 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
      <button
        @click="handleClose"
        :class="[
          'px-4 py-2 text-sm font-bold tracking-widest rounded-lg transition-colors',
          isDarkMode
            ? 'text-gray-400 hover:text-white hover:bg-gray-700'
            : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'
        ]"
      >
        {{ t('admin_cancel') || '取消' }}
      </button>
      <button
        v-if="!isViewMode"
        @click="handleSave"
        class="inline-flex items-center gap-2 px-4 py-2 bg-construct-red text-white text-sm font-bold tracking-widest rounded-lg hover:bg-red-700 transition-colors"
      >
        <Save size="16" />
        {{ t('admin_save') || '保存' }}
      </button>
    </div>
  </div>
</template>
