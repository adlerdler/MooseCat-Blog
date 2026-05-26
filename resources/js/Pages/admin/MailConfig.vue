<script setup>
import {
  ref,
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
  ConfirmDialog
} from '../../composables/useAdminImports';

const props = defineProps({
  mailConfig: { type: Object, default: () => ({}) },
});

const { t } = useI18n();

const getDefaultMailConfig = () => {
  const configs = props.mailConfig?.configs || [];
  return configs.find(config => config.is_default) || configs[0] || {};
};
const { isDarkMode } = useTheme();
const { success } = useToast();

const mailSettings = ref({ ...getDefaultMailConfig() });
const isSaving = ref(false);
const showSaveConfirm = ref(false);

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
  setTimeout(() => {
    console.log('Mail settings saved:', mailSettings.value);
    isSaving.value = false;
    success(t('admin_save') + ' ' + t('confirm'));
  }, 500);
};

const sendTestEmail = () => {
  success(t('admin_send_test') + ' ' + t('confirm'));
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
            @click="sendTestEmail"
            class="flex items-center gap-2 px-6 py-3 border font-bold tracking-wider transition-colors rounded"
            :class="[
              isDarkMode ? 'border-gray-700 hover:bg-gray-700 text-white' : 'border-gray-300 hover:bg-gray-100 text-gray-900'
            ]"
          >
            <Send size="18" /> {{ t('admin_send_test') }}
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
