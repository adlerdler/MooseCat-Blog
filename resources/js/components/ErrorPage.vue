<script setup>
/**
 * ErrorPage.vue - 错误页面组件
 * 
 * 功能说明：
 * - 展示404、403等错误页面
 * - 根据访问路径智能推断错误类型
 * - 提供返回首页的导航
 * 
 * 错误类型映射：
 * - /post/* - 文章未找到
 * - /video/* - 视频未找到
 * - /project/* - 项目未找到
 * - /admin/* - 访问被拒绝（403）
 * - 其他 - 页面未找到（404）
 */
import { computed } from 'vue'
import { ArrowLeft, AlertTriangle } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  errorCode: {
    type: Number,
    default: 404
  },
  errorPath: {
    type: String,
    default: ''
  }
})

const { t } = useI18n()

const pathSegments = computed(() => {
  const path = props.errorPath || window.location.pathname
  return path.split('/').filter(segment => segment !== '')
})

const detectedError = computed(() => {
  const segments = pathSegments.value
  const errorCode = props.errorCode

  if (segments.length === 0) {
    return { type: 'unknown', title: 'Page Not Found', hint: '' }
  }

  const firstSegment = segments[0].toLowerCase()

  const pathErrorMap = {
    'post': { type: 'post', title: t('article_not_found'), hint: t('article_not_found_desc') },
    'posts': { type: 'post', title: t('article_not_found'), hint: t('article_not_found_desc') },
    'video': { type: 'video', title: t('video_not_found'), hint: t('video_not_found_desc') },
    'videos': { type: 'video', title: t('video_not_found'), hint: t('video_not_found_desc') },
    'project': { type: 'project', title: t('project_not_found'), hint: t('project_not_found_desc') },
    'projects': { type: 'project', title: t('project_not_found'), hint: t('project_not_found_desc') },
    'resource': { type: 'resource', title: t('resource_not_found'), hint: t('resource_not_found_desc') },
    'resources': { type: 'resource', title: t('resource_not_found'), hint: t('resource_not_found_desc') },
    'author': { type: 'author', title: t('author_not_found'), hint: t('author_not_found_desc') },
    'category': { type: 'category', title: t('category_not_found'), hint: t('category_not_found_desc') },
    'tag': { type: 'tag', title: t('tag_not_found'), hint: t('tag_not_found_desc') },
    'admin': { type: 'admin', title: t('access_denied'), hint: t('admin_access_denied_desc'), code: 403 },
    'api': { type: 'api', title: t('api_not_found'), hint: t('api_not_found_desc') }
  }

  if (pathErrorMap[firstSegment]) {
    const result = pathErrorMap[firstSegment]
    return {
      ...result,
      errorCode: result.code || errorCode,
      path: props.errorPath || window.location.pathname
    }
  }

  return {
    type: 'page',
    title: 'Page Not Found',
    hint: '',
    errorCode,
    path: props.errorPath || window.location.pathname
  }
})

const errorInfo = computed(() => {
  const code = detectedError.value.errorCode || props.errorCode
  const detected = detectedError.value

  const codes = {
    404: {
      title: detected.title || t('data_corruption'),
      subtitle: detected.hint ? detected.hint : t('not_found_subtitle'),
      desc: detected.hint ? '' : t('not_found_desc'),
      code: detected.type ? `E_${detected.type.toUpperCase()}_NOT_FOUND` : 'E_STRUC_NODE_NOT_FOUND',
      integrity: 'COMPROMISED',
      sector: detected.type.toUpperCase() || 'UNKNOWN'
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
  return codes[code] || codes[404]
})

const currentTimestamp = computed(() => {
  return new Date().toISOString()
})

const goBack = () => {
  if (window.history.length > 1) {
    window.history.back()
  } else {
    window.location.href = '/'
  }
}
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
          {{ errorInfo.errorCode || errorCode }}
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
            {{ errorInfo.subtitle }} // {{ errorInfo.errorCode || errorCode }}
          </h2>

          <p v-if="errorInfo.desc" class="text-xs md:text-sm font-medium opacity-60 max-w-lg leading-relaxed mb-6 uppercase tracking-wide">
            {{ errorInfo.desc }}
          </p>

          <button
            @click="goBack"
            class="group inline-flex items-center gap-4 bg-construct-black text-white hover:bg-accent py-3 px-6 md:py-4 md:px-8 font-bold text-xs md:text-sm tracking-[0.15em] uppercase transition-all border-4 border-construct-black active:translate-x-0.5 active:translate-y-0.5"
          >
            <ArrowLeft class="w-4 h-4 transition-transform group-hover:-translate-x-1" />
            {{ t('go_back') || 'Go Back' }}
          </button>
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
              <span class="opacity-40 uppercase">Resource Type:</span>
              <span class="font-bold capitalize">{{ detectedError.type }}</span>
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