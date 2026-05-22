<script setup>
/**
 * I18nManager.vue - 多语言配置管理页面
 * 
 * 功能说明：
 * - 管理系统支持的语言列表
 * - 编辑各语言的翻译键值对
 * - 支持添加/删除语言
 * - 支持批量导入/导出翻译数据
 */
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Languages,
  Plus,
  Edit3,
  Trash2,
  X,
  Globe,
  Download,
  Upload,
  ChevronDown,
  ChevronUp,
  AlertCircle
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { languages as languagesConfig } from '../../data/i18n_config';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import SearchFilterModal from '../../components/admin/SearchFilterModal.vue';
import Pagination from '../../components/admin/Pagination.vue';
import { useToast } from '../../composables/useToast';

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success, error, warning } = useToast();

const showTopError = ref(false);
const topErrorMessage = ref('');

const showTopErrorToast = (message) => {
  topErrorMessage.value = message;
  showTopError.value = true;
  setTimeout(() => {
    showTopError.value = false;
  }, 3000);
};

const languages = ref([]);
const translations = ref({});

const activeTab = ref('languages');
const activeLanguage = ref('en');
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);

const showDeleteConfirm = ref(false);
const deletingItemId = ref(null);
const deletingItemType = ref('');

const showAddLanguageModal = ref(false);
const newLanguageForm = ref({
  code: '',
  name: '',
  native_name: '',
  flag: ''
});

const showEditTranslationModal = ref(false);
const editingKey = ref('');
const editingValue = ref('');

const showAddKeyModal = ref(false);
const newKeyForm = ref({
  key: '',
  value: ''
});

const loadLanguages = () => {
  languages.value = languagesConfig.map(lang => ({ ...lang }));
};

const loadTranslations = async () => {
  const loadedTranslations = {};
  
  for (const lang of languagesConfig) {
    try {
      const response = await fetch(lang.file_path);
      const data = await response.json();
      loadedTranslations[lang.code] = data;
    } catch (error) {
      console.error(`Failed to load translations for ${lang.code}:`, error);
      loadedTranslations[lang.code] = {};
    }
  }
  
  translations.value = loadedTranslations;
};

const availableLanguages = computed(() => {
  return languages.value.filter(lang => lang.is_active);
});

const currentTranslations = computed(() => {
  return translations.value[activeLanguage.value] || {};
});

const filteredTranslationKeys = computed(() => {
  const allKeys = new Set();
  Object.values(translations.value).forEach(trans => {
    Object.keys(trans).forEach(key => allKeys.add(key));
  });
  
  const keys = Array.from(allKeys);
  if (!searchQuery.value) return keys;
  const query = searchQuery.value.toLowerCase();
  return keys.filter(key => {
    if (key.toLowerCase().includes(query)) return true;
    return Object.values(translations.value).some(trans =>
      trans[key] && trans[key].toLowerCase().includes(query)
    );
  });
});

const totalPages = computed(() => Math.ceil(filteredTranslationKeys.value.length / itemsPerPage.value));

const paginatedKeys = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredTranslationKeys.value.slice(start, start + itemsPerPage.value);
});

const openAddLanguageModal = () => {
  showAddLanguageModal.value = true;
  newLanguageForm.value = { code: '', name: '', native_name: '', flag: '', file_path: '', is_default: false, is_active: true };
};

const cancelAddLanguage = () => {
  showAddLanguageModal.value = false;
  newLanguageForm.value = { code: '', name: '', native_name: '', flag: '', file_path: '', is_default: false, is_active: true };
};

const showEditLanguageModal = ref(false);
const editingLanguageForm = ref({ id: null, code: '', name: '', native_name: '', flag: '', file_path: '', is_default: false, is_active: true });

const openEditLanguageModal = (lang) => {
  showEditLanguageModal.value = true;
  editingLanguageForm.value = {
    id: lang.id,
    code: lang.code,
    name: lang.name,
    native_name: lang.native_name,
    flag: lang.flag,
    file_path: lang.file_path,
    is_default: lang.is_default,
    is_active: lang.is_active
  };
};

const cancelEditLanguage = () => {
  showEditLanguageModal.value = false;
  editingLanguageForm.value = { id: null, code: '', name: '', native_name: '', flag: '', file_path: '', is_default: false, is_active: true };
};

const confirmEditLanguage = () => {
  if (!editingLanguageForm.value.name.trim()) {
    showTopErrorToast('请输入语言名称');
    return;
  }

  const langIndex = languages.value.findIndex(l => l.id === editingLanguageForm.value.id);
  if (langIndex === -1) return;

  const oldCode = languages.value[langIndex].code;
  const newCode = editingLanguageForm.value.code.trim();

  languages.value[langIndex] = {
    ...languages.value[langIndex],
    name: editingLanguageForm.value.name.trim(),
    native_name: editingLanguageForm.value.native_name.trim() || editingLanguageForm.value.name.trim(),
    flag: editingLanguageForm.value.flag.trim() || '🌐',
    file_path: editingLanguageForm.value.file_path.trim() || `/locales/${newCode}.json`,
    is_default: editingLanguageForm.value.is_default,
    is_active: editingLanguageForm.value.is_active,
    updated_at: new Date().toISOString()
  };

  if (oldCode !== newCode && translations.value[oldCode]) {
    translations.value[newCode] = translations.value[oldCode];
    delete translations.value[oldCode];
  }

  cancelEditLanguage();
  success('语言信息已更新');
};

const confirmAddLanguage = () => {
  if (!newLanguageForm.value.code.trim()) {
    showTopErrorToast('请输入语言代码');
    return;
  }
  if (!newLanguageForm.value.name.trim()) {
    showTopErrorToast('请输入语言名称');
    return;
  }
  
  const code = newLanguageForm.value.code.trim();
  const exists = languages.value.some(l => l.code === code);
  if (exists) {
    showTopErrorToast('该语言代码已存在');
    return;
  }
  
  const newId = Math.max(...languages.value.map(l => l.id), 0) + 1;
  const maxSortOrder = Math.max(...languages.value.map(l => l.sort_order), 0);
  
  languages.value.push({
    id: newId,
    code,
    name: newLanguageForm.value.name.trim(),
    native_name: newLanguageForm.value.native_name.trim() || newLanguageForm.value.name.trim(),
    flag: newLanguageForm.value.flag.trim() || '🌐',
    file_path: newLanguageForm.value.file_path.trim() || `/locales/${code}.json`,
    is_default: newLanguageForm.value.is_default,
    is_active: newLanguageForm.value.is_active,
    sort_order: maxSortOrder + 1,
    created_at: new Date().toISOString(),
    updated_at: new Date().toISOString()
  });
  
  translations.value[code] = {};
  
  cancelAddLanguage();
  success('语言已添加');
};

const handleDelete = (id, type) => {
  deletingItemId.value = id;
  deletingItemType.value = type;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingItemType.value === 'language') {
    const lang = languages.value.find(l => l.code === deletingItemId.value);
    if (lang && lang.is_default) {
      showTopErrorToast('不能删除默认语言');
      showDeleteConfirm.value = false;
      return;
    }
    languages.value = languages.value.filter(l => l.code !== deletingItemId.value);
    delete translations.value[deletingItemId.value];
    if (activeLanguage.value === deletingItemId.value) {
      activeLanguage.value = languages.value[0]?.code || 'en';
    }
    warning('语言已删除');
  } else if (deletingItemType.value === 'translation') {
    const key = deletingItemId.value;
    Object.keys(translations.value).forEach(code => {
      delete translations.value[code][key];
    });
    warning('翻译键已删除');
  }
  showDeleteConfirm.value = false;
  deletingItemId.value = null;
  deletingItemType.value = '';
};

const editingValues = ref({});

const openEditTranslation = (key) => {
  editingKey.value = key;
  editingValues.value = {};
  availableLanguages.value.forEach(lang => {
    editingValues.value[lang.code] = translations.value[lang.code]?.[key] || '';
  });
  showEditTranslationModal.value = true;
};

const closeEditTranslation = () => {
  showEditTranslationModal.value = false;
  editingKey.value = '';
  editingValues.value = {};
};

const saveTranslation = () => {
  Object.entries(editingValues.value).forEach(([code, value]) => {
    if (!translations.value[code]) {
      translations.value[code] = {};
    }
    translations.value[code][editingKey.value] = value;
  });
  closeEditTranslation();
  success('翻译已更新');
};

const openAddKeyModal = () => {
  showAddKeyModal.value = true;
  newKeyForm.value = { key: '', values: {} };
  availableLanguages.value.forEach(lang => {
    newKeyForm.value.values[lang.code] = '';
  });
};

const cancelAddKey = () => {
  showAddKeyModal.value = false;
  newKeyForm.value = { key: '', values: {} };
};

const confirmAddKey = () => {
  if (!newKeyForm.value.key.trim()) {
    showTopErrorToast('请输入键名');
    return;
  }
  Object.entries(newKeyForm.value.values).forEach(([code, value]) => {
    if (!translations.value[code]) {
      translations.value[code] = {};
    }
    translations.value[code][newKeyForm.value.key.trim()] = value.trim();
  });
  cancelAddKey();
  success('翻译键已添加');
};

const deleteTranslationKey = (key) => {
  deletingItemId.value = key;
  deletingItemType.value = 'translation';
  showDeleteConfirm.value = true;
};

const exportTranslations = () => {
  const data = JSON.stringify(translations.value, null, 2);
  const blob = new Blob([data], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'translations.json';
  a.click();
  URL.revokeObjectURL(url);
  success('翻译数据已导出');
};

const importTranslations = () => {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = '.json';
  input.onchange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = (event) => {
      try {
        const data = JSON.parse(event.target.result);
        translations.value = data;
        success('翻译数据已导入');
      } catch (err) {
        error('导入失败：无效的JSON文件');
      }
    };
    reader.readAsText(file);
  };
  input.click();
};

onMounted(async () => {
  loadLanguages();
  await loadTranslations();
});

const tabs = [
  { key: 'languages', label: '语言列表', icon: Globe },
  { key: 'translations', label: '翻译管理', icon: Languages }
];
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Languages class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">多语言配置</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">管理系统支持的语言和翻译数据</p>
        </div>
        <div class="flex items-center gap-3">
          <button
            @click="exportTranslations"
            :class="['flex items-center gap-2 px-6 py-3 border font-bold text-xs uppercase tracking-wider transition-colors rounded-lg', isDarkMode ? 'border-gray-700 hover:bg-gray-700 text-gray-300' : 'border-gray-300 hover:bg-gray-100 text-gray-700']"
          >
            <Download size="16" /> 导出
          </button>
          <button
            @click="importTranslations"
            :class="['flex items-center gap-2 px-6 py-3 border font-bold text-xs uppercase tracking-wider transition-colors rounded-lg', isDarkMode ? 'border-gray-700 hover:bg-gray-700 text-gray-300' : 'border-gray-300 hover:bg-gray-100 text-gray-700']"
          >
            <Upload size="16" /> 导入
          </button>
          <button
            v-if="activeTab === 'languages'"
            @click="openAddLanguageModal"
            class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
          >
            <Plus size="18" :style="{ color: '#ffffff' }" />
            {{ t('admin_add') }}
          </button>
          <button
            v-if="activeTab === 'translations'"
            @click="openAddKeyModal"
            class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
          >
            <Plus size="18" :style="{ color: '#ffffff' }" />
            添加翻译键
          </button>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div :class="['flex gap-2 mb-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        @click="activeTab = tab.key; currentPage = 1; searchQuery = ''"
        :class="[
          'flex items-center gap-2 px-6 py-3 font-black text-xs uppercase tracking-[0.2em] transition-all border-b-2',
          activeTab === tab.key
            ? 'border-construct-red text-construct-red'
            : isDarkMode
              ? 'border-transparent text-gray-400 hover:text-white'
              : 'border-transparent text-gray-500 hover:text-gray-900'
        ]"
      >
        <component :is="tab.icon" size="16" />
        {{ tab.label }}
      </button>
    </div>

    <!-- Languages Tab -->
    <div v-if="activeTab === 'languages'" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="lang in languages"
          :key="lang.code"
          :class="[
            'border transition-all duration-500 hover:-translate-y-1 rounded-lg overflow-hidden',
            isDarkMode
              ? 'bg-gray-800 border-gray-700 hover:border-construct-red/50 hover:shadow-lg'
              : 'bg-white border-gray-200 hover:border-construct-red/30 hover:shadow-lg'
          ]"
        >
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <div class="text-4xl">{{ lang.flag }}</div>
              <div class="flex items-center gap-2">
                <span v-if="lang.is_default" class="px-2 py-1 text-xs font-bold rounded bg-construct-red/20 text-construct-red">默认</span>
                <span :class="['px-2 py-1 text-xs font-bold rounded', lang.is_active ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400']">
                  {{ lang.is_active ? '已启用' : '已禁用' }}
                </span>
              </div>
            </div>

            <h4 :class="['font-bold text-lg mb-1', isDarkMode ? 'text-white' : 'text-gray-900']">{{ lang.name }}</h4>
            <p :class="['text-sm mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ lang.native_name }}</p>
            <p :class="['text-xs font-mono font-bold', isDarkMode ? 'text-gray-600' : 'text-gray-400']">代码: {{ lang.code }}</p>

            <div :class="['flex items-center justify-between mt-4 pt-4 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
              <span :class="['text-xs font-bold', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                翻译数: {{ Object.keys(translations[lang.code] || {}).length }}
              </span>
              <div class="flex items-center gap-2">
                <button
                  @click="openEditLanguageModal(lang)"
                  :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-construct-red' : 'text-gray-400 hover:bg-gray-100 hover:text-construct-red']"
                >
                  <Edit3 size="14" />
                </button>
                <button
                  @click="handleDelete(lang.code, 'language')"
                  :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-red-400' : 'text-gray-400 hover:bg-gray-100 hover:text-red-500']"
                >
                  <Trash2 size="14" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Translations Tab -->
    <div v-if="activeTab === 'translations'">
      <!-- Search -->
      <div class="mb-6">
        <SearchFilterModal
          v-model:search-query="searchQuery"
          :search-placeholder="'搜索翻译键或值...'"
        />
      </div>

      <!-- Translation Table -->
      <div :class="['border rounded-lg overflow-hidden', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
        <table class="w-full text-sm">
          <thead :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
            <tr>
              <th :class="['px-4 py-3 text-left font-bold tracking-wider uppercase text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">翻译键</th>
              <th v-for="lang in availableLanguages" :key="lang.code" :class="['px-4 py-3 text-left font-bold tracking-wider uppercase text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
                <span class="mr-1">{{ lang.flag }}</span>{{ lang.native_name }}
              </th>
              <th :class="['px-4 py-3 text-center font-bold tracking-wider uppercase text-xs w-32', isDarkMode ? 'text-gray-400' : 'text-gray-500']">操作</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="key in paginatedKeys" :key="key" :class="['border-t', isDarkMode ? 'border-gray-700 hover:bg-gray-700/50' : 'border-gray-200 hover:bg-gray-50']">
              <td :class="['px-4 py-3 font-mono text-xs', isDarkMode ? 'text-construct-red' : 'text-construct-red']">{{ key }}</td>
              <td v-for="lang in availableLanguages" :key="lang.code" :class="['px-4 py-3', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                <span :class="{ 'opacity-40': !translations[lang.code]?.[key] }">
                  {{ translations[lang.code]?.[key] || '—' }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <button
                    @click="openEditTranslation(key)"
                    :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-construct-red' : 'text-gray-400 hover:bg-gray-100 hover:text-construct-red']"
                  >
                    <Edit3 size="14" />
                  </button>
                  <button
                    @click="deleteTranslationKey(key)"
                    :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-red-400' : 'text-gray-400 hover:bg-gray-100 hover:text-red-500']"
                  >
                    <Trash2 size="14" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination
        v-model:current-page="currentPage"
        :total-pages="totalPages"
        :total-items="filteredTranslationKeys.length"
        :items-per-page="itemsPerPage"
      />
    </div>

    <!-- Add Language Modal -->
    <Transition name="modal">
      <div v-if="showAddLanguageModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="cancelAddLanguage">
        <div :class="['w-full max-w-md mx-4 rounded-lg shadow-xl border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
          <div :class="['flex justify-between items-center p-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">添加语言</h3>
            <button @click="cancelAddLanguage" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
              <X :size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>

          <div class="p-6 space-y-4">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">语言代码</label>
              <input
                v-model="newLanguageForm.code"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="如：ja, ko, fr..."
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">语言名称</label>
              <input
                v-model="newLanguageForm.name"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="如：Japanese"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">本地化名称</label>
              <input
                v-model="newLanguageForm.native_name"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="如：日本語"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">旗帜图标</label>
              <input
                v-model="newLanguageForm.flag"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="如：🇯🇵"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">本地路径</label>
              <input
                v-model="newLanguageForm.file_path"
                :class="['w-full px-3 py-2 border rounded-lg font-mono text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="如：/locales/ja.json"
              />
            </div>
            <div class="flex gap-6">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="newLanguageForm.is_default"
                  type="checkbox"
                  class="w-4 h-4 rounded border-gray-300 text-construct-red focus:ring-construct-red"
                />
                <span :class="['text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">默认语言</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="newLanguageForm.is_active"
                  type="checkbox"
                  class="w-4 h-4 rounded border-gray-300 text-construct-red focus:ring-construct-red"
                />
                <span :class="['text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">启用</span>
              </label>
            </div>
          </div>

          <div :class="['flex justify-end gap-3 p-6 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <button
              @click="cancelAddLanguage"
              :class="['px-6 py-2 border font-bold text-xs uppercase tracking-wider transition-colors rounded-lg', isDarkMode ? 'border-gray-600 hover:bg-gray-700 text-gray-300' : 'border-gray-300 hover:bg-gray-100 text-gray-700']"
            >
              {{ t('admin_cancel') }}
            </button>
            <button
              @click="confirmAddLanguage"
              class="px-6 py-2 bg-construct-red text-white font-bold text-xs uppercase tracking-wider transition-colors rounded-lg"
            >
              {{ t('admin_create') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Edit Language Modal -->
    <Transition name="modal">
      <div v-if="showEditLanguageModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="cancelEditLanguage">
        <div :class="['w-full max-w-md mx-4 rounded-lg shadow-xl border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
          <div :class="['flex justify-between items-center p-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">编辑语言</h3>
            <button @click="cancelEditLanguage" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
              <X :size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>

          <div class="p-6 space-y-4">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">语言代码</label>
              <input
                v-model="editingLanguageForm.code"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="如：ja, ko, fr..."
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">语言名称</label>
              <input
                v-model="editingLanguageForm.name"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="如：Japanese"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">本地化名称</label>
              <input
                v-model="editingLanguageForm.native_name"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="如：日本語"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">旗帜图标</label>
              <input
                v-model="editingLanguageForm.flag"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="如：🇯🇵"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">本地路径</label>
              <input
                v-model="editingLanguageForm.file_path"
                :class="['w-full px-3 py-2 border rounded-lg font-mono text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="如：/locales/ja.json"
              />
            </div>
            <div class="flex gap-6">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="editingLanguageForm.is_default"
                  type="checkbox"
                  class="w-4 h-4 rounded border-gray-300 text-construct-red focus:ring-construct-red"
                />
                <span :class="['text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">默认语言</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="editingLanguageForm.is_active"
                  type="checkbox"
                  class="w-4 h-4 rounded border-gray-300 text-construct-red focus:ring-construct-red"
                />
                <span :class="['text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">启用</span>
              </label>
            </div>
          </div>

          <div :class="['flex justify-end gap-3 p-6 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <button
              @click="cancelEditLanguage"
              :class="['px-6 py-2 border font-bold text-xs uppercase tracking-wider transition-colors rounded-lg', isDarkMode ? 'border-gray-600 hover:bg-gray-700 text-gray-300' : 'border-gray-300 hover:bg-gray-100 text-gray-700']"
            >
              {{ t('admin_cancel') }}
            </button>
            <button
              @click="confirmEditLanguage"
              class="px-6 py-2 bg-construct-red text-white font-bold text-xs uppercase tracking-wider transition-colors rounded-lg"
            >
              {{ t('admin_save') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Edit Translation Modal -->
    <Transition name="modal">
      <div v-if="showEditTranslationModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="closeEditTranslation">
        <div :class="['w-full max-w-lg mx-4 rounded-lg shadow-xl border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
          <div :class="['flex justify-between items-center p-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">编辑翻译</h3>
            <button @click="closeEditTranslation" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
              <X :size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>

          <div class="p-6 space-y-4">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">翻译键</label>
              <div :class="['w-full px-3 py-2 border rounded-lg font-mono text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-construct-red' : 'bg-gray-100 border-gray-300 text-construct-red']">
                {{ editingKey }}
              </div>
            </div>
            <div v-for="lang in availableLanguages" :key="lang.code">
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                <span class="mr-1">{{ lang.flag }}</span>{{ lang.native_name }}
              </label>
              <textarea
                v-model="editingValues[lang.code]"
                rows="3"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
              ></textarea>
            </div>
          </div>

          <div :class="['flex justify-end gap-3 p-6 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <button
              @click="closeEditTranslation"
              :class="['px-6 py-2 border font-bold text-xs uppercase tracking-wider transition-colors rounded-lg', isDarkMode ? 'border-gray-600 hover:bg-gray-700 text-gray-300' : 'border-gray-300 hover:bg-gray-100 text-gray-700']"
            >
              {{ t('admin_cancel') }}
            </button>
            <button
              @click="saveTranslation"
              class="px-6 py-2 bg-construct-red text-white font-bold text-xs uppercase tracking-wider transition-colors rounded-lg"
            >
              {{ t('admin_save') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Add Translation Key Modal -->
    <Transition name="modal">
      <div v-if="showAddKeyModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="cancelAddKey">
        <div :class="['w-full max-w-lg mx-4 rounded-lg shadow-xl border', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
          <div :class="['flex justify-between items-center p-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">添加翻译键</h3>
            <button @click="cancelAddKey" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
              <X :size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>

          <div class="p-6 space-y-4">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">翻译键</label>
              <input
                v-model="newKeyForm.key"
                :class="['w-full px-3 py-2 border rounded-lg font-mono text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="如：nav_contact"
              />
            </div>
            <div v-for="lang in availableLanguages" :key="lang.code">
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                <span class="mr-1">{{ lang.flag }}</span>{{ lang.native_name }}
              </label>
              <textarea
                v-model="newKeyForm.values[lang.code]"
                rows="3"
                :class="['w-full px-3 py-2 border rounded-lg', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300']"
                placeholder="翻译内容..."
              ></textarea>
            </div>
          </div>

          <div :class="['flex justify-end gap-3 p-6 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <button
              @click="cancelAddKey"
              :class="['px-6 py-2 border font-bold text-xs uppercase tracking-wider transition-colors rounded-lg', isDarkMode ? 'border-gray-600 hover:bg-gray-700 text-gray-300' : 'border-gray-300 hover:bg-gray-100 text-gray-700']"
            >
              {{ t('admin_cancel') }}
            </button>
            <button
              @click="confirmAddKey"
              class="px-6 py-2 bg-construct-red text-white font-bold text-xs uppercase tracking-wider transition-colors rounded-lg"
            >
              {{ t('admin_create') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Confirm Dialog -->
    <ConfirmDialog
      v-model:visible="showDeleteConfirm"
      type="delete"
      :title="deletingItemType === 'language' ? '确认删除语言' : '确认删除翻译键'"
      :content="deletingItemType === 'language' ? '确定要删除该语言吗？此操作不可撤销。' : '确定要删除翻译键 \&quot;' + deletingItemId + '\&quot; 吗？此操作不可撤销。'"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />

    <!-- Top Center Error Toast -->
    <Transition name="top-toast">
      <div
        v-if="showTopError"
        class="fixed top-6 left-1/2 -translate-x-1/2 inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full shadow-lg backdrop-blur-xl border-red-500 bg-red-500/15 cursor-pointer"
        style="z-index: 1000;"
        @click="showTopError = false"
      >
        <AlertCircle class="flex-shrink-0 text-red-500" size="16" />
        <span class="text-sm font-medium text-center" :class="isDarkMode ? 'text-gray-200' : 'text-gray-800'">{{ topErrorMessage }}</span>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.top-toast-enter-active,
.top-toast-leave-active {
  transition: all 0.3s ease;
}

.top-toast-enter-from,
.top-toast-leave-to {
  opacity: 0;
  transform: translate(-50%, -20px);
}
</style>
