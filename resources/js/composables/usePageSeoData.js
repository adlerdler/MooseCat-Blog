/**
 * usePageSeoData.js - 页面 SEO 数据 Composable
 *
 * 功能说明：
 * - 提供页面 SEO 配置的查询功能
 * - 供前台页面根据路由名称获取 SEO 配置
 *
 * 使用方式：
 * // 通过 options 传入数据（必须）
 * import { usePageSeoData } from '../../composables/usePageSeoData';
 * const { getSeoByPageKey } = usePageSeoData({ pageSeoList: props.pageSeo });
 */
export function usePageSeoData(options = {}) {
  const pageSeoList = options.pageSeoList || [];

  const getSeoByPageKey = (pageKey) => {
    const page = pageSeoList.find(p => p.pageKey === pageKey);
    return page || null;
  };

  return { getSeoByPageKey };
}