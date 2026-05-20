/**
 * subscribers.js - 订阅者数据定义
 * 
 * 字段说明：
 * - id: 订阅者唯一标识
 * - email: 订阅邮箱
 * - name: 订阅者姓名
 * - is_active: 是否激活
 * - source: 订阅来源（website/newsletter/social）
 * - subscribed_at: 订阅时间
 * - created_at: 创建时间
 * - updated_at: 更新时间
 */

export const subscribers = [
  {
    id: 1,
    email: 'user1@example.com',
    name: 'John Doe',
    is_active: true,
    source: 'website',
    subscribed_at: '2024-01-15 10:30:00',
    created_at: '2024-01-15 10:30:00',
    updated_at: '2024-01-15 10:30:00',
  },
  {
    id: 2,
    email: 'user2@example.com',
    name: 'Jane Smith',
    is_active: true,
    source: 'newsletter',
    subscribed_at: '2024-02-20 14:15:00',
    created_at: '2024-02-20 14:15:00',
    updated_at: '2024-02-20 14:15:00',
  },
  {
    id: 3,
    email: 'user3@example.com',
    name: 'Mike Johnson',
    is_active: false,
    source: 'social',
    subscribed_at: '2024-03-10 09:45:00',
    created_at: '2024-03-10 09:45:00',
    updated_at: '2024-04-01 16:20:00',
  },
  {
    id: 4,
    email: 'user4@example.com',
    name: 'Emily Davis',
    is_active: true,
    source: 'website',
    subscribed_at: '2024-04-05 11:00:00',
    created_at: '2024-04-05 11:00:00',
    updated_at: '2024-04-05 11:00:00',
  },
  {
    id: 5,
    email: 'user5@example.com',
    name: 'Chris Wilson',
    is_active: true,
    source: 'newsletter',
    subscribed_at: '2024-05-12 15:30:00',
    created_at: '2024-05-12 15:30:00',
    updated_at: '2024-05-12 15:30:00',
  },
  {
    id: 6,
    email: 'user6@example.com',
    name: 'Sarah Brown',
    is_active: false,
    source: 'website',
    subscribed_at: '2024-06-18 08:20:00',
    created_at: '2024-06-18 08:20:00',
    updated_at: '2024-07-01 10:00:00',
  },
  {
    id: 7,
    email: 'user7@example.com',
    name: 'David Lee',
    is_active: true,
    source: 'social',
    subscribed_at: '2024-07-25 13:45:00',
    created_at: '2024-07-25 13:45:00',
    updated_at: '2024-07-25 13:45:00',
  },
  {
    id: 8,
    email: 'user8@example.com',
    name: 'Lisa Wang',
    is_active: true,
    source: 'newsletter',
    subscribed_at: '2024-08-30 17:10:00',
    created_at: '2024-08-30 17:10:00',
    updated_at: '2024-08-30 17:10:00',
  },
];

/**
 * 获取所有订阅者
 */
export const getSubscribers = () => {
  return subscribers;
};

/**
 * 根据ID获取订阅者
 */
export const getSubscriberById = (id) => {
  return subscribers.find(s => s.id === id) || null;
};

/**
 * 根据邮箱获取订阅者
 */
export const getSubscriberByEmail = (email) => {
  return subscribers.find(s => s.email === email) || null;
};

/**
 * 获取活跃订阅者
 */
export const getActiveSubscribers = () => {
  return subscribers.filter(s => s.is_active);
};

/**
 * 获取订阅来源选项
 */
export const getSourceOptions = () => {
  return ['website', 'newsletter', 'social'];
};

/**
 * 获取订阅来源标签
 */
export const getSourceLabel = (source) => {
  const labels = {
    website: '网站',
    newsletter: '邮件订阅',
    social: '社交媒体',
  };
  return labels[source] || source;
};
