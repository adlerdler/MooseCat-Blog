<script setup>
/**
 * ConfirmDialog.vue - 公共确认弹窗组件
 * 
 * 功能说明：
 * - 用于删除、退出等需要确认的操作
 * - 支持自定义标题、内容和按钮文本
 * - 支持深色/浅色模式
 * 
 * 使用示例：
 * <ConfirmDialog
 *   :visible="showDeleteConfirm"
 *   title="确认删除"
 *   content="确定要删除这个项目吗？此操作不可撤销。"
 *   confirm-text="删除"
 *   @confirm="handleDelete"
 *   @cancel="showDeleteConfirm = false"
 * />
 */
import { useI18n } from 'vue-i18n';
import { X, AlertTriangle } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { t } = useI18n();

const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  content: {
    type: String,
    default: ''
  },
  confirmText: {
    type: String,
    default: ''
  },
  cancelText: {
    type: String,
    default: ''
  },
  confirmVariant: {
    type: String,
    default: 'danger',
    validator: (value) => ['danger', 'primary', 'warning'].includes(value)
  }
});

const emit = defineEmits(['confirm', 'cancel']);

const { isDarkMode } = useTheme();

const handleConfirm = () => {
  emit('confirm');
};

const handleCancel = () => {
  emit('cancel');
};

const getConfirmButtonClasses = () => {
  const baseClasses = 'flex-1 flex items-center justify-center gap-2 px-6 py-3 font-bold tracking-widest uppercase text-sm transition-colors rounded';
  
  switch (props.confirmVariant) {
    case 'danger':
      return `${baseClasses} bg-construct-red text-white hover:bg-red-700`;
    case 'warning':
      return `${baseClasses} bg-yellow-500 text-white hover:bg-yellow-600`;
    case 'primary':
      return `${baseClasses} bg-construct-red text-white hover:bg-red-700`;
    default:
      return `${baseClasses} bg-construct-red text-white hover:bg-red-700`;
  }
};
</script>

<template>
  <Transition name="modal">
    <div v-if="visible" class="fixed inset-0 z-[200] flex items-center justify-center">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="handleCancel" />

      <!-- Modal -->
      <div
        :class="[
          'relative w-full max-w-md mx-4 rounded-xl shadow-2xl overflow-hidden',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]"
      >
        <!-- Header -->
        <div class="flex items-center justify-between p-6">
          <div class="flex items-center gap-3">
            <div :class="[
              'w-10 h-10 rounded-full flex items-center justify-center',
              confirmVariant === 'danger' ? 'bg-red-500/20' : 'bg-yellow-500/20'
            ]">
              <AlertTriangle :class="[
                'w-5 h-5',
                confirmVariant === 'danger' ? 'text-red-500' : 'text-yellow-500'
              ]" />
            </div>
            <h3 :class="['text-lg font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">
              {{ title || t('admin_confirm_title') }}
            </h3>
          </div>
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
        <div class="px-6 pb-6">
          <p :class="['text-sm', isDarkMode ? 'text-gray-300' : 'text-gray-600']">
            {{ content || t('admin_confirm_content') }}
          </p>
        </div>

        <!-- Footer -->
        <div class="flex gap-3 px-6 pb-6">
          <button
            @click="handleCancel"
            :class="[
              'flex-1 px-6 py-3 font-bold tracking-widest uppercase text-sm transition-colors rounded',
              isDarkMode 
                ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            {{ cancelText || t('admin_cancel') }}
          </button>
          <button
            @click="handleConfirm"
            :class="getConfirmButtonClasses()"
          >
            {{ confirmText || t('confirm') }}
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
