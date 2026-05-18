/**
 * taggables.js - 标签多态关联中间表
 *
 * 功能说明：
 * - 实现标签(Tags)与多种内容模型的多态多对多关联
 * - 模拟 Laravel 的 polymorphic many-to-many 关系
 * - 支持关联到 posts、videos、projects 等多种类型
 *
 * 字段说明：
 * - id: 关联记录唯一标识
 * - tag_id: 标签ID（关联 tags.js）
 * - taggable_id: 关联对象的ID
 * - taggable_type: 关联对象的类型（如 'Post'、'Video'、'Project'）
 *
 * 注意：逻辑已迁移到 composables/useTaggables.js
 */

/**
 * 标签多态关联数据
 *
 * 模拟数据库中的 taggables 中间表
 * 实际项目中，taggable_type 通常是完整的模型类名如 'App\\Models\\Post'
 * 这里使用简化的类型名称
 */
export const taggables = [
  // Posts 关联的标签 (posts.js 中 id: 1-5)
  { id: 1, tag_id: 1, taggable_type: 'Post', taggable_id: 1 },   // Architecture -> Post 1
  { id: 2, tag_id: 2, taggable_type: 'Post', taggable_id: 1 },   // Design -> Post 1
  { id: 3, tag_id: 10, taggable_type: 'Post', taggable_id: 1 },  // Algorithm -> Post 1

  { id: 4, tag_id: 2, taggable_type: 'Post', taggable_id: 2 },   // Design -> Post 2
  { id: 5, tag_id: 14, taggable_type: 'Post', taggable_id: 2 },  // Urban Design -> Post 2

  { id: 6, tag_id: 3, taggable_type: 'Post', taggable_id: 3 },   // Technology -> Post 3
  { id: 7, tag_id: 8, taggable_type: 'Post', taggable_id: 3 },   // Algorithm -> Post 3

  { id: 8, tag_id: 10, taggable_type: 'Post', taggable_id: 4 },  // Algorithm -> Post 4
  { id: 9, tag_id: 11, taggable_type: 'Post', taggable_id: 4 },  // Parametric -> Post 4

  { id: 10, tag_id: 1, taggable_type: 'Post', taggable_id: 5 },  // Architecture -> Post 5
  { id: 11, tag_id: 13, taggable_type: 'Post', taggable_id: 5 },  // Sustainability -> Post 5

  // Projects 关联的标签 (projects.js)
  { id: 12, tag_id: 2, taggable_type: 'Project', taggable_id: 1 }, // Design -> Project 1
  { id: 13, tag_id: 6, taggable_type: 'Project', taggable_id: 1 }, // Tutorial -> Project 1
  { id: 14, tag_id: 7, taggable_type: 'Project', taggable_id: 1 },  // Case Study -> Project 1

  { id: 15, tag_id: 1, taggable_type: 'Project', taggable_id: 2 },  // Architecture -> Project 2
  { id: 16, tag_id: 4, taggable_type: 'Project', taggable_id: 2 },  // Philosophy -> Project 2

  // Videos 关联的标签 (videos.js - 待扩展)
  // { id: 17, tag_id: 3, taggable_type: 'Video', taggable_id: 1 },

  // Resources 关联的标签 (resources.js - 待扩展)
  // { id: 18, tag_id: 5, taggable_type: 'Resource', taggable_id: 1 },
];
