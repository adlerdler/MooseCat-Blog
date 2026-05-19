<script setup>
/**
 * ContentForm.vue - 统一的内容表单组件
 * 
 * 功能说明：
 * - 支持文章、视频、项目、资源的表单
 * - 根据内容类型动态显示对应的表单字段
 * - 支持新增和编辑两种模式
 * 
 * 使用示例：
 * <ContentForm 
 *   :content-type="'post'" 
 *   :edit-data="null" 
 *   @save="handleSave" 
 *   @cancel="handleCancel" 
 * />
 */
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { X, Save, Plus, Trash2 } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import TagInput from './TagInput.vue';

const { t } = useI18n();

const props = defineProps({
  contentType: {
    type: String,
    required: true,
    validator: (value) => ['post', 'video', 'project', 'resource', 'social-link'].includes(value)
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
  const typeMap = {
    post: isEditMode.value ? t('admin_edit') + ' ' + t('admin_posts') : t('admin_add') + ' ' + t('admin_posts'),
    video: isEditMode.value ? t('admin_edit') + ' ' + t('admin_videos') : t('admin_add') + ' ' + t('admin_videos'),
    project: isEditMode.value ? t('admin_edit') + ' ' + t('admin_projects') : t('admin_add') + ' ' + t('admin_projects'),
    resource: isEditMode.value ? t('admin_edit') + ' ' + t('admin_resources') : t('admin_add') + ' ' + t('admin_resources'),
    'social-link': isEditMode.value ? t('admin_edit') + ' ' + t('admin_social_links') : t('admin_add') + ' ' + t('admin_social_links')
  };
  return typeMap[props.contentType];
});

const formData = ref({});

const initFormData = () => {
  const baseForm = {
    post: {
      title: '',
      category: 'THEORY',
      date: new Date().toISOString().split('T')[0].replace(/-/g, '.'),
      author: '',
      excerpt: '',
      content: '',
      color: 'red',
      tags: '',
      thumbnail: ''
    },
    video: {
      title: '',
      description: '',
      thumbnail: '',
      video_id: '',
      platform: 'youtube',
      date: new Date().toISOString().split('T')[0]
    },
    project: {
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
    },
    resource: {
      title: '',
      category: 'DESIGN',
      image: '',
      format: 'PDF',
      file_size: '',
      downloads_count: '0',
      date: new Date().toISOString().split('T')[0],
      direct_link: '',
      drives: []
    },
    'social-link': {
      platform: 'github',
      label: '',
      url: '',
      sort_order: 0
    }
  };
  
  formData.value = { ...baseForm[props.contentType] };
};

watch(() => props.visible, (newVal) => {
  if (newVal) {
    initFormData();
    if (props.editData) {
      Object.assign(formData.value, props.editData);
      if (formData.value.tags && Array.isArray(formData.value.tags)) {
        formData.value.tags = formData.value.tags.join(', ');
      }
      if (formData.value.technologies && Array.isArray(formData.value.technologies)) {
        formData.value.technologies = formData.value.technologies.join(', ');
      }
      if (formData.value.drives && !Array.isArray(formData.value.drives)) {
        formData.value.drives = [];
      }
    }
  }
});

const handleSubmit = () => {
  const data = { ...formData.value };
  
  if (data.tags && typeof data.tags === 'string') {
    data.tags = data.tags.split(',').map(tag => tag.trim()).filter(tag => tag);
  }
  if (data.technologies && typeof data.technologies === 'string') {
    data.technologies = data.technologies.split(',').map(tech => tech.trim()).filter(tech => tech);
  }
  
  emit('save', data);
};

const handleCancel = () => {
  emit('cancel');
};

const postCategories = [
  { value: 'THEORY', label: 'Theory' },
  { value: 'DESIGN', label: 'Design' },
  { value: 'TECHNOLOGY', label: 'Technology' },
  { value: 'CULTURE', label: 'Culture' },
  { value: 'SYSTEM-DESIGN', label: 'System Design' },
  { value: 'ENGINEERING', label: 'Engineering' }
];

const colorOptions = [
  { value: 'red', label: t('admin_post_form_red') },
  { value: 'black', label: t('admin_post_form_black') }
];

const platforms = [
  { value: 'youtube', label: 'YouTube' },
  { value: 'bilibili', label: 'Bilibili' }
];

const resourceFormats = [
  { value: 'PDF', label: 'PDF' },
  { value: 'Image', label: 'Image' },
  { value: 'Video', label: 'Video' },
  { value: 'Archive', label: 'Archive' },
  { value: 'Document', label: 'Document' }
];

const driveTypes = ['Google Drive', 'Dropbox', 'Baidu', 'OneDrive', 'AliCloud', 'Vimeo', 'Local', 'Other'];

const addDrive = () => {
  if (!formData.value.drives) {
    formData.value.drives = [];
  }
  formData.value.drives.push({ type: 'Google Drive', url: '' });
};

const removeDrive = (index) => {
  formData.value.drives.splice(index, 1);
};
</script>

<template>
  <Transition name="modal">
    <div v-if="visible" class="fixed inset-0 z-[100] flex items-center justify-center">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-black/60" @click="handleCancel"></div>
      
      <!-- Modal Content -->
      <div
        :class="[
          'relative w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto rounded-xl shadow-2xl',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]"
      >
        <!-- Header -->
        <div
          :class="[
            'sticky top-0 flex items-center justify-between p-6 border-b z-10',
            isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
          ]"
        >
          <h2 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">
            {{ formTitle }}
          </h2>
          <button
            @click="handleCancel"
            :class="[
              'p-2 rounded-lg transition-colors',
              isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600'
            ]"
          >
            <X :size="20" />
          </button>
        </div>

        <!-- Form Body -->
        <div class="p-6 space-y-6">
          <!-- ========== 文章表单 ========== -->
          <template v-if="contentType === 'post'">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_post_form_title') }} *
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
                placeholder="输入文章标题..."
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_post_form_category') }} *
                </label>
                <select
                  v-model="formData.category"
                  :class="[
                    'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode 
                      ? 'bg-gray-700 border-gray-600 text-white' 
                      : 'bg-white border-gray-300 text-gray-900'
                  ]"
                >
                  <option v-for="cat in postCategories" :key="cat.value" :value="cat.value">
                    {{ cat.label }}
                  </option>
                </select>
              </div>

              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_post_form_date') }} *
                </label>
                <input
                  v-model="formData.date"
                  type="text"
                  :class="[
                    'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode 
                      ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                      : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                  ]"
                  placeholder="YYYY.MM.DD"
                />
              </div>
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_post_form_thumbnail') }}
              </label>
              <input
                v-model="formData.thumbnail"
                type="text"
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                  isDarkMode 
                    ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                ]"
                placeholder="输入缩略图 URL..."
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_post_form_author') }} *
              </label>
              <input
                v-model="formData.author"
                type="text"
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                  isDarkMode 
                    ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                ]"
                placeholder="输入作者名称..."
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_post_form_excerpt') }} *
              </label>
              <textarea
                v-model="formData.excerpt"
                rows="2"
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red resize-none',
                  isDarkMode 
                    ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                ]"
                placeholder="输入文章摘要..."
              ></textarea>
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_post_form_content') }} *
              </label>
              <textarea
                v-model="formData.content"
                rows="10"
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red resize-none font-mono text-sm',
                  isDarkMode 
                    ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                ]"
                placeholder="输入 Markdown 格式的内容..."
              ></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_post_form_color') }}
                </label>
                <select
                  v-model="formData.color"
                  :class="[
                    'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode 
                      ? 'bg-gray-700 border-gray-600 text-white' 
                      : 'bg-white border-gray-300 text-gray-900'
                  ]"
                >
                  <option v-for="color in colorOptions" :key="color.value" :value="color.value">
                    {{ color.label }}
                  </option>
                </select>
              </div>

              <div>
                <TagInput v-model="formData.tags" :label="t('admin_post_form_tags')" placeholder="输入标签，按回车或逗号添加" />
              </div>
            </div>
          </template>

          <!-- ========== 视频表单 ========== -->
          <template v-if="contentType === 'video'">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_video_form_title') }} *
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
                placeholder="输入视频标题..."
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_video_form_platform') }} *
                </label>
                <select
                  v-model="formData.platform"
                  :class="[
                    'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode 
                      ? 'bg-gray-700 border-gray-600 text-white' 
                      : 'bg-white border-gray-300 text-gray-900'
                  ]"
                >
                  <option v-for="platform in platforms" :key="platform.value" :value="platform.value">
                    {{ platform.label }}
                  </option>
                </select>
              </div>

              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_video_form_video_id') }} *
                </label>
                <input
                  v-model="formData.video_id"
                  type="text"
                  :class="[
                    'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode 
                      ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                      : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                  ]"
                  :placeholder="formData.platform === 'youtube' ? '例如：dQw4w9WgXcQ' : '例如：BV1xx411c7mZ'"
                />
              </div>
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
{{ t('admin_video_form_thumbnail') }}
              </label>
              <input
                v-model="formData.thumbnail"
                type="text"
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                  isDarkMode 
                    ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                ]"
                placeholder="输入缩略图链接..."
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_video_form_description') }} *
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
                placeholder="输入视频描述..."
              ></textarea>
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                发布日期 *
              </label>
              <input
                v-model="formData.date"
                type="text"
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                  isDarkMode 
                    ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                ]"
                placeholder="例如：Apr 28, 2026"
              />
            </div>
          </template>

          <!-- ========== 项目表单 ========== -->
          <template v-if="contentType === 'project'">
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
                <TagInput v-model="formData.tags" :label="t('admin_post_form_tags')" placeholder="输入标签，按回车或逗号添加" />
              </div>

              <div>
                <TagInput v-model="formData.technologies" :label="t('admin_project_form_tech')" placeholder="输入技术，按回车或逗号添加" />
              </div>
            </div>
          </template>

          <!-- ========== 资源表单 ========== -->
          <template v-if="contentType === 'resource'">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_resource_form_title') }} *
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
                placeholder="输入资源名称..."
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_resource_form_category') }}
                </label>
                <select
                  v-model="formData.category"
                  :class="[
                    'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode 
                      ? 'bg-gray-700 border-gray-600 text-white' 
                      : 'bg-white border-gray-300 text-gray-900'
                  ]"
                >
                  <option value="DESIGN">Design</option>
                  <option value="THEORY">Theory</option>
                  <option value="PRACTICE">Practice</option>
                </select>
              </div>

              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_resource_form_format') }} *
                </label>
                <select
                  v-model="formData.format"
                  :class="[
                    'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode 
                      ? 'bg-gray-700 border-gray-600 text-white' 
                      : 'bg-white border-gray-300 text-gray-900'
                  ]"
                >
                  <option v-for="format in resourceFormats" :key="format.value" :value="format.value">
                    {{ format.label }}
                  </option>
                </select>
              </div>
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
{{ t('admin_video_form_thumbnail') }}
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
                placeholder="输入缩略图链接..."
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_resource_form_local_url') }} *
              </label>
              <input
                v-model="formData.direct_link"
                type="text"
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                  isDarkMode 
                    ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                ]"
                placeholder="例如：#download/resource.pdf"
              />
            </div>

            <div>
              <div class="flex items-center justify-between mb-2">
                <label :class="['block text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_resource_form_drives') }}
                </label>
                <button
                  type="button"
                  @click="addDrive"
                  class="flex items-center gap-1 px-3 py-1 text-sm rounded-lg transition-colors bg-construct-red text-white hover:bg-construct-red/80"
                >
                  <Plus class="w-4 h-4" />
                  {{ t('admin_add') }}
                </button>
              </div>
              <div class="space-y-3">
                <div
                  v-for="(drive, index) in formData.drives"
                  :key="index"
                  :class="[
                    'flex gap-3 items-start p-3 rounded-lg border',
                    isDarkMode ? 'bg-gray-700/50 border-gray-600' : 'bg-gray-50 border-gray-200'
                  ]"
                >
                  <select
                    v-model="drive.type"
                    :class="[
                      'w-36 px-3 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red flex-shrink-0',
                      isDarkMode 
                        ? 'bg-gray-600 border-gray-500 text-white' 
                        : 'bg-white border-gray-300 text-gray-900'
                    ]"
                  >
                    <option v-for="type in driveTypes" :key="type" :value="type">
                      {{ type }}
                    </option>
                  </select>
                  <input
                    v-model="drive.url"
                    type="text"
                    :class="[
                      'flex-1 px-3 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                      isDarkMode 
                        ? 'bg-gray-600 border-gray-500 text-white placeholder-gray-400' 
                        : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                    ]"
                    placeholder="输入网盘链接..."
                  />
                  <button
                    type="button"
                    @click="removeDrive(index)"
                    :class="[
                      'p-2 rounded-lg transition-colors flex-shrink-0',
                      isDarkMode 
                        ? 'text-gray-400 hover:text-red-400 hover:bg-gray-600' 
                        : 'text-gray-500 hover:text-red-500 hover:bg-gray-200'
                    ]"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
                <p
                  v-if="formData.drives.length === 0"
                  :class="['text-sm text-center py-4', isDarkMode ? 'text-gray-500' : 'text-gray-400']"
                >
                  {{ t('admin_resource_form_no_drive') }}
                </p>
              </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_resource_form_file_size') }}
                </label>
                <input
                  v-model="formData.file_size"
                  type="text"
                  :class="[
                    'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode 
                      ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                      : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                  ]"
                  placeholder="例如：24.5 MB"
                />
              </div>

              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_resource_form_downloads') }}
                </label>
                <input
                  v-model="formData.downloads_count"
                  type="text"
                  :class="[
                    'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode 
                      ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                      : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                  ]"
                  placeholder="例如：234"
                />
              </div>

              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_post_form_date') }}
                </label>
                <input
                  v-model="formData.date"
                  type="text"
                  :class="[
                    'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode 
                      ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                      : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                  ]"
                  placeholder="YYYY-MM-DD"
                />
              </div>
            </div>
          </template>

          <!-- ========== 社交媒体链接表单 ========== -->
          <template v-if="contentType === 'social-link'">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                平台类型
              </label>
              <select
                v-model="formData.platform"
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                  isDarkMode 
                    ? 'bg-gray-700 border-gray-600 text-white' 
                    : 'bg-white border-gray-300 text-gray-900'
                ]"
              >
                <option value="github">GitHub</option>
                <option value="twitter">Twitter</option>
                <option value="linkedin">LinkedIn</option>
                <option value="email">Email</option>
              </select>
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                显示名称 *
              </label>
              <input
                v-model="formData.label"
                type="text"
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                  isDarkMode 
                    ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
                ]"
                placeholder="例如：GITHUB"
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                链接地址 *
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
                placeholder="例如：https://github.com/username"
              />
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
          </template>
        </div>

        <!-- Footer -->
        <div
          :class="[
            'sticky bottom-0 flex gap-3 p-6 border-t',
            isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
          ]"
        >
          <button
            @click="handleCancel"
            :class="[
              'flex-1 px-4 py-2 text-sm font-bold rounded-lg transition-colors',
              isDarkMode 
                ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            {{ t('admin_cancel') }}
          </button>
          <button
            @click="handleSubmit"
            class="flex-1 px-4 py-2 text-sm font-bold text-white bg-gray-900 rounded-lg hover:bg-construct-red transition-colors flex items-center justify-center gap-2"
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
