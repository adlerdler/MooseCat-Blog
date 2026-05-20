/**
 * componentRegistry.js - 组件注册表
 *
 * 功能说明：
 * - 统一管理前后台页面组件
 * - 支持动态路由自动注册
 * - 使用命名约定自动解析组件路径
 *
 * 路径解析规则：
 * - 前台组件：{ComponentName} → ./Pages/front/{ComponentName}.vue
 *   例：Blog → ./Pages/front/Blog.vue
 * - 后台组件：{ComponentName} → ./Pages/admin/{ComponentName}.vue
 *   例：AdminPosts → ./Pages/admin/Posts.vue
 *
 * 使用方式：
 * 1. 创建组件文件
 * 2. 在 menu.js 中添加菜单项，设置 component_name
 * 3. 无需修改本文件（除特殊情况外）
 */

import { defineAsyncComponent } from 'vue';

const ComponentPathOverrides = {
  UserDetail: () => import('./Pages/admin/UserDetail.vue'),
  AdminFrontMenu: () => import('./Pages/admin/FrontMenu.vue'),
  SeoManager: () => import('./pages/admin/SeoManager.vue'),
};

function deriveFrontComponentPath(componentName) {
  const path = `./Pages/front/${componentName}.vue`;
  return () => import(/* @vite-ignore */ path);
}

function deriveAdminComponentPath(componentName) {
  let targetName = componentName;
  if (componentName.startsWith('Admin')) {
    targetName = componentName.replace(/^Admin/, '');
  }
  const path = `./Pages/admin/${targetName}.vue`;
  return () => import(/* @vite-ignore */ path);
}

export const componentRegistry = {
  getComponent(name, type = 'admin') {
    if (ComponentPathOverrides[name]) {
      return defineAsyncComponent(ComponentPathOverrides[name]);
    }

    const loader = type === 'front'
      ? deriveFrontComponentPath(name)
      : deriveAdminComponentPath(name);

    return defineAsyncComponent(loader);
  },
  hasComponent(name) {
    return true;
  }
};

export default componentRegistry;
