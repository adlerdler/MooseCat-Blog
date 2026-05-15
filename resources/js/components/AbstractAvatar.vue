<script setup>
/**
 * AbstractAvatar.vue - 抽象头像组件
 * 
 * 功能说明：
 * - 根据用户名称（seed）生成唯一的像素风格头像
 * - 使用 SVG 绘制，包含多种表情类型
 * - 随机颜色组合确保每个头像独特
 * 
 * 使用示例：
 * <AbstractAvatar :seed="username" :size="48" />
 */
import { computed } from 'vue';
import { generateAvatarSvg } from '../utils/avatarGenerator';

const props = defineProps({
  seed: {
    type: String,
    required: true
  },
  size: {
    type: Number,
    default: 48
  }
});

const svgContent = computed(() => {
  return generateAvatarSvg(props.seed, props.size);
});
</script>

<template>
  <div 
    class="flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform shadow-lg rounded-full overflow-hidden"
    :style="{ width: `${size}px`, height: `${size}px` }"
  >
    <div v-html="svgContent" class="w-full h-full" />
  </div>
</template>
