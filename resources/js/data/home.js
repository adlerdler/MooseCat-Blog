/**
 * home.js - 首页数据
 * 
 * 功能说明：
 * - 存储首页相关的静态数据
 * - 包含分类、技术栈等信息
 */

import { homeCategories } from './categories.js';

/**
 * 文章分类列表（从统一分类数据导入）
 */
export const categories = homeCategories;

/**
 * 跑马灯文本
 */
export const marqueeText = 'ARCHYX VOL. 2026 // BUILDING SYSTEM // MINIMALISM //';

/**
 * 技术栈列表
 */
export const techStack = ['TYPESCRIPT', 'VUE', 'LARAVEL', 'TAILWIND', 'NODE.JS', 'POSTGRES'];
