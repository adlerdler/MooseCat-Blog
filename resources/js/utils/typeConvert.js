/**
 * typeConvert.js - 通用类型转换工具函数
 * 
 * 功能说明：
 * - 统一处理各种类型转换
 * - 支持数字、字符串、布尔值之间的转换
 * - 提供格式化显示功能
 * - 提供数组查找功能
 */

/**
 * 转换为数字类型
 * @param {string|number|boolean} value - 原始值
 * @param {number} defaultValue - 转换失败时的默认值，默认 0
 * @returns {number} 数字类型的值
 */
export const toNumber = (value, defaultValue = 0) => {
  const num = Number(value);
  return Number.isNaN(num) ? defaultValue : num;
};

/**
 * 转换为字符串类型
 * @param {any} value - 原始值
 * @returns {string} 字符串类型的值
 */
export const toString = (value) => {
  if (value === null || value === undefined) {
    return '';
  }
  return String(value);
};

/**
 * 转换为布尔类型
 * @param {any} value - 原始值
 * @returns {boolean} 布尔类型的值
 */
export const toBoolean = (value) => {
  if (typeof value === 'boolean') return value;
  if (typeof value === 'string') {
    const lower = value.toLowerCase();
    return lower === 'true' || lower === '1' || lower === 'yes';
  }
  return Boolean(value);
};

/**
 * 格式化数字显示（补零）
 * @param {string|number} value - 原始值
 * @param {number} length - 目标长度，默认 2
 * @param {string} padChar - 补位字符，默认 '0'
 * @returns {string} 格式化后的字符串
 */
export const padZero = (value, length = 2, padChar = '0') => {
  return toString(value).padStart(length, padChar);
};

/**
 * 比较两个值是否相等（忽略类型）
 * @param {any} value1 - 第一个值
 * @param {any} value2 - 第二个值
 * @returns {boolean} 是否相等
 */
export const isEqual = (value1, value2) => {
  if (typeof value1 === typeof value2) {
    return value1 === value2;
  }
  return toString(value1) === toString(value2);
};

/**
 * 从数组中根据字段值查找项目
 * @param {Array} items - 数据数组
 * @param {string|number} value - 要查找的值
 * @param {string} key - 字段名，默认 'id'
 * @returns {Object|undefined} 找到的项目或 undefined
 */
export const findByField = (items, value, key = 'id') => {
  return items.find(item => isEqual(item[key], value));
};

/**
 * 从数组中根据字段值查找项目索引
 * @param {Array} items - 数据数组
 * @param {string|number} value - 要查找的值
 * @param {string} key - 字段名，默认 'id'
 * @returns {number} 索引，未找到返回 -1
 */
export const findIndexByField = (items, value, key = 'id') => {
  return items.findIndex(item => isEqual(item[key], value));
};

/**
 * 安全解析 JSON
 * @param {string} jsonString - JSON 字符串
 * @param {any} defaultValue - 解析失败时的默认值
 * @returns {any} 解析结果或默认值
 */
export const safeJsonParse = (jsonString, defaultValue = null) => {
  try {
    return JSON.parse(jsonString);
  } catch {
    return defaultValue;
  }
};

/**
 * 安全转换为整数
 * @param {any} value - 原始值
 * @param {number} defaultValue - 默认值
 * @returns {number} 整数
 */
export const toInteger = (value, defaultValue = 0) => {
  const num = parseInt(value, 10);
  return Number.isNaN(num) ? defaultValue : num;
};

/**
 * 安全转换为浮点数
 * @param {any} value - 原始值
 * @param {number} defaultValue - 默认值
 * @returns {number} 浮点数
 */
export const toFloat = (value, defaultValue = 0) => {
  const num = parseFloat(value);
  return Number.isNaN(num) ? defaultValue : num;
};

/**
 * 转换为数组
 * @param {any} value - 原始值
 * @returns {Array} 数组
 */
export const toArray = (value) => {
  if (Array.isArray(value)) return value;
  if (value === null || value === undefined) return [];
  return [value];
};

/**
 * 判断值是否为空
 * @param {any} value - 原始值
 * @returns {boolean} 是否为空
 */
export const isEmpty = (value) => {
  if (value === null || value === undefined) return true;
  if (typeof value === 'string') return value.trim() === '';
  if (Array.isArray(value)) return value.length === 0;
  if (typeof value === 'object') return Object.keys(value).length === 0;
  return false;
};

/**
 * 判断值是否为有效数字
 * @param {any} value - 原始值
 * @returns {boolean} 是否为有效数字
 */
export const isValidNumber = (value) => {
  return !Number.isNaN(Number(value));
};

// ID 相关的别名函数（向后兼容）
export const toNumberId = toNumber;
export const toStringId = toString;
export const formatId = padZero;
export const isIdEqual = isEqual;
export const findById = findByField;
export const findIndexById = findIndexByField;
