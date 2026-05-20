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
import {
  ref,
  computed,
  useI18n,
  useTheme,
  Plus,
  Search,
  Edit3,
  Trash2,
  Filter,
  Eye,
  X,
  CheckCircle,
  XCircle,
  Image as ImageIcon,
  Link as LinkIcon,
  Calendar,
  MousePointer,
  LayoutGrid,
  AlertCircle,
  Maximize,
  Sidebar,
  SkipForward,
  ExternalLink,
  TrendingUp,
  BarChart3,
  Check,
  ConfirmDialog,
  AdminPagination
} from '../../composables/useAdminImports';
import { Motion, AnimatePresence } from 'motion-v';
import { formatToShort } from '../../utils/dateUtils';
import { adPositions } from '../../data/ad_positions';
import { sampleAdvertisements } from '../../data/advertisements';

const { t } = useI18n();
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
  return pos ? t(pos.label_key) : position;
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
    ads.value = ads.value.filter(a => a.id !== deletingAdId.value);
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
    <!-- Header -->
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <ImageIcon class="text-construct-red" size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_advertisements') }}</h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_advertisements_subtitle') }}</p>
        </div>
        <button
          @click="handleAdd"
          class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
        >
          <Plus size="18" />
          {{ t('admin_add_ad') }}
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4 mb-10">
      <div class="flex-1 relative">
        <Search :class="['absolute left-4 top-1/2 -translate-y-1/2 transition-colors', isDarkMode ? 'text-gray-500' : 'text-gray-400']" size="20" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('admin_search_ad')"
          :class="[
            'w-full pl-12 pr-4 py-4 border focus:border-construct-red focus:outline-none transition-all rounded-2xl font-bold',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-500 shadow-inner' : 'bg-white border-gray-200 text-gray-900 placeholder-gray-400 shadow-sm'
          ]"
        />
      </div>
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
          <Filter :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" size="18" />
          <select
            v-model="positionFilter"
            :class="[
              'px-6 py-4 border focus:border-construct-red focus:outline-none transition-all rounded-2xl font-bold text-sm min-w-[160px]',
              isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-200 text-gray-900 shadow-sm'
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
            'px-6 py-4 border focus:border-construct-red focus:outline-none transition-all rounded-2xl font-bold text-sm min-w-[140px]',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-200 text-gray-900 shadow-sm'
          ]"
        >
          <option value="all">{{ t('admin_all_status') }}</option>
          <option value="active">{{ t('admin_active') }}</option>
          <option value="inactive">{{ t('admin_inactive') }}</option>
        </select>
      </div>
    </div>

    <!-- Ads Grid -->
    <div class="space-y-6">
      <AnimatePresence>
        <Motion
          v-for="ad in paginatedAds" 
          :key="ad.id"
          :initial="{ opacity: 0, y: 20 }"
          :animate="{ opacity: 1, y: 0 }"
          :exit="{ opacity: 0, scale: 0.95 }"
          :class="[
            'relative overflow-hidden group border p-6 transition-all duration-500 rounded-3xl',
            isDarkMode 
              ? 'bg-gray-800/40 border-gray-700/50 hover:border-construct-red/30 backdrop-blur-xl' 
              : 'bg-white border-gray-200 hover:border-construct-red/20 shadow-sm hover:shadow-xl'
          ]"
        >
          <!-- Active Status Bar -->
          <div 
            class="absolute left-0 top-6 bottom-6 w-1 rounded-r-full transition-all duration-500"
            :class="ad.is_active ? 'bg-green-500' : 'bg-red-500'"
          ></div>

          <div class="flex flex-col lg:flex-row items-start gap-8">
            <!-- Image Preview -->
            <div class="relative w-full lg:w-48 aspect-[4/3] lg:aspect-square rounded-2xl overflow-hidden flex-shrink-0 group/img shadow-lg">
              <img :src="ad.image_url" :alt="ad.title" class="w-full h-full object-cover transition-transform duration-700 group-hover/img:scale-110" />
              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                <ExternalLink class="text-white" size="24" />
              </div>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-4">
                <div class="space-y-1">
                  <div class="flex items-center gap-3">
                    <h3 :class="['font-display text-2xl tracking-tight truncate', isDarkMode ? 'text-white' : 'text-gray-900']">{{ ad.title }}</h3>
                    <div :class="[
                      'px-3 py-1 rounded-full text-[10px] font-black tracking-widest uppercase border flex items-center gap-1.5',
                      ad.is_active 
                        ? 'bg-green-500/10 text-green-500 border-green-500/20' 
                        : 'bg-red-500/10 text-red-500 border-red-500/20'
                    ]">
                      <div class="w-1.5 h-1.5 rounded-full" :class="ad.is_active ? 'bg-green-500 animate-pulse' : 'bg-red-500'"></div>
                      {{ ad.is_active ? t('admin_active') : t('admin_inactive') }}
                    </div>
                  </div>
                  <div :class="['flex items-center gap-2 text-xs font-bold uppercase tracking-wider', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                    <component :is="getPositionIcon(ad.position)" size="14" class="text-construct-red" />
                    {{ getPositionLabel(ad.position) }}
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <button
                    @click="toggleStatus(ad)"
                    :class="[
                      'p-3 rounded-xl transition-all duration-300 hover:scale-110',
                      isDarkMode ? 'hover:bg-gray-700 text-gray-500 hover:text-white' : 'hover:bg-gray-100 text-gray-400 hover:text-gray-900'
                    ]"
                  >
                    <MousePointer size="18" />
                  </button>
                  <button
                    @click="handleEdit(ad)"
                    :class="[
                      'p-3 rounded-xl transition-all duration-300 hover:scale-110',
                      isDarkMode ? 'hover:bg-gray-700 text-gray-500 hover:text-white' : 'hover:bg-gray-100 text-gray-400 hover:text-gray-900'
                    ]"
                  >
                    <Edit3 size="18" />
                  </button>
                  <button
                    @click="handleDelete(ad.id)"
                    :class="[
                      'p-3 rounded-xl transition-all duration-300 hover:scale-110',
                      isDarkMode ? 'hover:bg-red-500/10 text-gray-500 hover:text-red-400' : 'hover:bg-red-50 text-gray-400 hover:text-red-600'
                    ]"
                  >
                    <Trash2 size="18" />
                  </button>
                </div>
              </div>

              <!-- Link -->
              <div class="mb-6">
                <a 
                  :href="ad.link_url" 
                  target="_blank" 
                  :class="[
                    'inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium border transition-all truncate max-w-full',
                    isDarkMode ? 'bg-blue-500/5 border-blue-500/10 text-blue-400 hover:bg-blue-500/10' : 'bg-blue-50 border-blue-100 text-blue-600 hover:bg-blue-100'
                  ]"
                >
                  <LinkIcon size="14" />
                  {{ ad.link_url }}
                </a>
              </div>

              <!-- Stats & Info Grid -->
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div :class="['p-4 rounded-2xl border', isDarkMode ? 'bg-gray-900/30 border-gray-700/50' : 'bg-gray-50 border-gray-100']">
                  <div class="flex items-center gap-2 mb-1 opacity-50">
                    <Eye size="12" />
                    <span class="text-[10px] font-black uppercase tracking-widest">{{ t('admin_views') }}</span>
                  </div>
                  <div :class="['font-display text-lg font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ ad.views_count.toLocaleString() }}</div>
                </div>
                <div :class="['p-4 rounded-2xl border', isDarkMode ? 'bg-gray-900/30 border-gray-700/50' : 'bg-gray-50 border-gray-100']">
                  <div class="flex items-center gap-2 mb-1 opacity-50">
                    <MousePointer size="12" />
                    <span class="text-[10px] font-black uppercase tracking-widest">{{ t('admin_clicks') }}</span>
                  </div>
                  <div :class="['font-display text-lg font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ ad.clicks_count.toLocaleString() }}</div>
                </div>
                <div :class="['p-4 rounded-2xl border', isDarkMode ? 'bg-gray-900/30 border-gray-700/50' : 'bg-gray-50 border-gray-100']">
                  <div class="flex items-center gap-2 mb-1 opacity-50">
                    <TrendingUp size="12" />
                    <span class="text-[10px] font-black uppercase tracking-widest">CTR</span>
                  </div>
                  <div :class="['font-display text-lg font-bold text-construct-red']">{{ getCtr(ad) }}</div>
                </div>
                <div :class="['p-4 rounded-2xl border', isDarkMode ? 'bg-gray-900/30 border-gray-700/50' : 'bg-gray-50 border-gray-100']">
                  <div class="flex items-center gap-2 mb-1 opacity-50">
                    <Calendar size="12" />
                    <span class="text-[10px] font-black uppercase tracking-widest">{{ t('admin_table_date') }}</span>
                  </div>
                  <div :class="['text-[11px] font-bold leading-tight', isDarkMode ? 'text-white/60' : 'text-gray-600']">
                    {{ ad.start_date }} <br/>
                    <span class="opacity-30">{{ ad.end_date || 'Ongoing' }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </Motion>
      </AnimatePresence>
    </div>

    <!-- Empty State -->
    <div v-if="filteredAds.length === 0" :class="['text-center py-24 rounded-3xl border-2 border-dashed', isDarkMode ? 'border-gray-700 text-gray-500' : 'border-gray-100 text-gray-400']">
      <ImageIcon class="mx-auto mb-4 opacity-20" size="64" stroke-width="1" />
      <p class="font-display text-xl">{{ t('admin_no_ads') }}</p>
    </div>

    <!-- Pagination -->
    <div class="mt-10">
      <AdminPagination
        :current-page="currentPage"
        :total-pages="totalPages"
        :total-items="filteredAds.length"
        v-model:items-per-page="itemsPerPage"
        @update:current-page="currentPage = $event"
      />
    </div>

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
      type="delete"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.05);
}
</style>

