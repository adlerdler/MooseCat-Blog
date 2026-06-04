<script setup>
/**
 * 用户个人中心页（独立全屏，无侧边栏/页脚）
 * 风格：brutalist/constructivist，与 Auth.vue 保持一致
 */
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { usePageSeo } from '../../composables/usePageSeo';
import { Calendar, Heart, LogOut, ArrowLeft } from 'lucide-vue-next';

const { t } = useI18n();
const pageProps = usePage().props;

const props = defineProps({
  user: { type: Object, required: true },
  likedItems: { type: Object, default: () => ({ data: [] }) },
});

const { SeoHead } = usePageSeo({ title: t('auth.profile_title') });

const logoutForm = useForm({});

const handleLogout = () => {
  logoutForm.post(route('front.logout'));
};

// 格式化日期
const formatDate = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString(
    pageProps.locale === 'zh' ? 'zh-CN' : 'en-US',
    { year: 'numeric', month: 'long', day: 'numeric' }
  );
};

// 点赞类型标签
const likeTypeLabel = (type) => {
  const map = {
    'App\\Models\\Post': t('common.post'),
    'App\\Models\\Video': t('common.video'),
    'App\\Models\\Project': t('common.project'),
  };
  return map[type] || type;
};

// 点赞项链接
const likeItemUrl = (item) => {
  if (!item.interactable) return '#';
  const prefix = item.interactable_type === 'App\\Models\\Post'
    ? 'blog' : item.interactable_type === 'App\\Models\\Video'
    ? 'videos' : 'projects';
  return `/${prefix}/${item.interactable.slug}`;
};

// 类型徽章颜色
const typeBadgeClass = (type) => {
  const map = {
    'App\\Models\\Post': 'bg-blue-100 text-blue-700 border-blue-300',
    'App\\Models\\Video': 'bg-emerald-100 text-emerald-700 border-emerald-300',
    'App\\Models\\Project': 'bg-purple-100 text-purple-700 border-purple-300',
  };
  return map[type] || 'bg-gray-100 text-gray-600 border-gray-300';
};
</script>

<template>
  <SeoHead />

  <div class="min-h-screen bg-construct-paper flex flex-col items-center p-8">
    <div class="w-full max-w-3xl">

      <!-- 标题 -->
      <div class="text-center mb-12">
        <h1 class="font-display text-4xl tracking-tighter mb-4">
          ARCHYX // <span class="uppercase">PROFILE</span>
        </h1>
        <p class="text-gray-600 uppercase text-xs tracking-widest">
          {{ t('auth.profile_title') }}
        </p>
      </div>

      <!-- 用户信息卡片 -->
      <div class="bg-white border-4 border-construct-black mb-6">
        <div class="p-8">
          <div class="flex flex-col sm:flex-row items-center gap-6">
            <!-- 头像 -->
            <div class="flex-shrink-0">
              <img
                v-if="user.author_profile?.avatar"
                :src="user.author_profile.avatar"
                :alt="user.name"
                class="w-24 h-24 rounded-full object-cover border-4 border-construct-black"
              />
              <div
                v-else
                class="w-24 h-24 border-4 border-construct-black bg-construct-black flex items-center justify-center"
              >
                <span class="text-3xl font-bold text-white font-display">
                  {{ user.name?.charAt(0)?.toUpperCase() }}
                </span>
              </div>
            </div>

            <!-- 用户信息 -->
            <div class="flex-1 text-center sm:text-left">
              <h2 class="text-2xl font-display text-construct-black mb-1">{{ user.name }}</h2>
              <p class="text-sm text-gray-500 font-mono mb-4">{{ user.email }}</p>
              <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 text-xs">
                <span class="inline-flex items-center gap-1 font-mono text-gray-600">
                  <Calendar class="w-3.5 h-3.5" />
                  {{ t('auth.member_since') }}: {{ formatDate(user.created_at) }}
                </span>
                <span
                  class="px-2.5 py-1 border-2 border-construct-black font-mono text-xs uppercase tracking-wider"
                  :class="user.status === 'active' ? 'bg-construct-black text-white' : 'bg-gray-200 text-gray-500'"
                >
                  {{ user.status === 'active' ? t('auth.status_active') : t('auth.status_inactive') }}
                </span>
              </div>
            </div>

            <!-- 登出按钮 -->
            <button
              @click="handleLogout"
              :disabled="logoutForm.processing"
              class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 border-2 border-construct-red text-construct-red font-mono text-xs uppercase tracking-wider hover:bg-construct-red hover:text-white transition-colors disabled:opacity-50"
            >
              <LogOut class="w-4 h-4" />
              {{ t('auth.logout') }}
            </button>
          </div>
        </div>
        <div class="h-2 bg-construct-red" />
      </div>

      <!-- 点赞内容 -->
      <div class="bg-white border-4 border-construct-black mb-6">
        <!-- 标题栏 -->
        <div class="border-b-2 border-construct-black px-8 py-4 flex items-center gap-3">
          <Heart class="w-5 h-5 text-construct-red" />
          <h3 class="font-display text-base uppercase tracking-wider text-construct-black">
            {{ t('auth.liked_items') }}
          </h3>
          <span class="font-mono text-xs text-gray-400">
            ({{ likedItems.total || 0 }})
          </span>
        </div>

        <div class="p-8">
          <!-- 空状态 -->
          <div
            v-if="!likedItems.data || likedItems.data.length === 0"
            class="text-center py-16"
          >
            <Heart class="w-12 h-12 mx-auto mb-4 text-gray-300" />
            <p class="font-mono text-sm text-gray-400">{{ t('auth.no_liked_items') }}</p>
          </div>

          <!-- 点赞列表 -->
          <div v-else class="space-y-3">
            <div
              v-for="item in likedItems.data"
              :key="item.id"
              class="flex items-center gap-4 p-4 border-2 border-gray-200 hover:border-construct-black transition-colors group"
            >
              <!-- 类型徽章 -->
              <span
                class="flex-shrink-0 px-2.5 py-1 font-mono text-xs uppercase tracking-wider border"
                :class="typeBadgeClass(item.interactable_type)"
              >
                {{ likeTypeLabel(item.interactable_type) }}
              </span>

              <!-- 标题链接 -->
              <a
                v-if="item.interactable"
                :href="likeItemUrl(item)"
                class="flex-1 text-sm font-mono text-construct-black group-hover:text-construct-red transition-colors truncate"
              >
                {{ item.interactable.title || item.interactable.name }}
              </a>
              <span v-else class="flex-1 text-sm font-mono text-gray-400 italic">
                {{ t('auth.content_unavailable') }}
              </span>

              <!-- 日期 -->
              <span class="flex-shrink-0 font-mono text-xs text-gray-400">
                {{ formatDate(item.created_at) }}
              </span>
            </div>
          </div>

          <!-- 分页 -->
          <div v-if="likedItems.links && likedItems.links.length > 3" class="flex justify-center mt-8">
            <nav class="flex items-center gap-1">
              <template v-for="(link, i) in likedItems.links" :key="i">
                <span
                  v-if="link.url === null"
                  v-html="link.label"
                  class="px-3 py-1.5 font-mono text-xs text-gray-400"
                />
                <Link
                  v-else
                  :href="link.url"
                  v-html="link.label"
                  class="px-3 py-1.5 font-mono text-xs border-2 transition-colors"
                  :class="link.active
                    ? 'border-construct-black bg-construct-black text-white'
                    : 'border-gray-200 text-gray-500 hover:border-construct-black'"
                  preserve-scroll
                />
              </template>
            </nav>
          </div>
        </div>
      </div>

      <!-- 返回首页 -->
      <div class="text-center mb-6">
        <a
          href="/"
          class="inline-flex items-center gap-1 text-xs tracking-widest uppercase text-gray-500 hover:text-construct-red transition-colors"
        >
          <ArrowLeft class="w-3 h-3" />
          {{ t('nav_home') }}
        </a>
      </div>

      <!-- 装饰性边框 -->
      <div class="w-full h-2 bg-construct-black" />
    </div>
  </div>
</template>
