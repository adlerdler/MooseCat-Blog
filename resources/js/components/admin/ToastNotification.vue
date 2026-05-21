<script setup>
/**
 * ToastNotification.vue - 磨砂透明反馈通知组件
 *
 * 功能说明：
 * - 支持四种类型：success、error、warning、info
 * - 磨砂透明背景，带主题颜色边框
 * - 底部进度条显示倒计时
 * - 可手动关闭
 * - 支持 6 种位置配置
 * - 鼠标悬停暂停计时
 *
 * 使用示例：
 * <ToastNotification
 *   :visible="showToast"
 *   message="操作成功"
 *   type="success"
 *   position="bottom-right"
 *   :closable="true"
 *   @close="showToast = false"
 * />
 */
import { ref, computed, watch, onUnmounted } from 'vue';
import { Check, AlertCircle, AlertTriangle, Info, X } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { isDarkMode } = useTheme();

const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    default: ''
  },
  title: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'success',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  duration: {
    type: Number,
    default: 3000
  },
  position: {
    type: String,
    default: 'bottom-right',
    validator: (value) => ['top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right'].includes(value)
  },
  closable: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['close']);

const progress = ref(100);
let timer = null;
let progressTimer = null;
let isPaused = false;

const positionClasses = {
  'top-left': 'top-6 left-6',
  'top-center': 'top-6 left-1/2 -translate-x-1/2',
  'top-right': 'top-6 right-6',
  'bottom-left': 'bottom-6 left-6',
  'bottom-center': 'bottom-6 left-1/2 -translate-x-1/2',
  'bottom-right': 'bottom-6 right-6'
};

const toastConfig = {
  success: { icon: Check, color: 'text-green-500', border: 'border-green-500', bg: 'bg-green-500/10', glassBg: 'bg-green-500/15', progressColor: 'bg-green-500' },
  error: { icon: AlertCircle, color: 'text-red-500', border: 'border-red-500', bg: 'bg-red-500/10', glassBg: 'bg-red-500/15', progressColor: 'bg-red-500' },
  warning: { icon: AlertTriangle, color: 'text-yellow-500', border: 'border-yellow-500', bg: 'bg-yellow-500/10', glassBg: 'bg-yellow-500/15', progressColor: 'bg-yellow-500' },
  info: { icon: Info, color: 'text-blue-500', border: 'border-blue-500', bg: 'bg-blue-500/10', glassBg: 'bg-blue-500/15', progressColor: 'bg-blue-500' }
};

const currentConfig = computed(() => toastConfig[props.type] || toastConfig.success);
const currentPosition = computed(() => positionClasses[props.position] || positionClasses['bottom-right']);

const startTimer = () => {
  if (props.duration <= 0 || isPaused) return;

  progress.value = 100;

  if (timer) clearTimeout(timer);
  if (progressTimer) clearInterval(progressTimer);

  const interval = 30;
  const decrement = 100 / (props.duration / interval);

  progressTimer = setInterval(() => {
    if (!isPaused) {
      progress.value -= decrement;
      if (progress.value <= 0) {
        progress.value = 0;
        clearInterval(progressTimer);
      }
    }
  }, interval);

  timer = setTimeout(() => {
    emit('close');
    progress.value = 100;
  }, props.duration);
};

const stopTimer = () => {
  if (timer) {
    clearTimeout(timer);
    timer = null;
  }
  if (progressTimer) {
    clearInterval(progressTimer);
    progressTimer = null;
  }
};

const handleClose = () => {
  stopTimer();
  progress.value = 100;
  isPaused = false;
  emit('close');
};

const handleMouseEnter = () => {
  isPaused = true;
};

const handleMouseLeave = () => {
  isPaused = false;
};

onUnmounted(() => {
  stopTimer();
});

watch(() => props.visible, (newVal) => {
  if (newVal) {
    startTimer();
  } else {
    stopTimer();
    progress.value = 100;
    isPaused = false;
  }
}, { immediate: true });
</script>

<template>
  <Transition name="toast">
    <div
      v-if="visible"
      class="fixed z-[100] w-80 rounded-xl shadow-2xl overflow-hidden backdrop-blur-xl border-l-4"
      :class="[
        currentPosition,
        currentConfig.border,
        currentConfig.glassBg
      ]"
      @mouseenter="handleMouseEnter"
      @mouseleave="handleMouseLeave"
    >
      <div class="flex items-center gap-3 px-4 py-3">
        <div :class="['flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center', currentConfig.bg]">
          <component :is="currentConfig.icon" :class="['flex-shrink-0', currentConfig.color]" size="16" />
        </div>
        <div class="flex-1 min-w-0">
          <p v-if="title" :class="['text-sm font-bold', currentConfig.color]">{{ title }}</p>
          <p :class="['text-sm font-medium', isDarkMode ? 'text-gray-200' : 'text-gray-800', title ? 'mt-0.5' : '']">{{ message }}</p>
        </div>
        <button
          v-if="closable"
          @click="handleClose"
          :class="['p-1 rounded-lg transition-colors flex-shrink-0', isDarkMode ? 'hover:bg-white/10 text-gray-400' : 'hover:bg-black/5 text-gray-400']"
        >
          <X size="14" />
        </button>
      </div>
      <div class="h-0.5 w-full" :class="isDarkMode ? 'bg-black/20' : 'bg-black/5'">
        <div
          class="h-full"
          :class="currentConfig.progressColor"
          :style="{ width: progress + '%' }"
        ></div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>
