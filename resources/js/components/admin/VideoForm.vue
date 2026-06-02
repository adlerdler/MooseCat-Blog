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
  categories: {
    type: Array,
    default: () => []
  },
  mediaFiles: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['save', 'cancel']);

const isEditMode = computed(() => props.editData !== null);

const formTitle = computed(() => {
  return isEditMode.value ? t('admin_edit') + ' ' + t('admin_videos') : t('admin_add') + ' ' + t('admin_videos');
});

const statusOptions = [
  { value: 'draft', label: '草稿' },
  { value: 'published', label: '已发布' }
];

const platformOptions = [
  { value: '', label: '请选择平台' },
  { value: 'youtube', label: 'YouTube' },
  { value: 'bilibili', label: 'Bilibili' },
  { value: 'local', label: '本站点' }
];

const formData = ref({
  title: '',
  description: '',
  thumbnail: '',
  url: '',
  video_id: '',
  platform: '',
  duration: '',
  category: '',
  date: new Date().toISOString().split('T')[0].replace(/-/g, '.'),
  tags: '',
  status: 'draft'
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
    errors.value.title = '标题不能为空';
    valid = false;
  }
  if (!formData.value.description.trim()) {
    errors.value.description = '描述不能为空';
    valid = false;
  }
  return valid;
};

const initFormData = () => {
  formData.value = {
    title: '',
    description: '',
    thumbnail: '',
    url: '',
    video_id: '',
    platform: '',
    duration: '',
    category: '',
    date: new Date().toISOString().split('T')[0].replace(/-/g, '.'),
    tags: '',
    status: 'draft'
  };
  clearErrors();
};

watch(() => props.visible, (newVal) => {
  if (newVal) {
    initFormData();
    if (props.editData) {
      Object.assign(formData.value, props.editData);
      if (formData.value.tags && Array.isArray(formData.value.tags)) {
        formData.value.tags = formData.value.tags.join(', ');
      }
    }
  }
});

// 从 URL 自动识别平台和视频 ID
const detectPlatform = (url) => {
  if (!url || !url.trim()) return { platform: '', video_id: '' };

  const trimmed = url.trim();
  const currentHost = window.location.hostname;

  // 相对路径或本站链接 → local
  if (trimmed.startsWith('/') || trimmed.startsWith('./') || trimmed.startsWith('../')) {
    return { platform: 'local', video_id: trimmed };
  }

  // 本站完整 URL
  try {
    const urlObj = new URL(trimmed);
    if (urlObj.hostname === currentHost || urlObj.hostname === 'localhost' || urlObj.hostname === '127.0.0.1') {
      return { platform: 'local', video_id: urlObj.pathname + urlObj.search };
    }
  } catch (_) { /* 非完整 URL，继续检查 */ }

  // YouTube
  const ytMatch = trimmed.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
  if (ytMatch) {
    return { platform: 'youtube', video_id: ytMatch[1] };
  }

  // Bilibili
  const biliMatch = trimmed.match(/bilibili\.com\/video\/(BV[a-zA-Z0-9]+)/);
  if (biliMatch) {
    return { platform: 'bilibili', video_id: biliMatch[1] };
  }

  // 无协议前缀、非知名平台 → 视为本站文件
  if (!trimmed.match(/^https?:\/\//)) {
    return { platform: 'local', video_id: trimmed };
  }

  return { platform: '', video_id: '' };
};

watch(() => formData.value.url, (newUrl) => {
  const result = detectPlatform(newUrl);
  if (result.platform) {
    formData.value.platform = result.platform;
    formData.value.video_id = result.video_id;
  }
});

// 媒体选择器
const showMediaPicker = ref(false);
const handleMediaSelect = (file) => {
  formData.value.thumbnail = file.url;
  showMediaPicker.value = false;
};

const handleSave = () => {
  if (!validate()) return;
  const data = { ...formData.value };
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
          {{ t('admin_video_form_title') }} <span class="text-construct-red">*</span>
        </label>
        <input
          v-model="formData.title"
          type="text"
          :class="[
            'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2',
            errors.title ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-construct-red',
            isDarkMode 
              ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
              : 'bg-white text-gray-900 placeholder-gray-500'
          ]"
          placeholder="输入视频标题..."
        />
        <p v-if="errors.title" class="text-red-500 text-xs mt-1 font-bold">{{ errors.title }}</p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            视频 URL
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
            placeholder="https://www.youtube.com/watch?v=xxx"
          />
        </div>

        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            视频平台
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
            <option v-for="opt in platformOptions" :key="opt.value" :value="opt.value">
              {{ opt.label }}
            </option>
          </select>
          <p class="text-[10px] mt-1 text-gray-400">输入视频 URL 后自动识别平台</p>
        </div>
      </div>

      <div>
        <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          视频 ID
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
          :placeholder="formData.platform === 'youtube' ? 'YouTube 视频 ID，如 dQw4w9WgXcQ' : formData.platform === 'bilibili' ? 'Bilibili BV 号，如 BV1XG411s76m' : '本站点无需填写视频 ID'"
        />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            {{ t('admin_post_form_category') }}
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
            <option value="">无分类</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>

        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            时长 (秒)
          </label>
          <input
            v-model="formData.duration"
            type="number"
            :class="[
              'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
              isDarkMode 
                ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
            ]"
            placeholder="例如：360"
          />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
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
            placeholder="YYYY.MM.DD"
          />
        </div>

        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            状态
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
            <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
              {{ opt.label }}
            </option>
          </select>
        </div>
      </div>

      <div>
        <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          {{ t('admin_video_form_thumbnail') }}
        </label>
        <div class="flex gap-2">
          <input
            v-model="formData.thumbnail"
            type="text"
            :class="[
              'flex-1 px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
              isDarkMode 
                ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
            ]"
            placeholder="输入缩略图 URL..."
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
        <!-- 缩略图预览 -->
        <div v-if="formData.thumbnail" class="mt-2">
          <img :src="formData.thumbnail" alt="缩略图预览" class="w-32 h-20 object-cover rounded-lg border" />
        </div>
      </div>

      <div>
        <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          {{ t('admin_video_form_description') }} <span class="text-construct-red">*</span>
        </label>
        <textarea
          v-model="formData.description"
          rows="3"
          :class="[
            'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 resize-none',
            errors.description ? 'border-red-500 focus:ring-red-500' : 'focus:ring-construct-red',
            isDarkMode 
              ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
              : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
          placeholder="输入视频描述（必填）..."
        ></textarea>
        <p v-if="errors.description" class="text-red-500 text-xs mt-1 font-bold">{{ errors.description }}</p>
      </div>

      <div>
        <TagInput v-model="formData.tags" label="标签" placeholder="输入标签，按回车或逗号添加" />
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
