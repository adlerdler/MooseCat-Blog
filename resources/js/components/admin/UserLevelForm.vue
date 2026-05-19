<script setup>
/**
 * UserLevelForm.vue - 用户等级管理表单组件
 *
 * 功能说明：
 * - 支持用户等级的新增/编辑
 * - 包含等级名称、图标、颜色、折扣、权益等字段
 *
 * 使用示例：
 * <UserLevelForm :edit-data="editingLevel" :visible="isFormVisible" @save="handleSave" @cancel="handleCancel" />
 */
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { X, Save } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

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
  return isEditMode.value ? t('admin_edit') + ' ' + t('admin_user_level') : t('admin_add') + ' ' + t('admin_user_level');
});

const formData = ref({});

const initFormData = () => {
  formData.value = {
    name: '',
    level: 1,
    icon: 'user',
    color: '#808080',
    description: '',
    discount: 0,
    benefits: [],
    is_active: true,
    sort_order: 1
  };
};

const availableIcons = [
  'crown', 'star', 'award', 'user', 'user-x', 'shield', 'heart',
  'diamond', 'gift', 'trophy', 'medal', 'badge'
];

const availableColors = [
  '#ffd700', '#c0c0c0', '#cd7f32', '#4682b4', '#808080',
  '#dc143c', '#228b22', '#4169e1', '#ff6347', '#9370db'
];

watch(() => props.visible, (newVal) => {
  if (newVal) {
    initFormData();
    if (props.editData) {
      formData.value = {
        ...props.editData,
        benefits: [...(props.editData.benefits || [])]
      };
    }
  }
});

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
          'relative w-full max-w-lg mx-4 rounded-xl shadow-2xl overflow-hidden',
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

        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_level_form_name') }} *
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
              placeholder="VIP"
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_level_form_level') }}
              </label>
              <input
                v-model.number="formData.level"
                type="number"
                min="1"
                max="99"
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                  isDarkMode
                    ? 'bg-gray-700 border-gray-600 text-white'
                    : 'bg-white border-gray-300 text-gray-900'
                ]"
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_level_form_discount') }}
              </label>
              <div class="relative">
                <input
                  v-model.number="formData.discount"
                  type="number"
                  min="0"
                  max="100"
                  :class="[
                    'w-full px-4 py-2 pr-8 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode
                      ? 'bg-gray-700 border-gray-600 text-white'
                      : 'bg-white border-gray-300 text-gray-900'
                  ]"
                />
                <span :class="['absolute right-3 top-1/2 -translate-y-1/2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">%</span>
              </div>
            </div>
          </div>

          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_level_form_description') }}
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
              placeholder="等级描述..."
            ></textarea>
          </div>

          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_level_form_color') }}
            </label>
            <div class="flex gap-2 flex-wrap">
              <button
                v-for="color in availableColors"
                :key="color"
                type="button"
                @click="formData.color = color"
                :class="[
                  'w-8 h-8 rounded-full border-2 transition-all',
                  formData.color === color ? 'border-gray-900 scale-110' : 'border-transparent'
                ]"
                :style="{ backgroundColor: color }"
              />
              <input
                v-model="formData.color"
                type="color"
                :class="['w-8 h-8 rounded cursor-pointer']"
              />
            </div>
          </div>

          <div>
            <label :class="['flex items-center gap-2 cursor-pointer', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              <input
                v-model="formData.is_active"
                type="checkbox"
                class="w-4 h-4 rounded border-gray-300 text-construct-red focus:ring-construct-red"
              />
              <span class="text-sm font-bold">{{ t('admin_level_form_active') }}</span>
            </label>
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
</style>
