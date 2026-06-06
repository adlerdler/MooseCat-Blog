<script setup>
/**
 * Forbidden.vue - 403 无权限页面
 * 
 * 当用户访问没有权限的页面时显示此页面
 * 保持 Admin Layout 的导航框架
 *
 * 智能跳转：优先回到仪表盘，若仪表盘也无权限则跳转到首个可用页面
 */
import { computed } from 'vue'
import { ShieldOff, ArrowLeft, LayoutDashboard, Home } from 'lucide-vue-next'
import { router, usePage } from '@inertiajs/vue3'

const page = usePage()

const props = defineProps({
  title: { type: String, default: null },
  message: { type: String, default: null },
  permission: { type: String, default: null },
})

const displayTitle = computed(() => props.title || '访问被拒绝')
const displayMessage = computed(() => {
  if (props.message) return props.message
  if (props.permission) return `您需要权限「${props.permission}」才能访问此页面`
  return '抱歉，您没有权限访问此页面。如需获取访问权限，请联系管理员。'
})

/**
 * 从已按权限过滤的菜单树中查找第一个可导航路径
 */
const findFirstPath = (items) => {
  for (const item of items) {
    if (item.path) return item.path
    if (item.children?.length) {
      const found = findFirstPath(item.children)
      if (found) return found
    }
  }
  return null
}

const firstAccessibleRoute = computed(() => {
  const menus = page.props.menus || []
  return findFirstPath(menus)
})

const isDashboardAccessible = computed(() => {
  const path = firstAccessibleRoute.value
  return path === '/admin' || path === '/admin/index' || path === '/'
})

/**
 * 检查用户是否有任何后台权限
 */
const hasAnyAdminPermission = computed(() => {
  const permissions = page.props.auth?.user?.permissions || []
  return permissions.length > 0
})

const goBack = () => {
  const currentPath = window.location.pathname
  // 已在目标页，不做跳转
  if (firstAccessibleRoute.value && currentPath === firstAccessibleRoute.value) {
    return
  }
  if (firstAccessibleRoute.value) {
    router.visit(firstAccessibleRoute.value)
  } else {
    // 没有任何可访问页面，跳转到前台首页
    router.visit('/')
  }
}

const goHistoryBack = () => {
  if (window.history.length > 1) {
    window.history.back()
  } else if (firstAccessibleRoute.value) {
    router.visit(firstAccessibleRoute.value)
  }
}
</script>

<template>
  <div class="min-h-[80vh] flex flex-col items-center justify-center px-4">
    <div class="text-center max-w-md">
      <!-- Icon -->
      <div class="mx-auto mb-6 w-20 h-20 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
        <ShieldOff class="w-10 h-10 text-amber-600 dark:text-amber-400" />
      </div>

      <!-- Error Code -->
      <div class="text-7xl font-black text-gray-200 dark:text-gray-700 select-none mb-4">
        403
      </div>

      <!-- Title -->
      <h1 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">
        {{ displayTitle }}
      </h1>

      <!-- Description -->
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
        {{ displayMessage }}
      </p>

      <!-- Permission Badge (if available) -->
      <div v-if="permission" class="mb-8">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-xs font-mono text-gray-600 dark:text-gray-400">
          <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
          {{ permission }}
        </span>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-center gap-3">
        <button
          v-if="firstAccessibleRoute"
          @click="goBack"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 font-medium text-sm transition-colors border border-gray-200 dark:border-gray-700"
        >
          <LayoutDashboard v-if="isDashboardAccessible" class="w-4 h-4" />
          <Home v-else class="w-4 h-4" />
          <span v-if="isDashboardAccessible">回到仪表盘</span>
          <span v-else>前往可用页面</span>
        </button>
        <button
          v-else
          @click="router.visit('/')"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 font-medium text-sm transition-colors border border-gray-200 dark:border-gray-700"
        >
          <Home class="w-4 h-4" />
          返回首页
        </button>
        <button
          @click="goHistoryBack"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium text-sm transition-colors"
        >
          <ArrowLeft class="w-4 h-4" />
          返回上一页
        </button>
      </div>
    </div>
  </div>
</template>
