<script setup>
/**
 * UserDetailForm.vue - 用户详情编辑表单组件
 * 
 * 功能说明：
 * - 编辑用户基础信息
 * - 编辑作者信息（简介、宣言）
 * - 编辑技能列表
 * - 编辑社交链接（JSON对象）
 * 
 * ⚠️ 角色管理说明（2026-05-24）：
 * - role_id 已从数据库删除，改用 Spatie RBAC 管理角色
 * - 表单中的角色选择仅用于前端展示，实际角色分配通过 Spatie 方法
 */
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  User,
  Mail,
  X,
  Save,
  Plus,
  Trash2,
  FileText,
  Award,
  Link as LinkIcon,
  Github,
  Twitter,
  Linkedin,
  Globe,
  Youtube,
  Facebook,
  Video,
  Palette,
  MessageCircle
} from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { t } = useI18n();
const { isDarkMode } = useTheme();

const props = defineProps({
  userData: {
    type: Object,
    required: true
  },
  authorData: {
    type: Object,
    default: null
  },
  skillsData: {
    type: Array,
    default: () => []
  },
  socialLinksData: {
    type: Object,
    default: () => ({})
  },
  manifestosData: {
    type: Array,
    default: () => []
  },
  visible: {
    type: Boolean,
    default: false
  },
  roles: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['save', 'cancel']);

const editingUser = ref(null);
const editingAuthor = ref(null);
const editingSkills = ref([]);
const editingSocialLinks = ref({});
const editingManifestos = ref([]);

const showAddLinkModal = ref(false);
const newLinkPlatform = ref('');
const newLinkUrl = ref('');
const detectedPlatform = ref('');

const deepClone = (obj) => {
  if (!obj) return null;
  return JSON.parse(JSON.stringify(obj));
};

const detectPlatformFromUrl = (url) => {
  if (!url) return '';
  const urlLower = url.toLowerCase();
  if (urlLower.includes('github.com')) return 'github';
  if (urlLower.includes('twitter.com') || urlLower.includes('x.com')) return 'twitter';
  if (urlLower.includes('linkedin.com')) return 'linkedin';
  if (urlLower.includes('instagram.com')) return 'instagram';
  if (urlLower.includes('youtube.com') || urlLower.includes('youtu.be')) return 'youtube';
  if (urlLower.includes('bilibili.com')) return 'bilibili';
  if (urlLower.includes('tiktok.com')) return 'tiktok';
  if (urlLower.includes('facebook.com')) return 'facebook';
  if (urlLower.includes('weibo.com')) return 'weibo';
  if (urlLower.includes('zhihu.com')) return 'zhihu';
  if (urlLower.includes('dribbble.com')) return 'dribbble';
  if (urlLower.includes('behance.net')) return 'behance';
  return '';
};

const handleUrlInput = () => {
  detectedPlatform.value = detectPlatformFromUrl(newLinkUrl.value);
  if (detectedPlatform.value && !newLinkPlatform.value) {
    newLinkPlatform.value = detectedPlatform.value;
  }
};

watch(() => props.visible, (newVal) => {
  if (newVal) {
    editingUser.value = deepClone(props.userData);
    editingAuthor.value = deepClone(props.authorData);
    editingSkills.value = deepClone(props.skillsData);
    editingSocialLinks.value = { ...props.socialLinksData };
    editingManifestos.value = deepClone(props.manifestosData);
  }
});

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
    'behance': Palette
  };
  return iconMap[platform] || LinkIcon;
};

const addSkill = () => {
  const newId = Date.now();
  editingSkills.value.push({
    label: '',
    value: 50,
    description: '',
    category: 'frontend',
    sort_order: editingSkills.value.length + 1
  });
};

const removeSkill = (index) => {
  editingSkills.value.splice(index, 1);
};

const addSocialLink = () => {
  showAddLinkModal.value = true;
  newLinkPlatform.value = '';
  newLinkUrl.value = '';
  detectedPlatform.value = '';
};

const cancelAddLink = () => {
  showAddLinkModal.value = false;
  newLinkPlatform.value = '';
  newLinkUrl.value = '';
  detectedPlatform.value = '';
};

const confirmAddLink = () => {
  if (!newLinkPlatform.value.trim()) {
    alert('请输入平台名称');
    return;
  }
  if (!newLinkUrl.value.trim()) {
    alert('请输入链接地址');
    return;
  }
  
  const platform = newLinkPlatform.value.trim().toLowerCase().replace(/\s+/g, '_');
  if (editingSocialLinks.value[platform]) {
    alert('该平台已存在');
    return;
  }
  
  editingSocialLinks.value = {
    ...editingSocialLinks.value,
    [platform]: newLinkUrl.value.trim()
  };
  
  cancelAddLink();
};

const removeSocialLink = (platform) => {
  if (confirm(`确定要删除 ${platform} 链接吗？`)) {
    const newLinks = { ...editingSocialLinks.value };
    delete newLinks[platform];
    editingSocialLinks.value = newLinks;
  }
};

const addManifesto = () => {
  const newId = Date.now();
  editingManifestos.value.push({
    title: '',
    content: '',
    sort_order: editingManifestos.value.length + 1
  });
};

const removeManifesto = (index) => {
  editingManifestos.value.splice(index, 1);
};

const handleCancel = () => {
  emit('cancel');
};

const handleSave = () => {
  if (!editingUser.value?.name?.trim()) {
    alert('用户名不能为空');
    return;
  }
  if (!editingUser.value?.email?.trim()) {
    alert('邮箱不能为空');
    return;
  }
  
  emit('save', {
    user: editingUser.value,
    author: editingAuthor.value,
    skills: editingSkills.value,
    social_links: editingSocialLinks.value,
    manifestos: editingManifestos.value
  });
};
</script>

<template>
  <Transition name="modal">
    <div v-if="visible" class="fixed inset-0 z-[100] flex items-center justify-center">
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="handleCancel" />

      <div :class="['relative w-full max-w-4xl mx-4 rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col', isDarkMode ? 'bg-gray-800' : 'bg-white']">
        <div :class="['flex items-center justify-between p-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
          <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">
            {{ t('admin_edit') }} - {{ editingUser?.name }}
          </h3>
          <button @click="handleCancel" :class="['p-2 rounded-lg transition-colors', isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100']">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-6">
          <div :class="['p-6 rounded-lg', isDarkMode ? 'bg-gray-700' : 'bg-gray-50']">
            <h4 :class="['text-lg font-bold mb-4 flex items-center gap-2', isDarkMode ? 'text-white' : 'text-gray-900']">
              <User :size="20" />
              用户信息
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">用户名 *</label>
                <input v-model="editingUser.name" type="text" :class="['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red', isDarkMode ? 'bg-gray-600 border-gray-500 text-white' : 'bg-white border-gray-300 text-gray-900']" />
              </div>
              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">邮箱 *</label>
                <input v-model="editingUser.email" type="email" :class="['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red', isDarkMode ? 'bg-gray-600 border-gray-500 text-white' : 'bg-white border-gray-300 text-gray-900']" />
              </div>
              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">角色</label>
                <select v-model="editingUser.role_id" :class="['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red', isDarkMode ? 'bg-gray-600 border-gray-500 text-white' : 'bg-white border-gray-300 text-gray-900']">
                  <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.label }}</option>
                </select>
              </div>
              <div>
                <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">状态</label>
                <select v-model="editingUser.status" :class="['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red', isDarkMode ? 'bg-gray-600 border-gray-500 text-white' : 'bg-white border-gray-300 text-gray-900']">
                  <option value="active">活跃</option>
                  <option value="inactive">非活跃</option>
                </select>
              </div>
            </div>
          </div>

          <div v-if="editingAuthor" :class="['p-6 rounded-lg', isDarkMode ? 'bg-gray-700' : 'bg-gray-50']">
            <h4 :class="['text-lg font-bold mb-4 flex items-center gap-2', isDarkMode ? 'text-white' : 'text-gray-900']">
              <FileText :size="20" />
              作者信息
            </h4>
            <div class="mb-4">
              <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">简介</label>
              <textarea v-model="editingAuthor.bio" rows="3" :class="['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-construct-red', isDarkMode ? 'bg-gray-600 border-gray-500 text-white' : 'bg-white border-gray-300 text-gray-900']"></textarea>
            </div>

            <div>
              <div class="flex justify-between items-center mb-3">
                <label :class="['text-sm font-bold', isDarkMode ? 'text-gray-300' : 'text-gray-700']">宣言</label>
                <button @click="addManifesto" class="flex items-center gap-1 text-sm text-construct-red hover:underline">
                  <Plus :size="14" /> 添加
                </button>
              </div>
              <div class="space-y-3">
                <div v-for="(manifesto, index) in editingManifestos" :key="manifesto.id" :class="['p-3 rounded border-l-4 border-construct-red', isDarkMode ? 'bg-gray-600' : 'bg-white']">
                  <textarea v-model="manifesto.content" rows="2" :class="['w-full bg-transparent focus:outline-none', isDarkMode ? 'text-white' : 'text-gray-900']" placeholder="宣言内容..."></textarea>
                  <button @click="removeManifesto(index)" class="text-red-500 hover:text-red-700 text-sm flex items-center gap-1 mt-2">
                    <Trash2 :size="12" /> 删除
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div :class="['p-6 rounded-lg', isDarkMode ? 'bg-gray-700' : 'bg-gray-50']">
            <div class="flex justify-between items-center mb-4">
              <h4 :class="['text-lg font-bold flex items-center gap-2', isDarkMode ? 'text-white' : 'text-gray-900']">
                <Award :size="20" />
                技能
              </h4>
              <button @click="addSkill" class="flex items-center gap-1 text-sm text-construct-red hover:underline">
                <Plus :size="14" /> 添加
              </button>
            </div>
            <div class="space-y-3">
              <div v-for="(skill, index) in editingSkills" :key="skill.id" :class="['p-4 rounded-lg', isDarkMode ? 'bg-gray-600' : 'bg-white']">
                <div class="flex justify-between items-center mb-2">
                  <input v-model="skill.label" :class="['font-medium bg-transparent border-b border-gray-400 focus:outline-none focus:border-construct-red', isDarkMode ? 'text-white' : 'text-gray-900']" placeholder="技能名称..." />
                  <div class="flex items-center gap-2">
                    <input v-model.number="skill.value" type="range" min="0" max="100" class="w-24" />
                    <span class="text-sm font-bold">{{ skill.value }}%</span>
                    <button @click="removeSkill(index)" class="text-red-500 hover:text-red-700">
                      <Trash2 :size="16" />
                    </button>
                  </div>
                </div>
                <div :class="['w-full h-2 rounded-full', isDarkMode ? 'bg-gray-500' : 'bg-gray-200']">
                  <div class="h-2 rounded-full bg-construct-red transition-all" :style="{ width: skill.value + '%' }"></div>
                </div>
                <textarea v-model="skill.description" rows="2" :class="['text-sm mt-2 w-full bg-transparent border border-gray-400 rounded p-2 focus:outline-none focus:border-construct-red', isDarkMode ? 'text-gray-400' : 'text-gray-500']" placeholder="技能描述..."></textarea>
              </div>
            </div>
          </div>

          <div :class="['p-6 rounded-lg', isDarkMode ? 'bg-gray-700' : 'bg-gray-50']">
            <div class="flex justify-between items-center mb-4">
              <h4 :class="['text-lg font-bold flex items-center gap-2', isDarkMode ? 'text-white' : 'text-gray-900']">
                <LinkIcon :size="20" />
                社交链接
              </h4>
              <button @click="addSocialLink" class="flex items-center gap-1 text-sm text-construct-red hover:underline">
                <Plus :size="14" /> 添加
              </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div v-for="(url, platform) in editingSocialLinks" :key="platform" :class="['p-4 rounded-lg', isDarkMode ? 'bg-gray-600' : 'bg-white']">
                <div class="flex items-start gap-3 mb-2">
                  <component :is="getLinkIcon(platform)" :size="20" class="mt-1" />
                  <div class="flex-1">
                    <label :class="['text-xs font-bold uppercase tracking-wider mb-1 block', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ platform }}</label>
                    <input v-model="editingSocialLinks[platform]" :class="['text-sm w-full bg-transparent border-b border-gray-400 focus:outline-none focus:border-construct-red', isDarkMode ? 'text-gray-400' : 'text-gray-500']" :placeholder="`${platform} URL...`" />
                  </div>
                  <button @click="removeSocialLink(platform)" class="text-red-500 hover:text-red-700 ml-2">
                    <Trash2 :size="16" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div :class="['flex gap-3 p-6 border-t', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
          <button @click="handleCancel" :class="['flex-1 px-6 py-3 font-bold tracking-widest uppercase text-sm transition-colors rounded border', isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-100']">
            {{ t('admin_cancel') }}
          </button>
          <button @click="handleSave" class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-widest uppercase text-sm hover:bg-red-700 transition-colors rounded">
            <Save class="w-4 h-4" />
            {{ t('admin_save') }}
          </button>
        </div>
      </div>
    </div>
  </Transition>

  <Transition name="modal">
    <div v-if="showAddLinkModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-50" @click.self="cancelAddLink">
      <div :class="['modal-content w-full max-w-md mx-4 rounded-lg shadow-xl', isDarkMode ? 'bg-gray-800' : 'bg-white']">
        <div :class="['flex justify-between items-center p-6 border-b', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
          <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">添加社交链接</h3>
          <button @click="cancelAddLink" :class="['p-2 rounded-full transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
            <X :size="20" />
          </button>
        </div>

        <div class="p-6 space-y-4">
          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">平台名称</label>
            <input 
              v-model="newLinkPlatform" 
              :class="['w-full px-4 py-2 rounded border focus:outline-none focus:border-construct-red', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900']" 
              placeholder="如：instagram, tiktok, bilibili..." 
            />
            <p v-if="detectedPlatform" :class="['text-xs mt-1', isDarkMode ? 'text-green-400' : 'text-green-600']">
              ✓ 已自动识别为 {{ detectedPlatform }}
            </p>
          </div>

          <div>
            <label :class="['block text-sm font-bold mb-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">链接地址</label>
            <input 
              v-model="newLinkUrl" 
              @input="handleUrlInput"
              :class="['w-full px-4 py-2 rounded border focus:outline-none focus:border-construct-red', isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900']" 
              placeholder="https://..." 
            />
          </div>

          <div v-if="newLinkPlatform" :class="['p-3 rounded-lg flex items-center gap-3', isDarkMode ? 'bg-gray-700' : 'bg-gray-50']">
            <component :is="getLinkIcon(newLinkPlatform)" :size="24" class="text-construct-red" />
            <div>
              <p :class="['text-sm font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ newLinkPlatform }}</p>
              <p :class="['text-xs', isDarkMode ? 'text-gray-400' : 'text-gray-500']">预览图标</p>
            </div>
          </div>
        </div>

        <div :class="['flex gap-3 p-6 border-t', isDarkMode ? 'border-gray-700' : 'border-gray-200']">
          <button @click="cancelAddLink" :class="['flex-1 px-4 py-2 font-bold text-sm rounded border', isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-100']">
            取消
          </button>
          <button @click="confirmAddLink" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-construct-red text-white font-bold text-sm rounded hover:bg-red-700 transition-colors">
            <Plus :size="16" />
            添加
          </button>
        </div>
      </div>
    </div>
  </Transition>
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

.modal-enter-active .modal-content,
.modal-leave-active .modal-content {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
  transform: scale(0.95);
  opacity: 0;
}
</style>
