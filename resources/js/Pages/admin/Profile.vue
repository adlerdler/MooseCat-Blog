<script setup>
/**
 * Profile.vue - 个人资料页面
 * 
 * 功能说明：
 * - 查看/编辑用户信息（用户名、邮箱）
 * - 查看/编辑作者信息（显示名、简介、公司、角色等）
 * - 管理技能（对象数组：{ label, value, description }）
 * - 管理社交链接（键值对对象：{ platform: url }）
 * - 不展示文章、评论等额外信息
 */
import { ref, computed, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import {
  User,
  Mail,
  Link as LinkIcon,
  Github,
  Twitter,
  Linkedin,
  Globe,
  Youtube,
  Facebook,
  Video,
  Palette,
  MessageCircle,
  Save,
  Plus,
  X,
  Trash2,
  Camera,
  Loader
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const props = defineProps({
  profile: { type: Object, default: () => ({}) },
});

const { t } = useI18n();
const { isDarkMode } = useTheme();

const isEditing = ref(false);

// ========== 头像上传 ==========
const fileInputRef = ref(null);
const isUploadingAvatar = ref(false);
const avatarPreviewUrl = ref(null); // 本地预览

// 触发文件选择
const triggerFileInput = () => {
  fileInputRef.value?.click();
};

// 头像上传
const handleAvatarFileChange = (e) => {
  const file = e.target.files?.[0];
  if (!file) return;

  // 生成本地预览
  const reader = new FileReader();
  reader.onload = (evt) => {
    avatarPreviewUrl.value = evt.target.result;
  };
  reader.readAsDataURL(file);

  // 上传到服务器
  isUploadingAvatar.value = true;
  const formData = new FormData();
  formData.append('avatar', file);

  router.post(route('admin.profile.avatar', props.profile.slug), formData, {
    onSuccess: () => {
      isUploadingAvatar.value = false;
      avatarPreviewUrl.value = null;
      router.reload();
    },
    onError: () => {
      isUploadingAvatar.value = false;
      avatarPreviewUrl.value = null;
    },
    preserveScroll: true,
  });

  // 重置 input 以允许重复上传同一文件
  e.target.value = '';
};

// 当前显示的头像 URL（本地预览 > 服务器地址）
const displayAvatar = computed(() => {
  return avatarPreviewUrl.value || props.profile.avatar || null;
});

// 深度克隆
const deepClone = (obj) => {
  if (!obj) return null;
  return JSON.parse(JSON.stringify(obj));
};

// 表单数据
const formData = ref({
  user_name: '',
  user_email: '',
  display_name: '',
  bio: '',
  role_label: '',
  role_title: '',
  company: '',
  social_links: {},
  skills: [],
});

// 社交链接新增
const showAddLinkModal = ref(false);
const newLinkPlatform = ref('');
const newLinkUrl = ref('');

// 初始化
const initFormData = () => {
  formData.value = {
    user_name: props.profile.user_name || '',
    user_email: props.profile.user_email || '',
    display_name: props.profile.display_name || '',
    bio: props.profile.bio || '',
    role_label: props.profile.role_label || '',
    role_title: props.profile.role_title || '',
    company: props.profile.company || '',
    social_links: props.profile.social_links && typeof props.profile.social_links === 'object' && !Array.isArray(props.profile.social_links)
      ? { ...props.profile.social_links }
      : {},
    skills: deepClone(props.profile.skills) || [],
  };
};

watch(() => props.profile, () => {
  initFormData();
}, { immediate: true });

// 取消
const handleCancel = () => {
  initFormData();
  isEditing.value = false;
};

// 保存
const handleSave = () => {
  const form = useForm({
    user_name: formData.value.user_name,
    user_email: formData.value.user_email,
    display_name: formData.value.display_name,
    bio: formData.value.bio,
    role_label: formData.value.role_label,
    role_title: formData.value.role_title,
    company: formData.value.company,
    social_links: formData.value.social_links,
    skills: formData.value.skills,
  });

  form.put(route('admin.profile.update', props.profile.slug), {
    onSuccess: () => {
      isEditing.value = false;
      router.reload();
    },
  });
};

// ========== 技能管理 ==========
const addSkill = () => {
  formData.value.skills.push({
    label: '',
    value: 50,
    description: '',
    category: 'general',
    sort_order: formData.value.skills.length + 1,
  });
};

const removeSkill = (index) => {
  formData.value.skills.splice(index, 1);
};

// ========== 社交链接管理 ==========
const getLinkIcon = (platform) => {
  const iconMap = {
    'github': Github,
    'twitter': Twitter,
    'linkedin': Linkedin,
    'website': Globe,
    'email': Mail,
    'instagram': Palette,
    'youtube': Youtube,
    'bilibili': Video,
    'tiktok': Video,
    'facebook': Facebook,
    'weibo': MessageCircle,
    'zhihu': MessageCircle,
    'dribbble': Palette,
    'behance': Palette,
  };
  return iconMap[(platform || '').toLowerCase()] || LinkIcon;
};

const openAddLink = () => {
  showAddLinkModal.value = true;
  newLinkPlatform.value = '';
  newLinkUrl.value = '';
};

const cancelAddLink = () => {
  showAddLinkModal.value = false;
};

const confirmAddLink = () => {
  if (!newLinkPlatform.value.trim() || !newLinkUrl.value.trim()) return;
  const key = newLinkPlatform.value.trim().toLowerCase().replace(/\s+/g, '_');
  formData.value.social_links = {
    ...formData.value.social_links,
    [key]: newLinkUrl.value.trim(),
  };
  cancelAddLink();
};

const removeSocialLink = (platform) => {
  const newLinks = { ...formData.value.social_links };
  delete newLinks[platform];
  formData.value.social_links = newLinks;
};

// 查看模式：社交链接转数组
const socialLinkEntries = computed(() => {
  const linksObj = formData.value.social_links || {};
  return Object.entries(linksObj).map(([platform, url]) => ({ platform, url }));
});

// ========== 样式 ==========
const inputClass = computed(() => [
  'w-full px-4 py-2 text-sm rounded-xl border focus:outline-none focus:border-construct-red transition-colors',
  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500',
]);

const cardClass = computed(() => [
  'rounded-2xl ring-1 transition-all duration-300 hover:shadow-md',
  isDarkMode ? 'bg-gray-800 ring-gray-700' : 'bg-white ring-gray-200',
]);

const labelClass = computed(() => [
  'block text-xs font-bold tracking-widest uppercase mb-2',
  isDarkMode ? 'text-gray-400' : 'text-gray-500',
]);

const valueClass = computed(() => [
  'text-sm font-bold',
  isDarkMode ? 'text-white' : 'text-gray-900',
]);

const subValueClass = computed(() => [
  'text-sm leading-relaxed',
  isDarkMode ? 'text-gray-400' : 'text-gray-500',
]);

const sectionTitleClass = computed(() => [
  'text-lg font-bold tracking-tight',
  isDarkMode ? 'text-white' : 'text-gray-900',
]);
</script>

<template>
  <div class="p-6 lg:p-8 max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8 flex items-center justify-between">
      <div>
        <h2 :class="['text-2xl font-black tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">
          {{ t('admin_user_form_profile') }}
        </h2>
        <p :class="['text-xs font-bold tracking-[0.2em] uppercase mt-1 opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
          {{ props.profile.slug || '—' }}
        </p>
      </div>
      <button
        v-if="!isEditing"
        @click="isEditing = true"
        class="px-6 py-3 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] rounded-xl transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20"
      >
        {{ t('admin_edit') }}
      </button>
    </div>

    <!-- ===== Profile Hero Card ===== -->
    <div :class="[cardClass, 'mb-6']">
      <div class="p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
          <!-- Avatar -->
          <div class="relative group cursor-pointer shrink-0" @click="isEditing ? triggerFileInput() : null">
            <div :class="['w-24 h-24 rounded-2xl overflow-hidden flex items-center justify-center ring-2 transition-all', isDarkMode ? 'ring-gray-600 bg-gray-700' : 'ring-gray-200 bg-gray-100', isEditing ? 'hover:ring-construct-red' : '']">
              <img v-if="displayAvatar" :src="displayAvatar" class="w-full h-full object-cover" alt="Avatar" />
              <User v-else :size="40" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" />
            </div>
            <div v-if="isEditing && !isUploadingAvatar" 
                 :class="['absolute inset-0 rounded-2xl flex items-center justify-center transition-opacity group-hover:opacity-100', displayAvatar ? 'opacity-0' : 'opacity-50']"
                 :style="displayAvatar ? 'background: rgba(0,0,0,0.5)' : ''">
              <Camera :size="22" class="text-white" />
            </div>
            <div v-if="isUploadingAvatar" class="absolute inset-0 rounded-2xl flex items-center justify-center bg-black/50">
              <Loader :size="22" class="text-white animate-spin" />
            </div>
          </div>
          <input ref="fileInputRef" type="file" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden" @change="handleAvatarFileChange" />

          <!-- Info -->
          <div class="flex-1 text-center sm:text-left">
            <!-- Display Name -->
            <h3 :class="['text-2xl font-black tracking-tight mb-1', isDarkMode ? 'text-white' : 'text-gray-900']">
              {{ formData.display_name || formData.user_name || '—' }}
            </h3>
            <!-- Role Badge -->
            <span v-if="formData.role_label" :class="[
              'inline-block px-3 py-0.5 rounded-full text-xs font-bold mb-2',
              isDarkMode ? 'bg-construct-red/20 text-construct-red' : 'bg-red-50 text-construct-red'
            ]">
              {{ formData.role_label }}
            </span>
            <!-- Email + Slug -->
            <div :class="['flex flex-wrap items-center gap-3 text-sm', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              <span class="flex items-center gap-1.5">
                <Mail :size="14" />
                {{ formData.user_email }}
              </span>
              <span class="flex items-center gap-1.5">
                <LinkIcon :size="14" />
                {{ props.profile.slug || '—' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== Two-Column Grid ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Left Column: Author Details + Bio -->
      <div class="space-y-6">
        <!-- Author Details Card -->
        <div :class="cardClass">
          <div class="px-6 pt-5 pb-2">
            <h3 :class="sectionTitleClass">{{ t('admin_author_profile') || '作者信息' }}</h3>
          </div>
          <div class="px-6 pb-5 space-y-4">
            <div>
              <label :class="labelClass">{{ t('admin_user_form_name') }}</label>
              <input v-if="isEditing" v-model="formData.user_name" type="text" :class="inputClass" />
              <p v-else :class="valueClass">{{ formData.user_name || '—' }}</p>
            </div>
            <div>
              <label :class="labelClass">{{ t('admin_user_form_display_name') || '显示名称' }}</label>
              <input v-if="isEditing" v-model="formData.display_name" type="text" :class="inputClass" />
              <p v-else :class="valueClass">{{ formData.display_name || '—' }}</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label :class="labelClass">{{ t('admin_user_form_role_label') || '角色标签' }}</label>
                <input v-if="isEditing" v-model="formData.role_label" type="text" :class="inputClass" />
                <p v-else :class="valueClass">{{ formData.role_label || '—' }}</p>
              </div>
              <div>
                <label :class="labelClass">{{ t('admin_user_form_role_title') || '角色头衔' }}</label>
                <input v-if="isEditing" v-model="formData.role_title" type="text" :class="inputClass" />
                <p v-else :class="valueClass">{{ formData.role_title || '—' }}</p>
              </div>
            </div>
            <div>
              <label :class="labelClass">{{ t('admin_user_form_company') || '公司' }}</label>
              <input v-if="isEditing" v-model="formData.company" type="text" :class="inputClass" />
              <p v-else :class="valueClass">{{ formData.company || '—' }}</p>
            </div>
            <div>
              <label :class="labelClass">{{ t('admin_user_form_bio') || '个人简介' }}</label>
              <textarea v-if="isEditing" v-model="formData.bio" :class="[inputClass, 'min-h-[100px] resize-y']" :placeholder="t('admin_user_form_bio_placeholder') || '介绍你自己...'" />
              <p v-else :class="subValueClass">{{ formData.bio || '—' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Skills + Social Links -->
      <div class="space-y-6">
        <!-- Skills Card -->
        <div :class="cardClass">
          <div class="px-6 pt-5 pb-2 flex items-center justify-between">
            <h3 :class="sectionTitleClass">{{ t('admin_user_form_skills') || '技能' }}</h3>
            <button v-if="isEditing" @click="addSkill" class="flex items-center gap-1.5 text-sm font-bold text-construct-red hover:text-red-500 transition-colors">
              <Plus :size="14" /> {{ t('admin_user_form_add_skill') || '添加' }}
            </button>
          </div>
          <div class="px-6 pb-5">
            <template v-if="!isEditing">
              <div v-if="formData.skills.length > 0" class="space-y-3">
                <div v-for="(skill, idx) in formData.skills" :key="idx" :class="['p-4 rounded-xl', isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50']">
                  <div class="flex justify-between items-center mb-2">
                    <span :class="['font-bold text-sm', isDarkMode ? 'text-white' : 'text-gray-900']">{{ skill.label }}</span>
                    <span :class="['text-xs font-bold', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ skill.value }}%</span>
                  </div>
                  <div :class="['w-full h-1.5 rounded-full', isDarkMode ? 'bg-gray-600' : 'bg-gray-200']">
                    <div class="h-1.5 rounded-full bg-construct-red transition-all duration-500" :style="{ width: skill.value + '%' }"></div>
                  </div>
                  <p v-if="skill.description" :class="['text-xs mt-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ skill.description }}</p>
                </div>
              </div>
              <p v-else :class="['text-sm py-4', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                {{ t('admin_no_data') || '暂无数据' }}
              </p>
            </template>
            <template v-else>
              <div v-if="formData.skills.length > 0" class="space-y-3">
                <div v-for="(skill, idx) in formData.skills" :key="idx" :class="['p-4 rounded-xl ring-1', isDarkMode ? 'bg-gray-700/50 ring-gray-600' : 'bg-gray-50 ring-gray-200']">
                  <div class="flex justify-between items-center mb-3">
                    <input v-model="skill.label" :class="['font-bold text-sm bg-transparent border-b focus:outline-none focus:border-construct-red w-28', isDarkMode ? 'border-gray-500 text-white' : 'border-gray-300 text-gray-900']" placeholder="技能名称..." />
                    <div class="flex items-center gap-2">
                      <input v-model.number="skill.value" type="range" min="0" max="100" class="w-16 accent-construct-red" />
                      <span :class="['text-xs font-bold min-w-[3ch]', isDarkMode ? 'text-gray-300' : 'text-gray-600']">{{ skill.value }}%</span>
                      <button @click="removeSkill(idx)" :class="['p-1 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:text-red-400 hover:bg-red-500/10' : 'text-gray-400 hover:text-red-500 hover:bg-red-50']">
                        <Trash2 :size="14" />
                      </button>
                    </div>
                  </div>
                  <div :class="['w-full h-1.5 rounded-full', isDarkMode ? 'bg-gray-600' : 'bg-gray-200']">
                    <div class="h-1.5 rounded-full bg-construct-red transition-all duration-300" :style="{ width: skill.value + '%' }"></div>
                  </div>
                  <textarea v-model="skill.description" rows="2" :class="['text-xs mt-3 w-full bg-transparent border rounded-xl p-2.5 focus:outline-none focus:border-construct-red', isDarkMode ? 'border-gray-500 text-gray-300 placeholder-gray-500' : 'border-gray-300 text-gray-600 placeholder-gray-400']" placeholder="技能描述..." />
                </div>
              </div>
              <p v-else :class="['text-sm py-4', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                {{ t('admin_no_data') || '点击上方「添加」创建技能' }}
              </p>
            </template>
          </div>
        </div>

        <!-- Social Links Card -->
        <div :class="cardClass">
          <div class="px-6 pt-5 pb-2 flex items-center justify-between">
            <h3 :class="sectionTitleClass">{{ t('admin_social_links') || '社交链接' }}</h3>
            <button v-if="isEditing" @click="openAddLink" class="flex items-center gap-1.5 text-sm font-bold text-construct-red hover:text-red-500 transition-colors">
              <Plus :size="14" /> 添加
            </button>
          </div>
          <div class="px-6 pb-5">
            <template v-if="!isEditing">
              <div v-if="socialLinkEntries.length > 0" class="grid grid-cols-1 gap-2">
                <div v-for="link in socialLinkEntries" :key="link.platform" :class="['flex items-center gap-3 p-3 rounded-xl transition-colors', isDarkMode ? 'bg-gray-700/50 hover:bg-gray-700' : 'bg-gray-50 hover:bg-gray-100']">
                  <component :is="getLinkIcon(link.platform)" :size="18" class="shrink-0" />
                  <div class="min-w-0 flex-1">
                    <p :class="['text-xs font-bold uppercase tracking-wider', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ link.platform }}</p>
                    <a :href="link.url" target="_blank" :class="['text-sm font-medium hover:underline truncate block', isDarkMode ? 'text-blue-400' : 'text-blue-600']">{{ link.url }}</a>
                  </div>
                </div>
              </div>
              <p v-else :class="['text-sm py-4', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                {{ t('admin_no_data') || '暂无数据' }}
              </p>
            </template>
            <template v-else>
              <div v-if="Object.keys(formData.social_links).length > 0" class="grid grid-cols-1 gap-2">
                <div v-for="(url, platform) in formData.social_links" :key="platform" :class="['p-3 rounded-xl ring-1', isDarkMode ? 'bg-gray-700/50 ring-gray-600' : 'bg-gray-50 ring-gray-200']">
                  <div class="flex items-center gap-3">
                    <component :is="getLinkIcon(platform)" :size="18" class="shrink-0" />
                    <div class="flex-1 min-w-0">
                      <label :class="['text-xs font-bold uppercase tracking-wider mb-0.5 block', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ platform }}</label>
                      <input v-model="formData.social_links[platform]" :class="['text-sm w-full bg-transparent border-b focus:outline-none focus:border-construct-red', isDarkMode ? 'border-gray-500 text-gray-300 placeholder-gray-500' : 'border-gray-300 text-gray-600 placeholder-gray-400']" :placeholder="platform + ' URL...'" />
                    </div>
                    <button @click="removeSocialLink(platform)" :class="['p-1 rounded-lg transition-colors shrink-0', isDarkMode ? 'text-gray-400 hover:text-red-400 hover:bg-red-500/10' : 'text-gray-400 hover:text-red-500 hover:bg-red-50']">
                      <Trash2 :size="14" />
                    </button>
                  </div>
                </div>
              </div>
              <p v-else :class="['text-sm py-4', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                {{ t('admin_no_data') || '点击上方「添加」创建链接' }}
              </p>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- 操作按钮 -->
    <div v-if="isEditing" class="mt-8 flex gap-4 justify-end">
      <button @click="handleCancel" :class="['px-6 py-3 font-bold tracking-widest uppercase text-sm rounded-xl transition-colors', isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">
        {{ t('admin_cancel') }}
      </button>
      <button @click="handleSave" class="flex items-center gap-2 px-8 py-3 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] rounded-xl transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20">
        <Save :size="16" />
        {{ t('admin_save') }}
      </button>
    </div>

    <!-- 添加社交链接弹窗 -->
    <Transition name="modal">
      <div v-if="showAddLinkModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/50" @click.self="cancelAddLink">
        <div :class="['w-full max-w-md mx-4 rounded-2xl shadow-2xl ring-1', isDarkMode ? 'bg-gray-800 ring-gray-700' : 'bg-white ring-gray-200']">
          <div class="flex justify-between items-center p-6 pb-2">
            <h3 :class="['text-lg font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">添加社交链接</h3>
            <button @click="cancelAddLink" :class="['p-2 rounded-xl transition-colors', isDarkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-500 hover:bg-gray-100']">
              <X :size="20" />
            </button>
          </div>
          <div class="p-6 pt-2 space-y-4">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">平台名称</label>
              <input v-model="newLinkPlatform" :class="inputClass" placeholder="如：github, twitter, instagram..." />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">链接地址</label>
              <input v-model="newLinkUrl" :class="inputClass" placeholder="https://..." />
            </div>
          </div>
          <div class="flex gap-3 p-6 pt-2">
            <button @click="cancelAddLink" :class="['flex-1 px-4 py-2 font-bold text-sm rounded-xl transition-colors', isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">
              {{ t('admin_cancel') }}
            </button>
            <button @click="confirmAddLink" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-construct-red text-white font-bold text-sm rounded-xl hover:bg-red-700 transition-colors">
              <Plus :size="16" />
              添加
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
