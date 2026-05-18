<script setup>
/**
 * AdminSocialLinks.vue - 社交媒体链接管理页面
 * 
 * 功能说明：
 * - 管理作者页面的社交媒体链接
 * - 支持添加、编辑、删除社交媒体链接
 * - 支持链接排序和样式配置
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Link,
  Plus,
  Search,
  Edit3,
  Trash2,
  Filter,
  Github,
  Twitter,
  Linkedin,
  Mail,
  ArrowUp,
  ArrowDown
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { socialLinks } from '../../data/author';
import ContentForm from '../../components/admin/ContentForm.vue';
import ConfirmDialog from '../../components/admin/ConfirmDialog.vue';
import AdminPagination from '../../components/admin/AdminPagination.vue';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const selectedPlatform = ref('all');
const isFormVisible = ref(false);
const editingLink = ref(null);
const showDeleteConfirm = ref(false);
const deletingLinkId = ref(null);

const links = ref([...socialLinks]);

const platforms = ['all', 'github', 'twitter', 'linkedin', 'email'];

const platformIcons = {
  github: Github,
  twitter: Twitter,
  linkedin: Linkedin,
  email: Mail
};

const filteredLinks = computed(() => {
  let result = [...links.value];
  
  if (selectedPlatform.value !== 'all') {
    result = result.filter(link => link.platform === selectedPlatform.value);
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(link =>
      link.label.toLowerCase().includes(query) ||
      link.platform.toLowerCase().includes(query) ||
      link.url.toLowerCase().includes(query)
    );
  }
  
  return result.sort((a, b) => a.sortOrder - b.sortOrder);
});

const totalPages = computed(() => Math.ceil(filteredLinks.value.length / itemsPerPage.value));

const paginatedLinks = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredLinks.value.slice(start, start + itemsPerPage.value);
});

const handleEdit = (link) => {
  editingLink.value = {
    id: link.id,
    platform: link.platform,
    label: link.label,
    url: link.url,
    style: { ...link.style },
    sortOrder: link.sortOrder
  };
  isFormVisible.value = true;
};

const handleAdd = () => {
  editingLink.value = null;
  isFormVisible.value = true;
};

const handleSave = (data) => {
  if (editingLink.value) {
    const index = links.value.findIndex(l => l.id === editingLink.value.id);
    if (index !== -1) {
      links.value[index] = {
        ...links.value[index],
        ...data,
        style: data.style || links.value[index].style
      };
    }
  } else {
    const newId = Math.max(...links.value.map(l => l.id), 0) + 1;
    links.value.push({
      id: newId,
      ...data,
      icon: platformIcons[data.platform] || Link
    });
  }
  isFormVisible.value = false;
  editingLink.value = null;
};

const handleCancel = () => {
  isFormVisible.value = false;
  editingLink.value = null;
};

const handleDelete = (id) => {
  deletingLinkId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDelete = () => {
  if (deletingLinkId.value !== null) {
    links.value = links.value.filter(l => l.id !== deletingLinkId.value);
    deletingLinkId.value = null;
  }
  showDeleteConfirm.value = false;
};

const moveUp = (link) => {
  const index = links.value.findIndex(l => l.id === link.id);
  if (index > 0) {
    [links.value[index], links.value[index - 1]] = [links.value[index - 1], links.value[index]];
    links.value[index].sortOrder = index;
    links.value[index - 1].sortOrder = index - 1;
  }
};

const moveDown = (link) => {
  const index = links.value.findIndex(l => l.id === link.id);
  if (index < links.value.length - 1) {
    [links.value[index], links.value[index + 1]] = [links.value[index + 1], links.value[index]];
    links.value[index].sortOrder = index;
    links.value[index + 1].sortOrder = index + 1;
  }
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center gap-4 mb-2">
        <Link class="text-construct-red" size="32" />
        <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_social_links') }}</h2>
      </div>
      <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_social_links_subtitle') }}</p>
    </div>

    <!-- Search and Filter -->
    <div class="flex flex-col md:flex-row gap-4 mb-8">
      <div class="flex-1 relative">
        <Search :class="['absolute left-4 top-1/2 -translate-y-1/2', isDarkMode ? 'text-gray-400' : 'text-gray-500']" size="20" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('admin_search')"
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
            v-model="selectedPlatform"
            :class="[
              'px-4 py-3 border focus:border-construct-red focus:outline-none transition-colors',
              isDarkMode ? 'bg-gray-800 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]"
          >
            <option value="all">{{ t('admin_all_platforms') }}</option>
            <option value="github">GitHub</option>
            <option value="twitter">Twitter</option>
            <option value="linkedin">LinkedIn</option>
            <option value="email">Email</option>
          </select>
        </div>
        <button @click="handleAdd" class="flex items-center gap-2 px-6 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider transition-colors rounded">
          <Plus size="18" />
          {{ t('admin_add') }}
        </button>
      </div>
    </div>

    <!-- Links List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <div
        v-for="link in paginatedLinks"
        :key="link.id"
        :class="[
          'border-2 p-6 relative group transition-all hover:shadow-lg',
          isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <component :is="platformIcons[link.platform] || Link" :class="['w-6 h-6', isDarkMode ? 'text-white' : 'text-gray-900']" />
            <div>
              <h3 :class="['font-bold text-lg', isDarkMode ? 'text-white' : 'text-gray-900']">{{ link.label }}</h3>
              <p :class="['text-xs uppercase tracking-wider', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ link.platform }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button @click="moveUp(link)" :class="['p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              <ArrowUp size="16" />
            </button>
            <button @click="moveDown(link)" :class="['p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              <ArrowDown size="16" />
            </button>
          </div>
        </div>

        <div :class="['text-sm mb-4 truncate', isDarkMode ? 'text-gray-300' : 'text-gray-600']">
          {{ link.url }}
        </div>

        <div class="flex items-center justify-between">
          <span :class="['text-xs font-mono', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
            #{{ link.sortOrder }}
          </span>
          <div class="flex items-center gap-2">
            <button @click="handleEdit(link)" :class="['p-2 rounded transition-colors', isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500']">
              <Edit3 size="16" />
            </button>
            <button @click="handleDelete(link.id)" :class="['p-2 rounded transition-colors text-red-500', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-red-50']">
              <Trash2 size="16" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <AdminPagination
      v-model:current-page="currentPage"
      :total-items="filteredLinks.length"
      :items-per-page="itemsPerPage"
    />

    <!-- Form Modal -->
    <ContentForm
      :visible="isFormVisible"
      content-type="social-link"
      :edit-data="editingLink"
      @save="handleSave"
      @cancel="handleCancel"
    />

    <!-- Delete Confirmation -->
    <ConfirmDialog
      v-model:visible="showDeleteConfirm"
      :title="t('admin_delete_confirm')"
      :message="t('admin_delete_social_link_confirm')"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>
