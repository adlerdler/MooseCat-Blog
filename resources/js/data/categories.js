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
/**
 * 分类数据数组（前台后台共用）
 *
 * 字段说明：
 * - id: 分类唯一标识
 * - parent_id: 父分类ID（用于多级分类，null表示顶级分类）
 * - name: 分类名称（英文大写，用于程序判断）
 * - slug: 分类别名（URL友好）
 * - label: 分类标签（中文，用于展示）
 * - description: 分类描述
 * - sort_order: 排序序号
 * - createdAt: 创建时间
 * - updatedAt: 更新时间
 * - postCount: 该分类下的文章数量（后台管理使用）
 * - status: 状态（active/inactive，后台管理使用）
 */
export const categories = [
  {
    id: 1,
    parent_id: null,
    name: 'THEORY',
    slug: 'theory',
    label: '理论',
    description: '理论研究与探索',
    sort_order: 1,
    createdAt: '2024-01-01T00:00:00Z',
    updatedAt: '2024-01-01T00:00:00Z',
    postCount: 24,
    status: 'active'
  },
  {
    id: 2,
    parent_id: null,
    name: 'DESIGN',
    slug: 'design',
    label: '设计',
    description: '设计与创意',
    sort_order: 2,
    createdAt: '2024-01-01T00:00:00Z',
    updatedAt: '2024-01-01T00:00:00Z',
    postCount: 18,
    status: 'active'
  },
  {
    id: 3,
    parent_id: null,
    name: 'CULTURE',
    slug: 'culture',
    label: '文化',
    description: '文化与艺术',
    sort_order: 3,
    createdAt: '2024-01-01T00:00:00Z',
    updatedAt: '2024-01-01T00:00:00Z',
    postCount: 12,
    status: 'active'
  },
  {
    id: 4,
    parent_id: null,
    name: 'SYSTEM-DESIGN',
    slug: 'system-design',
    label: '系统设计',
    description: '系统架构与设计',
    sort_order: 4,
    createdAt: '2024-01-01T00:00:00Z',
    updatedAt: '2024-01-01T00:00:00Z',
    postCount: 15,
    status: 'active'
  },
  {
    id: 5,
    parent_id: null,
    name: 'ENGINEERING',
    slug: 'engineering',
    label: '工程',
    description: '工程与实践',
    sort_order: 5,
    createdAt: '2024-01-01T00:00:00Z',
    updatedAt: '2024-01-01T00:00:00Z',
    postCount: 8,
    status: 'active'
  },
  {
    id: 6,
    parent_id: null,
    name: 'HISTORY',
    slug: 'history',
    label: '历史',
    description: '历史与发展',
    sort_order: 6,
    createdAt: '2024-01-01T00:00:00Z',
    updatedAt: '2024-01-01T00:00:00Z',
    postCount: 20,
    status: 'active'
  },
];

