/**
 * advertisements.js - 广告数据
 *
 * ⚠️ 字段变更备注（2026-05-24）：
 * - position 字段已删除（已废弃，使用 position_id 替代）
 *
 * 功能说明：
 * - 存储广告内容数据
 * - 通过 position_id 关联广告位表
 *
 * 数据字段：
 * - id: 广告唯一标识
 * - position_id: 广告位ID（关联 ad_positions 表）
 * - title: 广告标题
 * - image_url: 广告图片URL
 * - link_url: 跳转链接
 * - is_active: 状态
 * - clicks_count: 点击次数
 * - views_count: 展示次数
 * - start_date: 开始日期
 * - end_date: 结束日期
 */

export const sampleAdvertisements = [
  // ============================================
  // Banner Ads (header, footer, sidebar, popup)
  // ============================================
  {
    id: 1,
    position_id: 2,
    title: 'Minimalist Hosting',
    image_url: 'https://via.placeholder.com/300x250',
    link_url: 'https://example.com/hosting',
    is_active: false,
    clicks_count: 1250,
    views_count: 15680,
    start_date: '2026-05-01',
    end_date: '2026-12-31',
    created_at: '2026-04-01T10:00:00',
    updated_at: '2026-04-01T10:00:00',
  },
  {
    id: 2,
    position_id: 1,
    title: 'Design Tools Pro',
    image_url: 'https://via.placeholder.com/728x90',
    link_url: 'https://example.com/design-tools',
    is_active: false,
    clicks_count: 890,
    views_count: 12340,
    start_date: '2026-05-10',
    end_date: '2026-11-30',
    created_at: '2026-04-10T08:30:00',
    updated_at: '2026-04-10T08:30:00',
  },
  {
    id: 3,
    position_id: 4,
    title: 'Creative Portfolio',
    image_url: 'https://via.placeholder.com/300x250',
    link_url: 'https://example.com/portfolio',
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
    position_id: 3,
    title: 'Digital Magazine',
    image_url: 'https://via.placeholder.com/728x90',
    link_url: 'https://example.com/magazine',
    is_active: false,
    clicks_count: 2100,
    views_count: 24500,
    start_date: '2026-03-01',
    end_date: '2026-09-01',
    created_at: '2026-02-01T09:15:00',
    updated_at: '2026-02-01T09:15:00',
  },
  {
    id: 5,
    position_id: 2,
    title: 'Architecture Weekly',
    image_url: 'https://via.placeholder.com/300x600',
    link_url: 'https://example.com/arch-weekly',
    is_active: false,
    clicks_count: 670,
    views_count: 9800,
    start_date: '2026-05-15',
    end_date: '2026-08-15',
    created_at: '2026-04-15T16:45:00',
    updated_at: '2026-04-15T16:45:00',
  },
  {
    id: 6,
    position_id: 5,
    title: 'Tech Conference 2026',
    image_url: 'https://via.placeholder.com/320x480',
    link_url: 'https://example.com/conference',
    is_active: false,
    clicks_count: 320,
    views_count: 5600,
    start_date: '2026-05-01',
    end_date: '2026-06-30',
    created_at: '2026-05-01T11:30:00',
    updated_at: '2026-05-01T11:30:00',
  },

  // ============================================
  // Card Ads (between_posts - 列表中间插入)
  // ============================================
  {
    id: 7,
    position_id: 4,
    title: 'Premium Code Editor - Write Better Code Faster',
    image_url: 'https://via.placeholder.com/800x450',
    link_url: 'https://example.com/code-editor',
    is_active: false,
    clicks_count: 234,
    views_count: 5600,
    start_date: '2026-05-01',
    end_date: '2026-12-31',
    created_at: '2026-05-01T10:00:00',
    updated_at: '2026-05-01T10:00:00',
  },
  {
    id: 8,
    position_id: 4,
    title: 'Learn UX Design - Free Trial',
    image_url: 'https://via.placeholder.com/800x450',
    link_url: 'https://example.com/ux-course',
    is_active: false,
    clicks_count: 189,
    views_count: 4200,
    start_date: '2026-05-15',
    end_date: '2026-11-30',
    created_at: '2026-05-15T10:00:00',
    updated_at: '2026-05-15T10:00:00',
  },
  {
    id: 9,
    position_id: 4,
    title: 'Video Editing Suite - 50% Off Today',
    image_url: 'https://via.placeholder.com/640x360',
    link_url: 'https://example.com/video-editor',
    is_active: false,
    clicks_count: 567,
    views_count: 8900,
    start_date: '2026-05-01',
    end_date: '2026-10-31',
    created_at: '2026-05-01T10:00:00',
    updated_at: '2026-05-01T10:00:00',
  },
  {
    id: 10,
    position_id: 4,
    title: 'Project Management Tool - Teams Love It',
    image_url: 'https://via.placeholder.com/800x500',
    link_url: 'https://example.com/project-tool',
    is_active: false,
    clicks_count: 432,
    views_count: 7800,
    start_date: '2026-05-01',
    end_date: '2026-09-30',
    created_at: '2026-05-01T10:00:00',
    updated_at: '2026-05-01T10:00:00',
  },
  {
    id: 11,
    position_id: 4,
    title: 'Icon Pack Pro - 5000+ Icons',
    image_url: 'https://via.placeholder.com/640x360',
    link_url: 'https://example.com/icon-pack',
    is_active: false,
    clicks_count: 623,
    views_count: 11200,
    start_date: '2026-05-01',
    end_date: '2026-08-31',
    created_at: '2026-05-01T10:00:00',
    updated_at: '2026-05-01T10:00:00',
  },
  // ============================================
  // In-Content Ads (文章中间)
  // ============================================
  {
    id: 12,
    position_id: 6,
    title: 'AI Writing Assistant - Write Faster, Better',
    image_url: 'https://via.placeholder.com/800x250',
    link_url: 'https://example.com/ai-writer',
    is_active: false,
    clicks_count: 345,
    views_count: 6700,
    start_date: '2026-05-01',
    end_date: '2026-12-31',
    created_at: '2026-05-01T10:00:00',
    updated_at: '2026-05-01T10:00:00',
  },
  {
    id: 13,
    position_id: 6,
    title: 'Code Generator - Auto Write Your Code',
    image_url: 'https://via.placeholder.com/800x250',
    link_url: 'https://example.com/code-gen',
    is_active: false,
    clicks_count: 210,
    views_count: 4500,
    start_date: '2026-05-15',
    end_date: '2026-10-15',
    created_at: '2026-05-15T10:00:00',
    updated_at: '2026-05-15T10:00:00',
  },
  {
    id: 14,
    position_id: 7,
    title: 'Video Editing Pro - Edit Like a Pro',
    image_url: 'https://via.placeholder.com/800x250',
    link_url: 'https://example.com/video-editor',
    is_active: false,
    clicks_count: 180,
    views_count: 3800,
    start_date: '2026-05-01',
    end_date: '2026-12-31',
    created_at: '2026-05-01T10:00:00',
    updated_at: '2026-05-01T10:00:00',
  }
];