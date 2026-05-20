/**
 * interactions.js - 用户互动数据
 *
 * 统一的互动数据结构（以数据库为准）：
 * - id: 互动唯一标识
 * - user_id: 用户ID（外键）
 * - interactable_id: 关联对象ID（多态关联）
 * - interactable_type: 关联对象类型（Post/Video/Project等）
 * - type: 互动类型（like/bookmark）
 * - created_at: 创建时间
 * - updated_at: 更新时间
 */

export const interactions = [
  {
    id: 1,
    user_id: 1,
    interactable_id: 1,
    interactable_type: 'Post',
    type: 'like',
    created_at: '2024-01-15T10:30:00',
    updated_at: '2024-01-15T10:30:00'
  },
  {
    id: 2,
    user_id: 2,
    interactable_id: 1,
    interactable_type: 'Post',
    type: 'bookmark',
    created_at: '2024-01-15T11:00:00',
    updated_at: '2024-01-15T11:00:00'
  },
  {
    id: 3,
    user_id: 3,
    interactable_id: 2,
    interactable_type: 'Post',
    type: 'like',
    created_at: '2024-01-16T09:15:00',
    updated_at: '2024-01-16T09:15:00'
  },
  {
    id: 4,
    user_id: 1,
    interactable_id: 1,
    interactable_type: 'Video',
    type: 'like',
    created_at: '2024-01-16T14:20:00',
    updated_at: '2024-01-16T14:20:00'
  },
  {
    id: 5,
    user_id: 4,
    interactable_id: 3,
    interactable_type: 'Post',
    type: 'like',
    created_at: '2024-01-17T16:30:00',
    updated_at: '2024-01-17T16:30:00'
  },
  {
    id: 6,
    user_id: 2,
    interactable_id: 2,
    interactable_type: 'Video',
    type: 'bookmark',
    created_at: '2024-01-18T08:20:00',
    updated_at: '2024-01-18T08:20:00'
  },
  {
    id: 7,
    user_id: 5,
    interactable_id: 1,
    interactable_type: 'Project',
    type: 'like',
    created_at: '2024-01-18T13:55:00',
    updated_at: '2024-01-18T13:55:00'
  },
  {
    id: 8,
    user_id: 3,
    interactable_id: 4,
    interactable_type: 'Post',
    type: 'bookmark',
    created_at: '2024-01-19T10:10:00',
    updated_at: '2024-01-19T10:10:00'
  },
  {
    id: 9,
    user_id: 6,
    interactable_id: 2,
    interactable_type: 'Post',
    type: 'like',
    created_at: '2024-01-20T14:30:00',
    updated_at: '2024-01-20T14:30:00'
  },
  {
    id: 10,
    user_id: 1,
    interactable_id: 3,
    interactable_type: 'Video',
    type: 'like',
    created_at: '2024-01-21T09:15:00',
    updated_at: '2024-01-21T09:15:00'
  },
  {
    id: 11,
    user_id: 7,
    interactable_id: 5,
    interactable_type: 'Post',
    type: 'like',
    created_at: '2024-01-22T16:45:00',
    updated_at: '2024-01-22T16:45:00'
  },
  {
    id: 12,
    user_id: 4,
    interactable_id: 1,
    interactable_type: 'Post',
    type: 'bookmark',
    created_at: '2024-01-23T11:30:00',
    updated_at: '2024-01-23T11:30:00'
  }
];

/**
 * 获取所有互动记录
 * @returns {Array} 互动数组
 */
export const getInteractions = () => {
  return interactions;
};

/**
 * 根据用户ID获取互动记录
 * @param {number} userId - 用户ID
 * @returns {Array} 互动数组
 */
export const getInteractionsByUserId = (userId) => {
  return interactions.filter(i => i.user_id === userId);
};

/**
 * 根据关联对象获取互动记录
 * @param {number} interactableId - 关联对象ID
 * @param {string} interactableType - 关联对象类型
 * @returns {Array} 互动数组
 */
export const getInteractionsByTarget = (interactableId, interactableType) => {
  return interactions.filter(
    i => i.interactable_id === interactableId && i.interactable_type === interactableType
  );
};

/**
 * 获取点赞记录
 * @param {number} interactableId - 关联对象ID
 * @param {string} interactableType - 关联对象类型
 * @returns {Array} 点赞数组
 */
export const getLikesByTarget = (interactableId, interactableType) => {
  return interactions.filter(
    i => i.interactable_id === interactableId && 
         i.interactable_type === interactableType && 
         i.type === 'like'
  );
};

/**
 * 获取收藏记录
 * @param {number} userId - 用户ID
 * @returns {Array} 收藏数组
 */
export const getBookmarksByUserId = (userId) => {
  return interactions.filter(
    i => i.user_id === userId && i.type === 'bookmark'
  );
};

/**
 * 检查用户是否已点赞
 * @param {number} userId - 用户ID
 * @param {number} interactableId - 关联对象ID
 * @param {string} interactableType - 关联对象类型
 * @returns {boolean} 是否已点赞
 */
export const hasUserLiked = (userId, interactableId, interactableType) => {
  return interactions.some(
    i => i.user_id === userId && 
         i.interactable_id === interactableId && 
         i.interactable_type === interactableType && 
         i.type === 'like'
  );
};

/**
 * 检查用户是否已收藏
 * @param {number} userId - 用户ID
 * @param {number} interactableId - 关联对象ID
 * @param {string} interactableType - 关联对象类型
 * @returns {boolean} 是否已收藏
 */
export const hasUserBookmarked = (userId, interactableId, interactableType) => {
  return interactions.some(
    i => i.user_id === userId && 
         i.interactable_id === interactableId && 
         i.interactable_type === interactableType && 
         i.type === 'bookmark'
  );
};

/**
 * 获取所有互动类型
 * @returns {Array} 互动类型数组
 */
export const getInteractionTypes = () => {
  return ['like', 'bookmark'];
};

/**
 * 获取互动类型标签
 * @param {string} type - 互动类型
 * @returns {string} 类型标签
 */
export const getInteractionTypeLabel = (type) => {
  const labels = {
    like: '点赞',
    bookmark: '收藏'
  };
  return labels[type] || type;
};
