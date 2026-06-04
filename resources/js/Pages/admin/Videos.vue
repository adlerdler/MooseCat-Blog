<script setup>
/**
 * AdminVideos.vue - 视频管理页面
 * 
 * 功能说明：
 * - 管理系统中的所有视频
 * - 支持搜索和平台筛选功能
 * - 支持视频的增删改查操作
 */
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { router, usePage } from '@inertiajs/vue3';
import {
  Play,
  Plus
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { useToast } from '../../composables/useToast';
import { formatToSmartDate } from '../../utils/dateUtils';
import VideoForm from '../../components/admin/VideoForm.vue';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import Pagination from '../../components/admin/Pagination.vue';
import SearchFilterModal from '../../components/admin/SearchFilterModal.vue';
import SeoForm from '../../components/admin/SeoForm.vue';
import ContentCard from '../../components/admin/ContentCard.vue';

const toDatetimeLocal = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  const pad = n => String(n).padStart(2, '0');
  return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
};

const props = defineProps({
  videos: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  },
  users: {
    type: Array,
    default: () => []
  },
  video: {
    type: Object,
    default: null
  }
});

const { t: originalT } = useI18n();
const t = (key, fallback = '') => {
  if (!key) return fallback || '';
  try {
    return originalT(key) || fallback;
  } catch (e) {
    return fallback;
  }
};
const { isDarkMode } = useTheme();
const { success: toastSuccess, error: toastError } = useToast();
const page = usePage();

const getAuthorName = (authorId) => {
  const user = props.users.find(u => u.id === authorId);
  return user ? (user.penName || user.name) : 'Unknown';
};

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const selectedPlatform = ref('all');
const isFormVisible = ref(false);
const editingVideo = ref(null);
const showDeleteConfirm = ref(false);
const deletingVideoId = ref(null);
const showSeoModal = ref(false);
const editingSeoVideo = ref({ id: null, meta_title: '', meta_description: '', meta_keywords: '' });

const localVideos = ref([...props.videos]);

watch(() => props.videos, (newVideos) => {
  localVideos.value = [...newVideos];
}, { immediate: true });

watch(() => props.video, (newVideo) => {
  if (newVideo) {
    editingVideo.value = {
      id: newVideo.id,
      title: newVideo.title,
      description: newVideo.description,
      thumbnail: newVideo.cover_image,
      url: newVideo.video_url,
      video_id: newVideo.video_id,
      platform: newVideo.platform,
      duration: newVideo.duration,
      category: newVideo.category,
      date: newVideo.published_at,
      tags: newVideo.tags,
      status: newVideo.status,
    };
    isFormVisible.value = true;
  }
}, { immediate: true });

const platforms = ['all', 'YouTube', 'Vimeo', 'Bilibili'];

const getStatusBadgeClass = (status) => {
  const dark = isDarkMode.value;
  const base = 'px-2 py-1 text-xs font-bold uppercase rounded';
  if (status === 'published') return `${base} ${dark ? 'bg-green-900 text-green-300' : 'bg-green-100 text-green-700'}`;
  if (status === 'draft') return `${base} ${dark ? 'bg-yellow-900 text-yellow-300' : 'bg-yellow-100 text-yellow-700'}`;
  return `${base} ${dark ? 'bg-gray-700 text-gray-300' : 'bg-gray-200 text-gray-700'}`;
};

const filteredVideos = computed(() => {
  let result = [...localVideos.value];
  
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
    router.delete(route('videos.destroy', deletingVideoId.value), {
      preserveState: true,
      onSuccess: () => {
        toastSuccess('Video deleted successfully');
        showDeleteConfirm.value = false;
        deletingVideoId.value = null;
      },
      onError: (err) => toastError(err?.message || 'Failed to delete video'),
    });
  }
};

const handleEdit = (video) => {
  editingVideo.value = {
    id: video.id,
    title: video.title,
    description: video.description,
    thumbnail: video.cover_image,
    url: video.video_url,
    video_id: video.video_id,
    platform: video.platform,
    duration: video.duration,
    category: video.category_id,
    date: toDatetimeLocal(video.published_at),
    tags: Array.isArray(video.tags) ? video.tags.join(', ') : video.tags,
    status: video.status,
  };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingVideo.value = null;
  isFormVisible.value = true;
};

const handleSave = (data) => {
  const category = props.categories.find(c => c.name === data.category || c.id === data.category);
  const payload = {
    title: data.title,
    description: data.description,
    video_url: data.url || null,
    video_id: data.video_id || null,
    platform: data.platform || null,
    cover_image: data.thumbnail,
    duration: data.duration ? parseInt(data.duration) : null,
    category_id: category ? category.id : null,
    tags: data.tags,
    published_at: data.date || null,
    status: data.status || 'draft',
  };

  if (editingVideo.value && editingVideo.value.id) {
    router.put(route('videos.update', editingVideo.value.id), payload, {
      preserveState: true,
      onError: (err) => toastError(err?.message || 'Failed to update video'),
      onSuccess: () => toastSuccess('Video updated successfully'),
    });
  } else {
    router.post(route('videos.store'), payload, {
      preserveState: true,
      onError: (err) => toastError(err?.message || 'Failed to create video'),
      onSuccess: () => toastSuccess('Video created successfully'),
    });
  }
  isFormVisible.value = false;
  editingVideo.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingVideo.value = null;
};

const handleSeoEdit = (video) => {
  editingSeoVideo.value = {
    id: video.id,
    meta_title: video.meta_title || '',
    meta_description: video.meta_description || '',
    meta_keywords: video.meta_keywords || '',
  };
  showSeoModal.value = true;
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
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_videos') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_videos_subtitle') }}</p>
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
      <ContentCard
        v-for="video in paginatedVideos"
        :key="video.id"
        :item="video"
        :cover-image="video.cover_image"
        :title="video.title"
        :description="video.description"
        :tags="video.tags"
        :date="formatToSmartDate(video.published_at || video.created_at)"
        :author="getAuthorName(video.author_id)"
        :placeholder-icon="Play"
        @edit="handleEdit"
        @seo-edit="handleSeoEdit"
        @delete="handleDelete"
      >
        <template #bottom-right-badge>
          <span :class="getStatusBadgeClass(video.status)">
            {{ video.status }}
          </span>
        </template>
      </ContentCard>
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
      :categories="categories"
      :media-files="page.props.mediaFiles || []"
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

    <!-- SEO Edit Modal -->
    <SeoForm
      v-model:visible="showSeoModal"
      :item="editingSeoVideo"
      resource-route="videos.update"
    />
  </div>
</template>
