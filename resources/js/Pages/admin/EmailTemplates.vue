<script setup>
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
  X,
  router,
  ConfirmDialog,
  useToast
} from '../../composables/useAdminImports';

const props = defineProps({
  templates: { type: Array, default: () => [] },
  template: { type: Object, default: null },
});

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success } = useToast();

const variables = [
  { name: 'user_name', desc: '收件人的全名' },
  { name: 'site_name', desc: '博客/网站名称' },
  { name: 'reset_link', desc: '密码重置的唯一链接' },
  { name: 'comment_content', desc: '评论内容' },
  { name: 'replier_name', desc: '回复者的名称' },
  { name: 'post_url', desc: '文章/评论所在页面的链接' }
];

const templates = computed(() => {
  return (props.templates || []).map(t => ({
    id: t.id,
    key: t.name,
    name: t.name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
    subject: t.subject,
    description: t.description,
    content: t.content,
  }));
});

const selectedTemplate = ref(null);

// Auto-select the template from edit mode, or the first one
if (props.template) {
  const match = templates.value.find(t => t.id === props.template.id);
  selectedTemplate.value = { ...(match || {}) };
} else if (templates.value.length > 0) {
  selectedTemplate.value = { ...templates.value[0] };
} else {
  selectedTemplate.value = {};
}

const activeMode = ref('visual');
const isSaving = ref(false);
const showSaveConfirm = ref(false);

const selectTemplate = (tpl) => {
  selectedTemplate.value = { ...tpl };
};

const saveTemplate = () => {
  showSaveConfirm.value = true;
};

const confirmSave = () => {
  showSaveConfirm.value = false;
  isSaving.value = true;
  router.put(`/admin/email-templates/${selectedTemplate.value.id}`, {
    subject: selectedTemplate.value.subject,
    content: selectedTemplate.value.content,
  }, {
    preserveState: false,
    preserveScroll: true,
    onFinish: () => {
      isSaving.value = false;
    },
  });
};

// ── Add Template Modal ──────────────────────────────────
const showAddModal = ref(false);
const isCreating = ref(false);
const newTemplate = ref({
  name: '',
  subject: '',
  description: '',
  content: '',
});

const openAddModal = () => {
  newTemplate.value = { name: '', subject: '', description: '', content: '' };
  showAddModal.value = true;
};

const createTemplate = () => {
  isCreating.value = true;
  router.post('/admin/email-templates', newTemplate.value, {
    preserveState: false,
    preserveScroll: true,
    onFinish: () => {
      isCreating.value = false;
      showAddModal.value = false;
    },
  });
};
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
      <button @click="openAddModal" class="flex items-center gap-2 px-6 py-3 bg-construct-red text-white font-bold tracking-wider rounded shadow-sm hover:bg-red-700 transition-colors">
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
            <AlertCircle v-if="tpl.key === 'welcome_email'" size="14" class="text-amber-500" />
          </div>
          <p class="text-xs opacity-60 line-clamp-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ tpl.description }}</p>
        </div>
      </div>

      <!-- Editor Area -->
      <div class="lg:col-span-3 space-y-6" v-if="selectedTemplate.id">
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

    <!-- Save Confirmation Dialog -->
    <ConfirmDialog
      v-model:visible="showSaveConfirm"
      :title="t('admin_save_confirm_title')"
      :content="t('admin_save_confirm_content')"
      :confirm-text="t('admin_save')"
      :cancel-text="t('admin_cancel')"
      confirm-variant="primary"
      @confirm="confirmSave"
      @cancel="showSaveConfirm = false"
    />

    <!-- Add Template Modal -->
    <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
      <div :class="['w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-lg shadow-xl', isDarkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200']">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
          <h3 :class="['font-display text-2xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_add') }} Email Template</h3>
          <button @click="showAddModal = false" :class="[isDarkMode ? 'text-gray-400 hover:text-white' : 'text-gray-500 hover:text-gray-900']">
            <X size="20" />
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
          <!-- Template Key -->
          <div>
            <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              Template Key <span class="text-construct-red">*</span>
            </label>
            <input
              v-model="newTemplate.name"
              type="text"
              placeholder="e.g. order_confirmation"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none font-mono text-sm',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
            <p class="text-[10px] mt-1 opacity-40 font-medium">唯一标识符，使用小写字母 + 下划线，如 welcome_email</p>
          </div>

          <!-- Subject -->
          <div>
            <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              Email Subject <span class="text-construct-red">*</span>
            </label>
            <input
              v-model="newTemplate.subject"
              type="text"
              placeholder="Enter email subject..."
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
          </div>

          <!-- Description -->
          <div>
            <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">Description</label>
            <input
              v-model="newTemplate.description"
              type="text"
              placeholder="Brief description of this template..."
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
          </div>

          <!-- Content -->
          <div>
            <label :class="['block text-xs font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              HTML Content <span class="text-construct-red">*</span>
            </label>
            <textarea
              v-model="newTemplate.content"
              rows="10"
              placeholder="<h1>Hello {{user_name}}!</h1><p>Your order has been confirmed.</p>"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none font-mono text-sm resize-none',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-emerald-400' : 'bg-white border-gray-300 text-emerald-700'
              ]"
            ></textarea>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-4 p-6 border-t" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
          <button
            @click="showAddModal = false"
            :class="['px-6 py-2 font-bold text-sm tracking-wider border transition-colors', isDarkMode ? 'border-gray-700 text-gray-300 hover:bg-gray-700' : 'border-gray-200 text-gray-600 hover:bg-gray-100']"
          >
            {{ t('admin_cancel') }}
          </button>
          <button
            @click="createTemplate"
            :disabled="isCreating || !newTemplate.name || !newTemplate.subject || !newTemplate.content"
            class="px-6 py-2 bg-construct-red text-white font-bold text-sm tracking-wider rounded hover:bg-red-700 transition-colors disabled:opacity-50"
          >
            <Plus size="16" class="inline mr-1" /> {{ isCreating ? 'CREATING...' : t('admin_add') }}
          </button>
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
