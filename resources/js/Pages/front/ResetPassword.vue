<script setup>
/**
 * 前台密码重置页（独立全屏）
 * 从邮件链接进入，携带 token 和 email 参数
 */
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { usePageSeo } from '../../composables/usePageSeo';
import { Lock, AlertCircle, Eye, EyeOff } from 'lucide-vue-next';

const { t } = useI18n();

const props = defineProps({
  token: { type: String, required: true },
  email: { type: String, required: true },
});

const { SeoHead } = usePageSeo({ title: t('auth.reset_password_title') });

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

const submitReset = () => {
  form.post(route('front.password.update'), {
    onFinish: () => {
      form.reset('password', 'password_confirmation');
    },
    onError: (errors) => {
      // 错误会通过 watch 捕获并显示
    },
  });
};

// 密码可见性
const showPassword = ref(false);
const showConfirm = ref(false);

// 表单错误 → 右下角 Toast
const bottomLeftToast = ref({ show: false, message: '' });
const showBottomLeftError = (msg) => {
  bottomLeftToast.value = { show: true, message: msg };
  setTimeout(() => { bottomLeftToast.value.show = false; }, 5000);
};

watch(() => form.errors, (errors) => {
  const msg = errors.email || errors.password || errors.token;
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
        <AlertCircle class="w-5 h-5 text-construct-red shrink-0 mt-0.5" />
        <p class="text-xs sm:text-sm font-mono text-construct-black flex-1">{{ bottomLeftToast.message }}</p>
        <button @click="bottomLeftToast.show = false" class="text-gray-400 hover:text-construct-red transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <div class="h-1 bg-construct-red" />
    </div>
  </Transition>

  <div class="min-h-screen bg-construct-paper flex items-center justify-center p-8">
    <div class="w-full max-w-md">
      <!-- 标题 -->
      <div class="text-center mb-12">
        <h1 class="font-display text-4xl tracking-tighter mb-4">
          ARKHYX // <span class="uppercase">RESET PASSWORD</span>
        </h1>
        <p class="text-gray-600 uppercase text-xs tracking-widest">
          {{ t('auth.reset_password_subtitle') }}
        </p>
      </div>

      <!-- 重置密码卡片 -->
      <div class="bg-white border-4 border-construct-black p-8">
        <form @submit.prevent="submitReset" class="space-y-5">
          <!-- 隐藏字段 -->
          <input type="hidden" v-model="form.token" />
          <input type="hidden" v-model="form.email" />

          <!-- 新密码 -->
          <div>
            <label class="block font-display text-xs tracking-widest uppercase mb-2">
              <Lock class="w-4 h-4 inline mr-2" />{{ t('auth.new_password') }}
            </label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                :placeholder="t('auth.new_password_placeholder')"
                class="w-full px-4 py-3 pr-12 border-2 border-construct-black font-mono text-sm focus:border-construct-red focus:outline-none transition-colors"
                :disabled="form.processing"
                @keyup.enter="submitReset"
              />
              <button type="button" @click="showPassword = !showPassword" tabindex="-1"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-construct-red transition-colors">
                <Eye v-if="!showPassword" class="w-5 h-5" />
                <EyeOff v-else class="w-5 h-5" />
              </button>
            </div>
          </div>

          <!-- 确认密码 -->
          <div>
            <label class="block font-display text-xs tracking-widest uppercase mb-2">
              <Lock class="w-4 h-4 inline mr-2" />{{ t('auth.confirm_new_password') }}
            </label>
            <div class="relative">
              <input
                v-model="form.password_confirmation"
                :type="showConfirm ? 'text' : 'password'"
                required
                :placeholder="t('auth.confirm_new_password_placeholder')"
                class="w-full px-4 py-3 pr-12 border-2 border-construct-black font-mono text-sm focus:border-construct-red focus:outline-none transition-colors"
                :disabled="form.processing"
                @keyup.enter="submitReset"
              />
              <button type="button" @click="showConfirm = !showConfirm" tabindex="-1"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-construct-red transition-colors">
                <Eye v-if="!showConfirm" class="w-5 h-5" />
                <EyeOff v-else class="w-5 h-5" />
              </button>
            </div>
          </div>

          <!-- 提交按钮 -->
          <button type="submit" :disabled="form.processing"
            class="w-full bg-construct-black text-white py-4 font-display tracking-widest uppercase text-sm hover:bg-construct-red transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            {{ form.processing ? t('auth.resetting') : t('auth.reset_password') }}
          </button>
        </form>

        <!-- 返回登录 -->
        <div class="mt-8 pt-6 border-t-2 border-construct-black text-center">
          <a :href="route('front.login')"
            class="text-xs tracking-widest uppercase text-gray-500 hover:text-construct-red transition-colors">
            ← {{ t('auth.back_to_login') }}
          </a>
        </div>

        <!-- 返回首页 -->
        <div class="mt-4 text-center">
          <a href="/" class="text-xs tracking-widest uppercase text-gray-500 hover:text-construct-red transition-colors">
            ← {{ t('nav_home') }}
          </a>
        </div>
      </div>

      <!-- 装饰性边框 -->
      <div class="w-full h-2 bg-construct-black mt-4" />
    </div>
  </div>
</template>
