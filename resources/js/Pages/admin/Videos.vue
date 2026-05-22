<script setup>
/**
 * AdminVideos.vue - 视频管理页面
 * 
 * 功能说明：
 * - 管理系统中的所有视频
 * - 支持搜索和平台筛选功能
 * - 支持视频的增删改查操作
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Play,
  Plus,
  Search,
  Edit3,
  Trash2,
  Clock,
  Filter,
  Youtube,
  Video
} from 'lucide-vue-next';
import { VIDEOS } from '../../data/videos';
import { useTheme } from '../../composables/useTheme';
import { formatToRelative } from '../../utils/dateUtils';
import VideoForm from '../../components/admin/VideoForm.vue';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import Pagination from '../../components/admin/Pagination.vue';
import SearchFilterModal from '../../components/admin/SearchFilterModal.vue';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const selectedPlatform = ref('all');
const isFormVisible = ref(false);
const editingVideo = ref(null);
const showDeleteConfirm = ref(false);
const deletingVideoId = ref(null);
const videos = ref([...VIDEOS]);

const platforms = ['all', 'YouTube', 'Vimeo', 'Bilibili'];

const getPlatformIcon = (platform) => {
  switch (platform) {
    case 'YouTube': return Youtube;
    case 'Vimeo': return Video;
    case 'Bilibili': return Video;
    default: return Play;
  }
};

const filteredVideos = computed(() => {
  let result = [...videos.value];
  
  if (selectedPlatform.value !== 'all') {
    result = result.filter(video => video.platform === selectedPlatform.value);
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(video =>
      video.title.toLowerCase().includes(query) ||
      video.description.toLowerCase().includes(query)
    );
  }
  
  return result;
});

const paginatedVideos = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredVideos.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredVideos.value.length / itemsPerPage.value);
});

const handleDelete = (id) => {
  deletingVideoId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingVideoId.value !== null) {
    videos.value = videos.value.filter(v => v.id !== deletingVideoId.value);
    deletingVideoId.value = null;
  }
  showDeleteConfirm.value = false;
};

const handleEdit = (video) => {
  editingVideo.value = {
    id: video.id,
    title: video.title,
    description: video.description,
    thumbnail: video.thumbnail,
    url: video.videoId,
    platform: video.platform.toLowerCase(),
    date: video.date,
    status: 'published'
  };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingVideo.value = null;
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (editingVideo.value) {
    console.log('Update video:', data);
  } else {
    console.log('Create video:', data);
  }
  isFormVisible.value = false;
  editingVideo.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingVideo.value = null;
};

const handleFilterChange = ({ key, value }) => {
  if (key === 'platform') {
    selectedPlatform.value = value;
  }
  currentPage.value = 1;
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Play class="text-construct-red" size="32" />
            <h2 class="font-display text-4xl tracking-tighter">{{ t('admin_videos') }}</h2>
          </div>
          <p class="text-gray-400 text-sm font-black tracking-[0.2em] uppercase opacity-50">{{ t('admin_videos_subtitle') }}</p>
        </div>
        <button
          @click="handleAdd"
          class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
        >
          <Plus size="18" />
          {{ t('admin_add') }}
        </button>
      </div>
    </div>

    <!-- Toolbar -->
    <SearchFilterModal
      v-model:search-query="searchQuery"
      :search-placeholder="t('admin_search_placeholder')"
      :filters="[
        {
          key: 'platform',
          options: platforms.map(p => ({
            value: p,
            label: p === 'all' ? t('admin_all') : p
          }))
        }
      ]"
      :filter-values="{ platform: selectedPlatform }"
      @filter-change="handleFilterChange"
    />

    <!-- Videos Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="video in paginatedVideos"
        :key="video.id"
        :class="[
          'border overflow-hidden hover:border-construct-red transition-colors',
          isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <!-- Video Thumbnail -->
        <div class="relative aspect-video bg-gray-900">
          <img
            v-if="video.thumbnail"
            :src="video.thumbnail"
            :alt="video.title"
            class="w-full h-full object-cover"
          />
          <div v-else class="w-full h-full flex items-center justify-center">
            <component :is="getPlatformIcon(video.platform)" class="w-12 h-12 text-gray-600" />
          </div>
          <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/80 text-white text-xs font-bold uppercase">
            {{ video.platform }}
          </div>
        </div>
        
        <!-- Video Info -->
        <div class="p-4">
          <h3 :class="['font-bold mb-2 line-clamp-2', isDarkMode ? 'text-white' : 'text-gray-900']">{{ video.title }}</h3>
          <p :class="['text-sm mb-4 line-clamp-2', isDarkMode ? 'text-gray-400' : 'text-gray-600']">{{ video.description }}</p>
          
          <div class="flex items-center justify-between">
            <div :class="['flex items-center gap-2 text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              <Clock size="12" />
              {{ formatToRelative(video.published_at) }}
            </div>
            
            <div class="flex gap-2">
              <button
                @click="handleEdit(video)"
                :class="[
                  'p-2 transition-colors',
                  isDarkMode ? 'text-gray-400 hover:text-blue-400 hover:bg-gray-700' : 'text-gray-500 hover:text-blue-500 hover:bg-gray-100'
                ]"
                :title="t('admin_edit')"
              >
                <Edit3 size="14" />
              </button>
              <button
                @click="handleDelete(video.id)"
                :class="[
                  'p-2 transition-colors',
                  isDarkMode ? 'text-gray-400 hover:text-red-400 hover:bg-gray-700' : 'text-gray-500 hover:text-red-500 hover:bg-gray-100'
                ]"
                :title="t('admin_delete')"
              >
                <Trash2 size="14" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <Pagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredVideos.length"
      v-model:items-per-page="itemsPerPage"
      @update:current-page="currentPage = $event"
    />

    <!-- Content Form Modal -->
    <VideoForm
      :edit-data="editingVideo"
      :visible="isFormVisible"
      @save="handleSave"
      @cancel="handleCancel"
    />

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      title="确认删除"
      content="确定要删除这个视频吗？此操作不可撤销。"
      confirm-text="删除"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>
