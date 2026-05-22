/**
 * notifications.js - 站内通知数据
 *
 * 功能说明：
 * - 存储所有站内通知的静态数据
 * - 支持多种通知类型（info、warning、error、success）
 * - 支持已读/未读状态管理
 *
 * 数据字段：
 * - id: 通知唯一标识
 * - title: 通知标题
 * - message: 通知内容
 * - type: 通知类型（info/warning/error/success）
 * - read: 是否已读
 * - created_at: 创建时间
 * - link: 关联链接（可选）
 */
export const NOTIFICATIONS = [
  {
    id: 1,
    title: '系统更新通知',
    message: '系统已更新至最新版本，新增多语言管理功能和通知系统。',
    type: 'info',
    read: false,
    created_at: '2026-05-22T10:00:00',
    link: '/admin/settings'
  },
  {
    id: 2,
    title: '安全提醒',
    message: '检测到异常登录尝试，请及时修改密码以确保账户安全。',
    type: 'warning',
    read: false,
    created_at: '2026-05-21T15:30:00',
    link: '/admin/users'
  },
  {
    id: 3,
    title: '备份完成',
    message: '数据库自动备份已完成，备份文件大小：2.3MB。',
    type: 'success',
    read: true,
    created_at: '2026-05-20T03:00:00',
    link: '/admin/backup'
  },
  {
    id: 4,
    title: '存储空间不足',
    message: '服务器存储空间使用率已超过 85%，请及时清理或扩容。',
    type: 'error',
    read: false,
    created_at: '2026-05-19T09:15:00',
    link: '/admin/settings'
  },
  {
    id: 5,
    title: '新用户注册',
    message: '有新用户注册了系统，请及时审核。',
    type: 'info',
    read: true,
    created_at: '2026-05-18T14:20:00',
    link: '/admin/users'
  }
];
