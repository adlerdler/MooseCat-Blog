/**
 * useOptimizationConfig.js - 优化配置 Composable
 *
 * 功能说明：
 * - 提供前端优化配置的查询功能
 * - 懒加载开关：lazy_load
 * - 代码压缩开关：minification
 * - CDN 配置：cdn, cdn_url
 *
 * 使用方式：
 * import { useOptimizationConfig } from '@/composables/useOptimizationConfig';
 * const { lazyLoadEnabled, minificationEnabled, cdnEnabled, cdnUrl } = useOptimizationConfig();
 */
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useOptimizationConfig(options = {}) {
  const pageSiteConfig = usePage().props.siteConfig || {};
  const siteConfig = options.config || pageSiteConfig;

  // 懒加载开关
  const lazyLoadEnabled = computed(() => siteConfig.lazy_load ?? true);

  // 代码压缩开关
  const minificationEnabled = computed(() => siteConfig.minification ?? true);

  // CDN 开关
  const cdnEnabled = computed(() => siteConfig.cdn ?? false);

  // CDN URL
  const cdnUrl = computed(() => siteConfig.cdn_url || '');

  // 获取完整优化配置
  const getOptimizationConfig = () => ({
    lazyLoadEnabled: lazyLoadEnabled.value,
    minificationEnabled: minificationEnabled.value,
    cdnEnabled: cdnEnabled.value,
    cdnUrl: cdnUrl.value,
  });

  return {
    lazyLoadEnabled,
    minificationEnabled,
    cdnEnabled,
    cdnUrl,
    getOptimizationConfig,
  };
}