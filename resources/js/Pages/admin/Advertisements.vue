<script setup>
/**
 * AdminAdvertisements.vue - 广告管理页面
 * 
 * 功能说明：
 * - 管理网站广告位
 * - 广告列表展示（标题、图片、位置、状态、点击量）
 * - 广告搜索和筛选
 * - 添加、编辑、删除广告
 * - 广告状态管理（启用/禁用）
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Plus,
  Search,
  Edit3,
  Trash2,
  Filter,
  Eye,
  X,
  CheckCircle,
  XCircle,
  Image,
  Link,
  Calendar,
  MousePointer,
  LayoutGrid,
  AlertCircle,
  Maximize,
  Sidebar,
  SkipForward
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import AdminPagination from '../../components/admin/AdminPagination.vue';
import { formatToShort } from '../../utils/dateUtils';
import { adPositions, sampleAdvertisements } from '../../data/advertisements';

const { t, locale } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const positionFilter = ref('all');
const statusFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const showFormModal = ref(false);
const editingAd = ref(null);
const showDeleteConfirm = ref(false);
const deletingAdId = ref(null);

const ads = ref([...sampleAdvertisements]);

const filteredAds = computed(() => {
  return ads.value.filter(ad => {
    const matchesSearch = ad.title.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesPosition = positionFilter.value === 'all' || ad.position === positionFilter.value;
    const matchesStatus = statusFilter.value === 'all' || 
      (statusFilter.value === 'active' && ad.is_active) ||
      (statusFilter.value === 'inactive' && !ad.is_active);
    return matchesSearch && matchesPosition && matchesStatus;
  });
});

const totalPages = computed(() => Math.ceil(filteredAds.value.length / itemsPerPage.value));

const paginatedAds = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredAds.value.slice(start, start + itemsPerPage.value);
});

const getPositionIcon = (position) => {
  const icons = {
    header: Maximize,
    sidebar: Sidebar,
    footer: SkipForward,
    between_posts: LayoutGrid,
    popup: AlertCircle
  };
  return icons[position] || LayoutGrid;
};

const getPositionLabel = (position) => {
  const pos = adPositions.find(p => p.value === position);
  return pos ? t(pos.labelKey) : position;
};

const toggleStatus = (ad) => {
  ad.is_active = !ad.is_active;
};

const handleEdit = (ad) => {
  editingAd.value = { ...ad };
  showFormModal.value = true;
};

const handleAdd = () => {
  editingAd.value = {
    title: '',
    image_url: '',
    link_url: '',
    position: 'sidebar',
    is_active: true,
    start_date: new Date().toISOString().split('T')[0],
    end_date: ''
  };
  showFormModal.value = true;
};

const handleSave = (data) => {
  if (editingAd.value && editingAd.value.id) {
    const index = ads.value.findIndex(a => a.id === editingAd.value.id);
    if (index !== -1) {
      ads.value[index] = { ...ads.value[index], ...data };
    }
  } else {
    const newId = Math.max(...ads.value.map(a => a.id), 0) + 1;
    ads.value.push({
      id: newId,
      ...data,
      clicks_count: 0,
      views_count: 0
    });
  }
  showFormModal.value = false;
  editingAd.value = null;
};

const handleCancel = () => {
  showFormModal.value = false;
  editingAd.value = null;
};

const handleDelete = (id) => {
  deletingAdId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingAdId.value !== null) {
    const index = ads.value.findIndex(a => a.id === deletingAdId.value);
    if (index !== -1) {
      ads.value.splice(index, 1);
    }
    deletingAdId.value = null;
  }
  showDeleteConfirm.value = false;
};

const getCtr = (ad) => {
  if (ad.views_count === 0) return '0%';
  return ((ad.clicks_count / ad.views_count) * 100).toFixed(2) + '%';
};
</script>

<template>
  <div class="p-8">
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Image class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_advertisements') }}</h2>
          </div>
          <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_advertisements_subtitle') }}</p>
        </div>
        <button
          @click="handleAdd"
          :class="[
            'flex items-center gap-2 px-6 py-3 font-bold uppercase tracking-wider transition-all hover:opacity-90',
            isDarkMode ? 'bg-construct-red text-white' : 'bg-construct-red text-white'
          ]"
        >
          <Plus size="20" />
          {{ t('admin_add_ad') }}
        </button>
      </div>
    </div>

    <div class="flex flex-col md:flex-row gap-4 mb-8">
      <div class="flex-1 relative">
        <Search :class="['absolute left-4 top-1/2 -translate-y-1/2', isDarkMode ? 'text-gray-400' : 'text-gray-500']" size="20" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('admin_search_ad')"
          :class="[
            'w-full pl-12 pr-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'
          ]"
        />
      </div>
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
          <Filter :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" size="18" />
          <select
            v-model="positionFilter"
            :class="[
              'px-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
              isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option value="all">{{ t('admin_all_positions') }}</option>
            <option value="header">{{ t('admin_ad_position_header') }}</option>
            <option value="sidebar">{{ t('admin_ad_position_sidebar') }}</option>
            <option value="footer">{{ t('admin_ad_position_footer') }}</option>
            <option value="between_posts">{{ t('admin_ad_position_between_posts') }}</option>
            <option value="popup">{{ t('admin_ad_position_popup') }}</option>
          </select>
        </div>
        <select
          v-model="statusFilter"
          :class="[
            'px-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
          ]"
        >
          <option value="all">{{ t('admin_all_status') }}</option>
          <option value="active">{{ t('admin_active') }}</option>
          <option value="inactive">{{ t('admin_inactive') }}</option>
        </select>
      </div>
    </div>

    <div class="space-y-4">
      <div 
        v-for="ad in paginatedAds" 
        :key="ad.id"
        :class="[
          'p-6 border transition-all hover:border-construct-red',
          isDarkMode ? 'bg-gray-800/50 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <div class="flex items-start gap-4">
          <div :class="['w-32 h-24 rounded overflow-hidden flex-shrink-0', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
            <img :src="ad.image_url" :alt="ad.title" class="w-full h-full object-cover" />
          </div>
          <div class="flex-1">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center gap-3">
                <span :class="['font-bold text-lg', isDarkMode ? 'text-white' : 'text-gray-900']">{{ ad.title }}</span>
                <span :class="[
                  'flex items-center gap-1 px-2 py-1 text-xs font-bold rounded',
                  ad.is_active 
                    ? 'bg-green-500/20 text-green-500' 
                    : 'bg-red-500/20 text-red-500'
                ]">
                  <component :is="ad.is_active ? CheckCircle : XCircle" size="14" />
                  {{ ad.is_active ? t('admin_active') : t('admin_inactive') }}
                </span>
                <span :class="['flex items-center gap-1 px-2 py-1 text-xs font-bold rounded', isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600']">
                  <component :is="getPositionIcon(ad.position)" size="14" />
                  {{ getPositionLabel(ad.position) }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <button
                  @click="toggleStatus(ad)"
                  :class="[
                    'p-2 transition-colors',
                    isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-900'
                  ]"
                  :title="ad.is_active ? t('admin_deactivate') : t('admin_activate')"
                >
                  <MousePointer size="18" />
                </button>
                <button
                  @click="handleEdit(ad)"
                  :class="[
                    'p-2 transition-colors',
                    isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-900'
                  ]"
                  :title="t('admin_edit')"
                >
                  <Edit3 size="18" />
                </button>
                <button
                  @click="handleDelete(ad.id)"
                  :class="[
                    'p-2 transition-colors',
                    isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-red-400' : 'hover:bg-gray-100 text-gray-500 hover:text-red-600'
                  ]"
                  :title="t('admin_delete')"
                >
                  <Trash2 size="18" />
                </button>
              </div>
            </div>
            <div class="flex items-center gap-4 mb-2">
              <a :href="ad.link_url" target="_blank" :class="['flex items-center gap-1 text-sm', isDarkMode ? 'text-blue-400 hover:text-blue-300' : 'text-blue-600 hover:text-blue-500']">
                <Link size="14" />
                {{ ad.link_url }}
              </a>
            </div>
            <div class="flex items-center gap-6 text-sm">
              <div class="flex items-center gap-2">
                <Eye :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="14" />
                <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                  {{ t('admin_views') }}: {{ ad.views_count.toLocaleString() }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <MousePointer :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="14" />
                <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                  {{ t('admin_clicks') }}: {{ ad.clicks_count.toLocaleString() }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                  CTR: {{ getCtr(ad) }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <Calendar :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="14" />
                <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                  {{ ad.start_date }} ~ {{ ad.end_date }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="filteredAds.length === 0" :class="['text-center py-12', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
      <Image :class="['mx-auto mb-4 opacity-50', isDarkMode ? 'text-gray-600' : 'text-gray-400']" size="48" />
      <p>{{ t('admin_no_ads') }}</p>
    </div>

    <AdminPagination
      :current-page="currentPage"
      :total-pages="totalPages"
      :total-items="filteredAds.length"
      :items-per-page="itemsPerPage"
      @update:currentPage="currentPage = $event"
      @update:itemsPerPage="itemsPerPage = $event"
    />

    <Teleport to="body">
      <div 
        v-if="showFormModal" 
        :class="['fixed inset-0 z-50 flex items-center justify-center bg-black/50', isDarkMode ? 'bg-black/70' : 'bg-black/50']"
        @click.self="handleCancel"
      >
        <div :class="[
          'w-full max-w-lg mx-4 p-6 rounded-lg shadow-xl',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between mb-6">
            <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">
              {{ editingAd && editingAd.id ? t('admin_edit_ad') : t('admin_add_ad') }}
            </h3>
            <button
              @click="handleCancel"
              :class="['p-2 transition-colors', isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500']"
            >
              <X size="20" />
            </button>
          </div>

          <form @submit.prevent="handleSave(editingAd)" class="space-y-4">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_ad_title') }} *
              </label>
              <input
                v-model="editingAd.title"
                type="text"
                required
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_ad_image_url') }} *
              </label>
              <input
                v-model="editingAd.image_url"
                type="url"
                required
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
              />
            </div>

            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                {{ t('admin_ad_link_url') }} *
              </label>
              <input
                v-model="editingAd.link_url"
                type="url"
                required
                :class="[
                  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_ad_position') }} *
                </label>
                <select
                  v-model="editingAd.position"
                  :class="[
                    'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                  ]"
                >
                  <option value="header">{{ t('admin_ad_position_header') }}</option>
                  <option value="sidebar">{{ t('admin_ad_position_sidebar') }}</option>
                  <option value="footer">{{ t('admin_ad_position_footer') }}</option>
                  <option value="between_posts">{{ t('admin_ad_position_between_posts') }}</option>
                  <option value="popup">{{ t('admin_ad_position_popup') }}</option>
                </select>
              </div>

              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_status') }}
                </label>
                <div class="flex items-center gap-4 mt-2">
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input
                      type="radio"
                      v-model="editingAd.is_active"
                      :value="true"
                      class="w-4 h-4 text-construct-red focus:ring-construct-red"
                    />
                    <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ t('admin_active') }}</span>
                  </label>
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input
                      type="radio"
                      v-model="editingAd.is_active"
                      :value="false"
                      class="w-4 h-4 text-construct-red focus:ring-construct-red"
                    />
                    <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ t('admin_inactive') }}</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_start_date') }}
                </label>
                <input
                  v-model="editingAd.start_date"
                  type="date"
                  :class="[
                    'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                  ]"
                />
              </div>

              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                  {{ t('admin_end_date') }}
                </label>
                <input
                  v-model="editingAd.end_date"
                  type="date"
                  :class="[
                    'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red',
                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                  ]"
                />
              </div>
            </div>

            <div class="flex items-center gap-4 pt-4">
              <button
                type="button"
                @click="handleCancel"
                :class="[
                  'flex-1 px-6 py-3 font-bold uppercase tracking-wider border transition-colors',
                  isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-100'
                ]"
              >
                {{ t('admin_cancel') }}
              </button>
              <button
                type="submit"
                class="flex-1 px-6 py-3 font-bold uppercase tracking-wider bg-construct-red text-white hover:bg-red-700 transition-colors"
              >
                {{ t('admin_save') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <ConfirmDialog
      :visible="showDeleteConfirm"
      :title="t('admin_delete_ad_title')"
      :content="t('admin_delete_ad_confirm')"
      :confirm-text="t('admin_delete')"
      :cancel-text="t('admin_cancel')"
      confirm-variant="danger"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>
