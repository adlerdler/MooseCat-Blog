/**
 * useDragSort.js - 拖拽排序 Composable
 * 
 * 提供 Vue 风格的拖拽排序功能，支持：
 * - 原生 HTML5 拖拽事件封装
 * - 实时排序更新
 * - 防抖保存到服务器
 */

import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export function useDragSort(options = {}) {
  const {
    updateUrl = '',
    onUpdateSuccess = () => {},
    onUpdateError = () => {},
    debounceDelay = 500
  } = options;

  const draggedItem = ref(null);
  let debounceTimer = null;

  // Debounce function
  const debounce = (fn, delay) => {
    return (...args) => {
      if (debounceTimer) {
        clearTimeout(debounceTimer);
      }
      debounceTimer = setTimeout(() => {
        fn(...args);
      }, delay);
    };
  };

  const handleDragStart = (e, item) => {
    draggedItem.value = item;
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', item.id);
  };

  const handleDragOver = (e, targetItem, items) => {
    e.preventDefault();
    if (draggedItem.value && draggedItem.value.id !== targetItem.id) {
      const draggedIndex = items.findIndex(item => item.id === draggedItem.value.id);
      const targetIndex = items.findIndex(item => item.id === targetItem.id);

      if (draggedIndex !== -1 && targetIndex !== -1) {
        // Create new array with updated order
        const newItems = [...items];
        const [dragged] = newItems.splice(draggedIndex, 1);
        newItems.splice(targetIndex, 0, dragged);

        // Update sort_order in place for UI feedback
        newItems.forEach((item, index) => {
          item.sort_order = index + 1;
        });

        // Update the original array reference
        items.length = 0;
        items.push(...newItems);
      }
    }
  };

  // Debounced save function
  const debouncedSave = debounce((items) => {
    if (updateUrl) {
      // Send updated sort orders to server
      items.forEach((item, index) => {
        router.put(updateUrl(item.id), {
          sort_order: index + 1,
        }, {
          preserveState: true,
          onSuccess: onUpdateSuccess,
          onError: onUpdateError
        });
      });
    }
  }, debounceDelay);

  const handleDragEnd = (items) => {
    if (draggedItem.value) {
      debouncedSave(items);
    }
    draggedItem.value = null;
  };

  return {
    draggedItem,
    handleDragStart,
    handleDragOver,
    handleDragEnd
  };
}
