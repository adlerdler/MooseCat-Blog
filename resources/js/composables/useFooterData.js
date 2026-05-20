/**
 * useFooterData.js - 页脚数据 Composable
 * 
 * 功能说明：
 * - 提供页脚配置的查询功能
 * - 供 Footer.vue 和 SocialLinks.vue 使用
 */
import { footerConfig } from '../data/footer_config';

export function useFooterData() {
  const getFooterSocialLinks = () => {
    return footerConfig.social_links
      .filter(link => link.is_active)
      .sort((a, b) => a.sort_order - b.sort_order);
  };

  const getFooterNavLinks = (section = 'categories') => {
    const links = footerConfig.nav_links[section] || [];
    return links
      .filter(link => link.is_active)
      .sort((a, b) => a.sort_order - b.sort_order);
  };

  const getFooterConfig = () => ({ ...footerConfig });

  return {
    getFooterSocialLinks,
    getFooterNavLinks,
    getFooterConfig,
    footerConfig
  };
}
