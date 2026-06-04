<script setup>
/**
 * Comment Management Page
 * 
 * Features:
 * - Comment list display with pagination
 * - Search and filter functionality
 * - Comment approval/rejection
 * - Reply to comments
 * - Delete comments with confirmation
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import {
  MessageSquare,
  Search,
  Trash2,
  Filter,
  Check,
  X,
  User,
  Clock,
  Reply,
  FileText,
  Send,
  CornerDownRight,
  ShieldCheck,
  AlertCircle,
  ConfirmDialog,
  Pagination,
  SearchFilterModal
} from '../../composables/useAdminImports';
import { useTheme } from '../../composables/useTheme';
import { useToast } from '../../composables/useToast';
import { formatToShort } from '../../utils/dateUtils';
import { Motion, AnimatePresence } from 'motion-v';

const props = defineProps({
  comments: { type: Array, default: () => [] },
  posts: { type: Array, default: () => [] },
});

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success: toastSuccess, error: toastError } = useToast();

const searchQuery = ref('');
const statusFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(6);

const showDeleteConfirm = ref(false);
const commentToDelete = ref(null);

const replyingToId = ref(null);
const replyContent = ref('');

const comments = computed(() => props.comments || []);

const getPostTitle = (postId) => {
  const post = (props.posts || []).find(p => p.id === postId);
  return post ? post.title : 'Unknown Post';
};

const filteredComments = computed(() => {
  return comments.value.filter(comment => {
    const postTitle = getPostTitle(comment.post_id);
    const commentName = comment.name || '';
    const commentBody = comment.body || '';
    const matchesSearch = commentName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         commentBody.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         postTitle.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = statusFilter.value === 'all' || 
                          (statusFilter.value === 'approved' && comment.is_approved) ||
                          (statusFilter.value === 'pending' && !comment.is_approved);
    return matchesSearch && matchesStatus;
  });
});

const totalPages = computed(() => Math.ceil(filteredComments.value.length / itemsPerPage.value));

const paginatedComments = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredComments.value.slice(start, start + itemsPerPage.value);
});

const getStatusColor = (isApproved) => {
  return isApproved 
    ? 'text-green-500 bg-green-500/10 border-green-500/20' 
    : 'text-amber-500 bg-amber-500/10 border-amber-500/20';
};

const getStatusLabel = (isApproved) => {
  return isApproved ? t('admin_approved') : t('admin_pending');
};

const approveComment = (comment) => {
  router.put(`/admin/comments/${comment.id}`, { is_approved: true }, {
    preserveState: true,
    onSuccess: () => {
      comment.is_approved = true;
      toastSuccess(t('admin_comment_approved') || 'Comment approved');
    },
    onError: () => {
      toastError(t('admin_update_failed') || 'Approval failed');
    },
  });
};

const rejectComment = (comment) => {
  router.put(`/admin/comments/${comment.id}`, { is_approved: false }, {
    preserveState: true,
    onSuccess: () => {
      comment.is_approved = false;
      toastSuccess(t('admin_comment_rejected') || 'Comment rejected');
    },
    onError: () => {
      toastError(t('admin_update_failed') || 'Rejection failed');
    },
  });
};

const handleDeleteClick = (comment) => {
  commentToDelete.value = comment;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (commentToDelete.value) {
    router.delete(`/admin/comments/${commentToDelete.value.id}`, {
      preserveState: true,
      onSuccess: () => {
        comments.value = comments.value.filter(c => c.id !== commentToDelete.value.id);
        commentToDelete.value = null;
        toastSuccess(t('admin_comment_deleted') || 'Comment deleted');
      },
      onError: () => {
        toastError(t('admin_delete_failed') || 'Delete failed');
      },
    });
  }
  showDeleteConfirm.value = false;
};

const toggleReply = (id) => {
  if (replyingToId.value === id) {
    replyingToId.value = null;
    replyContent.value = '';
  } else {
    replyingToId.value = id;
    replyContent.value = '';
  }
};

const submitReply = (comment) => {
  if (!replyContent.value.trim()) return;

  router.post(`/admin/comments/${comment.id}/reply`, {
    body: replyContent.value,
  }, {
    preserveState: true,
    onSuccess: () => {
      replyingToId.value = null;
      replyContent.value = '';
      toastSuccess(t('admin_reply_sent') || 'Reply sent');
      // 刷新页面以加载包含回复的最新数据
      router.reload({ only: ['comments'], preserveState: true, preserveScroll: true });
    },
    onError: (errors) => {
      toastError(Object.values(errors).flat()[0] || t('admin_reply_failed') || 'Reply failed');
    },
  });
};

// 删除单条回复
const deleteReply = (comment, reply) => {
  if (!confirm('确定要删除这条回复吗？')) return;

  router.delete(`/admin/comments/${reply.id}`, {
    preserveState: true,
    onSuccess: () => {
      // 从本地 replies 数组中移除
      comment.replies = comment.replies.filter(r => r.id !== reply.id);
      toastSuccess(t('admin_reply_deleted') || 'Reply deleted');
    },
    onError: () => {
      toastError(t('admin_delete_failed') || 'Delete failed');
    },
  });
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
            <MessageSquare class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_comments') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Control user discussions</p>
        </div>
      </div>
    </div>

    <!-- Search and Filter -->
    <SearchFilterModal
      v-model:search-query="searchQuery"
      :search-placeholder="t('admin_search_comments')"
      :filters="[
        {
          key: 'status',
          options: [
            { value: 'all', label: t('admin_all_status') },
            { value: 'approved', label: t('admin_approved') },
            { value: 'pending', label: t('admin_pending') },
            { value: 'spam', label: t('admin_spam') }
          ]
        }
      ]"
      :filter-values="{ status: statusFilter }"
      @filter-change="handleFilterChange"
    />

    <!-- Comments List -->
    <div class="space-y-6">
      <div 
        v-for="comment in paginatedComments" 
        :key="comment.id"
        :class="[
          'relative border p-6 transition-all duration-300 rounded-2xl group',
          isDarkMode 
            ? 'bg-gray-800/40 border-gray-700/50 hover:border-construct-red/30 backdrop-blur-sm' 
            : 'bg-white border-gray-200 hover:border-construct-red/20 shadow-sm hover:shadow-md'
        ]"
      >
        <!-- Status Indicator Strip -->
        <div 
          class="absolute left-0 top-6 bottom-6 w-1 rounded-r-full transition-all duration-300"
          :class="comment.is_approved ? 'bg-green-500' : 'bg-amber-500'"
        ></div>

        <div class="flex items-start justify-between mb-6 pl-2">
          <div class="flex items-center gap-4">
            <div :class="[
              'w-12 h-12 rounded-2xl flex items-center justify-center transition-transform duration-500 group-hover:scale-110 shadow-lg',
              isDarkMode ? 'bg-gray-700 text-construct-red' : 'bg-gray-100 text-construct-red'
            ]">
              <User size="24" />
            </div>
            <div>
              <div :class="['font-display text-lg tracking-tight', isDarkMode ? 'text-white' : 'text-gray-900']">{{ comment.name }}</div>
              <div :class="['text-xs font-medium opacity-60', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ comment.email }}</div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <div :class="['px-3 py-1 rounded-lg text-[10px] font-black tracking-widest uppercase border', getStatusColor(comment.is_approved)]">
              {{ getStatusLabel(comment.is_approved) }}
            </div>
          </div>
        </div>
        
        <div :class="['mb-6 p-5 rounded-2xl border-l-4 ml-2 leading-relaxed', isDarkMode ? 'bg-gray-900/30 border-gray-700 text-gray-300' : 'bg-gray-50 border-gray-100 text-gray-700 shadow-inner']">
          <p class="text-sm italic opacity-90">{{ comment.body }}</p>
        </div>

        <!-- Replies Section -->
        <div v-if="comment.replies && comment.replies.length > 0" class="ml-10 mb-6 space-y-4 border-l-2 border-dashed border-gray-200 dark:border-gray-700 pl-6">
          <div v-for="reply in comment.replies" :key="reply.id" class="relative">
            <CornerDownRight class="absolute -left-7 top-0 text-gray-300 dark:text-gray-600" size="18" />
            <div :class="['p-4 rounded-xl text-sm', reply.is_admin ? (isDarkMode ? 'bg-construct-red/10 border border-construct-red/20' : 'bg-red-50 border border-red-100') : (isDarkMode ? 'bg-gray-700/30' : 'bg-white')]">
              <div class="flex items-center gap-2 mb-1">
                <ShieldCheck v-if="reply.is_admin" size="14" class="text-construct-red" />
                <span :class="['font-bold', reply.is_admin ? 'text-construct-red' : (isDarkMode ? 'text-white' : 'text-gray-900')]">{{ reply.name }}</span>
                <span class="text-[10px] opacity-40 uppercase tracking-widest font-black ml-auto">{{ formatToShort(reply.created_at) }}</span>
                <button
                  @click="deleteReply(comment, reply)"
                  class="ml-2 p-1 rounded opacity-30 hover:opacity-100 hover:text-red-500 transition-all"
                  title="删除回复"
                >
                  <Trash2 size="12" />
                </button>
              </div>
              <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">{{ reply.body }}</p>
            </div>
          </div>
        </div>
        
        <div :class="['flex flex-col md:flex-row md:items-center justify-between pt-5 border-t gap-4 ml-2', isDarkMode ? 'border-gray-700/50' : 'border-gray-100']">
          <div :class="['flex items-center gap-6 text-[10px] font-black tracking-widest uppercase', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
            <div class="flex items-center gap-2">
              <FileText size="14" class="text-construct-red" />
              <span class="truncate max-w-[200px]">{{ getPostTitle(comment.post_id) }}</span>
            </div>
            <div class="flex items-center gap-2">
              <Clock size="14" class="text-construct-red" />
              <span>{{ formatToShort(comment.created_at || comment.date) }}</span>
            </div>
          </div>
          
          <div class="flex items-center gap-2 shrink-0">
            <button
              v-if="!comment.is_approved"
              @click="approveComment(comment)"
              :class="['flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black tracking-widest uppercase transition-all hover:scale-105 active:scale-95', isDarkMode ? 'bg-green-500/10 text-green-400 border border-green-500/20 hover:bg-green-500/20' : 'bg-green-50 text-green-600 border border-green-100 hover:bg-green-100']"
            >
              <Check size="14" /> {{ t('admin_approve') }}
            </button>
            <button
              v-if="comment.is_approved"
              @click="rejectComment(comment)"
              :class="['flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black tracking-widest uppercase transition-all hover:scale-105 active:scale-95', isDarkMode ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/20' : 'bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-100']"
            >
              <AlertCircle size="14" /> {{ t('admin_reject') }}
            </button>
            <button 
              @click="toggleReply(comment.id)"
              :class="['flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black tracking-widest uppercase transition-all hover:scale-105 active:scale-95', replyingToId === comment.id ? 'bg-construct-red text-white shadow-lg shadow-construct-red/20' : (isDarkMode ? 'bg-gray-700 text-gray-300 border border-gray-600' : 'bg-gray-100 text-gray-700 border border-gray-200')]">
              <Reply size="14" /> {{ t('admin_reply') }}
            </button>
            <button 
              @click="handleDeleteClick(comment)"
              :class="['p-2 rounded-xl transition-all duration-300 hover:scale-110', isDarkMode ? 'text-gray-500 hover:bg-red-500/10 hover:text-red-400' : 'text-gray-400 hover:bg-red-50 hover:text-red-600']"
            >
              <Trash2 size="18" />
            </button>
          </div>
        </div>

        <!-- Inline Reply Input -->
        <AnimatePresence>
          <Motion
            v-if="replyingToId === comment.id"
            :initial="{ height: 0, opacity: 0, marginTop: 0 }"
            :animate="{ height: 'auto', opacity: 1, marginTop: 24 }"
            :exit="{ height: 0, opacity: 0, marginTop: 0 }"
            class="overflow-hidden"
          >
            <div :class="['p-4 rounded-2xl border-2 border-dashed ml-2', isDarkMode ? 'bg-gray-900/50 border-gray-700' : 'bg-white border-gray-100 shadow-inner']">
              <div class="flex gap-4">
                <textarea
                  v-model="replyContent"
                  rows="3"
                  :placeholder="t('admin_reply_placeholder')"
                  :class="[
                    'flex-1 p-4 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-construct-red/50 transition-all resize-none',
                    isDarkMode ? 'bg-gray-800 text-white' : 'bg-gray-50 text-gray-900'
                  ]"
                ></textarea>
              </div>
              <div class="flex justify-end gap-3 mt-4">
                <button 
                  @click="toggleReply(comment.id)"
                  :class="['px-6 py-2 text-[10px] font-black tracking-widest uppercase rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-800' : 'text-gray-500 hover:bg-gray-100']">
                  {{ t('admin_cancel') }}
                </button>
                <button 
                  @click="submitReply(comment)"
                  :disabled="!replyContent.trim()"
                  class="px-8 py-2 bg-construct-red text-white text-[10px] font-black tracking-widest uppercase rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-construct-red/20 flex items-center gap-2"
                >
                  <Send size="14" /> {{ t('admin_send_reply') }}
                </button>
              </div>
            </div>
          </Motion>
        </AnimatePresence>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
      <Pagination
        :current-page="currentPage"
        :total-pages="totalPages"
        :total-items="filteredComments.length"
        v-model:items-per-page="itemsPerPage"
        @update:current-page="currentPage = $event"
      />
    </div>

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="showDeleteConfirm"
      type="delete"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 10px;
}
</style>
