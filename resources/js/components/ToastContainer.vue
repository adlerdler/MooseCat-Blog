<script setup>
/**
 * ToastContainer.vue - Toast 容器组件
 *
 * 放置在 App.vue 或布局组件中使用
 * <ToastContainer />
 */
import { useToast } from '../composables/useToast';
import ToastMessage from './admin/ToastMessage.vue';

const { toasts, removeToast } = useToast();
</script>

<template>
  <div class="fixed top-4 right-4 z-50 space-y-2">
    <ToastMessage
      v-for="toast in toasts"
      :key="toast.id"
      :visible="true"
      :type="toast.type"
      :title="toast.title"
      :message="toast.message"
      :duration="toast.duration"
      :closable="toast.closable"
      @close="removeToast(toast.id)"
    />
  </div>
</template>
