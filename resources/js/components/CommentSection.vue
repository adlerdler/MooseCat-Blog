<script setup>
/**
 * CommentSection.vue - 评论区组件
 *
 * 功能说明：
 * - 展示文章评论列表
 * - 提供评论提交表单
 * - 支持评论点赞功能
 * - 用户头像使用 AbstractAvatar 组件生成
 *
 * 时间显示规则：
 * - 1分钟内：显示 "just now"
 * - 1小时内：显示 "Xm ago"
 * - 24小时内：显示 "Xh ago"
 * - 2天内：显示 "Xd ago"
 * - 超过2天：显示完整日期
 *
 * 使用示例：
 * <CommentSection :comments="comments" :interactions="interactions" @comment-submitted="handleComment" />
 */
import { ref } from 'vue';
import axios from 'axios';
import { ChevronRight, Send, Heart } from 'lucide-vue-next';
import AbstractAvatar from './AbstractAvatar.vue';

const props = defineProps({
  postId: {
    type: Number,
    required: true
  },
  comments: {
    type: Array,
    default: () => []
  },
  interactions: {
    type: Array,
    default: () => []
  },
  currentUserId: {
    type: Number,
    default: null
  }
});

const emit = defineEmits(['comment-submitted']);

const isCommentFormOpen = ref(false);
const isSubmitting = ref(false);
const submitSuccess = ref(false);
const errorMessage = ref('');
const localInteractions = ref([...props.interactions]);

const localComments = ref([...props.comments]);

// 回复状态
const replyingToId = ref(null); // 当前正在回复的评论 ID
const replyFormData = ref({ name: '', email: '', body: '' });
const isReplySubmitting = ref(false);

const getLikeCount = (commentId) => {
  return localInteractions.value.filter(
    i => i.interactable_id === commentId && 
         i.interactable_type === 'Comment' && 
         i.type === 'like'
  ).length;
};

const isCommentLiked = (commentId) => {
  if (!props.currentUserId) return false;
  return localInteractions.value.some(
    i => i.user_id === props.currentUserId && 
         i.interactable_id === commentId && 
         i.interactable_type === 'Comment' && 
         i.type === 'like'
  );
};

const toggleLike = (commentId) => {
  if (!props.currentUserId) {
    errorMessage.value = 'Please login to like comments';
    return;
  }

  const existingIndex = localInteractions.value.findIndex(
    i => i.user_id === props.currentUserId && 
         i.interactable_id === commentId && 
         i.interactable_type === 'Comment' && 
         i.type === 'like'
  );

  if (existingIndex !== -1) {
    localInteractions.value.splice(existingIndex, 1);
  } else {
    const now = new Date().toISOString();
    const newId = Math.max(...localInteractions.value.map(i => i.id), 0) + 1;
    localInteractions.value.push({
      id: newId,
      user_id: props.currentUserId,
      interactable_id: commentId,
      interactable_type: 'Comment',
      type: 'like',
      created_at: now,
      updated_at: now
    });
  }
};

const formData = ref({
  name: '',
  email: '',
  body: ''
});

const formatRelativeTime = (dateString) => {
  const now = new Date();
  const commentDate = new Date(dateString);
  const diffMs = now - commentDate;
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMins / 60);
  const diffDays = Math.floor(diffHours / 24);

  if (diffMins < 1) return 'just now';
  if (diffMins < 60) return `${diffMins}m ago`;
  if (diffHours < 24) return `${diffHours}h ago`;
  if (diffDays < 2) return `${diffDays}d ago`;
  return commentDate.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const handleSubmit = async (e) => {
  e.preventDefault();
  errorMessage.value = '';
  submitSuccess.value = false;

  if (!formData.value.name.trim()) {
    errorMessage.value = 'Please enter your name';
    return;
  }
  if (!formData.value.email.trim()) {
    errorMessage.value = 'Please enter your email';
    return;
  }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email)) {
    errorMessage.value = 'Please enter a valid email address';
    return;
  }
  if (!formData.value.body.trim()) {
    errorMessage.value = 'Please enter your comment';
    return;
  }
  if (formData.value.body.trim().length < 10) {
    errorMessage.value = 'Comment must be at least 10 characters';
    return;
  }

  isSubmitting.value = true;

  try {
    const response = await axios.post(`/posts/${props.postId}/comments`, {
      name: formData.value.name.trim(),
      email: formData.value.email.trim(),
      body: formData.value.body.trim(),
    }, {
      headers: { 'X-Inertia': 'true' },
    });

    const newComment = response.data.comment;
    if (newComment && newComment.is_approved) {
      localComments.value = [newComment, ...localComments.value];
    }
    formData.value = { name: '', email: '', body: '' };
    isSubmitting.value = false;
    submitSuccess.value = true;
    emit('comment-submitted', newComment);
    setTimeout(() => {
      submitSuccess.value = false;
    }, 3000);
  } catch (err) {
    isSubmitting.value = false;
    if (err.response?.status === 422) {
      // 验证错误
      const errors = err.response.data.errors;
      const firstError = Object.values(errors).flat()[0];
      errorMessage.value = firstError || 'Validation failed.';
    } else if (err.response?.status === 403) {
      errorMessage.value = err.response.data.message || '评论功能已关闭。';
    } else {
      errorMessage.value = 'Submission failed. Please try again.';
    }
  }
};

// 展开/收起某条评论的回复表单
const toggleReplyForm = (commentId) => {
  if (replyingToId.value === commentId) {
    replyingToId.value = null;
    replyFormData.value = { name: '', email: '', body: '' };
  } else {
    replyingToId.value = commentId;
    replyFormData.value = { name: '', email: '', body: '' };
  }
};

// 提交回复（嵌套评论）
const handleReplySubmit = async (commentId) => {
  if (!replyFormData.value.body.trim()) return;
  if (replyFormData.value.body.trim().length < 2) return;

  isReplySubmitting.value = true;

  try {
    const response = await axios.post(`/posts/${props.postId}/comments`, {
      name: replyFormData.value.name.trim() || 'Anonymous',
      email: replyFormData.value.email.trim() || null,
      body: replyFormData.value.body.trim(),
      parent_id: commentId,
    }, {
      headers: { 'X-Inertia': 'true' },
    });

    const newReply = response.data.comment;
    if (newReply && newReply.is_approved) {
      // 找到父评论并追加回复
      const parentComment = localComments.value.find(c => c.id === commentId);
      if (parentComment) {
        if (!parentComment.replies) {
          parentComment.replies = [];
        }
        parentComment.replies.push(newReply);
      }
    }

    replyingToId.value = null;
    replyFormData.value = { name: '', email: '', body: '' };
    isReplySubmitting.value = false;
    emit('comment-submitted', newReply);
  } catch (err) {
    isReplySubmitting.value = false;
    if (err.response?.status === 422) {
      const errors = err.response.data.errors;
      errorMessage.value = Object.values(errors).flat()[0] || 'Validation failed.';
    } else {
      errorMessage.value = 'Reply failed. Please try again.';
    }
  }
};
</script>

<template>
  <div class="mt-16 pt-16">
    <!-- Comment Section Header -->
    <button
      @click="isCommentFormOpen = !isCommentFormOpen"
      class="flex items-center gap-4 mb-12 group cursor-pointer w-full text-left"
    >
      <div :class="['transition-transform duration-300', isCommentFormOpen ? 'rotate-90' : '']">
        <ChevronRight class="text-construct-red" size="24" />
      </div>
      <h3 class="font-display text-3xl tracking-tighter uppercase">TRANSMISSION_LOG</h3>
      <div class="flex-1 h-px bg-construct-black/10" />
      <span class="text-[10px] font-black tracking-widest opacity-40 group-hover:text-construct-red transition-colors">
        {{ isCommentFormOpen ? '[-] DISCONNECT' : '[+] CONNECT' }}
      </span>
    </button>

    <!-- Comment Form -->
    <Transition name="slide">
      <div v-if="isCommentFormOpen" class="overflow-hidden">
        <form @submit="handleSubmit" class="mb-24 space-y-8 bg-gray-100/50 p-6 md:p-10 border-2 border-construct-black">
          <!-- Success Message -->
          <Transition name="fade">
            <div v-if="submitSuccess" class="bg-green-100 border-2 border-green-500 text-green-700 px-4 py-3 text-sm font-bold tracking-widest uppercase flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              TRANSMISSION_RECEIVED // MESSAGE_DELIVERED
            </div>
          </Transition>

          <!-- Error Message -->
          <Transition name="fade">
            <div v-if="errorMessage" class="bg-red-100 border-2 border-red-500 text-red-700 px-4 py-3 text-sm font-bold tracking-widest uppercase">
              ERROR // {{ errorMessage }}
            </div>
          </Transition>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2">
              <label for="comment-name" class="text-[10px] font-black tracking-widest uppercase opacity-40">NAME //*</label>
              <input
                required
                type="text"
                id="comment-name"
                v-model="formData.name"
                placeholder="YOUR_NAME"
                class="w-full bg-gray-200 border-2 border-construct-black px-4 py-3 focus:border-construct-red outline-none transition-colors font-display text-sm tracking-widest uppercase placeholder:text-construct-black/30"
              />
            </div>
            <div class="space-y-2">
              <label for="comment-email" class="text-[10px] font-black tracking-widest uppercase opacity-40">EMAIL //*</label>
              <input
                required
                type="email"
                id="comment-email"
                v-model="formData.email"
                placeholder="YOUR_EMAIL"
                class="w-full bg-gray-200 border-2 border-construct-black px-4 py-3 focus:border-construct-red outline-none transition-colors font-display text-sm tracking-widest uppercase placeholder:text-construct-black/30"
              />
            </div>
          </div>
          <div class="space-y-2">
            <label for="comment-body" class="text-[10px] font-black tracking-widest uppercase opacity-40">TRANSMISSION //*</label>
            <textarea
              required
              id="comment-body"
              rows="6"
              v-model="formData.body"
              placeholder="ENTER_YOUR_TRANSMISSION..."
              class="w-full bg-gray-200 border-2 border-construct-black px-4 py-3 focus:border-construct-red outline-none border-t border-construct-black transition-colors resize-none font-medium text-lg leading-relaxed placeholder:text-construct-black/30"
            />
            <div class="flex justify-between items-center">
              <span class="text-[10px] font-black tracking-widest opacity-30">
                {{ formData.body.length }} CHARACTERS
              </span>
              <span class="text-[10px] font-black tracking-widest opacity-30">
                MIN: 10 // MAX: 2000
              </span>
            </div>
          </div>
          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="isSubmitting"
              class="group relative inline-flex items-center gap-3 bg-construct-black text-white px-8 py-4 font-display tracking-widest text-sm hover:bg-construct-red transition-all active:translate-x-1 active:translate-y-1 overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span class="relative z-10">{{ isSubmitting ? 'TRANSMITTING...' : 'SEND TRANSMISSION' }}</span>
              <Send :size="18" :class="['relative z-10 transition-transform', isSubmitting ? 'animate-spin' : 'group-hover:translate-x-1 group-hover:-translate-y-1']" />
              <div class="absolute top-0 left-0 w-full h-full bg-white/10 -translate-x-full group-hover:translate-x-0 transition-transform duration-500" />
            </button>
          </div>
        </form>
      </div>
    </Transition>

    <!-- Comment List -->
    <div class="space-y-8">
      <div v-if="localComments.length === 0" class="py-12 border-2 border-dashed border-construct-black/20 text-center">
        <span class="text-[10px] font-black tracking-[0.3em] uppercase opacity-30 italic">
          NO TRANSMISSIONS FOUND IN THIS SECTOR //
        </span>
      </div>
      <div
        v-for="(comment, index) in localComments"
        :key="comment.id"
        class="relative pl-12 border-l-4 border-construct-black group animate-fade-in"
        :style="{ animationDelay: `${index * 0.1}s` }"
      >
        <!-- Decorative Arrow -->
        <div class="absolute left-[-12px] top-0 w-5 h-5 bg-construct-red rotate-45 opacity-0 group-hover:opacity-100 transition-opacity" />
        
        <!-- Comment Header -->
        <div class="flex items-start gap-4 mb-4">
          <!-- Abstract Pattern Avatar -->
          <AbstractAvatar :seed="comment.name" :size="48" />
          
          <div class="flex-1">
            <h4 class="font-display text-xl tracking-tighter uppercase">{{ comment.name }}</h4>
            <span class="text-[10px] font-black tracking-widest uppercase opacity-30 block mt-1">
              {{ formatRelativeTime(comment.created_at || comment.date) }}
            </span>
          </div>

          <!-- Like Button -->
          <button 
            @click="toggleLike(comment.id)"
            :class="[
              'flex items-center gap-2 text-[10px] font-black tracking-widest transition-all cursor-pointer',
              isCommentLiked(comment.id)
                ? 'text-construct-red opacity-100'
                : 'opacity-40 hover:opacity-100 hover:text-construct-red'
            ]"
          >
            <Heart 
              :size="16" 
              :fill="isCommentLiked(comment.id) ? 'currentColor' : 'none'" 
              :stroke-width="2"
            />
            {{ getLikeCount(comment.id) }}
          </button>
        </div>

        <!-- Comment Body -->
        <div class="pl-16">
          <p class="text-lg leading-relaxed text-construct-black/80 max-w-2xl">
            {{ comment.body }}
          </p>
        </div>

        <!-- Admin / User Replies -->
        <div v-if="comment.replies && comment.replies.length > 0" class="pl-16 mt-6 space-y-4 border-l-2 border-dashed border-construct-black/20">
          <div
            v-for="reply in comment.replies"
            :key="reply.id"
            class="relative pl-8"
          >
            <!-- Decorative reply arrow -->
            <div class="absolute left-[-10px] top-2 w-4 h-4 bg-construct-black/10 rotate-45" />
            
            <!-- Reply Header -->
            <div class="flex items-start gap-3 mb-2">
              <AbstractAvatar :seed="reply.name" :size="32" />
              <div class="flex-1">
                <h5 class="font-display text-sm tracking-tighter uppercase flex items-center gap-2">
                  {{ reply.name }}
                  <!-- 作者标签 -->
                  <span
                    v-if="reply.is_admin"
                    class="inline-block text-[9px] font-black tracking-widest bg-construct-red text-white px-2 py-0.5"
                  >
                    作者
                  </span>
                </h5>
                <span class="text-[9px] font-black tracking-widest uppercase opacity-30 block mt-0.5">
                  {{ formatRelativeTime(reply.created_at) }}
                </span>
              </div>
            </div>

            <!-- Reply Body -->
            <div class="pl-11">
              <p class="text-sm leading-relaxed text-construct-black/70 max-w-2xl">
                {{ reply.body }}
              </p>
            </div>
          </div>
        </div>

        <!-- Reply Link -->
        <div class="pl-16 mt-4">
          <button
            @click="toggleReplyForm(comment.id)"
            :class="[
              'text-[10px] font-black tracking-widest transition-colors uppercase',
              replyingToId === comment.id
                ? 'text-construct-red opacity-100'
                : 'opacity-40 hover:opacity-100 hover:text-construct-red'
            ]"
          >
            {{ replyingToId === comment.id ? '[-] CANCEL_REPLY' : '[+] REPLY_TO_TRANSMISSION' }}
          </button>
        </div>

        <!-- Inline Reply Form -->
        <Transition name="slide">
          <div v-if="replyingToId === comment.id" class="pl-16 mt-4 overflow-hidden">
            <form @submit.prevent="handleReplySubmit(comment.id)" class="space-y-4 bg-gray-100/50 p-5 border-2 border-construct-red/30">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                  <label class="text-[9px] font-black tracking-widest uppercase opacity-40">NAME</label>
                  <input
                    type="text"
                    v-model="replyFormData.name"
                    placeholder="YOUR_NAME"
                    class="w-full bg-gray-200 border-2 border-construct-black px-3 py-2 focus:border-construct-red outline-none transition-colors font-display text-xs tracking-widest uppercase placeholder:text-construct-black/30"
                  />
                </div>
                <div class="space-y-1.5">
                  <label class="text-[9px] font-black tracking-widest uppercase opacity-40">EMAIL</label>
                  <input
                    type="email"
                    v-model="replyFormData.email"
                    placeholder="YOUR_EMAIL"
                    class="w-full bg-gray-200 border-2 border-construct-black px-3 py-2 focus:border-construct-red outline-none transition-colors font-display text-xs tracking-widest uppercase placeholder:text-construct-black/30"
                  />
                </div>
              </div>
              <div class="space-y-1.5">
                <label class="text-[9px] font-black tracking-widest uppercase opacity-40">REPLY //*</label>
                <textarea
                  required
                  rows="3"
                  v-model="replyFormData.body"
                  placeholder="ENTER_YOUR_REPLY..."
                  class="w-full bg-gray-200 border-2 border-construct-black px-3 py-2 focus:border-construct-red outline-none transition-colors resize-none font-medium text-sm leading-relaxed placeholder:text-construct-black/30"
                />
              </div>
              <div class="flex justify-end gap-3">
                <button
                  type="submit"
                  :disabled="isReplySubmitting"
                  class="group inline-flex items-center gap-2 bg-construct-black text-white px-5 py-2 font-display tracking-widest text-xs hover:bg-construct-red transition-all active:translate-x-0.5 active:translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span>{{ isReplySubmitting ? 'SENDING...' : 'SEND REPLY' }}</span>
                  <Send :size="14" :class="['transition-transform', isReplySubmitting ? 'animate-spin' : 'group-hover:translate-x-0.5 group-hover:-translate-y-0.5']" />
                </button>
              </div>
            </form>
          </div>
        </Transition>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
  overflow: hidden;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}
</style>
