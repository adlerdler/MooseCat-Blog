<script setup>
import { usePage } from '@inertiajs/vue3';
import {
  ref,
  computed,
  useI18n,
  useTheme,
  Mail,
  Send,
  Save,
  Shield,
  Monitor,
  User,
  X,
  useToast,
  router,
  ConfirmDialog
} from '../../composables/useAdminImports';

const page = usePage();
const props = defineProps({
  mailConfig: { type: Object, default: () => ({}) },
});

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success, error } = useToast();

const mailSettings = ref({ ...props.mailConfig });
const isSaving = ref(false);
const isSending = ref(false);
const showSaveConfirm = ref(false);

// 测试邮件弹窗
const showTestDialog = ref(false);
const testUseCustomEmail = ref(false);
const testCustomEmail = ref('');

const currentUserEmail = computed(() => page.props.auth?.user?.email || '');

const encryptionOptions = [
  { value: 'tls', label: 'TLS' },
  { value: 'ssl', label: 'SSL' },
  { value: 'none', label: 'None' }
];

const saveSettings = () => {
  showSaveConfirm.value = true;
};

const confirmSave = () => {
  showSaveConfirm.value = false;
  isSaving.value = true;
  router.put('/admin/mail-config', mailSettings.value, {
    preserveState: false,
    preserveScroll: true,
    onSuccess: () => {
      success(t('admin_save') + ' ' + t('confirm'));
    },
    onError: () => {
      error(t('admin_save_failed') || 'Save failed');
    },
    onFinish: () => {
      isSaving.value = false;
    },
  });
};

const openTestDialog = () => {
  testUseCustomEmail.value = false;
  testCustomEmail.value = '';
  showTestDialog.value = true;
};

const confirmSendTest = () => {
  if (testUseCustomEmail.value && !testCustomEmail.value.trim()) {
    error(t('admin_mail_test_custom_required'));
    return;
  }
  showTestDialog.value = false;
  if (isSending.value) return;
  isSending.value = true;

  const payload = { ...mailSettings.value };
  if (testUseCustomEmail.value) {
    payload.customEmail = testCustomEmail.value.trim();
  }

  router.post('/admin/mail-config/test', payload, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (p) => {
      const flashMsg = p.props.flash?.success;
      success(flashMsg || t('admin_mail_test_sent'));
    },
    onError: (errs) => {
      error(errs.test || t('admin_mail_test_failed'));
    },
    onFinish: () => {
      isSending.value = false;
    },
  });
};
</script>

<template>
  <div class="p-8">
    <!-- Page Header -->
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Mail class="text-construct-red" :size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">
              {{ t('admin_settings_mail') }}
            </h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
            {{ t('admin_mail_config_subtitle') }}
          </p>
        </div>
        <div class="flex items-center gap-4">
          <button
            @click="openTestDialog"
            :disabled="isSending"
            class="flex items-center gap-3 px-6 py-4 border-2 font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 rounded-xl disabled:opacity-50"
            :class="[
              isDarkMode 
                ? 'border-gray-700 hover:bg-gray-800 text-gray-300' 
                : 'border-gray-200 hover:bg-gray-50 text-gray-600'
            ]"
          >
            <Send v-if="!isSending" :size="18" />
            <span v-else class="inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
            {{ isSending ? t('admin_send_test') + '...' : t('admin_send_test') }}
          </button>
          <button
            @click="saveSettings"
            :disabled="isSaving"
            class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl disabled:opacity-50"
          >
            <Save v-if="!isSaving" :size="18" class="!text-white" />
            <span v-else class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            {{ isSaving ? t('admin_save') + '...' : t('admin_save') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Mail Configuration Card -->
    <div :class="[
      'p-10 rounded-3xl transition-all duration-500 border',
      isDarkMode 
        ? 'bg-gray-900/50 border-gray-800 backdrop-blur-xl' 
        : 'bg-white/50 border-white backdrop-blur-xl shadow-2xl shadow-gray-200/50'
    ]">
      <!-- SMTP Server Section -->
      <div class="mb-12">
        <div class="flex items-center gap-3 mb-8">
          <div class="p-2 rounded-lg bg-construct-red/10">
            <Monitor :size="20" class="text-construct-red" />
          </div>
          <h3 :class="['font-display text-xl font-bold tracking-tight', isDarkMode ? 'text-white' : 'text-gray-900']">
            {{ t('admin_smtp_server') }}
          </h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="space-y-2">
            <label :class="['block text-[10px] font-black tracking-[0.2em] uppercase opacity-50 ml-1', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ t('admin_smtp_host') }}
            </label>
            <input
              v-model="mailSettings.host"
              type="text"
              :placeholder="'smtp.example.com'"
              :class="[
                'w-full px-5 py-3.5 rounded-xl border-2 transition-all duration-300 font-mono text-sm focus:outline-none focus:ring-4',
                isDarkMode 
                  ? 'bg-gray-800/50 border-gray-700 text-white placeholder-gray-600 focus:border-construct-red/50 focus:ring-construct-red/10' 
                  : 'bg-gray-50/50 border-gray-100 text-gray-900 placeholder-gray-400 focus:border-construct-red/30 focus:ring-construct-red/5'
              ]"
            />
          </div>
          <div class="space-y-2">
            <label :class="['block text-[10px] font-black tracking-[0.2em] uppercase opacity-50 ml-1', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ t('admin_smtp_port') }}
            </label>
            <input
              v-model.number="mailSettings.port"
              type="number"
              :placeholder="'587'"
              :class="[
                'w-full px-5 py-3.5 rounded-xl border-2 transition-all duration-300 font-mono text-sm focus:outline-none focus:ring-4',
                isDarkMode 
                  ? 'bg-gray-800/50 border-gray-700 text-white placeholder-gray-600 focus:border-construct-red/50 focus:ring-construct-red/10' 
                  : 'bg-gray-50/50 border-gray-100 text-gray-900 placeholder-gray-400 focus:border-construct-red/30 focus:ring-construct-red/5'
              ]"
            />
          </div>
          <div class="space-y-2">
            <label :class="['block text-[10px] font-black tracking-[0.2em] uppercase opacity-50 ml-1', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ t('admin_smtp_encryption') }}
            </label>
            <div class="relative">
              <select
                v-model="mailSettings.encryption"
                :class="[
                  'w-full px-5 py-3.5 rounded-xl border-2 transition-all duration-300 text-sm focus:outline-none focus:ring-4 appearance-none',
                  isDarkMode 
                    ? 'bg-gray-800/50 border-gray-700 text-white focus:border-construct-red/50 focus:ring-construct-red/10' 
                    : 'bg-gray-50/50 border-gray-100 text-gray-900 focus:border-construct-red/30 focus:ring-construct-red/5'
                ]"
              >
                <option v-for="opt in encryptionOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
              </select>
              <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none opacity-50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Authentication Section -->
      <div class="mb-12">
        <div class="flex items-center gap-3 mb-8">
          <div class="p-2 rounded-lg bg-construct-red/10">
            <Shield :size="20" class="text-construct-red" />
          </div>
          <h3 :class="['font-display text-xl font-bold tracking-tight', isDarkMode ? 'text-white' : 'text-gray-900']">
            {{ t('admin_smtp_auth') }}
          </h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-2">
            <label :class="['block text-[10px] font-black tracking-[0.2em] uppercase opacity-50 ml-1', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ t('admin_smtp_user') }}
            </label>
            <input
              v-model="mailSettings.username"
              type="text"
              :class="[
                'w-full px-5 py-3.5 rounded-xl border-2 transition-all duration-300 font-mono text-sm focus:outline-none focus:ring-4',
                isDarkMode 
                  ? 'bg-gray-800/50 border-gray-700 text-white focus:border-construct-red/50 focus:ring-construct-red/10' 
                  : 'bg-gray-50/50 border-gray-100 text-gray-900 focus:border-construct-red/30 focus:ring-construct-red/5'
              ]"
            />
          </div>
          <div class="space-y-2">
            <label :class="['block text-[10px] font-black tracking-[0.2em] uppercase opacity-50 ml-1', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ t('admin_smtp_pass') }}
            </label>
            <input
              v-model="mailSettings.password"
              type="password"
              :class="[
                'w-full px-5 py-3.5 rounded-xl border-2 transition-all duration-300 font-mono text-sm focus:outline-none focus:ring-4',
                isDarkMode 
                  ? 'bg-gray-800/50 border-gray-700 text-white focus:border-construct-red/50 focus:ring-construct-red/10' 
                  : 'bg-gray-50/50 border-gray-100 text-gray-900 focus:border-construct-red/30 focus:ring-construct-red/5'
              ]"
            />
          </div>
        </div>
      </div>

      <!-- Sender Info Section -->
      <div>
        <div class="flex items-center gap-3 mb-8">
          <div class="p-2 rounded-lg bg-construct-red/10">
            <User :size="20" class="text-construct-red" />
          </div>
          <h3 :class="['font-display text-xl font-bold tracking-tight', isDarkMode ? 'text-white' : 'text-gray-900']">
            {{ t('admin_sender_info') }}
          </h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-2">
            <label :class="['block text-[10px] font-black tracking-[0.2em] uppercase opacity-50 ml-1', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ t('admin_sender_address') }}
            </label>
            <input
              v-model="mailSettings.fromAddress"
              type="email"
              :class="[
                'w-full px-5 py-3.5 rounded-xl border-2 transition-all duration-300 font-mono text-sm focus:outline-none focus:ring-4',
                isDarkMode 
                  ? 'bg-gray-800/50 border-gray-700 text-white focus:border-construct-red/50 focus:ring-construct-red/10' 
                  : 'bg-gray-50/50 border-gray-100 text-gray-900 focus:border-construct-red/30 focus:ring-construct-red/5'
              ]"
            />
          </div>
          <div class="space-y-2">
            <label :class="['block text-[10px] font-black tracking-[0.2em] uppercase opacity-50 ml-1', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
              {{ t('admin_sender_name') }}
            </label>
            <input
              v-model="mailSettings.fromName"
              type="text"
              :class="[
                'w-full px-5 py-3.5 rounded-xl border-2 transition-all duration-300 text-sm focus:outline-none focus:ring-4',
                isDarkMode 
                  ? 'bg-gray-800/50 border-gray-700 text-white focus:border-construct-red/50 focus:ring-construct-red/10' 
                  : 'bg-gray-50/50 border-gray-100 text-gray-900 focus:border-construct-red/30 focus:ring-construct-red/5'
              ]"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Test Email Dialog -->
    <div v-if="showTestDialog" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showTestDialog = false"></div>
      
      <div :class="[
        'relative w-full max-w-lg overflow-hidden rounded-3xl shadow-2xl transition-all duration-500 border',
        isDarkMode ? 'bg-gray-900 border-gray-800' : 'bg-white border-white'
      ]">
        <!-- Dialog Header -->
        <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="p-2 rounded-lg bg-construct-red/10">
              <Send :size="18" class="text-construct-red" />
            </div>
            <h3 :class="['font-display text-xl font-bold tracking-tight', isDarkMode ? 'text-white' : 'text-gray-900']">
              {{ t('admin_mail_test_title') }}
            </h3>
          </div>
          <button @click="showTestDialog = false" :class="[
            'p-2 rounded-xl transition-colors',
            isDarkMode ? 'text-gray-400 hover:text-white hover:bg-gray-800' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'
          ]">
            <X :size="20" />
          </button>
        </div>

        <div class="p-8 space-y-4">
          <!-- Option 1: Current User -->
          <label 
            @click="testUseCustomEmail = false"
            :class="[
              'group relative flex items-center gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all duration-300',
              !testUseCustomEmail 
                ? (isDarkMode ? 'border-construct-red bg-construct-red/10' : 'border-construct-red bg-red-50/50')
                : (isDarkMode ? 'border-gray-800 hover:border-gray-700' : 'border-gray-100 hover:border-gray-200')
            ]"
          >
            <div :class="[
              'w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-300',
              !testUseCustomEmail ? 'border-construct-red scale-110' : (isDarkMode ? 'border-gray-700' : 'border-gray-300')
            ]">
              <div v-if="!testUseCustomEmail" class="w-3 h-3 rounded-full bg-construct-red shadow-lg shadow-construct-red/50"></div>
            </div>
            <div>
              <p :class="['text-sm font-black tracking-wider uppercase', !testUseCustomEmail ? 'text-construct-red' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]">
                {{ t('admin_mail_test_current_user') }}
              </p>
              <p :class="['text-xs font-mono mt-1 opacity-60', isDarkMode ? 'text-gray-300' : 'text-gray-600']">
                {{ currentUserEmail }}
              </p>
            </div>
          </label>

          <!-- Option 2: Custom Email -->
          <div 
            :class="[
              'group relative rounded-2xl border-2 transition-all duration-300',
              testUseCustomEmail 
                ? (isDarkMode ? 'border-construct-red bg-construct-red/10' : 'border-construct-red bg-red-50/50')
                : (isDarkMode ? 'border-gray-800' : 'border-gray-100')
            ]"
          >
            <label 
              @click="testUseCustomEmail = true"
              class="flex items-start gap-4 p-5 cursor-pointer"
            >
              <div :class="[
                'w-6 h-6 rounded-full border-2 flex items-center justify-center mt-0.5 transition-all duration-300',
                testUseCustomEmail ? 'border-construct-red scale-110' : (isDarkMode ? 'border-gray-700' : 'border-gray-300')
              ]">
                <div v-if="testUseCustomEmail" class="w-3 h-3 rounded-full bg-construct-red shadow-lg shadow-construct-red/50"></div>
              </div>
              <div class="flex-1">
                <p :class="['text-sm font-black tracking-wider uppercase mb-3', testUseCustomEmail ? 'text-construct-red' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]">
                  {{ t('admin_mail_test_custom_email') }}
                </p>
                <input
                  v-model="testCustomEmail"
                  type="email"
                  :placeholder="'example@mail.com'"
                  :disabled="!testUseCustomEmail"
                  @focus="testUseCustomEmail = true"
                  :class="[
                    'w-full px-4 py-3 rounded-xl border-2 text-sm font-mono transition-all duration-300 focus:outline-none focus:ring-4',
                    isDarkMode 
                      ? 'bg-gray-800/50 border-gray-700 text-white placeholder-gray-600 focus:border-construct-red/50 focus:ring-construct-red/10' 
                      : 'bg-white border-gray-100 text-gray-900 placeholder-gray-400 focus:border-construct-red/30 focus:ring-construct-red/5',
                    !testUseCustomEmail ? 'opacity-40 cursor-not-allowed' : ''
                  ]"
                />
              </div>
            </label>
          </div>
        </div>

        <!-- Dialog Footer -->
        <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-800/50 flex items-center gap-4">
          <button
            @click="showTestDialog = false"
            :class="[
              'flex-1 px-6 py-3.5 rounded-xl font-black text-xs uppercase tracking-[0.2em] transition-all hover:bg-gray-200 dark:hover:bg-gray-700',
              isDarkMode ? 'text-gray-400' : 'text-gray-500'
            ]"
          >
            {{ t('admin_cancel') }}
          </button>
          <button
            @click="confirmSendTest"
            class="flex-1 flex items-center justify-center gap-3 px-6 py-3.5 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] rounded-xl transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20"
          >
            <Send :size="16" class="!text-white" />
            {{ t('admin_send_test') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Confirm Dialog -->
    <ConfirmDialog
      v-model:visible="showSaveConfirm"
      :title="t('admin_save_confirm_title')"
      :content="t('admin_save_confirm_content')"
      :confirm-text="t('admin_save')"
      confirm-variant="primary"
      @confirm="confirmSave"
      @cancel="showSaveConfirm = false"
    />
  </div>
</template>

<style scoped>
.font-display {
  font-family: 'Outfit', sans-serif;
}
</style>
