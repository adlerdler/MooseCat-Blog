/**
 * usePageSeoData.js - 页面 SEO 数据 Composable
 * 
 * 功能说明：
 * - 提供页面 SEO 配置的查询功能
 * - 供前台页面根据路由名称获取 SEO 配置
 */
import { pageSeoList } from '../data/page_seo';

export function usePageSeoData() {
  const getSeoByRoute = (routeName) => {
    const page = pageSeoList.find(p => p.routeName === routeName && p.isActive);
    return page || null;
  };

  const getSeoByPath = (path) => {
    const page = pageSeoList.find(p => p.path === path && p.isActive);
    return page || null;
  };

  return { getSeoByRoute, getSeoByPath };
}
