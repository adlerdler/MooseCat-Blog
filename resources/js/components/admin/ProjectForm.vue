<script setup>
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useTheme } from '../../composables/useTheme';
import { Image as ImageIcon } from 'lucide-vue-next';
import TagInput from './TagInput.vue';
import ContentFormModal from './ContentFormModal.vue';
import MediaPickerModal from './MediaPickerModal.vue';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const props = defineProps({
  editData: {
    type: Object,
    default: null
  },
  visible: {
    type: Boolean,
    default: false
  },
  mediaFiles: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['save', 'cancel']);

const isEditMode = computed(() => props.editData !== null);

const formTitle = computed(() => {
  return isEditMode.value ? t('admin_edit') + ' ' + t('admin_projects') : t('admin_add') + ' ' + t('admin_projects');
});

const formData = ref({
  title: '',
  description: '',
  long_description: '',
  image: '',
  url: '',
  github_url: '',
  role: '',
  year: new Date().getFullYear().toString(),
  technologies: '',
  tags: '',
  status: 'completed',
  sort_order: 0,
});

const initFormData = () => {
  formData.value = {
    title: '',
    description: '',
    long_description: '',
    image: '',
    url: '',
    github_url: '',
    role: '',
    year: new Date().getFullYear().toString(),
    technologies: '',
    tags: '',
    status: 'completed',
    sort_order: 0,
  };
  clearErrors();
};

watch(() => props.visible, (newVal) => {
  if (newVal) {
    initFormData();
    if (props.editData) {
      Object.assign(formData.value, props.editData);
      if (formData.value.technologies && Array.isArray(formData.value.technologies)) {
        formData.value.technologies = formData.value.technologies.join(', ');
      }
      if (formData.value.tags && Array.isArray(formData.value.tags)) {
        formData.value.tags = formData.value.tags.join(', ');
      }
    }
  }
});

const errors = ref({
  title: '',
  description: ''
});

const clearErrors = () => {
  errors.value = { title: '', description: '' };
};

const validate = () => {
  clearErrors();
  let valid = true;
  if (!formData.value.title.trim()) {
    errors.value.title = '项目名称不能为空';
    valid = false;
  }
  if (!formData.value.description.trim()) {
    errors.value.description = '简短描述不能为空';
    valid = false;
  }
  return valid;
};

// 媒体选择器
const showMediaPicker = ref(false);
const handleMediaSelect = (file) => {
  formData.value.image = file.url;
  showMediaPicker.value = false;
};

const handleSave = () => {
  if (!validate()) return;
  const data = { ...formData.value };
  if (data.technologies && typeof data.technologies === 'string') {
    data.technologies = data.technologies.split(',').map(tech => tech.trim()).filter(tech => tech);
  }
  if (data.tags && typeof data.tags === 'string') {
    data.tags = data.tags.split(',').map(tag => tag.trim()).filter(tag => tag);
  }
  emit('save', data);
};

const handleCancel = () => {
  emit('cancel');
};

</script>

<template>
  <ContentFormModal
    :title="formTitle"
    :visible="visible"
    :is-edit-mode="isEditMode"
    @save="handleSave"
    @cancel="handleCancel"
  >
    <div class="space-y-6">
      <div>
        <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          {{ t('admin_project_form_title') }} <span class="text-construct-red">*</span>
        </label>
        <input
          v-model="formData.title"
          type="text"
          :class="[
            'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2',
            errors.title ? 'border-red-500 focus:ring-red-500' : 'focus:ring-construct-red',
            isDarkMode 
              ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
              : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
          placeholder="输入项目名称..."
        />
        <p v-if="errors.title" class="text-red-500 text-xs mt-1 font-bold">{{ errors.title }}</p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            {{ t('admin_project_form_role') }}
          </label>
          <input
            v-model="formData.role"
            type="text"
            :class="[
              'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
              isDarkMode 
                ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
            ]"
            placeholder="例如：Lead Architect"
          />
        </div>

        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            {{ t('admin_project_form_year') }}
          </label>
          <input
            v-model="formData.year"
            type="text"
            :class="[
              'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
              isDarkMode 
                ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
            ]"
            placeholder="例如：2026"
          />
        </div>
      </div>

      <div>
        <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          {{ t('admin_project_form_short_desc') }} <span class="text-construct-red">*</span>
        </label>
        <textarea
          v-model="formData.description"
          rows="2"
          :class="[
            'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 resize-none',
            errors.description ? 'border-red-500 focus:ring-red-500' : 'focus:ring-construct-red',
            isDarkMode 
              ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
              : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
          placeholder="输入项目简短描述..."
        ></textarea>
        <p v-if="errors.description" class="text-red-500 text-xs mt-1 font-bold">{{ errors.description }}</p>
      </div>

      <div>
        <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          {{ t('admin_project_form_desc') }}
        </label>
        <textarea
          v-model="formData.long_description"
          rows="4"
          :class="[
            'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red resize-none',
            isDarkMode 
              ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
              : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
          placeholder="输入项目详细描述..."
        ></textarea>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            项目图片 URL
          </label>
          <div class="flex gap-2">
            <input
              v-model="formData.image"
              type="text"
              :class="[
                'flex-1 px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                isDarkMode 
                  ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                  : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
              ]"
              placeholder="输入图片链接..."
            />
            <button
              type="button"
              @click="showMediaPicker = true"
              class="px-4 py-2 bg-construct-black text-white text-sm font-bold rounded-lg hover:bg-construct-red transition-colors flex items-center gap-2 shrink-0"
            >
              <ImageIcon size="16" />
              选图
            </button>
          </div>
          <!-- 图片预览 -->
          <div v-if="formData.image" class="mt-2">
            <img :src="formData.image" alt="项目预览" class="w-32 h-20 object-cover rounded-lg border" />
          </div>
        </div>

        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            项目网址
          </label>
          <input
            v-model="formData.url"
            type="text"
            :class="[
              'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
              isDarkMode 
                ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
            ]"
            placeholder="输入项目网址..."
          />
        </div>
      </div>

      <div>
        <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          {{ t('admin_project_form_github') }}
        </label>
        <input
          v-model="formData.github_url"
          type="text"
          :class="[
            'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
            isDarkMode 
              ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
              : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
          placeholder="输入 GitHub 仓库链接..."
        />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            项目状态
          </label>
          <select
            v-model="formData.status"
            :class="[
              'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
              isDarkMode 
                ? 'bg-gray-700 border-gray-600 text-white' 
                : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option value="completed">已完成</option>
            <option value="in-progress">进行中</option>
            <option value="planning">计划中</option>
          </select>
        </div>
        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            排序顺序
          </label>
          <input
            v-model.number="formData.sort_order"
            type="number"
            min="0"
            :class="[
              'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
              isDarkMode 
                ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
            ]"
            placeholder="数字越小越靠前"
          />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <TagInput v-model="formData.technologies" :label="t('admin_project_form_tech')" placeholder="输入技术，按回车或逗号添加" />
        </div>
        <div>
          <TagInput v-model="formData.tags" label="标签" placeholder="输入标签，按回车或逗号添加" />
        </div>
      </div>
    </div>
  </ContentFormModal>

  <!-- 媒体选择器弹窗 -->
  <MediaPickerModal
    :visible="showMediaPicker"
    :media="props.mediaFiles"
    @select="handleMediaSelect"
    @close="showMediaPicker = false"
  />
</template>
