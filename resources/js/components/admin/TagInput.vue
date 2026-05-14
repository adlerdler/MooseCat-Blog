<script setup>
/**
 * TagInput.vue - 标签输入组件
 * 
 * 功能说明：
 * - 支持输入标签并按回车或逗号添加
 * - 显示已添加的标签列表
 * - 支持点击删除标签
 * - 支持最大标签数量限制
 * 
 * 使用示例：
 * <TagInput v-model="tags" placeholder="输入标签..." :maxTags="10" />
 */
import { ref, computed } from 'vue';
import { X, Plus } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: '输入标签，按回车或逗号添加'
  },
  maxTags: {
    type: Number,
    default: 20
  },
  label: {
    type: String,
    default: '标签'
  }
});

const emit = defineEmits(['update:modelValue']);

const { isDarkMode } = useTheme();
const inputValue = ref('');

const tags = computed({
  get: () => {
    if (!props.modelValue) return [];
    return props.modelValue.split(',').map(tag => tag.trim()).filter(tag => tag);
  },
  set: (newTags) => {
    emit('update:modelValue', newTags.join(', '));
  }
});

const addTag = () => {
  const tag = inputValue.value.trim();
  if (!tag) return;
  
  if (tags.value.length >= props.maxTags) {
    return;
  }
  
  if (tags.value.includes(tag)) {
    return;
  }
  
  tags.value = [...tags.value, tag];
  inputValue.value = '';
};

const removeTag = (index) => {
  tags.value = tags.value.filter((_, i) => i !== index);
};

const handleKeydown = (e) => {
  if (e.key === 'Enter' || e.key === ',') {
    e.preventDefault();
    addTag();
  }
  if (e.key === 'Backspace' && !inputValue.value && tags.value.length > 0) {
    tags.value = tags.value.slice(0, -1);
  }
};
</script>

<template>
  <div>
    <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
      {{ label }}
    </label>
    <div
      :class="[
        'flex flex-wrap gap-2 p-3 rounded-lg border min-h-[48px] cursor-text',
        isDarkMode 
          ? 'bg-gray-700 border-gray-600' 
          : 'bg-white border-gray-300'
      ]"
      @click="$refs.input.focus()"
    >
      <span
        v-for="(tag, index) in tags"
        :key="index"
        :class="[
          'flex items-center gap-1 px-3 py-1 text-sm rounded-md transition-colors',
          isDarkMode 
            ? 'bg-gray-600 text-gray-200' 
            : 'bg-gray-100 text-gray-700'
        ]"
      >
        {{ tag }}
        <button
          type="button"
          @click.stop="removeTag(index)"
          :class="[
            'p-0.5 rounded transition-colors',
            isDarkMode 
              ? 'hover:bg-gray-500 text-gray-400 hover:text-gray-200' 
              : 'hover:bg-gray-200 text-gray-500 hover:text-gray-700'
          ]"
        >
          <X class="w-3 h-3" />
        </button>
      </span>
      
      <input
        ref="input"
        v-model="inputValue"
        type="text"
        :placeholder="tags.length === 0 ? placeholder : ''"
        :class="[
          'flex-1 min-w-[120px] bg-transparent outline-none text-sm',
          isDarkMode 
            ? 'text-white placeholder-gray-400' 
            : 'text-gray-900 placeholder-gray-500'
        ]"
        @keydown="handleKeydown"
        @blur="addTag"
      />
    </div>
    <p :class="['text-xs mt-1', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
      已添加 {{ tags.length }} / {{ maxTags }} 个标签
    </p>
  </div>
</template>
