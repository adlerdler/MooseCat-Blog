<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { X, Save } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  visible: {
    type: Boolean,
    default: false
  },
  isEditMode: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['save', 'cancel']);

const handleSave = () => {
  emit('save');
};

const handleCancel = () => {
  emit('cancel');
};
</script>

<template>
  <Transition name="modal">
    <div v-if="visible" class="fixed inset-0 z-[100] flex items-center justify-center">
      <div class="absolute inset-0 bg-black/60" @click="handleCancel"></div>
      
      <div
        :class="[
          'relative w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl ring-1',
          isDarkMode ? 'bg-gray-800 ring-gray-700' : 'bg-white ring-gray-200'
        ]"
      >
        <div
          :class="[
            'sticky top-0 flex items-center justify-between p-6 pb-2 z-10',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]"
        >
          <h2 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">
            {{ title }}
          </h2>
          <button
            @click="handleCancel"
            :class="[
              'p-2 rounded-xl transition-colors',
              isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600'
            ]"
          >
            <X :size="20" />
          </button>
        </div>

        <div class="p-6 pt-2">
          <slot></slot>
        </div>

        <div
          :class="[
            'sticky bottom-0 flex gap-3 p-6 pt-2',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]"
        >
          <button
            @click="handleCancel"
            :class="[
              'flex-1 px-4 py-2 text-sm font-bold rounded-xl transition-colors',
              isDarkMode 
                ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            {{ t('admin_cancel') }}
          </button>
          <button
            @click="handleSave"
            class="flex-1 px-4 py-2 text-sm font-bold text-white bg-gray-900 rounded-xl hover:bg-construct-red transition-colors flex items-center justify-center gap-2"
          >
            <Save :size="16" />
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
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: transform 0.2s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
  transform: scale(0.95);
}
</style>
