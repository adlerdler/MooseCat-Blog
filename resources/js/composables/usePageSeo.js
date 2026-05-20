/**
 * usePageSeo.js - 页面 SEO 管理 Composable
 * 
 * 功能说明：
 * - 统一管理页面 SEO 元数据
 * - 支持动态更新 title、description、keywords
 * - 支持 Open Graph 和 Twitter Card
 * - 支持 canonical URL
 * - 支持结构化数据（JSON-LD）
 * 
 * 使用示例：
 * usePageSeo({
 *   title: '页面标题',
 *   description: '页面描述',
 *   keywords: '关键词1, 关键词2',
 *   image: '封面图URL',
 *   url: '页面URL',
 *   type: 'Article'
 * })
 */
import { watch, isRef } from 'vue'

export function usePageSeo(seoConfig) {
  const updateMeta = (name, content, attribute = 'name') => {
    if (!content) return
    
    let meta = document.querySelector(`meta[${attribute}="${name}"]`)
    if (!meta) {
      meta = document.createElement('meta')
      meta.setAttribute(attribute, name)
      document.head.appendChild(meta)
    }
    meta.content = content
  }

  const updateCanonical = (url) => {
    if (!url) return
    
    let canonical = document.querySelector('link[rel="canonical"]')
    if (!canonical) {
      canonical = document.createElement('link')
      canonical.setAttribute('rel', 'canonical')
      document.head.appendChild(canonical)
    }
    canonical.href = url
  }

  const updateStructuredData = (type, title, description, url, author) => {
    if (!type) return
    
    let script = document.querySelector('script[type="application/ld+json"]')
    if (!script) {
      script = document.createElement('script')
      script.setAttribute('type', 'application/ld+json')
      document.head.appendChild(script)
    }
    
    const data = {
      "@context": "https://schema.org",
      "@type": type,
      "name": title,
      "description": description,
      "url": url
    }
    
    if (author) {
      data.author = {
        "@type": "Person",
        "name": author
      }
    }
    
    script.textContent = JSON.stringify(data)
  }

  const applySeo = () => {
    const title = isRef(seoConfig.title) ? seoConfig.title.value : seoConfig.title
    const description = isRef(seoConfig.description) ? seoConfig.description.value : seoConfig.description
    const keywords = isRef(seoConfig.keywords) ? seoConfig.keywords.value : seoConfig.keywords
    const image = isRef(seoConfig.image) ? seoConfig.image.value : seoConfig.image
    const url = isRef(seoConfig.url) ? seoConfig.url.value : seoConfig.url
    const type = isRef(seoConfig.type) ? seoConfig.type.value : seoConfig.type
    const author = isRef(seoConfig.author) ? seoConfig.author.value : seoConfig.author

    if (title) {
      document.title = title
    }
    
    updateMeta('description', description)
    updateMeta('keywords', keywords)
    updateMeta('og:title', title, 'property')
    updateMeta('og:description', description, 'property')
    
    if (image) {
      updateMeta('og:image', image, 'property')
    }
    
    if (url) {
      updateMeta('og:url', url, 'property')
      updateCanonical(url)
    }
    
    if (type) {
      updateMeta('og:type', type, 'property')
      updateStructuredData(type, title, description, url, author)
    }
  }

  const hasRefs = Object.values(seoConfig).some(isRef)
  
  if (hasRefs) {
    const watchers = Object.entries(seoConfig)
      .filter(([_, value]) => isRef(value))
      .map(([key, value]) => watch(value, () => applySeo()))
    
    applySeo()
  } else {
    applySeo()
  }

  return { updateMeta, updateCanonical }
}
