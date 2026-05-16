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
        :class="[
          'relative w-full max-w-6xl max-h-[90vh] flex flex-col md:flex-row rounded-2xl overflow-hidden shadow-2xl border',
          isDarkMode ? 'bg-black border-white/10' : 'bg-white border-gray-200'
        ]"
      >
        <!-- Close Button (Mobile) -->
        <button 
          @click="handleClose"
          class="absolute top-4 right-4 z-20 p-2 bg-black/50 text-white rounded-full md:hidden"
        >
          <X size="24" />
        </button>

        <!-- Preview Area -->
        <div :class="['flex-1 flex items-center justify-center relative group min-h-[300px] overflow-hidden', isDarkMode ? 'bg-neutral-950' : 'bg-gray-100']">
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
            <div :class="['w-full h-full overflow-auto p-4', isDarkMode ? 'bg-neutral-800' : 'bg-gray-200']">
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
            <div :class="['flex flex-col items-center gap-4', isDarkMode ? 'text-white/40' : 'text-gray-400']">
              <FileText size="80" stroke-width="1" />
              <span class="font-bold tracking-widest uppercase text-sm">No Preview Available</span>
            </div>
          </template>

          <!-- Floating Actions -->
          <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
            <button 
              @click="handleDownload"
              :class="[
                'px-6 py-3 backdrop-blur-md border rounded-full flex items-center gap-2 font-bold text-xs uppercase tracking-widest transition-all',
                isDarkMode ? 'bg-white/10 hover:bg-white/20 text-white border-white/10' : 'bg-black/10 hover:bg-black/20 text-gray-900 border-black/10'
              ]"
            >
              <Download size="16" /> {{ t('admin_download') }}
            </button>
          </div>
        </div>

        <!-- Sidebar Info -->
        <div :class="['w-full md:w-[350px] border-l flex flex-col', isDarkMode ? 'bg-neutral-900 border-white/10' : 'bg-gray-50 border-gray-200']">
          <div :class="['p-6 border-b flex items-center justify-between', isDarkMode ? 'border-white/5 bg-black/20' : 'border-gray-200 bg-white']">
            <h3 :class="['font-bold tracking-tighter text-xl', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_preview') }}</h3>
            <button 
              @click="handleClose"
              :class="['hidden md:flex p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-white/5 text-white/50 hover:text-white' : 'hover:bg-gray-200 text-gray-400 hover:text-gray-900']"
            >
              <X size="20" />
            </button>
          </div>

          <div class="flex-1 overflow-y-auto p-6 space-y-8">
            <!-- File Info -->
            <div class="space-y-6">
              <div>
                <label :class="['text-[10px] font-black tracking-widest uppercase block mb-2', isDarkMode ? 'text-white/30' : 'text-gray-400']">File Name</label>
                <div :class="['font-bold text-lg break-all leading-tight', isDarkMode ? 'text-white' : 'text-gray-900']">{{ file?.name }}</div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div :class="['p-4 rounded-xl border', isDarkMode ? 'bg-white/5 border-white/5' : 'bg-white border-gray-200']">
                  <label :class="['text-[10px] font-black tracking-widest uppercase block mb-1', isDarkMode ? 'text-white/30' : 'text-gray-400']">Type</label>
                  <div :class="['font-bold text-sm uppercase', isDarkMode ? 'text-white' : 'text-gray-800']">{{ file?.type }}</div>
                </div>
                <div :class="['p-4 rounded-xl border', isDarkMode ? 'bg-white/5 border-white/5' : 'bg-white border-gray-200']">
                  <label :class="['text-[10px] font-black tracking-widest uppercase block mb-1', isDarkMode ? 'text-white/30' : 'text-gray-400']">Size</label>
                  <div :class="['font-bold text-sm', isDarkMode ? 'text-white' : 'text-gray-800']">{{ file?.size }}</div>
                </div>
              </div>

              <div class="space-y-4">
                <div :class="['flex items-center gap-3', isDarkMode ? 'text-white/60' : 'text-gray-600']">
                  <Calendar size="16" class="text-construct-red" />
                  <span class="text-sm font-medium">{{ file?.date }}</span>
                </div>
                <div :class="['flex items-center gap-3', isDarkMode ? 'text-white/60' : 'text-gray-600']">
                  <HardDrive size="16" class="text-construct-red" />
                  <span class="text-sm font-medium">Internal Storage</span>
                </div>
              </div>
            </div>

            <!-- URL Copy -->
            <div>
              <label :class="['text-[10px] font-black tracking-widest uppercase block mb-2', isDarkMode ? 'text-white/30' : 'text-gray-400']">Public URL</label>
              <div class="relative group/copy">
                <div :class="['p-4 border rounded-xl text-[11px] font-mono break-all pr-10 transition-colors', isDarkMode ? 'bg-black border-white/10 text-white/40 group-hover/copy:text-white/60' : 'bg-white border-gray-200 text-gray-500 group-hover/copy:text-gray-700']">
                  {{ file?.url }}
                </div>
                <button :class="['absolute right-3 top-1/2 -translate-y-1/2 transition-colors', isDarkMode ? 'text-white/20 hover:text-white' : 'text-gray-300 hover:text-gray-600']">
                  <ExternalLink size="14" />
                </button>
              </div>
            </div>
          </div>

          <!-- Bottom Actions -->
          <div :class="['p-6 border-t flex gap-3', isDarkMode ? 'border-white/5 bg-black/20' : 'border-gray-200 bg-white']">
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
