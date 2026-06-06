<script setup>
/**
 * FrontMenu.vue - 菜单节点管理页面
 * 
 * 功能说明：
 * - 管理前台和后台导航栏菜单项 (节点管理)
 * - 支持切换前台菜单与后台菜单视图
 * - 编辑菜单标签 (label_key) 和 路径 (path/route)
 * - 支持拖拽排序、禁用状态切换、搜索过滤
 */
import {
  ref,
  computed,
  watch,
  useI18n,
  useTheme,
  Plus,
  Trash2,
  GripVertical,
  ExternalLink,
  Save,
  RotateCcw,
  Pagination,
  ConfirmDialog,
  EmptyState,
  Info,
  useToast,
  Monitor,
  LayoutDashboard,
  Edit3,
  X,
  Eye,
  EyeOff,
  Search,
  Filter,
  Folder,
  SearchFilterModal
} from '../../composables/useAdminImports';
import { useMenuItems } from '../../composables/useMenuItems';
import { useDragSort } from '../../composables/useDragSort';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  allMenus: { type: Array, default: () => [] },
});

// 获取所有菜单（包括非活跃的）用于管理
const { frontMenuItems: frontMenuItemsAll } = useMenuItems({ menus: props.allMenus, includeInactive: true });
const { adminMenuItems: adminMenuItemsAll } = useMenuItems({ menus: props.allMenus, includeInactive: true });

const { t: originalT } = useI18n();
const t = (key, fallback = '') => {
  if (!key) return fallback || '';
  try {
    return originalT(key) || fallback;
  } catch (e) {
    return fallback;
  }
};
const { isDarkMode } = useTheme();
const { success, error } = useToast();

// 辅助函数：生成临时 ID
const generateTempId = () => `temp_${Date.now()}_${Math.floor(Math.random() * 1000)}`;

// 辅助函数：转换菜单项以便保存
const mapMenuId = (item) => ({
  ...item,
  id: (typeof item.id === 'string' && item.id.startsWith('temp_')) ? null : item.id,
  parent_id: (typeof item.parent_id === 'string' && item.parent_id.startsWith('temp_')) ? null : item.parent_id
});

// 将树形菜单展平为列表
const flattenAdminMenu = (menuTree) => {
  const result = [];
  const flatten = (items, level = 0) => {
    items.forEach(item => {
      result.push({ ...item, _level: level });
      if (item.children && item.children.length > 0) {
        flatten(item.children, level + 1);
      }
    });
  };
  flatten(menuTree);
  return result;
};

const currentTab = ref('front'); // 'front' or 'admin'
const frontMenuList = ref([...frontMenuItemsAll]);
const adminMenuList = ref([...adminMenuItemsAll]);
const searchQuery = ref('');
const showInactive = ref(true);

// 用于拖拽的扁平化 admin 菜单列表 - 初始化时就计算
const flattenedAdminMenuList = ref(flattenAdminMenu([...adminMenuItemsAll]));

watch(() => props.allMenus, (newMenus) => {
  // 使用 includeInactive 获取所有菜单，包括非活跃的
  const { frontMenuItems: newFrontAll } = useMenuItems({ menus: newMenus, includeInactive: true });
  const { adminMenuItems: newAdminAll } = useMenuItems({ menus: newMenus, includeInactive: true });
  frontMenuList.value = [...newFrontAll];
  adminMenuList.value = [...newAdminAll];
  // 同步扁平化列表
  flattenedAdminMenuList.value = flattenAdminMenu(newAdminAll);
}, { deep: true });

const flattenedAdminMenu = computed(() => {
  let items = [...flattenedAdminMenuList.value];
  
  // 搜索过滤
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    items = items.filter(item => 
      (item.label_key && item.label_key.toLowerCase().includes(query)) || 
      (item.label_key && t(item.label_key).toLowerCase().includes(query)) ||
      (item.path && item.path.toLowerCase().includes(query))
    );
  }
  
  // 状态过滤
  if (!showInactive.value) {
    items = items.filter(item => item.is_active !== false);
  }
  
  return items;
});

const filteredFrontMenu = computed(() => {
  let items = frontMenuList.value;
  
  // 搜索过滤
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    items = items.filter(item => 
      (item.label_key && item.label_key.toLowerCase().includes(query)) || 
      (item.label_key && t(item.label_key).toLowerCase().includes(query)) ||
      (item.path && item.path.toLowerCase().includes(query))
    );
  }
  
  // 状态过滤
  if (!showInactive.value) {
    items = items.filter(item => item.is_active !== false);
  }
  
  return items;
});

const isSaving = ref(false);
const showSaveConfirm = ref(false);
const deleteConfirm = ref({ visible: false, item: null });

const handleAdd = () => {
  const newId = generateTempId();
  
  if (currentTab.value === 'front') {
    frontMenuList.value.push({
      id: newId,
      type: 'front',
      parent_id: null,
      label_key: 'nav_new_item',
      path: '/new',
      sort_order: frontMenuList.value.length + 1,
      is_active: true
    });
  } else {
    flattenedAdminMenuList.value.push({
      id: newId,
      type: 'admin',
      parent_id: null,
      label_key: 'admin_new_item',
      icon_name: 'FileText',
      path: '/admin/new',
      sort_order: flattenedAdminMenuList.value.length + 1,
      is_active: true,
      _level: 0
    });
  }
};

const handleDelete = (item) => {
  deleteConfirm.value = { visible: true, item };
};

const confirmDelete = () => {
  const item = deleteConfirm.value.item;
  
  const performLocalDelete = () => {
    if (currentTab.value === 'front') {
      frontMenuList.value = frontMenuList.value.filter(l => l.id !== item.id);
    } else {
      flattenedAdminMenuList.value = flattenedAdminMenuList.value.filter(l => l.id !== item.id);
    }
  };

  if (typeof item.id === 'string' && item.id.startsWith('temp_')) {
    performLocalDelete();
    success(t('admin_delete') + ' ' + t('confirm'));
  } else {
    router.delete(route('admin.front-menu.destroy', item.id), {
      preserveState: true,
      onSuccess: () => {
        performLocalDelete();
        success(t('admin_delete') + ' ' + t('confirm'));
      }
    });
  }
  deleteConfirm.value = { visible: false, item: null };
};

const handleSave = () => {
  showSaveConfirm.value = true;
};

const confirmSave = () => {
  showSaveConfirm.value = false;
  isSaving.value = true;
  
  // 收集所有修改（包括前台和后台），避免保存一个 tab 丢失另一个 tab 的未保存更改
  const allData = [
    ...frontMenuList.value.map(item => ({ ...mapMenuId(item), type: 'front' })),
    ...flattenedAdminMenuList.value.map(item => ({ ...mapMenuId(item), type: 'admin' }))
  ];

  // 验证必填项
  const invalidItems = allData.filter(item => {
    const hasLabel = item.label_key?.trim();
    const hasPath = item.path?.trim();
    const isParentMenu = !item.parent_id;
    return !hasLabel || (!hasPath && !isParentMenu);
  });
  
  if (invalidItems.length > 0) {
    isSaving.value = false;
    error(t('admin_error_empty_fields') || '请填写所有必填字段');
    return;
  }
  
  router.post(route('admin.front-menu.batch-update'), { menus: allData }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      isSaving.value = false;
      success(t('admin_save') + ' ' + t('confirm'));
    },
    onError: (errors) => {
      isSaving.value = false;
      console.error('Batch save error:', errors);
      error('保存失败');
    },
  });
};

const handleReset = () => {
  if (currentTab.value === 'front') {
    frontMenuList.value = [...frontMenuItemsAll];
  } else {
    adminMenuList.value = [...adminMenuItemsAll];
  }
};

// Use drag sort composable
const { handleDragStart, handleDragOver, handleDragEnd } = useDragSort({
  batchUpdateUrl: route('admin.front-menu.batch-update'),
  onUpdateSuccess: () => success(t('admin_save') + ' ' + t('confirm')),
  onUpdateError: (err) => {
    console.error('FrontMenu sort update error:', err);
    error('Failed to update sort order');
  },
  debounceDelay: 800,
  mapItem: mapMenuId
});

// 切换禁用状态
const toggleActive = (item) => {
  item.is_active = !item.is_active;
};

// 获取可用图标列表（与 Layout.vue 中的 iconMap 保持同步）
const availableIcons = [
  // 通用图标
  'LayoutDashboard', 'Home', 'Search', 'Bell', 'Settings', 'HelpCircle', 'Info',
  
  // 文件与文档
  'FileText', 'File', 'FileImage', 'FileCode', 'FileJson', 'Book', 'BookOpen', 
  'Archive', 'Folder', 'FolderOpen', 'FolderKanban',
  
  // 用户与权限
  'Users', 'User', 'UserPlus', 'UserMinus', 'Shield', 'ShieldCheck', 'Lock', 'Key',
  
  // 内容管理
  'Play', 'Image', 'Video', 'Music', 'Camera', 'Bookmark', 'Tag',
  
  // 数据与图表
  'BarChart3', 'PieChart', 'Activity', 'Database', 'Server', 'HardDrive',
  
  // 导航与链接
  'Link', 'ExternalLink', 'Navigation', 'Compass', 'MapPin', 'Globe',
  
  // 媒体与通信
  'MessageSquare', 'MessageCircle', 'Mail', 'Phone', 'Send',
  
  // 系统工具
  'SlidersHorizontal', 'RotateCcw', 'RefreshCw', 'Download', 'Upload',
  
  // 状态与反馈
  'CheckCircle2', 'XCircle', 'AlertCircle', 'AlertTriangle', 'Check', 'X',
  
  // 界面元素
  'Menu', 'Grid3X3', 'List', 'Layers', 'ChevronDown', 'ChevronLeft', 'ChevronUp',
  
  // 动作与操作
  'Plus', 'Trash2', 'Edit3', 'Eye', 'EyeOff', 'Star', 'Heart',
  
  // 商业与财务
  'Briefcase', 'Wallet', 'CreditCard', 'DollarSign', 'Receipt',
  
  // 时间与日期
  'Calendar', 'CalendarDays', 'Clock', 'Timer', 'History',
  
  // 社交与互动
  'ThumbsUp', 'Comment',
  
  // 其他
  'Zap', 'Lightbulb', 'Award', 'Trophy', 'Gift', 'Package', 'ShoppingCart'
];

// 获取父级菜单选项（用于二级菜单）
const parentMenuOptions = computed(() => {
  if (currentTab.value !== 'admin') return [];
  
  return flattenedAdminMenuList.value
    .filter(item => !item.parent_id)
    .map(item => ({
      id: item.id,
      label: (item.label_key ? t(item.label_key) : item.label_key) || String(item.id)
    }));
});

const handleFilterChange = ({ key, value }) => {
  if (key === 'showInactive') {
    showInactive.value = value;
  }
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <Folder :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="32" />
          <div>
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_menu_management') }}</h2>
            <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_menu_management_subtitle') }}</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <button 
            @click="handleReset"
            :class="['px-6 py-3 border flex items-center gap-2 font-bold text-xs tracking-wider uppercase transition-colors rounded-xl', isDarkMode ? 'border-gray-700 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-100']"
          >
            <RotateCcw size="16" /> {{ t('admin_reset') }}
          </button>
          <button 
            @click="handleSave"
            :disabled="isSaving"
            class="flex items-center gap-2 px-8 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-700 transition-colors rounded-xl shadow-sm disabled:opacity-50"
          >
            <Save size="16" class="!text-white" /> {{ isSaving ? t('admin_save') + '...' : t('admin_save') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Tabs UI -->
    <div class="mb-6 flex gap-2 p-1 border rounded-lg w-fit" :class="isDarkMode ? 'border-gray-700 bg-gray-900/50' : 'border-gray-200 bg-gray-100'">
      <button 
        @click="currentTab = 'front'"
        :class="[
          'px-6 py-2 rounded-md text-xs font-black tracking-widest uppercase transition-all flex items-center gap-2',
          currentTab === 'front' 
            ? 'bg-construct-red text-white shadow-lg' 
            : (isDarkMode ? 'text-gray-500 hover:text-gray-300' : 'text-gray-500 hover:text-gray-700')
        ]"
      >
        <Monitor size="14" /> {{ t('admin_front_menu_tab') || '前台菜单' }}
      </button>
      <button 
        @click="currentTab = 'admin'"
        :class="[
          'px-6 py-2 rounded-md text-xs font-black tracking-widest uppercase transition-all flex items-center gap-2',
          currentTab === 'admin' 
            ? 'bg-construct-red text-white shadow-lg' 
            : (isDarkMode ? 'text-gray-500 hover:text-gray-300' : 'text-gray-500 hover:text-gray-700')
        ]"
      >
        <LayoutDashboard size="14" /> {{ t('admin_backend_menu_tab') || '后台菜单' }}
      </button>
    </div>

    <!-- Search & Filter Bar -->
    <div class="-mb-10">
      <SearchFilterModal
        v-model:search-query="searchQuery"
        :search-placeholder="t('admin_search_menu') || '搜索菜单...'"
        :filters="[
          {
            key: 'showInactive',
            type: 'checkbox',
            checkedLabel: t('admin_show_inactive') || '显示禁用',
            uncheckedLabel: t('admin_hide_inactive') || '隐藏禁用'
          }
        ]"
        :filter-values="{ showInactive: showInactive }"
        @filter-change="handleFilterChange"
      />
    </div>

    <!-- Help Notice -->
    <div :class="['mt-16 mb-6 p-5 border rounded-xl flex items-start gap-4', isDarkMode ? 'bg-blue-900/20 border-blue-800/50' : 'bg-blue-50/80 border-blue-100']">
      <div :class="['p-2 rounded-lg text-white shadow-sm shrink-0', isDarkMode ? 'bg-blue-600' : 'bg-blue-500']">
        <Info size="18" />
      </div>
      <div>
        <h4 :class="['font-bold mb-1.5 text-sm', isDarkMode ? 'text-blue-300' : 'text-blue-900']">
          {{ t('admin_configuration_note') || '配置提示' }}
        </h4>
        <p :class="['text-sm leading-relaxed', isDarkMode ? 'text-blue-400/90' : 'text-blue-700/90']">
          {{ t('admin_menu_help_text') || '菜单标签使用语言文件中的翻译键。确保你输入的' }} 
          <code :class="['px-1.5 py-0.5 rounded text-xs font-bold', isDarkMode ? 'bg-blue-800/60 text-blue-300' : 'bg-blue-100 text-blue-800']">label_key</code> 
          {{ t('admin_menu_help_text_2') || '存在于' }} 
          <code :class="['px-1.5 py-0.5 rounded text-xs font-bold', isDarkMode ? 'bg-blue-800/60 text-blue-300' : 'bg-blue-100 text-blue-800']">zh.json</code>、
          <code :class="['px-1.5 py-0.5 rounded text-xs font-bold', isDarkMode ? 'bg-blue-800/60 text-blue-300' : 'bg-blue-100 text-blue-800']">en.json</code> 
          {{ t('admin_menu_help_text_3') || '等语言文件中，以显示正确的多语言文本。' }}
        </p>
      </div>
    </div>

    <!-- Menu List Table -->
    <div :class="['border rounded-lg overflow-hidden', isDarkMode ? 'border-gray-700 bg-gray-800' : 'border-gray-200 bg-white']">
      <table class="w-full text-left table-fixed">
        <thead :class="['border-b', isDarkMode ? 'border-gray-700 bg-gray-900/50 text-gray-400' : 'border-gray-200 bg-gray-50 text-gray-500']">
          <tr>
            <th class="px-4 py-3 font-bold uppercase text-[10px] tracking-widest w-12 text-center">Order</th>
            <th class="px-4 py-3 font-bold uppercase text-[10px] tracking-widest w-8 text-center">Status</th>
            <th class="px-4 py-3 font-bold uppercase text-[10px] tracking-widest w-1/4">Label Key</th>
            <th class="px-4 py-3 font-bold uppercase text-[10px] tracking-widest w-1/5">Display Text</th>
            <th class="px-4 py-3 font-bold uppercase text-[10px] tracking-widest w-1/5">{{ currentTab === 'front' ? 'Path' : 'Route' }}</th>
            <th v-if="currentTab === 'admin'" class="px-4 py-3 font-bold uppercase text-[10px] tracking-widest w-20">Icon</th>
            <th v-if="currentTab === 'admin'" class="px-4 py-3 font-bold uppercase text-[10px] tracking-widest w-20">Parent</th>
            <th class="px-4 py-3 font-bold uppercase text-[10px] tracking-widest w-24 text-right">Actions</th>
          </tr>
        </thead>
        <tbody :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
          <template v-if="currentTab === 'front'">
            <tr 
              v-for="(item, index) in filteredFrontMenu" 
              :key="item.id"
              :class="[
                'border-b transition-colors cursor-move', 
                isDarkMode ? 'border-gray-700 hover:bg-gray-700/30' : 'border-gray-100 hover:bg-gray-50/50',
                !item.is_active ? 'opacity-50' : ''
              ]"
              draggable="true"
              @dragstart="handleDragStart($event, item)"
              @dragover="(e) => handleDragOver(e, item, frontMenuList)"
              @dragend="() => handleDragEnd(frontMenuList)"
            >
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-2">
                  <GripVertical :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="16" />
                  <span class="text-xs font-bold w-4 text-center">{{ index + 1 }}</span>
                </div>
              </td>
              <td class="px-4 py-3 text-center">
                <button 
                  @click="toggleActive(item)"
                  :class="[
                    'p-2 rounded-lg transition-colors hover:scale-110',
                    item.is_active 
                      ? (isDarkMode ? 'bg-green-500/20 text-green-400' : 'bg-green-50 text-green-600')
                      : (isDarkMode ? 'bg-red-500/20 text-red-400' : 'bg-red-50 text-red-600')
                  ]"
                >
                  <Eye v-if="item.is_active" size="14" />
                  <EyeOff v-else size="14" />
                </button>
              </td>
              <td class="px-4 py-3">
                <input 
                  v-model="item.label_key"
                  type="text"
                  :class="[
                    'w-full px-3 py-1.5 border rounded-lg font-mono text-xs focus:border-construct-red focus:ring-1 focus:ring-construct-red/20 focus:outline-none transition-all',
                    isDarkMode ? 'bg-gray-900/50 border-gray-700 text-white placeholder-gray-600' : 'bg-gray-50/50 border-gray-200 text-gray-900 placeholder-gray-400 hover:border-gray-300'
                  ]"
                  :placeholder="t('admin_label_key_placeholder') || 'e.g. nav_home'"
                />
              </td>
              <td class="px-4 py-3">
                <span class="text-sm font-bold truncate block" :title="t(item.label_key)">
                  {{ t(item.label_key) || '(Empty)' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <input 
                    v-model="item.path"
                    type="text"
                    :class="[
                      'flex-1 px-3 py-1.5 border rounded-lg font-mono text-xs focus:border-construct-red focus:ring-1 focus:ring-construct-red/20 focus:outline-none transition-all',
                      isDarkMode ? 'bg-gray-900/50 border-gray-700 text-white placeholder-gray-600' : 'bg-gray-50/50 border-gray-200 text-gray-900 placeholder-gray-400 hover:border-gray-300'
                    ]"
                    placeholder="/"
                  />
                  <a 
                    v-if="item.path"
                    :href="item.path"
                    target="_blank"
                    class="p-1 text-gray-400 hover:text-construct-red transition-colors shrink-0"
                  >
                    <ExternalLink size="14" />
                  </a>
                </div>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button 
                    @click="handleDelete(item)"
                    class="p-2 text-gray-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20"
                  >
                    <Trash2 size="16" />
                  </button>
                </div>
              </td>
            </tr>
          </template>
          <template v-else>
            <tr 
              v-for="(item, index) in flattenedAdminMenu" 
              :key="item.id"
              :class="[
                'border-b transition-colors cursor-move', 
                isDarkMode ? 'border-gray-700 hover:bg-gray-700/30' : 'border-gray-100 hover:bg-gray-50/50',
                !item.is_active ? 'opacity-50' : ''
              ]"
              draggable="true"
              @dragstart="handleDragStart($event, item)"
              @dragover="(e) => handleDragOver(e, item, flattenedAdminMenuList)"
              @dragend="() => handleDragEnd(flattenedAdminMenuList)"
            >
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-2">
                  <GripVertical :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="16" />
                  <span class="text-xs font-bold w-4 text-center">{{ index + 1 }}</span>
                </div>
              </td>
              <td class="px-4 py-3 text-center">
                <button 
                  @click="toggleActive(item)"
                  :class="[
                    'p-2 rounded-lg transition-colors hover:scale-110',
                    item.is_active 
                      ? (isDarkMode ? 'bg-green-500/20 text-green-400' : 'bg-green-50 text-green-600')
                      : (isDarkMode ? 'bg-red-500/20 text-red-400' : 'bg-red-50 text-red-600')
                  ]"
                >
                  <Eye v-if="item.is_active" size="14" />
                  <EyeOff v-else size="14" />
                </button>
              </td>
              <td class="px-4 py-3" :style="{ paddingLeft: `${item._level * 20 + 16}px` }">
                <div class="flex items-center gap-2">
                  <span v-if="item._level > 0" :class="['text-xs font-bold', isDarkMode ? 'text-gray-500' : 'text-gray-400']">└─</span>
                  <input 
                    v-model="item.label_key"
                    type="text"
                    :class="[
                      'flex-1 px-3 py-1.5 border rounded-lg font-mono text-xs focus:border-construct-red focus:ring-1 focus:ring-construct-red/20 focus:outline-none transition-all',
                      isDarkMode ? 'bg-gray-900/50 border-gray-700 text-white placeholder-gray-600' : 'bg-gray-50/50 border-gray-200 text-gray-900 placeholder-gray-400 hover:border-gray-300'
                    ]"
                    :placeholder="t('admin_label_key_placeholder') || 'e.g. admin_dashboard'"
                  />
                </div>
              </td>
              <td class="px-4 py-3">
                <span class="text-sm font-bold truncate block" :title="t(item.label_key)">
                  {{ t(item.label_key) || '(Empty)' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <input 
                    v-model="item.path"
                    type="text"
                    :class="[
                      'flex-1 px-3 py-1.5 border rounded-lg font-mono text-xs focus:border-construct-red focus:ring-1 focus:ring-construct-red/20 focus:outline-none transition-all',
                      isDarkMode ? 'bg-gray-900/50 border-gray-700 text-white placeholder-gray-600' : 'bg-gray-50/50 border-gray-200 text-gray-900 placeholder-gray-400 hover:border-gray-300'
                    ]"
                    placeholder="/admin/"
                  />
                </div>
              </td>
              <td class="px-4 py-3">
                <select 
                  v-model="item.icon_name"
                  :class="[
                    'w-full px-3 py-1.5 border rounded-lg font-mono text-xs focus:border-construct-red focus:ring-1 focus:ring-construct-red/20 focus:outline-none transition-all appearance-none cursor-pointer',
                    isDarkMode ? 'bg-gray-900/50 border-gray-700 text-white' : 'bg-gray-50/50 border-gray-200 text-gray-900 hover:border-gray-300'
                  ]"
                >
                  <option value="" disabled>{{ t('admin_select_icon') || '选择图标' }}</option>
                  <option v-for="icon in availableIcons" :key="icon" :value="icon">{{ icon }}</option>
                </select>
              </td>
              <td class="px-4 py-3">
                <select 
                  v-model="item.parent_id"
                  :class="[
                    'w-full px-3 py-1.5 border rounded-lg text-xs focus:border-construct-red focus:ring-1 focus:ring-construct-red/20 focus:outline-none transition-all appearance-none cursor-pointer',
                    isDarkMode ? 'bg-gray-900/50 border-gray-700 text-white' : 'bg-gray-50/50 border-gray-200 text-gray-900 hover:border-gray-300'
                  ]"
                >
                  <option :value="null">{{ t('admin_no_parent') || '无父级' }}</option>
                  <option v-for="parent in parentMenuOptions" :key="parent.id" :value="parent.id">{{ parent.label }}</option>
                </select>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button 
                    @click="handleDelete(item)"
                    class="p-2 text-gray-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20"
                  >
                    <Trash2 size="16" />
                  </button>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
      
      <!-- Empty State -->
      <div v-if="(currentTab === 'front' ? frontMenuList : flattenedAdminMenuList).length === 0" class="mt-6">
        <EmptyState 
          :title="t('admin_no_menu_items') || 'No menu items found'"
          :description="t('admin_click_add') || 'Click add to create a new menu item'"
          :icon="Folder"
        />
      </div>

      <!-- Add Row -->
      <div :class="['p-4 border-t border-dashed transition-all', isDarkMode ? 'bg-gray-900/30 border-gray-700' : 'bg-gray-50/30 border-gray-200']">
        <button 
          @click="handleAdd"
          :class="['w-full py-3 border-2 border-dashed rounded-lg flex items-center justify-center gap-2 text-sm font-bold transition-all group',
            isDarkMode ? 'border-gray-600 text-gray-400 hover:text-construct-red hover:border-construct-red' : 'border-gray-300 text-gray-400 hover:text-construct-red hover:border-construct-red'
          ]"
        >
          <Plus size="18" class="group-hover:scale-125 transition-transform" /> 
          {{ currentTab === 'front' ? (t('admin_add_front_item') || 'Add Frontend Item') : (t('admin_add_backend_item') || 'Add Backend Item') }}
        </button>
      </div>
    </div>

    <!-- Save Confirm Dialog -->
    <ConfirmDialog
      :visible="showSaveConfirm"
      :title="t('admin_save_confirm_title') || 'Confirm Save'"
      :content="t('admin_save_confirm_content') || 'Are you sure you want to save the changes?'"
      :confirm-text="t('admin_save') || 'Save'"
      confirm-variant="primary"
      @confirm="confirmSave"
      @cancel="showSaveConfirm = false"
    />

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :visible="deleteConfirm.visible"
      :type="'delete'"
      @confirm="confirmDelete"
      @cancel="deleteConfirm = { visible: false, item: null }"
    />
  </div>
</template>

<style scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}
</style>
