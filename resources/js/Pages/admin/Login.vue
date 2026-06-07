<script setup>
/**
 * Login.vue - 管理后台登录页面
 *
 * 功能说明：
 * - 管理员登录界面
 * - 验证邮箱和密码
 * - 登录成功后跳转到管理后台首页
 * - 支持记住密码功能
 * - 支持密码显示/隐藏切换
 * - 支持中英文国际化
 * - 超时/手动退出后在右下角显示通知，点击页面或X关闭
 */
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Lock, Mail, AlertCircle, Eye, EyeOff, X, ShieldOff, Clock, Shield, RefreshCw, AlertTriangle, LogOut } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { t } = useI18n();
const { isDarkMode, initTheme } = useTheme();
const page = usePage();

// ─── 统一通知系统 ────
const notification = ref({
  show: false,
  message: '',
  icon: 'alert', // alert | shield | clock | logout
  autoDismiss: true,
});
let notificationTimer = null;

const showNotification = (message, icon = 'alert', autoDismiss = true, duration = 5000) => {
  clearTimeout(notificationTimer);
  notification.value = { show: true, message, icon, autoDismiss };
  if (autoDismiss) {
    notificationTimer = setTimeout(() => {
      notification.value.show = false;
    }, duration);
  }
};

const dismissNotification = () => {
  clearTimeout(notificationTimer);
  notification.value.show = false;
};

onUnmounted(() => {
  clearTimeout(notificationTimer);
});

const props = defineProps({
  captcha: String,
});

const email = ref('');
const password = ref('');
const captchaInput = ref('');
const captchaImg = ref(props.captcha || '');
const rememberMe = ref(false);
const showPassword = ref(false);
const isLoading = ref(false);

// 刷新验证码
const refreshCaptcha = () => {
  captchaInput.value = '';
  router.reload({
    only: ['captcha'],
    onSuccess: (page) => {
      captchaImg.value = page.props.captcha || '';
    },
  });
};

// ─── 禁用用户提示（来自后端 disabled 错误，10秒自动消失） ────
const dismissedDisabledNotice = ref(false);
const disabledMessage = computed(() => {
  if (dismissedDisabledNotice.value) return null;
  return page.props.errors?.disabled || null;
});

watch(disabledMessage, (val) => {
  if (val) {
    showNotification(val, 'shield', true, 10000);
    dismissedDisabledNotice.value = true;
  }
}, { immediate: true });

// ─── 登出通知（手动退出10秒自动消失，超时退出不自动消失） ────
const dismissedLogoutNotice = ref(false);
const logoutMessage = computed(() => {
  if (dismissedLogoutNotice.value) return null;
  const manual = localStorage.getItem('manual_logout');
  const timeout = localStorage.getItem('timeout_logout');
  if (manual) return { text: '已安全退出登录', type: 'manual' };
  if (timeout) return { text: '会话已过期，请重新登录', type: 'timeout' };
  return null;
});

watch(logoutMessage, (val) => {
  if (val) {
    localStorage.removeItem('manual_logout');
    localStorage.removeItem('timeout_logout');
    if (val.type === 'manual') {
      showNotification(val.text, 'logout', true, 10000);
    } else {
      showNotification(val.text, 'clock', false);
    }
    dismissedLogoutNotice.value = true;
  }
}, { immediate: true });

const dismissLogoutNotice = () => {
  dismissNotification();
  dismissedLogoutNotice.value = true;
  dismissedDisabledNotice.value = true;
};

const handleLogin = () => {
  if (!captchaInput.value.trim()) {
    showNotification('请输入验证码', 'alert', true, 5000);
    return;
  }

  isLoading.value = true;

  router.post('/admin/login', {
    email: email.value,
    password: password.value,
    captcha: captchaInput.value,
    remember: rememberMe.value,
  }, {
    onFinish: () => {
      isLoading.value = false;
      captchaInput.value = '';
    },
    onError: () => {
      captchaInput.value = '';
      refreshCaptcha();
    },
  });
};

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};
</script>

<template>
  <div class="min-h-screen bg-construct-paper flex items-center justify-center p-8" @click="dismissLogoutNotice">

    <!-- 统一通知（右下角） -->
    <Transition name="slide-up">
      <div v-if="notification.show"
        class="fixed bottom-8 right-8 z-50 max-w-sm">
        <div class="bg-white border-4 border-construct-black p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,0.15)]">
          <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-construct-black flex items-center justify-center">
              <AlertTriangle v-if="notification.icon === 'alert'" class="w-5 h-5 text-construct-red" />
              <ShieldOff v-else-if="notification.icon === 'shield'" class="w-5 h-5 text-white" />
              <Clock v-else-if="notification.icon === 'clock'" class="w-5 h-5 text-white" />
              <LogOut v-else-if="notification.icon === 'logout'" class="w-5 h-5 text-white" />
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-mono text-sm text-gray-600 leading-relaxed">
                {{ notification.message }}
              </p>
            </div>
            <button @click="dismissNotification" class="flex-shrink-0 text-gray-400 hover:text-construct-red transition-colors">
              <X class="w-4 h-4" />
            </button>
          </div>
        </div>
        <div class="w-full h-1.5 bg-construct-black" />
      </div>
    </Transition>

    <div class="w-full max-w-md">
      <!-- 标题 -->
      <div class="text-center mb-12">
        <h1 class="font-display text-4xl tracking-tighter mb-4">ARKHYX // ADMIN</h1>
        <p class="text-gray-600 uppercase text-xs tracking-widest">{{ t('login_subtitle') }}</p>
      </div>

      <!-- 登录卡片 -->
      <div class="bg-white border-4 border-construct-black p-8">
        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- 邮箱输入 -->
          <div>
            <label class="block font-display text-xs tracking-widest uppercase mb-2">
              <Mail class="w-4 h-4 inline mr-2" />
              {{ t('login_email') }}
            </label>
            <input
              v-model="email"
              type="email"
              :placeholder="t('login_email_placeholder')"
              class="w-full px-4 py-3 border-2 border-construct-black font-mono text-sm focus:border-construct-red focus:outline-none transition-colors"
              :disabled="isLoading"
            />
          </div>

          <!-- 密码输入 -->
          <div>
            <label class="block font-display text-xs tracking-widest uppercase mb-2">
              <Lock class="w-4 h-4 inline mr-2" />
              {{ t('login_password') }}
            </label>
            <div class="relative">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                :placeholder="t('login_password_placeholder')"
                class="w-full px-4 py-3 pr-12 border-2 border-construct-black font-mono text-sm focus:border-construct-red focus:outline-none transition-colors"
                :disabled="isLoading"
                @keyup.enter="handleLogin"
              />
              <button
                type="button"
                @click="togglePasswordVisibility"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-construct-red transition-colors"
              >
                <Eye v-if="!showPassword" class="w-5 h-5" />
                <EyeOff v-else class="w-5 h-5" />
              </button>
            </div>
          </div>

          <!-- 验证码 -->
          <div>
            <label class="block font-display text-xs tracking-widest uppercase mb-2">
              <Shield class="w-4 h-4 inline mr-2" />
              {{ t('auth.captcha') }}
            </label>
            <div class="flex gap-2 items-stretch">
              <input
                v-model="captchaInput"
                type="text"
                maxlength="4"
                autocomplete="off"
                :placeholder="t('auth.captcha_placeholder')"
                class="flex-1 px-4 py-3 border-2 border-construct-black font-mono text-sm tracking-widest uppercase focus:border-construct-red focus:outline-none transition-colors"
                :disabled="isLoading"
              />
              <img v-if="captchaImg" :src="captchaImg" alt="Captcha" class="h-full w-auto border-2 border-construct-black cursor-pointer" @click="refreshCaptcha" />
              <div v-else class="border-2 border-construct-black bg-gray-100 flex items-center justify-center px-3 text-xs text-gray-400 shrink-0">CAPTCHA</div>
            </div>
          </div>

          <!-- 记住密码 -->
          <div class="flex items-center">
            <input
              v-model="rememberMe"
              type="checkbox"
              id="remember-me"
              class="w-4 h-4 border-2 border-construct-black accent-construct-red cursor-pointer"
            />
            <label for="remember-me" class="ml-2 text-xs font-bold tracking-widest uppercase cursor-pointer">
              {{ t('login_remember_me') }}
            </label>
          </div>

          <!-- 错误提示 -->
          <div v-if="$page.props.errors && ($page.props.errors.email || $page.props.errors.captcha)" class="flex items-center gap-2 text-red-600 text-sm">
            <AlertCircle class="w-4 h-4" />
            <span>{{ $page.props.errors.email || $page.props.errors.captcha }}</span>
          </div>

          <!-- 登录按钮 -->
          <button
            type="submit"
            :disabled="isLoading"
            class="w-full bg-construct-black text-white py-4 font-display tracking-widest uppercase text-sm hover:bg-construct-red transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isLoading ? t('login_authenticating') : t('login_button') }}
          </button>
        </form>

        <!-- 返回前台 -->
        <div class="mt-8 text-center">
          <a
            href="/"
            class="text-xs tracking-widest uppercase text-gray-500 hover:text-construct-red transition-colors"
          >
            ← {{ t('login_return_site') }}
          </a>
        </div>
      </div>

      <!-- 装饰性边框 -->
      <div class="w-full h-2 bg-construct-black mt-4" />
    </div>
  </div>
</template>

<style scoped>
.slide-up-enter-active {
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-leave-active {
  transition: all 0.2s ease-in;
}
.slide-up-enter-from {
  opacity: 0;
  transform: translateY(24px) scale(0.95);
}
.slide-up-leave-to {
  opacity: 0;
  transform: translateY(12px) scale(0.98);
}
</style>
