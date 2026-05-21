<script setup>
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { Plus, Trash2 } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
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
  return isEditMode.value ? t('admin_edit') + ' ' + t('admin_resources') : t('admin_add') + ' ' + t('admin_resources');
});

const resourceFormats = [
  { value: 'PDF', label: 'PDF' },
  { value: 'Image', label: 'Image' },
  { value: 'Video', label: 'Video' },
  { value: 'Archive', label: 'Archive' },
  { value: 'Document', label: 'Document' }
];

const driveTypes = ['Google Drive', 'Dropbox', 'Baidu', 'OneDrive', 'AliCloud', 'Vimeo', 'Local', 'Other'];

const formData = ref({
  title: '',
  category: 'DESIGN',
  image: '',
  format: 'PDF',
  file_size: '',
  downloads_count: '0',
  date: new Date().toISOString().split('T')[0],
  direct_link: '',
  drives: []
});

const initFormData = () => {
  formData.value = {
    title: '',
    category: 'DESIGN',
    image: '',
    format: 'PDF',
    file_size: '',
    downloads_count: '0',
    date: new Date().toISOString().split('T')[0],
    direct_link: '',
    drives: []
  };
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
  formData.value.drives.push({ type: 'Google Drive', url: '' });
};

const removeDrive = (index) => {
  formData.value.drives.splice(index, 1);
};

const handleSave = () => {
  emit('save', { ...formData.value });
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
    </div>
  </ContentFormModal>
</template>
