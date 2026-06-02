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
  Briefcase,
  Award,
  FileText,
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
  'w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red transition-colors',
  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500',
]);

const cardClass = computed(() => [
  'rounded-xl border overflow-hidden',
  isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200',
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
  'text-sm',
  isDarkMode ? 'text-gray-400' : 'text-gray-500',
]);

const sectionTitleClass = computed(() => [
  'text-lg font-bold tracking-tight mb-4',
  isDarkMode ? 'text-white' : 'text-gray-900',
]);
</script>

<template>
  <div class="p-8 max-w-3xl mx-auto">
    <!-- Page Header -->
    <div class="mb-10 flex items-center justify-between">
      <div>
        <div class="flex items-center gap-3 mb-2">
          <User class="text-construct-red" :size="28" />
          <h2 :class="['text-3xl font-black tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">
            {{ t('admin_user_form_profile') }}
          </h2>
        </div>
        <p :class="['text-sm font-bold tracking-widest uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
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

    <!-- User Info Card -->
    <div :class="cardClass">
      <div :class="['px-6 py-4 border-b', isDarkMode ? 'border-gray-700 bg-gray-800' : 'border-gray-200 bg-gray-50']">
        <h3 :class="sectionTitleClass">{{ t('admin_user_form_profile') }}</h3>
      </div>
      <div class="p-6 space-y-5">
        <!-- 头像区域 -->
        <div class="flex items-center gap-5">
          <div class="relative group cursor-pointer" @click="isEditing ? triggerFileInput() : null">
            <!-- 头像容器 -->
            <div :class="['w-20 h-20 rounded-full overflow-hidden flex items-center justify-center border-2 transition-all', isDarkMode ? 'border-gray-600 bg-gray-700' : 'border-gray-200 bg-gray-100', isEditing ? 'hover:border-construct-red' : '']">
              <img v-if="displayAvatar" :src="displayAvatar" class="w-full h-full object-cover" alt="Avatar" />
              <User v-else :size="36" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" />
            </div>
            <!-- 编辑模式：hover 遮罩 -->
            <div v-if="isEditing && !isUploadingAvatar" 
                 :class="['absolute inset-0 rounded-full flex items-center justify-center transition-opacity group-hover:opacity-100', displayAvatar ? 'opacity-0' : 'opacity-50']"
                 :style="displayAvatar ? 'background: rgba(0,0,0,0.45)' : ''">
              <Camera :size="20" class="text-white" />
            </div>
            <!-- 上传中 -->
            <div v-if="isUploadingAvatar" class="absolute inset-0 rounded-full flex items-center justify-center bg-black/50">
              <Loader :size="20" class="text-white animate-spin" />
            </div>
          </div>
          <!-- <div>
            <p :class="['text-sm', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
              {{ t('admin_user_form_name') }}: <span class="font-bold">{{ formData.user_name }}</span>
            </p>
            <p :class="['text-xs mt-1', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              {{ isEditing ? t('admin_avatar_upload_hint') : '' }}
            </p>
          </div> -->
        </div>

        <!-- 隐藏文件选择器 -->
        <input ref="fileInputRef" type="file" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden" @change="handleAvatarFileChange" />

        <div>
          <label :class="labelClass"><User :size="12" class="inline mr-1" />{{ t('admin_user_form_name') }}</label>
          <input v-if="isEditing" v-model="formData.user_name" type="text" :class="inputClass" />
          <p v-else :class="valueClass">{{ formData.user_name }}</p>
        </div>
        <div>
          <label :class="labelClass"><Mail :size="12" class="inline mr-1" />{{ t('admin_user_form_email') }}</label>
          <input v-if="isEditing" v-model="formData.user_email" type="email" :class="inputClass" />
          <p v-else :class="valueClass">{{ formData.user_email }}</p>
        </div>
      </div>
    </div>

    <!-- Author Profile Card -->
    <div :class="[cardClass, 'mt-6']">
      <div :class="['px-6 py-4 border-b', isDarkMode ? 'border-gray-700 bg-gray-800' : 'border-gray-200 bg-gray-50']">
        <h3 :class="sectionTitleClass">{{ t('admin_author_profile') || '作者信息' }}</h3>
      </div>
      <div class="p-6 space-y-5">
        <!-- Slug（仅查看） -->
        <div>
          <label :class="labelClass"><LinkIcon :size="12" class="inline mr-1" />Slug</label>
          <p :class="valueClass">{{ props.profile.slug || '—' }}</p>
        </div>

        <div>
          <label :class="labelClass"><Award :size="12" class="inline mr-1" />{{ t('admin_user_form_display_name') || '显示名称' }}</label>
          <input v-if="isEditing" v-model="formData.display_name" type="text" :class="inputClass" />
          <p v-else :class="valueClass">{{ formData.display_name || '—' }}</p>
        </div>
        <div>
          <label :class="labelClass"><Award :size="12" class="inline mr-1" />{{ t('admin_user_form_role_label') || '角色标签' }}</label>
          <input v-if="isEditing" v-model="formData.role_label" type="text" :class="inputClass" />
          <p v-else :class="valueClass">{{ formData.role_label || '—' }}</p>
        </div>
        <div>
          <label :class="labelClass"><Briefcase :size="12" class="inline mr-1" />{{ t('admin_user_form_role_title') || '角色头衔' }}</label>
          <input v-if="isEditing" v-model="formData.role_title" type="text" :class="inputClass" />
          <p v-else :class="valueClass">{{ formData.role_title || '—' }}</p>
        </div>
        <div>
          <label :class="labelClass"><Briefcase :size="12" class="inline mr-1" />{{ t('admin_user_form_company') || '公司' }}</label>
          <input v-if="isEditing" v-model="formData.company" type="text" :class="inputClass" />
          <p v-else :class="valueClass">{{ formData.company || '—' }}</p>
        </div>
        <div>
          <label :class="labelClass"><FileText :size="12" class="inline mr-1" />{{ t('admin_user_form_bio') || '个人简介' }}</label>
          <textarea v-if="isEditing" v-model="formData.bio" :class="[inputClass, 'min-h-[100px] resize-y']" :placeholder="t('admin_user_form_bio_placeholder') || '介绍你自己...'" />
          <p v-else :class="subValueClass">{{ formData.bio || '—' }}</p>
        </div>
      </div>
    </div>

    <!-- Skills Card -->
    <div :class="[cardClass, 'mt-6']">
      <div :class="['px-6 py-4 border-b flex items-center justify-between', isDarkMode ? 'border-gray-700 bg-gray-800' : 'border-gray-200 bg-gray-50']">
        <h3 :class="[sectionTitleClass, 'mb-0']">{{ t('admin_user_form_skills') || '技能' }}</h3>
        <button v-if="isEditing" @click="addSkill" class="flex items-center gap-1 text-sm text-construct-red hover:underline">
          <Plus :size="14" /> {{ t('admin_user_form_add_skill') || '添加' }}
        </button>
      </div>
      <div class="p-6">
        <!-- 查看模式 -->
        <template v-if="!isEditing">
          <div class="space-y-3">
            <div v-for="(skill, idx) in formData.skills" :key="idx" :class="['p-4 rounded-lg', isDarkMode ? 'bg-gray-700' : 'bg-gray-50']">
              <div class="flex justify-between items-center mb-2">
                <span :class="['font-medium', isDarkMode ? 'text-white' : 'text-gray-900']">{{ skill.label }}</span>
                <span :class="['text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-600']">{{ skill.value }}%</span>
              </div>
              <div :class="['w-full h-2 rounded-full', isDarkMode ? 'bg-gray-600' : 'bg-gray-200']">
                <div class="h-2 rounded-full bg-construct-red transition-all" :style="{ width: skill.value + '%' }"></div>
              </div>
              <p v-if="skill.description" :class="['text-sm mt-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ skill.description }}</p>
            </div>
            <p v-if="formData.skills.length === 0" :class="['text-sm', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              {{ t('admin_no_data') || '暂无数据' }}
            </p>
          </div>
        </template>

        <!-- 编辑模式 -->
        <template v-else>
          <div class="space-y-3">
            <div v-for="(skill, idx) in formData.skills" :key="idx" :class="['p-4 rounded-lg', isDarkMode ? 'bg-gray-700' : 'bg-white border border-gray-200']">
              <div class="flex justify-between items-center mb-2">
                <input v-model="skill.label" :class="['font-medium bg-transparent border-b border-gray-400 focus:outline-none focus:border-construct-red', isDarkMode ? 'text-white' : 'text-gray-900']" placeholder="技能名称..." />
                <div class="flex items-center gap-2">
                  <input v-model.number="skill.value" type="range" min="0" max="100" class="w-24" />
                  <span :class="['text-sm font-bold min-w-[3ch]', isDarkMode ? 'text-gray-300' : 'text-gray-600']">{{ skill.value }}%</span>
                  <button @click="removeSkill(idx)" class="text-red-500 hover:text-red-700 p-1">
                    <Trash2 :size="16" />
                  </button>
                </div>
              </div>
              <div :class="['w-full h-2 rounded-full', isDarkMode ? 'bg-gray-600' : 'bg-gray-200']">
                <div class="h-2 rounded-full bg-construct-red transition-all" :style="{ width: skill.value + '%' }"></div>
              </div>
              <textarea v-model="skill.description" rows="2" :class="['text-sm mt-2 w-full bg-transparent border rounded p-2 focus:outline-none focus:border-construct-red', isDarkMode ? 'border-gray-500 text-gray-400' : 'border-gray-300 text-gray-500']" placeholder="技能描述..." />
            </div>
            <p v-if="formData.skills.length === 0" :class="['text-sm', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
              {{ t('admin_no_data') || '点击上方「添加」创建技能' }}
            </p>
          </div>
        </template>
      </div>
    </div>

    <!-- Social Links Card -->
    <div :class="[cardClass, 'mt-6']">
      <div :class="['px-6 py-4 border-b flex items-center justify-between', isDarkMode ? 'border-gray-700 bg-gray-800' : 'border-gray-200 bg-gray-50']">
        <h3 :class="[sectionTitleClass, 'mb-0']">{{ t('admin_social_links') || '社交链接' }}</h3>
        <button v-if="isEditing" @click="openAddLink" class="flex items-center gap-1 text-sm text-construct-red hover:underline">
          <Plus :size="14" /> 添加
        </button>
      </div>
      <div class="p-6">
        <!-- 查看模式 -->
        <template v-if="!isEditing">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div v-for="link in socialLinkEntries" :key="link.platform" :class="['flex items-center gap-3 p-4 rounded-lg transition-colors', isDarkMode ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-50 hover:bg-gray-100']">
              <component :is="getLinkIcon(link.platform)" :size="20" />
              <div>
                <p :class="['text-xs font-bold uppercase tracking-wider', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ link.platform }}</p>
                <a :href="link.url" target="_blank" :class="['text-sm underline truncate block max-w-[180px]', isDarkMode ? 'text-blue-400' : 'text-blue-600']">{{ link.url }}</a>
              </div>
            </div>
          </div>
          <p v-if="socialLinkEntries.length === 0" :class="['text-sm', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
            {{ t('admin_no_data') || '暂无数据' }}
          </p>
        </template>

        <!-- 编辑模式 -->
        <template v-else>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div v-for="(url, platform) in formData.social_links" :key="platform" :class="['p-4 rounded-lg', isDarkMode ? 'bg-gray-700' : 'bg-white border border-gray-200']">
              <div class="flex items-start gap-3 mb-2">
                <component :is="getLinkIcon(platform)" :size="20" class="mt-1" />
                <div class="flex-1">
                  <label :class="['text-xs font-bold uppercase tracking-wider mb-1 block', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ platform }}</label>
                  <input v-model="formData.social_links[platform]" :class="['text-sm w-full bg-transparent border-b focus:outline-none focus:border-construct-red', isDarkMode ? 'border-gray-500 text-gray-300' : 'border-gray-300 text-gray-600']" :placeholder="platform + ' URL...'" />
                </div>
                <button @click="removeSocialLink(platform)" class="text-red-500 hover:text-red-700 ml-2 p-1">
                  <Trash2 :size="16" />
                </button>
              </div>
            </div>
          </div>
          <p v-if="Object.keys(formData.social_links).length === 0" :class="['text-sm mt-2', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
            {{ t('admin_no_data') || '点击上方「添加」创建链接' }}
          </p>
        </template>
      </div>
    </div>

    <!-- 操作按钮 -->
    <div v-if="isEditing" class="mt-8 flex gap-4 justify-end">
      <button @click="handleCancel" :class="['px-6 py-3 font-bold tracking-widest uppercase text-sm rounded-xl border transition-colors', isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-100']">
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
        <div :class="['w-full max-w-md mx-4 rounded-xl shadow-2xl', isDarkMode ? 'bg-gray-800' : 'bg-white']">
          <div :class="['flex justify-between items-center p-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">添加社交链接</h3>
            <button @click="cancelAddLink" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
              <X :size="20" />
            </button>
          </div>
          <div class="p-6 space-y-4">
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">平台名称</label>
              <input v-model="newLinkPlatform" :class="inputClass" placeholder="如：github, twitter, instagram..." />
            </div>
            <div>
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">链接地址</label>
              <input v-model="newLinkUrl" :class="inputClass" placeholder="https://..." />
            </div>
          </div>
          <div :class="['flex gap-3 p-6 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
            <button @click="cancelAddLink" :class="['flex-1 px-4 py-2 font-bold text-sm rounded-lg border transition-colors', isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-100']">
              {{ t('admin_cancel') }}
            </button>
            <button @click="confirmAddLink" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-construct-red text-white font-bold text-sm rounded-lg hover:bg-red-700 transition-colors">
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
