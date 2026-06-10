<script setup>
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useTheme } from '../../composables/useTheme';
import { Image as ImageIcon, Upload as UploadIcon } from 'lucide-vue-next';
import { useToast } from '../../composables/useToast';
import TagInput from './TagInput.vue';
import ContentFormModal from './ContentFormModal.vue';
import MediaPickerModal from './MediaPickerModal.vue';

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success: toastSuccess, error: toastError } = useToast();

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
  return isEditMode.value ? t('admin_edit') + ' ' + t('admin_posts') : t('admin_add') + ' ' + t('admin_posts');
});

const categoryOptions = computed(() => {
  return props.categories.map(cat => ({
    value: cat.id,
    label: cat.name
  }));
});

const statusOptions = [
  { value: 'draft', label: t('admin_post_status_draft') },
  { value: 'published', label: t('admin_post_status_published') },
  { value: 'scheduled', label: t('admin_post_status_scheduled') },
];

const colorOptions = [
  { value: 'red', label: t('admin_post_form_red') },
  { value: 'black', label: t('admin_post_form_black') }
];

const toDatetimeLocal = (date) => {
  const d = date ? new Date(date) : new Date();
  const pad = n => String(n).padStart(2, '0');
  return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
};

const formData = ref({
  title: '',
  category: '',
  status: 'published',
  excerpt: '',
  content: '',
  color: 'red',
  tags: '',
  thumbnail: '',
  date: toDatetimeLocal()
});

const initFormData = () => {
  formData.value = {
    title: '',
    category: '',
    status: 'published',
    excerpt: '',
    content: '',
    color: 'red',
    tags: '',
    thumbnail: '',
    date: toDatetimeLocal()
  };
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

const handleSave = () => {
  const data = { ...formData.value };
  if (data.tags && typeof data.tags === 'string') {
    data.tags = data.tags.split(',').map(tag => tag.trim()).filter(tag => tag);
  }
  emit('save', data);
};

const handleCancel = () => {
  emit('cancel');
};

// 媒体选择器
const showMediaPicker = ref(false);
const handleMediaSelect = (file) => {
  formData.value.thumbnail = file.url;
  showMediaPicker.value = false;
};

// Markdown 导入
const mdFileInput = ref(null);

const triggerMdImport = () => {
  mdFileInput.value?.click();
};

const handleMdImport = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    const text = e.target.result || '';
    
    let title = file.name.replace(/\.md$/i, '');
    let content = text;
    let excerpt = '';
    let tags = '';
    let date = formData.value.date;

    // 正则表达式匹配 YAML Front Matter
    const frontMatterRegex = /^---\r?\n([\s\S]*?)\r?\n---\r?\n/;
    const match = text.match(frontMatterRegex);

    if (match) {
      const yamlBlock = match[1];
      content = text.substring(match[0].length); // 提取正文内容

      // 简易 YAML 解析
      const lines = yamlBlock.split('\n');
      lines.forEach(line => {
        const index = line.indexOf(':');
        if (index > -1) {
          const key = line.substring(0, index).trim().toLowerCase();
          let val = line.substring(index + 1).trim();
          val = val.replace(/^['"]|['"]$/g, ''); // 去除包围的外层引号

          if (key === 'title') {
            title = val;
          } else if (key === 'excerpt' || key === 'description' || key === 'summary') {
            excerpt = val;
          } else if (key === 'tags') {
            tags = val.replace(/[\[\]]/g, '').split(',').map(t => t.trim()).filter(Boolean).join(', ');
          } else if (key === 'date') {
            try {
              const parsedDate = new Date(val);
              if (!isNaN(parsedDate.getTime())) {
                date = toDatetimeLocal(parsedDate);
              }
            } catch (err) {
              // 忽略解析错误，保留原时间
            }
          }
        }
      });
    }

    // 兜底：如果 yaml 中未定义摘要，提取正文前 150 字并过滤 Markdown 字符作为推荐摘要
    if (!excerpt) {
      excerpt = content
        .replace(/[#*`\-_\[\]\(\)\n]/g, ' ') // 过滤 Markdown 符号
        .replace(/\s+/g, ' ')                // 压缩空白
        .substring(0, 150)
        .trim();
      if (content.length > 150) {
        excerpt += '...';
      }
    }

    // 填充至表单
    formData.value.title = title;
    formData.value.content = content;
    formData.value.excerpt = excerpt;
    if (tags) {
      formData.value.tags = tags;
    }
    formData.value.date = date;

    toastSuccess('Markdown 导入成功，已自动填充表单！');
    
    // 清空选择框以支持重复选择同一文件
    event.target.value = '';
  };

  reader.readAsText(file);
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
          {{ t('admin_post_form_title') }} *
        </label>
        <input
          v-model="formData.title"
          type="text"
          :class="[
            'w-full px-4 py-2 text-sm rounded-xl border focus:outline-none focus:border-construct-red',
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
              'w-full px-4 py-2 text-sm rounded-xl border focus:outline-none focus:border-construct-red',
              isDarkMode 
                ? 'bg-gray-700 border-gray-600 text-white' 
                : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option value="" disabled>{{ t('admin_select_category') }}</option>
            <option v-for="cat in categoryOptions" :key="cat.value" :value="cat.value">
              {{ cat.label }}
            </option>
          </select>
        </div>

        <div>
          <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            {{ t('admin_post_status') }}
          </label>
          <select
            v-model="formData.status"
            :class="[
              'w-full px-4 py-2 text-sm rounded-xl border focus:outline-none focus:border-construct-red',
              isDarkMode 
                ? 'bg-gray-700 border-gray-600 text-white' 
                : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option v-for="s in statusOptions" :key="s.value" :value="s.value">
              {{ s.label }}
            </option>
          </select>
        </div>
      </div>

      <div>
        <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          {{ t('admin_post_form_date') }}
        </label>
        <input
          v-model="formData.date"
          type="datetime-local"
          :class="[
            'w-full px-4 py-2 text-sm rounded-xl border focus:outline-none focus:border-construct-red',
            isDarkMode 
              ? 'bg-gray-700 border-gray-600 text-white [color-scheme:dark]' 
              : 'bg-white border-gray-300 text-gray-900'
          ]"
        />
      </div>

      <div>
        <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          {{ t('admin_post_form_thumbnail') }}
        </label>
        <div class="flex gap-2">
          <input
            v-model="formData.thumbnail"
            type="text"
            :class="[
              'flex-1 px-4 py-2 text-sm rounded-xl border focus:outline-none focus:border-construct-red',
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
            {{ t('admin_select_media') }}
          </button>
        </div>
        <div v-if="formData.thumbnail" class="mt-2">
          <img :src="formData.thumbnail" alt="预览" class="max-h-32 rounded-lg border object-contain" />
        </div>
      </div>

      <div>
        <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
          {{ t('admin_post_form_excerpt') }} *
        </label>
        <textarea
          v-model="formData.excerpt"
          rows="2"
          :class="[
            'w-full px-4 py-2 text-sm rounded-xl border focus:outline-none focus:border-construct-red resize-none',
            isDarkMode 
              ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' 
              : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
          placeholder="输入文章摘要..."
        ></textarea>
      </div>

      <div>
        <div class="flex items-center justify-between mb-2">
          <label :class="['block text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            {{ t('admin_post_form_content') }} *
          </label>
          <button
            type="button"
            @click="triggerMdImport"
            class="flex items-center gap-1.5 text-xs font-bold text-construct-red hover:underline focus:outline-none cursor-pointer"
          >
            <UploadIcon size="14" />
            导入 Markdown
          </button>
          <input
            ref="mdFileInput"
            type="file"
            accept=".md"
            class="hidden"
            @change="handleMdImport"
          />
        </div>
        <textarea
          v-model="formData.content"
          rows="10"
          :class="[
            'w-full px-4 py-2 text-sm rounded-xl border focus:outline-none focus:border-construct-red resize-none font-mono',
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
              'w-full px-4 py-2 text-sm rounded-xl border focus:outline-none focus:border-construct-red',
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

    </div>
  </ContentFormModal>

  <!-- 缩略图媒体选择器弹窗 -->
  <MediaPickerModal
    :visible="showMediaPicker"
    :media="props.mediaFiles"
    @select="handleMediaSelect"
    @close="showMediaPicker = false"
  />
</template>
