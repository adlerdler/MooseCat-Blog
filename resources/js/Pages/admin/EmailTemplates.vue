<script setup>
/**
 * EmailTemplates.vue - 邮件模板编辑器
 * 
 * 功能说明：
 * - 列表展示系统内置邮件模板
 * - 可视化/代码双模式编辑器
 * - 支持模板变量提示
 * - 预览预览功能
 */
import {
  ref,
  computed,
  useI18n,
  useTheme,
  Mail,
  Edit3,
  Eye,
  Save,
  RotateCcw,
  Plus,
  Trash2,
  CheckCircle,
  AlertCircle,
  FileCode,
  Layout,
  useToast
} from '../../composables/useAdminImports';

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success } = useToast();

const templates = ref([
  {
    id: 'welcome',
    name: 'Welcome Email',
    subject: 'Welcome to Archyx!',
    description: 'Sent to new users after registration.',
    lastUpdated: '2026-05-10',
    content: '<h1>Welcome, {{user_name}}!</h1><p>We are glad to have you here.</p>'
  },
  {
    id: 'password_reset',
    name: 'Password Reset',
    subject: 'Reset Your Password',
    description: 'Sent when a user requests a password reset.',
    lastUpdated: '2026-05-12',
    content: '<h1>Reset Password</h1><p>Click the link below to reset your password: <a href="{{reset_link}}">Reset Now</a></p>'
  },
  {
    id: 'comment_reply',
    name: 'Comment Reply Notification',
    subject: 'Someone replied to your comment',
    description: 'Sent when a user receives a reply to their comment.',
    lastUpdated: '2026-05-15',
    content: '<h1>New Reply</h1><p>{{replier_name}} replied to your comment: "{{comment_content}}"</p>'
  }
]);

const selectedTemplate = ref(templates.value[0]);
const activeMode = ref('visual'); // visual | code
const isSaving = ref(false);

const selectTemplate = (template) => {
  selectedTemplate.value = { ...template };
};

const saveTemplate = () => {
  isSaving.value = true;
  setTimeout(() => {
    isSaving.value = false;
    success(t('admin_save') + ' ' + t('confirm'));
  }, 1000);
};

const variables = [
  { name: 'user_name', desc: 'The recipient\'s full name' },
  { name: 'site_name', desc: 'The name of your blog' },
  { name: 'reset_link', desc: 'Unique URL for password reset' },
  { name: 'comment_content', desc: 'Content of the comment' },
  { name: 'replier_name', desc: 'Name of the person who replied' }
];
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-8 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <Mail class="text-construct-red" size="32" />
        <div>
          <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_email_templates') }}</h2>
          <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Design and manage system emails</p>
        </div>
      </div>
      <button class="flex items-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-wider rounded shadow-sm hover:bg-red-700 transition-colors">
        <Plus size="18" /> {{ t('admin_add') }}
      </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <!-- Templates List -->
      <div class="lg:col-span-1 space-y-4">
        <div 
          v-for="tpl in templates" 
          :key="tpl.id"
          @click="selectTemplate(tpl)"
          :class="[
            'p-5 border cursor-pointer transition-all',
            selectedTemplate.id === tpl.id 
              ? 'border-construct-red bg-red-50 dark:bg-construct-red/10' 
              : isDarkMode ? 'bg-gray-800 border-gray-700 hover:border-gray-500' : 'bg-white border-gray-200 hover:border-gray-300'
          ]"
        >
          <div class="flex items-center justify-between mb-2">
            <h4 :class="['font-bold', selectedTemplate.id === tpl.id ? 'text-construct-red' : isDarkMode ? 'text-white' : 'text-gray-900']">{{ tpl.name }}</h4>
            <AlertCircle v-if="tpl.id === 'welcome'" size="14" class="text-amber-500" />
          </div>
          <p class="text-xs opacity-60 line-clamp-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ tpl.description }}</p>
        </div>
      </div>

      <!-- Editor Area -->
      <div class="lg:col-span-3 space-y-6">
        <div :class="['border p-8', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
          <div class="flex items-center justify-between mb-8 border-b pb-6" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
            <div class="flex-1 mr-8">
              <label :class="['block text-[10px] font-black uppercase tracking-widest opacity-50 mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Email Subject</label>
              <input
                v-model="selectedTemplate.subject"
                type="text"
                :class="[
                  'w-full px-4 py-2 border font-bold focus:border-construct-red focus:outline-none transition-all',
                  isDarkMode ? 'bg-gray-900 border-gray-700 text-white' : 'bg-gray-50 border-gray-200 text-gray-900'
                ]"
              />
            </div>
            <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-900 p-1 rounded">
              <button 
                @click="activeMode = 'visual'"
                :class="['px-4 py-2 text-xs font-bold transition-all', activeMode === 'visual' ? 'bg-white dark:bg-gray-700 shadow-sm text-construct-red' : 'text-gray-500 hover:text-gray-700']"
              >
                <Layout size="14" class="inline mr-1" /> VISUAL
              </button>
              <button 
                @click="activeMode = 'code'"
                :class="['px-4 py-2 text-xs font-bold transition-all', activeMode === 'code' ? 'bg-white dark:bg-gray-700 shadow-sm text-construct-red' : 'text-gray-500 hover:text-gray-700']"
              >
                <FileCode size="14" class="inline mr-1" /> HTML
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- Editor -->
            <div class="xl:col-span-3">
              <div v-if="activeMode === 'code'">
                <textarea
                  v-model="selectedTemplate.content"
                  rows="15"
                  :class="[
                    'w-full p-6 border font-mono text-sm focus:border-construct-red focus:outline-none resize-none transition-all',
                    isDarkMode ? 'bg-gray-900 border-gray-700 text-emerald-400' : 'bg-gray-50 border-gray-200 text-emerald-700'
                  ]"
                ></textarea>
              </div>
              <div v-else>
                <!-- Simplified Visual Preview/Editor -->
                <div :class="['w-full min-h-[400px] p-8 border border-dashed rounded-lg flex flex-col', isDarkMode ? 'bg-gray-900 border-gray-700' : 'bg-gray-50 border-gray-300']">
                   <div class="bg-white text-gray-900 p-8 shadow-lg max-w-md mx-auto w-full border border-gray-200 rounded prose prose-sm" v-html="selectedTemplate.content"></div>
                   <div class="mt-8 text-center text-xs text-gray-400 font-bold uppercase tracking-widest">
                     [ Visual Editor Engine Loading... ]
                   </div>
                </div>
              </div>

              <div class="flex items-center justify-between mt-8">
                <div class="flex items-center gap-4">
                  <button :class="['px-6 py-2 border font-bold text-xs transition-all hover:bg-gray-100 dark:hover:bg-gray-700', isDarkMode ? 'border-gray-700 text-gray-300' : 'border-gray-200 text-gray-600']">
                    <RotateCcw size="14" class="inline mr-2" /> RESTORE DEFAULT
                  </button>
                </div>
                <button
                  @click="saveTemplate"
                  :disabled="isSaving"
                  class="flex items-center gap-2 px-10 py-3 bg-construct-red text-white font-bold tracking-wider rounded shadow-sm hover:bg-red-700 transition-colors disabled:opacity-50"
                >
                  <Save size="18" /> {{ isSaving ? 'SAVING...' : t('admin_save') }}
                </button>
              </div>
            </div>

            <!-- Variables Sidebar -->
            <div class="xl:col-span-1 space-y-4">
              <h5 class="text-xs font-black uppercase tracking-widest opacity-40">Available Tokens</h5>
              <div class="space-y-3">
                  <div 
                    v-for="variable in variables" 
                    :key="variable.name"
                    class="group p-3 rounded border border-transparent hover:border-construct-red/20 hover:bg-construct-red/5 transition-all cursor-help"
                  >
                    <code 
                      v-text="'{{ ' + variable.name + ' }}'"
                      class="text-[10px] font-black text-construct-red px-1.5 py-0.5 bg-construct-red/5 rounded"
                    ></code>
                    <p class="text-[10px] mt-1 opacity-50 font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ variable.desc }}</p>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-display {
  font-family: 'Outfit', sans-serif;
}
.prose h1 { margin-top: 0; }
</style>
