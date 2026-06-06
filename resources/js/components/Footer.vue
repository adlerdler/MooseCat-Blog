<script setup>
/**
 * Footer.vue - 页脚组件
 * 
 * 功能说明：
 * - 网站全局页脚，显示品牌信息和社交链接
 * - 支持显示/隐藏控制（通过 v-model）
 * - 响应式多列布局
 * - 数据从 footer_config.js 动态获取
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
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Github, Twitter, Linkedin, Globe, MessageCircle, Palette, Youtube, Facebook, Video } from 'lucide-vue-next';
import { useFooterData } from '../composables/useFooterData';
import { useSiteConfig } from '../composables/useSiteConfig';
import AdSlot from './front/AdSlot.vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: true
  },
  footerConfig: {
    type: Object,
    default: () => ({})
  },
  siteConfig: {
    type: Object,
    default: () => ({})
  }
});

const { getFooterSocialLinks, getFooterNavLinks } = useFooterData({ footerConfig: props.footerConfig });
const { t } = useI18n();

const socialLinks = getFooterSocialLinks();
const categoryLinks = getFooterNavLinks('categories');
const dataLinks = getFooterNavLinks('data');
const { getSiteName, getSiteDescription } = useSiteConfig({ config: props.siteConfig });
const siteName = computed(() => getSiteName());
const siteDescription = computed(() => getSiteDescription());

const emit = defineEmits(['update:modelValue'])

const toggleFooter = () => {
  emit('update:modelValue', !props.modelValue)
}

const getIconComponent = (iconName) => {
  if (!iconName) return Globe;
  
  const iconMap = {
    'github': Github,
    'twitter': Twitter,
    'linkedin': Linkedin,
    'globe': Globe,
    'website': Globe,
    'messagecircle': MessageCircle,
    'palette': Palette,
    'youtube': Youtube,
    'facebook': Facebook,
    'video': Video,
    'bilibili': Video,
  };
  return iconMap[iconName.toLowerCase()] || Globe;
}

const getLinkStyle = (index) => {
  const styles = [
    'bg-white text-construct-black hover:bg-construct-black hover:text-white border-2 border-black hover:border-construct-black',
    'bg-white text-construct-black hover:bg-construct-black hover:text-white border-2 border-black hover:border-construct-black',
    'bg-construct-red text-white hover:bg-construct-black hover:text-white border-2 border-construct-red hover:border-construct-black',
    'bg-construct-black text-white hover:bg-construct-red hover:text-white border-2 border-black hover:border-construct-red'
  ];
  return styles[index] || styles[0];
}
</script>

<template>
  <div>
    <AdSlot position="footer" />
    <footer
      v-if="props.modelValue"
    class="p-6 md:p-10 bg-construct-paper border-t-4 border-black grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 sm:gap-6 md:gap-8"
  >
    <div class="sm:col-span-2">
      <Link
        href="/"
        class="font-display text-4xl md:text-5xl tracking-tighter mb-3 block hover:text-accent transition-colors"
      >
        {{ siteName }}
      </Link>
      <p class="max-w-xs text-sm uppercase font-bold tracking-tight opacity-60 min-h-[2rem]">
        {{ siteDescription }}
      </p>
      <div class="flex gap-4 mt-4">
        <a
          v-for="(link, index) in socialLinks"
          :key="link.id"
          :href="link.url"
          target="_blank"
          rel="noopener noreferrer"
          :class="[
            'p-2 transition-colors w-10 h-10 flex items-center justify-center',
            getLinkStyle(index)
          ]"
          :aria-label="link.label"
        >
          <component :is="getIconComponent(link.icon_name)" class="w-4 h-4" />
        </a>
      </div>
    </div>
    <div v-if="categoryLinks.length > 0">
      <h4 class="font-display mb-3 tracking-widest text-sm uppercase bg-black text-white inline-block px-3 py-1">
        {{ t('footer_categories') }}
      </h4>
      <ul class="text-xs space-y-2 font-bold tracking-widest uppercase">
        <li v-for="link in categoryLinks" :key="link.id">
          <Link :href="link.url" class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
            / {{ link.label }}
          </Link>
        </li>
      </ul>
    </div>
    <div v-if="dataLinks.length > 0">
      <h4 class="font-display mb-3 tracking-widest text-sm uppercase bg-black text-white inline-block px-3 py-1">
        {{ t('footer_data') }}
      </h4>
      <ul class="text-xs space-y-2 font-bold tracking-widest uppercase">
        <li v-for="link in dataLinks" :key="link.id">
          <a v-if="link.url" :href="link.url" target="_blank" rel="noopener noreferrer" class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
            {{ link.label }}
          </a>
          <Link v-else :href="link.route" class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
            / {{ link.label }}
          </Link>
        </li>
      </ul>
    </div>
  </footer>
  </div>
</template>

<style lang="scss" scoped>
$font-display: 'Space Grotesk', system-ui, sans-serif;

.font-display {
  font-family: $font-display;
}
</style>
