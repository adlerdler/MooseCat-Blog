<script setup>
/**
 * AdminComments.vue - 评论管理页面
 * 
 * 功能说明：
 * - 管理用户评论
 * - 评论列表展示（作者、内容、文章、状态、时间）
 * - 评论搜索和筛选
 * - 审核、回复、删除评论
 * - 批量操作
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  MessageSquare,
  Plus,
  Search,
  Edit3,
  Trash2,
  ChevronLeft,
  ChevronRight,
  Filter,
  Check,
  X,
  User,
  Clock,
  Reply,
  FileText
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const statusFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = 8;

const comments = ref([
  { id: 1, author: 'John Doe', email: 'john@example.com', content: 'Great article! Very insightful analysis of constructivist principles.', post: 'THE GEOMETRY OF PERCEPTION', status: 'approved', date: '2024-01-15 10:30' },
  { id: 2, author: 'Jane Smith', email: 'jane@example.com', content: 'Would love to see more examples of parametric design.', post: 'DIGITAL FABRICATION', status: 'pending', date: '2024-01-15 14:22' },
  { id: 3, author: 'Bob Wilson', email: 'bob@example.com', content: 'The code examples are very helpful. Thanks!', post: 'COMPUTATIONAL THINKING', status: 'approved', date: '2024-01-16 09:15' },
  { id: 4, author: 'Alice Chen', email: 'alice@example.com', content: 'Can you elaborate on the sustainability aspect?', post: 'THE GEOMETRY OF PERCEPTION', status: 'pending', date: '2024-01-16 11:45' },
  { id: 5, author: 'Mike Johnson', email: 'mike@example.com', content: 'Excellent work on the BIM integration section.', post: 'BIM WORKFLOW', status: 'approved', date: '2024-01-17 16:30' },
  { id: 6, author: 'Sarah Lee', email: 'sarah@example.com', content: 'This is spam. Please remove.', post: 'ARCHITECTURAL THEORY', status: 'spam', date: '2024-01-18 08:20' },
  { id: 7, author: 'David Kim', email: 'david@example.com', content: 'The visualizations are stunning!', post: 'DATA VISUALIZATION', status: 'approved', date: '2024-01-18 13:55' },
  { id: 9, author: 'Emma White', email: 'emma@example.com', content: 'Looking forward to the next article in this series.', post: 'GENERATIVE DESIGN', status: 'pending', date: '2024-01-19 10:10' },
]);

const filteredComments = computed(() => {
  return comments.value.filter(comment => {
    const matchesSearch = comment.author.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         comment.content.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         comment.post.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = statusFilter.value === 'all' || comment.status === statusFilter.value;
    return matchesSearch && matchesStatus;
  });
});

const totalPages = computed(() => Math.ceil(filteredComments.value.length / itemsPerPage));

const paginatedComments = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredComments.value.slice(start, start + itemsPerPage);
});

const getStatusColor = (status) => {
  if (isDarkMode.value) {
    const colors = {
      approved: 'text-green-400',
      pending: 'text-yellow-400',
      spam: 'text-red-400'
    };
    return colors[status] || 'text-gray-400';
  } else {
    const colors = {
      approved: 'text-green-600',
      pending: 'text-yellow-600',
      spam: 'text-red-600'
    };
    return colors[status] || 'text-gray-600';
  }
};

const getStatusLabel = (status) => {
  const labels = {
    approved: t('admin_approved'),
    pending: t('admin_pending'),
    spam: t('admin_spam')
  };
  return labels[status] || status;
};

const approveComment = (comment) => {
  comment.status = 'approved';
};

const rejectComment = (comment) => {
  comment.status = 'spam';
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center gap-4 mb-2">
        <MessageSquare class="text-construct-red" size="32" />
        <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_comments') }}</h2>
      </div>
      <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Manage user comments</p>
    </div>

    <!-- Search and Filter -->
    <div class="flex flex-col md:flex-row gap-4 mb-8">
      <div class="flex-1 relative">
        <Search :class="['absolute left-4 top-1/2 -translate-y-1/2', isDarkMode ? 'text-gray-400' : 'text-gray-500']" size="20" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('admin_search_comments')"
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
            <option value="approved">{{ t('admin_approved') }}</option>
            <option value="pending">{{ t('admin_pending') }}</option>
            <option value="spam">{{ t('admin_spam') }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Comments List -->
    <div class="space-y-4">
      <div 
        v-for="comment in paginatedComments" 
        :key="comment.id"
        :class="[
          'border p-6 hover:border-construct-red transition-colors',
          isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div :class="['w-10 h-10 rounded-full flex items-center justify-center', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
              <User :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" size="18" />
            </div>
            <div>
              <div :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ comment.author }}</div>
              <div :class="['text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ comment.email }}</div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <span :class="['px-3 py-1 rounded-full text-xs font-bold', getStatusColor(comment.status)]">
              {{ getStatusLabel(comment.status) }}
            </span>
          </div>
        </div>
        
        <div :class="['mb-4 p-4 rounded-lg', isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50']">
          <p :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ comment.content }}</p>
        </div>
        
        <div :class="['flex items-center justify-between pt-4 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
          <div :class="['flex items-center gap-4 text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
            <div class="flex items-center gap-1">
              <FileText size="14" />
              <span>{{ comment.post }}</span>
            </div>
            <div class="flex items-center gap-1">
              <Clock size="14" />
              <span>{{ comment.date }}</span>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button
              @click="approveComment(comment)"
              :class="['flex items-center gap-1 px-3 py-1.5 transition-colors', isDarkMode ? 'bg-green-500/20 text-green-400 hover:bg-green-500/30' : 'bg-green-100 text-green-700 hover:bg-green-200']"
              v-if="comment.status !== 'approved'"
            >
              <Check size="16" /> {{ t('admin_approve') }}
            </button>
            <button
              @click="rejectComment(comment)"
              :class="['flex items-center gap-1 px-3 py-1.5 transition-colors', isDarkMode ? 'bg-red-500/20 text-red-400 hover:bg-red-500/30' : 'bg-red-100 text-red-700 hover:bg-red-200']"
              v-if="comment.status !== 'spam'"
            >
              <X size="16" /> {{ t('admin_reject') }}
            </button>
            <button :class="['flex items-center gap-1 px-3 py-1.5 transition-colors', isDarkMode ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-100 hover:bg-gray-200']">
              <Reply size="16" /> {{ t('admin_reply') }}
            </button>
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
      <div class="text-sm text-gray-400">
        {{ t('admin_showing') }} {{ ((currentPage - 1) * itemsPerPage) + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredComments.length) }} {{ t('admin_of') }} {{ filteredComments.length }} {{ t('admin_comments') }}
      </div>
      <div class="flex items-center gap-2">
        <button
          @click="currentPage = Math.max(1, currentPage - 1)"
          :disabled="currentPage === 1"
          class="p-2 border border-gray-700 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <ChevronLeft size="18" />
        </button>
        <span class="px-4 py-2 border border-gray-700">{{ currentPage }} / {{ totalPages }}</span>
        <button
          @click="currentPage = Math.min(totalPages, currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="p-2 border border-gray-700 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <ChevronRight size="18" />
        </button>
      </div>
    </div>
  </div>
</template>
