/**
 * useDragSort.js - 拖拽排序 Composable
 * 
 * 提供 Vue 风格的拖拽排序功能，支持：
 * - 原生 HTML5 拖拽事件封装
 * - 实时排序更新
 * - 防抖保存到服务器（批量更新）
 */

import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export function useDragSort(options = {}) {
  const {
    batchUpdateUrl = '',
    onUpdateSuccess = () => {},
    onUpdateError = () => {},
    debounceDelay = 800,
    itemKey = 'id',
    sortField = 'sort_order',
    dataKey = 'menus', // 后端接收数据的键名
    mapItem = (item) => item // 新增：保存前的转换函数
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
    e.dataTransfer.setData('text/plain', item[itemKey]);
  };

  const handleDragOver = (e, targetItem, items) => {
    e.preventDefault();
    if (draggedItem.value && draggedItem.value[itemKey] !== targetItem[itemKey]) {
      const draggedIndex = items.findIndex(item => item[itemKey] === draggedItem.value[itemKey]);
      const targetIndex = items.findIndex(item => item[itemKey] === targetItem[itemKey]);

      if (draggedIndex !== -1 && targetIndex !== -1) {
        // Create new array with updated order
        const newItems = [...items];
        const [dragged] = newItems.splice(draggedIndex, 1);
        newItems.splice(targetIndex, 0, dragged);

        // Update sort field in place for UI feedback
        newItems.forEach((item, index) => {
          item[sortField] = index + 1;
        });

        // Update the original array reference
        items.length = 0;
        items.push(...newItems);
      }
    }
  };

  // Debounced batch save function
  const debouncedSave = debounce((items) => {
    if (batchUpdateUrl && items.length > 0) {
      // Prepare batch update data with all item properties
      const updateData = items.map((item, index) => {
        const mapped = mapItem(item);
        return {
          ...mapped,
          [sortField]: index + 1,
        };
      });

      // Send single batch update request
      router.post(batchUpdateUrl, { [dataKey]: updateData }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: onUpdateSuccess,
        onError: onUpdateError
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
