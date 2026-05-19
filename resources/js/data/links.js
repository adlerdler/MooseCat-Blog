/**
 * links.js - 社交链接数据
 * 
 * 功能说明：
 * - 存储社交链接配置
 * - 用于作者页面、页脚、后台社交链接管理等
 * - 通过 user_id 关联 users.js 中的用户记录
 * 
 * 表结构：
 * - links: 社交链接表
 * 
 * 字段说明：
 * - id: 链接唯一标识
 * - user_id: 关联用户ID（对应 users.js 中的用户）
 * - platform: 平台标识
 * - icon_name: 图标名称（lucide-vue-next）
 * - label: 显示标签
 * - url: 链接地址
 * - sort_order: 排序顺序
 * - is_active: 是否启用
 */

export const links = [
  {
    id: 1,
    user_id: 9,
    platform: 'github',
    icon_name: 'Github',
    label: 'GITHUB',
    url: 'https://github.com/adlerdler',
    sort_order: 1,
    is_active: true,
  },
  {
    id: 2,
    user_id: 9,
    platform: 'twitter',
    icon_name: 'Twitter',
    label: 'TWITTER',
    url: 'https://twitter.com/adlerdler',
    sort_order: 2,
    is_active: true,
  },
  {
    id: 3,
    user_id: 9,
    platform: 'linkedin',
    icon_name: 'Linkedin',
    label: 'LINKEDIN',
    url: 'https://linkedin.com/in/adlerdler',
    sort_order: 3,
    is_active: true,
  },
  {
    id: 4,
    user_id: 9,
    platform: 'email',
    icon_name: 'Mail',
    label: 'CONTACT',
    url: 'mailto:contact@adlerdler.com',
    sort_order: 4,
    is_active: true,
  },
];

/**
 * 根据用户ID获取社交链接列表
 */
export const getLinksByUserId = (userId) => {
  return links
    .filter(l => l.user_id === userId && l.is_active)
    .sort((a, b) => a.sort_order - b.sort_order);
};

/**
 * 获取所有社交链接列表
 */
export const getAllLinks = () => {
  return links.filter(l => l.is_active).sort((a, b) => a.sort_order - b.sort_order);
};
