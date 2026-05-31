<script setup>
/**
 * User Detail Page
 * 
 * Features:
 * - Display detailed user information
 * - Show author profile (skills, social links, manifestos)
 * - Edit user details
 * - Back navigation to user list
 */
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import {
  User,
  ChevronLeft,
  Edit3,
  Award,
  FileText,
  Link as LinkIcon,
  Github,
  Twitter,
  Linkedin,
  Mail,
  CheckCircle
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { findById, findIndexById } from '../../utils/typeConvert';
import UserDetailForm from '../../components/admin/UserDetailForm.vue';

const props = defineProps({
  user: { type: Object, default: () => ({}) },
  roles: { type: Array, default: () => [] },
  media: { type: Array, default: () => [] },
});

const { t } = useI18n();
const { isDarkMode } = useTheme();

const isFormVisible = ref(false);
const showSuccessMessage = ref(false);

const authorInfo = computed(() => {
  if (!props.user) return null;
  return {
    bio: props.user.bio,
    avatar: props.user.avatar,
    skills: props.user.skills || [],
    social_links: props.user.social_links || {},
    manifestos: props.user.manifestos || [],
  };
});

const authorSkills = computed(() => {
  return authorInfo.value?.skills || [];
});

const authorLinksObj = computed(() => {
  return authorInfo.value?.social_links || {};
});

const authorLinks = computed(() => {
  const linksObj = authorLinksObj.value;
  return Object.entries(linksObj).map(([platform, url], index) => {
    const iconMap = {
      github: 'Github',
      twitter: 'Twitter',
      linkedin: 'Linkedin',
      website: 'Globe',
      email: 'Mail'
    };
    const labelMap = {
      github: 'GITHUB',
      twitter: 'TWITTER',
      linkedin: 'LINKEDIN',
      website: 'WEBSITE',
      email: 'CONTACT'
    };
    return {
      id: index + 1,
      platform,
      icon_name: iconMap[platform] || 'Link',
      label: labelMap[platform] || platform.toUpperCase(),
      url
    };
  });
});

const authorManifestos = computed(() => {
  return authorInfo.value?.manifestos || [];
});

const formatToShort = (dateStr) => {
  const date = new Date(dateStr);
  return date.toLocaleDateString('zh-CN', { year: 'numeric', month: 'short', day: 'numeric' });
};

const getRoleStyle = (roleName) => {
  const role = (props.roles || []).find(r => r.name === roleName);
  if (!role) return isDarkMode.value ? 'bg-gray-500/20 text-gray-400 border-gray-500/50' : 'bg-gray-100 text-gray-600 border-gray-300';

  const colorMap = {
    'red': isDarkMode.value ? 'bg-red-500/20 text-red-400 border-red-500/50' : 'bg-red-100 text-red-600 border-red-300',
    'blue': isDarkMode.value ? 'bg-blue-500/20 text-blue-400 border-blue-500/50' : 'bg-blue-100 text-blue-600 border-blue-300',
    'green': isDarkMode.value ? 'bg-green-500/20 text-green-400 border-green-500/50' : 'bg-green-100 text-green-600 border-green-300',
    'purple': isDarkMode.value ? 'bg-purple-500/20 text-purple-400 border-purple-500/50' : 'bg-purple-100 text-purple-600 border-purple-300',
    'gray': isDarkMode.value ? 'bg-gray-500/20 text-gray-400 border-gray-500/50' : 'bg-gray-100 text-gray-600 border-gray-300',
    'yellow': isDarkMode.value ? 'bg-yellow-500/20 text-yellow-400 border-yellow-500/50' : 'bg-yellow-100 text-yellow-600 border-yellow-300',
    'cyan': isDarkMode.value ? 'bg-cyan-500/20 text-cyan-400 border-cyan-500/50' : 'bg-cyan-100 text-cyan-600 border-cyan-300',
  };
  return colorMap[role.color] || colorMap['gray'];
};

const getRoleLabel = (roleName) => {
  const role = (props.roles || []).find(r => r.name === roleName);
  return role ? (role.label || role.name) : (t('admin_role_user') || '用户');
};

const getLinkIcon = (iconName) => {
  const iconMap = {
    'Github': Github,
    'Twitter': Twitter,
    'Linkedin': Linkedin,
    'Mail': Mail
  };
  return iconMap[iconName] || LinkIcon;
};

const openEditForm = () => {
  isFormVisible.value = true;
};

const handleSave = (data) => {
  // 拆平 user 字段到顶层（后端 UpdateUserRequest 要求 name/email 在顶层）
  // 排除 roles（名称数组），role_id 才是后端需要的角色 ID
  const { roles: _roles, ...userFields } = data.user;
  const payload = {
    ...data.author,
    ...userFields,
    skills: data.skills,
    social_links: data.social_links,
    manifestos: data.manifestos,
  };
  const form = useForm(payload);
  form.put(route('admin.users.update', props.user.id), {
    onSuccess: () => {
      isFormVisible.value = false;
      showSuccessMessage.value = true;
      setTimeout(() => {
        showSuccessMessage.value = false;
      }, 3000);
    },
    onError: (errors) => {
      console.error('Update error:', errors);
    }
  });
};

const handleCancel = () => {
  isFormVisible.value = false;
};
</script>

<template>
  <div class="p-8">
    <div v-if="user">
      <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
          <Link
            href="/admin/users"
            :class="['flex items-center gap-2 px-4 py-2 rounded-lg transition-colors', isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600']"
          >
            <ChevronLeft :size="20" />
            <span class="font-bold tracking-wider uppercase text-sm">{{ t('admin_users') }}</span>
          </Link>

          <button
            @click="openEditForm"
            class="flex items-center gap-2 px-6 py-2 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider rounded-lg transition-colors"
          >
            <Edit3 :size="18" />
            {{ t('admin_edit') }}
          </button>
        </div>

        <div v-if="showSuccessMessage" :class="['mb-6 p-4 rounded-lg flex items-center gap-3', isDarkMode ? 'bg-green-900/30 text-green-400 border border-green-700' : 'bg-green-50 text-green-700 border border-green-200']">
          <CheckCircle :size="20" />
          <span class="font-medium">保存成功！</span>
        </div>

        <div :class="['rounded-xl p-8', isDarkMode ? 'bg-gray-800' : 'bg-white shadow-lg']">
          <div class="flex items-start gap-6 mb-8">
            <div :class="['w-24 h-24 rounded-full flex items-center justify-center overflow-hidden', isDarkMode ? 'bg-gray-700' : 'bg-gray-100']">
              <img v-if="user.avatar" :src="user.avatar" :alt="user.name" class="w-full h-full object-cover" />
              <User v-else :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" size="48" />
            </div>
            <div class="flex-1">
              <h2 :class="['text-3xl font-bold mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">{{ user.name }}</h2>
              <p :class="['text-lg', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ user.email }}</p>
              <div class="flex items-center gap-3 mt-3">
                <span v-if="user.roles && user.roles.length > 0" :class="['px-4 py-1.5 rounded-full text-sm font-bold border', getRoleStyle(user.roles[0])]">{{ getRoleLabel(user.roles[0]) }}</span>
                <span v-else :class="['px-4 py-1.5 rounded-full text-sm font-bold border bg-gray-500 text-white border-gray-400']">{{ t('admin_role_user') || '用户' }}</span>
                <span :class="['px-4 py-1.5 rounded-full text-sm font-bold', user.status === 'active' ? (isDarkMode ? 'bg-green-500/20 text-green-400' : 'bg-green-100 text-green-600') : (isDarkMode ? 'bg-gray-600/50 text-gray-400' : 'bg-gray-100 text-gray-500')]">
                  {{ user.status === 'active' ? t('admin_active') : t('admin_inactive') }}
                </span>
              </div>
            </div>
          </div>

          <div :class="['grid grid-cols-1 md:grid-cols-2 gap-4 mb-8', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            <div class="flex items-center justify-between p-4 rounded-lg" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
              <span class="text-sm font-bold tracking-wider uppercase">{{ t('admin_table_joined') }}</span>
              <span>{{ formatToShort(user.created_at) }}</span>
            </div>
            <div class="flex items-center justify-between p-4 rounded-lg" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
              <span class="text-sm font-bold tracking-wider uppercase">{{ t('admin_table_role') }}</span>
              <span v-if="user.roles && user.roles.length > 0">{{ getRoleLabel(user.roles[0]) }}</span>
              <span v-else>{{ t('admin_role_user') || '用户' }}</span>
            </div>
          </div>

          <div v-if="authorInfo" class="mb-8">
            <h3 :class="['text-xl font-bold mb-4 flex items-center gap-2', isDarkMode ? 'text-white' : 'text-gray-900']">
              <FileText :size="20" />
              作者信息
            </h3>

            <div v-if="authorInfo.bio" :class="['p-4 rounded-lg mb-4', isDarkMode ? 'bg-gray-700' : 'bg-gray-50']">
              <p :class="['text-sm font-bold tracking-wider uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">简介</p>
              <p>{{ authorInfo.bio }}</p>
            </div>

            <div v-if="authorManifestos.length > 0" class="mb-4">
              <p :class="['text-sm font-bold tracking-wider uppercase mb-3', isDarkMode ? 'text-gray-400' : 'text-gray-500']">宣言</p>
              <div :class="['space-y-3 p-4 rounded-lg', isDarkMode ? 'bg-gray-700' : 'bg-gray-50']">
                <div v-for="manifesto in authorManifestos" :key="manifesto.id" class="pl-4 border-l-4 border-construct-red">
                  <p class="font-medium">{{ manifesto.content }}</p>
                </div>
              </div>
            </div>
          </div>

          <div v-if="authorSkills.length > 0" class="mb-8">
            <h3 :class="['text-xl font-bold mb-4 flex items-center gap-2', isDarkMode ? 'text-white' : 'text-gray-900']">
              <Award :size="20" />
              技能
            </h3>
            <div class="space-y-3">
              <div v-for="(skill, index) in authorSkills" :key="skill.id || index" :class="['p-4 rounded-lg', isDarkMode ? 'bg-gray-700' : 'bg-gray-50']">
                <div class="flex justify-between items-center mb-2">
                  <span class="font-medium">{{ skill.label }}</span>
                  <span class="text-sm font-bold">{{ skill.value }}%</span>
                </div>
                <div :class="['w-full h-2 rounded-full', isDarkMode ? 'bg-gray-600' : 'bg-gray-200']">
                  <div class="h-2 rounded-full bg-construct-red transition-all" :style="{ width: skill.value + '%' }"></div>
                </div>
                <p v-if="skill.description" :class="['text-sm mt-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ skill.description }}</p>
              </div>
            </div>
          </div>

          <div v-if="authorLinks.length > 0" class="mb-8">
            <h3 :class="['text-xl font-bold mb-4 flex items-center gap-2', isDarkMode ? 'text-white' : 'text-gray-900']">
              <LinkIcon :size="20" />
              社交链接
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div v-for="link in authorLinks" :key="link.platform" :class="['flex items-center gap-3 p-4 rounded-lg transition-colors', isDarkMode ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-50 hover:bg-gray-100']">
                <component :is="getLinkIcon(link.icon_name)" :size="20" />
                <div>
                  <p class="font-medium">{{ link.label }}</p>
                  <p :class="['text-sm truncate', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ link.url }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else :class="['text-center py-16', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
      <User :size="64" class="mx-auto mb-4 opacity-50" />
      <p class="text-xl">{{ t('no_artifacts') || '用户不存在' }}</p>
      <Link
        href="/admin/users"
        :class="['mt-6 px-6 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider rounded-lg transition-colors']"
      >
        {{ t('admin_users') }}
      </Link>
    </div>

    <UserDetailForm
      :user-data="user"
      :author-data="authorInfo"
      :social-links-data="authorLinksObj"
      :skills-data="authorSkills"
      :manifestos-data="authorManifestos"
      :visible="isFormVisible"
      :roles="props.roles"
      :media="props.media"
      @save="handleSave"
      @cancel="handleCancel"
    />
  </div>
</template>
