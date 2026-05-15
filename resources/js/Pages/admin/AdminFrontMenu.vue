<script setup>
/**
 * AdminFrontMenu.vue - 前台菜单管理页面
 * 
 * 功能说明：
 * - 管理前台导航栏菜单项
 * - 编辑菜单标签 (labelKey) 和 路径 (path)
 * - 新增、删除菜单项
 * - 拖拽排序 (模拟)
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
  AdminPagination
} from '../../composables/useAdminImports';
import { frontMenuItems } from '../../data/front_menu';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const menuList = ref([...frontMenuItems]);
const isSaving = ref(false);

const handleAdd = () => {
  const newId = menuList.value.length > 0 ? Math.max(...menuList.value.map(i => i.id)) + 1 : 1;
  menuList.value.push({
    id: newId,
    labelKey: '',
    path: '/'
  });
};

const handleDelete = (id) => {
  menuList.value = menuList.value.filter(item => item.id !== id);
};

const handleSave = () => {
  isSaving.value = true;
  // 模拟保存过程
  setTimeout(() => {
    console.log('Saved Menu List:', menuList.value);
    isSaving.value = false;
    alert(t('admin_save') + ' ' + t('confirm'));
  }, 500);
};

const handleReset = () => {
  menuList.value = [...frontMenuItems];
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8 flex justify-between items-end">
      <div>
        <h2 :class="['font-display text-4xl tracking-tighter mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">
          {{ t('admin_front_menu') }}
        </h2>
        <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
          {{ t('admin_front_menu_subtitle') }}
        </p>
      </div>
      
      <div class="flex gap-3">
        <button 
          @click="handleReset"
          :class="['px-6 py-3 border flex items-center gap-2 font-bold text-sm tracking-wider uppercase transition-colors rounded hover:bg-gray-100', isDarkMode ? 'border-gray-700 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50']"
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

    <!-- Menu List Table -->
    <div :class="['border rounded-lg overflow-hidden', isDarkMode ? 'border-gray-700 bg-gray-800' : 'border-gray-200 bg-white']">
      <table class="w-full text-left">
        <thead :class="['border-b', isDarkMode ? 'border-gray-700 bg-gray-900/50 text-gray-400' : 'border-gray-200 bg-gray-50 text-gray-500']">
          <tr>
            <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest w-12 text-center">Order</th>
            <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest">Label Key (Translation)</th>
            <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest">Display Text</th>
            <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest">Path</th>
            <th class="px-6 py-4 font-bold uppercase text-[10px] tracking-widest text-right">Actions</th>
          </tr>
        </thead>
        <tbody :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
          <tr 
            v-for="(item, index) in menuList" 
            :key="item.id"
            :class="['border-b transition-colors', isDarkMode ? 'border-gray-700 hover:bg-gray-700/30' : 'border-gray-100 hover:bg-gray-50/50']"
          >
            <td class="px-6 py-4 text-center">
              <GripVertical size="16" class="mx-auto cursor-move opacity-30 hover:opacity-100" />
            </td>
            <td class="px-6 py-4">
              <input 
                v-model="item.labelKey"
                type="text"
                :class="[
                  'w-full px-3 py-2 border rounded font-mono text-xs focus:border-construct-red focus:outline-none',
                  isDarkMode ? 'bg-gray-900 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
                placeholder="e.g. nav_home"
              />
            </td>
            <td class="px-6 py-4">
              <span class="text-sm font-bold">{{ t(item.labelKey) || '(Empty)' }}</span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <input 
                  v-model="item.path"
                  type="text"
                  :class="[
                    'flex-1 px-3 py-2 border rounded font-mono text-xs focus:border-construct-red focus:outline-none',
                    isDarkMode ? 'bg-gray-900 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
                  ]"
                  placeholder="/"
                />
                <ExternalLink size="14" class="opacity-40" />
              </div>
            </td>
            <td class="px-6 py-4 text-right">
              <button 
                @click="handleDelete(item.id)"
                class="p-2 text-gray-400 hover:text-red-500 transition-colors rounded hover:bg-red-50"
              >
                <Trash2 size="16" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Empty State -->
      <div v-if="menuList.length === 0" class="p-12 text-center text-gray-500 font-bold uppercase tracking-widest text-sm">
        No menu items found. Click add to create one.
      </div>

      <!-- Add Row -->
      <div class="p-4 bg-gray-50/30 dark:bg-gray-900/30 border-t border-dashed border-gray-200 dark:border-gray-700">
        <button 
          @click="handleAdd"
          class="w-full py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex items-center justify-center gap-2 text-sm font-bold text-gray-400 hover:text-construct-red hover:border-construct-red transition-all group"
        >
          <Plus size="18" class="group-hover:scale-125 transition-transform" /> Add New Menu Item
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
  </div>
</template>

<style scoped>
.font-display {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}
</style>
