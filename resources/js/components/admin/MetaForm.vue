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

const isEditMode = computed(() => props.editData !== null);

const formTitle = computed(() => {
  if (props.type === 'category') {
    return isEditMode.value ? '编辑分类' : '新增分类';
  }
  return isEditMode.value ? '编辑标签' : '新增标签';
});

const formData = ref({});

const initFormData = () => {
  if (props.type === 'category') {
    formData.value = {
      name: '',
      description: '',
      status: 'active'
    };
  } else {
    formData.value = {
      name: '',
      status: 'active'
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

const handleSubmit = () => {
  if (!formData.value.name.trim()) {
    return;
  }
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
          <!-- Name -->
          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ type === 'category' ? '分类名称' : '标签名称' }} *
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
              :placeholder="type === 'category' ? '输入分类名称...' : '输入标签名称...'"
            />
          </div>

          <!-- Description (仅分类) -->
          <div v-if="type === 'category'">
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              描述
            </label>
            <textarea
              v-model="formData.description"
              rows="3"
              :class="[
                'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red resize-none',
                isDarkMode 
                  ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                  : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
              ]"
              placeholder="输入分类描述..."
            ></textarea>
          </div>

          <!-- Status -->
          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              状态
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
                {{ formData.status === 'active' ? '启用' : '禁用' }}
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
            取消
          </button>
          <button
            @click="handleSubmit"
            class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-700 transition-colors rounded"
          >
            <Save class="w-4 h-4" />
            {{ isEditMode ? '保存' : '新增' }}
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
