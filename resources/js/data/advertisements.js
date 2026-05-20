/**
 * advertisements.js - 广告数据（以数据库为准）
 * 
 * 功能说明：
 * - 存储广告内容数据
 * - 通过 position 字段关联 ad_positions.js 中的广告位
 * 
 * 数据字段（以数据库为准）：
 * - id: 广告唯一标识
 * - position_id: 广告位ID（外键，关联 ad_positions 表）
 * - title: 广告标题
 * - image_url: 广告图片URL
 * - link_url: 跳转链接
 * - position: 广告位置（冗余字段，方便查询）
 * - is_active: 状态（true/false）
 * - clicks_count: 点击次数
 * - views_count: 展示次数
 * - start_date: 开始日期
 * - end_date: 结束日期
 * - created_at: 创建时间
 * - updated_at: 更新时间
 */

import { adPositions, getAdPositionByName, getAdPositionById } from './ad_positions';

export const sampleAdvertisements = [
  {
    id: 1,
    position_id: 2,
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
    position_id: 1,
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
    position_id: 4,
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
    position_id: 3,
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
    position_id: 2,
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
    position_id: 5,
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

/**
 * 获取所有广告
 * @returns {Array} 广告数组
 */
export const getAllAdvertisements = () => {
  return sampleAdvertisements;
};

/**
 * 根据广告位名称获取广告列表
 * @param {string} positionName - 广告位名称
 * @returns {Array} 广告数组
 */
export const getAdvertisementsByPosition = (positionName) => {
  return sampleAdvertisements.filter(ad => ad.position === positionName && ad.is_active);
};

/**
 * 根据广告位ID获取广告列表
 * @param {number} positionId - 广告位ID
 * @returns {Array} 广告数组
 */
export const getAdvertisementsByPositionId = (positionId) => {
  return sampleAdvertisements.filter(ad => ad.position_id === positionId && ad.is_active);
};

/**
 * 获取广告及其关联的广告位信息
 * @param {number} adId - 广告ID
 * @returns {Object|null} 广告及广告位信息
 */
export const getAdvertisementWithPosition = (adId) => {
  const ad = sampleAdvertisements.find(a => a.id === adId);
  if (!ad) return null;
  
  const position = getAdPositionById(ad.position_id);
  return {
    ...ad,
    position_info: position
  };
};

/**
 * 获取所有广告及其关联的广告位信息
 * @returns {Array} 广告及广告位信息数组
 */
export const getAllAdvertisementsWithPositions = () => {
  return sampleAdvertisements.map(ad => {
    const position = getAdPositionById(ad.position_id);
    return {
      ...ad,
      position_info: position
    };
  });
};

/**
 * 获取广告位选项（用于表单下拉选择）
 * @returns {Array} 广告位选项数组
 */
export const getAdPositionOptions = () => {
  return adPositions
    .filter(p => p.is_active)
    .map(p => ({
      value: p.name,
      label_key: p.label_key
    }));
};
