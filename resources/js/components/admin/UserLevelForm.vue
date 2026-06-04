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
import { X, Save, Crown } from 'lucide-vue-next';
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
    min_points: 0,
    max_points: null,
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
    <div v-if="visible" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="handleCancel">

      <div
        :class="[
          'modal-content w-full max-w-lg mx-4 flex flex-col max-h-[85vh] rounded-2xl shadow-2xl ring-1',
          isDarkMode ? 'bg-gray-800 ring-gray-700' : 'bg-white ring-gray-200/80'
        ]"
      >
        <!-- Header (sticky top) -->
        <div class="flex items-center justify-between p-6 pb-3 flex-shrink-0">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl shadow-md bg-gradient-to-br from-yellow-400 to-amber-600 flex items-center justify-center flex-shrink-0">
              <Crown :size="18" :style="{ color: '#ffffff' }" />
            </div>
            <div>
              <h3 :class="['font-display text-xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">
                {{ formTitle }}
              </h3>
              <p :class="['text-xs font-medium mt-0.5', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                {{ isEditMode ? '编辑用户等级' : '创建新的用户等级' }}
              </p>
            </div>
          </div>
          <button
            @click="handleCancel"
            :class="[
              'p-2 rounded-xl transition-colors flex-shrink-0',
              isDarkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-500 hover:bg-gray-100'
            ]"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Body (scrollable) -->
        <div class="px-6 py-2 space-y-4 overflow-y-auto flex-1 scroll-smooth">
          <!-- Name -->
          <div>
            <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ t('admin_level_form_name') }} <span class="text-construct-red">*</span>
            </label>
            <input
              v-model="formData.name"
              type="text"
              :class="[
                'w-full px-4 py-3 rounded-xl border focus:border-construct-red focus:outline-none transition-all',
                isDarkMode
                  ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500'
                  : 'bg-gray-50 border-gray-300 text-gray-900 placeholder-gray-400'
              ]"
              placeholder="VIP"
            />
          </div>

          <!-- Level + Discount -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                {{ t('admin_level_form_level') }}
              </label>
              <input
                v-model.number="formData.level"
                type="number"
                min="1"
                max="99"
                :class="[
                  'w-full px-4 py-3 rounded-xl border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode
                    ? 'bg-gray-700 border-gray-600 text-white'
                    : 'bg-gray-50 border-gray-300 text-gray-900'
                ]"
              />
            </div>

            <div>
              <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                {{ t('admin_level_form_discount') }}
              </label>
              <div class="relative">
                <input
                  v-model.number="formData.discount"
                  type="number"
                  min="0"
                  max="100"
                  :class="[
                    'w-full px-4 py-3 pr-10 rounded-xl border focus:border-construct-red focus:outline-none transition-all',
                    isDarkMode
                      ? 'bg-gray-700 border-gray-600 text-white'
                      : 'bg-gray-50 border-gray-300 text-gray-900'
                  ]"
                />
                <span :class="['absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold', isDarkMode ? 'text-gray-400' : 'text-gray-400']">%</span>
              </div>
            </div>
          </div>

          <!-- Min Points + Max Points -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                {{ t('admin_level_form_min_points') || '最低积分' }} <span class="text-construct-red">*</span>
              </label>
              <input
                v-model.number="formData.min_points"
                type="number"
                min="0"
                :class="[
                  'w-full px-4 py-3 rounded-xl border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode
                    ? 'bg-gray-700 border-gray-600 text-white'
                    : 'bg-gray-50 border-gray-300 text-gray-900'
                ]"
              />
            </div>

            <div>
              <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                {{ t('admin_level_form_max_points') || '最高积分' }}
              </label>
              <input
                v-model.number="formData.max_points"
                type="number"
                :min="formData.min_points"
                :class="[
                  'w-full px-4 py-3 rounded-xl border focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode
                    ? 'bg-gray-700 border-gray-600 text-white'
                    : 'bg-gray-50 border-gray-300 text-gray-900'
                ]"
              />
            </div>
          </div>

          <!-- Description -->
          <div>
            <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ t('admin_level_form_description') }}
            </label>
            <textarea
              v-model="formData.description"
              rows="2"
              :class="[
                'w-full px-4 py-3 rounded-xl border focus:border-construct-red focus:outline-none transition-all resize-none',
                isDarkMode
                  ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500'
                  : 'bg-gray-50 border-gray-300 text-gray-900 placeholder-gray-400'
              ]"
              placeholder="等级描述..."
            ></textarea>
          </div>

          <!-- Color -->
          <div>
            <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ t('admin_level_form_color') }}
            </label>
            <div class="flex gap-2 flex-wrap items-center">
              <button
                v-for="color in availableColors"
                :key="color"
                type="button"
                @click="formData.color = color"
                :class="[
                  'w-9 h-9 rounded-xl border-2 transition-all',
                  formData.color === color ? 'border-gray-900 scale-110 shadow-md' : 'border-transparent hover:scale-105'
                ]"
                :style="{ backgroundColor: color }"
              />
              <input
                v-model="formData.color"
                type="color"
                class="w-9 h-9 rounded-xl cursor-pointer border-2 border-transparent hover:border-gray-300 transition-all"
              />
            </div>
          </div>

          <!-- Active -->
          <div>
            <label :class="['flex items-center gap-3 cursor-pointer py-1', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              <input
                v-model="formData.is_active"
                type="checkbox"
                class="w-5 h-5 rounded-md border-gray-300 text-construct-red focus:ring-construct-red"
              />
              <span class="text-sm font-semibold">{{ t('admin_level_form_active') }}</span>
            </label>
          </div>
        </div>

        <!-- Footer (sticky bottom) -->
        <div class="flex gap-3 p-6 pt-3 flex-shrink-0">
          <button
            @click="handleCancel"
            :class="[
              'flex-1 px-6 py-3 font-bold tracking-wider uppercase text-sm rounded-xl border transition-colors',
              isDarkMode
                ? 'border-gray-600 text-gray-300 hover:bg-gray-700'
                : 'border-gray-300 text-gray-500 hover:bg-gray-100'
            ]"
          >
            {{ t('admin_cancel') }}
          </button>
          <button
            @click="handleSubmit"
            class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-wider uppercase text-sm rounded-xl shadow-lg shadow-construct-red/20 hover:bg-red-700 transition-all"
          >
            <Save class="w-4 h-4" :style="{ color: '#ffffff' }" />
            {{ isEditMode ? t('admin_save') : t('admin_create') }}
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.font-display {
  font-family: 'Outfit', sans-serif;
}

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
  transition: transform 0.35s ease, opacity 0.3s ease;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
  transform: scale(0.95) translateY(10px);
  opacity: 0;
}
</style>
