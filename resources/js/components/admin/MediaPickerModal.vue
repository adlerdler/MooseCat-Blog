<script setup>
/**
 * MediaPickerModal.vue - 媒体选择器弹窗
 * 
 * 功能说明：
 * - 从媒体库中选择文件
 * - 支持网格和列表视图
 * - 搜索和筛选功能
 * - 选择后返回文件URL
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useTheme } from '../../composables/useTheme';
import {
  X,
  Search,
  Image as ImageIcon,
  Video as VideoIcon,
  FileText as FileIcon,
  Archive,
  LayoutGrid,
  List,
  Check
} from 'lucide-vue-next';
import { adminMedia } from '../../data/media';

const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close', 'select']);

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const typeFilter = ref('all');
const viewMode = ref('grid');
const selectedId = ref(null);

const mediaFiles = ref([...adminMedia]);

const filteredMedia = computed(() => {
  return mediaFiles.value.filter(file => {
    const matchesSearch = file.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesType = typeFilter.value === 'all' || file.type === typeFilter.value;
    return matchesSearch && matchesType;
  });
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
    case 'image': return 'bg-blue-500/10 text-blue-500';
    case 'video': return 'bg-purple-500/10 text-purple-500';
    case 'document': return 'bg-green-500/10 text-green-500';
    default: return 'bg-orange-500/10 text-orange-500';
  }
};

const handleSelect = (file) => {
  selectedId.value = file.id;
  emit('select', file);
  emit('close');
};

const handleClose = () => {
  selectedId.value = null;
  searchQuery.value = '';
  typeFilter.value = 'all';
  emit('close');
};
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="visible" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="handleClose" />

        <!-- Modal -->
        <div 
          :class="[
            'relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh] transition-all transform',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]"
          @click.stop
        >
          <!-- Header -->
          <div :class="['p-6 border-b flex items-center justify-between', isDarkMode ? 'border-gray-700 bg-gray-900/50' : 'border-gray-100 bg-gray-50/50']">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-construct-red flex items-center justify-center shadow-lg shadow-construct-red/20">
                <ImageIcon class="text-white" size="20" />
              </div>
              <div>
                <h3 :class="['text-lg font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_select_media') }}</h3>
                <p :class="['text-xs font-medium', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_select_media_subtitle') }}</p>
              </div>
            </div>
            <button 
              @click="handleClose" 
              class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
            >
              <X size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>

          <!-- Toolbar -->
          <div :class="['p-4 border-b flex items-center gap-4 flex-wrap', isDarkMode ? 'border-gray-700' : 'border-gray-100']">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
              <Search size="16" :class="['absolute left-3 top-1/2 -translate-y-1/2', isDarkMode ? 'text-gray-500' : 'text-gray-400']" />
              <input
                v-model="searchQuery"
                type="text"
                :placeholder="t('admin_search')"
                :class="[
                  'w-full pl-10 pr-4 py-2 border rounded-lg focus:border-construct-red focus:outline-none transition-all text-sm',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'
                ]"
              />
            </div>

            <!-- Type Filter -->
            <select
              v-model="typeFilter"
              :class="[
                'px-4 py-2 border rounded-lg focus:border-construct-red focus:outline-none transition-all text-sm',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            >
              <option value="all">{{ t('admin_all_types') }}</option>
              <option value="image">{{ t('admin_type_image') }}</option>
              <option value="video">{{ t('admin_type_video') }}</option>
              <option value="document">{{ t('admin_type_document') }}</option>
            </select>

            <!-- View Toggle -->
            <div :class="['flex border rounded-lg overflow-hidden', isDarkMode ? 'border-gray-600' : 'border-gray-300']">
              <button
                @click="viewMode = 'grid'"
                :class="[
                  'p-2 transition-colors',
                  viewMode === 'grid' ? 'bg-construct-red text-white' : (isDarkMode ? 'bg-gray-700 text-gray-400 hover:bg-gray-600' : 'bg-white text-gray-500 hover:bg-gray-100')
                ]"
              >
                <LayoutGrid size="16" />
              </button>
              <button
                @click="viewMode = 'list'"
                :class="[
                  'p-2 transition-colors',
                  viewMode === 'list' ? 'bg-construct-red text-white' : (isDarkMode ? 'bg-gray-700 text-gray-400 hover:bg-gray-600' : 'bg-white text-gray-500 hover:bg-gray-100')
                ]"
              >
                <List size="16" />
              </button>
            </div>
          </div>

          <!-- Content -->
          <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
            <!-- Grid View -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
              <div
                v-for="file in filteredMedia"
                :key="file.id"
                @click="handleSelect(file)"
                :class="[
                  'relative border rounded-xl overflow-hidden cursor-pointer transition-all group',
                  selectedId === file.id 
                    ? 'border-construct-red ring-2 ring-construct-red/20' 
                    : (isDarkMode ? 'border-gray-700 hover:border-gray-600' : 'border-gray-200 hover:border-gray-300')
                ]"
              >
                <!-- Preview -->
                <div :class="['aspect-square flex items-center justify-center', isDarkMode ? 'bg-gray-900' : 'bg-gray-50']">
                  <img
                    v-if="file.url && file.type === 'image'"
                    :src="file.url"
                    :alt="file.name"
                    class="w-full h-full object-cover"
                  />
                  <div v-else :class="['w-16 h-16 rounded-full flex items-center justify-center', getFileColor(file.type)]">
                    <component :is="getFileIcon(file.type)" size="32" />
                  </div>
                </div>

                <!-- Info -->
                <div :class="['p-3', isDarkMode ? 'bg-gray-800' : 'bg-white']">
                  <p :class="['text-sm font-bold truncate', isDarkMode ? 'text-white' : 'text-gray-900']">{{ file.name }}</p>
                  <p :class="['text-xs mt-1', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ file.size }} · {{ file.date }}</p>
                </div>

                <!-- Selected Badge -->
                <div
                  v-if="selectedId === file.id"
                  class="absolute top-2 right-2 w-6 h-6 rounded-full bg-construct-red flex items-center justify-center"
                >
                  <Check size="14" class="text-white" />
                </div>
              </div>
            </div>

            <!-- List View -->
            <div v-else class="space-y-2">
              <div
                v-for="file in filteredMedia"
                :key="file.id"
                @click="handleSelect(file)"
                :class="[
                  'flex items-center gap-4 p-4 border rounded-xl cursor-pointer transition-all',
                  selectedId === file.id 
                    ? 'border-construct-red bg-construct-red/5' 
                    : (isDarkMode ? 'border-gray-700 hover:bg-gray-700/50' : 'border-gray-200 hover:bg-gray-50')
                ]"
              >
                <!-- Icon/Preview -->
                <div :class="['w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
                  <img
                    v-if="file.url && file.type === 'image'"
                    :src="file.url"
                    :alt="file.name"
                    class="w-full h-full object-cover rounded-lg"
                  />
                  <component v-else :is="getFileIcon(file.type)" size="24" :class="getFileColor(file.type)" />
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                  <p :class="['text-sm font-bold truncate', isDarkMode ? 'text-white' : 'text-gray-900']">{{ file.name }}</p>
                  <p :class="['text-xs mt-1', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ file.size }} · {{ file.date }}</p>
                </div>

                <!-- Selected Badge -->
                <div
                  v-if="selectedId === file.id"
                  class="w-6 h-6 rounded-full bg-construct-red flex items-center justify-center flex-shrink-0"
                >
                  <Check size="14" class="text-white" />
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredMedia.length === 0" class="flex flex-col items-center justify-center py-12">
              <ImageIcon size="48" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'" />
              <p :class="['text-sm font-bold mt-4', isDarkMode ? 'text-gray-500' : 'text-gray-400']">{{ t('admin_no_media_found') }}</p>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
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
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
  transform: scale(0.95);
  opacity: 0;
}
</style>
