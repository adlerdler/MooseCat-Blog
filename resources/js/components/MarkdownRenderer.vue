<script setup>
/**
 * MarkdownRenderer.vue - Markdown 渲染组件
 * 
 * 功能说明：
 * - 将 Markdown 格式文本渲染为 HTML
 * - 集成 Prism.js 实现代码语法高亮
 * - 支持 Vue 代码块的语法识别
 * 
 * 技术实现：
 * - 使用 marked 库解析 Markdown
 * - 自定义 Prism 语言扩展支持 Vue SFC
 * - 处理代码块的语言标识
 * 
 * 使用示例：
 * <MarkdownRenderer :content="post.content" />
 */
import { computed } from 'vue';
import { marked } from 'marked';
import Prism from 'prismjs';

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
  }
});

const escapeHtml = (text) => {
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
};

marked.use({
  renderer: {
    code(token) {
      const code = token.text || '';
      const lang = token.lang || 'text';

      let highlightedCode;

      if (lang && Prism.languages[lang]) {
        try {
          highlightedCode = Prism.highlight(code, Prism.languages[lang], lang);
        } catch (e) {
          console.warn('Highlight error for', lang, e.message);
          highlightedCode = escapeHtml(code);
        }
      } else {
        highlightedCode = escapeHtml(code);
      }

      return `<pre class="language-${lang}"><code class="language-${lang}">${highlightedCode}</code></pre>`;
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

  return html;
});
</script>

<template>
  <div class="markdown-body" v-html="renderedContent"></div>
</template>

<style lang="scss" scoped>
.markdown-body {
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

  :deep(pre) {
    margin: 1.5rem 0;
    border-radius: 0.5rem;
    overflow-x: auto;
  }

  :deep(pre code) {
    font-family: 'JetBrains Mono', 'Fira Code', 'SF Mono', Consolas, monospace;
    font-size: 0.9rem;
    line-height: 1.6;
  }
}
</style>
