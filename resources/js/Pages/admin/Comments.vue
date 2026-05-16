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
  Filter,
  Check,
  X,
  User,
  Clock,
  Reply,
  FileText
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { commentsData } from '../../data/comments';
import { POSTS } from '../../data/posts';
import { formatToShort } from '../../utils/dateUtils';
import AdminPagination from '../../components/admin/AdminPagination.vue';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const statusFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(6);

const comments = ref([...commentsData]);

const getPostTitle = (postId) => {
  const post = POSTS.find(p => p.id === postId);
  return post ? post.title : 'Unknown Post';
};

const filteredComments = computed(() => {
  return comments.value.filter(comment => {
    const postTitle = getPostTitle(comment.postId);
    const matchesSearch = comment.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         comment.content.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         postTitle.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = statusFilter.value === 'all' || comment.status === statusFilter.value;
    return matchesSearch && matchesStatus;
  });
});

const totalPages = computed(() => Math.ceil(filteredComments.value.length / itemsPerPage.value));

const paginatedComments = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredComments.value.slice(start, start + itemsPerPage.value);
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
              <div :class="['font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ comment.name }}</div>
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
              <span>{{ getPostTitle(comment.postId) }}</span>
            </div>
            <div class="flex items-center gap-1">
              <Clock size="14" />
              <span>{{ formatToShort(comment.date) }}</span>
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
    <AdminPagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredComments.length"
      v-model:items-per-page="itemsPerPage"
      @update:current-page="currentPage = $event"
    />
  </div>
</template>
