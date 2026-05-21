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
 * - parent_id: 父分类ID（用于多级分类，null表示顶级分类）
 * - name: 分类名称（英文大写，用于程序判断）
 * - slug: 分类别名（URL友好）
 * - description: 分类描述
 * - sort_order: 排序序号
 * - created_at: 创建时间
 * - updated_at: 更新时间
 */
export const categories = [
  {
    id: 1,
    parent_id: null,
    name: 'THEORY',
    slug: 'theory',
    description: '理论研究与探索',
    sort_order: 1,
    status: 'active',
    postCount: 12,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 2,
    parent_id: null,
    name: 'DESIGN',
    slug: 'design',
    description: '设计与创意',
    sort_order: 2,
    status: 'active',
    postCount: 8,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 3,
    parent_id: null,
    name: 'CULTURE',
    slug: 'culture',
    description: '文化与艺术',
    sort_order: 3,
    status: 'active',
    postCount: 5,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 4,
    parent_id: null,
    name: 'SYSTEM-DESIGN',
    slug: 'system-design',
    description: '系统架构与设计',
    sort_order: 4,
    status: 'active',
    postCount: 15,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 5,
    parent_id: null,
    name: 'ENGINEERING',
    slug: 'engineering',
    description: '工程与实践',
    sort_order: 5,
    status: 'active',
    postCount: 10,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 6,
    parent_id: null,
    name: 'HISTORY',
    slug: 'history',
    description: '历史与发展',
    sort_order: 6,
    status: 'inactive',
    postCount: 3,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
];
