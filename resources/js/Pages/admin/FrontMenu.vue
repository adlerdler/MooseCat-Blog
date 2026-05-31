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
  Info,
  useToast,
  Monitor,
  LayoutDashboard,
  Edit3,
  X,
  ChevronUp,
  ChevronDown,
  Eye,
  EyeOff,
  Search,
  Filter,
  Folder,
  SearchFilterModal
} from '../../composables/useAdminImports';
import { useMenuItems } from '../../composables/useMenuItems';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  allMenus: { type: Array, default: () => [] },
});

const { frontMenuItems, adminMenuItems } = useMenuItems({ menus: props.allMenus });

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

const currentTab = ref('front'); // 'front' or 'admin'
const frontMenuList = ref([...frontMenuItems]);
const adminMenuList = ref([...adminMenuItems]);
const searchQuery = ref('');
const showInactive = ref(true);

watch(() => props.allMenus, (newMenus) => {
  const { frontMenuItems: newFront, adminMenuItems: newAdmin } = useMenuItems({ menus: newMenus });
  frontMenuList.value = [...newFront];
  adminMenuList.value = [...newAdmin];
}, { deep: true });

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

const flattenedAdminMenu = computed(() => {
  let items = flattenAdminMenu(adminMenuList.value);
  
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
  const targetList = currentTab.value === 'front' ? frontMenuList : adminMenuList;
  const currentItems = currentTab.value === 'front' ? frontMenuItems : adminMenuItems;
  const newId = Math.max(...currentItems.map(i => i.id || 0), ...targetList.value.map(i => i.id || 0), 0) + 1;
  
  if (currentTab.value === 'front') {
    targetList.value.push({
      id: newId,
      type: 'front',
      parent_id: null,
      label_key: 'nav_new_item',
      path: '/new',
      sort_order: targetList.value.length + 1,
      is_active: true
    });
  } else {
    targetList.value.push({
      id: newId,
      type: 'admin',
      parent_id: null,
      label_key: 'admin_new_item',
      icon_name: 'FileText',
      path: '/admin/new',
      sort_order: targetList.value.length + 1,
      is_active: true,
      children: []
    });
  }
};

const handleDelete = (item) => {
  deleteConfirm.value = { visible: true, item };
};

const confirmDelete = () => {
  const item = deleteConfirm.value.item;
  router.delete(route('admin.front-menu.destroy', item.id), {
    onSuccess: () => {
      if (currentTab.value === 'front') {
        frontMenuList.value = frontMenuList.value.filter(l => l.id !== item.id);
      } else {
        const removeFromTree = (items) => {
          return items.filter(i => {
            if (i.id === item.id) return false;
            if (i.children) {
              i.children = removeFromTree(i.children);
            }
            return true;
          });
        };
        adminMenuList.value = removeFromTree(adminMenuList.value);
      }
      success(t('admin_delete') + ' ' + t('confirm'));
    }
  });
  deleteConfirm.value = { visible: false, item: null };
};

const handleSave = () => {
  showSaveConfirm.value = true;
};

const confirmSave = () => {
  showSaveConfirm.value = false;
  isSaving.value = true;
  
  const targetList = currentTab.value === 'front' ? frontMenuList.value : flattenedAdminMenu.value;
  const invalidItems = targetList.filter(item => {
    const hasLabel = item.label_key?.trim();
    const hasPath = item.path?.trim();
    const isParentMenu = !item.parent_id;
    return !hasLabel || (!hasPath && !isParentMenu);
  });
  
  if (invalidItems.length > 0) {
    isSaving.value = false;
    console.log('Invalid items detail:', invalidItems.map(i => ({ id: i.id, label_key: i.label_key, path: i.path })));
    error(t('admin_error_empty_fields') || '请填写所有必填字段');
    return;
  }
  
  const menusData = targetList.map(item => ({
    id: props.allMenus.some(m => m.id === item.id) ? item.id : null,
    type: currentTab.value === 'front' ? 'front' : 'admin',
    label_key: item.label_key,
    path: item.path || null,
    icon_name: item.icon_name || null,
    component_name: item.component_name || null,
    parent_id: item.parent_id || null,
    sort_order: item.sort_order || 1,
    is_active: item.is_active !== false,
  }));
  
  router.post(route('admin.front-menu.batch-update'), { menus: menusData }, {
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
    frontMenuList.value = [...frontMenuItems];
  } else {
    adminMenuList.value = [...adminMenuItems];
  }
};

// 移动菜单项
const moveItem = (index, direction) => {
  const targetList = currentTab.value === 'front' ? frontMenuList : adminMenuList;
  
  if (currentTab.value === 'front') {
    const newIndex = direction === 'up' ? index - 1 : index + 1;
    if (newIndex >= 0 && newIndex < targetList.value.length) {
      const temp = targetList.value[index];
      targetList.value[index] = targetList.value[newIndex];
      targetList.value[newIndex] = temp;
      // 更新排序
      targetList.value.forEach((item, i) => {
        item.sort_order = i + 1;
      });
    }
  } else {
    // 后台树形菜单的排序需要更复杂的处理
    const moveInTree = (items, targetId, direction) => {
      for (let i = 0; i < items.length; i++) {
        if (items[i].id === targetId) {
          const newIndex = direction === 'up' ? i - 1 : i + 1;
          if (newIndex >= 0 && newIndex < items.length) {
            const temp = items[i];
            items[i] = items[newIndex];
            items[newIndex] = temp;
            items.forEach((item, idx) => {
              item.sort_order = idx + 1;
            });
            return true;
          }
          return false;
        }
        if (items[i].children && items[i].children.length > 0) {
          if (moveInTree(items[i].children, targetId, direction)) {
            return true;
          }
        }
      }
      return false;
    };
    
    const item = flattenedAdminMenu.value[index];
    moveInTree(targetList.value, item.id, direction);
  }
};

// 切换禁用状态
const toggleActive = (item) => {
  item.is_active = !item.is_active;
};

// 获取可用图标列表
const availableIcons = [
  'LayoutDashboard', 'FileText', 'Play', 'FolderKanban', 'BookOpen', 'Users', 
  'Settings', 'Folder', 'Tag', 'MessageSquare', 'Shield', 'HardDrive', 
  'Info', 'Archive', 'SlidersHorizontal', 'Book', 'RotateCcw', 'Image', 
  'Zap', 'Link', 'Menu', 'ExternalLink', 'Plus', 'Trash2'
];

// 获取父级菜单选项（用于二级菜单）
const parentMenuOptions = computed(() => {
  if (currentTab.value !== 'admin') return [];
  
  const parents = [];
  const collectParents = (items) => {
    items.forEach(item => {
      if (!item.parent_id) {
        parents.push({ 
          id: item.id, 
          label: (item.label_key ? t(item.label_key) : item.label_key) || String(item.id)
        });
      }
      if (item.children) {
        collectParents(item.children);
      }
    });
  };
  collectParents(adminMenuList.value);
  return parents;
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
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
      <div>
        <h2 :class="['font-display text-4xl tracking-tighter mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">
          {{ t('admin_menu_management') }}
        </h2>
        <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
          {{ t('admin_menu_management_subtitle') }}
        </p>
      </div>
      
      <div class="flex gap-3">
        <button 
          @click="handleReset"
          :class="['px-6 py-3 border flex items-center gap-2 font-bold text-xs tracking-wider uppercase transition-colors rounded', isDarkMode ? 'border-gray-700 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-100']"
        >
          <RotateCcw size="16" /> {{ t('admin_reset') }}
        </button>
        <button 
          @click="handleSave"
          :disabled="isSaving"
          class="flex items-center gap-2 px-8 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-700 transition-colors rounded shadow-sm disabled:opacity-50"
        >
          <Save size="16" class="!text-white" /> {{ isSaving ? t('admin_save') + '...' : t('admin_save') }}
        </button>
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
                'border-b transition-colors', 
                isDarkMode ? 'border-gray-700 hover:bg-gray-700/30' : 'border-gray-100 hover:bg-gray-50/50',
                !item.is_active ? 'opacity-50' : ''
              ]"
            >
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-1">
                  <button 
                    @click="moveItem(index, 'up')"
                    :disabled="index === 0"
                    :class="[
                      'p-1 rounded transition-colors',
                      index > 0 
                        ? (isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-600' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100')
                        : 'opacity-20 cursor-not-allowed'
                    ]"
                  >
                    <ChevronUp size="14" />
                  </button>
                  <span class="text-xs font-bold w-4 text-center">{{ index + 1 }}</span>
                  <button 
                    @click="moveItem(index, 'down')"
                    :disabled="index === filteredFrontMenu.length - 1"
                    :class="[
                      'p-1 rounded transition-colors',
                      index < filteredFrontMenu.length - 1 
                        ? (isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-600' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100')
                        : 'opacity-20 cursor-not-allowed'
                    ]"
                  >
                    <ChevronDown size="14" />
                  </button>
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
                    'w-full px-3 py-2 border rounded font-mono text-xs focus:border-construct-red focus:outline-none transition-all',
                    isDarkMode ? 'bg-gray-900 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
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
                      'flex-1 px-3 py-2 border rounded font-mono text-xs focus:border-construct-red focus:outline-none transition-all',
                      isDarkMode ? 'bg-gray-900 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
                    ]"
                    placeholder="/"
                  />
                  <ExternalLink size="14" class="opacity-40 shrink-0" />
                </div>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button 
                    @click="handleDelete(item)"
                    class="p-2 text-gray-400 hover:text-red-500 transition-colors rounded hover:bg-red-50 dark:hover:bg-red-900/20"
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
                'border-b transition-colors', 
                isDarkMode ? 'border-gray-700 hover:bg-gray-700/30' : 'border-gray-100 hover:bg-gray-50/50',
                !item.is_active ? 'opacity-50' : ''
              ]"
            >
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-1">
                  <button 
                    @click="moveItem(index, 'up')"
                    :disabled="index === 0"
                    :class="[
                      'p-1 rounded transition-colors',
                      index > 0 
                        ? (isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-600' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100')
                        : 'opacity-20 cursor-not-allowed'
                    ]"
                  >
                    <ChevronUp size="14" />
                  </button>
                  <span class="text-xs font-bold w-4 text-center">{{ index + 1 }}</span>
                  <button 
                    @click="moveItem(index, 'down')"
                    :disabled="index === flattenedAdminMenu.length - 1"
                    :class="[
                      'p-1 rounded transition-colors',
                      index < flattenedAdminMenu.length - 1 
                        ? (isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-600' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100')
                        : 'opacity-20 cursor-not-allowed'
                    ]"
                  >
                    <ChevronDown size="14" />
                  </button>
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
                      'flex-1 px-3 py-2 border rounded font-mono text-xs focus:border-construct-red focus:outline-none transition-all',
                      isDarkMode ? 'bg-gray-900 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
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
                      'flex-1 px-3 py-2 border rounded font-mono text-xs focus:border-construct-red focus:outline-none transition-all',
                      isDarkMode ? 'bg-gray-900 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
                    ]"
                    placeholder="/admin/"
                  />
                  <ExternalLink size="14" class="opacity-40 shrink-0" />
                </div>
              </td>
              <td class="px-4 py-3">
                <select 
                  v-model="item.icon_name"
                  :class="[
                    'w-full px-3 py-2 border rounded font-mono text-xs focus:border-construct-red focus:outline-none transition-all appearance-none',
                    isDarkMode ? 'bg-gray-900 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
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
                    'w-full px-3 py-2 border rounded text-xs focus:border-construct-red focus:outline-none transition-all appearance-none',
                    isDarkMode ? 'bg-gray-900 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
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
                    class="p-2 text-gray-400 hover:text-red-500 transition-colors rounded hover:bg-red-50 dark:hover:bg-red-900/20"
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
      <div v-if="(currentTab === 'front' ? filteredFrontMenu : flattenedAdminMenu).length === 0" :class="['p-12 text-center', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
        <div class="text-6xl mb-4">
          <Folder :size="64" />
        </div>
        <p class="font-bold uppercase tracking-widest text-sm">
          {{ t('admin_no_menu_items') || 'No menu items found' }}
        </p>
        <p class="text-sm mt-2 opacity-70">
          {{ t('admin_click_add') || 'Click add to create a new menu item' }}
        </p>
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

    <!-- Help Notice -->
    <div :class="['mt-8 p-6 border rounded-lg flex items-start gap-4', isDarkMode ? 'bg-blue-900/20 border-blue-800' : 'bg-blue-50 border-blue-100']">
      <div :class="['p-2 rounded text-white shadow-sm', isDarkMode ? 'bg-blue-600' : 'bg-blue-500']">
        <Info size="20" />
      </div>
      <div>
        <h4 :class="['font-bold mb-1', isDarkMode ? 'text-blue-300' : 'text-blue-900']">
          {{ t('admin_configuration_note') || 'Configuration Note' }}
        </h4>
        <p :class="['text-sm leading-relaxed', isDarkMode ? 'text-blue-400' : 'text-blue-700']">
          {{ t('admin_menu_help_text') || 'The menu labels use translation keys from the language files. Ensure the' }} 
          <code :class="['px-1 rounded text-xs font-bold', isDarkMode ? 'bg-blue-800' : 'bg-blue-100']">label_key</code> 
          {{ t('admin_menu_help_text_2') || 'you enter exists in' }} 
          <code :class="['px-1 rounded text-xs', isDarkMode ? 'bg-blue-800' : 'bg-blue-100']">zh.json</code>, 
          <code :class="['px-1 rounded text-xs', isDarkMode ? 'bg-blue-800' : 'bg-blue-100']">en.json</code>, 
          {{ t('admin_menu_help_text_3') || 'etc. to display correct multi-language text.' }}
        </p>
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
