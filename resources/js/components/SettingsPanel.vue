<script setup>
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useTheme } from '../composables/useTheme'

defineProps({
  isMobile: {
    type: Boolean,
    default: false
  }
})

const { t, locale } = useI18n()
const { themes, currentTheme, setTheme } = useTheme()

const languages = [
  { code: 'en', label: 'EN' },
  { code: 'zh', label: '中文(简)' },
  { code: 'zh-TW', label: '中文(繁)' },
]

const currentLang = ref(locale.value)

watch(locale, (newVal) => {
  currentLang.value = newVal
})

const setLanguage = (code) => {
  console.log('Setting language to:', code)
  locale.value = code
  currentLang.value = code
  localStorage.setItem('lang', code)
  console.log('Current locale after:', locale.value)
}

const activeThemeLabel = computed(() => {
  const found = themes.value.find(theme => theme.name === currentTheme.value.name)
  return found ? `${found.name} // ${found.label}` : ''
})
</script>

<template>
  <div class="flex flex-col gap-6 w-full">
    <!-- Version Title -->
    <div class="text-right">
      <p class="text-accent font-display text-xs tracking-[0.3em] uppercase">
        ARCHYX v0.1
      </p>
    </div>

    <!-- Language Selection -->
    <div>
      <label class="text-white/40 text-[10px] uppercase tracking-[0.2em] font-bold mb-3 block text-right">
        {{ t('interface_lang') }}
      </label>
      <div class="flex gap-2">
        <button
          v-for="lang in languages"
          :key="lang.code"
          @click="setLanguage(lang.code)"
          class="flex-1 py-2 text-[10px] font-bold tracking-widest border transition-colors"
          :class="currentLang === lang.code ? 'bg-white text-black border-white' : 'text-white border-white/20 hover:bg-white/10'"
        >
          {{ lang.label }}
        </button>
      </div>
    </div>

    <!-- Theme Selection -->
    <div>
      <label class="text-white/40 text-[10px] uppercase tracking-[0.2em] font-bold mb-3 block text-right">
        {{ t('accent_theme') }}
      </label>
      <div class="flex gap-2 justify-end">
        <button
          v-for="theme in themes"
          :key="theme.name"
          @click="setTheme(theme)"
          class="w-10 h-10 rounded-full relative transition-all hover:scale-110 focus:outline-none focus:ring-2 focus:ring-white/50"
          :style="{ backgroundColor: theme.color }"
          :title="`${theme.name} // ${theme.label}`"
        >
          <div
            v-if="currentTheme.name === theme.name"
            class="absolute inset-0 flex items-center justify-center"
          >
            <div class="w-2 h-2 bg-white rounded-full shadow-sm"></div>
          </div>
        </button>
      </div>
      <div class="mt-2 text-[10px] uppercase tracking-[0.2em] font-bold text-white/50 text-right">
        {{ activeThemeLabel }}
      </div>
    </div>

    <!-- Copyright -->
    <div class="w-12 h-0.5 bg-accent mx-auto mt-4"></div>
    <p class="text-[10px] uppercase tracking-[0.2em] font-bold text-white/40 text-right">
      © 2026 BLDG_SYSTEM
    </p>
  </div>
</template>
