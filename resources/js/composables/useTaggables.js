/**
 * useTaggables.js - 标签多态关联管理
 *
 * 功能说明：
 * - 实现标签(Tags)与多种内容模型的多态多对多关联
 * - 模拟 Laravel 的 polymorphic many-to-many 关系
 * - 支持关联到 posts、videos、projects 等多种类型
 *
 * 使用场景：
 * - 获取某个标签关联的所有内容
 * - 获取某个内容的所有标签
 * - 标签筛选和搜索
 */
import { taggables } from '../data/taggables';
import { adminTags } from '../data/tags';

export const useTaggables = () => {
  /**
   * 根据类型获取关联的标签ID列表
   * @param {string} type - 内容类型（如 'Post'、'Video'、'Project'）
   * @param {number} id - 内容ID
   * @returns {number[]} 标签ID数组
   */
  const getTagIdsByTaggable = (type, id) => {
    return taggables
      .filter(t => t.taggable_type === type && t.taggable_id === id)
      .map(t => t.tag_id);
  };

  /**
   * 根据类型和ID获取标签对象列表
   * @param {string} type - 内容类型
   * @param {number} id - 内容ID
   * @param {Array} allTags - 所有标签数组（可选，默认使用 adminTags）
   * @returns {Array} 标签对象数组
   */
  const getTagsByTaggable = (type, id, allTags = adminTags) => {
    const tagIds = getTagIdsByTaggable(type, id);
    return allTags.filter(tag => tagIds.includes(tag.id));
  };

  /**
   * 根据标签ID获取所有关联的内容
   * @param {number} tagId - 标签ID
   * @returns {Array} 关联记录数组 [{type, id}]
   */
  const getTaggablesByTagId = (tagId) => {
    return taggables
      .filter(t => t.tag_id === tagId)
      .map(t => ({ type: t.taggable_type, id: t.taggable_id }));
  };

  /**
   * 检查某个内容是否关联了指定标签
   * @param {string} type - 内容类型
   * @param {number} id - 内容ID
   * @param {number} tagId - 标签ID
   * @returns {boolean}
   */
  const hasTag = (type, id, tagId) => {
    return taggables.some(
      t => t.taggable_type === type && t.taggable_id === id && t.tag_id === tagId
    );
  };

  /**
   * 便捷函数：获取文章的所有标签
   * @param {number} postId - 文章ID
   * @param {Array} allTags - 所有标签数组（可选）
   * @returns {Array} 标签对象数组
   */
  const getTagsByPostId = (postId, allTags = adminTags) => {
    return getTagsByTaggable('Post', postId, allTags);
  };

  /**
   * 便捷函数：获取项目的所有标签
   * @param {number} projectId - 项目ID
   * @param {Array} allTags - 所有标签数组（可选）
   * @returns {Array} 标签对象数组
   */
  const getTagsByProjectId = (projectId, allTags = adminTags) => {
    return getTagsByTaggable('Project', projectId, allTags);
  };

  return {
    taggables,
    adminTags,
    getTagIdsByTaggable,
    getTagsByTaggable,
    getTaggablesByTagId,
    hasTag,
    getTagsByPostId,
    getTagsByProjectId
  };
};
