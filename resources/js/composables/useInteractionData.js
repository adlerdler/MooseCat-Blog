/**
 * useInteractionData.js - 互动数据 Composable
 * 
 * 功能说明：
 * - 提供互动相关的工具函数
 * - 供 PostDetail.vue 和 CommentSection.vue 使用
 */
import { interactions } from '../data/interactions';

export function useInteractionData() {
  const getLikesByTarget = (interactableId, interactableType) => {
    return interactions.filter(
      i => i.interactable_id === interactableId && 
           i.interactable_type === interactableType && 
           i.type === 'like'
    );
  };

  const hasUserLiked = (userId, interactableId, interactableType) => {
    return interactions.some(
      i => i.user_id === userId && 
           i.interactable_id === interactableId && 
           i.interactable_type === interactableType && 
           i.type === 'like'
    );
  };

  const hasUserBookmarked = (userId, interactableId, interactableType) => {
    return interactions.some(
      i => i.user_id === userId && 
           i.interactable_id === interactableId && 
           i.interactable_type === interactableType && 
           i.type === 'bookmark'
    );
  };

  const getBookmarksByUserId = (userId) => {
    return interactions.filter(
      i => i.user_id === userId && i.type === 'bookmark'
    );
  };

  return {
    getLikesByTarget,
    hasUserLiked,
    hasUserBookmarked,
    getBookmarksByUserId
  };
}
