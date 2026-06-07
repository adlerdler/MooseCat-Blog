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
 *   image: 'https://example.com/og-image.jpg',
 *   url: 'https://example.com/page',
 *   type: 'Article',
 *   author: '作者名',
 *   publishedTime: '2026-01-01T00:00:00.000Z',
 * })
 * 
 * 在模板中使用: <SeoHead />
 */
import { computed, isRef, h } from 'vue'
import { Head } from '@inertiajs/vue3'

const SITE_NAME = 'ARKHYX'

export function usePageSeo(seoConfig) {
  const getValue = (key) => {
    if (!seoConfig[key]) return ''
    return isRef(seoConfig[key]) ? seoConfig[key].value : seoConfig[key]
  }

  /**
   * 生成 JSON-LD 结构化数据
   * - Article 类型：Schema.org Article 标记，用于 Google 富文本摘要
   * - 其它类型暂不生成
   */
  const jsonLdContent = computed(() => {
    const type = getValue('type')
    if (!['Article', 'Person'].includes(type)) return null

    const title = getValue('title')
    const description = getValue('description')
    const image = getValue('image')
    const url = getValue('url')
    const author = getValue('author')
    const publishedTime = getValue('publishedTime')

    if (type === 'Article' && title) {
      const article = {
        '@context': 'https://schema.org',
        '@type': 'Article',
        headline: title,
      }
      if (description) article.description = description
      if (image) article.image = image
      if (url) article.url = url
      if (publishedTime) article.datePublished = publishedTime
      if (author) {
        article.author = { '@type': 'Person', name: author }
      }
      return JSON.stringify(article)
    }

    if (type === 'Person') {
      const person = {
        '@context': 'https://schema.org',
        '@type': 'Person',
        name: getValue('personName') || title,
      }
      if (getValue('jobTitle')) person.jobTitle = getValue('jobTitle')
      if (description) person.description = description
      if (image) person.image = image
      if (url) person.url = url
      if (getValue('email')) person.email = getValue('email')
      if (getValue('sameAs')?.length) person.sameAs = getValue('sameAs')
      if (getValue('knowsAbout')?.length) person.knowsAbout = getValue('knowsAbout')
      return JSON.stringify(person)
    }

    return null
  })

  const SeoHead = () => h(Head, {
    title: getValue('title'),
  }, () => {
    const tags = []

    // --- 基础 meta ---
    const description = getValue('description')
    const keywords = getValue('keywords')
    const title = getValue('title')
    const url = getValue('url')

    if (description) tags.push(h('meta', { name: 'description', content: description }))
    if (keywords) tags.push(h('meta', { name: 'keywords', content: keywords }))

    // --- Open Graph ---
    tags.push(h('meta', { property: 'og:site_name', content: SITE_NAME }))
    if (title) tags.push(h('meta', { property: 'og:title', content: title }))
    if (description) tags.push(h('meta', { property: 'og:description', content: description }))
    const image = getValue('image')
    if (image) tags.push(h('meta', { property: 'og:image', content: image }))
    if (url) tags.push(h('meta', { property: 'og:url', content: url }))
    const type = getValue('type')
    if (type) tags.push(h('meta', { property: 'og:type', content: type.toLowerCase() }))

    // --- Canonical URL ---
    if (url) tags.push(h('link', { rel: 'canonical', href: url }))

    // --- Twitter Card ---
    tags.push(h('meta', { name: 'twitter:card', content: image ? 'summary_large_image' : 'summary' }))
    if (title) tags.push(h('meta', { name: 'twitter:title', content: title }))
    if (description) tags.push(h('meta', { name: 'twitter:description', content: description }))
    if (image) tags.push(h('meta', { name: 'twitter:image', content: image }))

    // --- Article 专属标签 ---
    if (type === 'Article') {
      const publishedTime = getValue('publishedTime')
      const author = getValue('author')
      if (publishedTime) tags.push(h('meta', { property: 'article:published_time', content: publishedTime }))
      if (author) tags.push(h('meta', { property: 'article:author', content: author }))
    }

    // --- JSON-LD 结构化数据 ---
    if (jsonLdContent.value) {
      tags.push(h('script', { type: 'application/ld+json', innerHTML: jsonLdContent.value }))
    }

    return tags
  })

  return { SeoHead }
}
