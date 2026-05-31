<script setup>
/**
 * AdminProjects.vue - 项目管理页面
 * 
 * 功能说明：
 * - 管理系统中的所有项目
 * - 支持搜索和状态筛选功能
 * - 支持项目的增删改查操作
 */
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
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
import { useTheme } from '../../composables/useTheme';
import { formatToShort } from '../../utils/dateUtils';
import ProjectForm from '../../components/admin/ProjectForm.vue';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import Pagination from '../../components/admin/Pagination.vue';
import SearchFilterModal from '../../components/admin/SearchFilterModal.vue';

const props = defineProps({
  projects: {
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

const PROJECT_STATUS = Object.freeze({
  COMPLETED: 'completed',
  IN_PROGRESS: 'in-progress',
  PLANNING: 'planning',
});

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const selectedStatus = ref('all');
const isFormVisible = ref(false);
const editingProject = ref(null);
const showDeleteConfirm = ref(false);
const deletingProjectId = ref(null);

const localProjects = ref([...props.projects]);

watch(() => props.projects, (newProjects) => {
  localProjects.value = [...newProjects];
}, { immediate: true });

const statuses = ['all', PROJECT_STATUS.COMPLETED, PROJECT_STATUS.IN_PROGRESS, PROJECT_STATUS.PLANNING];

const filteredProjects = computed(() => {
  let result = [...localProjects.value];
  
  if (selectedStatus.value !== 'all') {
    result = result.filter(project => project.status === selectedStatus.value);
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(project =>
      project.title.toLowerCase().includes(query) ||
      project.description.toLowerCase().includes(query) ||
      (project.technologies && project.technologies.some(tech => tech.toLowerCase().includes(query)))
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
    router.delete(`/admin/projects/${deletingProjectId.value}`, {
      onSuccess: () => {
        localProjects.value = localProjects.value.filter(p => p.id !== deletingProjectId.value);
        deletingProjectId.value = null;
      },
    });
  }
  showDeleteConfirm.value = false;
};

const handleEdit = (project) => {
  const techValue = project.technologies;
  const techString = Array.isArray(techValue) 
    ? techValue.join(', ') 
    : (typeof techValue === 'string' ? techValue : '');
  
  editingProject.value = {
    id: project.id,
    title: project.title || '',
    description: project.description,
    long_description: project.long_description || '',
    image: project.image || '',
    url: project.url || '',
    github_url: project.github_url || '',
    role: project.role || '',
    year: project.year || '',
    technologies: techString,
    status: project.status || 'completed',
    sort_order: project.sort_order || 0
  };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingProject.value = null;
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (editingProject.value) {
    router.put(`/admin/projects/${editingProject.value.id}`, data, {
      onSuccess: () => {
        isFormVisible.value = false;
        editingProject.value = null;
      },
    });
  } else {
    router.post('/admin/projects', data, {
      onSuccess: () => {
        isFormVisible.value = false;
        editingProject.value = null;
      },
    });
  }
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingProject.value = null;
};

const handleFilterChange = ({ key, value }) => {
  if (key === 'status') {
    selectedStatus.value = value;
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
            <FolderKanban class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_projects') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_projects_subtitle') }}</p>
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
          key: 'status',
          options: statuses.map(s => ({
            value: s,
            label: s === 'all' ? t('admin_all') : s
          }))
        }
      ]"
      :filter-values="{ status: selectedStatus }"
      @filter-change="handleFilterChange"
    />

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
          
          <div :class="['flex items-center justify-between pt-3 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <div :class="['flex items-center gap-2 text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              <Clock size="14" />
              {{ formatToShort(project.created_at || project.date) }}
            </div>
            
            <div class="flex gap-2">
              <a
                v-if="project.github_url"
                :href="project.github_url"
                target="_blank"
                rel="noopener noreferrer"
                :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100']"
                :title="'GitHub'"
              >
                <Github size="16" />
              </a>
              <a
                v-if="project.url"
                :href="project.url"
                target="_blank"
                rel="noopener noreferrer"
                :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100']"
                :title="'Live Demo'"
              >
                <ExternalLink size="16" />
              </a>
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
    <Pagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredProjects.length"
      v-model:items-per-page="itemsPerPage"
      @update:current-page="currentPage = $event"
    />

    <!-- Content Form Modal -->
    <ProjectForm
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
