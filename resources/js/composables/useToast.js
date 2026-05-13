/**
 * useToast.js - Toast 通知管理
 * 
 * 使用方式：
 * const { showToast, success, error, warning, info } = useToast()
 * 
 * success('操作成功')
 * error('操作失败', '错误标题')
 * warning('请注意', '警告信息')
 * info('提示信息')
 * 
 * // 自定义配置
 * showToast({
 *   type: 'success',
 *   title: '标题',
 *   message: '消息内容',
 *   duration: 5000
 * })
 */
import { ref } from 'vue';

const toasts = ref([]);
let idCounter = 0;

export function useToast() {
  const showToast = (options) => {
    const id = ++idCounter;
    const toast = {
      id,
      type: options.type || 'info',
      title: options.title || '',
      message: options.message,
      duration: options.duration ?? 3000,
      closable: options.closable ?? true
    };

    toasts.value.push(toast);

    // 自动清理
    setTimeout(() => {
      removeToast(id);
    }, toast.duration + 500);

    return id;
  };

  const removeToast = (id) => {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index > -1) {
      toasts.value.splice(index, 1);
    }
  };

  const success = (message, title = '') => {
    return showToast({ type: 'success', title, message });
  };

  const error = (message, title = '') => {
    return showToast({ type: 'error', title, message, duration: 5000 });
  };

  const warning = (message, title = '') => {
    return showToast({ type: 'warning', title, message });
  };

  const info = (message, title = '') => {
    return showToast({ type: 'info', title, message });
  };

  return {
    toasts,
    showToast,
    removeToast,
    success,
    error,
    warning,
    info
  };
}
