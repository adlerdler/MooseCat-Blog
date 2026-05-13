/**
 * home.js - 首页数据
 * 
 * 功能说明：
 * - 存储首页相关的静态数据
 * - 包含分类、技术栈、精选文章等信息
 */

/**
 * 文章分类列表
 */
export const categories = ['ALL', 'THEORY', 'DESIGN', 'HISTORY', 'CULTURE'];

/**
 * 跑马灯文本
 */
export const marqueeText = 'ARCHYX VOL. 2026 // BUILDING SYSTEM // MINIMALISM //';

/**
 * 技术栈列表
 */
export const techStack = ['TYPESCRIPT', 'VUE', 'LARAVEL', 'TAILWIND', 'NODE.JS', 'POSTGRES'];

/**
 * 精选文章数据
 */
export const featuredPosts = [
  {
    id: 1,
    title: 'ARCHITECTURAL PRINCIPLES',
    excerpt: 'Exploring the fundamental concepts that define modern structural design and digital innovation.',
    category: 'THEORY',
    categoryLabel: '理论',
    views_count: 1234
  },
  {
    id: 2,
    title: 'DIGITAL FABRICATION',
    excerpt: 'How computational design is revolutionizing the way we approach construction and manufacturing.',
    category: 'DESIGN',
    categoryLabel: '设计',
    views_count: 987
  },
  {
    id: 3,
    title: 'SUSTAINABLE MATERIALS',
    excerpt: 'An investigation into eco-friendly building materials and their impact on environmental architecture.',
    category: 'HISTORY',
    categoryLabel: '历史',
    views_count: 756
  }
];
