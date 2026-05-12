<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import { ArrowLeft, Share2, Bookmark, Clock, User, BookOpen, Send, ChevronRight } from 'lucide-vue-next';
import { marked } from 'marked';
import { POSTS } from '../data/posts';

const route = useRoute();
const router = useRouter();

const post = computed(() => {
  const foundPost = POSTS.find(p => p.id === route.params.id);
  if (!foundPost) {
    return POSTS[0]; // 返回第一篇文章作为默认值
  }
  return foundPost;
});

// 提取文章三级标题
const tableOfContents = computed(() => {
  if (!post.value?.content) return [];
  
  const headings = [];
  const lines = post.value.content.split('\n');
  
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

const renderedContent = computed(() => {
  if (!post.value?.content) return '';
  
  // 先获取原始 HTML
  let html = marked(post.value.content);
  
  // 然后手动添加 id 属性给标题
  html = html.replace(/<h([1-3])>([^<]*)<\/h\1>/g, (match, level, text) => {
    const id = text.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-');
    return `<h${level} id="${id}">${text}</h${level}>`;
  });
  
  return html;
});

function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return text.replace(/[&<>"']/g, m => map[m]);
}

const showBackToTop = ref(false);
const isCommentFormOpen = ref(false);
const isMetaOpen = ref(true);
const showShareModal = ref(false);

const comments = ref([
  {
    id: '1',
    name: 'Visitor',
    email: 'visitor@example.com',
    body: 'Interesting perspective on cognitive architecture! The grid analogy really resonates.',
    date: 'May 10, 2026, 14:30'
  }
]);

const formData = ref({
  name: '',
  email: '',
  body: ''
});

const calculateReadTime = (content) => {
  const wordsPerMinute = 200;
  const words = content.trim().split(/\s+/).length;
  const minutes = Math.ceil(words / wordsPerMinute);
  return Math.max(1, minutes);
};

const handleSubmit = (e) => {
  e.preventDefault();
  if (!formData.value.name || !formData.value.email || !formData.value.body) return;

  const newComment = {
    id: Date.now().toString(),
    name: formData.value.name,
    email: formData.value.email,
    body: formData.value.body,
    date: new Date().toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  };

  comments.value = [newComment, ...comments.value];
  formData.value = { name: '', email: '', body: '' };
};

const handleScroll = () => {
  showBackToTop.value = window.scrollY > 300;
};

const goBack = () => {
  router.back();
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

const copyLink = async () => {
  const url = window.location.href;
  try {
    await navigator.clipboard.writeText(url);
    alert('链接已复制到剪贴板！');
  } catch (err) {
    const textArea = document.createElement('textarea');
    textArea.value = url;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
    alert('链接已复制到剪贴板！');
  }
};

const shareToX = () => {
  const url = encodeURIComponent(window.location.href);
  const text = encodeURIComponent(`${post.value.title}`);
  window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
};

const shareToInstagram = () => {
  alert('Instagram分享功能需要使用原生应用');
};

const shareToWeChatMoments = () => {
  alert('朋友圈分享功能需要使用微信原生应用');
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});


</script>

<template>
  <div v-if="!post" class="min-h-screen bg-construct-paper flex items-center justify-center p-8">
    <div class="text-center">
      <h1 class="font-display text-4xl md:text-6xl mb-8 tracking-tighter">DATA CORRUPTION</h1>
      <RouterLink to="/blog" class="bg-construct-black text-white px-8 py-3 font-display tracking-widest text-sm hover:bg-construct-red transition-colors inline-block">
        RETURN TO BLOG
      </RouterLink>
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
    <Transition name="fade">
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
          <div class="text-xs font-bold tracking-[0.4em] mb-4 opacity-70 animate-slide-down">
            FILE: {{ post.category }} // ID: {{ post.id.padStart(4, '0') }}
          </div>
          
          <div class="font-display text-4xl sm:text-5xl md:text-6xl lg:text-8xl leading-[0.9] tracking-tighter mb-8 animate-slide-down" style="animation-delay: 0.1s">
            {{ post.title }}
          </div>

          <div class="flex flex-wrap gap-8 items-center text-[10px] sm:text-xs font-bold tracking-[0.2em] opacity-80 animate-fade-in" style="animation-delay: 0.2s">
            <div class="flex items-center gap-2">
              <User size="14" class="text-white/40" />
              <RouterLink :to="`/author/${encodeURIComponent(post.author)}`" class="hover:underline">{{ post.author }}</RouterLink>
            </div>
            <div class="flex items-center gap-2">
              <Clock size="14" class="text-white/40" />
              {{ post.date }}
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
              <button class="hover:text-construct-black transition-colors"><Bookmark size="16" /></button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Content Section -->
    <article class="container mx-auto px-4 sm:px-8 py-16 md:py-32 grid grid-cols-1 md:grid-cols-12 gap-12 md:gap-16">
      <!-- 左侧：文章目录 -->
      <aside class="md:col-span-3 space-y-8 md:space-y-12 order-1">
        <div class="relative md:sticky md:top-24">
          <div class="mb-6">
            <h4 class="font-display text-sm tracking-widest text-construct-black mb-4 uppercase">CONTENTS //</h4>
            <nav class="space-y-2">
              <div
                v-for="heading in tableOfContents"
                :key="heading.id"
                @click="scrollToHeading(heading.id)"
                :class="[
                  'cursor-pointer transition-all hover:text-construct-red',
                  {
                    'pl-0 text-sm font-bold': heading.level === 1,
                    'pl-3 text-xs font-medium': heading.level === 2,
                    'pl-6 text-[11px] opacity-70': heading.level === 3
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
      <div class="md:col-span-6 max-w-2xl order-2">
        <div class="prose prose-lg font-medium text-lg md:text-xl leading-relaxed text-construct-black space-y-8">
          <div v-html="renderedContent" class="markdown-body"></div>
        </div>

        <div class="mt-24 pt-16 border-t-4 border-construct-black">
          <h4 class="font-display text-2xl mb-8 tracking-tighter">END OF TRANSMISSION //</h4>
        </div>

        <!-- Comment Section -->
        <div class="mt-16 pt-16">
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

          <Transition name="slide">
            <div v-if="isCommentFormOpen" class="overflow-hidden">
              <form @submit="handleSubmit" class="mb-24 space-y-8 bg-gray-100/50 p-6 md:p-10 border-2 border-construct-black">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div class="space-y-2">
                    <label for="name" class="text-[10px] font-black tracking-widest uppercase opacity-40">NAME //*</label>
                    <input
                      required
                      type="text"
                      id="name"
                      v-model="formData.name"
                      class="w-full bg-gray-200 border-2 border-construct-black px-4 py-3 focus:border-construct-red outline-none transition-colors font-display text-sm tracking-widest uppercase"
                    />
                  </div>
                  <div class="space-y-2">
                    <label for="email" class="text-[10px] font-black tracking-widest uppercase opacity-40">EMAIL //*</label>
                    <input
                      required
                      type="email"
                      id="email"
                      v-model="formData.email"
                      class="w-full bg-gray-200 border-2 border-construct-black px-4 py-3 focus:border-construct-red outline-none transition-colors font-display text-sm tracking-widest uppercase"
                    />
                  </div>
                </div>
                <div class="space-y-2">
                  <label for="body" class="text-[10px] font-black tracking-widest uppercase opacity-40">TRANSMISSION //*</label>
                  <textarea
                    required
                    id="body"
                    rows="6"
                    v-model="formData.body"
                    class="w-full bg-gray-200 border-2 border-construct-black px-4 py-3 focus:border-construct-red outline-none border-t border-construct-black transition-colors resize-none font-medium text-lg leading-relaxed"
                  />
                </div>
                <div class="flex justify-end">
                  <button
                    type="submit"
                    class="group relative inline-flex items-center gap-3 bg-construct-black text-white px-8 py-4 font-display tracking-widest text-sm hover:bg-construct-red transition-all active:translate-x-1 active:translate-y-1 overflow-hidden"
                  >
                    <span class="relative z-10">SEND TRANSMISSION</span>
                    <Send size="18" class="relative z-10 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
                    <div class="absolute top-0 left-0 w-full h-full bg-white/10 -translate-x-full group-hover:translate-x-0 transition-transform duration-500" />
                  </button>
                </div>
              </form>
            </div>
          </Transition>

          <!-- Comment List -->
          <div class="space-y-12">
            <div v-if="comments.length === 0" class="py-12 border-2 border-dashed border-construct-black/20 text-center">
              <span class="text-[10px] font-black tracking-[0.3em] uppercase opacity-30 italic">
                NO TRANSMISSIONS FOUND IN THIS SECTOR //
              </span>
            </div>
            <div
              v-for="comment in comments"
              :key="comment.id"
              class="relative pl-8 border-l-4 border-construct-black group animate-fade-in"
            >
              <div class="absolute left-[-12px] top-0 w-5 h-5 bg-construct-red rotate-45 opacity-0 group-hover:opacity-100 transition-opacity" />
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h4 class="font-display text-xl tracking-tighter uppercase">{{ comment.name }}</h4>
                  <span class="text-[10px] font-black tracking-widest uppercase opacity-40">
                    {{ comment.date }} // ENCRYPTED_USER_ID: {{ comment.id.slice(-6) }}
                  </span>
                </div>
              </div>
              <p class="text-lg leading-relaxed text-construct-black/80 max-w-2xl">
                {{ comment.body }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- 右侧：文章元数据 -->
      <aside class="md:col-span-3 space-y-8 md:space-y-12 order-3">
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
    <Transition name="fade">
      <div v-if="showShareModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div @click="showShareModal = false" class="absolute inset-0 bg-black/60" />
        
        <!-- Modal -->
        <div class="relative bg-construct-paper border-4 border-construct-black w-full max-w-md p-8">
          <div class="flex items-center justify-between mb-6">
            <h3 class="font-display text-2xl tracking-tighter uppercase">SHARE //</h3>
            <button @click="showShareModal = false" class="w-8 h-8 flex items-center justify-center hover:bg-construct-black hover:text-white transition-colors">
              <span class="text-xl font-bold">&times;</span>
            </button>
          </div>
          
          <div class="space-y-4">
            <button
              @click="copyLink"
              class="w-full flex items-center gap-4 p-4 border-2 border-construct-black hover:bg-construct-black hover:text-white transition-all group"
            >
              <div class="w-10 h-10 flex items-center justify-center bg-construct-black text-white group-hover:bg-white group-hover:text-construct-black transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
              </div>
              <span class="font-display text-sm tracking-widest uppercase">COPY LINK</span>
            </button>

            <button
              @click="shareToWeChatMoments"
              class="w-full flex items-center gap-4 p-4 border-2 border-construct-black hover:bg-construct-black hover:text-white transition-all group"
            >
              <div class="w-10 h-10 flex items-center justify-center bg-construct-black text-white group-hover:bg-white group-hover:text-construct-black transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.002 5.55a.59.59 0 01.213.665l-.39 1.48c-.019.07-.048.141-.048.213 0 .163.13.295.29.295a.326.326 0 00.167-.054l1.903-1.114a.864.864 0 01.717-.098 10.16 10.16 0 002.837.403c.276 0 .543-.027.811-.05-.857-2.578.157-4.972 1.932-6.446 1.703-1.415 3.882-1.98 5.853-1.838-.576-3.583-4.196-6.348-8.596-6.348zM5.785 5.991c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 01-1.162 1.178A1.17 1.17 0 014.623 7.17c0-.651.52-1.18 1.162-1.18zm5.813 0c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 01-1.162 1.178 1.17 1.17 0 01-1.162-1.178c0-.651.52-1.18 1.162-1.18zm5.34 2.867c-1.797-.052-3.746.512-5.28 1.786-1.72 1.428-2.687 3.72-1.78 6.22.942 2.453 3.666 4.229 6.884 4.229.826 0 1.622-.12 2.361-.336a.722.722 0 01.598.082l1.584.926a.272.272 0 00.14.045c.134 0 .24-.11.24-.245 0-.06-.024-.119-.04-.178l-.327-1.233a.49.49 0 01.178-.554C23.81 19.077 24 18.041 24 16.983c0-3.983-3.207-7.22-6.876-8.019V8.89c-.062-.01-.125-.027-.186-.032zm-1.964 3.577c.535 0 .969.44.969.982a.976.976 0 01-.969.983.976.976 0 01-.969-.983c0-.542.434-.982.97-.982zm4.844 0c.535 0 .969.44.969.982a.976.976 0 01-.969.983.976.976 0 01-.969-.983c0-.542.434-.982.97-.982z"/>
                </svg>
              </div>
              <span class="font-display text-sm tracking-widest uppercase">WECHAT MOMENTS</span>
            </button>

            <button
              @click="shareToX"
              class="w-full flex items-center gap-4 p-4 border-2 border-construct-black hover:bg-construct-black hover:text-white transition-all group"
            >
              <div class="w-10 h-10 flex items-center justify-center bg-construct-black text-white group-hover:bg-white group-hover:text-construct-black transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
              </div>
              <span class="font-display text-sm tracking-widest uppercase">X (TWITTER)</span>
            </button>

            <button
              @click="shareToInstagram"
              class="w-full flex items-center gap-4 p-4 border-2 border-construct-black hover:bg-construct-black hover:text-white transition-all group"
            >
              <div class="w-10 h-10 flex items-center justify-center bg-construct-black text-white group-hover:bg-white group-hover:text-construct-black transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
              </div>
              <span class="font-display text-sm tracking-widest uppercase">INSTAGRAM</span>
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style lang="scss" scoped>
.font-display {
  font-family: 'Figtree', system-ui, sans-serif;
}

:deep(.markdown-body) {
  h1, h2, h3, h4, h5, h6 {
    font-family: 'Figtree', system-ui, sans-serif;
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
    font-family: monospace;
    background: #f3f4f6;
    padding: 0.25rem 0.5rem;
    font-size: 0.875em;
  }

  pre {
    background: #0f172a;
    color: #e2e8f0;
    padding: 1.5rem;
    overflow-x: auto;
    margin: 1.5rem 0;
    border: 4px solid #000;
    position: relative;
  }

  pre code {
    background: transparent;
    padding: 0;
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    font-size: 0.875rem;
    line-height: 1.7;
  }

  /* Code syntax highlighting (basic) */
  pre code .keyword { color: #f472b6; }
  pre code .string { color: #86efac; }
  pre code .comment { color: #64748b; font-style: italic; }
  pre code .number { color: #fdba74; }
  pre code .function { color: #60a5fa; }
  pre code .class { color: #a78bfa; }
  pre code .operator { color: #f87171; }
  pre code .property { color: #2dd4bf; }

  /* Language indicator */
  pre::before {
    content: attr(data-language);
    position: absolute;
    top: 0;
    right: 0;
    background: #000;
    color: white;
    padding: 0.25rem 0.75rem;
    font-family: 'Figtree', sans-serif;
    font-size: 0.625rem;
    font-weight: 900;
    letter-spacing: 0.1em;
    text-transform: uppercase;
  }
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

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
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

.animate-slide-down {
  animation: slideDown 0.5s ease-out forwards;
}

.animate-fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}
</style>