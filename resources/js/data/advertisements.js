/**
 * advertisements.js - 广告数据
 * 
 * 功能说明：
 * - 存储广告位配置和广告内容数据
 * - 包含广告位置、广告素材、链接、统计信息等
 * 
 * 广告位：
 * - header: 顶部广告
 * - sidebar: 侧边栏广告
 * - footer: 底部广告
 * - between_posts: 文章间广告
 * - popup: 弹窗广告
 */

export const adPositions = [
  { value: 'header', labelKey: 'admin_ad_position_header' },
  { value: 'sidebar', labelKey: 'admin_ad_position_sidebar' },
  { value: 'footer', labelKey: 'admin_ad_position_footer' },
  { value: 'between_posts', labelKey: 'admin_ad_position_between_posts' },
  { value: 'popup', labelKey: 'admin_ad_position_popup' }
];

export const sampleAdvertisements = [
  {
    id: 1,
    title: 'Minimalist Hosting',
    image_url: 'https://via.placeholder.com/300x250',
    link_url: 'https://example.com/hosting',
    position: 'sidebar',
    is_active: true,
    clicks_count: 1250,
    views_count: 15680,
    start_date: '2026-05-01',
    end_date: '2026-12-31'
  },
  {
    id: 2,
    title: 'Design Tools Pro',
    image_url: 'https://via.placeholder.com/728x90',
    link_url: 'https://example.com/design-tools',
    position: 'header',
    is_active: true,
    clicks_count: 890,
    views_count: 12340,
    start_date: '2026-05-10',
    end_date: '2026-11-30'
  },
  {
    id: 3,
    title: 'Creative Portfolio',
    image_url: 'https://via.placeholder.com/300x250',
    link_url: 'https://example.com/portfolio',
    position: 'between_posts',
    is_active: false,
    clicks_count: 456,
    views_count: 8900,
    start_date: '2026-04-01',
    end_date: '2026-10-15'
  },
  {
    id: 4,
    title: 'Digital Magazine',
    image_url: 'https://via.placeholder.com/728x90',
    link_url: 'https://example.com/magazine',
    position: 'footer',
    is_active: true,
    clicks_count: 2100,
    views_count: 24500,
    start_date: '2026-03-01',
    end_date: '2026-09-01'
  },
  {
    id: 5,
    title: 'Architecture Weekly',
    image_url: 'https://via.placeholder.com/300x600',
    link_url: 'https://example.com/arch-weekly',
    position: 'sidebar',
    is_active: true,
    clicks_count: 670,
    views_count: 9800,
    start_date: '2026-05-15',
    end_date: '2026-08-15'
  },
  {
    id: 6,
    title: 'Tech Conference 2026',
    image_url: 'https://via.placeholder.com/320x480',
    link_url: 'https://example.com/conference',
    position: 'popup',
    is_active: false,
    clicks_count: 320,
    views_count: 5600,
    start_date: '2026-06-01',
    end_date: '2026-06-30'
  }
];
