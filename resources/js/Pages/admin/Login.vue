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
 */
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Lock, Mail, AlertCircle, Eye, EyeOff } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { t } = useI18n();
const { isDarkMode, initTheme } = useTheme();

const email = ref('');
const password = ref('');
const rememberMe = ref(false);
const showPassword = ref(false);
const isLoading = ref(false);

const handleLogin = () => {
  isLoading.value = true;

  router.post('/admin/login', {
    email: email.value,
    password: password.value,
    remember: rememberMe.value,
  }, {
    onFinish: () => {
      isLoading.value = false;
    },
  });
};

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};
</script>

<template>
  <div class="min-h-screen bg-construct-paper flex items-center justify-center p-8">
    <div class="w-full max-w-md">
      <!-- 标题 -->
      <div class="text-center mb-12">
        <h1 class="font-display text-4xl tracking-tighter mb-4">ARCHYX // ADMIN</h1>
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
          <div v-if="$page.props.errors && $page.props.errors.email" class="flex items-center gap-2 text-red-600 text-sm">
            <AlertCircle class="w-4 h-4" />
            <span>{{ $page.props.errors.email }}</span>
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
