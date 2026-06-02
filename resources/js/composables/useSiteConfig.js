/**
 * useSiteConfig.js - 站点配置 Composable
 *
 * ⚠️ 字段变更备注（2026-05-24）：
 * - maintenanceMode → maintenance
 * - showAuthorBio → author_bio
 * - showComments → comments
 *
 * 功能说明：
 * - 提供站点配置的查询功能
 * - 供多个页面和组件使用
 * - 优先使用传入的 config，否则自动从 Inertia usePage().props.siteConfig 读取
 *
 * 使用方式：
 * // 自动读取全局共享 siteConfig（推荐）
 * import { useSiteConfig } from '../../composables/useSiteConfig';
 * const { isCommentsVisible } = useSiteConfig();
 *
 * // 手动传入 config
 * const { getSiteName } = useSiteConfig({ config: props.siteConfig });
 */
import { usePage } from '@inertiajs/vue3';

export function useSiteConfig(options = {}) {
  const pageSiteConfig = usePage().props.siteConfig || {};
  const siteConfig = options.config || pageSiteConfig;

  const getSiteConfig = () => ({ ...siteConfig });
  const getSiteName = () => siteConfig.name || '';
  const getSiteUrl = () => siteConfig.siteUrl || '';
  const getSiteDescription = () => siteConfig.description || '';
  const getSiteCopyright = () => siteConfig.copyright || '';
  const isMaintenanceMode = () => siteConfig.maintenance || false;
  const isAuthorBioVisible = () => siteConfig.author_bio !== false;
  const isCommentsVisible = () => siteConfig.comments !== false;
  const isSearchVisible = () => siteConfig.search !== false;

  return {
    getSiteConfig,
    getSiteName,
    getSiteUrl,
    getSiteDescription,
    getSiteCopyright,
    isMaintenanceMode,
    isAuthorBioVisible,
    isCommentsVisible,
    isSearchVisible
  };
}