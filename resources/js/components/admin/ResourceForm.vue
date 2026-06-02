<script setup>
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { Plus, Trash2, Image as ImageIcon } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
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
  return isEditMode.value ? t('admin_edit') + ' ' + t('admin_resources') : t('admin_add') + ' ' + t('admin_resources');
});

const resourceFormats = [
  { value: 'PDF', label: 'PDF' },
  { value: 'Image', label: 'Image' },
  { value: 'Video', label: 'Video' },
  { value: 'Archive', label: 'Archive' },
  { value: 'Document', label: 'Document' },
  { value: 'Other', label: 'Other' }
];

const driveTypes = ['Google Drive', 'Dropbox', 'Baidu', 'OneDrive', 'AliCloud', 'Vimeo', 'Local', 'Other'];

const formData = ref({
  category_id: '',
  title: '',
  description: '',
  image: '',
  format: '',
  file_size: '',
  direct_link: '',
  drives: []
});

const initFormData = () => {
  formData.value = {
    category_id: '',
    title: '',
    description: '',
    image: '',
    format: '',
    file_size: '',
    direct_link: '',
    drives: []
  };
  clearErrors();
};

watch(() => props.visible, (newVal) => {
  if (newVal) {
    initFormData();
    if (props.editData) {
      Object.assign(formData.value, props.editData);
      if (!formData.value.drives || !Array.isArray(formData.value.drives)) {
        formData.value.drives = [];
      }
    }
  }
});

const addDrive = () => {
  if (!formData.value.drives) {
    formData.value.drives = [];
  }
  formData.value.drives.push({ name: 'Google Drive', url: '' });
};

const removeDrive = (index) => {
  formData.value.drives.splice(index, 1);
};

const errors = ref({
  title: '',
  format: '',
  direct_link: ''
});

const clearErrors = () => {
  errors.value = { title: '', format: '', direct_link: '' };
};

const validate = () => {
  clearErrors();
  let valid = true;
  if (!formData.value.title.trim()) {
    errors.value.title = '资源名称不能为空';
    valid = false;
  }
  if (!formData.value.format) {
    errors.value.format = '请选择资源格式';
    valid = false;
  }
  if (!formData.value.direct_link.trim()) {
    errors.value.direct_link = '下载链接不能为空';
    valid = false;
  }
  return valid;
};

const handleSave = () => {
  if (!validate()) return;
  emit('save', { ...formData.value });
};

const handleCancel = () => {
  emit('cancel');
};

// 媒体选择器
const showMediaPicker = ref(false);
const handleMediaSelect = (file) => {
  formData.value.image = file.url;
  showMediaPicker.value = false;
};

const getFormatFromFile = (file) => {
  const ext = (file.url || '').split('.').pop()?.toLowerCase() || '';
  const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
  const videoExts = ['mp4', 'mov', 'avi', 'mkv', 'webm', 'flv'];
  const archiveExts = ['zip', 'rar', '7z', 'tar', 'gz', 'bz2'];
  const docExts = ['doc', 'docx', 'txt', 'md', 'csv', 'xls', 'xlsx', 'ppt', 'pptx'];
  
  if (ext === 'pdf') return 'PDF';
  if (imageExts.includes(ext)) return 'Image';
  if (videoExts.includes(ext)) return 'Video';
  if (archiveExts.includes(ext)) return 'Archive';
  if (docExts.includes(ext)) return 'Document';
  return 'Other';
};

const showDirectLinkPicker = ref(false);
const handleDirectLinkSelect = (file) => {
  formData.value.direct_link = file.url;
  formData.value.file_size = file.size || '';
  formData.value.format = getFormatFromFile(file);
  showDirectLinkPicker.value = false;
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
          {{ t('admin_resource_form_title') }} *
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
          placeholder="输入资源名称..."
        />
        <p v-if="errors.title" class="text-red-500 text-xs mt-1 font-bold">{{ errors.title }}</p>
      </div>

      <div>
        <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          {{ t('admin_resource_form_description') }}
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
          placeholder="输入资源描述..."
        />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            {{ t('admin_resource_form_category') }}
          </label>
          <select
            v-model="formData.category_id"
            :class="[
              'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
              isDarkMode 
                ? 'bg-gray-700 border-gray-600 text-white' 
                : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option value="">-- Select Category --</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>

        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            {{ t('admin_resource_form_format') }} *
          </label>
          <select
            v-model="formData.format"
            :class="[
              'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2',
              errors.format ? 'border-red-500 focus:ring-red-500' : 'focus:ring-construct-red',
              isDarkMode 
                ? 'bg-gray-700 border-gray-600 text-white' 
                : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option v-for="format in resourceFormats" :key="format.value" :value="format.value">
              {{ format.label }}
            </option>
          </select>
          <p v-if="errors.format" class="text-red-500 text-xs mt-1 font-bold">{{ errors.format }}</p>
        </div>
      </div>

      <div>
        <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          {{ t('admin_video_form_thumbnail') }}
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
            placeholder="输入缩略图链接..."
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
          <img :src="formData.image" alt="缩略图预览" class="w-32 h-20 object-cover rounded-lg border" />
        </div>
      </div>

      <div>
        <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          {{ t('admin_resource_form_local_url') }} *
        </label>
        <div class="flex gap-2">
          <input
            v-model="formData.direct_link"
            type="text"
            :class="[
              'flex-1 px-4 py-2 rounded-lg border focus:outline-none focus:ring-2',
              errors.direct_link ? 'border-red-500 focus:ring-red-500' : 'focus:ring-construct-red',
              isDarkMode 
                ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
                : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
            ]"
            placeholder="例如：#download/resource.pdf"
          />
          <button
            type="button"
            @click="showDirectLinkPicker = true"
            class="px-4 py-2 bg-construct-black text-white text-sm font-bold rounded-lg hover:bg-construct-red transition-colors flex items-center gap-2 shrink-0"
          >
            <ImageIcon size="16" />
            下载链接
          </button>
        </div>
        <p v-if="errors.direct_link" class="text-red-500 text-xs mt-1 font-bold">{{ errors.direct_link }}</p>
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
              v-model="drive.name"
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

    </div>
  </ContentFormModal>

  <!-- 缩略图媒体选择器弹窗 -->
  <MediaPickerModal
    :visible="showMediaPicker"
    :media="props.mediaFiles"
    @select="handleMediaSelect"
    @close="showMediaPicker = false"
  />

  <!-- 下载链接媒体选择器弹窗 -->
  <MediaPickerModal
    :visible="showDirectLinkPicker"
    :media="props.mediaFiles"
    @select="handleDirectLinkSelect"
    @close="showDirectLinkPicker = false"
  />
</template>
