<script setup>
/**
 * AdminTags.vue - 标签管理页面
 * 
 * 功能说明：
 * - 管理文章和视频的标签
 * - 标签列表展示（名称、使用次数、状态）
 * - 标签搜索和筛选
 * - 添加、编辑、删除标签
 * - 标签状态管理（启用/禁用）
 */
import {
  ref,
  computed,
  watch,
  router,
  useI18n,
  useTheme,
  useToast,
  Tag,
  Plus,
  Edit3,
  Trash2,
  MetaForm,
  ConfirmDialog,
  Pagination,
  EmptyState,
  SearchFilterModal
} from '../../composables/useAdminImports';
import { Motion } from 'motion-v';

const props = defineProps({
  tags: {
    type: Array,
    default: () => []
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

const searchQuery = ref('');
const statusFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const isFormVisible = ref(false);
const editingTag = ref(null);
const showDeleteConfirm = ref(false);
const deletingTagId = ref(null);

const localTags = ref([...props.tags]);

watch(() => props.tags, (newTags) => {
  localTags.value = [...newTags];
}, { immediate: true });

const filteredTags = computed(() => {
  return localTags.value.filter(tag => {
    const matchesSearch = tag.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = statusFilter.value === 'all' || tag.status === statusFilter.value;
    return matchesSearch && matchesStatus;
  });
});

const totalPages = computed(() => Math.ceil(filteredTags.value.length / itemsPerPage.value));

const paginatedTags = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredTags.value.slice(start, start + itemsPerPage.value);
});

const handleEdit = (tag) => {
  editingTag.value = { ...tag };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingTag.value = null;
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (editingTag.value) {
    router.put(`/admin/tags/${editingTag.value.id}`, data, {
      preserveState: true,
      onSuccess: () => {
        toastSuccess(t('toast.update_success'));
        isFormVisible.value = false;
        editingTag.value = null;
      },
      onError: (err) => {
        console.error('Tag update error:', err);
        toastError(t('toast.update_error'));
      },
    });
  } else {
    router.post('/admin/tags', data, {
      preserveState: true,
      onSuccess: () => {
        toastSuccess(t('toast.create_success'));
        isFormVisible.value = false;
        editingTag.value = null;
      },
      onError: (err) => {
        console.error('Tag create error:', err);
        toastError(t('toast.create_error'));
      },
    });
  }
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingTag.value = null;
};

const handleDelete = (id) => {
  deletingTagId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingTagId.value !== null) {
    router.delete(`/admin/tags/${deletingTagId.value}`, {
      preserveState: true,
      onSuccess: () => {
        toastSuccess(t('toast.delete_success'));
        localTags.value = localTags.value.filter(t => t.id !== deletingTagId.value);
        deletingTagId.value = null;
      },
      onError: (err) => {
        console.error('Tag delete error:', err);
        toastError(t('toast.delete_error'));
      },
    });
  }
  showDeleteConfirm.value = false;
};

const handleFilterChange = ({ key, value }) => {
  if (key === 'status') {
    statusFilter.value = value;
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
            <Tag class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_tags') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Manage content tags</p>
        </div>
        <button @click="handleAdd" class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl">
          <Plus size="18" class="!text-white" /> {{ t('admin_add_tag') }}
        </button>
      </div>
    </div>

    <!-- Search and Filter -->
    <SearchFilterModal
      v-model:search-query="searchQuery"
      :search-placeholder="t('admin_search_tags')"
      :filters="[
        {
          key: 'status',
          options: [
            { value: 'all', label: t('admin_all_status') },
            { value: 'active', label: t('admin_active') },
            { value: 'inactive', label: t('admin_inactive') }
          ]
        }
      ]"
      :filter-values="{ status: statusFilter }"
      @filter-change="handleFilterChange"
    />

    <!-- Content Area -->
    <template v-if="filteredTags.length > 0">
      <!-- Tags Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <Motion
          v-for="(tag, index) in paginatedTags" 
          :key="tag.id"
          :initial="{ opacity: 0, y: 24 }"
          :animate="{ opacity: 1, y: 0 }"
          :transition="{ duration: 0.35, delay: index * 0.06, ease: 'easeOut' }"
          :whileHover="{ scale: 1.02, y: -3, boxShadow: '0 16px 40px rgba(0,0,0,0.14)' }"
          :whileTap="{ scale: 0.98 }"
          :class="[
            'group relative flex flex-col rounded-xl overflow-hidden cursor-default',
            isDarkMode 
              ? 'bg-gray-800/60 border border-gray-700/50 shadow-[0_4px_20px_rgba(0,0,0,0.2)]'
              : 'bg-white border border-gray-200/60 shadow-[0_4px_20px_rgba(0,0,0,0.04)]'
          ]"
        >
          <!-- Card Background Accent -->
          <div class="absolute top-0 right-0 p-8 opacity-0 group-hover:opacity-10 transition-opacity duration-500 pointer-events-none">
            <Tag size="80" class="rotate-12" />
          </div>

          <!-- Card Body -->
          <div class="p-5 pb-3 flex flex-col flex-1">
            <!-- Tag Icon -->
            <div :class="[
              'w-12 h-12 rounded-xl flex items-center justify-center mb-3 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6',
              isDarkMode 
                ? 'bg-gradient-to-br from-construct-red/20 to-construct-red/10 text-construct-red' 
                : 'bg-gradient-to-br from-construct-red/10 to-construct-red/5 text-construct-red'
            ]">
              <Tag size="24" />
            </div>

            <!-- Name with hash -->
            <h3 :class="[
              'font-bold text-lg mb-1.5 truncate transition-colors duration-300',
              isDarkMode ? 'text-white group-hover:text-construct-red' : 'text-gray-900 group-hover:text-construct-red'
            ]">
              <span class="text-construct-red">#</span>{{ tag.name }}
            </h3>

            <!-- Slug -->
            <p :class="['text-xs mb-2 truncate', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              {{ tag.slug }}
            </p>
          </div>

          <!-- Bottom Bar: Badges + Actions -->
          <div class="flex items-center justify-between px-5 py-3 mt-auto">
            <div class="flex gap-1.5 text-[11px] font-bold">
              <span :class="['px-2 py-0.5 rounded-md', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200/70 text-gray-500']" v-if="tag.posts_count">
                📝 {{ tag.posts_count }}
              </span>
              <span :class="['px-2 py-0.5 rounded-md', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200/70 text-gray-500']" v-if="tag.videos_count">
                🎬 {{ tag.videos_count }}
              </span>
              <span :class="['px-2 py-0.5 rounded-md', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200/70 text-gray-500']" v-if="tag.projects_count">
                📁 {{ tag.projects_count }}
              </span>
              <span :class="['px-2 py-0.5 rounded-md', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200/70 text-gray-500']" v-if="tag.resources_count">
                📦 {{ tag.resources_count }}
              </span>
            </div>
            <div class="flex gap-0.5">
              <button 
                @click="handleEdit(tag)" 
                :class="['p-2 rounded-lg transition-all duration-200 hover:scale-110', isDarkMode ? 'text-gray-400 hover:text-blue-400 hover:bg-gray-700' : 'text-gray-400 hover:text-blue-500 hover:bg-gray-200']"
              >
                <Edit3 size="14" />
              </button>
              <button 
                @click="handleDelete(tag.id)" 
                :class="['p-2 rounded-lg transition-all duration-200 hover:scale-110', isDarkMode ? 'text-gray-400 hover:text-red-400 hover:bg-gray-700/50' : 'text-gray-400 hover:text-red-500 hover:bg-red-50']"
              >
                <Trash2 size="14" />
              </button>
            </div>
          </div>
        </Motion>
      </div>

      <!-- Pagination -->
      <Pagination
        :current-page="currentPage"
        :total-pages="totalPages"
        :total-items="filteredTags.length"
        v-model:items-per-page="itemsPerPage"
        @update:current-page="currentPage = $event"
      />
    </template>

    <!-- Empty State -->
    <template v-else>
      <div class="mt-8">
        <EmptyState
          :title="t('admin_no_tags_found')"
          :description="t('admin_no_tags_description')"
          :icon="Tag"
        />
      </div>
    </template>

    <!-- Meta Form Modal -->
    <MetaForm
      type="tag"
      :edit-data="editingTag"
      :visible="isFormVisible"
      @save="handleSave"
      @cancel="handleCancel"
    />

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      type="delete"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>
