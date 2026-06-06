<script setup>
/**
 * PostDetail.vue - 文章详情页
 * 
 * 功能说明：
 * - 展示单篇文章的完整内容
 * - 支持 Markdown 格式渲染，包含代码高亮
 * - 自动提取文章目录结构，方便导航
 * - 集成评论区功能
 * 
 * 交互功能：
 * - 分享按钮（支持多种平台）
 * - 书签收藏功能
 * - 返回顶部按钮
 * - 评论区评论提交
 */
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Share2, Bookmark, Clock, User, BookOpen, ChevronRight, Heart, ArrowRight } from 'lucide-vue-next';
import { Motion, AnimatePresence } from 'motion-v';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import { formatToEnglish } from '../../utils/dateUtils';
import { findById, formatId } from '../../utils/typeConvert';
import { getCategoryNameById } from '../../utils/categoryUtils';
import { useSiteConfig } from '../../composables/useSiteConfig';
import { usePageSeo } from '../../composables/usePageSeo';
import { useAdSlot } from '../../composables/useAdSlot';
import AdSlot from '@/components/front/AdSlot.vue';
import MarkdownRenderer from '@/components/MarkdownRenderer.vue';
import CommentSection from '@/components/CommentSection.vue';
import ShareModal from '@/components/ShareModal.vue';

const props = defineProps({
  post: { type: Object, default: null },
  categories: { type: Array, default: () => [] },
  authors: { type: Array, default: () => [] },
  comments: { type: Array, default: () => [] },
  interactions: { type: Array, default: () => [] }
});

const pageProps = usePage().props;
const { getSingleAd } = useAdSlot({ ads: pageProps.frontAds ?? [], adPositions: pageProps.frontAdPositions ?? [] });

const { t } = useI18n();

const likeStorageKey = computed(() => props.post ? `liked_post_${props.post.id}` : '');

const liked = ref(likeStorageKey.value ? localStorage.getItem(likeStorageKey.value) === '1' : false);
const likesCount = ref(props.post?.likes_count ?? 0);

const { isCommentsVisible, isAuthorBioVisible } = useSiteConfig();
const showComments = computed(() => isCommentsVisible());
const showAuthorBio = computed(() => isAuthorBioVisible());

const postComments = computed(() => {
  return props.comments.filter(comment => comment.is_approved && comment.post_id === props.post?.id);
});

const getAuthorName = (authorId) => {
  const user = props.authors.find(u => u.id === authorId);
  return user ? (user.penName || user.name) : 'Unknown';
};

const getAuthorSlug = (authorId) => {
  const user = props.authors.find(u => u.id === authorId);
  return user?.slug || '';
};

const inContentAd = computed(() => {
  return getSingleAd('in_content');
});

const { SeoHead } = usePageSeo({
  title: computed(() => props.post?.meta_title || (props.post?.title ? `${props.post.title} - ARCHYX` : 'ARCHYX')),
  description: computed(() => props.post?.meta_description || props.post?.excerpt || ''),
  keywords: computed(() => props.post?.meta_keywords || (props.post?.tags?.join(', ') || '')),
  image: computed(() => props.post?.cover_image || ''),
  url: computed(() => `${window.location.origin}/posts/${props.post?.slug}`),
  type: 'Article',
  author: computed(() => getAuthorName(props.post?.author_id)),
  publishedTime: computed(() => props.post?.published_at || ''),
})

const tableOfContents = computed(() => {
  if (!props.post?.content) return [];
  
  const headings = [];
  const lines = props.post.content.split('\n');
  
  lines.forEach(line => {
    const headingMatch = line.match(/^(#{1,3})\s+(.+)$/);
    if (headingMatch) {
      const level = headingMatch[1].length;
      const text = headingMatch[2].trim();
      const id = text.toLowerCase().replace(/[^a-z0-9]+/g, '-');
      headings.push({ level, text, id });
    }
  });
  
  return headings;
});

const showBackToTop = ref(false);
const isMetaOpen = ref(true);
const showShareModal = ref(false);
const showBookmarkTip = ref(false);
let bookmarkTimer = null;

const bookmarkPost = () => {
  const url = window.location.href;
  const title = document.title;

  // 国产浏览器 / IE：直接弹出收藏对话框
  if (window.external && typeof window.external.addFavorite === 'function') {
    try { window.external.addFavorite(url, title); return; } catch (_) {}
  }

  // 旧版 Firefox
  if (window.sidebar && typeof window.sidebar.addPanel === 'function') {
    try { window.sidebar.addPanel(title, url, ''); return; } catch (_) {}
  }

  // Chrome / Edge / Safari / 新版 Firefox：无 JS API，提示快捷键
  showBookmarkTip.value = true;
  clearTimeout(bookmarkTimer);
  bookmarkTimer = setTimeout(() => {
    showBookmarkTip.value = false;
  }, 2500);
};

const calculateReadTime = (content) => {
  const wordsPerMinute = 200;
  const words = content.trim().split(/\s+/).length;
  const minutes = Math.ceil(words / wordsPerMinute);
  return Math.max(1, minutes);
};

const handleScroll = () => {
  showBackToTop.value = window.scrollY > 300;
};

const goBack = () => {
  window.history.back();
};

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const scrollToHeading = (id) => {
  const element = document.getElementById(id);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
};

const copyCode = (button) => {
  const codeBlock = button.closest('.code-block-wrapper');
  const codeElement = codeBlock.querySelector('code');
  const codeText = codeElement.textContent;
  
  navigator.clipboard.writeText(codeText).then(() => {
    const copyText = button.querySelector('.copy-text');
    const originalText = copyText.textContent;
    copyText.textContent = 'Copied!';
    button.classList.add('copied');
    
    setTimeout(() => {
      copyText.textContent = originalText;
      button.classList.remove('copied');
    }, 2000);
  }).catch((err) => {
    console.error('Failed to copy code:', err);
  });
};

if (typeof window !== 'undefined') {
  window.copyCode = copyCode;
}

const handleCommentSubmitted = (comment) => {
  // 评论已通过 Inertia router.post() 提交到真实 API
  if (comment) {
    // Comment submitted successfully
  }
};

const togglePostLike = async () => {
  if (!props.post) return;
  try {
    const { data } = await axios.post('/likes/toggle', {
      interactable_type: 'App\\Models\\Post',
      interactable_id: props.post.id,
    });
    liked.value = data.liked;
    likesCount.value = data.likes_count;

    // 持久化到 localStorage，刷新后保留点赞状态
    if (data.liked) {
      localStorage.setItem(likeStorageKey.value, '1');
    } else {
      localStorage.removeItem(likeStorageKey.value);
    }
  } catch (e) {
    // 忽略错误
  }
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
  <SeoHead />
  <div v-if="!post" class="min-h-screen bg-construct-paper flex items-center justify-center p-8">
    <div class="text-center">
      <h1 class="font-display text-4xl md:text-6xl mb-8 tracking-tighter">DATA CORRUPTION</h1>
      <Link href="/blog" class="bg-construct-black text-white px-8 py-3 font-display tracking-widest text-sm hover:bg-construct-red transition-colors inline-block">
        {{ t('post_return_to_blog') }}
      </Link>
    </div>
  </div>

  <div v-else class="bg-construct-paper min-h-screen">
    <!-- Back Button -->
    <button
      @click="goBack"
      class="fixed top-4 left-4 z-50 w-12 h-12 bg-construct-black text-white flex items-center justify-center hover:bg-construct-red transition-colors shadow-lg"
    >
      <ArrowLeft class="w-6 h-6" />
    </button>

    <!-- Back to Top Button -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-75"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-75"
    >
      <button
        v-if="showBackToTop"
        @click="scrollToTop"
        class="fixed bottom-4 right-4 z-50 w-12 h-12 bg-construct-red text-white flex items-center justify-center hover:bg-construct-black transition-colors shadow-lg"
      >
        <ChevronRight class="w-6 h-6 rotate-[-90deg]" />
      </button>
    </Transition>

    <!-- Header Panel -->
    <header :class="['relative py-24 md:py-32 px-8 overflow-hidden', post.color === 'red' ? 'bg-construct-red text-white' : 'bg-construct-black text-white']">
      <div class="absolute right-0 bottom-0 w-1/2 h-1/2 bg-white/10" />
      
      <div class="container mx-auto relative z-10 md:pl-24">
        <div class="max-w-4xl">
          <Motion
            :initial="{ opacity: 0, y: -30 }"
            :animate="{ opacity: 0.7, y: 0 }"
            :transition="{ type: 'spring', damping: 25, stiffness: 200 }"
          >
            <div class="text-xs font-bold tracking-[0.4em] mb-4 opacity-70">
              FILE: {{ getCategoryNameById(categories, post.category_id) }} // ID: {{ formatId(post.id, 4) }}
            </div>
          </Motion>
          
          <Motion
            :initial="{ opacity: 0, y: -30 }"
            :animate="{ opacity: 1, y: 0 }"
            :transition="{ type: 'spring', damping: 25, stiffness: 200, delay: 0.1 }"
          >
            <div class="font-display text-4xl sm:text-5xl md:text-6xl lg:text-8xl leading-[0.9] tracking-tighter mb-8">
              {{ post.title }}
            </div>
          </Motion>

          <Motion
            :initial="{ opacity: 0, x: -20 }"
            :animate="{ opacity: 0.8, x: 0 }"
            :transition="{ duration: 0.4, delay: 0.2 }"
          >
            <div class="flex flex-wrap gap-8 items-center text-[10px] sm:text-xs font-bold tracking-[0.2em] opacity-80">
            <div class="flex items-center gap-2">
              <User size="14" class="text-white/40" />
              <Link
                v-if="showAuthorBio"
                :href="getAuthorSlug(post.author_id) ? `/author/${getAuthorSlug(post.author_id)}` : '#'"
                class="hover:underline"
              >{{ getAuthorName(post.author_id) }}</Link>
              <span v-else class="opacity-70">{{ getAuthorName(post.author_id) }}</span>
            </div>
            <div class="flex items-center gap-2">
              <Clock size="14" class="text-white/40" />
              {{ formatToEnglish(post.published_at) }}
            </div>
            <div class="flex items-center gap-2">
              <BookOpen size="14" class="text-white/40" />
              {{ calculateReadTime(post.content || '') }} MIN READ
            </div>
            <div class="h-4 w-[1px] bg-white/20 hidden sm:block" />
            <div class="flex flex-wrap gap-2">
              <span
                v-for="tag in post.tags"
                :key="tag"
                class="text-[10px] font-black tracking-widest px-2 py-1 bg-white/10 hover:bg-white/20 transition-colors cursor-default"
              >
                #{{ tag }}
              </span>
            </div>
            <div class="h-4 w-[1px] bg-white/20 hidden sm:block" />
            <div class="flex gap-4">
              <button @click="showShareModal = true" class="hover:text-construct-black transition-colors"><Share2 size="16" /></button>
              <button 
                @click="togglePostLike" 
                :class="['flex items-center gap-1 transition-colors', liked ? 'text-construct-red' : 'hover:text-construct-black']"
              >
                <Heart size="16" :fill="liked ? 'currentColor' : 'none'" :stroke-width="2" />
                <span class="text-[10px] font-bold">{{ likesCount }}</span>
              </button>
              <button
                @click="bookmarkPost"
                class="hover:text-construct-black transition-colors relative"
              >
                <Bookmark size="16" />
                <AnimatePresence>
                  <Motion
                    v-if="showBookmarkTip"
                    :initial="{ opacity: 0, y: 4 }"
                    :animate="{ opacity: 1, y: 0 }"
                    :exit="{ opacity: 0, y: 4 }"
                  >
                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-construct-black text-white text-[10px] px-2 py-1 rounded pointer-events-none">
                      Ctrl+D 收藏
                    </span>
                  </Motion>
                </AnimatePresence>
              </button>
            </div>
            </div>
          </Motion>
        </div>
      </div>
    </header>

    <!-- Content Section -->
    <article class="container mx-auto px-4 sm:px-8 py-16 md:py-32 grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12">
      <!-- 左侧：文章目录 -->
      <aside class="md:col-span-2 space-y-8 md:space-y-12 order-1">
        <div class="relative md:sticky md:top-24">
          <div class="mb-6">
            <h4 class="font-display text-sm tracking-widest text-construct-black mb-4 uppercase">{{ t('post_contents') }}</h4>
            <nav class="space-y-2">
              <div
                v-for="heading in tableOfContents"
                :key="heading.id"
                @click="scrollToHeading(heading.id)"
                :class="[
                  'cursor-pointer transition-all hover:text-construct-red',
                  {
                    'pl-0 text-sm font-bold': heading.level === 1,
                    'pl-2 text-xs font-medium': heading.level === 2,
                    'pl-4 text-[11px] opacity-70': heading.level === 3
                  }
                ]"
              >
                {{ heading.text }}
              </div>
            </nav>
          </div>
        </div>
      </aside>

      <!-- 中间：文章内容 -->
      <div class="md:col-span-8 max-w-4xl order-2">
        <div class="prose prose-lg font-medium text-lg md:text-xl leading-relaxed text-construct-black space-y-8">
          <MarkdownRenderer :content="post.content" :in-content-ad="inContentAd" />
        </div>

        <div class="mt-24 pt-16 border-t-4 border-construct-black">
          <h4 class="font-display text-2xl mb-8 tracking-tighter">{{ t('post_end_transmission') }}</h4>
        </div>

        <!-- Author Bio Section -->
        <div v-if="showAuthorBio" class="mt-16 pt-16 border-t-4 border-construct-black">
          <div class="flex items-start gap-6">
            <div class="w-16 h-16 bg-construct-black flex items-center justify-center flex-shrink-0">
              <User size="32" class="text-white" />
            </div>
            <div class="flex-1">
              <h4 class="font-display text-xl tracking-tighter uppercase mb-2">
                {{ t('author_title') }} {{ getAuthorName(post.author_id) }}
              </h4>
              <p class="text-sm leading-relaxed text-construct-black/70">
                {{ t('author_bio') }}
              </p>
              <Link 
                :href="getAuthorSlug(post.author_id) ? `/author/${getAuthorSlug(post.author_id)}` : '#'" 
                class="inline-flex items-center gap-2 mt-4 text-[10px] font-black tracking-widest uppercase text-construct-red hover:underline"
              >
                {{ t('read_more') || 'VIEW_FULL_PROFILE' }} <ArrowRight size="12" />
              </Link>
            </div>
          </div>
        </div>

        <!-- Comment Section -->
        <CommentSection
          v-if="showComments"
          :post-id="post.id"
          :comments="postComments"
          :interactions="interactions"
          :current-user-id="1" 
          @comment-submitted="handleCommentSubmitted" 
        />
      </div>

      <!-- 右侧：文章元数据 -->
      <aside class="md:col-span-2 space-y-8 md:space-y-12 order-3">
        <!-- Sidebar Ad -->
        <AdSlot position="sidebar" class="mb-8" />

        <div class="relative md:sticky md:top-24">
          <div class="mb-4">
            <button
              @click="isMetaOpen = !isMetaOpen"
              class="group flex items-center justify-between w-full bg-construct-black text-white px-4 py-2 text-[10px] font-black tracking-widest uppercase hover:bg-construct-red transition-colors"
              :title="isMetaOpen ? 'Retract details' : 'Expand details'"
            >
              <span>{{ isMetaOpen ? '[-] RETRACT_METADATA' : '[+] EXPAND_METADATA' }}</span>
              <div :class="['transition-transform duration-300', isMetaOpen ? 'rotate-90' : '']">
                <ChevronRight size="14" />
              </div>
            </button>
          </div>

          <div
            v-show="isMetaOpen"
            class="overflow-hidden space-y-6 transition-all duration-300"
            :style="{
              opacity: isMetaOpen ? 1 : 0,
              transform: isMetaOpen ? 'translateX(0)' : 'translateX(20px)'
            }"
          >
            <div class="p-6 bg-white border-4 border-construct-black">
              <h4 class="font-display text-sm tracking-widest text-construct-red mb-4 uppercase">ABSTRACT //</h4>
              <p class="text-xs font-medium leading-relaxed italic opacity-80">
                {{ post.excerpt }}
              </p>
            </div>

            <div class="p-6 border-2 border-construct-black bg-gray-100">
              <h4 class="font-display text-sm tracking-widest text-construct-black mb-4 uppercase">METADATA // TAGS</h4>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="tag in post.tags"
                  :key="tag"
                  class="text-[9px] font-black tracking-tighter px-2 py-1 bg-construct-black text-white hover:bg-construct-red transition-colors cursor-pointer uppercase"
                >
                  {{ tag }}
                </span>
              </div>
            </div>
          </div>
          
          <div class="space-y-4 py-6">
            <span class="text-[10px] font-bold tracking-widest opacity-40 block uppercase">ENCRYPTION: AES-256</span>
            <span class="text-[10px] font-bold tracking-widest opacity-40 block uppercase">STATUS: PUBLIC ARCHIVE</span>
            <span class="text-[10px] font-bold tracking-widest opacity-40 block uppercase">TRANSMISSION: FIXED RATE</span>
          </div>
        </div>
      </aside>
    </article>

    <!-- Decorative Footer -->
    <footer class="h-64 bg-construct-black flex items-center justify-center overflow-hidden relative">
      <div class="absolute inset-0 opacity-10 pointer-events-none flex flex-col justify-around">
        <div v-for="i in 5" :key="i" class="h-px bg-white w-full" />
      </div>
      <span class="font-display text-[20vw] opacity-5 text-white pointer-events-none select-none">
        CONSTRUCT
      </span>
    </footer>

    <!-- Share Modal -->
    <ShareModal 
      :show="showShareModal" 
      :title="post.title"
      @close="showShareModal = false" 
    />
  </div>
</template>

<style lang="scss" scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}

:deep(.markdown-body) {
  h1, h2, h3, h4, h5, h6 {
    font-family: 'Space Grotesk', system-ui, sans-serif;
    font-weight: 900;
    letter-spacing: -0.025em;
    margin-top: 2rem;
    margin-bottom: 1rem;
    text-transform: uppercase;
  }

  h1 { font-size: 2.5rem; }
  h2 { font-size: 2rem; }
  h3 { font-size: 1.5rem; }
  h4 { font-size: 1.25rem; }

  p {
    margin-bottom: 1.5rem;
    line-height: 1.8;
  }

  ul, ol {
    margin-bottom: 1.5rem;
    padding-left: 1.5rem;
  }

  li {
    margin-bottom: 0.5rem;
    line-height: 1.8;
  }

  strong {
    font-weight: 900;
  }

  a {
    color: inherit;
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 3px;
  }

  a:hover {
    color: #ef4444;
  }

  blockquote {
    border-left: 4px solid #000;
    padding-left: 1.5rem;
    font-style: italic;
    margin: 1.5rem 0;
  }

  code {
    font-family: 'JetBrains Mono', 'Fira Code', 'SF Mono', Consolas, monospace;
    background: #2d2d2d;
    color: #ccc;
    padding: 0.2rem 0.4rem;
    font-size: 0.875em;
    border-radius: 4px;
  }
}
</style>