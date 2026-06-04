<script setup>
/**
 * Layout.vue - 管理后台布局组件
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
import { usePage, router as inertiaRouter } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import {
  LogOut,
  User,
  Menu,
  X,
  ChevronLeft,
  ChevronRight,
  ChevronDown,
  ExternalLink,
  LayoutDashboard,
  FileText,
  Play,
  FolderKanban,
  BookOpen,
  Users,
  Settings,
  Folder,
  Tag,
  MessageSquare,
  Shield,
  HardDrive,
  Info,
  Archive,
  SlidersHorizontal,
  Book,
  BookText,
  RotateCcw,
  Image,
  Zap,
  Link,
  Crown,
  Mail,
  LayoutPanelLeft,
  Languages,
  Bell
} from 'lucide-vue-next';
import ThemeToggle from '../../components/ThemeToggle.vue';
import ToastContainer from '../../components/ToastContainer.vue';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import NotificationBell from '../../components/NotificationBell.vue';
import { useTheme } from '../../composables/useTheme';
import { useMenuItems } from '../../composables/useMenuItems';
import { useToast } from '../../composables/useToast';

const page = usePage();
// 后端已过滤权限，直接使用
const adminMenuItems = computed(() => {
  const menus = page.props.menus;
  return Array.isArray(menus) ? menus : [];
});

// 从全局共享数据中读取站点配置
const siteConfig = computed(() => page.props.siteConfig || {});
const siteName = computed(() => siteConfig.value.name || 'Admin');
const siteLogo = computed(() => siteConfig.value.logo || '');

const { t: originalT } = useI18n();
const t = (key, fallback = '') => {
  if (!key) return fallback || '';
  try {
    return originalT(key) || fallback;
  } catch (e) {
    return fallback;
  }
};

// 当前登录用户信息
const currentUser = computed(() => page.props.auth?.user || {});
const adminDisplayName = computed(() => currentUser.value.name || '用户');
const adminEmail = computed(() => currentUser.value.email || '');
const adminRole = computed(() => currentUser.value.roles?.[0]?.name || 'User');
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
    expandedMenus.value.clear();
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

const handleProfile = () => {
  const slug = currentUser.value.slug;
  if (slug) {
    inertiaRouter.visit(`/admin/${slug}`);
  }
  closeUserMenu();
};

const handleLogout = () => {
  showLogoutConfirm.value = true;
  closeUserMenu();
};

const confirmLogout = () => {
  try {
    // 历史遗留的明文敏感 key
    localStorage.removeItem('admin_logged_in');
    localStorage.removeItem('admin_email');
    localStorage.removeItem('admin_login_time');
    localStorage.removeItem('admin_remembered_email');
    localStorage.removeItem('admin_remembered_password');
    localStorage.removeItem('admin_token');
    localStorage.removeItem('admin_user');
    sessionStorage.clear();
  } catch { /* noop */ }

  // 标记为手动退出，登录页据此显示通知
  try { localStorage.setItem('active_logout', Date.now().toString()); } catch { /* noop */ }

  showLogoutConfirm.value = false;
  
  inertiaRouter.post('/admin/logout', {}, {
    onSuccess: () => {
      inertiaRouter.visit('/admin/login');
    },
  });
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
  try {
    const collapsed = localStorage.getItem('sidebar_collapsed');
    if (collapsed !== null) {
      isSidebarCollapsed.value = collapsed === 'true';
      if (isSidebarCollapsed.value) {
        expandedMenus.value.clear();
      }
    }
  } catch { /* noop */ }
  
  handleResize();
  window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
  if (leaveTimeout) {
    clearTimeout(leaveTimeout);
    leaveTimeout = null;
  }
});

const toggleSidebarCollapse = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
  try { localStorage.setItem('sidebar_collapsed', isSidebarCollapsed.value.toString()); } catch { /* noop */ }
  if (isSidebarCollapsed.value) {
    expandedMenus.value.clear();
  }
};

const iconMap = {
  LayoutDashboard,
  FileText,
  Play,
  FolderKanban,
  BookOpen,
  BookText,
  Users,
  Settings,
  Folder,
  Tag,
  MessageSquare,
  Shield,
  HardDrive,
  Info,
  Archive,
  SlidersHorizontal,
  Book,
  RotateCcw,
  Image,
  Zap,
  Link,
  Menu,
  Crown,
  Mail,
  LayoutPanelLeft,
  Languages,
  Bell
};

const menuItems = computed(() => {
  return adminMenuItems.value.map(item => {
    const mappedItem = {
      ...item,
      label: (item.label_key ? t(item.label_key) : item.label_key) || String(item.label_key || item.id),
      icon: iconMap[item.icon_name]
    };
    if (item.children && item.children.length > 0) {
      mappedItem.children = item.children.map(child => ({
        ...child,
        label: (child.label_key ? t(child.label_key) : child.label_key) || String(child.label_key || child.id),
        icon: iconMap[child.icon_name]
      }));
    }
    return mappedItem;
  });
});

const isActiveRoute = (item) => {
  if (!item.path) return false;
  return page.url.startsWith(item.path);
};

const navigateTo = (item) => {
  if (item.path) {
    inertiaRouter.visit(item.path);
  }
};

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const getMenuTopPosition = (menuId) => {
  const menuIndex = menuItems.value.findIndex(item => item.id === menuId);
  const baseTop = 72; // header(64) + nav padding(8) = first menu item top
  const menuItemHeight = 48; // py-3(24) + content height + spacing
  
  return baseTop + menuIndex * menuItemHeight;
};

let leaveTimeout = null;

const handleMouseEnter = (menuId) => {
  if (leaveTimeout) {
    clearTimeout(leaveTimeout);
    leaveTimeout = null;
  }
  if (isSidebarCollapsed.value) {
    // 先关闭所有其他展开的菜单，再打开当前菜单
    if (!expandedMenus.value.has(menuId)) {
      expandedMenus.value.clear();
    }
    expandedMenus.value.add(menuId);
  }
};

const handleMouseLeave = (menuId) => {
  if (isSidebarCollapsed.value) {
    if (leaveTimeout) {
      clearTimeout(leaveTimeout);
    }
    leaveTimeout = setTimeout(() => {
      expandedMenus.value.delete(menuId);
      leaveTimeout = null;
    }, 250);
  }
};

const clearCache = () => {
  try {
    localStorage.removeItem('accent_theme');
    localStorage.removeItem('admin_theme');
    sessionStorage.clear();
  } catch { /* noop */ }
  location.reload();
};

// ========== 闲置自动退出（稳健版：时间戳 + 单 setInterval） ==========
const IDLE_TIMEOUT = 1440; // 24 分钟无操作自动退出（单位：秒）
const WARNING_THRESHOLD = 30; // 提前 30 秒警告
const idleCountdown = ref(IDLE_TIMEOUT);
const isIdleWarning = ref(false);
let lastActivity = Date.now();  // 最后活动时间戳
let idleCheckInterval = null;   // 唯一的定时器
const { warning, toasts } = useToast();

// 活动事件处理：仅更新时间戳
const onUserActivity = () => {
  lastActivity = Date.now();
};

// 每秒检查一次闲置时间
const idleCheck = () => {
  const elapsed = Math.floor((Date.now() - lastActivity) / 1000);
  const remaining = IDLE_TIMEOUT - elapsed;

  if (remaining <= 0) {
    // 超时，立即退出
    performAutoLogout();
    return;
  }

  idleCountdown.value = remaining;
  isIdleWarning.value = remaining <= WARNING_THRESHOLD;

  // 倒计时 30 秒时弹出警告提示
  if (remaining === WARNING_THRESHOLD) {
    warning('闲置超时提醒', `您已 ${Math.floor(elapsed / 60)} 分钟未操作，${WARNING_THRESHOLD} 秒后将自动退出登录`);
  }
};

const performAutoLogout = () => {
  // 停止定时器
  if (idleCheckInterval) {
    clearInterval(idleCheckInterval);
    idleCheckInterval = null;
  }

  // 清空所有 toast，避免跳转后残留
  toasts.value = [];

  // 标记为闲置超时退出，登录页据此显示通知
  try { localStorage.setItem('active_logout', Date.now().toString()); } catch { /* noop */ }

  // 清理所有认证相关的 localStorage/sessionStorage 数据
  try {
    localStorage.removeItem('admin_logged_in');
    localStorage.removeItem('admin_email');
    localStorage.removeItem('admin_login_time');
    localStorage.removeItem('admin_remembered_email');
    localStorage.removeItem('admin_remembered_password');
    localStorage.removeItem('admin_token');
    localStorage.removeItem('admin_user');
    sessionStorage.clear();
  } catch { /* noop */ }

  inertiaRouter.post('/admin/logout', {}, {
    onSuccess: () => {
      inertiaRouter.visit('/admin/login');
    },
  });
};

const idleEvents = ['mousemove', 'mousedown', 'click', 'keydown', 'scroll', 'touchstart'];

const startIdleDetection = () => {
  lastActivity = Date.now();
  idleCountdown.value = IDLE_TIMEOUT;
  isIdleWarning.value = false;

  idleEvents.forEach(event => {
    document.addEventListener(event, onUserActivity);
  });

  idleCheckInterval = setInterval(idleCheck, 1000);
};

const stopIdleDetection = () => {
  if (idleCheckInterval) {
    clearInterval(idleCheckInterval);
    idleCheckInterval = null;
  }
  idleEvents.forEach(event => {
    document.removeEventListener(event, onUserActivity);
  });
};

onMounted(() => {
  startIdleDetection();
});

onUnmounted(() => {
  stopIdleDetection();
});
</script>

<template>
  <div :class="['min-h-screen transition-colors', isDarkMode ? 'bg-gray-900 text-white' : 'bg-white text-gray-900 admin-layout']">
    <!-- Header -->
    <header :class="['fixed top-0 right-0 h-16 flex items-center justify-between px-6 z-50 transition-all duration-300', isDarkMode ? 'bg-gray-800' : 'bg-white admin-header', isSidebarOpen && !isSidebarCollapsed ? 'lg:left-56' : 'lg:left-16']">
      <div class="flex items-center gap-4">
        <button
          @click="toggleSidebar"
          :class="['p-2 transition-colors lg:hidden', isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100']"
        >
          <Menu v-if="!isSidebarOpen" size="24" />
          <X v-else size="24" />
        </button>
      </div>
      
      <!-- Actions -->
      <div class="relative flex items-center gap-1 sm:gap-2">
        <!-- Visit Website -->
        <a 
          href="/" 
          target="_blank"
          :class="['p-2 rounded-lg transition-colors group', isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100']"
          :title="t('login_return_site')"
        >
          <ExternalLink size="20" class="group-hover:scale-110 transition-transform" />
        </a>

        <!-- Theme Toggle -->
        <ThemeToggle />

        <!-- Notification Bell -->
        <NotificationBell />

        <!-- Clear Cache -->
        <button
          @click="clearCache"
          :class="['p-2 rounded-lg transition-colors group', isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100']"
          :title="'清理缓存'"
        >
          <Zap size="20" class="group-hover:scale-110 transition-transform" />
        </button>

        <!-- Idle Countdown Indicator -->
        <span
          v-if="idleCountdown <= 15 && idleCountdown > 0"
          :class="['text-xs font-bold px-2 py-1 rounded transition-all animate-pulse',
            idleCountdown <= 5
              ? 'bg-red-500/15 text-red-400'
              : 'bg-yellow-500/10 text-yellow-400']"
        >
          {{ idleCountdown }}s
        </span>
      </div>
    </header>
    
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed left-0 top-0 bottom-0 transition-all duration-300 z-40 admin-sidebar overflow-visible',
        isDarkMode ? 'bg-gray-800' : 'bg-white',
        isSidebarOpen && !isSidebarCollapsed ? 'w-56 lg:w-56' : '',
        isSidebarOpen && isSidebarCollapsed ? 'w-16 lg:w-16' : '',
        !isSidebarOpen ? 'w-0 lg:w-16' : ''
      ]"
    >
      <div class="flex flex-col h-full">
        <!-- Logo at top-left -->
        <div class="p-2 pt-4">
          <div class="flex items-center gap-2 px-2 py-2">
            <!-- Logo area: collapsed时 hover 显示展开图标 -->
            <div
              class="relative w-8 h-8 flex-shrink-0 cursor-pointer group/logo"
              @click="isSidebarCollapsed ? toggleSidebarCollapse() : null"
            >
              <img
                v-if="siteLogo"
                :src="siteLogo"
                :alt="siteName"
                class="w-8 h-8 object-contain transition-opacity group-hover/logo:opacity-20"
              />
              <div
                v-else
                class="w-8 h-8 bg-construct-red flex items-center justify-center transition-opacity group-hover/logo:opacity-20"
              >
                <span class="text-sm font-bold tracking-tight text-white">{{ siteName.charAt(0) }}</span>
              </div>
              <!-- Expand icon: only visible on hover when collapsed -->
              <div
                v-if="isSidebarCollapsed"
                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover/logo:opacity-100 transition-opacity"
                :class="isDarkMode ? 'text-white' : 'text-gray-600'"
              >
                <ChevronRight :size="18" />
              </div>
            </div>

            <!-- Site name (hidden when collapsed) -->
            <span v-if="!isSidebarCollapsed" class="font-display text-sm tracking-tighter truncate flex-1">{{ siteName }}</span>

            <!-- Collapse button (hidden when collapsed or on mobile) -->
            <button
              v-if="!isSidebarCollapsed"
              @click="toggleSidebarCollapse"
              class="hidden lg:block ml-auto p-1 rounded transition-colors flex-shrink-0"
              :class="isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100'"
              :title="t('admin_collapse_sidebar')"
            >
              <ChevronLeft :size="16" />
            </button>
          </div>
        </div>
        
        <nav class="p-2 space-y-1 flex-1 overflow-y-auto">
          <template v-for="item in menuItems" :key="item.id">
            <!-- Parent menu item with children -->
            <div 
              v-if="item.children && item.children.length > 0" 
              class="relative"
              @mouseenter="handleMouseEnter(item.id)"
              @mouseleave="handleMouseLeave(item.id)"
            >
            <button
              @click="toggleMenu(item.id)"
              :class="[
                'group w-full flex items-center gap-3 px-3 py-3 text-sm font-bold tracking-widest uppercase transition-all rounded-lg',
                item.path && isActiveRoute(item)
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
                  item.path && isActiveRoute(item) ? '!text-construct-red' : (isDarkMode ? 'text-gray-400' : 'text-gray-600')
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
                  'absolute left-16 px-2 py-1 text-xs rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50',
                  isDarkMode ? 'bg-gray-700 text-white' : 'bg-white text-gray-900 shadow-lg ring-1 ring-gray-200'
                ]"
              >
                {{ item.label }}
              </span>
            </button>
            
            <!-- Children menu items - expanded mode -->
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
            :disabled="!item.path"
            :class="[
              'group w-full flex items-center gap-3 px-3 py-3 text-sm font-bold tracking-widest uppercase transition-all rounded-lg',
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
              !item.path && 'opacity-50 cursor-not-allowed',
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
            <span v-if="!isSidebarCollapsed" class="truncate flex-1 text-left">{{ item.label }}</span>
            <span 
              v-if="isSidebarCollapsed" 
              :class="[
                'absolute left-16 px-2 py-1 text-xs rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50',
                isDarkMode ? 'bg-gray-700 text-white' : 'bg-white text-gray-900 shadow-lg ring-1 ring-gray-200'
              ]"
            >
              {{ item.label }}
            </span>
          </button>
        </template>
        
        </nav>
        
        <!-- Sidebar Footer: User Info -->
        <div class="p-2">
          <!-- User Info -->
          <div 
            @click="toggleUserMenu"
            :class="[
              'flex items-center gap-2 px-2 py-2 rounded-lg cursor-pointer transition-colors',
              isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'
            ]"
          >
            <div :class="['w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
              <img v-if="currentUser.avatar" :src="currentUser.avatar" alt="" class="w-full h-full object-cover" />
              <User v-else :size="16" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'" />
            </div>
            <div v-if="!isSidebarCollapsed" class="flex-1 min-w-0">
              <p :class="['text-xs font-bold truncate', isDarkMode ? 'text-white' : 'text-gray-900']">{{ adminDisplayName }}</p>
              <p :class="['text-xs truncate', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ adminEmail }}</p>
            </div>
          </div>
          
          <!-- User Dropdown Menu (moved outside aside to avoid stacking context issues) -->
        </div>
        

      </div>
    </aside>
    
    <!-- User Dropdown Menu (fixed position outside aside to avoid stacking context issues) -->
    <Transition name="dropdown">
      <div
        v-if="isUserMenuOpen"
        :class="[
          'fixed w-48 rounded-xl shadow-xl ring-1 z-[100]',
          isDarkMode ? 'bg-gray-800 ring-gray-700' : 'bg-white ring-gray-200'
        ]"
        style="bottom: 80px; left: 8px;"
      >
        <div :class="['p-4']">
          <p :class="['text-sm font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ adminDisplayName }}</p>
          <p :class="['text-xs mt-1', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ adminEmail }}</p>
        </div>
        <div class="p-2">
          <!-- Profile Button -->
          <button
            @click.stop.prevent="handleProfile"
            :class="['w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm transition-colors',
              isDarkMode ? 'text-gray-300 hover:bg-gray-700 hover:text-white' : 'text-gray-700 hover:bg-gray-100']"
          >
            <User :size="18" />
            <span>{{ t('admin_user_form_profile') }}</span>
          </button>
          
          <!-- Logout Button -->
          <button
            id="logout-btn"
            @click.stop.prevent="handleLogout"
            :class="['w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm transition-colors mt-1 cursor-pointer',
              isDarkMode ? 'text-gray-300 hover:bg-gray-700 hover:text-red-400' : 'text-gray-700 hover:bg-gray-100 hover:text-red-500']"
          >
            <LogOut :size="18" />
            <span>{{ t('login_logout') }}</span>
          </button>
        </div>
      </div>
    </Transition>
    
    <!-- Click outside to close user menu -->
    <div
      v-if="isUserMenuOpen"
      class="fixed inset-0 z-[90]"
      @click="closeUserMenu"
    ></div>
    
    <!-- Collapsed Sidebar Floating Menu -->
    <div class="hidden lg:block">
      <template v-for="item in menuItems" :key="'float-' + item.id">
        <div 
          v-if="item.children && item.children.length > 0"
          v-show="isSidebarCollapsed && isMenuExpanded(item.id)"
          :class="[
            'fixed w-48 py-2 rounded-lg shadow-xl z-[100]',
            isDarkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'
          ]"
          :style="{ top: getMenuTopPosition(item.id) + 'px', left: '4rem' }"
          @mouseenter="handleMouseEnter(item.id)"
          @mouseleave="handleMouseLeave(item.id)"
        >
          <!-- Parent menu title
          <div :class="['px-4 py-3 text-sm font-bold tracking-widest uppercase border-b', isDarkMode ? 'border-gray-700 text-gray-400' : 'border-gray-200 text-gray-500']">
            {{ item.label }}
          </div> -->
          <!-- Child menu items -->
          <button
            v-for="child in item.children"
            :key="child.id"
            @click="navigateTo(child)"
            :class="[
              'w-full flex items-center gap-3 px-4 py-2 text-sm font-bold tracking-widest uppercase transition-all',
              isActiveRoute(child)
                ? isDarkMode
                  ? 'bg-red-500/10 text-construct-red'
                  : 'bg-red-50 text-construct-red'
                : isDarkMode
                  ? 'text-gray-300 hover:bg-gray-700'
                  : 'text-gray-700 hover:bg-gray-100'
            ]"
          >
            <component 
              :is="child.icon" 
              :size="16"
              :class="[
                isActiveRoute(child) ? '!text-construct-red' : (isDarkMode ? 'text-gray-400' : 'text-gray-600')
              ]"
            />
            <span>{{ child.label }}</span>
          </button>
        </div>
      </template>
    </div>
    
    <!-- Main Content -->
    <main
      :class="[
        'pt-16 min-h-screen transition-all duration-300 admin-content',
        isDarkMode ? 'bg-gray-900' : 'bg-gray-50',
        isSidebarOpen && !isSidebarCollapsed ? 'lg:ml-56' : 'lg:ml-16'
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
      :cancel-text="t('admin_cancel')"
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
