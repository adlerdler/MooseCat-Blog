<script setup>
/**
 * MediaPreviewModal.vue - 媒体文件预览弹窗
 * 
 * 功能说明：
 * - 大图/视频全屏预览
 * - 支持图片和视频类型
 * - 响应式设计，优美的动画效果
 */
import { X, Download, Trash2, Calendar, HardDrive, FileText, ExternalLink, Loader2 } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { useI18n } from 'vue-i18n';
import { Motion, AnimatePresence } from 'motion-v';
import VuePdfEmbed from 'vue-pdf-embed';
import { renderAsync } from 'docx-preview';
import { ref, watch, computed, nextTick } from 'vue';

const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  },
  file: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['close', 'delete', 'download']);

const { isDarkMode } = useTheme();
const { t } = useI18n();

const docxContainer = ref(null);
const isLoadingDoc = ref(false);

const isPdf = computed(() => props.file?.type === 'document' && props.file?.url?.toLowerCase().endsWith('.pdf'));
const isDocx = computed(() => props.file?.type === 'document' && props.file?.url?.toLowerCase().endsWith('.docx'));

const handleClose = () => {
  emit('close');
};

const handleDownload = () => {
  emit('download', props.file);
};

const handleDelete = () => {
  emit('delete', props.file);
};

const loadDocx = async () => {
  if (!isDocx.value || !props.visible) return;
  
  isLoadingDoc.value = true;
  try {
    const response = await fetch(props.file.url);
    const arrayBuffer = await response.arrayBuffer();
    await nextTick();
    if (docxContainer.value) {
      docxContainer.value.innerHTML = '';
      await renderAsync(arrayBuffer, docxContainer.value, null, {
        className: "docx", // class to skip target style
        inWrapper: false, // enables or disables usage of wrapper
        ignoreWidth: false, // disables rendering of width
        ignoreHeight: false, // disables rendering of height
        ignoreFonts: false, // disables fonts rendering
        breakPages: true, // enables page breaks rendering
        ignoreLastRenderedPageBreak: true, // disables usage of lastRenderedPageBreak for page breaks
        experimental: false, // enables experimental features (no need for now)
        trimXmlDeclaration: true, // if true, xml declaration will be removed from xml documents
        useBase64URL: false, // if true, images, fonts, etc. will be converted to base64 URLs, otherwise object URLs will be used
        useSharedDOMParser: false, // if true, shared DOMParser will be used
        useStyleWithId: false, // if true, styles will be generated with id, to prevent conflicts
      });
    }
  } catch (error) {
    console.error('Error rendering docx:', error);
  } finally {
    isLoadingDoc.value = false;
  }
};

watch([() => props.file, () => props.visible], () => {
  if (isDocx.value && props.visible) {
    loadDocx();
  }
}, { immediate: true });
</script>

<template>
  <AnimatePresence>
    <div v-if="visible" class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-8">
      <!-- Backdrop -->
      <Motion
        :initial="{ opacity: 0 }"
        :animate="{ opacity: 1 }"
        :exit="{ opacity: 0 }"
        class="absolute inset-0 bg-black/90 backdrop-blur-md"
        @click="handleClose"
      />

      <!-- Modal Content -->
      <Motion
        :initial="{ opacity: 0, scale: 0.9, y: 20 }"
        :animate="{ opacity: 1, scale: 1, y: 0 }"
        :exit="{ opacity: 0, scale: 0.9, y: 20 }"
        class="relative w-full max-w-6xl max-h-[90vh] flex flex-col md:flex-row bg-black rounded-2xl overflow-hidden shadow-2xl border border-white/10"
      >
        <!-- Close Button (Mobile) -->
        <button 
          @click="handleClose"
          class="absolute top-4 right-4 z-20 p-2 bg-black/50 text-white rounded-full md:hidden"
        >
          <X size="24" />
        </button>

        <!-- Preview Area -->
        <div class="flex-1 bg-neutral-950 flex items-center justify-center relative group min-h-[300px] overflow-hidden">
          <!-- Loading State -->
          <div v-if="isLoadingDoc" class="absolute inset-0 z-10 flex items-center justify-center bg-black/50">
            <Loader2 class="w-10 h-10 text-construct-red animate-spin" />
          </div>

          <template v-if="file?.type === 'image'">
            <img 
              :src="file?.url" 
              :alt="file?.name" 
              class="max-w-full max-h-[80vh] object-contain"
            />
          </template>
          <template v-else-if="file?.type === 'video'">
            <video 
              :src="file?.url" 
              controls 
              autoplay
              class="max-w-full max-h-[80vh]"
            ></video>
          </template>
          <template v-else-if="isPdf">
            <div class="w-full h-full overflow-auto bg-neutral-800 p-4">
              <VuePdfEmbed 
                :source="file?.url" 
                class="mx-auto max-w-4xl shadow-2xl"
              />
            </div>
          </template>
          <template v-else-if="isDocx">
            <div class="w-full h-full overflow-auto bg-white p-8">
              <div ref="docxContainer" class="mx-auto max-w-4xl docx-preview-container"></div>
            </div>
          </template>
          <template v-else>
            <div class="flex flex-col items-center gap-4 text-white/40">
              <FileText size="80" stroke-width="1" />
              <span class="font-bold tracking-widest uppercase text-sm">No Preview Available</span>
            </div>
          </template>

          <!-- Floating Actions -->
          <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
            <button 
              @click="handleDownload"
              class="px-6 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/10 rounded-full flex items-center gap-2 font-bold text-xs uppercase tracking-widest transition-all"
            >
              <Download size="16" /> {{ t('admin_download') }}
            </button>
          </div>
        </div>

        <!-- Sidebar Info -->
        <div class="w-full md:w-[350px] bg-neutral-900 border-l border-white/10 flex flex-col">
          <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-white font-bold tracking-tighter text-xl">{{ t('admin_preview') }}</h3>
            <button 
              @click="handleClose"
              class="hidden md:flex p-2 hover:bg-white/5 text-white/50 hover:text-white rounded-lg transition-colors"
            >
              <X size="20" />
            </button>
          </div>

          <div class="flex-1 overflow-y-auto p-6 space-y-8">
            <!-- File Info -->
            <div class="space-y-6">
              <div>
                <label class="text-[10px] font-black tracking-widest text-white/30 uppercase block mb-2">File Name</label>
                <div class="text-white font-bold text-lg break-all leading-tight">{{ file?.name }}</div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-white/5 rounded-xl border border-white/5">
                  <label class="text-[10px] font-black tracking-widest text-white/30 uppercase block mb-1">Type</label>
                  <div class="text-white font-bold text-sm uppercase">{{ file?.type }}</div>
                </div>
                <div class="p-4 bg-white/5 rounded-xl border border-white/5">
                  <label class="text-[10px] font-black tracking-widest text-white/30 uppercase block mb-1">Size</label>
                  <div class="text-white font-bold text-sm">{{ file?.size }}</div>
                </div>
              </div>

              <div class="space-y-4">
                <div class="flex items-center gap-3 text-white/60">
                  <Calendar size="16" class="text-construct-red" />
                  <span class="text-sm font-medium">{{ file?.date }}</span>
                </div>
                <div class="flex items-center gap-3 text-white/60">
                  <HardDrive size="16" class="text-construct-red" />
                  <span class="text-sm font-medium">Internal Storage</span>
                </div>
              </div>
            </div>

            <!-- URL Copy -->
            <div>
              <label class="text-[10px] font-black tracking-widest text-white/30 uppercase block mb-2">Public URL</label>
              <div class="relative group/copy">
                <div class="p-4 bg-black border border-white/10 rounded-xl text-[11px] font-mono text-white/40 break-all pr-10">
                  {{ file?.url }}
                </div>
                <button class="absolute right-3 top-1/2 -translate-y-1/2 text-white/20 hover:text-white transition-colors">
                  <ExternalLink size="14" />
                </button>
              </div>
            </div>
          </div>

          <!-- Bottom Actions -->
          <div class="p-6 border-t border-white/5 bg-black/20 flex gap-3">
            <button 
              @click="handleDelete"
              class="flex-1 py-4 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/20 rounded-xl font-black text-[10px] tracking-[0.2em] uppercase transition-all flex items-center justify-center gap-2"
            >
              <Trash2 size="16" /> {{ t('admin_delete') }}
            </button>
          </div>
        </div>
      </Motion>
    </div>
  </AnimatePresence>
</template>

<style scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}

:deep(.docx-preview-container) {
  background-color: white !important;
  color: black !important;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

:deep(.docx-wrapper) {
  background-color: transparent !important;
  padding: 0 !important;
}

:deep(.docx) {
  margin-bottom: 0 !important;
}
</style>
