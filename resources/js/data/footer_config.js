/**
 * footer_config.js - 页脚配置数据文件
 * 
 * 功能说明：
 * - 管理前台页脚的所有可配置内容
 * - 支持后台管理页面编辑
 * - 前台 Footer 组件直接引用此文件
 * 
 * 数据结构：
 * - 品牌信息（Logo、标语）
 * - 导航链接（分类、数据链接）
 * - 社交媒体链接
 * - 版权信息
 */

export const footerConfig = {
  brand: {
    logo_text: 'ARCHYX',
    tagline_key: 'footer_tagline',
    tagline_default: 'Constructivist Architecture & Design'
  },

  social_links: [
    {
      id: 1,
      platform: 'github',
      icon_name: 'Github',
      label: 'GITHUB',
      url: 'https://github.com/adler-decht',
      sort_order: 1,
      is_active: true,
      style: {
        bg: 'bg-white',
        text: 'text-construct-black',
        hover_bg: 'hover:bg-construct-black',
        hover_text: 'hover:text-white',
        border: 'border-black',
        hover_border: 'hover:border-construct-black'
      }
    },
    {
      id: 2,
      platform: 'twitter',
      icon_name: 'Twitter',
      label: 'TWITTER',
      url: 'https://twitter.com/adler_decht',
      sort_order: 2,
      is_active: true,
      style: {
        bg: 'bg-white',
        text: 'text-construct-black',
        hover_bg: 'hover:bg-construct-black',
        hover_text: 'hover:text-white',
        border: 'border-black',
        hover_border: 'hover:border-construct-black'
      }
    },
    {
      id: 3,
      platform: 'linkedin',
      icon_name: 'Linkedin',
      label: 'LINKEDIN',
      url: 'https://linkedin.com/in/adler-decht',
      sort_order: 3,
      is_active: true,
      style: {
        bg: 'bg-construct-red',
        text: 'text-white',
        hover_bg: 'hover:bg-construct-black',
        hover_text: 'hover:text-white',
        border: 'border-construct-red',
        hover_border: 'hover:border-construct-black'
      }
    },
    {
      id: 4,
      platform: 'website',
      icon_name: 'Globe',
      label: 'WEBSITE',
      url: 'https://adler-decht.com',
      sort_order: 4,
      is_active: true,
      style: {
        bg: 'bg-construct-black',
        text: 'text-white',
        hover_bg: 'hover:bg-construct-red',
        hover_text: 'hover:text-white',
        border: 'border-black',
        hover_border: 'hover:border-construct-red'
      }
    }
  ],

  nav_links: {
    categories: [
      {
        id: 1,
        label_key: 'footer_theory',
        label_default: 'Theory',
        route: '/',
        sort_order: 1,
        is_active: true
      },
      {
        id: 2,
        label_key: 'footer_design',
        label_default: 'Design',
        route: '/',
        sort_order: 2,
        is_active: true
      },
      {
        id: 3,
        label_key: 'footer_blog',
        label_default: 'Blog',
        route: '/blog',
        sort_order: 3,
        is_active: true
      },
      {
        id: 4,
        label_key: 'footer_about',
        label_default: 'About',
        route: '/author',
        sort_order: 4,
        is_active: true
      }
    ],
    data: [
      {
        id: 1,
        label_key: 'footer_rss',
        label_default: 'RSS Feed',
        url: '/feed.xml',
        sort_order: 1,
        is_active: true
      },
      {
        id: 2,
        label_key: 'footer_api',
        label_default: 'API',
        url: '/api',
        sort_order: 2,
        is_active: true
      },
      {
        id: 3,
        label_key: 'GITHUB',
        label_default: 'GITHUB',
        url: 'https://github.com/adler-decht',
        sort_order: 3,
        is_active: true
      }
    ]
  },

  section_titles: {
    categories_key: 'footer_categories',
    categories_default: 'Categories',
    data_key: 'footer_data',
    data_default: 'Data'
  }
};

/**
 * 获取社交链接列表
 */
export function getFooterSocialLinks() {
  return footerConfig.social_links
    .filter(link => link.is_active)
    .sort((a, b) => a.sort_order - b.sort_order);
}

/**
 * 获取导航链接
 */
export function getFooterNavLinks(section = 'categories') {
  const links = footerConfig.nav_links[section] || [];
  return links
    .filter(link => link.is_active)
    .sort((a, b) => a.sort_order - b.sort_order);
}

/**
 * 获取品牌信息
 */
export function getFooterBrand() {
  return footerConfig.brand;
}
