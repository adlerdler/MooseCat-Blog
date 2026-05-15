/**
 * categoryUtils.js - 分类相关工具函数
 * 
 * 功能说明：
 * - 提供分类数据的查询和格式化功能
 * - 与 data/categories.js 配合使用
 * 
 * 使用方式：
 * import { categories } from '../data/categories';
 * import { getCategoryLabel } from '../utils/categoryUtils';
 */

/**
 * 根据名称获取分类
 * @param {Array} categories - 分类数组
 * @param {string} name - 分类名称
 * @returns {Object|null} 分类对象
 */
export function getCategoryByName(categories, name) {
  return categories.find(c => c.name === name) || null;
}

/**
 * 根据 ID 获取分类
 * @param {Array} categories - 分类数组
 * @param {string} id - 分类 ID
 * @returns {Object|null} 分类对象
 */
export function getCategoryById(categories, id) {
  return categories.find(c => c.id === id) || null;
}

/**
 * 获取分类标签（中文）
 * @param {Array} categories - 分类数组
 * @param {string} name - 分类名称
 * @returns {string} 分类标签
 */
export function getCategoryLabel(categories, name) {
  const category = getCategoryByName(categories, name);
  return category ? category.label : name;
}

/**
 * 获取分类描述
 * @param {Array} categories - 分类数组
 * @param {string} name - 分类名称
 * @returns {string} 分类描述
 */
export function getCategoryDescription(categories, name) {
  const category = getCategoryByName(categories, name);
  return category ? category.description : '';
}

/**
 * 获取所有分类名称数组
 * @param {Array} categories - 分类数组
 * @returns {string[]} 分类名称数组
 */
export function getCategoryNames(categories) {
  return categories.map(c => c.name);
}

/**
 * 获取所有分类 ID 数组
 * @param {Array} categories - 分类数组
 * @returns {string[]} 分类 ID 数组
 */
export function getCategoryIds(categories) {
  return categories.map(c => c.id);
}