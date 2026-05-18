<script setup>
/**
 * AdminProjects.vue - 项目管理页面
 * 
 * 功能说明：
 * - 管理系统中的所有项目
 * - 支持搜索和状态筛选功能
 * - 支持项目的增删改查操作
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  FolderKanban,
  Plus,
  Search,
  Edit3,
  Trash2,
  Eye,
  Clock,
  Filter,
  ExternalLink,
  Github
} from 'lucide-vue-next';
import { PROJECTS } from '../../data/projects';
import { useTheme } from '../../composables/useTheme';
import { formatToShort } from '../../utils/dateUtils';
import ContentForm from '../../components/admin/ContentForm.vue';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import AdminPagination from '../../components/admin/AdminPagination.vue';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const PROJECT_STATUS = Object.freeze({
  COMPLETED: 'completed',
  ACTIVE: 'active',
});

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const selectedStatus = ref('all');
const isFormVisible = ref(false);
const editingProject = ref(null);
const showDeleteConfirm = ref(false);
const deletingProjectId = ref(null);

const statuses = ['all', PROJECT_STATUS.COMPLETED, PROJECT_STATUS.ACTIVE];

const filteredProjects = computed(() => {
  let result = [...PROJECTS];
  
  if (selectedStatus.value !== 'all') {
    result = result.filter(project => project.status === selectedStatus.value);
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(project =>
      (project.title || project.name).toLowerCase().includes(query) ||
      project.description.toLowerCase().includes(query) ||
      (project.tags && project.tags.some(tag => tag.toLowerCase().includes(query)))
    );
  }
  
  return result;
});

const paginatedProjects = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredProjects.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredProjects.value.length / itemsPerPage.value);
});

const getStatusColor = (status) => {
  switch (status) {
    case 'Completed': return 'bg-green-900 text-green-300';
    case 'In Progress': return 'bg-blue-900 text-blue-300';
    case 'Planning': return 'bg-purple-900 text-purple-300';
    default: return 'bg-gray-700 text-gray-300';
  }
};

const handleDelete = (id) => {
  deletingProjectId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingProjectId.value !== null) {
    console.log('Delete project:', deletingProjectId.value);
    deletingProjectId.value = null;
  }
  showDeleteConfirm.value = false;
};

const handleEdit = (project) => {
  editingProject.value = {
    id: project.id,
    title: project.title || '',
    name: project.name || '',
    description: project.description,
    longDescription: project.longDescription || '',
    image: project.image || '',
    url: project.url || '',
    githubUrl: project.githubUrl || '',
    tags: project.tags ? project.tags.join(', ') : '',
    role: project.role || '',
    year: project.year || '',
    technologies: project.technologies ? project.technologies.join(', ') : '',
    status: project.status || 'completed',
    progress: project.progress || 0,
    startDate: project.startDate || new Date().toISOString().split('T')[0],
    sortOrder: project.sortOrder || 0
  };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingProject.value = null;
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (editingProject.value) {
    console.log('Update project:', data);
  } else {
    console.log('Create project:', data);
  }
  isFormVisible.value = false;
  editingProject.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingProject.value = null;
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <h2 :class="['font-display text-4xl tracking-tighter mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_projects') }}</h2>
      <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_projects_subtitle') }}</p>
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
      
      <!-- Status Filter -->
      <div class="relative">
        <Filter :class="['absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5', isDarkMode ? 'text-gray-500' : 'text-gray-400']" />
        <select
          v-model="selectedStatus"
          :class="[
            'pl-10 pr-8 py-3 border focus:border-construct-red focus:outline-none appearance-none cursor-pointer min-w-[150px]',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-200 text-gray-900'
          ]"
        >
          <option v-for="s in statuses" :key="s" :value="s">
            {{ s === 'all' ? t('admin_all') : s }}
          </option>
        </select>
      </div>
      
      <!-- Add Button -->
      <button
        @click="handleAdd"
        class="flex items-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-700 transition-colors rounded"
      >
        <Plus size="16" class="!text-white" />
        {{ t('admin_add') }}
      </button>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="project in paginatedProjects"
        :key="project.id"
        :class="[
          'border overflow-hidden hover:border-construct-red transition-colors',
          isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <!-- Project Image -->
        <div :class="['relative aspect-video', isDarkMode ? 'bg-gray-900' : 'bg-gray-100']">
          <img
            v-if="project.image"
            :src="project.image"
            :alt="project.title"
            class="w-full h-full object-cover"
          />
          <div v-else class="w-full h-full flex items-center justify-center">
            <FolderKanban :class="['w-12 h-12', isDarkMode ? 'text-gray-600' : 'text-gray-400']" />
          </div>
          <div class="absolute top-2 right-2">
            <span
              :class="['text-[10px] px-2 py-1 uppercase font-bold', getStatusColor(project.status)]"
            >
              {{ project.status }}
            </span>
          </div>
        </div>
        
        <!-- Project Info -->
        <div class="p-4">
          <h3 :class="['font-bold mb-2 line-clamp-2', isDarkMode ? 'text-white' : 'text-gray-900']">{{ project.title }}</h3>
          <p :class="['text-sm mb-4 line-clamp-3', isDarkMode ? 'text-gray-400' : 'text-gray-600']">{{ project.description }}</p>
          
          <div class="flex flex-wrap gap-1 mb-4">
            <span
              v-for="tag in (project.tags || []).slice(0, 3)"
              :key="tag"
              :class="['text-[10px] px-2 py-0.5 uppercase', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']"
            >
              #{{ tag }}
            </span>
          </div>
          
          <div :class="['flex items-center justify-between pt-3 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <div :class="['flex items-center gap-2 text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              <Clock size="14" />
              {{ formatToShort(project.date) }}
            </div>
            
            <div class="flex gap-2">
              <button
                v-if="project.github"
                :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100']"
                :title="'GitHub'"
              >
                <Github size="16" />
              </button>
              <button
                v-if="project.link"
                :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100']"
                :title="'Live Demo'"
              >
                <ExternalLink size="16" />
              </button>
              <button
                @click="handleEdit(project)"
                :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:text-blue-400 hover:bg-gray-700' : 'text-gray-500 hover:text-blue-500 hover:bg-gray-100']"
                :title="t('admin_edit')"
              >
                <Edit3 size="16" />
              </button>
              <button
                @click="handleDelete(project.id)"
                :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:text-red-400 hover:bg-gray-700' : 'text-gray-500 hover:text-red-500 hover:bg-gray-100']"
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
    <AdminPagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredProjects.length"
      v-model:items-per-page="itemsPerPage"
      @update:current-page="currentPage = $event"
    />

    <!-- Content Form Modal -->
    <ContentForm
      content-type="project"
      :edit-data="editingProject"
      :visible="isFormVisible"
      @save="handleSave"
      @cancel="handleCancel"
    />

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      title="确认删除"
      content="确定要删除这个项目吗？此操作不可撤销。"
      confirm-text="删除"
      confirm-variant="danger"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>
