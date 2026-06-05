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
  <!-- Form Body (scrollable) -->
  <div class="nf-form-body" :class="isDarkMode ? 'nf-is-dark' : 'nf-is-light'">
      <!-- Title -->
      <div>
        <label class="nf-label">
          {{ t('admin_notification_form_title') || '通知标题' }} <span class="text-construct-red">*</span>
        </label>
        <input
          v-model="formData.title"
          type="text"
          :disabled="isViewMode"
          class="nf-input"
          :placeholder="t('admin_notification_form_title_placeholder') || '输入通知标题...'"
        />
      </div>

      <!-- Type -->
      <div>
        <label class="nf-label">
          {{ t('admin_notification_form_type') || '通知类型' }} <span class="text-construct-red">*</span>
        </label>
        <select
          v-model="formData.type"
          :disabled="isViewMode"
          class="nf-select"
        >
          <option v-for="option in typeOptions" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
      </div>

      <!-- Message -->
      <div>
        <label class="nf-label">
          {{ t('admin_notification_form_message') || '通知内容' }} <span class="text-construct-red">*</span>
        </label>
        <textarea
          v-model="formData.message"
          :disabled="isViewMode"
          rows="4"
          class="nf-textarea"
          :placeholder="t('admin_notification_form_message_placeholder') || '输入通知内容...'"
        />
      </div>

      <!-- Link -->
      <div>
        <label class="nf-label">
          {{ t('admin_notification_form_link') || '关联链接' }}
        </label>
        <input
          v-model="formData.link"
          type="text"
          :disabled="isViewMode"
          class="nf-input nf-input--mono"
          :placeholder="t('admin_notification_form_link_placeholder') || '输入关联链接（可选）...'"
        />
      </div>
    </div>

    <!-- Actions (sticky bottom) -->
    <div class="nf-footer" :class="isDarkMode ? 'nf-is-dark' : 'nf-is-light'">
      <button @click="handleClose" class="nf-btn-cancel">
        {{ t('admin_cancel') || '取消' }}
      </button>
      <button
        v-if="!isViewMode"
        @click="handleSave"
        class="nf-btn-save"
      >
        <Save :size="16" style="color: #ffffff;" />
        {{ t('admin_save') || '保存' }}
      </button>
    </div>
</template>

<style scoped>
/* ========== Dark/Light Context ========== */
.nf-form-body,
.nf-footer {
  transition: background 0.2s ease;
}
.nf-form-body.nf-is-dark,
.nf-footer.nf-is-dark {
  --nf-bg: #1f2937;
  --nf-input-bg: #374151;
  --nf-input-border: #4b5563;
  --nf-input-text: #ffffff;
  --nf-input-placeholder: #6b7280;
  --nf-label: #9ca3af;
  --nf-btn-cancel-text: #d1d5db;
  --nf-btn-cancel-border: #4b5563;
  --nf-btn-cancel-hover: #374151;
}
.nf-form-body.nf-is-light,
.nf-footer.nf-is-light {
  --nf-bg: #ffffff;
  --nf-input-bg: #f9fafb;
  --nf-input-border: #d1d5db;
  --nf-input-text: #111827;
  --nf-input-placeholder: #9ca3af;
  --nf-label: #6b7280;
  --nf-btn-cancel-text: #6b7280;
  --nf-btn-cancel-border: #d1d5db;
  --nf-btn-cancel-hover: #f3f4f6;
}

.nf-form-body {
  padding: 0.5rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  overflow-y: auto;
  flex: 1;
  scroll-behavior: smooth;
  background: var(--nf-bg);
}

/* ========== Form Controls ========== */
.nf-label {
  display: block;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  margin-bottom: 0.5rem;
  color: var(--nf-label);
}

.nf-input,
.nf-select,
.nf-textarea {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid var(--nf-input-border);
  border-radius: 0.75rem;
  background: var(--nf-input-bg);
  color: var(--nf-input-text);
  transition: all 0.2s ease;
  outline: none;
}
.nf-input:focus,
.nf-select:focus,
.nf-textarea:focus {
  border-color: var(--accent, #CF202E);
  box-shadow: 0 0 0 2px rgba(207, 32, 46, 0.15);
}
.nf-input::placeholder,
.nf-textarea::placeholder {
  color: var(--nf-input-placeholder);
}
.nf-input:disabled,
.nf-select:disabled,
.nf-textarea:disabled {
  opacity: 0.5;
}
.nf-textarea {
  resize: none;
}
.nf-select {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 1rem center;
  padding-right: 2.5rem;
}
.nf-input--mono {
  font-family: 'JetBrains Mono', 'Fira Code', monospace;
  font-size: 0.875rem;
}

/* ========== Footer ========== */
.nf-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem 1.5rem;
  flex-shrink: 0;
  background: var(--nf-bg);
}

.nf-btn-cancel {
  padding: 0.75rem 1.5rem;
  font-size: 0.875rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  border-radius: 0.75rem;
  border: 1px solid var(--nf-btn-cancel-border);
  color: var(--nf-btn-cancel-text);
  background: transparent;
  transition: all 0.15s ease;
}
.nf-btn-cancel:hover {
  background: var(--nf-btn-cancel-hover);
}

.nf-btn-save {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: var(--accent, #CF202E);
  color: #ffffff;
  font-size: 0.875rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  border-radius: 0.75rem;
  box-shadow: 0 4px 16px rgba(207, 32, 46, 0.2);
  transition: all 0.15s ease;
}
.nf-btn-save:hover {
  background: #b91c1c;
}
</style>
