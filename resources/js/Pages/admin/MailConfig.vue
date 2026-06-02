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
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <Mail class="text-construct-red" size="32" />
          <div>
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_settings_mail') }}</h2>
            <p :class="['text-sm font-bold tracking-widest uppercase', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_mail_config_subtitle') }}</p>
          </div>
        </div>
        <div class="flex items-center gap-4">
          <button
            @click="openTestDialog"
            :disabled="isSending"
            class="flex items-center gap-2 px-6 py-3 border font-bold tracking-wider transition-colors rounded disabled:opacity-50"
            :class="[
              isDarkMode ? 'border-gray-700 hover:bg-gray-700 text-white' : 'border-gray-300 hover:bg-gray-100 text-gray-900'
            ]"
          >
            <Send v-if="!isSending" size="18" />
            <span v-else class="inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
            {{ isSending ? t('admin_send_test') + '...' : t('admin_send_test') }}
          </button>
          <button
            @click="saveSettings"
            :disabled="isSaving"
            class="flex items-center gap-2 px-8 py-3 bg-construct-red hover:bg-red-600 text-white font-bold tracking-wider transition-colors rounded shadow-sm disabled:opacity-50"
          >
            <Save size="18" :style="{ color: '#ffffff' }" /> {{ isSaving ? t('admin_save') + '...' : t('admin_save') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Mail Configuration -->
    <div :class="['border p-8', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <div class="mb-8">
        <h3 class="font-display text-2xl tracking-tighter mb-6 flex items-center gap-3">
          <Monitor size="24" class="text-construct-red" />
          {{ t('admin_smtp_server') }}
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_smtp_host') }}</label>
            <input
              v-model="mailSettings.host"
              type="text"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all font-mono',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
          </div>
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_smtp_port') }}</label>
            <input
              v-model.number="mailSettings.port"
              type="number"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all font-mono',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
          </div>
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_smtp_encryption') }}</label>
            <select
              v-model="mailSettings.encryption"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            >
              <option v-for="opt in encryptionOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </select>
          </div>
        </div>
      </div>

      <div class="mb-8">
        <h3 class="font-display text-2xl tracking-tighter mb-6 flex items-center gap-3">
          <Shield size="24" class="text-construct-red" />
          {{ t('admin_smtp_auth') }}
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_smtp_user') }}</label>
            <input
              v-model="mailSettings.username"
              type="text"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all font-mono',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
          </div>
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_smtp_pass') }}</label>
            <input
              v-model="mailSettings.password"
              type="password"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all font-mono',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
          </div>
        </div>
      </div>

      <div>
        <h3 class="font-display text-2xl tracking-tighter mb-6 flex items-center gap-3">
          <User size="24" class="text-construct-red" />
          {{ t('admin_sender_info') }}
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_sender_address') }}</label>
            <input
              v-model="mailSettings.fromAddress"
              type="email"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all font-mono',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
          </div>
          <div>
            <label :class="['block text-sm font-bold tracking-widest uppercase mb-2', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ t('admin_sender_name') }}</label>
            <input
              v-model="mailSettings.fromName"
              type="text"
              :class="[
                'w-full px-4 py-3 border focus:border-construct-red focus:outline-none transition-all',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Test Email Dialog -->
    <div v-if="showTestDialog" class="fixed inset-0 z-[200] flex items-center justify-center">
      <div class="absolute inset-0 bg-black/60" @click="showTestDialog = false"></div>
      <div :class="['relative w-full max-w-md mx-4 p-8 border shadow-2xl z-10', isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <div class="flex items-center justify-between mb-6">
          <h3 class="font-display text-2xl tracking-tighter">{{ t('admin_mail_test_title') }}</h3>
          <button @click="showTestDialog = false" :class="isDarkMode ? 'text-gray-400 hover:text-white' : 'text-gray-500 hover:text-gray-900'">
            <X size="20" />
          </button>
        </div>

        <!-- Option 1: Current User -->
        <label :class="['flex items-center gap-3 p-4 border mb-3 cursor-pointer transition-colors', testUseCustomEmail ? (isDarkMode ? 'border-gray-700 opacity-50' : 'border-gray-200 opacity-50') : (isDarkMode ? 'border-construct-red bg-red-500/10' : 'border-construct-red bg-red-50')]">
          <input type="radio" v-model="testUseCustomEmail" :value="false" class="sr-only" />
          <div :class="['w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0', !testUseCustomEmail ? 'border-construct-red' : (isDarkMode ? 'border-gray-600' : 'border-gray-300')]">
            <div v-if="!testUseCustomEmail" class="w-2.5 h-2.5 rounded-full bg-construct-red"></div>
          </div>
          <div>
            <p :class="['text-sm font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_mail_test_current_user') }}</p>
            <p :class="['text-xs font-mono mt-0.5', isDarkMode ? 'text-gray-400' : 'text-gray-500']">{{ currentUserEmail }}</p>
          </div>
        </label>

        <!-- Option 2: Custom Email -->
        <label :class="['flex items-start gap-3 p-4 border cursor-pointer transition-colors', testUseCustomEmail ? (isDarkMode ? 'border-construct-red bg-red-500/10' : 'border-construct-red bg-red-50') : (isDarkMode ? 'border-gray-700 opacity-50' : 'border-gray-200 opacity-50')]">
          <input type="radio" v-model="testUseCustomEmail" :value="true" class="sr-only" />
          <div :class="['w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 mt-0.5', testUseCustomEmail ? 'border-construct-red' : (isDarkMode ? 'border-gray-600' : 'border-gray-300')]">
            <div v-if="testUseCustomEmail" class="w-2.5 h-2.5 rounded-full bg-construct-red"></div>
          </div>
          <div class="flex-1">
            <p :class="['text-sm font-bold mb-2', isDarkMode ? 'text-white' : 'text-gray-900']">{{ t('admin_mail_test_custom_email') }}</p>
            <input
              v-model="testCustomEmail"
              type="email"
              :placeholder="'example@mail.com'"
              :disabled="!testUseCustomEmail"
              :class="[
                'w-full px-4 py-2.5 border text-sm font-mono focus:border-construct-red focus:outline-none transition-all',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400',
                !testUseCustomEmail ? 'opacity-50' : ''
              ]"
            />
          </div>
        </label>

        <!-- Actions -->
        <div class="flex items-center gap-3 mt-6">
          <button
            @click="showTestDialog = false"
            :class="['flex-1 px-4 py-3 text-sm font-bold tracking-wider uppercase border transition-colors', isDarkMode ? 'border-gray-700 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-600 hover:bg-gray-100']"
          >
            {{ t('admin_cancel') }}
          </button>
          <button
            @click="confirmSendTest"
            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-construct-red hover:bg-red-600 text-white text-sm font-bold tracking-wider uppercase transition-colors"
          >
            <Send size="16" /> {{ t('admin_send_test') }}
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
