<script setup>
/**
 * AdminMedia.vue - 媒体库页面 (优化版 - 后台风格一致)
 * 
 * 功能说明：
 * - 支持网格(Grid)和列表(List)视图切换
 * - 图片文件实时预览
 * - 文件详细信息侧边栏
 * - 风格与后台管理系统统一
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
  Info,
  X,
  AdminPagination,
  MediaPreviewModal
} from '../../composables/useAdminImports';
import { adminMedia } from '../../data/media';
import { Motion, AnimatePresence } from 'motion-v';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const typeFilter = ref('all');
const viewMode = ref('grid'); // 'grid' or 'list'
const currentPage = ref(1);
const itemsPerPage = ref(12);
const selectedFileId = ref(null);
const showPreview = ref(false);
const previewFile = ref(null);

const handlePreview = (file) => {
  previewFile.value = file;
  showPreview.value = true;
};

const mediaFiles = ref([...adminMedia]);

const filteredMedia = computed(() => {
  return mediaFiles.value.filter(file => {
    const matchesSearch = file.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesType = typeFilter.value === 'all' || file.type === typeFilter.value;
    return matchesSearch && matchesType;
  });
});

const totalPages = computed(() => Math.ceil(filteredMedia.value.length / itemsPerPage.value));

const paginatedMedia = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredMedia.value.slice(start, start + itemsPerPage.value);
});

const selectedFile = computed(() => {
  return mediaFiles.value.find(f => f.id === selectedFileId.value) || null;
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

const handleFileClick = (file) => {
  selectedFileId.value = selectedFileId.value === file.id ? null : file.id;
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
            <button class="flex items-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-700 transition-colors rounded shadow-sm">
              <Upload size="16" class="!text-white" /> {{ t('admin_upload') }}
            </button>
          </div>
        </div>

        <!-- Search and Filter -->
        <div class="flex flex-col lg:flex-row gap-4 mb-8">
          <div class="flex-1 relative">
            <Search :class="['absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5', isDarkMode ? 'text-gray-500' : 'text-gray-400']" />
            <input
              v-model="searchQuery"
              type="text"
              :placeholder="t('admin_search_media')"
              :class="[
                'w-full pl-10 pr-4 py-3 border focus:border-construct-red focus:outline-none transition-colors rounded',
                isDarkMode ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
              ]"
            />
          </div>
          <div class="flex items-center gap-4">
            <div class="relative">
               <Filter :class="['absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4', isDarkMode ? 'text-gray-500' : 'text-gray-400']" />
              <select
                v-model="typeFilter"
                :class="[
                  'pl-9 pr-8 py-3 border focus:border-construct-red focus:outline-none appearance-none cursor-pointer rounded text-sm font-bold',
                  isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
              >
                <option value="all">{{ t('admin_all_types') }}</option>
                <option value="image">{{ t('admin_images') }}</option>
                <option value="video">{{ t('admin_videos') }}</option>
                <option value="document">{{ t('admin_documents') }}</option>
              </select>
            </div>
            <button :class="['px-6 py-3 border flex items-center gap-2 font-bold text-sm tracking-wider uppercase transition-colors rounded hover:bg-gray-100', isDarkMode ? 'border-gray-700 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50']">
              <Plus size="16" /> {{ t('admin_new_folder') }}
            </button>
          </div>
        </div>

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
                selectedFileId === file.id ? 'border-construct-red ring-2 ring-construct-red/20' : (isDarkMode ? 'bg-gray-800 border-gray-700 hover:border-gray-600' : 'bg-white border-gray-200 hover:border-gray-300')
              ]"
              @click="handleFileClick(file)"
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
                  <button class="p-2 bg-white text-gray-900 rounded-full hover:bg-construct-red hover:text-white transition-colors">
                    <Download size="16" />
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
                :class="['border-b transition-colors cursor-pointer', isDarkMode ? 'border-gray-700 hover:bg-gray-700/50' : 'border-gray-100 hover:bg-gray-50', selectedFileId === file.id ? (isDarkMode ? 'bg-red-900/20' : 'bg-red-50') : '']"
                @click="handleFileClick(file)"
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
                    <button class="p-2 hover:bg-red-100 text-red-500 rounded transition-colors"><Trash2 size="14" /></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
          <AdminPagination
            :current-page="currentPage"
            :total-pages="totalPages"
            :total-items="filteredMedia.length"
            v-model:items-per-page="itemsPerPage"
            @update:current-page="(page) => currentPage = page"
          />
        </div>
      </div>
    </div>

    <!-- Side Info Panel -->
    <AnimatePresence>
      <Motion
        v-if="selectedFile"
        :initial="{ x: '100%' }"
        :animate="{ x: 0 }"
        :exit="{ x: '100%' }"
        :transition="{ type: 'spring', damping: 25, stiffness: 200 }"
        :class="['absolute top-0 right-0 h-full w-[380px] border-l shadow-2xl flex flex-col z-30', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']"
      >
        <div :class="['p-6 border-b flex items-center justify-between', isDarkMode ? 'border-gray-700 bg-gray-900/50' : 'border-gray-200 bg-gray-50']">
          <div class="flex items-center gap-2">
            <Info size="18" class="text-construct-red" />
            <span class="font-bold text-sm uppercase tracking-wider">{{ t('admin_media') }} {{ t('admin_view') }}</span>
          </div>
          <button @click="selectedFileId = null" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full transition-colors">
            <X size="20" />
          </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
          <!-- Preview -->
          <div class="aspect-video bg-gray-50 dark:bg-gray-900 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 mb-6 flex items-center justify-center overflow-hidden">
            <template v-if="selectedFile.type === 'image' && selectedFile.url">
              <img :src="selectedFile.url" class="w-full h-full object-cover" />
            </template>
            <component v-else :is="getFileIcon(selectedFile.type)" size="60" :class="getFileColor(selectedFile.type)" />
          </div>

          <!-- Meta Info -->
          <div class="space-y-6">
            <div>
              <label class="text-[10px] font-black tracking-widest text-gray-400 uppercase block mb-1">Filename</label>
              <div class="font-bold text-base break-all" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ selectedFile.name }}</div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-[10px] font-black tracking-widest text-gray-400 uppercase block mb-1">Type</label>
                <div class="font-bold text-sm uppercase" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ selectedFile.type }}</div>
              </div>
              <div>
                <label class="text-[10px] font-black tracking-widest text-gray-400 uppercase block mb-1">Size</label>
                <div class="font-bold text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ selectedFile.size }}</div>
              </div>
            </div>

            <div>
              <label class="text-[10px] font-black tracking-widest text-gray-400 uppercase block mb-1">Uploaded At</label>
              <div class="flex items-center gap-2 font-bold text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                <Calendar size="14" class="text-construct-red" />
                {{ selectedFile.date }}
              </div>
            </div>

            <div>
              <label class="text-[10px] font-black tracking-widest text-gray-400 uppercase block mb-1">File URL</label>
              <div class="p-3 bg-gray-50 dark:bg-gray-900 border rounded text-[11px] font-mono break-all cursor-pointer hover:border-construct-red transition-colors" :class="isDarkMode ? 'border-gray-700 text-gray-400' : 'border-gray-200 text-gray-600'">
                {{ selectedFile.url || '/storage/media/' + selectedFile.name }}
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="p-6 border-t flex gap-3" :class="isDarkMode ? 'border-gray-700 bg-gray-900/50' : 'border-gray-200 bg-gray-50'">
          <button class="flex-1 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-xs hover:bg-red-700 transition-colors rounded flex items-center justify-center gap-2">
            <Download size="16" class="!text-white" /> Download
          </button>
          <button class="p-3 border text-red-500 hover:bg-red-50 transition-colors rounded" :class="isDarkMode ? 'border-gray-700 hover:bg-red-900/20' : 'border-gray-300'">
            <Trash2 size="18" />
          </button>
        </div>
      </Motion>
    </AnimatePresence>

    <!-- Media Preview Modal -->
    <MediaPreviewModal
      :visible="showPreview"
      :file="previewFile"
      @close="showPreview = false"
      @delete="(file) => console.log('Delete file:', file)"
      @download="(file) => console.log('Download file:', file)"
    />
  </div>
</template>

<style scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}
</style>
