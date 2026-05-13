<script setup>
/**
 * ToastNotification.vue - 操作提示弹窗组件
 * 
 * 功能说明：
 * - 显示成功、错误、警告、信息四种类型的提示
 * - 自动关闭（可配置）
 * - 支持手动关闭
 * - 深色/浅色模式自适应
 * - 动画效果
 */
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { CheckCircle, XCircle, AlertTriangle, Info, X } from 'lucide-vue-next';
import { useTheme } from '../composables/useTheme';

const { isDarkMode } = useTheme();

const props = defineProps({
  type: {
    type: String,
    default: 'success',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  title: {
    type: String,
    default: ''
  },
  message: {
    type: String,
    required: true
  },
  duration: {
    type: Number,
    default: 3000
  },
  closable: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['close']);

const visible = ref(false);
const progress = ref(100);
let timer = null;
let progressTimer = null;

const icons = {
  success: CheckCircle,
  error: XCircle,
  warning: AlertTriangle,
  info: Info
};

const colors = {
  success: {
    bg: isDarkMode.value ? 'bg-green-500/10' : 'bg-green-50',
    border: 'border-green-500',
    icon: 'text-green-500',
    title: isDarkMode.value ? 'text-green-400' : 'text-green-700'
  },
  error: {
    bg: isDarkMode.value ? 'bg-red-500/10' : 'bg-red-50',
    border: 'border-red-500',
    icon: 'text-red-500',
    title: isDarkMode.value ? 'text-red-400' : 'text-red-700'
  },
  warning: {
    bg: isDarkMode.value ? 'bg-yellow-500/10' : 'bg-yellow-50',
    border: 'border-yellow-500',
    icon: 'text-yellow-500',
    title: isDarkMode.value ? 'text-yellow-400' : 'text-yellow-700'
  },
  info: {
    bg: isDarkMode.value ? 'bg-blue-500/10' : 'bg-blue-50',
    border: 'border-blue-500',
    icon: 'text-blue-500',
    title: isDarkMode.value ? 'text-blue-400' : 'text-blue-700'
  }
};

const currentColor = computed(() => colors[props.type]);
const currentIcon = computed(() => icons[props.type]);

const startTimer = () => {
  if (props.duration > 0) {
    const step = 100 / (props.duration / 10);
    progressTimer = setInterval(() => {
      progress.value -= step;
      if (progress.value <= 0) {
        clearInterval(progressTimer);
      }
    }, 10);

    timer = setTimeout(() => {
      close();
    }, props.duration);
  }
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

const close = () => {
  stopTimer();
  visible.value = false;
  setTimeout(() => {
    emit('close');
  }, 300);
};

onMounted(() => {
  setTimeout(() => {
    visible.value = true;
    startTimer();
  }, 100);
});

onUnmounted(() => {
  stopTimer();
});
</script>

<template>
  <div
    :class="[
      'fixed top-4 right-4 z-50 max-w-sm w-full transform transition-all duration-300',
      visible ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0'
    ]"
    @mouseenter="stopTimer"
    @mouseleave="startTimer"
  >
    <div
      :class="[
        'relative overflow-hidden rounded-lg border-l-4 shadow-lg p-4',
        currentColor.bg,
        currentColor.border,
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]"
    >
      <!-- Progress bar -->
      <div
        v-if="duration > 0"
        class="absolute bottom-0 left-0 h-1 transition-all duration-100"
        :class="currentColor.bg.replace('/10', '')"
        :style="{ width: progress + '%' }"
      />

      <div class="flex items-start gap-3">
        <!-- Icon -->
        <component
          :is="currentIcon"
          :class="['flex-shrink-0 mt-0.5', currentColor.icon]"
          size="20"
        />

        <!-- Content -->
        <div class="flex-1 min-w-0">
          <h4
            v-if="title"
            :class="['font-semibold text-sm mb-1', currentColor.title]"
          >
            {{ title }}
          </h4>
          <p :class="['text-sm', isDarkMode ? 'text-gray-300' : 'text-gray-600']">
            {{ message }}
          </p>
        </div>

        <!-- Close button -->
        <button
          v-if="closable"
          @click="close"
          :class="[
            'flex-shrink-0 p-1 rounded transition-colors',
            isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500'
          ]"
        >
          <X size="16" />
        </button>
      </div>
    </div>
  </div>
</template>
