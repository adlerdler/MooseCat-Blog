/**
 * advertisements.js - 广告数据（以数据库为准）
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
 * 
 * 数据字段（以数据库为准）：
 * - id: 广告唯一标识
 * - title: 广告标题
 * - image_url: 广告图片URL
 * - link_url: 跳转链接
 * - position: 广告位置
 * - is_active: 状态（true/false）
 * - clicks_count: 点击次数
 * - views_count: 展示次数
 * - start_date: 开始日期
 * - end_date: 结束日期
 * - created_at: 创建时间
 * - updated_at: 更新时间
 */

export const adPositions = [
  { value: 'header', label_key: 'admin_ad_position_header' },
  { value: 'sidebar', label_key: 'admin_ad_position_sidebar' },
  { value: 'footer', label_key: 'admin_ad_position_footer' },
  { value: 'between_posts', label_key: 'admin_ad_position_between_posts' },
  { value: 'popup', label_key: 'admin_ad_position_popup' }
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
    end_date: '2026-12-31',
    created_at: '2026-04-01T10:00:00',
    updated_at: '2026-04-01T10:00:00',
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
    end_date: '2026-11-30',
    created_at: '2026-04-10T08:30:00',
    updated_at: '2026-04-10T08:30:00',
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
    end_date: '2026-10-15',
    created_at: '2026-03-01T14:20:00',
    updated_at: '2026-03-01T14:20:00',
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
    end_date: '2026-09-01',
    created_at: '2026-02-01T09:15:00',
    updated_at: '2026-02-01T09:15:00',
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
    end_date: '2026-08-15',
    created_at: '2026-04-15T16:45:00',
    updated_at: '2026-04-15T16:45:00',
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
    end_date: '2026-06-30',
    created_at: '2026-05-01T11:30:00',
    updated_at: '2026-05-01T11:30:00',
  }
];
