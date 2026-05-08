<script setup>
import { computed } from 'vue'
import { ArrowLeft, AlertTriangle } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  errorCode: {
    type: Number,
    default: 404
  }
})

const { t } = useI18n()

const errorInfo = computed(() => {
  const codes = {
    404: {
      title: t('data_corruption'),
      subtitle: t('not_found_subtitle'),
      desc: t('not_found_desc'),
      code: 'E_STRUC_NODE_NOT_FOUND',
      integrity: 'COMPROMISED',
      sector: 'UNKNOWN'
    },
    403: {
      title: t('access_denied'),
      subtitle: t('forbidden_subtitle'),
      desc: t('forbidden_desc'),
      code: 'E_AUTH_ACCESS_DENIED',
      integrity: 'INTACT',
      sector: 'AUTH'
    },
    500: {
      title: t('system_error'),
      subtitle: t('server_error_subtitle'),
      desc: t('server_error_desc'),
      code: 'E_SYS_INTERNAL_ERROR',
      integrity: 'DEGRADED',
      sector: 'CORE'
    }
  }
  return codes[props.errorCode] || codes[404]
})

const currentTimestamp = computed(() => {
  return new Date().toISOString()
})
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-construct-paper p-4">
    <!-- Background Decorative Elements -->
    <div class="fixed inset-0 z-0 pointer-events-none opacity-[0.03]">
      <div class="absolute top-0 left-0 w-full h-[15vh] bg-construct-black" />
      <div class="absolute top-[15vh] left-[25%] w-[1px] h-[85vh] bg-construct-black" />
      <div class="absolute top-[15vh] left-[50%] w-[1px] h-[85vh] bg-construct-black" />
      <div class="absolute top-[15vh] left-[75%] w-[1px] h-[85vh] bg-construct-black" />
    </div>

    <div class="relative z-10 w-full max-w-5xl">
      <!-- Large Geometric Error Code -->
      <div class="relative mb-4">
        <div class="font-display text-[20vw] md:text-[16vw] lg:text-[14vw] leading-none tracking-tighter text-construct-black text-center select-none">
          {{ errorCode }}
        </div>
        <div class="absolute top-1/2 left-0 w-full h-3 bg-accent -translate-y-1/2 mix-blend-multiply" />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
        <!-- Left Side - Error Info -->
        <div class="md:col-span-7">
          <div class="flex items-center gap-3 mb-3">
            <AlertTriangle class="text-accent h-6 w-6 md:h-8 md:w-8 shrink-0 animate-pulse" />
            <h1 class="font-display text-2xl md:text-3xl lg:text-4xl tracking-tighter leading-none uppercase">
              {{ errorInfo.title }}
            </h1>
          </div>

          <h2 class="text-sm md:text-base lg:text-lg font-bold tracking-widest text-construct-black mb-3 uppercase">
            {{ errorInfo.subtitle }} // {{ errorCode }}
          </h2>

          <p class="text-xs md:text-sm font-medium opacity-60 max-w-lg leading-relaxed mb-6 uppercase tracking-wide">
            {{ errorInfo.desc }}
          </p>

          <a
            href="/"
            class="group inline-flex items-center gap-4 bg-construct-black text-white hover:bg-accent py-3 px-6 md:py-4 md:px-8 font-bold text-xs md:text-sm tracking-[0.15em] uppercase transition-all border-4 border-construct-black active:translate-x-0.5 active:translate-y-0.5"
          >
            <ArrowLeft class="w-4 h-4 transition-transform group-hover:-translate-x-1" />
            {{ t('return_home') }}
          </a>
        </div>

        <!-- Right Side - Diagnostics -->
        <div class="md:col-span-5 border-t-2 md:border-t-0 md:border-l-2 border-construct-black pt-4 md:pt-0 md:pl-6">
          <div class="text-[10px] font-black tracking-[0.3em] opacity-30 uppercase mb-3">System Diagnostics</div>
          <div class="space-y-2 font-mono text-[10px] md:text-xs">
            <div class="flex justify-between gap-4">
              <span class="opacity-40 uppercase">Error Code:</span>
              <span class="font-bold">{{ errorInfo.code }}</span>
            </div>
            <div class="flex justify-between gap-4">
              <span class="opacity-40 uppercase">Path Integrity:</span>
              <span class="font-bold text-accent">{{ errorInfo.integrity }}</span>
            </div>
            <div class="flex justify-between gap-4">
              <span class="opacity-40 uppercase">Sector:</span>
              <span class="font-bold underline">{{ errorInfo.sector }}</span>
            </div>
            <div class="flex justify-between gap-4">
              <span class="opacity-40 uppercase">Timestamp:</span>
              <span class="font-bold text-[10px]">{{ currentTimestamp }}</span>
            </div>
          </div>

          <!-- Progress Bar Decoration -->
          <div class="mt-4 space-y-1">
            <div class="h-1.5 w-full bg-construct-black opacity-10" />
            <div class="h-3 w-full bg-construct-black opacity-20" />
            <div class="h-5 w-full bg-construct-black opacity-40" />
            <div class="h-8 w-full bg-accent" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
