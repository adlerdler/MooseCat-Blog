<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { X, Download, Copy, Check } from 'lucide-vue-next';

const props = defineProps({
  resource: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['close']);

const copiedLink = ref(null);

const handleCopy = (text) => {
  navigator.clipboard.writeText(text);
  copiedLink.value = text;
  setTimeout(() => {
    copiedLink.value = null;
  }, 2000);
};

const getDriveIcon = (type) => {
  switch (type) {
    case 'google':
      return 'google';
    case 'baidu':
      return 'baidu';
    case 'ali':
      return 'ali';
    default:
      return 'default';
  }
};

const getDriveName = (type) => {
  switch (type) {
    case 'google':
      return 'Google Drive';
    case 'baidu':
      return 'Baidu Netdisk';
    case 'ali':
      return 'Aliyun Drive';
    default:
      return 'Link';
  }
};

const handleClose = () => {
  emit('close');
};

const handleBackdropClick = (e) => {
  if (e.target === e.currentTarget) {
    handleClose();
  }
};

watch(() => props.resource, (newVal) => {
  if (newVal) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = 'auto';
  }
});

onMounted(() => {
  if (props.resource) {
    document.body.style.overflow = 'hidden';
  }
});

onUnmounted(() => {
  document.body.style.overflow = 'auto';
});
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="resource"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-8"
        @click="handleBackdropClick"
      >
        <div class="absolute inset-0 bg-construct-black/80 backdrop-blur-sm" />
        
        <Transition name="modal-content">
          <div
            class="relative bg-construct-paper w-full max-w-4xl max-h-[90vh] flex flex-col md:flex-row shadow-2xl overflow-hidden"
          >
            <button
              @click="handleClose"
              class="absolute top-4 right-4 z-20 w-10 h-10 bg-construct-black text-white flex items-center justify-center hover:bg-construct-red transition-colors shadow-md"
            >
              <X class="w-6 h-6" />
            </button>

            <!-- Left side: Image -->
            <div class="w-full h-[300px] md:h-auto md:w-1/2 flex-shrink-0 bg-construct-black relative">
              <img
                :src="resource.image"
                :alt="resource.title"
                class="absolute inset-0 w-full h-full object-cover"
              />
            </div>

            <!-- Right side: Content -->
            <div class="w-full md:w-1/2 p-6 md:p-12 flex flex-col overflow-y-auto">
              <div>
                <div class="text-[10px] font-bold tracking-[0.3em] uppercase text-construct-red mb-4 mt-8 md:mt-0">
                  {{ resource.category }} • {{ resource.format }} • {{ resource.fileSize }}
                </div>
                
                <h3 class="font-display text-4xl md:text-5xl leading-none tracking-tighter mb-6">
                  {{ resource.title }}
                </h3>
                
                <p class="text-sm font-medium leading-relaxed opacity-70 mb-12">
                  {{ resource.description }}
                </p>
              </div>

              <div class="space-y-4 mt-12 md:mt-auto pt-8">
                <a
                  v-if="resource.directLink"
                  :href="resource.directLink"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex justify-between items-center w-full bg-construct-black text-white p-4 font-bold text-xs tracking-widest uppercase hover:bg-construct-red transition-colors"
                >
                  <span>Download Resource</span>
                  <Download class="w-4 h-4" />
                </a>
                
                <div
                  v-for="(drive, idx) in resource.drives"
                  :key="idx"
                  class="flex justify-between items-center w-full border-2 border-construct-black p-4 font-bold text-xs tracking-widest"
                >
                  <div class="flex items-center gap-3">
                    <svg v-if="getDriveIcon(drive.type) === 'google'" viewBox="0 0 87.3 78" class="w-4 h-4 fill-current">
                      <path d="M6.6 66.85l38.8-66.85h-13.2l-32.2 55.7z"/>
                      <path d="M45.4 66.85l-19.4-33.55 16.1-27.85 38.8 66.85h-13.2z"/>
                      <path d="M74.1 16.4l-19.4-33.55h-38.8l19.4 33.55z"/>
                    </svg>
                    <svg v-else-if="getDriveIcon(drive.type) === 'baidu'" viewBox="0 0 1024 1024" class="w-4 h-4 fill-current">
                      <path d="M725.64 430.76c-31.42 0-82.63-23.73-100.28-76.81C607.72 300.86 630.12 189.6 573.74 126c-56.36-63.61-180.5-44.62-232.06 6.06-51.52 50.68-45.71 164.74-67.62 216.59-21.93 51.84-75.98 70.83-93.57 69.11-17.58-1.74-63.26-19.06-93.57 11.23-30.29 30.28-11.75 75.25-11.75 75.25s17.06 43.19 22.01 54.43c4.95 11.23 20.73 34.56 50.49 25.05 29.77-9.5 73.16-36.29 116.59-4.32a106.84 106.84 0 0 1 35.88 56.66c10.42 36.31 1.63 76.8-5.32 100.41-6.95 23.59-24.97 73.91 8.54 107.41 33.53 33.52 83.15 15.39 105.77 8.35s62.58-19.68 97.51 16.29c34.92 35.97 22.5 86.84 14.53 108.43-7.96 21.58-27.13 71.9 6.38 105.42 33.53 33.52 83.15 15.39 105.77 8.35s63.42-20.44 116.32 0c52.92 20.45 106.33-4.39 123.95-21.99 17.61-17.59-7.14-72.33-14.73-89.92-7.59-17.58-30.82-59.57 8.32-98.71a108.97 108.97 0 0 1 76-26.4c35.61-8.52 81.3-33.15 88.24-56.77s-1.84-60.67-1.84-60.67-17.06-44.47-50.59-54.84c-33.48-10.37-67.95 2.15-99.39 2.15z"/>
                    </svg>
                    <svg v-else-if="getDriveIcon(drive.type) === 'ali'" viewBox="0 0 1024 1024" class="w-5 h-5 fill-current">
                      <path d="M512 0C229.2 0 0 229.2 0 512c0 282.8 229.2 512 512 512 282.8 0 512-229.2 512-512C1024 229.2 794.8 0 512 0zm201.2 713c-23.7 23.3-51.5 42-83 55.4-31.5 13.5-65 20.2-100.5 20.2-36.2 0-70.5-6.9-102.5-20.5-31.9-13.6-59.7-32.9-83-57.5-23.3-24.6-41.5-53.5-54.4-86l88.5-38.3c7.7 20 19.3 37.6 34.6 52.6 15.3 15 33.3 26.6 53.8 34.6 20.5 8 43 12 67.2 12 23.8 0 45.8-3.9 66-11.6 20.1-7.7 37.8-18.7 52.8-32.9l60.5 72zM753 361.3c0 38.6-6.7 75.1-20.2 109.4-13.4 34.2-32.6 64.9-57.2 91.5l-63.5-69.5c16.3-17.5 28.9-38.3 37.6-61.6 8.7-23.3 13.1-48.4 13.1-74.8 0-48.7-18-90.2-53.8-123.8-35.8-33.6-78.8-50.5-128.5-50.5-49.8 0-92.8 16.8-128.8 50.4C315.8 266 298 307.7 298 356.5c0 26.4 4.5 51.5 13.3 75 8.9 23.5 21.6 44.2 38.2 61.8l-64 70.3c-24.5-25.9-43.5-55.9-56.7-89.5-13.2-33.6-19.8-69.6-19.8-107.5 0-66.4 23.2-123 69.1-169s102.6-68.8 169.3-68.8c66.4 0 122.9 22.9 169 68.4 46 45.6 69.1 102 69.1 169s-2.5-4.9-2.5-4.9z"/>
                    </svg>
                    <Download v-else class="w-4 h-4" />
                    <span class="uppercase">{{ getDriveName(drive.type) }}</span>
                  </div>
                  <button
                    @click="handleCopy(drive.password ? `${drive.link} | PASS: ${drive.password}` : drive.link)"
                    class="flex items-center gap-2 hover:text-construct-red transition-colors uppercase"
                  >
                    <template v-if="copiedLink === (drive.password ? `${drive.link} | PASS: ${drive.password}` : drive.link)">
                      <span>Copied</span>
                      <Check class="w-4 h-4" />
                    </template>
                    <template v-else>
                      <span>Copy Link</span>
                      <Copy class="w-4 h-4" />
                    </template>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<style lang="scss" scoped>
.modal {
  &-enter-active,
  &-leave-active {
    transition: opacity 0.2s ease;
  }

  &-enter-from,
  &-leave-to {
    opacity: 0;
  }
}

.modal-content {
  &-enter-active {
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  }

  &-leave-active {
    transition: all 0.2s ease;
  }

  &-enter-from {
    opacity: 0;
    transform: scale(0.95);
  }

  &-leave-to {
    opacity: 0;
    transform: scale(0.95);
  }
}

.font-display {
  font-family: system-ui, -apple-system, sans-serif;
}
</style>