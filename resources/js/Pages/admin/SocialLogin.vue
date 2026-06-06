<script setup>
/**
 * 社交登录管理页（后台）
 * 管理 Google / GitHub OAuth 配置
 * Tab 切换布局，与 I18nManager / SocialLinks 风格一致
 */
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useTheme } from '../../composables/useTheme';
import { useToast } from '../../composables/useToast';
import { Shield, RefreshCw, Info, Plus, X, Globe, Trash2 } from 'lucide-vue-next';

const { t } = useI18n();
const { isDarkMode } = useTheme();
const { success: toastSuccess, error: toastError } = useToast();

const props = defineProps({
  configs: { type: Array, default: () => [] },
});

const activeTab = ref('google');

const defaultProviders = ['google', 'github'];

const providerLabels = {
  google: 'Google',
  github: 'GitHub',
};

const providerIcons = {
  google: 'M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.627 0-8.4-3.733-8.4-8.373 0-4.64 3.773-8.373 8.4-8.373 2.533 0 4.413 1.013 5.813 2.347l2.347-2.347C18.213 2.347 15.56 1 12.48 1 5.867 1 .52 6.08.52 12.36s5.347 11.36 11.96 11.36c3.52 0 6.187-1.28 8.267-3.693 2.133-2.133 2.8-5.12 2.8-7.507 0-.64-.053-1.253-.16-1.84H12.48v3.24z',
  github: 'M12 1C5.922 1 1 5.922 1 12c0 4.891 3.148 9.016 7.521 10.477.55.1.753-.239.753-.53 0-.26-.01-1.118-.015-2.02-3.06.665-3.706-1.299-3.706-1.299-.5-1.27-1.221-1.608-1.221-1.608-.999-.683.075-.669.075-.669 1.104.078 1.685 1.134 1.685 1.134.981 1.68 2.575 1.195 3.202.914.1-.71.384-1.195.698-1.47-2.442-.278-5.01-1.221-5.01-5.438 0-1.202.429-2.185 1.133-2.955-.114-.278-.492-1.398.108-2.913 0 0 .925-.295 3.03 1.129A10.56 10.56 0 0112 6.322a10.56 10.56 0 012.756.375c2.105-1.424 3.03-1.129 3.03-1.129.6 1.515.222 2.635.108 2.913.704.77 1.133 1.753 1.133 2.955 0 4.226-2.572 5.157-5.022 5.43.395.34.747 1.01.747 2.037 0 1.47-.014 2.657-.014 3.017 0 .294.2.637.756.53C19.854 21.013 23 16.89 23 12c0-6.078-4.922-11-11-11z',
};

// 从 configs 中提取已有的自定义 provider
const customProviders = reactive(
  props.configs
    .filter((c) => !defaultProviders.includes(c.provider))
    .map((c) => ({
      provider: c.provider,
      name: c.name || c.provider,
      client_id: c.client_id || '',
      client_secret: '',
      redirect_uri: c.redirect_uri || `http://localhost:8000/auth/${c.provider}/callback`,
      enabled: c.enabled || false,
      saving: false,
      testing: false,
    }))
);

// 所有 Tab（默认 + 自定义）
const allTabs = computed(() => [
  ...defaultProviders.map((p) => ({ key: p, label: providerLabels[p], isCustom: false })),
  ...customProviders.map((c) => ({ key: c.provider, label: c.name, isCustom: true })),
]);

// 将 configs 转换为 reactive 映射（含默认和自定义）
const configMap = reactive({});
defaultProviders.forEach((p) => {
  const existing = props.configs.find((c) => c.provider === p);
  configMap[p] = reactive({
    provider: p,
    client_id: existing?.client_id || '',
    client_secret: '',
    redirect_uri: existing?.redirect_uri || `http://localhost:8000/auth/${p}/callback`,
    enabled: existing?.enabled || false,
    name: existing?.name || providerLabels[p],
    saving: false,
    testing: false,
    isCustom: false,
  });
});
customProviders.forEach((c) => {
  configMap[c.provider] = c;
  c.isCustom = true;
});

// 添加弹窗
const showAddModal = ref(false);
const newProviderName = ref('');

const openAddModal = () => {
  showAddModal.value = true;
  newProviderName.value = '';
};

const closeAddModal = () => {
  showAddModal.value = false;
  newProviderName.value = '';
};

// 添加自定义 provider
const addCustomProvider = () => {
  const name = newProviderName.value.trim();
  if (!name) return;
  const key = name.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
  if (!key) return;

  // 检查重复
  if (configMap[key] || defaultProviders.includes(key)) {
    toastError(t('admin.provider_exists') || 'Provider already exists');
    return;
  }

  const newConfig = reactive({
    provider: key,
    name,
    client_id: '',
    client_secret: '',
    redirect_uri: `${window.location.origin}/auth/${key}/callback`,
    enabled: false,
    saving: false,
    testing: false,
    isCustom: true,
  });

  customProviders.push(newConfig);
  configMap[key] = newConfig;
  activeTab.value = key;
  closeAddModal();
};

// 删除自定义 provider
const deleteCustomProvider = (provider) => {
  if (defaultProviders.includes(provider)) return;
  const idx = customProviders.findIndex((c) => c.provider === provider);
  if (idx > -1) {
    customProviders.splice(idx, 1);
    delete configMap[provider];
    // 切换到第一个可用 tab
    const first = allTabs.value[0];
    if (first) activeTab.value = first.key;
  }
};

// 保存配置
const saveConfig = () => {
  const provider = activeTab.value;
  const config = configMap[provider];
  config.saving = true;

  router.put(route('admin.social-login.update', { provider }), {
    name: config.name,
    client_id: config.client_id || null,
    client_secret: config.client_secret || null,
    redirect_uri: config.redirect_uri || null,
    enabled: config.enabled,
  }, {
    preserveState: true,
    onSuccess: () => {
      toastSuccess(t('common.saved') || 'Saved');
      config.client_secret = '';
    },
    onError: () => {
      toastError(t('common.save_failed') || 'Save failed');
    },
    onFinish: () => {
      config.saving = false;
    },
  });
};

// 测试配置
const testConfig = () => {
  const provider = activeTab.value;
  const config = configMap[provider];
  if (!config.client_id) {
    toastError(t('admin.social_login_incomplete') || 'Please enter Client ID first');
    return;
  }
  config.testing = true;
  router.post(route('admin.social-login.test', { provider }), {}, {
    preserveState: true,
    onSuccess: () => {
      toastSuccess(t('admin.test_connection_success') || 'Connection test successful');
    },
    onError: () => {
      toastError(t('admin.test_connection_failed') || 'Connection test failed');
    },
    onFinish: () => {
      config.testing = false;
    },
  });
};

// 生成 callback URL
const generateCallbackUrl = () => {
  configMap[activeTab.value].redirect_uri = `${window.location.origin}/auth/${activeTab.value}/callback`;
};

// 当前 provider 是否为自定义
const isCustomProvider = computed(() => configMap[activeTab.value]?.isCustom || false);

</script>

<template>
  <div class="p-8">
    <!-- 页面头部 -->
    <div class="mb-10">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-4 mb-2">
            <Shield class="text-construct-red" :size="32" />
            <h2 :class="['font-display text-4xl tracking-tighter', isDarkMode ? 'text-white' : 'text-gray-900']">
              {{ t('admin.social_login') }}
            </h2>
          </div>
          <p :class="['text-sm font-black tracking-[0.2em] uppercase opacity-50', isDarkMode ? 'text-gray-400' : 'text-gray-500']">
            {{ t('admin.social_login_description') }}
          </p>
        </div>
        <button
          @click="openAddModal"
          class="flex items-center gap-3 px-8 py-4 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl"
        >
          <Plus :size="18" />
          {{ t('admin_add') }}
        </button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-2 mb-8 flex-wrap">
      <button
        v-for="tab in allTabs"
        :key="tab.key"
        @click="activeTab = tab.key"
        :class="[
          'relative flex items-center gap-2.5 px-5 py-2.5 font-bold text-sm rounded-xl transition-all duration-300',
          activeTab === tab.key
            ? isDarkMode
              ? 'bg-construct-red/15 text-construct-red shadow-sm'
              : 'bg-construct-red text-white shadow-md shadow-construct-red/25 scale-105'
            : isDarkMode
              ? 'text-gray-500 hover:text-gray-200 hover:bg-gray-800'
              : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'
        ]"
      >
        <svg v-if="!tab.isCustom" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
          <path :d="providerIcons[tab.key]" />
        </svg>
        <Globe v-else :size="16" />
        {{ tab.label }}
      </button>
    </div>

    <!-- 当前 Provider 配置表单 -->
    <div
      :class="[
        'border rounded-xl overflow-hidden transition-all duration-300',
        isDarkMode
          ? 'bg-gray-500/5 border-gray-500/20'
          : 'bg-gray-50 border-gray-200'
      ]"
    >
      <div class="p-6 space-y-5">
        <!-- 启用开关 + Provider 信息 -->
        <div class="flex items-center justify-between pb-4">
          <div class="flex items-center gap-3">
            <div :class="[
              'w-10 h-10 rounded-xl flex items-center justify-center shadow-md',
              isDarkMode ? 'bg-gray-700' : 'bg-gray-100'
            ]">
              <svg v-if="!isCustomProvider" class="w-5 h-5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" viewBox="0 0 24 24" fill="currentColor">
                <path :d="providerIcons[activeTab]" />
              </svg>
              <Globe v-else class="w-5 h-5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" />
            </div>
            <div>
              <h3 :class="['font-bold text-lg', isDarkMode ? 'text-white' : 'text-gray-900']">
                {{ configMap[activeTab]?.name || activeTab }}
              </h3>
              <span :class="['text-xs font-mono', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                {{ activeTab }}
              </span>
            </div>
          </div>

          <label class="relative inline-flex items-center cursor-pointer">
            <input
              v-model="configMap[activeTab].enabled"
              type="checkbox"
              class="sr-only peer"
            />
            <div :class="[
              'w-11 h-6 rounded-full peer after:content-[\'\'] after:absolute after:top-[2px] after:start-[2px] after:rounded-full after:h-5 after:w-5 after:transition-all',
              isDarkMode
                ? 'bg-gray-600 peer-checked:bg-construct-red after:bg-gray-400 peer-checked:after:bg-white'
                : 'bg-gray-300 peer-checked:bg-construct-red after:bg-white peer-checked:after:bg-white',
              'peer-checked:after:translate-x-full'
            ]" />
          </label>
        </div>

        <!-- Client ID -->
        <div>
          <label :class="['block text-sm font-bold mb-1.5', isDarkMode ? 'text-gray-400' : 'text-gray-600']">
            Client ID
          </label>
          <input
            v-model="configMap[activeTab].client_id"
            type="text"
            :class="[
              'w-full px-4 py-3 border rounded-xl text-sm transition-all',
              isDarkMode
                ? 'bg-gray-500/5 border-gray-500/20 text-white placeholder-gray-500 focus:border-construct-red focus:bg-gray-500/10'
                : 'bg-gray-50 border-gray-200 text-gray-900 placeholder-gray-400 focus:border-construct-red focus:bg-gray-100'
            ]"
            :placeholder="t('admin.enter_client_id')"
          />
        </div>

        <!-- Client Secret -->
        <div>
          <label :class="['block text-sm font-bold mb-1.5', isDarkMode ? 'text-gray-400' : 'text-gray-600']">
            Client Secret
          </label>
          <input
            v-model="configMap[activeTab].client_secret"
            type="password"
            :class="[
              'w-full px-4 py-3 border rounded-xl text-sm transition-all',
              isDarkMode
                ? 'bg-gray-500/5 border-gray-500/20 text-white placeholder-gray-500 focus:border-construct-red focus:bg-gray-500/10'
                : 'bg-gray-50 border-gray-200 text-gray-900 placeholder-gray-400 focus:border-construct-red focus:bg-gray-100'
            ]"
            :placeholder="configMap[activeTab].client_id ? t('admin.reenter_to_change') : t('admin.enter_client_secret')"
          />
          <p :class="['mt-1.5 text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
            {{ t('admin.client_secret_encrypted_notice') }}
          </p>
        </div>

        <!-- Redirect URI -->
        <div>
          <label :class="['block text-sm font-bold mb-1.5', isDarkMode ? 'text-gray-400' : 'text-gray-600']">
            Redirect URI
          </label>
          <div class="flex gap-2">
            <input
              v-model="configMap[activeTab].redirect_uri"
              type="text"
              :class="[
                'flex-1 px-4 py-3 border rounded-xl text-sm transition-all backdrop-blur-md',
                isDarkMode
                  ? 'bg-white/5 border-white/10 text-white placeholder-gray-500 focus:border-construct-red focus:bg-white/10'
                  : 'bg-white/60 border-white/40 text-gray-900 placeholder-gray-400 focus:border-construct-red focus:bg-white/80'
              ]"
            />
            <button
              @click="generateCallbackUrl"
              :class="[
                'px-3 py-2.5 border rounded-xl transition-colors',
                isDarkMode
                  ? 'border-gray-600 text-gray-400 hover:text-white hover:bg-gray-700'
                  : 'border-gray-300 text-gray-500 hover:text-gray-700 hover:bg-gray-100'
              ]"
              :title="t('admin.auto_generate')"
            >
              <RefreshCw :size="16" />
            </button>
          </div>
        </div>

        <!-- 操作按钮 -->
        <div class="flex items-center gap-3 pt-4">
          <button
            @click="saveConfig"
            :disabled="configMap[activeTab].saving"
            class="flex items-center gap-3 px-8 py-3 bg-construct-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-lg shadow-construct-red/20 rounded-xl disabled:opacity-50 disabled:hover:scale-100"
          >
            <template v-if="configMap[activeTab].saving">
              {{ t('common.saving') }}...
            </template>
            <template v-else>
              {{ t('common.save') }}
            </template>
          </button>
          <button
            @click="testConfig"
            :disabled="configMap[activeTab].testing"
            :class="[
              'px-6 py-3 font-bold text-xs uppercase tracking-wider rounded-xl transition-colors border',
              isDarkMode
                ? 'border-gray-600 text-gray-400 hover:bg-gray-700 hover:text-white'
                : 'border-gray-300 text-gray-600 hover:bg-gray-100 hover:text-gray-900'
            ]"
          >
            <template v-if="configMap[activeTab].testing">
              {{ t('common.testing') }}...
            </template>
            <template v-else>
              {{ t('admin.test_connection') }}
            </template>
          </button>
          <button
            v-if="isCustomProvider"
            @click="deleteCustomProvider(activeTab)"
            class="ml-auto px-4 py-3 font-bold text-xs uppercase tracking-wider rounded-xl transition-colors border border-red-500/50 text-red-400 hover:bg-red-500/10 hover:text-red-300"
          >
            <Trash2 :size="14" class="mr-1.5 inline" />
            删除
          </button>
        </div>
      </div>
    </div>

    <!-- 配置指南 -->
    <div
      :class="[
        'mt-8 border rounded-xl p-6',
        isDarkMode
          ? 'bg-blue-500/5 border-blue-500/20'
          : 'bg-blue-50 border-blue-200'
      ]"
    >
      <div class="flex items-center gap-2 mb-3">
        <Info :class="['w-4 h-4', isDarkMode ? 'text-blue-400' : 'text-blue-600']" />
        <h3 :class="['text-sm font-bold', isDarkMode ? 'text-blue-300' : 'text-blue-700']">
          {{ t('admin.social_login_guide_title') }}
        </h3>
      </div>
      <ul class="space-y-2 text-sm">
        <li class="flex items-start gap-2">
          <span :class="['font-bold mt-0.5', isDarkMode ? 'text-blue-400' : 'text-blue-600']">1.</span>
          <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">{{ t('admin.social_login_guide_step1') }}</span>
        </li>
        <li class="flex items-start gap-2">
          <span :class="['font-bold mt-0.5', isDarkMode ? 'text-blue-400' : 'text-blue-600']">2.</span>
          <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">{{ t('admin.social_login_guide_step2') }}</span>
        </li>
        <li class="flex items-start gap-2">
          <span :class="['font-bold mt-0.5', isDarkMode ? 'text-blue-400' : 'text-blue-600']">3.</span>
          <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">{{ t('admin.social_login_guide_step3') }}</span>
        </li>
        <li class="flex items-start gap-2">
          <span :class="['font-bold mt-0.5', isDarkMode ? 'text-blue-400' : 'text-blue-600']">4.</span>
          <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">{{ t('admin.social_login_guide_step4') }}</span>
        </li>
      </ul>
    </div>

    <!-- 添加第三方 Provider 弹窗 -->
    <Transition name="modal">
      <div v-if="showAddModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" @click.self="closeAddModal">
        <div :class="['w-full max-w-md mx-4 rounded-2xl shadow-2xl ring-1', isDarkMode ? 'bg-gray-800 ring-gray-700' : 'bg-white ring-gray-200/80']">
          <div :class="['flex justify-between items-center p-6 pb-2', isDarkMode ? 'text-white' : 'text-gray-900']">
            <h3 :class="['text-xl font-bold', isDarkMode ? 'text-white' : 'text-gray-900']">
              添加第三方登录
            </h3>
            <button @click="closeAddModal" :class="['p-2 rounded-xl transition-colors', isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
              <X :size="20" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" />
            </button>
          </div>

          <div class="p-6 space-y-4">
            <div>
              <label :class="['block text-sm font-bold mb-1.5', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
                Provider 名称
              </label>
              <input
                v-model="newProviderName"
                type="text"
                :class="['w-full px-3 py-2.5 border rounded-xl text-sm backdrop-blur-sm', isDarkMode ? 'bg-gray-800/50 border-gray-600/30 text-white placeholder-gray-500 focus:border-construct-red' : 'bg-white/50 border-white/30 text-gray-900 placeholder-gray-400 focus:border-construct-red']"
                placeholder="例如：微信、Twitter、LinkedIn..."
                @keyup.enter="addCustomProvider"
              />
              <p :class="['mt-1.5 text-xs', isDarkMode ? 'text-gray-500' : 'text-gray-400']">
                输入平台名称后点击添加，即可为其配置 OAuth 密钥
              </p>
            </div>
          </div>

          <div :class="['flex gap-3 p-6 pt-2', isDarkMode ? 'text-gray-300' : 'text-gray-700']">
            <button
              @click="closeAddModal"
              :class="['flex-1 px-4 py-3 font-bold text-sm border rounded-xl transition-colors', isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 hover:bg-gray-100']"
            >
              {{ t('admin_cancel') }}
            </button>
            <button
              @click="addCustomProvider"
              class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-construct-red text-white font-bold text-sm rounded-xl hover:bg-red-700 transition-colors"
            >
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
  transition: opacity 0.3s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-active > div,
.modal-leave-active > div {
  transition: transform 0.3s ease, opacity 0.3s ease;
}
.modal-enter-from > div,
.modal-leave-to > div {
  transform: scale(0.95);
  opacity: 0;
}
</style>
