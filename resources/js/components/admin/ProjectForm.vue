<script setup>
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useTheme } from '../../composables/useTheme';
import TagInput from './TagInput.vue';
import ContentFormModal from './ContentFormModal.vue';

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
  status: 'completed',
  sort_order: 0
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
    status: 'completed',
    sort_order: 0
  };
};

watch(() => props.visible, (newVal) => {
  if (newVal) {
    initFormData();
    if (props.editData) {
      Object.assign(formData.value, props.editData);
      if (formData.value.technologies && Array.isArray(formData.value.technologies)) {
        formData.value.technologies = formData.value.technologies.join(', ');
      }
    }
  }
});

const handleSave = () => {
  const data = { ...formData.value };
  if (data.technologies && typeof data.technologies === 'string') {
    data.technologies = data.technologies.split(',').map(tech => tech.trim()).filter(tech => tech);
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
          {{ t('admin_project_form_title') }} *
        </label>
        <input
          v-model="formData.title"
          type="text"
          :class="[
            'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
            isDarkMode 
              ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
              : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
          placeholder="输入项目名称..."
        />
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
          {{ t('admin_project_form_short_desc') }} *
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
          placeholder="输入项目简短描述..."
        ></textarea>
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
          <input
            v-model="formData.image"
            type="text"
            :class="[
              'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
              isDarkMode 
                ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
            ]"
            placeholder="输入图片链接..."
          />
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
      </div>
    </div>
  </ContentFormModal>
</template>
