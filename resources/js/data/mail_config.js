/**
 * mail_config.js - 邮件配置数据（数据库格式模拟）
 * 
 * 功能说明：
 * - 模拟数据库邮件配置表结构
 * - 存储 SMTP 邮件系统配置
 * - 支持多邮件配置（主配置、备用配置等）
 * 
 * 字段说明：
 * - id: 配置唯一标识
 * - name: 配置名称
 * - driver: 邮件驱动（smtp/mailgun/sendmail等）
 * - host: SMTP 服务器主机
 * - port: SMTP 服务器端口
 * - username: SMTP 认证用户名
 * - password: SMTP 认证密码
 * - encryption: 加密方式（tls/ssl/none）
 * - from_address: 发件人邮箱地址
 * - from_name: 发件人显示名称
 * - is_default: 是否为默认配置
 * - is_active: 是否启用
 * - created_at: 创建时间
 * - updated_at: 更新时间
 */

export const mailConfig = [
  {
    id: 1,
    name: '主邮件配置',
    driver: 'smtp',
    host: 'smtp.archyx.com',
    port: 587,
    username: 'admin@archyx.com',
    password: '****************',
    encryption: 'tls',
    from_address: 'no-reply@archyx.com',
    from_name: 'Archyx System',
    is_default: true,
    is_active: true,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 2,
    name: '备用邮件配置',
    driver: 'smtp',
    host: 'smtp.backup.com',
    port: 465,
    username: 'noreply@backup.com',
    password: '****************',
    encryption: 'ssl',
    from_address: 'noreply@backup.com',
    from_name: 'Archyx Backup',
    is_default: false,
    is_active: false,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  }
];

/**
 * 获取默认邮件配置
 */
export const getDefaultMailConfig = () => {
  return mailConfig.find(config => config.is_default) || mailConfig[0];
};

/**
 * 获取所有启用的邮件配置
 */
export const getActiveMailConfigs = () => {
  return mailConfig.filter(config => config.is_active);
};
