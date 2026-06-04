<script setup>
/**
 * 前台密码重置页（独立全屏）
 * 从邮件链接进入，携带 token 和 email 参数
 */
import { ref, computed } from 'vue';
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
      form.reset('password');
      form.reset('password_confirmation');
    },
  });
};

// 密码可见性
const showPassword = ref(false);
const showConfirm = ref(false);

// 错误信息
const formErrors = computed(() => {
  return form.errors.email || form.errors.password || null;
});
</script>

<template>
  <SeoHead />

  <div class="min-h-screen bg-construct-paper flex items-center justify-center p-8">
    <div class="w-full max-w-md">
      <!-- 标题 -->
      <div class="text-center mb-12">
        <h1 class="font-display text-4xl tracking-tighter mb-4">
          ARCHYX // <span class="uppercase">RESET PASSWORD</span>
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

          <!-- 错误提示 -->
          <div v-if="formErrors" class="flex items-center gap-2 text-red-600 text-sm">
            <AlertCircle class="w-4 h-4" /><span>{{ formErrors }}</span>
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
