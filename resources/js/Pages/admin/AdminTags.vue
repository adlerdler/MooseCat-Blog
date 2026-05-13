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
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Tag,
  Plus,
  Search,
  Edit3,
  Trash2,
  ChevronLeft,
  ChevronRight,
  Filter,
  FileText,
  ToggleLeft,
  ToggleRight
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const statusFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = 12;

const tags = ref([
  { id: 1, name: 'Architecture', usageCount: 45, status: 'active' },
  { id: 2, name: 'Design', usageCount: 38, status: 'active' },
  { id: 3, name: 'Technology', usageCount: 32, status: 'active' },
  { id: 4, name: 'Philosophy', usageCount: 28, status: 'active' },
  { id: 5, name: 'Research', usageCount: 24, status: 'active' },
  { id: 6, name: 'Tutorial', usageCount: 19, status: 'active' },
  { id: 7, name: 'Case Study', usageCount: 15, status: 'active' },
  { id: 8, name: 'Algorithm', usageCount: 12, status: 'active' },
  { id: 9, name: 'Parametric', usageCount: 10, status: 'inactive' },
  { id: 10, name: 'Computational', usageCount: 18, status: 'active' },
  { id: 11, name: 'Digital Fabrication', usageCount: 8, status: 'active' },
  { id: 12, name: 'BIM', usageCount: 6, status: 'active' },
  { id: 13, name: 'Sustainability', usageCount: 14, status: 'active' },
  { id: 14, name: 'Urban Design', usageCount: 11, status: 'active' },
  { id: 15, name: 'Generative', usageCount: 9, status: 'inactive' },
]);

const filteredTags = computed(() => {
  return tags.value.filter(tag => {
    const matchesSearch = tag.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = statusFilter.value === 'all' || tag.status === statusFilter.value;
    return matchesSearch && matchesStatus;
  });
});

const totalPages = computed(() => Math.ceil(filteredTags.value.length / itemsPerPage));

const paginatedTags = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredTags.value.slice(start, start + itemsPerPage);
});

const toggleStatus = (tag) => {
  tag.status = tag.status === 'active' ? 'inactive' : 'active';
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center gap-4 mb-2">
        <Tag class="text-construct-red" size="32" />
        <h2 class="font-display text-4xl tracking-tighter">{{ t('admin_tags') }}</h2>
      </div>
      <p class="text-gray-400 text-sm font-bold tracking-widest uppercase">Manage content tags</p>
    </div>

    <!-- Search and Filter -->
    <div class="flex flex-col md:flex-row gap-4 mb-8">
      <div class="flex-1 relative">
        <Search :class="['absolute left-4 top-1/2 -translate-y-1/2', isDarkMode ? 'text-gray-400' : 'text-gray-500']" size="20" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('admin_search_tags')"
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
            v-model="statusFilter"
            :class="[
              'px-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
              isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option value="all">{{ t('admin_all_status') }}</option>
            <option value="active">{{ t('admin_active') }}</option>
            <option value="inactive">{{ t('admin_inactive') }}</option>
          </select>
        </div>
        <button class="flex items-center gap-2 px-6 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider transition-colors rounded">
          <Plus size="16" class="!text-white" /> {{ t('admin_add_tag') }}
        </button>
      </div>
    </div>

    <!-- Tags Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
      <div 
        v-for="tag in paginatedTags" 
        :key="tag.id"
        :class="[
          'border p-4 hover:border-construct-red transition-colors',
          isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <div class="flex items-start justify-between mb-3">
          <div class="flex items-center gap-2">
            <Tag :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="16" />
            <span :class="['font-bold text-sm', isDarkMode ? 'text-white' : 'text-gray-900']">{{ tag.name }}</span>
          </div>
          <button
            @click="toggleStatus(tag)"
            class="flex items-center"
          >
            <ToggleRight v-if="tag.status === 'active'" :class="isDarkMode ? 'text-green-400' : 'text-green-600'" size="20" />
            <ToggleLeft v-else :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="20" />
          </button>
        </div>
        
        <div :class="['flex items-center justify-between pt-3 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
          <div :class="['flex items-center gap-2 text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
            <FileText size="14" />
            <span>{{ tag.usageCount }} {{ t('admin_posts') }}</span>
          </div>
          <div class="flex items-center gap-1">
            <button :class="['p-1.5 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-500 hover:bg-gray-100']">
              <Edit3 size="16" />
            </button>
            <button :class="['p-1.5 transition-colors', isDarkMode ? 'text-gray-400 hover:bg-red-500/20' : 'text-gray-500 hover:bg-red-50']">
              <Trash2 size="16" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between mt-8">
      <div :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
        {{ t('admin_showing') }} {{ ((currentPage - 1) * itemsPerPage) + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredTags.length) }} {{ t('admin_of') }} {{ filteredTags.length }} {{ t('admin_tags') }}
      </div>
      <div class="flex items-center gap-2">
        <button
          @click="currentPage = Math.max(1, currentPage - 1)"
          :disabled="currentPage === 1"
          :class="[
            'p-2 border disabled:opacity-50 disabled:cursor-not-allowed transition-colors',
            isDarkMode ? 'border-gray-700 hover:bg-gray-700' : 'border-gray-300 hover:bg-gray-100'
          ]"
        >
          <ChevronLeft size="18" />
        </button>
        <span :class="['px-4 py-2 border', isDarkMode ? 'border-gray-700 text-white' : 'border-gray-300 text-gray-900']">{{ currentPage }} / {{ totalPages }}</span>
        <button
          @click="currentPage = Math.min(totalPages, currentPage + 1)"
          :disabled="currentPage === totalPages"
          :class="[
            'p-2 border disabled:opacity-50 disabled:cursor-not-allowed transition-colors',
            isDarkMode ? 'border-gray-700 hover:bg-gray-700' : 'border-gray-300 hover:bg-gray-100'
          ]"
        >
          <ChevronRight size="18" />
        </button>
      </div>
    </div>
  </div>
</template>
