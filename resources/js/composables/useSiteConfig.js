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
 *
 * 使用方式：
 * // 通过 options 传入数据（必须）
 * import { useSiteConfig } from '../../composables/useSiteConfig';
 * const { getSiteName } = useSiteConfig({ config: props.siteConfig });
 */
export function useSiteConfig(options = {}) {
  const siteConfig = options.config || {};

  const getSiteConfig = () => ({ ...siteConfig });
  const getSiteName = () => siteConfig.name || '';
  const getSiteUrl = () => siteConfig.siteUrl || '';
  const getSiteDescription = () => siteConfig.description || '';
  const getSiteCopyright = () => siteConfig.copyright || '';
  const isMaintenanceMode = () => siteConfig.maintenance || false;
  const isAuthorBioVisible = () => siteConfig.author_bio !== false;
  const isCommentsVisible = () => siteConfig.comments !== false;

  return {
    getSiteConfig,
    getSiteName,
    getSiteUrl,
    getSiteDescription,
    getSiteCopyright,
    isMaintenanceMode,
    isAuthorBioVisible,
    isCommentsVisible
  };
}