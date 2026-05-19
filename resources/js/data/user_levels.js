/**
 * user_levels.js - 用户等级数据定义
 *
 * 功能说明：
 * - 定义用户等级系统
 * - 支持等级权限、图标、颜色等配置
 */

export const userLevels = [
  {
    id: 1,
    name: 'VIP',
    level: 5,
    icon: 'crown',
    color: '#ffd700',
    description: '最高等级会员',
    discount: 30,
    benefits: ['全部内容访问', '专属客服', '30%折扣'],
    is_active: true,
    sort_order: 1
  },
  {
    id: 2,
    name: 'PREMIUM',
    level: 4,
    icon: 'star',
    color: '#c0c0c0',
    description: '高级会员',
    discount: 20,
    benefits: ['优质内容访问', '20%折扣'],
    is_active: true,
    sort_order: 2
  },
  {
    id: 3,
    name: 'STANDARD',
    level: 3,
    icon: 'award',
    color: '#cd7f32',
    description: '标准会员',
    discount: 10,
    benefits: ['标准内容访问', '10%折扣'],
    is_active: true,
    sort_order: 3
  },
  {
    id: 4,
    name: 'BASIC',
    level: 2,
    icon: 'user',
    color: '#4682b4',
    description: '基础会员',
    discount: 5,
    benefits: ['基础内容访问', '5%折扣'],
    is_active: true,
    sort_order: 4
  },
  {
    id: 5,
    name: 'GUEST',
    level: 1,
    icon: 'user-x',
    color: '#808080',
    description: '访客用户',
    discount: 0,
    benefits: ['公开内容访问'],
    is_active: true,
    sort_order: 5
  }
];

export const getLevelById = (id) => {
  return userLevels.find(level => level.id === id);
};

export const getLevelByName = (name) => {
  return userLevels.find(level => level.name === name);
};

export const getActiveLevels = () => {
  return userLevels.filter(level => level.is_active).sort((a, b) => a.sort_order - b.sort_order);
};
