<script setup>
/**
 * AdminMedia.vue - 媒体库页面
 * 
 * 功能说明：
 * - 管理上传的媒体文件
 * - 媒体文件列表展示（图片、视频、文档）
 * - 文件搜索和筛选
 * - 上传、预览、删除文件
 * - 批量操作
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Image,
  Video,
  FileText,
  Archive,
  Plus,
  Search,
  Upload,
  Trash2,
  ChevronLeft,
  ChevronRight,
  Filter,
  Eye,
  Download,
  Calendar,
  HardDrive
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const typeFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = 12;

const mediaFiles = ref([
  { id: 1, name: 'architecture-cover.jpg', type: 'image', size: '2.4 MB', url: '', date: '2024-01-15' },
  { id: 2, name: 'design-system.pdf', type: 'document', size: '1.8 MB', url: '', date: '2024-01-16' },
  { id: 3, name: 'project-showcase.mp4', type: 'video', size: '45.2 MB', url: '', date: '2024-01-17' },
  { id: 4, name: 'blueprint-01.png', type: 'image', size: '3.1 MB', url: '', date: '2024-01-18' },
  { id: 5, name: 'tutorial-video.webm', type: 'video', size: '28.7 MB', url: '', date: '2024-01-19' },
  { id: 6, name: 'code-snippet.js', type: 'document', size: '12 KB', url: '', date: '2024-01-20' },
  { id: 7, name: 'render-01.jpg', type: 'image', size: '5.6 MB', url: '', date: '2024-01-21' },
  { id: 8, name: 'model-file.obj', type: 'document', size: '8.9 MB', url: '', date: '2024-01-22' },
  { id: 9, name: 'screenshot-02.png', type: 'image', size: '1.2 MB', url: '', date: '2024-01-23' },
  { id: 10, name: 'presentation.pdf', type: 'document', size: '4.5 MB', url: '', date: '2024-01-24' },
  { id: 11, name: 'demo-video.mp4', type: 'video', size: '67.3 MB', url: '', date: '2024-01-25' },
  { id: 12, name: 'icon-set.svg', type: 'document', size: '156 KB', url: '', date: '2024-01-26' },
  { id: 13, name: 'banner-01.jpg', type: 'image', size: '2.8 MB', url: '', date: '2024-01-27' },
  { id: 14, name: 'documentation.pdf', type: 'document', size: '3.2 MB', url: '', date: '2024-01-28' },
  { id: 15, name: 'animation-01.gif', type: 'image', size: '4.1 MB', url: '', date: '2024-01-29' },
]);

const filteredMedia = computed(() => {
  return mediaFiles.value.filter(file => {
    const matchesSearch = file.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesType = typeFilter.value === 'all' || file.type === typeFilter.value;
    return matchesSearch && matchesType;
  });
});

const totalPages = computed(() => Math.ceil(filteredMedia.value.length / itemsPerPage));

const paginatedMedia = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredMedia.value.slice(start, start + itemsPerPage);
});

const getFileIcon = (type) => {
  switch (type) {
    case 'image': return Image;
    case 'video': return Video;
    case 'document': return FileText;
    default: return Archive;
  }
};

const getFileColor = (type) => {
  switch (type) {
    case 'image': return 'bg-purple-500';
    case 'video': return 'bg-blue-500';
    case 'document': return 'bg-green-500';
    default: return 'bg-gray-500';
  }
};

const selectedFiles = ref([]);
const toggleSelect = (file) => {
  const index = selectedFiles.value.indexOf(file.id);
  if (index > -1) {
    selectedFiles.value.splice(index, 1);
  } else {
    selectedFiles.value.push(file.id);
  }
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <HardDrive class="text-construct-red" size="32" />
          <div>
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_media') }}</h2>
            <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Manage media files</p>
          </div>
        </div>
        <button class="flex items-center gap-2 px-6 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider transition-colors rounded">
          <Upload size="16" class="!text-white" /> {{ t('admin_upload') }}
        </button>
      </div>
    </div>

    <!-- Search and Filter -->
    <div class="flex flex-col md:flex-row gap-4 mb-8">
      <div class="flex-1 relative">
        <Search :class="['absolute left-4 top-1/2 -translate-y-1/2', isDarkMode ? 'text-gray-400' : 'text-gray-500']" size="20" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('admin_search_media')"
          :class="[
            'w-full pl-12 pr-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
        />
      </div>
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
          <Filter :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="18" />
          <select
            v-model="typeFilter"
            :class="[
              'px-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
              isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option value="all">{{ t('admin_all_types') }}</option>
            <option value="image">{{ t('admin_images') }}</option>
            <option value="video">{{ t('admin_videos') }}</option>
            <option value="document">{{ t('admin_documents') }}</option>
          </select>
        </div>
        <button class="flex items-center gap-2 px-4 py-3 border border-gray-700 hover:bg-gray-700 text-white font-bold tracking-wider transition-colors">
          <Plus size="18" /> {{ t('admin_new_folder') }}
        </button>
      </div>
    </div>

    <!-- Media Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
      <div 
        v-for="file in paginatedMedia" 
        :key="file.id"
        :class="[
          'border p-4 hover:border-construct-red transition-colors cursor-pointer group',
          isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
        ]"
        @click="toggleSelect(file)"
      >
        <div class="relative mb-3">
          <div :class="['w-full aspect-square rounded-lg flex items-center justify-center', getFileColor(file.type)]">
            <component :is="getFileIcon(file.type)" size="32" class="text-white" />
          </div>
          <div 
            v-if="selectedFiles.includes(file.id)"
            class="absolute top-2 right-2 w-6 h-6 bg-construct-red rounded flex items-center justify-center"
          >
            <div class="w-3 h-3 bg-white rounded-full"></div>
          </div>
        </div>
        
        <div :class="['text-sm font-medium truncate mb-1', isDarkMode ? 'text-white' : 'text-gray-900']">{{ file.name }}</div>
        <div :class="['flex items-center justify-between text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
          <span>{{ file.size }}</span>
          <span>{{ file.date }}</span>
        </div>
        
        <div :class="['flex items-center gap-2 pt-3 border-t opacity-0 group-hover:opacity-100 transition-opacity', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
          <button :class="['flex-1 flex items-center justify-center gap-1 p-2 transition-colors', isDarkMode ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-100 hover:bg-gray-200']">
            <Eye size="14" /> {{ t('admin_view') }}
          </button>
          <button :class="['flex-1 flex items-center justify-center gap-1 p-2 transition-colors', isDarkMode ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-100 hover:bg-gray-200']">
            <Download size="14" /> {{ t('admin_download') }}
          </button>
          <button :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-red-500/20' : 'text-gray-500 hover:bg-red-50']">
            <Trash2 size="14" />
          </button>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between mt-8">
      <div class="text-sm text-gray-400">
        {{ t('admin_showing') }} {{ ((currentPage - 1) * itemsPerPage) + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredMedia.length) }} {{ t('admin_of') }} {{ filteredMedia.length }} {{ t('admin_files') }}
      </div>
      <div class="flex items-center gap-2">
        <button
          @click="currentPage = Math.max(1, currentPage - 1)"
          :disabled="currentPage === 1"
          class="p-2 border border-gray-700 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <ChevronLeft size="18" />
        </button>
        <span class="px-4 py-2 border border-gray-700">{{ currentPage }} / {{ totalPages }}</span>
        <button
          @click="currentPage = Math.min(totalPages, currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="p-2 border border-gray-700 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <ChevronRight size="18" />
        </button>
      </div>
    </div>
  </div>
</template>
