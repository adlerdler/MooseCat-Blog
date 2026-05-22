<script setup>
/**
 * MarkdownRenderer.vue - Markdown 渲染组件
 * 
 * 功能说明：
 * - 将 Markdown 格式文本渲染为 HTML
 * - 使用 Prism.js 原生功能实现代码语法高亮
 * - 支持代码行数显示、复制功能、语言标识
 * 
 * 技术实现：
 * - 使用 marked 库解析 Markdown
 * - 使用 Prism 原生高亮方法 Prism.highlightElement()
 * - 集成 Prism 行号插件和复制插件
 * - 自定义 Vue SFC 语法支持
 * 
 * 使用示例：
 * <MarkdownRenderer :content="post.content" />
 */
import { ref, computed, onMounted, nextTick } from 'vue';
import { marked } from 'marked';
import Prism from 'prismjs';
import 'prismjs/plugins/toolbar/prism-toolbar.js';
import 'prismjs/plugins/show-language/prism-show-language.js';
import 'prismjs/plugins/copy-to-clipboard/prism-copy-to-clipboard.js';
import 'prismjs/plugins/line-numbers/prism-line-numbers.js';
import 'prismjs/plugins/toolbar/prism-toolbar.css';
import 'prismjs/plugins/line-numbers/prism-line-numbers.css';

if (Prism.languages.markup) {
  Prism.languages.vue = Prism.languages.extend('markup', {});
  
  if (Prism.languages.javascript && Prism.languages.css) {
    Prism.languages.insertBefore('vue', 'cdata', {
      'script-block': {
        pattern: /(<script[\s\S]*?>)[\s\S]*?(<\/script>)/gi,
        lookbehind: true,
        inside: Prism.languages.javascript,
        greedy: true
      },
      'style-block': {
        pattern: /(<style[\s\S]*?>)[\s\S]*?(<\/style>)/gi,
        lookbehind: true,
        inside: Prism.languages.css,
        greedy: true
      }
    });
  }
}

const props = defineProps({
  content: {
    type: String,
    required: true
  },
  addHeadingIds: {
    type: Boolean,
    default: true
  },
  inContentAd: {
    type: Object,
    default: null
  }
});

const containerRef = ref(null);

marked.use({
  renderer: {
    code(token) {
      const code = token.text || '';
      const lang = token.lang || 'text';
      const escapedCode = Prism.util.encode(code);
      
      return `
        <div class="code-block-wrapper">
          <pre class="language-${lang} line-numbers" data-src="" data-toolbar><code class="language-${lang}">${escapedCode}</code></pre>
        </div>
      `;
    }
  }
});

const renderedContent = computed(() => {
  if (!props.content) return '';

  let html = marked.parse(props.content);

  if (props.addHeadingIds) {
    html = html.replace(/<h([1-6])>([^<]*)<\/h\1>/g, (match, level, text) => {
      const id = text.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-');
      return `<h${level} id="${id}">${text}</h${level}>`;
    });
  }

  // 在文章中间插入广告
  if (props.inContentAd) {
    const paragraphs = html.split('</p>');
    const middleIndex = Math.floor(paragraphs.length / 2);
    
    if (paragraphs.length > 2) {
      const adHtml = `
        </p>
        <div class="in-content-ad-wrapper">
          <div class="in-content-ad">
            <span class="ad-label">SPONSORED</span>
            <a href="${props.inContentAd.link_url}" target="_blank" rel="noopener noreferrer" class="ad-content">
              <img src="${props.inContentAd.image_url}" alt="${props.inContentAd.title}" class="ad-image">
              <h4 class="ad-title">${props.inContentAd.title}</h4>
              <span class="ad-cta">Learn More →</span>
            </a>
          </div>
        </div>
      `;
      
      paragraphs.splice(middleIndex, 0, adHtml);
      html = paragraphs.join('</p>');
    }
  }

  return html;
});

onMounted(async () => {
  await nextTick();
  if (containerRef.value) {
    const codeBlocks = containerRef.value.querySelectorAll('pre code');
    codeBlocks.forEach((codeBlock) => {
      try {
        Prism.highlightElement(codeBlock);
      } catch (e) {
        console.warn('Prism highlight error:', e.message);
      }
    });
  }
});
</script>

<template>
  <div ref="containerRef" class="markdown-body" v-html="renderedContent"></div>
</template>

<style lang="scss" scoped>
.markdown-body {
  :deep(.in-content-ad-wrapper) {
    margin: 2.5rem 0;
    padding: 1.5rem;
    background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
    border: 3px solid #000;
    position: relative;
    overflow: hidden;
  }
  
  :deep(.in-content-ad) {
    position: relative;
    z-index: 1;
  }
  
  :deep(.ad-label) {
    display: inline-block;
    font-size: 0.625rem;
    font-weight: 900;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: #ef4444;
    margin-bottom: 0.75rem;
  }
  
  :deep(.ad-content) {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    text-decoration: none;
    color: inherit;
    transition: transform 0.3s ease;
  }
  
  :deep(.ad-content:hover) {
    transform: translateX(0.5rem);
  }
  
  :deep(.ad-image) {
    width: 100%;
    height: auto;
    object-fit: cover;
  }
  
  :deep(.ad-title) {
    font-family: 'Space Grotesk', system-ui, sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    margin: 0;
  }
  
  :deep(.ad-cta) {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #fff;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
  }

  :deep(h1), :deep(h2), :deep(h3), :deep(h4), :deep(h5), :deep(h6) {
    font-family: 'Space Grotesk', system-ui, sans-serif;
    font-weight: 900;
    letter-spacing: -0.025em;
    margin-top: 2rem;
    margin-bottom: 1rem;
    text-transform: uppercase;
  }

  :deep(h1) { font-size: 2.5rem; }
  :deep(h2) { font-size: 2rem; }
  :deep(h3) { font-size: 1.5rem; }
  :deep(h4) { font-size: 1.25rem; }

  :deep(p) {
    margin-bottom: 1.5rem;
    line-height: 1.8;
  }

  :deep(ul), :deep(ol) {
    margin-bottom: 1.5rem;
    padding-left: 1.5rem;
  }

  :deep(li) {
    margin-bottom: 0.5rem;
    line-height: 1.8;
  }

  :deep(strong) {
    font-weight: 900;
  }

  :deep(a) {
    color: inherit;
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 3px;
  }

  :deep(a:hover) {
    color: #ef4444;
  }

  :deep(blockquote) {
    border-left: 4px solid #000;
    padding-left: 1.5rem;
    font-style: italic;
    margin: 1.5rem 0;
  }

  :deep(:not(pre) > code) {
    font-family: 'JetBrains Mono', 'Fira Code', 'SF Mono', Consolas, monospace;
    background: #2d2d2d;
    color: #f8f8f2;
    padding: 0.2rem 0.4rem;
    font-size: 0.875em;
    border-radius: 4px;
  }

  :deep(.code-block-wrapper) {
    margin: 1.5rem 0;
    border-radius: 0.5rem;
    overflow: hidden;
    background: #272822;
  }

  :deep(pre) {
    margin: 0;
    padding: 1rem;
    overflow-x: auto;
  }

  :deep(pre code) {
    font-family: 'JetBrains Mono', 'Fira Code', 'SF Mono', Consolas, monospace;
    font-size: 1rem;
    line-height: 1.6;
    color: #f8f8f2;
    background: transparent !important;
    padding: 0 !important;
  }

  :deep(.line-numbers) {
    counter-reset: line;
  }

  :deep(.line-numbers-rows) {
    border-right: 1px solid #3d3d3d;
    margin-right: 0.75rem;
    padding-right: 0.75rem;
    user-select: none;
    opacity: 0.5;
  }

  :deep(.line-numbers-row) {
    counter-increment: line;
    
    &::before {
      content: counter(line);
      display: inline-block;
      width: 1.5rem;
      text-align: right;
      margin-right: 0.75rem;
      color: #858585;
      font-size: 0.75rem;
    }
  }
}
</style>
