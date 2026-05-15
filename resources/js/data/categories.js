/**
 * categories.js - 分类数据（前台后台统一）
 * 
 * 功能说明：
 * - 纯数据文件，存储分类数据
 * - 前台和后台共用同一张数据表
 * - 不包含任何业务逻辑
 * 
 * 分类列表：
 * - THEORY: 理论
 * - DESIGN: 设计
 * - CULTURE: 文化
 * - SYSTEM-DESIGN: 系统设计
 * - ENGINEERING: 工程
 * - HISTORY: 历史
 */

/**
 * 分类数据数组（前台后台共用）
 * 
 * 字段说明：
 * - id: 分类唯一标识
 * - name: 分类名称（英文大写，用于程序判断）
 * - label: 分类标签（中文，用于展示）
 * - description: 分类描述
 * - postCount: 该分类下的文章数量（后台管理使用）
 * - status: 状态（active/inactive，后台管理使用）
 */
export const categories = [
  {
    id: 1,
    name: 'THEORY',
    label: '理论',
    description: '理论研究与探索',
    postCount: 24,
    status: 'active'
  },
  {
    id: 2,
    name: 'DESIGN',
    label: '设计',
    description: '设计与创意',
    postCount: 18,
    status: 'active'
  },
  {
    id: 3,
    name: 'CULTURE',
    label: '文化',
    description: '文化与艺术',
    postCount: 12,
    status: 'active'
  },
  {
    id: 4,
    name: 'SYSTEM-DESIGN',
    label: '系统设计',
    description: '系统架构与设计',
    postCount: 15,
    status: 'active'
  },
  {
    id: 5,
    name: 'ENGINEERING',
    label: '工程',
    description: '工程与实践',
    postCount: 8,
    status: 'active'
  },
  {
    id: 6,
    name: 'HISTORY',
    label: '历史',
    description: '历史与发展',
    postCount: 20,
    status: 'active'
  },
];

/**
 * 首页分类（包含 ALL 选项，前台使用）
 */
export const homeCategories = ['ALL', ...categories.map(c => c.name)];

/**
 * 管理后台分类数据（别名，保持向后兼容）
 */
export const adminCategories = categories;