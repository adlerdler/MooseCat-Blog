<script setup>
/**
 * Footer.vue - 页脚组件
 * 
 * 功能说明：
 * - 网站全局页脚，显示品牌信息和社交链接
 * - 支持显示/隐藏控制（通过 v-model）
 * - 响应式多列布局
 * - 社交媒体链接从 author.js 动态获取
 * 
 * 内容区域：
 * - 品牌Logo和标语
 * - 快速导航链接
 * - 社交媒体链接
 * - 版权信息
 * 
 * 使用示例：
 * <Footer v-model="isFooterVisible" />
 */
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { socialLinks } from '../data/author';

const { t } = useI18n();

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:modelValue'])

const toggleFooter = () => {
  emit('update:modelValue', !props.modelValue)
}
</script>

<template>
  <footer
    v-if="props.modelValue"
    class="p-8 md:p-16 bg-construct-paper border-t-8 border-black grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-12 sm:gap-8 md:gap-12"
  >
    <div class="sm:col-span-2">
      <RouterLink
        to="/"
        class="font-display text-5xl md:text-6xl tracking-tighter mb-4 block hover:text-accent transition-colors"
      >
        ARCHYX
      </RouterLink>
      <p class="max-w-xs text-sm uppercase font-bold tracking-tight opacity-60 min-h-[3rem]">
        {{ t('footer_tagline') }}
      </p>
      <div class="flex gap-4 mt-8">
        <a
          v-for="link in socialLinks"
          :key="link.id"
          :href="link.url"
          target="_blank"
          rel="noopener noreferrer"
          :class="[
            'p-2 border-2 border-black transition-colors w-10 h-10 flex items-center justify-center',
            link.style.background,
            link.style.textColor,
            link.style.hover
          ]"
          :aria-label="link.label"
        >
          <component :is="link.icon" class="w-4 h-4" />
        </a>
      </div>
    </div>
    <div>
      <h4 class="font-display mb-6 tracking-widest text-sm uppercase bg-black text-white inline-block px-3 py-1">
        {{ t('footer_categories') }}
      </h4>
      <ul class="text-xs space-y-3 font-bold tracking-widest uppercase">
        <li>
          <RouterLink to="/" class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
            / {{ t('footer_theory') }}
          </RouterLink>
        </li>
        <li>
          <RouterLink to="/" class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
            / {{ t('footer_design') }}
          </RouterLink>
        </li>
        <li>
          <RouterLink to="/blog" class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
            / {{ t('footer_blog') }}
          </RouterLink>
        </li>
        <li>
          <RouterLink to="/author" class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
            / {{ t('footer_about') }}
          </RouterLink>
        </li>
      </ul>
    </div>
    <div>
      <h4 class="font-display mb-6 tracking-widest text-sm uppercase bg-black text-white inline-block px-3 py-1">
        {{ t('footer_data') }}
      </h4>
      <ul class="text-xs space-y-3 font-bold tracking-widest uppercase">
        <li class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
          {{ t('footer_rss') }}
        </li>
        <li class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
          {{ t('footer_api') }}
        </li>
        <li class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
          GITHUB
        </li>
      </ul>
    </div>
  </footer>
</template>

<style lang="scss" scoped>
// 字体变量
$font-display: 'Space Grotesk', system-ui, sans-serif;

.font-display {
  font-family: $font-display;
}
</style>
