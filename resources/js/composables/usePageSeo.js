/**
 * usePageSeo.js - 页面 SEO 管理 Composable
 * 
 * 功能说明：
 * - 统一管理页面 SEO 元数据
 * - 使用 Inertia Head 组件管理浏览器标签页标题
 * - 支持动态更新 title、description、keywords
 * - 支持 Open Graph 和 Twitter Card
 * - 支持 canonical URL
 * - 支持结构化数据（JSON-LD）
 * 
 * 使用示例：
 * const { SeoHead } = usePageSeo({
 *   title: '页面标题',
 *   description: '页面描述',
 *   keywords: '关键词1, 关键词2',
 * })
 * 
 * 在模板中使用: <SeoHead />
 */
import { computed, isRef, h } from 'vue'
import { Head } from '@inertiajs/vue3'

export function usePageSeo(seoConfig) {
  const getValue = (key) => {
    if (!seoConfig[key]) return ''
    return isRef(seoConfig[key]) ? seoConfig[key].value : seoConfig[key]
  }

  const SeoHead = () => h(Head, {
    title: getValue('title'),
  }, () => [
    getValue('description') ? h('meta', { name: 'description', content: getValue('description') }) : null,
    getValue('keywords') ? h('meta', { name: 'keywords', content: getValue('keywords') }) : null,
    getValue('title') ? h('meta', { property: 'og:title', content: getValue('title') }) : null,
    getValue('description') ? h('meta', { property: 'og:description', content: getValue('description') }) : null,
    getValue('image') ? h('meta', { property: 'og:image', content: getValue('image') }) : null,
    getValue('url') ? h('meta', { property: 'og:url', content: getValue('url') }) : null,
    getValue('url') ? h('link', { rel: 'canonical', href: getValue('url') }) : null,
    getValue('type') ? h('meta', { property: 'og:type', content: getValue('type') }) : null,
  ].filter(Boolean))

  return { SeoHead }
}
