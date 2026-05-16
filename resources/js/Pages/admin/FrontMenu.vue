<script setup>
/**
 * FrontMenu.vue - 菜单节点管理页面
 * 
 * 功能说明：
 * - 管理前台和后台导航栏菜单项 (节点管理)
 * - 支持切换前台菜单与后台菜单视图
 * - 编辑菜单标签 (labelKey) 和 路径 (path/route)
 */
import {
  ref,
  useI18n,
  useTheme,
  Plus,
  Trash2,
  GripVertical,
  ExternalLink,
  Save,
  RotateCcw,
  AdminPagination,
  ConfirmDialog,
  Info,
  useToast,
  Monitor,
  LayoutDashboard,
  Edit3,
  X
} from '../../composables/useAdminImports';
import { frontMenuItems } from '../../data/front_menu';
import { adminMenuItems } from '../../data/menu';

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success } = useToast();

const currentTab = ref('front'); // 'front' or 'admin'
const frontMenuList = ref([...frontMenuItems]);
const adminMenuList = ref([...adminMenuItems]);

const isSaving = ref(false);
const showSaveConfirm = ref(false);
const isEditing = ref(false);

const startEditing = () => {
  isEditing.value = true;
};

const cancelEditing = () => {
  frontMenuList.value = [...frontMenuItems];
  adminMenuList.value = [...adminMenuItems];
  isEditing.value = false;
};

const handleAdd = () => {
  const targetList = currentTab.value === 'front' ? frontMenuList : adminMenuList;
  const newId = targetList.value.length > 0 ? Math.max(...targetList.value.map(i => i.id || 0)) + 1 : 1;
  
  if (currentTab.value === 'front') {
    targetList.value.push({
      id: newId,
      labelKey: '',
      path: '/'
    });
  } else {
    targetList.value.push({
      id: String(newId),
      labelKey: '',
      iconKey: 'circle',
      route: '/admin/'
    });
  }
};

const handleDelete = (id) => {
  if (currentTab.value === 'front') {
    frontMenuList.value = frontMenuList.value.filter(item => item.id !== id);
  } else {
    adminMenuList.value = adminMenuList.value.filter(item => item.id !== id);
  }
};

const handleSave = () => {
  showSaveConfirm.value = true;
};

const confirmSave = () => {
  showSaveConfirm.value = false;
  isSaving.value = true;
  // 模拟保存过程
  setTimeout(() => {
    console.log('Saved Front Menu:', frontMenuList.value);
    console.log('Saved Admin Menu:', adminMenuList.value);
    isSaving.value = false;
    isEditing.value = false;
    success(t('admin_save') + ' ' + t('confirm'));
  }, 500);
};

const handleReset = () => {
  if (currentTab.value === 'front') {
    frontMenuList.value = [...frontMenuItems];
  } else {
    adminMenuList.value = [...adminMenuItems];
  }
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8 flex justify-between items-end">
      <div>
        <h2 :class="['font-display text-4xl tracking-tighter mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">
          {{ t('admin_menu_management') }}
        </h2>
        <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
          {{ t('admin_menu_management_subtitle') }}
        </p>
      </div>
      
      <div class="flex gap-3">
        <template v-if="!isEditing">
          <button 
            @click="startEditing"
            class="flex items-center gap-2 px-8 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-700 transition-colors rounded shadow-sm"
          >
            <Edit3 size="16" class="!text-white" /> {{ t('admin_edit') }}
          </button>
        </template>
        <template v-else>
          <button 
            @click="cancelEditing"
            :class="['px-6 py-3 border flex items-center gap-2 font-bold text-sm tracking-wider uppercase transition-colors rounded hover:bg-gray-100', isDarkMode ? 'border-gray-700 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50']"
          >
            <X size="16" /> {{ t('admin_cancel') }}
          </button>
          <button 
            @click="handleSave"
            :disabled="isSaving"
            class="flex items-center gap-2 px-8 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-700 transition-colors rounded shadow-sm disabled:opacity-50"
          >
            <Save size="16" class="!text-white" /> {{ isSaving ? t('admin_save') + '...' : t('admin_save') }}
          </button>
        </template>
      </div>
    </div>

    <!-- Tabs UI -->
    <div class="mb-6 flex gap-2 p-1 border rounded-lg w-fit" :class="isDarkMode ? 'border-gray-700 bg-gray-900/50' : 'border-gray-200 bg-gray-100'">
      <button 
        @click="currentTab = 'front'"
        :disabled="isEditing"
        :class="[
          'px-6 py-2 rounded-md text-xs font-black tracking-widest uppercase transition-all flex items-center gap-2',
          currentTab === 'front' 
            ? 'bg-construct-red text-white shadow-lg' 
            : (isDarkMode ? 'text-gray-500 hover:text-gray-300' : 'text-gray-500 hover:text-gray-700'),
          isEditing ? 'opacity-50 cursor-not-allowed' : ''
        ]"
      >
        <Monitor size="14" /> {{ t('admin_front_menu_tab') }}
      </button>
      <button 
        @click="currentTab = 'admin'"
        :disabled="isEditing"
        :class="[
          'px-6 py-2 rounded-md text-xs font-black tracking-widest uppercase transition-all flex items-center gap-2',
          currentTab === 'admin' 
            ? 'bg-construct-red text-white shadow-lg' 
            : (isDarkMode ? 'text-gray-500 hover:text-gray-300' : 'text-gray-500 hover:text-gray-700'),
          isEditing ? 'opacity-50 cursor-not-allowed' : ''
        ]"
      >
        <LayoutDashboard size="14" /> {{ t('admin_backend_menu_tab') }}
      </button>
    </div>

    <!-- Menu List Table -->
    <div :class="['border rounded-lg overflow-hidden', isDarkMode ? 'border-gray-700 bg-gray-800' : 'border-gray-200 bg-white']">
      <table class="w-full text-left table-fixed">
        <thead :class="['border-b', isDarkMode ? 'border-gray-700 bg-gray-900/50 text-gray-400' : 'border-gray-200 bg-gray-50 text-gray-500']">
          <tr>
            <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest w-16 text-center">Order</th>
            <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest w-1/4">Label Key</th>
            <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest w-1/4">Display Text</th>
            <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest w-1/4">{{ currentTab === 'front' ? 'Path' : 'Route' }}</th>
            <th v-if="currentTab === 'admin'" class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest w-24">Icon</th>
            <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest w-20 text-right">Actions</th>
          </tr>
        </thead>
        <tbody :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
          <tr 
            v-for="(item, index) in (currentTab === 'front' ? frontMenuList : adminMenuList)" 
            :key="item.id"
            :class="['border-b transition-colors', isDarkMode ? 'border-gray-700 hover:bg-gray-700/30' : 'border-gray-100 hover:bg-gray-50/50']"
          >
            <td class="px-6 py-4 text-center">
              <GripVertical size="16" :class="['mx-auto opacity-30', isEditing ? 'cursor-move hover:opacity-100' : 'opacity-10 cursor-not-allowed']" />
            </td>
            <td class="px-6 py-4">
              <input 
                v-model="item.labelKey"
                type="text"
                :disabled="!isEditing"
                :class="[
                  'w-full px-3 py-2 border rounded font-mono text-xs focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-900 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900',
                  !isEditing ? 'opacity-50 cursor-not-allowed' : ''
                ]"
                placeholder="e.g. nav_home"
              />
            </td>
            <td class="px-6 py-4">
              <span class="text-sm font-bold truncate block" :title="t(item.labelKey)">
                {{ t(item.labelKey) || '(Empty)' }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <input 
                  v-model="item[currentTab === 'front' ? 'path' : 'route']"
                  type="text"
                  :disabled="!isEditing"
                  :class="[
                    'flex-1 px-3 py-2 border rounded font-mono text-xs focus:border-construct-red focus:outline-none transition-all',
                    isDarkMode ? 'bg-gray-900 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900',
                    !isEditing ? 'opacity-50 cursor-not-allowed' : ''
                  ]"
                  placeholder="/"
                />
                <ExternalLink size="14" class="opacity-40 shrink-0" />
              </div>
            </td>
            <td v-if="currentTab === 'admin'" class="px-6 py-4">
              <input 
                v-model="item.iconKey"
                type="text"
                :disabled="!isEditing"
                :class="[
                  'w-full px-3 py-2 border rounded font-mono text-xs focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-900 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900',
                  !isEditing ? 'opacity-50 cursor-not-allowed' : ''
                ]"
                placeholder="iconName"
              />
            </td>
            <td class="px-6 py-4 text-right">
              <button 
                @click="handleDelete(item.id)"
                :disabled="!isEditing"
                class="p-2 text-gray-400 hover:text-red-500 transition-colors rounded hover:bg-red-50 dark:hover:bg-red-900/20 disabled:opacity-20 disabled:cursor-not-allowed"
              >
                <Trash2 size="16" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Empty State -->
      <div v-if="(currentTab === 'front' ? frontMenuList : adminMenuList).length === 0" class="p-12 text-center text-gray-500 font-bold uppercase tracking-widest text-sm">
        No menu items found. Click edit to unlock management.
      </div>

      <!-- Add Row -->
      <div v-if="isEditing" class="p-4 bg-gray-50/30 dark:bg-gray-900/30 border-t border-dashed border-gray-200 dark:border-gray-700 transition-all">
        <button 
          @click="handleAdd"
          class="w-full py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex items-center justify-center gap-2 text-sm font-bold text-gray-400 hover:text-construct-red hover:border-construct-red transition-all group"
        >
          <Plus size="18" class="group-hover:scale-125 transition-transform" /> {{ currentTab === 'front' ? 'Add Frontend Item' : 'Add Backend Item' }}
        </button>
      </div>
    </div>

    <!-- Help Notice -->
    <div class="mt-8 p-6 bg-blue-50 border border-blue-100 rounded-lg flex items-start gap-4">
      <div class="p-2 bg-blue-500 rounded text-white shadow-sm">
        <Info size="20" />
      </div>
      <div>
        <h4 class="font-bold text-blue-900 mb-1">Configuration Note</h4>
        <p class="text-sm text-blue-700 leading-relaxed">
          The menu labels use translation keys from the language files. Ensure the <code class="bg-blue-100 px-1 rounded text-xs font-bold">labelKey</code> you enter exists in <code class="bg-blue-100 px-1 rounded text-xs">zh.json</code>, <code class="bg-blue-100 px-1 rounded text-xs">en.json</code>, etc. to display correct multi-language text.
        </p>
      </div>
    </div>

    <!-- Confirm Dialog -->
    <ConfirmDialog
      :visible="showSaveConfirm"
      :title="t('admin_save_confirm_title') || 'Confirm Save'"
      :content="t('admin_save_confirm_content') || 'Are you sure you want to save the changes?'"
      :confirm-text="t('admin_save') || 'Save'"
      confirm-variant="primary"
      @confirm="confirmSave"
      @cancel="showSaveConfirm = false"
    />
  </div>
</template>

<style scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}
</style>
