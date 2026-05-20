/**
 * themes.js - 主题定制系统数据（数据库格式）
 * 
 * 功能说明：
 * - 模拟数据库主题表结构
 * - 存储应用的主题配置信息
 * - 包含 Accent Color 主题列表
 * - 支持后台管理页面编辑
 * 
 * 字段说明：
 * - id: 主题唯一标识
 * - name: 主题名称（英文标识符）
 * - label: 主题显示名称（中文）
 * - color: 主题颜色值（HEX格式）
 * - sort_order: 排序顺序
 * - is_active: 是否启用
 * - is_default: 是否为默认主题
 */

export const themes = [
  { id: 1, name: 'construct-red', label: '建筑红', color: '#CF202E', sort_order: 1, is_active: true, is_default: false },
  { id: 2, name: 'ocean-blue', label: '海洋蓝', color: '#0066FF', sort_order: 2, is_active: true, is_default: true },
  { id: 3, name: 'forest-green', label: '森林绿', color: '#228B22', sort_order: 3, is_active: true, is_default: false },
  { id: 4, name: 'sunset-orange', label: '日落橙', color: '#FF8C00', sort_order: 4, is_active: true, is_default: false },
  { id: 5, name: 'purple-haze', label: '紫雾', color: '#8B5CF6', sort_order: 5, is_active: true, is_default: false },
  { id: 6, name: 'pink-05', label: '粉色', color: '#FF007A', sort_order: 6, is_active: true, is_default: false },
];

/**
 * 获取所有主题列表
 * @returns {Array} 主题列表
 */
export function getThemes() {
  return [...themes];
}

/**
 * 获取启用的主题列表
 * @returns {Array} 启用的主题列表
 */
export function getActiveThemes() {
  return themes.filter(t => t.is_active);
}

/**
 * 根据ID获取主题
 * @param {number} id - 主题ID
 * @returns {Object|undefined} 主题对象
 */
export function getThemeById(id) {
  return themes.find(t => t.id === id);
}

/**
 * 根据名称获取主题
 * @param {string} name - 主题名称
 * @returns {Object|undefined} 主题对象
 */
export function getThemeByName(name) {
  return themes.find(t => t.name === name);
}

/**
 * 获取默认主题
 * @returns {Object} 默认主题对象
 */
export function getDefaultTheme() {
  return themes.find(t => t.is_default) || themes[0];
}
