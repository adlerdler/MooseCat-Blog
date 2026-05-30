<script setup>
/**
 * Media Library Page
 * 
 * Features:
 * - Grid and list view modes
 * - File preview functionality
 * - File upload support
 * - Search and filter by type
 * - File deletion with confirmation
 * - Bulk selection support
 */
import {
  ref,
  computed,
  useI18n,
  useTheme,
  formatToShort,
  Image as ImageIcon,
  Video as VideoIcon,
  FileText as FileIcon,
  Archive,
  Plus,
  Search,
  Upload,
  Trash2,
  Filter,
  Eye,
  Download,
  Calendar,
  HardDrive,
  LayoutGrid,
  List,
  X,
  Pagination,
  MediaPreviewModal,
  MediaUploadModal,
  SearchFilterModal,
  ConfirmDialog,
  useToast
} from '../../composables/useAdminImports';
import { Motion, AnimatePresence } from 'motion-v';
import axios from 'axios';

const props = defineProps({
  media: { type: Array, default: () => [] },
});

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success } = useToast();

const searchQuery = ref('');
const typeFilter = ref('all');
const viewMode = ref('grid'); // 'grid' or 'list'
const currentPage = ref(1);
const itemsPerPage = ref(12);
const showPreview = ref(false);
const previewFile = ref(null);
const showUpload = ref(false);
const showDeleteConfirm = ref(false);
const deletingFile = ref(null);

const handlePreview = (file) => {
  previewFile.value = file;
  showPreview.value = true;
};

const handleUploadSuccess = (newFiles) => {
  // Refresh page to get latest data from server
  window.location.reload();
};

const mediaFiles = computed(() => {
  const data = props.media?.data || props.media || [];
  return Array.isArray(data) ? [...data] : [];
});

const filteredMedia = computed(() => {
  return mediaFiles.value.filter(file => {
    const name = file.name || file.title || '';
    const matchesSearch = name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesType = typeFilter.value === 'all' || file.type === typeFilter.value;
    return matchesSearch && matchesType;
  });
});

const totalPages = computed(() => Math.ceil(filteredMedia.value.length / itemsPerPage.value));

const paginatedMedia = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredMedia.value.slice(start, start + itemsPerPage.value);
});

const getFileIcon = (type) => {
  switch (type) {
    case 'image': return ImageIcon;
    case 'video': return VideoIcon;
    case 'document': return FileIcon;
    default: return Archive;
  }
};

const getFileColor = (type) => {
  switch (type) {
    case 'image': return isDarkMode.value ? 'text-purple-400 bg-purple-900/20' : 'text-purple-500 bg-purple-50';
    case 'video': return isDarkMode.value ? 'text-blue-400 bg-blue-900/20' : 'text-blue-500 bg-blue-50';
    case 'document': return isDarkMode.value ? 'text-green-400 bg-green-900/20' : 'text-green-500 bg-green-50';
    default: return isDarkMode.value ? 'text-gray-400 bg-gray-700/20' : 'text-gray-500 bg-gray-50';
  }
};

const handleFilterChange = ({ key, value }) => {
  if (key === 'type') {
    typeFilter.value = value;
  }
  currentPage.value = 1;
};

const handleDelete = (file) => {
  deletingFile.value = file;
  showDeleteConfirm.value = true;
};

const handleDownload = (file) => {
  if (!file.url) return;
  const link = document.createElement('a');
  link.href = file.url;
  link.download = file.name || 'download';
  link.target = '_blank';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const confirmDelete = async () => {
  if (!deletingFile.value) return;
  
  try {
    await axios.delete(`/admin/media/${deletingFile.value.id}`);
    window.location.reload();
  } catch (error) {
    console.error('Delete failed:', error);
  } finally {
    showDeleteConfirm.value = false;
    deletingFile.value = null;
  }
};
</script>

<template>
  <div class="flex h-full overflow-hidden relative">
    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 transition-colors duration-300 overflow-y-auto">
      <div class="p-8">
        <!-- Page Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
          <div>
            <h2 :class="['font-display text-4xl tracking-tighter mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">
              {{ t('admin_media') }}
            </h2>
            <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ filteredMedia.length }} {{ t('admin_files') }}
            </p>
          </div>
          
          <div class="flex items-center gap-2">
            <div class="flex border rounded overflow-hidden mr-2" :class="isDarkMode ? 'border-gray-700' : 'border-gray-300'">
              <button 
                :class="['p-2 transition-colors', viewMode === 'grid' ? (isDarkMode ? 'bg-gray-700 text-white' : 'bg-gray-100 text-gray-900') : (isDarkMode ? 'text-gray-500' : 'text-gray-400')]" 
                @click="viewMode = 'grid'"
              >
                <LayoutGrid size="18" />
              </button>
              <button 
                :class="['p-2 transition-colors', viewMode === 'list' ? (isDarkMode ? 'bg-gray-700 text-white' : 'bg-gray-100 text-gray-900') : (isDarkMode ? 'text-gray-500' : 'text-gray-400')]" 
                @click="viewMode = 'list'"
              >
                <List size="18" />
              </button>
            </div>
            <button 
              @click="showUpload = true"
              class="flex items-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-700 transition-colors rounded shadow-sm"
            >
              <Upload size="16" class="!text-white" /> {{ t('admin_upload') }}
            </button>
          </div>
        </div>

        <!-- Search and Filter -->
        <SearchFilterModal
          v-model:search-query="searchQuery"
          :search-placeholder="t('admin_search_media')"
          :filters="[
            {
              key: 'type',
              options: [
                { value: 'all', label: t('admin_all_types') },
                { value: 'image', label: t('admin_images') },
                { value: 'video', label: t('admin_videos') },
                { value: 'document', label: t('admin_documents') }
              ]
            }
          ]"
          :filter-values="{ type: typeFilter }"
          @filter-change="handleFilterChange"
        />

        <!-- Media Grid -->
        <div v-if="viewMode === 'grid'" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
          <AnimatePresence>
            <Motion
              v-for="file in paginatedMedia"
              :key="file.id"
              :initial="{ opacity: 0, scale: 0.95 }"
              :animate="{ opacity: 1, scale: 1 }"
              :exit="{ opacity: 0, scale: 0.95 }"
              :class="[
                'relative group cursor-pointer border rounded-lg transition-all',
                isDarkMode ? 'bg-gray-800 border-gray-700 hover:border-gray-600' : 'bg-white border-gray-200 hover:border-gray-300'
              ]"
            >
              <!-- Preview Area -->
              <div class="aspect-square relative overflow-hidden rounded-t-lg bg-gray-50 flex items-center justify-center border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
                <template v-if="file.type === 'image' && file.url">
                  <img :src="file.url" :alt="file.name" class="w-full h-full object-cover" />
                </template>
                <div v-else :class="['w-full h-full flex items-center justify-center rounded-t-lg', getFileColor(file.type)]">
                  <component :is="getFileIcon(file.type)" size="40" />
                </div>
                
                <!-- Quick Actions Overlay -->
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 z-10">
                  <button 
                    @click.stop="handlePreview(file)"
                    class="p-2 bg-white text-gray-900 rounded-full hover:bg-construct-red hover:text-white transition-colors"
                  >
                    <Eye size="16" />
                  </button>
                  <button 
                    @click.stop="handleDownload(file)"
                    class="p-2 bg-white text-gray-900 rounded-full hover:bg-construct-red hover:text-white transition-colors"
                  >
                    <Download size="16" />
                  </button>
                  <button 
                    @click.stop="handleDelete(file)"
                    class="p-2 bg-white text-gray-900 rounded-full hover:bg-red-500 hover:text-white transition-colors"
                  >
                    <Trash2 size="16" />
                  </button>
                </div>
              </div>

              <!-- Info Area -->
              <div class="p-3">
                <div :class="['text-sm font-bold truncate mb-1', isDarkMode ? 'text-white' : 'text-gray-900']">
                  {{ file.name }}
                </div>
                <div :class="['flex items-center justify-between text-[11px] font-medium opacity-60', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                  <span>{{ file.size }}</span>
                  <span>{{ formatToShort(file.date) }}</span>
                </div>
              </div>
            </Motion>
          </AnimatePresence>
        </div>

        <!-- Media List View -->
        <div v-else :class="['border rounded-lg overflow-hidden', isDarkMode ? 'border-gray-700 bg-gray-800' : 'border-gray-200 bg-white']">
          <table class="w-full text-left">
            <thead :class="['border-b', isDarkMode ? 'border-gray-700 bg-gray-900/50 text-gray-400' : 'border-gray-200 bg-gray-50 text-gray-500']">
              <tr>
                <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest">Name</th>
                <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest">Type</th>
                <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest">Size</th>
                <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest">Date</th>
                <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest text-right">Actions</th>
              </tr>
            </thead>
            <tbody :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              <tr 
                v-for="file in paginatedMedia" 
                :key="file.id"
                :class="['border-b transition-colors cursor-pointer', isDarkMode ? 'border-gray-700 hover:bg-gray-700/50' : 'border-gray-100 hover:bg-gray-50']"
              >
                <td class="px-6 py-3">
                  <div class="flex items-center gap-3">
                    <component :is="getFileIcon(file.type)" size="18" :class="['p-1 rounded', getFileColor(file.type)]" />
                    <span class="font-medium text-sm">{{ file.name }}</span>
                  </div>
                </td>
                <td class="px-6 py-3 text-xs font-bold uppercase opacity-50">{{ file.type }}</td>
                <td class="px-6 py-3 text-xs font-bold opacity-50">{{ file.size }}</td>
                <td class="px-6 py-3 text-xs font-bold opacity-50">{{ formatToShort(file.date) }}</td>
                <td class="px-6 py-3 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button 
                      @click.stop="handlePreview(file)"
                      class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                    >
                      <Eye size="14" />
                    </button>
                    <button class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"><Download size="14" /></button>
                    <button 
                      @click.stop="handleDelete(file)"
                      class="p-2 hover:bg-red-100 text-red-500 rounded transition-colors"
                    >
                      <Trash2 size="14" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
          <Pagination
            :current-page="currentPage"
            :total-pages="totalPages"
            :total-items="filteredMedia.length"
            v-model:items-per-page="itemsPerPage"
            @update:current-page="(page) => currentPage = page"
          />
        </div>
      </div>
    </div>

    <!-- Media Preview Modal -->
    <MediaPreviewModal
      :visible="showPreview"
      :file="previewFile"
      @close="showPreview = false"
      @delete="(file) => handleDelete(file)"
      @download="(file) => handleDownload(file)"
    />

    <!-- Media Upload Modal -->
    <MediaUploadModal
      :visible="showUpload"
      @close="showUpload = false"
      @uploaded="handleUploadSuccess"
    />

    <!-- Delete Confirmation Dialog -->
    <ConfirmDialog
      v-model:visible="showDeleteConfirm"
      type="delete"
      :confirm-text="t('admin_delete')"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>

<style scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}
</style>
