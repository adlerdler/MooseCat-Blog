/**
 * ad_positions.js - 广告位数据表
 * 
 * 功能说明：
 * - 存储广告位配置信息
 * - 与 advertisements.js 通过 position 字段关联
 * 
 * 数据库字段说明：
 * - id: 广告位唯一标识
 * - name: 广告位名称（英文标识）
 * - label_key: 国际化翻译key
 * - description: 广告位描述
 * - default_width: 默认宽度（像素）
 * - default_height: 默认高度（像素）
 * - is_active: 是否启用
 * - sort_order: 排序顺序
 * - created_at: 创建时间
 * - updated_at: 更新时间
 */

export const adPositions = [
  {
    id: 1,
    name: 'header',
    label_key: 'admin_ad_position_header',
    description: '页面顶部横幅广告',
    default_width: 728,
    default_height: 90,
    is_active: true,
    sort_order: 1,
    created_at: '2026-01-01 00:00:00',
    updated_at: '2026-01-01 00:00:00'
  },
  {
    id: 2,
    name: 'sidebar',
    label_key: 'admin_ad_position_sidebar',
    description: '侧边栏广告',
    default_width: 300,
    default_height: 250,
    is_active: true,
    sort_order: 2,
    created_at: '2026-01-01 00:00:00',
    updated_at: '2026-01-01 00:00:00'
  },
  {
    id: 3,
    name: 'footer',
    label_key: 'admin_ad_position_footer',
    description: '页面底部广告',
    default_width: 728,
    default_height: 90,
    is_active: true,
    sort_order: 3,
    created_at: '2026-01-01 00:00:00',
    updated_at: '2026-01-01 00:00:00'
  },
  {
    id: 4,
    name: 'between_posts',
    label_key: 'admin_ad_position_between_posts',
    description: '文章列表之间的广告',
    default_width: 300,
    default_height: 250,
    is_active: true,
    sort_order: 4,
    created_at: '2026-01-01 00:00:00',
    updated_at: '2026-01-01 00:00:00'
  },
  {
    id: 5,
    name: 'popup',
    label_key: 'admin_ad_position_popup',
    description: '弹窗广告',
    default_width: 320,
    default_height: 480,
    is_active: true,
    sort_order: 5,
    created_at: '2026-01-01 00:00:00',
    updated_at: '2026-01-01 00:00:00'
  }
];

/**
 * 获取所有广告位
 * @returns {Array} 广告位数组
 */
export const getAllAdPositions = () => {
  return adPositions.filter(p => p.is_active).sort((a, b) => a.sort_order - b.sort_order);
};

/**
 * 根据名称获取广告位
 * @param {string} name - 广告位名称
 * @returns {Object|null} 广告位对象
 */
export const getAdPositionByName = (name) => {
  return adPositions.find(p => p.name === name) || null;
};

/**
 * 根据ID获取广告位
 * @param {number} id - 广告位ID
 * @returns {Object|null} 广告位对象
 */
export const getAdPositionById = (id) => {
  return adPositions.find(p => p.id === id) || null;
};

/**
 * 获取广告位的默认尺寸
 * @param {string} name - 广告位名称
 * @returns {Object} 尺寸对象 { width, height }
 */
export const getAdPositionSize = (name) => {
  const position = getAdPositionByName(name);
  if (!position) return { width: 300, height: 250 };
  return { width: position.default_width, height: position.default_height };
};
