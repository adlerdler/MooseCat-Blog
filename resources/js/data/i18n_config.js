/**
 * i18n_config.js - 多语言配置数据
 * 
 * 功能说明：
 * - 存储系统支持的语言列表（数据库格式）
 * - 引用真实的翻译文件路径
 * - 支持后台管理页面编辑
 * 
 * 表结构说明：
 * - languages: 支持的语言列表
 *   - id: 语言唯一标识
 *   - code: 语言代码（en/zh/zh-TW）
 *   - name: 语言英文名称
 *   - native_name: 语言本地化名称
 *   - flag: 旗帜图标
 *   - file_path: 翻译文件路径
 *   - is_default: 是否为默认语言
 *   - is_active: 是否启用
 *   - sort_order: 排序顺序
 *   - created_at: 创建时间
 *   - updated_at: 更新时间
 */

export const languages = [
  {
    id: 1,
    code: 'en',
    name: 'English',
    native_name: 'English',
    flag: '🇺🇸',
    file_path: '/locales/en.json',
    is_default: true,
    is_active: true,
    sort_order: 1,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 2,
    code: 'zh',
    name: 'Chinese (Simplified)',
    native_name: '简体中文',
    flag: '🇨🇳',
    file_path: '/locales/zh.json',
    is_default: false,
    is_active: true,
    sort_order: 2,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 3,
    code: 'zh-TW',
    name: 'Chinese (Traditional)',
    native_name: '繁體中文',
    flag: '🇹🇼',
    file_path: '/locales/zh-TW.json',
    is_default: false,
    is_active: true,
    sort_order: 3,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  }
];
