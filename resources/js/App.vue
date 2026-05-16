<script setup>
/**
 * App.vue - 主应用组件
 * 
 * 功能说明：
 * - 根据路由元信息动态选择布局组件
 * - 管理后台页面使用 AdminLayout
 * - 前台页面直接显示内容
 */
import '../css/frontend.css';
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AdminLayout from './Pages/admin/Layout.vue';

const route = useRoute();

const isAdminLayout = computed(() => {
  return route.meta.layout === 'admin';
});
</script>

<template>
  <AdminLayout v-if="isAdminLayout">
    <router-view />
  </AdminLayout>
  <router-view v-else />
</template>
