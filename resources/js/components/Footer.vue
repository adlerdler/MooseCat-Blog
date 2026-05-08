<script setup>
import { Twitter, Github, Linkedin, Instagram } from 'lucide-vue-next'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:modelValue'])

const toggleFooter = () => {
  emit('update:modelValue', !props.modelValue)
}
</script>

<template>
  <footer
    v-if="props.modelValue"
    class="p-8 md:p-16 bg-construct-paper border-t-8 border-black grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-12 sm:gap-8 md:gap-12"
  >
    <div class="sm:col-span-2">
      <a
        href="/"
        class="font-display text-5xl md:text-6xl tracking-tighter mb-4 block hover:text-accent transition-colors"
      >
        ARCHYX
      </a>
      <p class="max-w-xs text-sm uppercase font-bold tracking-tight opacity-60 min-h-[3rem]">
        探索建筑与技术的边界，构建未来的数字空间体验
      </p>
      <div class="flex gap-4 mt-8">
        <a
          href="#"
          class="p-2 border-2 border-black hover:bg-black hover:text-white transition-colors w-10 h-10 flex items-center justify-center"
          aria-label="Twitter"
        >
          <Twitter class="w-4 h-4" />
        </a>
        <a
          href="#"
          class="p-2 border-2 border-black hover:bg-black hover:text-white transition-colors w-10 h-10 flex items-center justify-center"
          aria-label="GitHub"
        >
          <Github class="w-4 h-4" />
        </a>
        <a
          href="#"
          class="p-2 border-2 border-black hover:bg-black hover:text-white transition-colors w-10 h-10 flex items-center justify-center"
          aria-label="LinkedIn"
        >
          <Linkedin class="w-4 h-4" />
        </a>
        <a
          href="#"
          class="p-2 border-2 border-black hover:bg-black hover:text-white transition-colors w-10 h-10 flex items-center justify-center"
          aria-label="Instagram"
        >
          <Instagram class="w-4 h-4" />
        </a>
      </div>
    </div>
    <div>
      <h4 class="font-display mb-6 tracking-widest text-sm uppercase bg-black text-white inline-block px-3 py-1">
        分类
      </h4>
      <ul class="text-xs space-y-3 font-bold tracking-widest uppercase">
        <li>
          <a href="/" class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
            / 理论
          </a>
        </li>
        <li>
          <a href="/" class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
            / 设计
          </a>
        </li>
        <li>
          <a href="/blog" class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
            / 博客
          </a>
        </li>
        <li>
          <a href="/author" class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
            / 关于
          </a>
        </li>
      </ul>
    </div>
    <div>
      <h4 class="font-display mb-6 tracking-widest text-sm uppercase bg-black text-white inline-block px-3 py-1">
        数据
      </h4>
      <ul class="text-xs space-y-3 font-bold tracking-widest uppercase">
        <li class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
          RSS FEED
        </li>
        <li class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
          API ACCESS
        </li>
        <li class="hover:text-accent hover:underline decoration-2 underline-offset-4 cursor-pointer transition-all">
          GITHUB
        </li>
      </ul>
    </div>
  </footer>
</template>

<style scoped>
.font-display {
  font-family: system-ui, -apple-system, sans-serif;
}
</style>
