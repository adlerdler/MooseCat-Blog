/**
 * useSiteConfig.js - 站点配置 Composable
 * 
 * 功能说明：
 * - 提供站点配置的查询功能
 * - 供多个页面和组件使用
 */
import { siteConfig } from '../data/site_config';

export function useSiteConfig() {
  const getSiteConfig = () => ({ ...siteConfig });
  const getSiteName = () => siteConfig.name;
  const getSiteUrl = () => siteConfig.siteUrl;
  const getSiteDescription = () => siteConfig.description;
  const getSiteCopyright = () => siteConfig.copyright;
  const isMaintenanceMode = () => siteConfig.maintenanceMode;
  const isAuthorBioVisible = () => siteConfig.showAuthorBio;
  const isCommentsVisible = () => siteConfig.showComments;

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
