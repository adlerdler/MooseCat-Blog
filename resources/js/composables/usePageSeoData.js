/**
 * usePageSeoData.js - 页面 SEO 数据 Composable
 *
 * 功能说明：
 * - 提供页面 SEO 配置的查询功能
 * - 供前台页面根据路由名称获取 SEO 配置
 * - 优先使用传入的 pageSeoList，否则自动从全局 Inertia 共享数据读取
 *
 * 使用方式：
 * import { usePageSeoData } from '../../composables/usePageSeoData';
 * const { getSeoByPageKey } = usePageSeoData();
 * const seo = getSeoByPageKey('blog');
 */
import { usePage } from '@inertiajs/vue3';

export function usePageSeoData(options = {}) {
  const pageSeoList = options.pageSeoList && options.pageSeoList.length > 0
    ? options.pageSeoList
    : (usePage().props.pageSeo || []);

  const getSeoByPageKey = (pageKey) => {
    // page_seo 表中的字段是 page_key (snake_case)
    const page = pageSeoList.find(p => p.page_key === pageKey || p.pageKey === pageKey);
    return page || null;
  };

  return { getSeoByPageKey };
}