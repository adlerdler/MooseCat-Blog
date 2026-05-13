<script setup>
/**
 * AdminLayout.vue - 管理后台布局组件
 * 
 * 功能说明：
 * - 统一的管理后台布局框架
 * - 包含顶部导航栏和侧边栏菜单
 * - 提供用户信息显示和登出功能
 * - 与前台风格保持一致的设计
 * 
 * 布局结构：
 * - Header: 顶部导航栏（logo、用户信息、登出按钮）
 * - Sidebar: 侧边栏菜单（导航链接）
 * - Main: 主内容区域（路由视图）
 */
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import {
  LayoutDashboard,
  FileText,
  Play,
  FolderKanban,
  BookOpen,
  Users,
  Settings,
  BarChart3,
  LogOut,
  User,
  Menu,
  X,
  Folder,
  Tag,
  MessageSquare,
  Shield,
  HardDrive
} from 'lucide-vue-next';
import ThemeToggle from '../../components/ThemeToggle.vue';
import ToastContainer from '../../components/ToastContainer.vue';
import { useTheme } from '../../composables/useTheme';

const { t } = useI18n();
const router = useRouter();
const route = useRoute();
const adminEmail = ref('');
const isSidebarOpen = ref(true);
const { isDarkMode } = useTheme();

onMounted(() => {
  adminEmail.value = localStorage.getItem('admin_email') || 'Archyx@admin.com';
});

const menuItems = [
  { id: 'dashboard', label: t('admin_dashboard'), icon: LayoutDashboard, route: '/admin/index' },
  { id: 'posts', label: t('admin_posts'), icon: FileText, route: '/admin/posts' },
  { id: 'videos', label: t('admin_videos'), icon: Play, route: '/admin/videos' },
  { id: 'projects', label: t('admin_projects'), icon: FolderKanban, route: '/admin/projects' },
  { id: 'resources', label: t('admin_resources'), icon: BookOpen, route: '/admin/resources' },
  { id: 'categories', label: t('admin_categories'), icon: Folder, route: '/admin/categories' },
  { id: 'tags', label: t('admin_tags'), icon: Tag, route: '/admin/tags' },
  { id: 'comments', label: t('admin_comments'), icon: MessageSquare, route: '/admin/comments' },
  { id: 'users', label: t('admin_users'), icon: Users, route: '/admin/users' },
  { id: 'roles', label: t('admin_roles'), icon: Shield, route: '/admin/roles' },
  { id: 'media', label: t('admin_media'), icon: HardDrive, route: '/admin/media' },
  { id: 'settings', label: t('admin_settings'), icon: Settings, route: '/admin/settings' },
];

const currentRouteName = computed(() => route.name);

const isActiveRoute = (item) => {
  if (!item.route) return false;
  return route.path.startsWith(item.route);
};

const navigateTo = (item) => {
  if (item.route) {
    router.push(item.route);
  }
};

const handleLogout = () => {
  localStorage.removeItem('admin_logged_in');
  localStorage.removeItem('admin_email');
  localStorage.removeItem('admin_login_time');
  router.push('/admin/login');
};

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<template>
  <div :class="['min-h-screen transition-colors', isDarkMode ? 'bg-gray-900 text-white' : 'bg-white text-gray-900 admin-layout']">
    <!-- Header -->
    <header :class="['fixed top-0 left-0 right-0 h-16 flex items-center justify-between px-6 z-50 transition-colors', isDarkMode ? 'bg-gray-800 border-b border-gray-700' : 'bg-white border-b border-gray-200 admin-header']">
      <div class="flex items-center gap-4">
        <button
          @click="toggleSidebar"
          :class="['p-2 transition-colors lg:hidden', isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100']"
        >
          <Menu v-if="!isSidebarOpen" size="24" />
          <X v-else size="24" />
        </button>
        
        <!-- Logo -->
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-construct-red flex items-center justify-center">
            <span class="text-xl font-bold tracking-tight text-white">A</span>
          </div>
          <span class="font-display text-xl tracking-tighter hidden sm:block">ARCHYX</span>
        </div>
      </div>
      
      <!-- User Info -->
      <div class="flex items-center gap-4">
        <ThemeToggle />
        <div class="hidden md:flex flex-col items-end">
          <span class="text-sm font-bold text-construct-red">ADMIN</span>
          <span :class="['text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ adminEmail }}</span>
        </div>
        <div :class="['w-10 h-10 rounded-full flex items-center justify-center', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
          <User :size="18" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'" />
        </div>
        <button
          @click="handleLogout"
          :class="['p-2 transition-colors', isDarkMode ? 'text-gray-400 hover:text-red-400 hover:bg-gray-700' : 'text-gray-500 hover:text-red-500 hover:bg-gray-100']"
          :title="t('login_logout')"
        >
          <LogOut size="20" />
        </button>
      </div>
    </header>
    
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed left-0 top-16 bottom-0 transition-all duration-300 z-40 overflow-hidden admin-sidebar',
        isDarkMode ? 'bg-gray-800 border-r border-gray-700' : 'bg-white border-r border-gray-200',
        isSidebarOpen ? 'w-64' : 'w-0 lg:w-64'
      ]"
    >
      <nav class="p-4 space-y-1 h-full">
        <button
          v-for="item in menuItems"
          :key="item.id"
          @click="navigateTo(item)"
          :disabled="!item.route"
          :class="[
            'w-full flex items-center gap-3 px-4 py-3 text-sm font-bold tracking-widest uppercase transition-all rounded-lg',
            isActiveRoute(item)
              ? isDarkMode
                ? 'bg-red-500/10 border-l-4 border-construct-red text-construct-red'
                : 'bg-red-50 border-l-4 border-construct-red text-construct-red'
              : isDarkMode
                ? 'text-gray-400 hover:bg-gray-700/50 hover:text-white'
                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
            !item.route && 'opacity-50 cursor-not-allowed'
          ]"
        >
          <component 
            :is="item.icon" 
            size="18"
            :class="[
              isActiveRoute(item) ? '!text-construct-red' : (isDarkMode ? 'text-gray-400' : 'text-gray-600')
            ]"
          />
          <span>{{ item.label }}</span>
        </button>
      </nav>
    </aside>
    
    <!-- Main Content -->
    <main
      :class="[
        'pt-16 min-h-screen transition-all duration-300 admin-content',
        isDarkMode ? 'bg-gray-900' : 'bg-gray-50',
        isSidebarOpen ? 'lg:ml-64' : 'lg:ml-64'
      ]"
    >
      <slot />
    </main>
    
    <!-- Mobile Overlay -->
    <div
      v-if="isSidebarOpen"
      @click="isSidebarOpen = false"
      class="fixed inset-0 bg-black/50 z-30 lg:hidden"
    ></div>
    
    <!-- Toast Container -->
    <ToastContainer />
  </div>
</template>
