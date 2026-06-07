<script setup>
/**
 * 前台认证页（登录 / 注册 / 忘记密码，独立全屏）
 * - mode="login" → 邮箱密码登录 + OAuth
 * - mode="register" → 邮箱密码注册
 * - mode="forgot-password" → 重置密码
 */
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { usePageSeo } from '../../composables/usePageSeo';
import { Lock, Mail, User, AlertCircle, Eye, EyeOff, ArrowLeft, X, AlertTriangle, RefreshCw, Shield } from 'lucide-vue-next';

const { t } = useI18n();

// 左下角错误提示（独立于 admin ToastContainer）
const bottomLeftToast = ref({ show: false, message: '' });
let toastTimer = null;

const showBottomLeftError = (message) => {
  clearTimeout(toastTimer);
  bottomLeftToast.value = { show: true, message };
  toastTimer = setTimeout(() => {
    bottomLeftToast.value.show = false;
  }, 5000);
};

// 监听 forgot-password：用户不存在时后端返回 flash.user_not_found = true
onMounted(() => {
  if (usePage().props.flash?.user_not_found) {
    showBottomLeftError(t('auth.user_not_found'));
  }
});

watch(() => usePage().props.flash?.user_not_found, (newVal) => {
  if (newVal) {
    showBottomLeftError(t('auth.user_not_found'));
  }
});

const props = defineProps({
  mode: { type: String, default: 'login' }, // 'login' | 'register' | 'forgot-password'
  captcha: { type: String, default: '' },
  providers: { type: Array, default: () => [] },
});

// 响应式模式（允许页面内切换）
const currentMode = ref(props.mode);

const isLogin = computed(() => currentMode.value === 'login');
const isForgot = computed(() => currentMode.value === 'forgot-password');

// 动态 SEO 标题
const seoTitle = ref(
  isLogin.value ? t('auth.login_title')
  : isForgot.value ? t('auth.forgot_password_title')
  : t('auth.register_title')
);
watch(currentMode, (mode) => {
  if (mode === 'forgot-password') seoTitle.value = t('auth.forgot_password_title');
  else if (mode === 'login') seoTitle.value = t('auth.login_title');
  else seoTitle.value = t('auth.register_title');
});

const { SeoHead } = usePageSeo({ title: seoTitle.value });

// —— 图片验证码 ——
const captchaImg = ref(props.captcha || '');
const captchaInput = ref('');
const loginCaptchaInput = ref('');

const refreshCaptcha = () => {
  loginCaptchaInput.value = '';
  router.reload({
    only: ['captcha'],
    onSuccess: (page) => {
      captchaImg.value = page.props.captcha || '';
    },
  });
};

// —— 邮箱验证码发送 ——
const sendingCode = ref(false);
const codeCooldown = ref(0);
const emailCode = ref('');
let cooldownTimer = null;

const sendEmailCode = async () => {
  if (sendingCode.value || codeCooldown.value > 0) return;
  sendingCode.value = true;
  try {
    const res = await fetch('/api/send-verification-code', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
      body: JSON.stringify({ email: registerForm.email }),
    });
    if (res.ok) {
      showBottomLeftError(t('auth.code_sent'));
      codeCooldown.value = 60;
      cooldownTimer = setInterval(() => {
        codeCooldown.value--;
        if (codeCooldown.value <= 0) { clearInterval(cooldownTimer); cooldownTimer = null; }
      }, 1000);
    } else {
      const data = await res.json();
      showBottomLeftError(data.message || t('auth.invalid_captcha'));
      refreshCaptcha();
    }
  } catch {
    showBottomLeftError(t('auth.email_send_failed'));
    refreshCaptcha();
  }
  sendingCode.value = false;
};

onUnmounted(() => { if (cooldownTimer) clearInterval(cooldownTimer); });

// —— 忘记密码表单 ——
const forgotForm = useForm({
  credential: '',
});

const submitForgotPassword = () => {
  forgotForm.post(route('front.password.email'));
};

// —— 登录表单 ——
const loginForm = useForm({
  email: '',
  password: '',
  captcha: '',
  remember: false,
});

const submitLogin = () => {
  if (!loginCaptchaInput.value.trim()) {
    showBottomLeftError(t('auth.captcha_required') || '请输入验证码');
    return;
  }
  loginForm.captcha = loginCaptchaInput.value;
  loginForm.post(route('front.login.handle'), {
    onFinish: () => {
      loginForm.reset('password', 'captcha');
      loginCaptchaInput.value = '';
    },
  });
};

// —— 注册表单 ——
const registerForm = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  captcha: '',
  verification_code: '',
});

const submitRegister = () => {
  registerForm.captcha = captchaInput.value;
  registerForm.verification_code = emailCode.value;
  registerForm.post(route('front.register.handle'), {
    onFinish: () => {
      registerForm.reset();
      captchaInput.value = '';
      emailCode.value = '';
      codeCooldown.value = 0;
      if (cooldownTimer) { clearInterval(cooldownTimer); cooldownTimer = null; }
    },
  });
};

// 密码可见性
const showPassword = ref(false);
const showConfirm = ref(false);

// OAuth
const oauthLogin = (provider) => {
  window.location.href = route('front.social.redirect', { provider });
};

const hasOAuth = computed(() => props.providers.length > 0 && usePage().props.siteConfig?.social_login !== false);

const providerIcons = {
  google: 'M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.627 0-8.4-3.733-8.4-8.373 0-4.64 3.773-8.373 8.4-8.373 2.533 0 4.413 1.013 5.813 2.347l2.347-2.347C18.213 2.347 15.56 1 12.48 1 5.867 1 .52 6.08.52 12.36s5.347 11.36 11.96 11.36c3.52 0 6.187-1.28 8.267-3.693 2.133-2.133 2.8-5.12 2.8-7.507 0-.64-.053-1.253-.16-1.84H12.48v3.24z',
  github: 'M12 1C5.922 1 1 5.922 1 12c0 4.891 3.148 9.016 7.521 10.477.55.1.753-.239.753-.53 0-.26-.01-1.118-.015-2.02-3.06.665-3.706-1.299-3.706-1.299-.5-1.27-1.221-1.608-1.221-1.608-.999-.683.075-.669.075-.669 1.104.078 1.685 1.134 1.685 1.134.981 1.68 2.575 1.195 3.202.914.1-.71.384-1.195.698-1.47-2.442-.278-5.01-1.221-5.01-5.438 0-1.202.429-2.185 1.133-2.955-.114-.278-.492-1.398.108-2.913 0 0 .925-.295 3.03 1.129A10.56 10.56 0 0112 6.322a10.56 10.56 0 012.756.375c2.105-1.424 3.03-1.129 3.03-1.129.6 1.515.222 2.635.108 2.913.704.77 1.133 1.753 1.133 2.955 0 4.226-2.572 5.157-5.022 5.43.395.34.747 1.01.747 2.037 0 1.47-.014 2.657-.014 3.017 0 .294.2.637.756.53C19.854 21.013 23 16.89 23 12c0-6.078-4.922-11-11-11z',
};

const providerNames = { google: 'Google', github: 'GitHub' };

// 表单错误 → 右下角 Toast 弹窗
watch(() => { return { ...loginForm.errors }; }, (errors) => {
  const msg = errors.email || errors.password || errors.captcha;
  if (msg) showBottomLeftError(msg);
}, { deep: true });

watch(() => { return { ...forgotForm.errors }; }, (errors) => {
  if (errors.credential) showBottomLeftError(errors.credential);
}, { deep: true });

watch(() => { return { ...registerForm.errors }; }, (errors) => {
  const msg = errors.name || errors.email || errors.password || errors.captcha || errors.verification_code;
  if (msg) showBottomLeftError(msg);
}, { deep: true });
</script>

<template>
  <SeoHead />

  <!-- 右下角错误弹窗 -->
  <Transition name="toast-slide">
    <div v-if="bottomLeftToast.show"
      class="fixed bottom-4 right-4 left-4 sm:left-auto sm:right-6 sm:bottom-6 z-50 sm:max-w-sm bg-white border-4 border-construct-black shadow-lg">
      <div class="flex items-start gap-3 p-3 sm:p-4">
        <AlertTriangle class="w-5 h-5 text-construct-red shrink-0 mt-0.5" />
        <p class="text-xs sm:text-sm font-mono text-construct-black flex-1">{{ bottomLeftToast.message }}</p>
        <button @click="bottomLeftToast.show = false" class="text-gray-400 hover:text-construct-red transition-colors">
          <X class="w-4 h-4" />
        </button>
      </div>
      <div class="h-1 bg-construct-red" />
    </div>
  </Transition>

  <div class="min-h-dvh bg-construct-paper flex items-start justify-center p-4 sm:p-6 md:p-8 overflow-y-auto">
    <div class="w-full max-w-md py-4 sm:py-6 md:py-10">
      <!-- 标题 -->
      <div class="text-center mb-4 sm:mb-6 md:mb-10">
        <h1 class="font-display text-xl sm:text-3xl md:text-4xl tracking-tighter mb-1.5 sm:mb-3 md:mb-4">
          <span class="block sm:inline">ARKHYX</span>
          <span class="block sm:hidden text-lg mt-1 uppercase">
            {{ isForgot ? 'RESET PASSWORD' : (isLogin ? 'LOGIN' : 'REGISTER') }}
          </span>
          <span class="hidden sm:inline"> // <span class="uppercase">
            {{ isForgot ? 'RESET PASSWORD' : (isLogin ? 'LOGIN' : 'REGISTER') }}
          </span></span>
        </h1>
        <p class="text-gray- uppercase text-[9px] sm:text-xs tracking-widest">
          {{ isForgot ? t('auth.forgot_password_subtitle') : (isLogin ? t('auth.login_subtitle') : t('auth.register_subtitle')) }}
        </p>
      </div>

      <!-- 认证卡片 -->
      <div class="bg-white border-3 sm:border-4 border-construct-black p-4 sm:p-6 md:p-8">

        <!-- ====== 登录表单 ====== -->
        <form v-if="isLogin" @submit.prevent="submitLogin" class="space-y-3 sm:space-y-5 md:space-y-6">
          <div>
            <label class="block font-display text-[10px] sm:text-xs tracking-widest uppercase mb-1.5 sm:mb-2">
              <Mail class="w-3.5 h-3.5 sm:w-4 sm:h-4 inline mr-1.5 sm:mr-2" />{{ t('auth.email') }}
            </label>
            <input
              v-model="loginForm.email"
              type="email"
              placeholder="your@email.com"
              class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border-2 border-construct-black font-mono text-xs sm:text-sm focus:border-construct-red focus:outline-none transition-colors"
              :disabled="loginForm.processing"
              @keyup.enter="submitLogin"
            />
          </div>
          <div>
            <div class="flex justify-between items-center mb-1.5 sm:mb-2">
              <label class="font-display text-[10px] sm:text-xs tracking-widest uppercase">
                <Lock class="w-3.5 h-3.5 sm:w-4 sm:h-4 inline mr-1.5 sm:mr-2" />{{ t('auth.password') }}
              </label>
              <button type="button" @click="currentMode = 'forgot-password'"
                class="text-[10px] sm:text-xs tracking-widest uppercase text-gray-400 hover:text-construct-red transition-colors">
                FORGOT?
              </button>
            </div>
            <div class="relative">
              <input
                v-model="loginForm.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 pr-10 sm:pr-12 border-2 border-construct-black font-mono text-xs sm:text-sm focus:border-construct-red focus:outline-none transition-colors"
                :disabled="loginForm.processing"
                @keyup.enter="submitLogin"
              />
              <button type="button" @click="showPassword = !showPassword" tabindex="-1"
                class="absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-construct-red transition-colors">
                <Eye v-if="!showPassword" class="w-4 h-4 sm:w-5 sm:h-5" />
                <EyeOff v-else class="w-4 h-4 sm:w-5 sm:h-5" />
              </button>
            </div>
          </div>

          <!-- 图片验证码 -->
          <div>
            <label class="block font-display text-[10px] sm:text-xs tracking-widest uppercase mb-1.5 sm:mb-2">
              <Shield class="w-3.5 h-3.5 sm:w-4 sm:h-4 inline mr-1.5 sm:mr-2" />{{ t('auth.captcha') }}
            </label>
            <div class="flex gap-1.5 sm:gap-2 items-stretch">
              <input v-model="loginCaptchaInput" type="text" required maxlength="4" autocomplete="off" :placeholder="t('auth.captcha_placeholder')"
                class="flex-1 px-2.5 sm:px-3 py-2.5 sm:py-3 border-2 border-construct-black font-mono text-xs sm:text-sm focus:border-construct-red focus:outline-none uppercase placeholder:normal-case"
                :disabled="loginForm.processing" />
              <img v-if="captchaImg" :src="captchaImg" alt="Captcha" class="h-full w-auto border-2 border-construct-black cursor-pointer" @click="refreshCaptcha" />
              <div v-else class="border-2 border-construct-black bg-gray-100 flex items-center justify-center px-3 text-[10px] text-gray-400 shrink-0">CAPTCHA</div>
            </div>
          </div>

          <div class="flex items-center">
            <input v-model="loginForm.remember" type="checkbox" id="remember-me"
              class="w-3.5 h-3.5 sm:w-4 sm:h-4 border-2 border-construct-black accent-construct-red cursor-pointer" />
            <label for="remember-me" class="ml-2 text-[10px] sm:text-xs font-bold tracking-widest uppercase cursor-pointer">
              {{ t('auth.remember_me') }}
            </label>
          </div>
          <button type="submit" :disabled="loginForm.processing"
            class="w-full bg-construct-black text-white py-3 sm:py-4 font-display tracking-widest uppercase text-xs sm:text-sm hover:bg-construct-red transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            {{ loginForm.processing ? t('auth.logging_in') : t('auth.login') }}
          </button>
        </form>

        <!-- ====== 忘记密码表单 ====== -->
        <form v-else-if="isForgot" @submit.prevent="submitForgotPassword" class="space-y-3 sm:space-y-4 md:space-y-5">
          <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
            {{ t('auth.forgot_password_desc') }}
          </p>
          <div>
            <label class="block font-display text-[10px] sm:text-xs tracking-widest uppercase mb-1.5 sm:mb-2">
              <User class="w-3.5 h-3.5 sm:w-4 sm:h-4 inline mr-1.5 sm:mr-2" />{{ t('auth.username_or_email') }}
            </label>
            <input
              v-model="forgotForm.credential"
              type="text"
              required
              :placeholder="t('auth.username_or_email_placeholder')"
              class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border-2 border-construct-black font-mono text-xs sm:text-sm focus:border-construct-red focus:outline-none transition-colors"
              :disabled="forgotForm.processing"
              @keyup.enter="submitForgotPassword"
            />
          </div>
          <div v-if="forgotForm.recentlySuccessful && !usePage().props.flash?.user_not_found" class="flex items-center gap-2 text-green-600 text-xs sm:text-sm">
            <AlertCircle class="w-3.5 h-3.5 sm:w-4 sm:h-4" /><span>{{ t('auth.reset_link_sent') }}</span>
          </div>
          <button type="submit" :disabled="forgotForm.processing"
            class="w-full bg-construct-black text-white py-3 sm:py-4 font-display tracking-widest uppercase text-xs sm:text-sm hover:bg-construct-red transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            {{ forgotForm.processing ? t('auth.sending_reset_link') : t('auth.send_reset_link') }}
          </button>
          <div class="text-center">
            <button type="button" @click="currentMode = 'login'"
              class="inline-flex items-center gap-1 text-[10px] sm:text-xs tracking-widest uppercase text-gray-500 hover:text-construct-red transition-colors">
              <ArrowLeft class="w-3 h-3" />
              {{ t('auth.back_to_login') }}
            </button>
          </div>
        </form>

        <!-- ====== 注册表单 ====== -->
        <form v-else @submit.prevent="submitRegister" class="space-y-3 sm:space-y-4 md:space-y-5">
          <div>
            <label class="block font-display text-[10px] sm:text-xs tracking-widest uppercase mb-1.5 sm:mb-2">
              <User class="w-3.5 h-3.5 sm:w-4 sm:h-4 inline mr-1.5 sm:mr-2" />{{ t('auth.name') }}
            </label>
            <input v-model="registerForm.name" type="text" required autocomplete="off" :placeholder="t('auth.name_placeholder')"
              class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border-2 border-construct-black font-mono text-xs sm:text-sm focus:border-construct-red focus:outline-none transition-colors"
              :disabled="registerForm.processing" />
          </div>
          <div>
            <label class="block font-display text-[10px] sm:text-xs tracking-widest uppercase mb-1.5 sm:mb-2">
              <Mail class="w-3.5 h-3.5 sm:w-4 sm:h-4 inline mr-1.5 sm:mr-2" />{{ t('auth.email') }}
            </label>
            <input v-model="registerForm.email" type="email" required autocomplete="nope" placeholder="your@email.com"
              class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border-2 border-construct-black font-mono text-xs sm:text-sm focus:border-construct-red focus:outline-none transition-colors"
              :disabled="registerForm.processing" />
          </div>

          <!-- 邮箱验证码：输入框 + 发送按钮同行 -->
          <div>
            <label class="block font-display text-[10px] sm:text-xs tracking-widest uppercase mb-1.5 sm:mb-2">
              <Mail class="w-3.5 h-3.5 sm:w-4 sm:h-4 inline mr-1.5 sm:mr-2" />{{ t('auth.verification_code') }}
            </label>
            <div class="flex gap-1.5 sm:gap-2 items-stretch">
              <input v-model="emailCode" type="text" required maxlength="6" autocomplete="one-time-code" :placeholder="t('auth.code_placeholder')"
                class="flex-1 px-3 sm:px-4 py-2.5 sm:py-3 border-2 border-construct-black font-mono text-xs sm:text-sm tracking-widest text-center focus:border-construct-red focus:outline-none transition-colors"
                :disabled="registerForm.processing" />
              <button type="button" @click="sendEmailCode" :disabled="sendingCode || codeCooldown > 0 || registerForm.processing || !registerForm.email"
                class="px-3 sm:px-4 py-2.5 sm:py-3 font-display tracking-widest uppercase text-[10px] sm:text-xs border-2 border-construct-black hover:bg-construct-black hover:text-white transition-colors disabled:opacity-40 disabled:cursor-not-allowed shrink-0 whitespace-nowrap">
                {{ codeCooldown > 0 ? t('auth.resend_code', { s: codeCooldown }) : (sendingCode ? '...' : t('auth.send_code')) }}
              </button>
            </div>
          </div>

          <div>
            <label class="block font-display text-[10px] sm:text-xs tracking-widest uppercase mb-1.5 sm:mb-2">
              <Lock class="w-3.5 h-3.5 sm:w-4 sm:h-4 inline mr-1.5 sm:mr-2" />{{ t('auth.password') }}
            </label>
            <div class="relative">
              <input v-model="registerForm.password" :type="showPassword ? 'text' : 'password'" required placeholder="••••••••" autocomplete="new-password"
                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 pr-10 sm:pr-12 border-2 border-construct-black font-mono text-xs sm:text-sm focus:border-construct-red focus:outline-none transition-colors"
                :disabled="registerForm.processing" />
              <button type="button" @click="showPassword = !showPassword" tabindex="-1"
                class="absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-construct-red transition-colors">
                <Eye v-if="!showPassword" class="w-4 h-4 sm:w-5 sm:h-5" /><EyeOff v-else class="w-4 h-4 sm:w-5 sm:h-5" />
              </button>
            </div>
          </div>
          <div v-if="registerForm.password">
            <label class="block font-display text-[10px] sm:text-xs tracking-widest uppercase mb-1.5 sm:mb-2">
              <Lock class="w-3.5 h-3.5 sm:w-4 sm:h-4 inline mr-1.5 sm:mr-2" />{{ t('auth.confirm_password') }}
            </label>
            <div class="relative">
              <input v-model="registerForm.password_confirmation" :type="showConfirm ? 'text' : 'password'" required placeholder="••••••••"
                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 pr-10 sm:pr-12 border-2 border-construct-black font-mono text-xs sm:text-sm focus:border-construct-red focus:outline-none transition-colors"
                :disabled="registerForm.processing" />
              <button type="button" @click="showConfirm = !showConfirm" tabindex="-1"
                class="absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-construct-red transition-colors">
                <Eye v-if="!showConfirm" class="w-4 h-4 sm:w-5 sm:h-5" /><EyeOff v-else class="w-4 h-4 sm:w-5 sm:h-5" />
              </button>
            </div>
          </div>

          <!-- 图片验证码 -->
          <div>
            <label class="block font-display text-[10px] sm:text-xs tracking-widest uppercase mb-1.5 sm:mb-2">
              <Shield class="w-3.5 h-3.5 sm:w-4 sm:h-4 inline mr-1.5 sm:mr-2" />{{ t('auth.captcha') }}
            </label>
            <div class="flex gap-1.5 sm:gap-2 items-stretch">
              <input v-model="captchaInput" type="text" required maxlength="4" autocomplete="off" :placeholder="t('auth.captcha_placeholder')"
                class="flex-1 px-2.5 sm:px-3 py-2.5 sm:py-3 border-2 border-construct-black font-mono text-xs sm:text-sm focus:border-construct-red focus:outline-none uppercase placeholder:normal-case"
                :disabled="registerForm.processing" />
              <img v-if="captchaImg" :src="captchaImg" alt="Captcha" class="h-full w-auto border-2 border-construct-black cursor-pointer" @click="refreshCaptcha" />
              <div v-else class="border-2 border-construct-black bg-gray-100 flex items-center justify-center px-3 text-[10px] text-gray-400 shrink-0">CAPTCHA</div>
            </div>
          </div>

          <button type="submit" :disabled="registerForm.processing"
            class="w-full bg-construct-black text-white py-3 sm:py-4 font-display tracking-widest uppercase text-xs sm:text-sm hover:bg-construct-red transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            {{ registerForm.processing ? t('auth.creating_account') : t('auth.create_account') }}
          </button>
        </form>

        <!-- ====== OAuth（仅登录模式显示） ====== -->
        <template v-if="isLogin && hasOAuth">
          <div class="relative my-4 sm:my-5 md:my-6">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t-2 border-construct-black"></div></div>
            <div class="relative flex justify-center">
              <span class="px-3 sm:px-4 bg-white font-display text-[10px] sm:text-xs tracking-widest uppercase text-gray-500">
                {{ t('auth.or_continue_with') }}
              </span>
            </div>
          </div>
          <div class="grid gap-2 sm:gap-3"
            :class="{
              'grid-cols-1': providers.length <= 1,
              'grid-cols-2': providers.length === 2,
              'grid-cols-3': providers.length >= 3,
            }">
            <button v-for="p in providers" :key="p.provider" @click="oauthLogin(p.provider)"
              class="flex items-center justify-center gap-2 py-2.5 sm:py-3 border-2 border-construct-black text-xs sm:text-sm font-medium hover:bg-construct-black hover:text-white transition-colors">
              <svg v-if="providerIcons[p.provider]" class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="currentColor">
                <path :d="providerIcons[p.provider]" />
              </svg>
              <span>{{ providerNames[p.provider] || p.name }}</span>
            </button>
          </div>
        </template>

        <!-- ====== 模式切换（忘记密码时不显示） ====== -->
        <div v-if="!isForgot" class="mt-4 sm:mt-6 md:mt-8 pt-3 sm:pt-5 md:pt-6 border-t-2 border-construct-black text-center">
          <button
            @click="currentMode = isLogin ? 'register' : 'login'"
            class="text-[10px] sm:text-xs tracking-widest uppercase text-gray-500 hover:text-construct-red transition-colors"
          >
            <template v-if="isLogin">
              {{ t('auth.no_account') }} → {{ t('auth.create_account') }}
            </template>
            <template v-else>
              {{ t('auth.already_have_account') }} → {{ t('auth.login') }}
            </template>
          </button>
        </div>

        <!-- 返回首页 -->
        <div class="mt-3 sm:mt-4 md:mt-6 text-center">
          <a href="/" class="text-[10px] sm:text-xs tracking-widest uppercase text-gray-500 hover:text-construct-red transition-colors">
            ← {{ t('nav_home') }}
          </a>
        </div>
      </div>

      <!-- 装饰性边框 -->
      <div class="w-full h-1.5 sm:h-2 bg-construct-black mt-2 sm:mt-3 md:mt-4" />
    </div>
  </div>
</template>

<style scoped>
.toast-slide-enter-active {
  transition: all 0.3s ease-out;
}
.toast-slide-leave-active {
  transition: all 0.2s ease-in;
}
.toast-slide-enter-from {
  opacity: 0;
  transform: translateX(20px);
}
.toast-slide-leave-to {
  opacity: 0;
  transform: translateX(20px);
}
</style>
