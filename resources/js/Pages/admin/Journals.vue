<script setup>
/**
 * Journals.vue - 日志管理页面
 * 
 * 功能说明：
 * - 管理用户日志
 * - 日志列表展示（标题、用户、心情、天气、日期、状态）
 * - 日志搜索和筛选功能
 * - 查看、编辑、删除日志
 * - 日志状态管理（公开/私密）
 */
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import {
  BookText,
  Eye,
  Edit3,
  Trash2,
  Calendar,
  Cloud,
  Smile,
  Globe,
  Lock,
  CheckCircle,
  Plus
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { journals as journalsData } from '../../data/journals';
import { useJournalData } from '../../composables/useJournalData';
import { adminUsers } from '../../data/users';
import { findById, findIndexById } from '../../utils/typeConvert';
import JournalForm from '../../components/admin/JournalForm.vue';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import SearchFilterModal from '../../components/admin/SearchFilterModal.vue';

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { getJournalsByUserId, getMoodLabel, getWeatherLabel, getMoodTypes, getWeatherTypes } = useJournalData();
const router = useRouter();

const searchQuery = ref('');
const moodFilter = ref('all');
const weatherFilter = ref('all');
const statusFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = 8;
const isFormVisible = ref(false);
const editingJournal = ref(null);
const showDeleteConfirm = ref(false);
const deletingJournalId = ref(null);
const showSuccessMessage = ref(false);

const journals = ref([...journalsData]);

const filteredJournals = computed(() => {
  return journals.value.filter(journal => {
    const matchesSearch = journal.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         journal.content.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesMood = moodFilter.value === 'all' || journal.mood === moodFilter.value;
    const matchesWeather = weatherFilter.value === 'all' || journal.weather === weatherFilter.value;
    const matchesStatus = statusFilter.value === 'all' || 
                         (statusFilter.value === 'public' && journal.is_public) ||
                         (statusFilter.value === 'private' && !journal.is_public);
    return matchesSearch && matchesMood && matchesWeather && matchesStatus;
  });
});

const totalPages = computed(() => Math.ceil(filteredJournals.value.length / itemsPerPage));

const paginatedJournals = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredJournals.value.slice(start, start + itemsPerPage);
});

const getUserName = (userId) => {
  const user = findById(adminUsers, userId);
  return user ? user.name : '未知用户';
};

const formatDate = (dateStr) => {
  const date = new Date(dateStr);
  return date.toLocaleDateString('zh-CN', { year: 'numeric', month: 'short', day: 'numeric' });
};

const formatDateTime = (dateStr) => {
  const date = new Date(dateStr);
  return date.toLocaleString('zh-CN', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const togglePublic = (journal) => {
  journal.is_public = !journal.is_public;
};

const handleView = (journal) => {
  editingJournal.value = { ...journal, action: 'view' };
  isFormVisible.value = true;
};

const handleEdit = (journal) => {
  editingJournal.value = { ...journal, action: 'edit' };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingJournal.value = { action: 'add' };
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (data.action === 'edit') {
    const index = findIndexById(journals.value, data.id);
    if (index !== -1) {
      journals.value[index] = { 
        ...journals.value[index], 
        ...data,
        updated_at: new Date().toISOString().replace('T', ' ').substring(0, 19)
      };
    }
  } else if (data.action === 'add') {
    const newId = Math.max(...journals.value.map(j => j.id), 0) + 1;
    const now = new Date().toISOString().replace('T', ' ').substring(0, 19);
    journals.value.push({
      id: newId,
      ...data,
      created_at: now,
      updated_at: now
    });
  }
  
  isFormVisible.value = false;
  editingJournal.value = null;
  showSuccessMessage.value = true;
  
  setTimeout(() => {
    showSuccessMessage.value = false;
  }, 3000);
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingJournal.value = null;
};

const handleDelete = (id) => {
  deletingJournalId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingJournalId.value !== null) {
    journals.value = journals.value.filter(j => j.id !== deletingJournalId.value);
    deletingJournalId.value = null;
  }
  showDeleteConfirm.value = false;
};

const handleFilterChange = ({ key, value }) => {
  if (key === 'mood') {
    moodFilter.value = value;
  } else if (key === 'weather') {
    weatherFilter.value = value;
  } else if (key === 'status') {
    statusFilter.value = value;
  }
  currentPage.value = 1;
};

const clearFilters = () => {
  moodFilter.value = 'all';
  weatherFilter.value = 'all';
  statusFilter.value = 'all';
  searchQuery.value = '';
  currentPage.value = 1;
};
</script>

<template>
  <div class="p-8">
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <BookText class="text-construct-red" :size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">日志管理</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">管理系统中的所有日志内容</p>
        </div>
        <button
          @click="handleAdd"
          class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
        >
          <Plus :size="18" />
          新增日志
        </button>
      </div>
    </div>

    <div v-if="showSuccessMessage" :class="['mb-6 p-4 rounded-lg flex items-center gap-3', isDarkMode ? 'bg-green-900/30 text-green-400 border border-green-700' : 'bg-green-50 text-green-700 border border-green-200']">
      <CheckCircle :size="20" />
      <span class="font-medium">保存成功！</span>
    </div>

    <SearchFilterModal
      v-model:search-query="searchQuery"
      :search-placeholder="'搜索日志标题或内容...'"
      :filters="[
        {
          key: 'mood',
          options: [
            { value: 'all', label: '所有心情' },
            ...getMoodTypes().map(mood => ({ value: mood, label: getMoodLabel(mood) }))
          ]
        },
        {
          key: 'weather',
          options: [
            { value: 'all', label: '所有天气' },
            ...getWeatherTypes().map(weather => ({ value: weather, label: getWeatherLabel(weather) }))
          ]
        },
        {
          key: 'status',
          options: [
            { value: 'all', label: '所有状态' },
            { value: 'public', label: '公开' },
            { value: 'private', label: '私密' }
          ]
        }
      ]"
      :filter-values="{ mood: moodFilter, weather: weatherFilter, status: statusFilter }"
      @filter-change="handleFilterChange"
    />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div 
        v-for="journal in paginatedJournals" 
        :key="journal.id"
        :class="['border rounded-xl overflow-hidden transition-all hover:shadow-lg', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']"
      >
        <div :class="['p-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
          <div class="flex items-start justify-between mb-3">
            <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ journal.title }}</h3>
            <button 
              @click="togglePublic(journal)"
              :class="['p-2 rounded-lg transition-colors', journal.is_public ? 'text-green-500 hover:bg-green-500/10' : 'text-gray-500 hover:bg-gray-500/10']"
            >
              <component :is="journal.is_public ? Globe : Lock" :size="18" />
            </button>
          </div>

          <div class="flex flex-wrap gap-3 mb-4">
            <div :class="['flex items-center gap-2 px-3 py-1 rounded-full text-sm', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
              <Smile :size="14" />
              <span>{{ getMoodLabel(journal.mood) }}</span>
            </div>
            <div :class="['flex items-center gap-2 px-3 py-1 rounded-full text-sm', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
              <Cloud :size="14" />
              <span>{{ getWeatherLabel(journal.weather) }}</span>
            </div>
            <div :class="['flex items-center gap-2 px-3 py-1 rounded-full text-sm', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
              <Calendar :size="14" />
              <span>{{ formatDate(journal.date) }}</span>
            </div>
          </div>

          <p :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-600']">作者：{{ getUserName(journal.user_id) }}</p>
        </div>

        <div :class="['p-6', isDarkMode ? 'bg-gray-750' : 'bg-gray-50']">
          <p :class="['text-sm line-clamp-3 mb-4', isDarkMode ? 'text-gray-300' : 'text-gray-700']">{{ journal.content }}</p>
          
          <div :class="['flex items-center justify-between text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
            <span>创建：{{ formatDateTime(journal.created_at) }}</span>
            <span>更新：{{ formatDateTime(journal.updated_at) }}</span>
          </div>
        </div>

        <div :class="['flex items-center justify-end gap-2 p-4 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
          <button 
            @click="handleView(journal)" 
            :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-500 hover:bg-gray-100']"
          >
            <Eye :size="16" />
          </button>
          <button 
            @click="handleEdit(journal)" 
            :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-500 hover:bg-gray-100']"
          >
            <Edit3 :size="16" />
          </button>
          <button 
            @click="handleDelete(journal.id)" 
            :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-red-500/20' : 'text-gray-500 hover:bg-red-50']"
          >
            <Trash2 :size="16" />
          </button>
        </div>
      </div>
    </div>

    <div v-if="filteredJournals.length === 0" :class="['text-center py-16', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
      <BookText :size="64" class="mx-auto mb-4 opacity-50" />
      <p class="text-xl">没有找到日志</p>
    </div>

    <div v-if="totalPages > 1" class="flex items-center justify-center gap-2 mt-8">
      <button 
        @click="currentPage--" 
        :disabled="currentPage === 1"
        :class="['px-4 py-2 rounded-lg transition-colors', currentPage === 1 ? 'opacity-50 cursor-not-allowed' : '', isDarkMode ? 'bg-gray-800 text-white hover:bg-gray-700' : 'bg-white text-gray-900 hover:bg-gray-100']"
      >
        上一页
      </button>
      
      <span :class="['px-4 py-2', isDarkMode ? 'text-gray-400' : 'text-gray-600']">
        第 {{ currentPage }} / {{ totalPages }} 页
      </span>

      <button 
        @click="currentPage++" 
        :disabled="currentPage === totalPages"
        :class="['px-4 py-2 rounded-lg transition-colors', currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : '', isDarkMode ? 'bg-gray-800 text-white hover:bg-gray-700' : 'bg-white text-gray-900 hover:bg-gray-100']"
      >
        下一页
      </button>
    </div>

    <JournalForm
      :journal-data="editingJournal"
      :visible="isFormVisible"
      @save="handleSave"
      @cancel="handleCancel"
    />

    <ConfirmDialog
      :visible="showDeleteConfirm"
      type="delete"
      title="确认删除"
      content="删除后将无法恢复，确定要删除此日志吗？"
      confirm-text="删除"
      cancel-text="取消"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>
