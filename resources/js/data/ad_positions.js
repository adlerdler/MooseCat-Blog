/**
 * ad_positions.js - 广告位配置数据
 *
 * 功能说明：
 * - 存储广告位的元信息配置
 * - 与 advertisements.js 中的 position_id 字段关联
 * - 支持广告位的启用/禁用、排序等管理
 *
 * 数据字段：
 * - id: 广告位唯一标识
 * - name: 广告位名称（英文标识，用于代码引用）
 * - label_key: 国际化翻译key（用于前端显示）
 * - description: 广告位描述
 * - default_width: 默认宽度（像素）
 * - default_height: 默认高度（像素）
 * - is_active: 是否启用
 * - sort_order: 排序顺序
 * - created_at: 创建时间
 * - updated_at: 更新时间
 *
 * ⚠️ 函数已迁移到 useAdSlot.js composable
 */

export const adPositions = [
  {
    id: 1,
    name: 'header',
    label_key: 'admin_ad_position_header',
    description: '页面顶部横幅广告位，显示在页面头部导航下方',
    default_width: 728,
    default_height: 90,
    is_active: true,
    sort_order: 1,
    created_at: '2026-05-01T10:00:00',
    updated_at: '2026-05-01T10:00:00',
  },
  {
    id: 2,
    name: 'sidebar',
    label_key: 'admin_ad_position_sidebar',
    description: '侧边栏广告位，显示在页面右侧边栏区域',
    default_width: 300,
    default_height: 250,
    is_active: true,
    sort_order: 2,
    created_at: '2026-05-01T10:00:00',
    updated_at: '2026-05-01T10:00:00',
  },
  {
    id: 3,
    name: 'footer',
    label_key: 'admin_ad_position_footer',
    description: '页面底部横幅广告位，显示在页脚上方',
    default_width: 728,
    default_height: 90,
    is_active: true,
    sort_order: 3,
    created_at: '2026-05-01T10:00:00',
    updated_at: '2026-05-01T10:00:00',
  },
  {
    id: 4,
    name: 'between_posts',
    label_key: 'admin_ad_position_between_posts',
    description: '文章列表中间插入广告位，每4篇文章插入一条',
    default_width: 800,
    default_height: 450,
    is_active: true,
    sort_order: 4,
    created_at: '2026-05-01T10:00:00',
    updated_at: '2026-05-01T10:00:00',
  },
  {
    id: 5,
    name: 'popup',
    label_key: 'admin_ad_position_popup',
    description: '弹窗广告位，用户首次访问时显示',
    default_width: 320,
    default_height: 480,
    is_active: true,
    sort_order: 5,
    created_at: '2026-05-01T10:00:00',
    updated_at: '2026-05-01T10:00:00',
  },
  {
    id: 6,
    name: 'in_content',
    label_key: 'admin_ad_position_in_content',
    description: '文章内容中间广告位，嵌入在正文段落之间',
    default_width: 800,
    default_height: 250,
    is_active: true,
    sort_order: 6,
    created_at: '2026-05-01T10:00:00',
    updated_at: '2026-05-01T10:00:00',
  },
  {
    id: 7,
    name: 'video_bottom',
    label_key: 'admin_ad_position_video_bottom',
    description: '视频下方广告位，显示在视频播放区域下方',
    default_width: 800,
    default_height: 250,
    is_active: true,
    sort_order: 7,
    created_at: '2026-05-01T10:00:00',
    updated_at: '2026-05-01T10:00:00',
  },
];

export const getAdPositionOptions = () => {
  return adPositions.map(pos => ({
    value: pos.id,
    label_key: pos.label_key,
    name: pos.name,
  }));
};

export const getAdPositionById = (id) => {
  const numericId = typeof id === 'string' ? parseInt(id, 10) : id;
  return adPositions.find(pos => pos.id === numericId) || null;
};
