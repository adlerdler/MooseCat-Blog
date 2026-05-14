<script setup>
/**
 * AdminResources.vue - 资源管理页面
 * 
 * 功能说明：
 * - 管理系统中的所有资源文件
 * - 支持搜索和类型筛选功能
 * - 支持资源的增删改查操作
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  BookOpen,
  Plus,
  Search,
  Edit3,
  Trash2,
  Eye,
  Clock,
  ChevronLeft,
  ChevronRight,
  Filter,
  Download,
  File,
  FileText,
  Image,
  Video,
  Archive
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { formatToShort } from '../../utils/dateUtils';
import { resourcesData, resourceTypes } from '../../data/resources';
import ContentForm from '../../components/admin/ContentForm.vue';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 12;
const selectedType = ref('all');
const isFormVisible = ref(false);
const editingResource = ref(null);
const showDeleteConfirm = ref(false);
const deletingResourceId = ref(null);

const resources = ref([...resourcesData]);

const getTypeIcon = (format) => {
  switch (format) {
    case 'PDF': return FileText;
    case 'Image': return Image;
    case 'Video': return Video;
    case 'Archive': return Archive;
    default: return File;
  }
};

const getTypeColor = (format) => {
  if (isDarkMode.value) {
    switch (format) {
      case 'PDF': return 'bg-red-900 text-red-300';
      case 'Image': return 'bg-purple-900 text-purple-300';
      case 'Video': return 'bg-blue-900 text-blue-300';
      case 'Archive': return 'bg-yellow-900 text-yellow-300';
      default: return 'bg-gray-700 text-gray-300';
    }
  } else {
    switch (format) {
      case 'PDF': return 'bg-red-100 text-red-700';
      case 'Image': return 'bg-purple-100 text-purple-700';
      case 'Video': return 'bg-blue-100 text-blue-700';
      case 'Archive': return 'bg-yellow-100 text-yellow-700';
      default: return 'bg-gray-200 text-gray-700';
    }
  }
};

const filteredResources = computed(() => {
  let result = [...resources.value];
  
  if (selectedType.value !== 'all') {
    result = result.filter(resource => resource.format === selectedType.value);
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(resource =>
      resource.title.toLowerCase().includes(query)
    );
  }
  
  return result;
});

const paginatedResources = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredResources.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredResources.value.length / itemsPerPage);
});

const handleDelete = (id) => {
  deletingResourceId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingResourceId.value !== null) {
    console.log('Delete resource:', deletingResourceId.value);
    deletingResourceId.value = null;
  }
  showDeleteConfirm.value = false;
};

const handleEdit = (resource) => {
  editingResource.value = {
    id: resource.id,
    title: resource.title,
    description: resource.description || '',
    thumbnail: resource.thumbnail || '',
    url: resource.url || '',
    date: resource.date,
    status: 'published'
  };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingResource.value = null;
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (editingResource.value) {
    console.log('Update resource:', data);
  } else {
    console.log('Create resource:', data);
  }
  isFormVisible.value = false;
  editingResource.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingResource.value = null;
};

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <h2 :class="['font-display text-4xl tracking-tighter mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_resources') }}</h2>
      <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_resources_subtitle') }}</p>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row gap-4 mb-6">
      <!-- Search -->
      <div class="relative flex-1">
        <Search :class="['absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5', isDarkMode ? 'text-gray-500' : 'text-gray-400']" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('admin_search_placeholder')"
          :class="[
            'w-full pl-10 pr-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-500' : 'bg-white border-gray-200 text-gray-900 placeholder-gray-400'
          ]"
        />
      </div>
      
      <!-- Type Filter -->
      <div class="relative">
        <Filter :class="['absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5', isDarkMode ? 'text-gray-500' : 'text-gray-400']" />
        <select
          v-model="selectedType"
          :class="[
            'pl-10 pr-8 py-3 border focus:border-construct-red focus:outline-none appearance-none cursor-pointer min-w-[150px]',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-200 text-gray-900'
          ]"
        >
          <option v-for="type in resourceTypes" :key="type" :value="type">
            {{ type === 'all' ? t('admin_filter_all') : type }}
          </option>
        </select>
      </div>
      
      <!-- Add Button -->
      <button
        @click="handleAdd"
        class="flex items-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-700 transition-colors rounded"
      >
        <Plus size="16" class="!text-white" />
        {{ t('admin_add_new') }}
      </button>
    </div>

    <!-- Resources Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="resource in paginatedResources"
        :key="resource.id"
        :class="[
          'border overflow-hidden hover:border-construct-red transition-colors',
          isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <!-- Resource Icon -->
        <div :class="['relative h-32 flex items-center justify-center', isDarkMode ? 'bg-gray-900' : 'bg-gray-100']">
          <component
            :is="getTypeIcon(resource.format)"
            :class="['w-16 h-16', 
              resource.format === 'PDF' ? (isDarkMode ? 'text-red-400' : 'text-red-500') :
              resource.format === 'Image' ? (isDarkMode ? 'text-purple-400' : 'text-purple-500') :
              resource.format === 'Video' ? (isDarkMode ? 'text-blue-400' : 'text-blue-500') :
              resource.format === 'Archive' ? (isDarkMode ? 'text-yellow-400' : 'text-yellow-500') : 
              (isDarkMode ? 'text-gray-400' : 'text-gray-500')]"
          />
          <div class="absolute top-2 right-2">
            <span
              :class="['text-[10px] px-2 py-1 uppercase font-bold', getTypeColor(resource.format)]"
            >
              {{ resource.format }}
            </span>
          </div>
        </div>
        
        <!-- Resource Info -->
        <div class="p-4">
          <h3 :class="['font-bold mb-2 line-clamp-2 text-sm', isDarkMode ? 'text-white' : 'text-gray-900']">{{ resource.title }}</h3>
          
          <div :class="['flex items-center justify-between text-xs mb-3', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
            <span>{{ resource.fileSize }}</span>
            <span class="flex items-center gap-1">
              <Download size="14" />
              {{ resource.downloadCount }}
            </span>
          </div>
          
          <div :class="['flex items-center justify-between pt-3 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <div :class="['flex items-center gap-2 text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              <Clock size="14" />
              {{ formatToShort(resource.date) }}
            </div>
            
            <div class="flex gap-1">
              <button
                :class="['p-1.5 transition-colors', isDarkMode ? 'text-gray-400 hover:text-green-400 hover:bg-gray-700' : 'text-gray-500 hover:text-green-500 hover:bg-gray-100']"
                :title="t('admin_download')"
              >
                <Download size="16" />
              </button>
              <button
                @click="handleEdit(resource)"
                :class="['p-1.5 transition-colors', isDarkMode ? 'text-gray-400 hover:text-blue-400 hover:bg-gray-700' : 'text-gray-500 hover:text-blue-500 hover:bg-gray-100']"
                :title="t('admin_edit')"
              >
                <Edit3 size="16" />
              </button>
              <button
                @click="handleDelete(resource.id)"
                :class="['p-1.5 transition-colors', isDarkMode ? 'text-gray-400 hover:text-red-400 hover:bg-gray-700' : 'text-gray-500 hover:text-red-500 hover:bg-gray-100']"
                :title="t('admin_delete')"
              >
                <Trash2 size="16" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between mt-8">
      <div class="text-sm text-gray-400">
        {{ t('admin_showing') }} {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredResources.length) }} {{ t('admin_of') }} {{ filteredResources.length }}
      </div>
      <div class="flex gap-2">
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
          class="p-2 border border-gray-700 text-gray-400 hover:text-white hover:border-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <ChevronLeft size="18" />
        </button>
        <button
          v-for="page in totalPages"
          :key="page"
          @click="goToPage(page)"
          :class="[
            'px-4 py-2 border text-sm font-bold transition-colors',
            page === currentPage
              ? 'border-construct-red text-construct-red bg-gray-800'
              : 'border-gray-700 text-gray-400 hover:text-white hover:border-gray-500'
          ]"
        >
          {{ page }}
        </button>
        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="p-2 border border-gray-700 text-gray-400 hover:text-white hover:border-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <ChevronRight size="18" />
        </button>
      </div>
    </div>

    <!-- Content Form Modal -->
    <ContentForm
      content-type="resource"
      :edit-data="editingResource"
      :visible="isFormVisible"
      @save="handleSave"
      @cancel="handleCancel"
    />

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      title="确认删除"
      content="确定要删除这个资源吗？此操作不可撤销。"
      confirm-text="删除"
      confirm-variant="danger"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>
