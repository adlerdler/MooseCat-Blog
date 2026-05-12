<script setup>
/**
 * ShareModal.vue - 分享模态框组件
 * 
 * 功能说明：
 * - 展示文章/视频分享选项
 * - 支持复制链接功能
 * - 多种社交平台分享入口
 * 
 * 技术实现：
 * - 使用 Clipboard API 复制链接
 * - 支持 Twitter、Facebook、LinkedIn 等平台分享
 * 
 * 使用示例：
 * <ShareModal :show="showShare" title="文章标题" @close="closeModal" />
 */
import { ref } from 'vue';
const props = defineProps({
 show: {
 type: Boolean,
 default: false
 },
 title: {
 type: String,
 default: ''
 }
});
const emit = defineEmits(['close']);
const isCopying = ref(false);
const copySuccess = ref(false);
const copyLink = async () => {
 isCopying.value = true;
 const url = window.location.href;
 try {
 await navigator.clipboard.writeText(url);
 copySuccess.value = true;
 }
 catch (err) {
 const textArea = document.createElement('textarea');
 textArea.value = url;
 document.body.appendChild(textArea);
 textArea.select();
 document.execCommand('copy');
 document.body.removeChild(textArea);
 copySuccess.value = true;
 }
 setTimeout(() => {
 isCopying.value = false;
 copySuccess.value = false;
 }, 2000);
};
const shareToX = () => {
 const url = encodeURIComponent(window.location.href);
 const text = encodeURIComponent(props.title || 'Check this out');
 window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
 emit('close');
};
const shareToInstagram = () => {
 alert('Instagram分享功能需要使用原生应用');
};
const shareToWeChatMoments = () => {
 alert('朋友圈分享功能需要使用微信原生应用');
};
const handleClose = () => {
 emit('close');
};
</script>

<template>
  <Transition name="fade">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <!-- Backdrop -->
      <div @click="handleClose" class="absolute inset-0 bg-black/60" />
      
      <!-- Modal -->
      <div class="relative bg-construct-paper border-4 border-construct-black w-full max-w-md p-8">
        <div class="flex items-center justify-between mb-6">
          <h3 class="font-display text-2xl tracking-tighter uppercase">SHARE //</h3>
          <button @click="handleClose" class="w-8 h-8 flex items-center justify-center hover:bg-construct-black hover:text-white transition-colors">
            <span class="text-xl font-bold">&times;</span>
          </button>
        </div>
        
        <div class="space-y-4">
          <button
            @click="copyLink"
            :disabled="isCopying"
            class="w-full flex items-center gap-4 p-4 border-2 border-construct-black hover:bg-construct-black hover:text-white transition-all group disabled:opacity-50"
          >
            <div class="w-10 h-10 flex items-center justify-center bg-construct-black text-white group-hover:bg-white group-hover:text-construct-black transition-colors">
              <svg v-if="!copySuccess" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
            <span class="font-display text-sm tracking-widest uppercase">
              {{ copySuccess ? 'LINK COPIED!' : 'COPY LINK' }}
            </span>
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
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>