<script setup>
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import {
  Languages,
  Plus,
  Edit3,
  Trash2,
  X,
  Globe,
  Download,
  Upload,
  AlertCircle
} from 'lucide-vue-next';
import { Motion } from 'motion-v';
import { useTheme } from '../../composables/useTheme';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import SearchFilterModal from '../../components/admin/SearchFilterModal.vue';
import Pagination from '../../components/admin/Pagination.vue';
import { useToast } from '../../composables/useToast';

const props = defineProps({
  i18nConfig: { type: Object, default: () => ({}) },
});

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success, error: toastError } = useToast();

// ─── State ──────────────────────────────────────────────────────
const languages = ref([]);
const translations = ref({});
const activeTab = ref('languages');
const activeLanguage = ref('en');
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);
const isSaving = ref(false); // 用于单个翻译保存/添加/删除的 loading 状态

const showTopError = ref(false);
const topErrorMessage = ref('');

const showTopErrorToast = (message) => {
  topErrorMessage.value = message;
  showTopError.value = true;
  setTimeout(() => { showTopError.value = false; }, 3000);
};

// ─── Init from props ────────────────────────────────────────────
const initData = () => {
  languages.value = [...(props.i18nConfig?.languages || [])];
  translations.value = { ...(props.i18nConfig?.translations || {}) };

  // 确保每个语言都有翻译对象
  languages.value.forEach(lang => {
    if (!translations.value[lang.code]) {
      translations.value[lang.code] = {};
    }
  });
};

onMounted(() => initData());

// ─── Computed ───────────────────────────────────────────────────
const availableLanguages = computed(() =>
  languages.value.filter(lang => lang.is_active)
);

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

// ─── 持久化翻译到文件 ──────────────────────────────────────────
const persistTranslations = async () => {
  isSaving.value = true;
  try {
    await axios.put('/admin/i18n/translations', {
      translations: translations.value,
    });
    return true;
  } catch (err) {
    showTopErrorToast('保存失败：' + (err.response?.data?.message || err.message));
    return false;
  } finally {
    isSaving.value = false;
  }
};

// ─── Translations CRUD ──────────────────────────────────────────
const showTranslationModal = ref(false);
const isEditingTranslation = ref(false);
const translationForm = ref({ key: '', values: {} });

const openTranslationModal = (key = null) => {
  if (key) {
    isEditingTranslation.value = true;
    translationForm.value = { key, values: {} };
    availableLanguages.value.forEach(lang => {
      translationForm.value.values[lang.code] = translations.value[lang.code]?.[key] || '';
    });
  } else {
    isEditingTranslation.value = false;
    translationForm.value = { key: '', values: {} };
    availableLanguages.value.forEach(lang => {
      translationForm.value.values[lang.code] = '';
    });
  }
  showTranslationModal.value = true;
};

const cancelTranslationModal = () => {
  showTranslationModal.value = false;
};

const confirmTranslation = async () => {
  const { key, values } = translationForm.value;
  if (!key.trim()) {
    showTopErrorToast('请输入键名');
    return;
  }
  Object.entries(values).forEach(([code, value]) => {
    if (!translations.value[code]) translations.value[code] = {};
    if (value === '' || value === null) {
      delete translations.value[code][key.trim()];
    } else {
      translations.value[code][key.trim()] = value;
    }
  });
  cancelTranslationModal();
  await persistTranslations();
  success(isEditingTranslation.value ? '翻译已保存' : '翻译键已添加并保存');
};

// ─── Delete translation ─────────────────────────────────────────
const showDeleteConfirm = ref(false);
const deletingItemId = ref(null);
const deletingItemType = ref('');

const deleteTranslationKey = (key) => {
  deletingItemId.value = key;
  deletingItemType.value = 'translation';
  showDeleteConfirm.value = true;
};

const confirmDelete = async () => {
  if (deletingItemType.value === 'language') {
    handleDeleteLanguage(deletingItemId.value);
  } else if (deletingItemType.value === 'translation') {
    Object.keys(translations.value).forEach(code => {
      delete translations.value[code][deletingItemId.value];
    });
    await persistTranslations();
    success('翻译键已删除');
  }
  showDeleteConfirm.value = false;
  deletingItemId.value = null;
  deletingItemType.value = '';
};

// ─── Languages CRUD ─────────────────────────────────────────────
const showLanguageModal = ref(false);
const isEditingLanguage = ref(false);
const languageForm = ref({ code: '', name: '', native_name: '', flag: '', file_path: '', is_default: false, is_active: true });
const isUploading = ref(false);
const fileInputRef = ref(null);

const openAddLanguageModal = () => {
  isEditingLanguage.value = false;
  languageForm.value = { code: '', name: '', native_name: '', flag: '', file_path: '', is_default: false, is_active: true };
  showLanguageModal.value = true;
};

const openEditLanguageModal = (lang) => {
  isEditingLanguage.value = true;
  languageForm.value = {
    code: lang.code,
    name: lang.name,
    native_name: lang.native_name,
    flag: lang.flag,
    file_path: lang.file_path || '',
    is_default: lang.is_default,
    is_active: lang.is_active,
  };
  showLanguageModal.value = true;
};

const cancelLanguageModal = () => {
  showLanguageModal.value = false;
};

const handleLocaleUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const code = languageForm.value.code.trim();
  if (!code) {
    showTopErrorToast('请先输入语言代码，再上传文件');
    event.target.value = '';
    return;
  }

  isUploading.value = true;
  try {
    const formData = new FormData();
    formData.append('code', code);
    formData.append('file', file);

    const res = await axios.post('/admin/i18n/upload-locale', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    languageForm.value.file_path = res.data.file_path;
    success('文件已上传，路径已自动填入');
  } catch (err) {
    showTopErrorToast('上传失败：' + (err.response?.data?.message || err.message));
  } finally {
    isUploading.value = false;
    event.target.value = '';
  }
};

const confirmLanguage = async () => {
  if (!languageForm.value.name.trim()) return showTopErrorToast('请输入语言名称');

  if (isEditingLanguage.value) {
    try {
      const code = languageForm.value.code;
      await axios.put(`/admin/i18n/languages/${code}`, {
        name: languageForm.value.name.trim(),
        native_name: languageForm.value.native_name.trim() || languageForm.value.name.trim(),
        flag: languageForm.value.flag.trim() || '🌐',
        file_path: languageForm.value.file_path.trim() || null,
        is_default: languageForm.value.is_default,
        is_active: languageForm.value.is_active,
      });
      // 局部更新本地状态，无需全量刷新
      const idx = languages.value.findIndex(l => l.code === code);
      if (idx > -1) {
        languages.value[idx] = {
          ...languages.value[idx],
          name: languageForm.value.name.trim(),
          native_name: languageForm.value.native_name.trim() || languageForm.value.name.trim(),
          flag: languageForm.value.flag.trim() || '🌐',
          file_path: languageForm.value.file_path.trim() || null,
          is_default: languageForm.value.is_default,
          is_active: languageForm.value.is_active,
        };
      }
      success('语言已更新');
      cancelLanguageModal();
    } catch (err) {
      showTopErrorToast(err.response?.data?.message || '更新失败');
    }
  } else {
    if (!languageForm.value.code.trim()) return showTopErrorToast('请输入语言代码');
    try {
      const res = await axios.post('/admin/i18n/languages', languageForm.value);
      // 局部更新：直接插入新语言，无需全量刷新
      const newLang = { ...res.data.language, translation_count: 0 };
      languages.value.push(newLang);
      translations.value[newLang.code] = {};
      success('语言已添加');
      cancelLanguageModal();
    } catch (err) {
      showTopErrorToast(err.response?.data?.message || '添加失败');
    }
  }
};

const handleDeleteLanguage = async (code) => {
  try {
    await axios.delete(`/admin/i18n/languages/${code}`);
    // 局部更新：从本地数组中移除，无需全量刷新
    languages.value = languages.value.filter(l => l.code !== code);
    delete translations.value[code];
    success('语言已删除');
  } catch (err) {
    showTopErrorToast(err.response?.data?.message || '删除失败');
  }
};

const downloadLanguage = (lang) => {
  const data = JSON.stringify(translations.value[lang.code] || {}, null, 2);
  const blob = new Blob([data], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `${lang.code}.json`;
  a.click();
  URL.revokeObjectURL(url);
};

const handleDelete = (code, type) => {
  deletingItemId.value = code;
  deletingItemType.value = type;
  showDeleteConfirm.value = true;
};

const deleteTitle = computed(() =>
  deletingItemType.value === 'language' ? '确认删除语言' : '确认删除翻译键'
);

const deleteContent = computed(() =>
  deletingItemType.value === 'language'
    ? '确定要删除该语言吗？此操作不可撤销。'
    : `确定要删除翻译键 "${deletingItemId.value}" 吗？此操作不可撤销。`
);

// ─── Tabs ────────────────────────────────────────────────────────
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
            v-if="activeTab === 'translations'"
            @click="openTranslationModal()"
            class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
          >
            <Plus size="18" :style="{ color: '#ffffff' }" />
            添加翻译键
          </button>
          <button
            v-if="activeTab === 'languages'"
            @click="openAddLanguageModal"
            class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
          >
            <Plus size="18" :style="{ color: '#ffffff' }" />
            添加语言
          </button>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div :class="['flex gap-2 mb-8', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        @click="activeTab = tab.key; currentPage = 1; searchQuery = ''"
        :class="[
          'relative flex items-center gap-2.5 px-5 py-2.5 font-bold text-sm rounded-xl transition-all duration-300',
          activeTab === tab.key
            ? isDarkMode
              ? 'bg-construct-red/15 text-construct-red shadow-sm'
              : 'bg-construct-red text-white shadow-md shadow-construct-red/25 scale-105'
            : isDarkMode
              ? 'text-gray-500 hover:text-gray-200 hover:bg-gray-800'
              : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'
        ]"
      >
        <component :is="tab.icon" size="16" />
        {{ tab.label }}
      </button>
    </div>

    <!-- Languages Tab -->
    <div v-if="activeTab === 'languages'">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <Motion
          v-for="(lang, idx) in languages"
          :key="lang.code"
          :initial="{ opacity: 0, y: 20 }"
          :animate="{ opacity: 1, y: 0 }"
          :transition="{ delay: idx * 0.05, duration: 0.35 }"
        >
          <div
            :class="[
              'group border transition-all duration-300 rounded-xl overflow-hidden hover:-translate-y-1.5 hover:shadow-xl',
              isDarkMode
                ? 'bg-gray-800/80 border-gray-700/50 hover:border-construct-red/50 hover:shadow-construct-red/5'
                : 'bg-white border-gray-200 hover:border-construct-red/30 hover:shadow-construct-red/10'
            ]"
          >
            <div class="p-6">
              <div class="flex items-start justify-between mb-4">
                <div :class="[
                  'w-12 h-12 rounded-xl flex items-center justify-center text-3xl shadow-md transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg',
                  isDarkMode ? 'bg-gradient-to-br from-gray-700 to-gray-800' : 'bg-gradient-to-br from-gray-100 to-gray-200'
                ]">
                  {{ lang.flag }}
                </div>
                <div class="flex items-center gap-1">
                  <span v-if="lang.is_default" class="px-2 py-1 text-xs font-bold rounded bg-construct-red/20 text-construct-red">默认</span>
                  <span :class="['px-2 py-1 text-xs font-bold rounded', lang.is_active ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400']">
                    {{ lang.is_active ? '已启用' : '已禁用' }}
                  </span>
                </div>
              </div>
              <h4 :class="['font-bold text-lg mb-1', isDarkMode ? 'text-white' : 'text-gray-900']">{{ lang.name }}</h4>
              <p :class="['text-sm mb-1', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ lang.native_name }}</p>
              <p :class="['text-xs font-mono font-bold', isDarkMode ? 'text-gray-600' : 'text-gray-400']">代码: {{ lang.code }}</p>
              <div class="flex items-center justify-between mt-4">
                <span :class="['text-xs font-mono font-bold', isDarkMode ? 'text-gray-600' : 'text-gray-400']">
                  翻译数: {{ lang.translation_count || Object.keys(translations[lang.code] || {}).length }}
                </span>
                <div class="flex items-center gap-2">
                  <button @click="downloadLanguage(lang)" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-blue-400' : 'text-gray-400 hover:bg-gray-100 hover:text-blue-500']" :title="'下载 ' + lang.code + '.json'">
                    <Download size="14" />
                  </button>
                  <button @click="openEditLanguageModal(lang)" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-construct-red' : 'text-gray-400 hover:bg-gray-100 hover:text-construct-red']">
                    <Edit3 size="14" />
                  </button>
                  <button @click="handleDelete(lang.code, 'language')" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-red-400' : 'text-gray-400 hover:bg-gray-100 hover:text-red-500']">
                    <Trash2 size="14" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </Motion>
      </div>
    </div>

    <!-- Translations Tab -->
    <div v-if="activeTab === 'translations'">
      <div class="mb-6">
        <SearchFilterModal
          v-model:search-query="searchQuery"
          :search-placeholder="'搜索翻译键或值...'"
        />
      </div>

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
              <td :class="['px-4 py-3 font-mono text-xs max-w-[160px] truncate', isDarkMode ? 'text-construct-red' : 'text-construct-red']" :title="key">{{ key }}</td>
              <td
                v-for="lang in availableLanguages"
                :key="lang.code"
                :class="['px-4 py-3 max-w-[180px]', isDarkMode ? 'text-gray-300' : 'text-gray-700']"
              >
                <span
                  :class="['block truncate', { 'opacity-40': !translations[lang.code]?.[key] }]"
                  :title="translations[lang.code]?.[key] || ''"
                >
                  {{ translations[lang.code]?.[key] || '—' }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <button @click="openTranslationModal(key)" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-construct-red' : 'text-gray-400 hover:bg-gray-100 hover:text-construct-red']">
                    <Edit3 size="14" />
                  </button>
                  <button @click="deleteTranslationKey(key)" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-red-400' : 'text-gray-400 hover:bg-gray-100 hover:text-red-500']">
                    <Trash2 size="14" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination
        v-model:current-page="currentPage"
        :total-pages="totalPages"
        :total-items="filteredTranslationKeys.length"
        :items-per-page="itemsPerPage"
      />
    </div>

    <!-- Language Modal (Add/Edit) -->
    <Transition name="modal">
      <div v-if="showLanguageModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="cancelLanguageModal">
        <div :class="['w-full max-w-md mx-4 rounded-2xl shadow-2xl ring-1', isDarkMode ? 'bg-gray-800 ring-gray-700' : 'bg-white ring-gray-200/80']">
          <div :class="['flex justify-between items-center p-6 pb-2', isDarkMode ? 'text-white' : 'text-gray-900']">
            <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ isEditingLanguage ? '编辑语言' : '添加语言' }}</h3>
            <button @click="cancelLanguageModal" :class="['p-2 rounded-xl transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
              <X :size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>
          <div class="p-6 space-y-4">
            <div>
              <label :class="['block text-sm font-bold mb-1.5', isDarkMode ? 'text-gray-300' : 'text-gray-700']">语言代码</label>
              <input
                v-if="!isEditingLanguage"
                v-model="languageForm.code"
                :class="['w-full px-3 py-2 border rounded-xl text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-white focus:border-construct-red' : 'border-gray-300 focus:border-construct-red']"
                placeholder="如：ja, ko, fr..."
              />
              <input
                v-else
                :value="languageForm.code"
                disabled
                :class="['w-full px-3 py-2 border rounded-xl opacity-50 text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-gray-400' : 'bg-gray-100 border-gray-300 text-gray-500']"
              />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-1.5', isDarkMode ? 'text-gray-300' : 'text-gray-700']">语言名称</label>
              <input v-model="languageForm.name" :class="['w-full px-3 py-2 border rounded-xl text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-white focus:border-construct-red' : 'border-gray-300 focus:border-construct-red']" placeholder="如：Japanese" />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-1.5', isDarkMode ? 'text-gray-300' : 'text-gray-700']">本地化名称</label>
              <input v-model="languageForm.native_name" :class="['w-full px-3 py-2 border rounded-xl text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-white focus:border-construct-red' : 'border-gray-300 focus:border-construct-red']" placeholder="如：日本語" />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-1.5', isDarkMode ? 'text-gray-300' : 'text-gray-700']">旗帜图标</label>
              <input v-model="languageForm.flag" :class="['w-full px-3 py-2 border rounded-xl text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-white focus:border-construct-red' : 'border-gray-300 focus:border-construct-red']" placeholder="如：🇯🇵" />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-1.5', isDarkMode ? 'text-gray-300' : 'text-gray-700']">文件路径</label>
              <div class="flex gap-2">
                <input v-model="languageForm.file_path" :class="['flex-1 px-3 py-2 border rounded-xl font-mono text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-white focus:border-construct-red' : 'border-gray-300 focus:border-construct-red']" placeholder="如：/locales/ja.json 或点击上传" />
                <input ref="fileInputRef" type="file" accept=".json" class="hidden" @change="handleLocaleUpload" />
                <button
                  @click="fileInputRef?.click()"
                  :disabled="isUploading"
                  :class="['flex items-center gap-1.5 px-3 py-2 border rounded-xl font-bold text-xs uppercase tracking-wider transition-colors whitespace-nowrap', isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-600 hover:bg-gray-100']"
                >
                  <Upload size="14" />
                  {{ isUploading ? '上传中...' : '上传' }}
                </button>
              </div>
            </div>
            <div class="flex gap-6">
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="languageForm.is_default" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-construct-red focus:ring-construct-red" />
                <span :class="['text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">默认语言</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="languageForm.is_active" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-construct-red focus:ring-construct-red" />
                <span :class="['text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">启用</span>
              </label>
            </div>
          </div>
          <div :class="['flex justify-end gap-3 p-6 pt-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            <button @click="cancelLanguageModal" :class="['px-6 py-2 border font-bold text-xs uppercase tracking-wider transition-colors rounded-xl', isDarkMode ? 'border-gray-600 hover:bg-gray-700 text-gray-300' : 'border-gray-300 hover:bg-gray-100 text-gray-700']">取消</button>
            <button @click="confirmLanguage" class="px-6 py-2 bg-construct-red text-white font-bold text-xs uppercase tracking-wider transition-colors rounded-xl">{{ isEditingLanguage ? '保存' : '创建' }}</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Translation Modal (Add/Edit) -->
    <Transition name="modal">
      <div v-if="showTranslationModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="cancelTranslationModal">
        <div :class="['w-full max-w-lg mx-4 rounded-2xl shadow-2xl ring-1 max-h-[90vh] flex flex-col', isDarkMode ? 'bg-gray-800 ring-gray-700' : 'bg-white ring-gray-200/80']">
          <div :class="['flex justify-between items-center p-6 pb-2 shrink-0', isDarkMode ? 'text-white' : 'text-gray-900']">
            <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ isEditingTranslation ? '编辑翻译' : '添加翻译键' }}</h3>
            <button @click="cancelTranslationModal" :class="['p-2 rounded-xl transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
              <X :size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>
          <div class="p-6 space-y-4 overflow-y-auto flex-1">
            <div>
              <label :class="['block text-sm font-bold mb-1.5', isDarkMode ? 'text-gray-300' : 'text-gray-700']">翻译键</label>
              <input
                v-if="!isEditingTranslation"
                v-model="translationForm.key"
                :class="['w-full px-3 py-2 border rounded-xl font-mono text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-white focus:border-construct-red' : 'border-gray-300 focus:border-construct-red']"
                placeholder="如：nav_contact"
              />
              <div
                v-else
                :class="['w-full px-3 py-2 border rounded-xl font-mono text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-construct-red' : 'bg-gray-100 border-gray-300 text-construct-red']"
              >{{ translationForm.key }}</div>
            </div>
            <div v-for="lang in availableLanguages" :key="lang.code">
              <label :class="['block text-sm font-bold mb-1.5', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                <span class="mr-1">{{ lang.flag }}</span>{{ lang.native_name }}
              </label>
              <textarea
                v-model="translationForm.values[lang.code]"
                rows="2"
                :class="['w-full px-3 py-2 border rounded-xl resize-none text-sm', isDarkMode ? 'bg-gray-700 border-gray-600 text-white focus:border-construct-red' : 'border-gray-300 focus:border-construct-red']"
                :placeholder="isEditingTranslation ? undefined : '翻译内容...'"
              ></textarea>
            </div>
          </div>
          <div :class="['flex justify-end gap-3 p-6 pt-2 shrink-0', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            <button @click="cancelTranslationModal" :class="['px-6 py-2 border font-bold text-xs uppercase tracking-wider transition-colors rounded-xl', isDarkMode ? 'border-gray-600 hover:bg-gray-700 text-gray-300' : 'border-gray-300 hover:bg-gray-100 text-gray-700']">取消</button>
            <button @click="confirmTranslation" class="px-6 py-2 bg-construct-red text-white font-bold text-xs uppercase tracking-wider transition-colors rounded-xl">{{ isEditingTranslation ? '保存' : '创建' }}</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Confirm Dialog -->
    <ConfirmDialog
      v-model:visible="showDeleteConfirm"
      type="delete"
      :title="deleteTitle"
      :content="deleteContent"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />

    <!-- Top Error Toast -->
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
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
.top-toast-enter-active, .top-toast-leave-active {
  transition: all 0.3s ease;
}
.top-toast-enter-from, .top-toast-leave-to {
  opacity: 0;
  transform: translate(-50%, -20px);
}
</style>
