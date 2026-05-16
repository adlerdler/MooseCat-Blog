<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { 
  X, 
  Upload, 
  File, 
  Image as ImageIcon, 
  Video as VideoIcon, 
  Archive,
  Trash2,
  CheckCircle,
  AlertCircle,
  Loader
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const props = defineProps({
  visible: Boolean
});

const emit = defineEmits(['close', 'uploaded']);

const { t } = useI18n();
const { isDarkMode } = useTheme();

const isDragging = ref(false);
const fileInput = ref(null);
const uploadFiles = ref([]);
const isUploading = ref(false);

const handleDragOver = (e) => {
  e.preventDefault();
  isDragging.value = true;
};

const handleDragLeave = () => {
  isDragging.value = false;
};

const handleDrop = (e) => {
  e.preventDefault();
  isDragging.value = false;
  addFiles(e.dataTransfer.files);
};

const handleFileSelect = (e) => {
  addFiles(e.target.files);
};

const addFiles = (files) => {
  for (let file of files) {
    uploadFiles.value.push({
      file,
      name: file.name,
      size: formatSize(file.size),
      type: getFileType(file.type),
      progress: 0,
      status: 'pending', // pending, uploading, success, error
      error: ''
    });
  }
};

const removeFile = (index) => {
  uploadFiles.value.splice(index, 1);
};

const formatSize = (bytes) => {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const getFileType = (type) => {
  if (type.startsWith('image/')) return 'image';
  if (type.startsWith('video/')) return 'video';
  if (type.includes('pdf') || type.includes('word') || type.includes('text')) return 'document';
  return 'archive';
};

const getFileIcon = (type) => {
  switch (type) {
    case 'image': return ImageIcon;
    case 'video': return VideoIcon;
    case 'document': return File;
    default: return Archive;
  }
};

const startUpload = async () => {
  if (uploadFiles.value.length === 0) return;
  
  isUploading.value = true;
  
  for (let item of uploadFiles.value) {
    if (item.status === 'success') continue;
    
    item.status = 'uploading';
    
    // Simulate upload progress
    try {
      await simulateUpload(item);
      item.status = 'success';
      item.progress = 100;
    } catch (err) {
      item.status = 'error';
      item.error = 'Upload failed';
    }
  }
  
  isUploading.value = false;
  
  // If all success, wait a bit and close
  if (uploadFiles.value.every(f => f.status === 'success')) {
    setTimeout(() => {
      emit('uploaded', uploadFiles.value);
      closeModal();
    }, 1500);
  }
};

const simulateUpload = (item) => {
  return new Promise((resolve, reject) => {
    let progress = 0;
    const interval = setInterval(() => {
      progress += Math.random() * 30;
      if (progress >= 100) {
        item.progress = 100;
        clearInterval(interval);
        resolve();
      } else {
        item.progress = Math.floor(progress);
      }
    }, 300);
  });
};

const closeModal = () => {
  if (isUploading.value) return;
  uploadFiles.value = [];
  emit('close');
};
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="visible" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModal" />

        <!-- Modal -->
        <div 
          :class="[
            'relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh] transition-all transform',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]"
          @click.stop
        >
          <!-- Header -->
          <div :class="['p-6 border-b flex items-center justify-between', isDarkMode ? 'border-gray-700 bg-gray-900/50' : 'border-gray-100 bg-gray-50/50']">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-construct-red flex items-center justify-center shadow-lg shadow-construct-red/20">
                <Upload class="text-white" size="20" />
              </div>
              <div>
                <h3 :class="['text-lg font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_upload') }}</h3>
                <p :class="['text-xs font-medium', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Upload new media to your library</p>
              </div>
            </div>
            <button 
              @click="closeModal" 
              class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
              :disabled="isUploading"
            >
              <X size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>

          <!-- Body -->
          <div class="p-6 overflow-y-auto custom-scrollbar">
            <!-- Dropzone -->
            <div
              v-if="uploadFiles.length === 0"
              @dragover="handleDragOver"
              @dragleave="handleDragLeave"
              @drop="handleDrop"
              @click="fileInput.click()"
              :class="[
                'border-2 border-dashed rounded-2xl p-12 flex flex-col items-center justify-center transition-all cursor-pointer group',
                isDragging ? 'border-construct-red bg-construct-red/5 scale-[0.99]' : (isDarkMode ? 'border-gray-700 hover:border-gray-600' : 'border-gray-200 hover:border-gray-300')
              ]"
            >
              <input 
                type="file" 
                ref="fileInput" 
                multiple 
                class="hidden" 
                @change="handleFileSelect"
              />
              <div :class="[
                'w-20 h-20 rounded-full flex items-center justify-center mb-4 transition-transform duration-500 group-hover:scale-110',
                isDarkMode ? 'bg-gray-700 text-gray-500 group-hover:text-construct-red' : 'bg-gray-50 text-gray-400 group-hover:text-construct-red'
              ]">
                <Upload size="40" />
              </div>
              <h4 :class="['text-lg font-bold mb-1', isDarkMode ? 'text-white' : 'text-gray-900']">Click or drag files here</h4>
              <p :class="['text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Support for images, videos, documents and archives</p>
              <div class="mt-6 px-4 py-2 bg-construct-red/10 text-construct-red text-[10px] font-black tracking-widest uppercase rounded-full">
                Maximum file size: 100MB
              </div>
            </div>

            <!-- File List -->
            <div v-else class="space-y-3">
              <div 
                v-for="(item, index) in uploadFiles" 
                :key="index"
                :class="[
                  'p-4 border rounded-xl flex items-center gap-4 transition-all',
                  isDarkMode ? 'bg-gray-900/50 border-gray-700' : 'bg-gray-50 border-gray-100'
                ]"
              >
                <!-- Icon -->
                <div :class="[
                  'w-12 h-12 rounded-lg flex items-center justify-center shrink-0',
                  isDarkMode ? 'bg-gray-800' : 'bg-white shadow-sm'
                ]">
                  <component :is="getFileIcon(item.type)" size="24" class="text-construct-red" />
                </div>

                <!-- Info & Progress -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between mb-1.5">
                    <span :class="['text-sm font-bold truncate pr-4', isDarkMode ? 'text-white' : 'text-gray-900']">{{ item.name }}</span>
                    <span class="text-[10px] font-black tracking-widest text-gray-400 uppercase whitespace-nowrap">{{ item.size }}</span>
                  </div>
                  
                  <div class="relative w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div 
                      class="absolute top-0 left-0 h-full bg-construct-red transition-all duration-300"
                      :style="{ width: item.progress + '%' }"
                    ></div>
                  </div>
                  
                  <div class="flex items-center justify-between mt-1.5">
                    <span v-if="item.status === 'error'" class="text-[10px] font-bold text-red-500">{{ item.error }}</span>
                    <span v-else-if="item.status === 'success'" class="text-[10px] font-bold text-green-500 flex items-center gap-1">
                      <CheckCircle size="10" /> Uploaded
                    </span>
                    <span v-else-if="item.status === 'uploading'" class="text-[10px] font-bold text-construct-red flex items-center gap-1">
                      <Loader size="10" class="animate-spin" /> Uploading {{ item.progress }}%
                    </span>
                    <span v-else class="text-[10px] font-bold text-gray-400">Ready to upload</span>
                  </div>
                </div>

                <!-- Actions -->
                <button 
                  v-if="item.status !== 'uploading' && item.status !== 'success'"
                  @click="removeFile(index)"
                  class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                >
                  <Trash2 size="18" />
                </button>
                <div v-else-if="item.status === 'success'" class="p-2 text-green-500">
                  <CheckCircle size="20" />
                </div>
              </div>

              <!-- Add More Button -->
              <button 
                v-if="!isUploading"
                @click="fileInput.click()"
                :class="[
                  'w-full py-4 border-2 border-dashed rounded-xl flex items-center justify-center gap-2 text-sm font-bold transition-all',
                  isDarkMode ? 'border-gray-700 text-gray-500 hover:border-gray-600 hover:text-gray-400' : 'border-gray-200 text-gray-400 hover:border-gray-300 hover:text-gray-500'
                ]"
              >
                <input type="file" ref="fileInput" multiple class="hidden" @change="handleFileSelect" />
                <Plus size="18" /> Add more files
              </button>
            </div>
          </div>

          <!-- Footer -->
          <div :class="['p-6 border-t flex items-center justify-between', isDarkMode ? 'border-gray-700 bg-gray-900/50' : 'border-gray-100 bg-gray-50/50']">
            <p :class="['text-xs font-bold uppercase tracking-wider', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              {{ uploadFiles.length }} files selected
            </p>
            <div class="flex gap-3">
              <button 
                @click="closeModal" 
                :disabled="isUploading"
                :class="[
                  'px-6 py-2.5 font-bold tracking-widest uppercase text-xs transition-all rounded-lg border',
                  isDarkMode ? 'border-gray-700 text-gray-400 hover:bg-gray-800' : 'border-gray-200 text-gray-500 hover:bg-white'
                ]"
              >
                Cancel
              </button>
              <button 
                @click="startUpload"
                :disabled="uploadFiles.length === 0 || isUploading"
                class="px-8 py-2.5 bg-construct-red text-white font-bold tracking-widest uppercase text-xs hover:bg-red-700 transition-all rounded-lg shadow-lg shadow-construct-red/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <Loader v-if="isUploading" size="14" class="animate-spin" />
                {{ isUploading ? 'Uploading...' : 'Start Upload' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .transform,
.modal-leave-active .transform {
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-enter-from .transform {
  transform: scale(0.9) translateY(20px);
}

.modal-leave-to .transform {
  transform: scale(0.95);
}

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
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
}
</style>
