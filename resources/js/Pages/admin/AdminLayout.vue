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
import '../../../css/admin.css';
import { ref, onMounted, computed, onUnmounted } from 'vue';
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
  HardDrive,
  ChevronLeft,
  ChevronRight,
  ChevronDown,
  Info
} from 'lucide-vue-next';
import ThemeToggle from '../../components/ThemeToggle.vue';
import ToastContainer from '../../components/ToastContainer.vue';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import { useTheme } from '../../composables/useTheme';
import { adminMenuItems } from '../../data/menu';

const { t } = useI18n();
const router = useRouter();
const route = useRoute();
const adminEmail = ref('');
const isSidebarOpen = ref(true);
const isSidebarCollapsed = ref(false);
const isUserMenuOpen = ref(false);
const showLogoutConfirm = ref(false);
const expandedMenus = ref(new Set());
const { isDarkMode } = useTheme();

const toggleMenu = (menuId) => {
  if (expandedMenus.value.has(menuId)) {
    expandedMenus.value.delete(menuId);
  } else {
    expandedMenus.value.add(menuId);
  }
};

const isMenuExpanded = (menuId) => {
  return expandedMenus.value.has(menuId);
};

const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value;
};

const closeUserMenu = () => {
  isUserMenuOpen.value = false;
};

const handleLogout = () => {
  showLogoutConfirm.value = true;
  closeUserMenu();
};

const confirmLogout = () => {
  localStorage.removeItem('admin_logged_in');
  localStorage.removeItem('admin_email');
  localStorage.removeItem('admin_login_time');
  showLogoutConfirm.value = false;
  router.push({ name: 'admin-login' });
};

const handleResize = () => {
  const windowWidth = window.innerWidth;
  const isDesktop = windowWidth >= 1024;
  const isTablet = windowWidth >= 768 && windowWidth < 1024;
  
  if (isDesktop) {
    isSidebarOpen.value = true;
  } else if (isTablet) {
    isSidebarOpen.value = false;
  } else {
    isSidebarOpen.value = false;
  }
};

onMounted(() => {
  adminEmail.value = localStorage.getItem('admin_email') || 'Archyx@admin.com';
  const collapsed = localStorage.getItem('sidebar_collapsed');
  if (collapsed !== null) {
    isSidebarCollapsed.value = collapsed === 'true';
  }
  
  handleResize();
  window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
});

const toggleSidebarCollapse = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
  localStorage.setItem('sidebar_collapsed', isSidebarCollapsed.value.toString());
};

const iconMap = {
  layoutDashboard: LayoutDashboard,
  fileText: FileText,
  play: Play,
  folderKanban: FolderKanban,
  bookOpen: BookOpen,
  folder: Folder,
  tag: Tag,
  messageSquare: MessageSquare,
  users: Users,
  shield: Shield,
  hardDrive: HardDrive,
  settings: Settings,
  info: Info
};

const menuItems = computed(() => {
  return adminMenuItems.map(item => {
    const mappedItem = {
      ...item,
      label: t(item.labelKey),
      icon: iconMap[item.iconKey]
    };
    if (item.children && item.children.length > 0) {
      mappedItem.children = item.children.map(child => ({
        ...child,
        label: t(child.labelKey),
        icon: iconMap[child.iconKey]
      }));
    }
    return mappedItem;
  });
});

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
      <div class="relative flex items-center gap-4">
        <button
          @click="toggleUserMenu"
          class="flex items-center gap-3 transition-colors rounded-lg px-2 py-1"
          :class="isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'"
        >
          <div class="hidden md:flex flex-col items-end">
            <span class="text-sm font-bold text-construct-red">ADMIN</span>
            <span :class="['text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ adminEmail }}</span>
          </div>
          <div :class="['w-10 h-10 rounded-full flex items-center justify-center', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
            <User :size="18" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'" />
          </div>
          <ChevronDown :size="16" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
        </button>

        <!-- User Dropdown Menu -->
        <Transition name="dropdown">
          <div
            v-if="isUserMenuOpen"
            class="absolute right-0 top-full mt-2 w-56 rounded-lg shadow-xl border z-50"
            :class="isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'"
          >
            <div :class="['p-4 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
              <p :class="['text-sm font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ adminEmail }}</p>
              <p :class="['text-xs mt-1', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Administrator</p>
            </div>
            <div class="p-2">
              <!-- Theme Toggle in Menu -->
              <div :class="['flex items-center justify-between px-3 py-2 rounded-lg', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
                <span :class="['text-sm', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ isDarkMode ? t('theme_dark') : t('theme_light') }}
                </span>
                <ThemeToggle />
              </div>
              <!-- Logout Button -->
              <button
                @click="handleLogout"
                :class="['w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors mt-1',
                  isDarkMode ? 'text-gray-300 hover:bg-gray-700 hover:text-red-400' : 'text-gray-700 hover:bg-gray-100 hover:text-red-500']"
              >
                <LogOut :size="18" />
                <span>{{ t('login_logout') }}</span>
              </button>
            </div>
          </div>
        </Transition>
      </div>
      <!-- Click outside to close menu -->
      <div
        v-if="isUserMenuOpen"
        class="fixed inset-0 z-40"
        @click="closeUserMenu"
      ></div>
    </header>
    
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed left-0 top-16 bottom-0 transition-all duration-300 z-40 overflow-hidden admin-sidebar',
        isDarkMode ? 'bg-gray-800' : 'bg-white',
        isSidebarOpen ? (isDarkMode ? 'border-r border-gray-700' : 'border-r border-gray-200') : '',
        isSidebarOpen && !isSidebarCollapsed ? 'w-64 lg:w-64' : '',
        isSidebarOpen && isSidebarCollapsed ? 'w-16 lg:w-16' : '',
        !isSidebarOpen ? 'w-0 lg:w-16' : '',
        !isSidebarOpen && 'lg:shadow-sm'
      ]"
    >
      <nav class="p-2 space-y-1 h-full flex flex-col">
        <template v-for="item in menuItems" :key="item.id">
          <!-- Parent menu item with children -->
          <div v-if="item.children && item.children.length > 0">
            <button
              @click="toggleMenu(item.id)"
              :class="[
                'w-full flex items-center gap-3 px-3 py-3 text-sm font-bold tracking-widest uppercase transition-all rounded-lg',
                item.route && isActiveRoute(item)
                  ? isSidebarCollapsed
                    ? isDarkMode
                      ? 'bg-red-500/20 text-construct-red'
                      : 'bg-red-50 text-construct-red'
                    : isDarkMode
                      ? 'bg-red-500/10 border-l-4 border-construct-red text-construct-red'
                      : 'bg-red-50 border-l-4 border-construct-red text-construct-red'
                  : isDarkMode
                    ? 'text-gray-400 hover:bg-construct-red hover:text-white'
                    : 'text-gray-600 hover:bg-construct-red hover:text-white',
                isSidebarCollapsed ? 'justify-center' : ''
              ]"
            >
              <component 
                :is="item.icon" 
                :size="isSidebarCollapsed ? 20 : 18"
                :class="[
                  item.route && isActiveRoute(item) ? '!text-construct-red' : (isDarkMode ? 'text-gray-400' : 'text-gray-600')
                ]"
              />
              <span v-if="!isSidebarCollapsed">{{ item.label }}</span>
              <ChevronDown 
                v-if="!isSidebarCollapsed" 
                :size="16" 
                :class="[
                  'ml-auto transition-transform',
                  isMenuExpanded(item.id) ? 'rotate-180' : ''
                ]"
              />
              <span 
                v-if="isSidebarCollapsed" 
                :class="[
                  'absolute left-16 px-2 py-1 text-xs rounded whitespace-nowrap opacity-0 hover:opacity-100 transition-opacity pointer-events-none z-50',
                  isDarkMode ? 'bg-gray-700 text-white' : 'bg-gray-900 text-white'
                ]"
              >
                {{ item.label }}
              </span>
            </button>
            
            <!-- Children menu items -->
            <div 
              v-show="isMenuExpanded(item.id) && !isSidebarCollapsed"
              class="ml-4 space-y-1"
            >
              <button
                v-for="child in item.children"
                :key="child.id"
                @click="navigateTo(child)"
                :class="[
                  'w-full flex items-center gap-3 px-3 py-3 text-sm font-bold tracking-widest uppercase transition-all rounded-lg',
                  isActiveRoute(child)
                    ? isSidebarCollapsed
                      ? isDarkMode
                        ? 'bg-red-500/20 text-construct-red'
                        : 'bg-red-50 text-construct-red'
                      : isDarkMode
                        ? 'bg-red-500/10 border-l-4 border-construct-red text-construct-red'
                        : 'bg-red-50 border-l-4 border-construct-red text-construct-red'
                    : isDarkMode
                      ? 'text-gray-400 hover:bg-construct-red hover:text-white'
                      : 'text-gray-600 hover:bg-construct-red hover:text-white'
                ]"
              >
                <component 
                  :is="child.icon" 
                  :size="isSidebarCollapsed ? 20 : 18"
                  :class="[
                    isActiveRoute(child) ? '!text-construct-red' : (isDarkMode ? 'text-gray-400' : 'text-gray-600')
                  ]"
                />
                <span>{{ child.label }}</span>
              </button>
            </div>
          </div>
          
          <!-- Regular menu item -->
          <button
            v-else
            @click="navigateTo(item)"
            :disabled="!item.route"
            :class="[
              'w-full flex items-center gap-3 px-3 py-3 text-sm font-bold tracking-widest uppercase transition-all rounded-lg',
              isActiveRoute(item)
                ? isSidebarCollapsed
                  ? isDarkMode
                    ? 'bg-red-500/20 text-construct-red'
                    : 'bg-red-50 text-construct-red'
                  : isDarkMode
                    ? 'bg-red-500/10 border-l-4 border-construct-red text-construct-red'
                    : 'bg-red-50 border-l-4 border-construct-red text-construct-red'
                : isDarkMode
                  ? 'text-gray-400 hover:bg-construct-red hover:text-white'
                  : 'text-gray-600 hover:bg-construct-red hover:text-white',
              !item.route && 'opacity-50 cursor-not-allowed',
              isSidebarCollapsed ? 'justify-center' : ''
            ]"
          >
            <component 
              :is="item.icon" 
              :size="isSidebarCollapsed ? 20 : 18"
              :class="[
                isActiveRoute(item) ? '!text-construct-red' : (isDarkMode ? 'text-gray-400' : 'text-gray-600')
              ]"
            />
            <span v-if="!isSidebarCollapsed">{{ item.label }}</span>
            <span 
              v-if="isSidebarCollapsed" 
              :class="[
                'absolute left-16 px-2 py-1 text-xs rounded whitespace-nowrap opacity-0 hover:opacity-100 transition-opacity pointer-events-none z-50',
                isDarkMode ? 'bg-gray-700 text-white' : 'bg-gray-900 text-white'
              ]"
            >
              {{ item.label }}
            </span>
          </button>
        </template>
        
        <!-- Spacer to push collapse button to bottom -->
        <div class="flex-1"></div>
        
        <!-- Collapse Toggle Button (hidden on mobile) -->
        <div class="hidden lg:block mt-auto pt-4">
          <button
            @click="toggleSidebarCollapse"
            :class="[
              'w-full flex items-center justify-center py-3 transition-colors rounded-lg',
              isDarkMode ? 'text-gray-400 hover:bg-gray-700/50 hover:text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
            ]"
            :title="isSidebarCollapsed ? t('admin_expand_sidebar') : t('admin_collapse_sidebar')"
          >
            <ChevronLeft v-if="!isSidebarCollapsed" :size="isSidebarCollapsed ? 20 : 18" />
            <ChevronRight v-else :size="isSidebarCollapsed ? 20 : 18" />
            <span v-if="!isSidebarCollapsed" class="ml-2 text-xs">{{ isSidebarCollapsed ? t('admin_expand') : t('admin_collapse') }}</span>
          </button>
        </div>
      </nav>
    </aside>
    
    <!-- Main Content -->
    <main
      :class="[
        'pt-16 min-h-screen transition-all duration-300 admin-content',
        isDarkMode ? 'bg-gray-900' : 'bg-gray-50',
        isSidebarOpen && !isSidebarCollapsed ? 'lg:ml-64' : 'lg:ml-16'
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
    
    <!-- Logout Confirmation Dialog -->
    <ConfirmDialog
      :visible="showLogoutConfirm"
      :title="t('login_logout')"
      :content="t('admin_delete_warning')"
      :confirm-text="t('confirm')"
      :cancel-text="t('cancel')"
      confirm-variant="primary"
      @confirm="confirmLogout"
      @cancel="showLogoutConfirm = false"
    />
  </div>
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: transform 0.2s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
  transform: scale(0.95);
}
</style>
