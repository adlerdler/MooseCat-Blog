/**
 * home.js - 首页数据
 * 
 * 功能说明：
 * - 存储首页相关的静态数据
 * - 包含分类、技术栈等信息
 */

import { categories as allCategories } from './categories.js';

/**
 * 文章分类列表（包含 ALL 选项，前台首页使用）
 */
export const categories = ['ALL', ...allCategories.map(c => c.name)];

/**
 * 跑马灯文本
 */
export const marqueeText = 'ARCHYX VOL. 2026 // BUILDING SYSTEM // MINIMALISM //';

/**
 * 技术栈列表
 */
export const techStack = ['TYPESCRIPT', 'VUE', 'LARAVEL', 'TAILWIND', 'NODE.JS', 'POSTGRES'];
