<script setup>
/**
 * AbstractAvatar.vue - 抽象头像组件
 * 
 * 功能说明：
 * - 根据用户名称（seed）生成唯一的像素风格头像
 * - 使用 SVG 绘制，包含多种表情类型
 * - 随机颜色组合确保每个头像独特
 * 
 * 技术实现：
 * - 基于字符串 hash 生成确定性的随机数
 * - SVG 绘制像素表情图形
 * - 支持多种表情：happy, neutral, wink, surprised, cool
 * 
 * 使用示例：
 * <AbstractAvatar :seed="username" :size="48" />
 */
import { computed } from 'vue';

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

const generateHash = (seed) => {
  if (!seed || typeof seed !== 'string') {
    seed = 'default';
  }
  let hash = 0;
  for (let i = 0; i < seed.length; i++) {
    hash = seed.charCodeAt(i) + ((hash << 5) - hash);
  }
  return Math.abs(hash);
};

const getRandomColor = (seed, index = 0) => {
  const colors = [
    '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
    '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9',
    '#F8B500', '#FF69B4', '#00CED1', '#FF7F50', '#9370DB',
    '#32CD32', '#FFD700', '#FF6347', '#00FA9A', '#BA55D3'
  ];
  return colors[(generateHash(seed + index) % colors.length)];
};

const pixelFaces = [
  {
    name: 'happy',
    draw: (hash, size, bgColor, faceColor) => {
      const eyeSize = size * 0.08;
      const eyeY = size * 0.35;
      const eyeXOffset = size * 0.2;
      const mouthWidth = size * 0.3;
      const mouthHeight = size * 0.15;
      const mouthY = size * 0.6;
      
      return `
        <rect width="${size}" height="${size}" fill="${bgColor}" rx="${size * 0.15}"/>
        <circle cx="${size/2 - eyeXOffset}" cy="${eyeY}" r="${eyeSize}" fill="#1a1a1a"/>
        <circle cx="${size/2 + eyeXOffset}" cy="${eyeY}" r="${eyeSize}" fill="#1a1a1a"/>
        <path d="M ${size/2 - mouthWidth/2} ${mouthY} Q ${size/2} ${mouthY + mouthHeight} ${size/2 + mouthWidth/2} ${mouthY}" 
              fill="none" stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
        <circle cx="${size * 0.25}" cy="${size * 0.55}" r="${size * 0.06}" fill="#FFB6C1" opacity="0.6"/>
        <circle cx="${size * 0.75}" cy="${size * 0.55}" r="${size * 0.06}" fill="#FFB6C1" opacity="0.6"/>
      `;
    }
  },
  {
    name: 'surprised',
    draw: (hash, size, bgColor, faceColor) => {
      const eyeSize = size * 0.12;
      const eyeY = size * 0.35;
      const eyeXOffset = size * 0.2;
      const mouthSize = size * 0.15;
      const mouthY = size * 0.6;
      
      return `
        <rect width="${size}" height="${size}" fill="${bgColor}" rx="${size * 0.15}"/>
        <circle cx="${size/2 - eyeXOffset}" cy="${eyeY}" r="${eyeSize}" fill="#1a1a1a"/>
        <circle cx="${size/2 + eyeXOffset}" cy="${eyeY}" r="${eyeSize}" fill="#1a1a1a"/>
        <circle cx="${size/2 - eyeXOffset}" cy="${eyeY}" r="${eyeSize * 0.4}" fill="#fff"/>
        <circle cx="${size/2 + eyeXOffset}" cy="${eyeY}" r="${eyeSize * 0.4}" fill="#fff"/>
        <circle cx="${size/2}" cy="${mouthY}" r="${mouthSize}" fill="#1a1a1a"/>
      `;
    }
  },
  {
    name: 'cool',
    draw: (hash, size, bgColor, faceColor) => {
      const eyeY = size * 0.35;
      const eyeXOffset = size * 0.25;
      const mouthY = size * 0.58;
      
      return `
        <rect width="${size}" height="${size}" fill="${bgColor}" rx="${size * 0.15}"/>
        <rect x="${size/2 - eyeXOffset - size * 0.08}" y="${eyeY - size * 0.04}" width="${size * 0.16}" height="${size * 0.08}" fill="#1a1a1a" rx="${size * 0.02}"/>
        <rect x="${size/2 + eyeXOffset - size * 0.08}" y="${eyeY - size * 0.04}" width="${size * 0.16}" height="${size * 0.08}" fill="#1a1a1a" rx="${size * 0.02}"/>
        <line x1="${size/2 - eyeXOffset - size * 0.04}" y1="${eyeY}" x2="${size/2 - eyeXOffset + size * 0.04}" y2="${eyeY}" stroke="#fff" stroke-width="${size * 0.02}"/>
        <line x1="${size/2 + eyeXOffset - size * 0.04}" y1="${eyeY}" x2="${size/2 + eyeXOffset + size * 0.04}" y2="${eyeY}" stroke="#fff" stroke-width="${size * 0.02}"/>
        <path d="M ${size/2 - size * 0.2} ${mouthY} L ${size/2} ${mouthY + size * 0.08} L ${size/2 + size * 0.2} ${mouthY}" 
              fill="none" stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
      `;
    }
  },
  {
    name: 'sleepy',
    draw: (hash, size, bgColor, faceColor) => {
      const eyeY = size * 0.35;
      const eyeXOffset = size * 0.2;
      const mouthY = size * 0.6;
      
      return `
        <rect width="${size}" height="${size}" fill="${bgColor}" rx="${size * 0.15}"/>
        <line x1="${size/2 - eyeXOffset - size * 0.1}" y1="${eyeY - size * 0.05}" x2="${size/2 - eyeXOffset + size * 0.1}" y2="${eyeY + size * 0.05}" stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
        <line x1="${size/2 + eyeXOffset - size * 0.1}" y1="${eyeY - size * 0.05}" x2="${size/2 + eyeXOffset + size * 0.1}" y2="${eyeY + size * 0.05}" stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
        <circle cx="${size * 0.2}" cy="${size * 0.25}" r="${size * 0.04}" fill="#FFD700" opacity="0.8"/>
        <circle cx="${size * 0.8}" cy="${size * 0.25}" r="${size * 0.04}" fill="#FFD700" opacity="0.8"/>
        <path d="M ${size/2 - size * 0.15} ${mouthY} L ${size/2} ${mouthY} L ${size/2 + size * 0.15} ${mouthY}" 
              fill="none" stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
      `;
    }
  },
  {
    name: 'angry',
    draw: (hash, size, bgColor, faceColor) => {
      const eyeSize = size * 0.08;
      const eyeY = size * 0.38;
      const eyeXOffset = size * 0.22;
      const mouthY = size * 0.6;
      
      return `
        <rect width="${size}" height="${size}" fill="${bgColor}" rx="${size * 0.15}"/>
        <path d="M ${size/2 - eyeXOffset - size * 0.06} ${eyeY - size * 0.06} L ${size/2 - eyeXOffset + size * 0.06} ${eyeY + size * 0.06}" 
              stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
        <path d="M ${size/2 + eyeXOffset - size * 0.06} ${eyeY - size * 0.06} L ${size/2 + eyeXOffset + size * 0.06} ${eyeY + size * 0.06}" 
              stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
        <path d="M ${size/2 - size * 0.2} ${mouthY + size * 0.05} L ${size/2} ${mouthY - size * 0.05} L ${size/2 + size * 0.2} ${mouthY + size * 0.05}" 
              fill="none" stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
        <path d="M ${size * 0.15} ${size * 0.3} L ${size * 0.2} ${size * 0.35}" stroke="#1a1a1a" stroke-width="${size * 0.04}" stroke-linecap="round"/>
        <path d="M ${size * 0.85} ${size * 0.3} L ${size * 0.8} ${size * 0.35}" stroke="#1a1a1a" stroke-width="${size * 0.04}" stroke-linecap="round"/>
      `;
    }
  },
  {
    name: 'heart',
    draw: (hash, size, bgColor, faceColor) => {
      const eyeSize = size * 0.08;
      const eyeY = size * 0.35;
      const eyeXOffset = size * 0.2;
      const mouthY = size * 0.6;
      
      return `
        <rect width="${size}" height="${size}" fill="${bgColor}" rx="${size * 0.15}"/>
        <path d="M ${size/2 - eyeXOffset} ${eyeY + size * 0.04} C ${size/2 - eyeXOffset - size * 0.08} ${eyeY - size * 0.02}, ${size/2 - eyeXOffset - size * 0.08} ${eyeY - size * 0.1}, ${size/2 - eyeXOffset} ${eyeY - size * 0.06} C ${size/2 - eyeXOffset + size * 0.08} ${eyeY - size * 0.1}, ${size/2 - eyeXOffset + size * 0.08} ${eyeY - size * 0.02}, ${size/2 - eyeXOffset} ${eyeY + size * 0.04}" fill="#FF6B6B"/>
        <path d="M ${size/2 + eyeXOffset} ${eyeY + size * 0.04} C ${size/2 + eyeXOffset - size * 0.08} ${eyeY - size * 0.02}, ${size/2 + eyeXOffset - size * 0.08} ${eyeY - size * 0.1}, ${size/2 + eyeXOffset} ${eyeY - size * 0.06} C ${size/2 + eyeXOffset + size * 0.08} ${eyeY - size * 0.1}, ${size/2 + eyeXOffset + size * 0.08} ${eyeY - size * 0.02}, ${size/2 + eyeXOffset} ${eyeY + size * 0.04}" fill="#FF6B6B"/>
        <path d="M ${size/2 - size * 0.18} ${mouthY} Q ${size/2} ${mouthY - size * 0.1} ${size/2 + size * 0.18} ${mouthY}" 
              fill="none" stroke="#FF6B6B" stroke-width="${size * 0.06}" stroke-linecap="round"/>
      `;
    }
  },
  {
    name: 'wink',
    draw: (hash, size, bgColor, faceColor) => {
      const eyeSize = size * 0.08;
      const eyeY = size * 0.35;
      const eyeXOffset = size * 0.2;
      const mouthWidth = size * 0.25;
      const mouthHeight = size * 0.12;
      const mouthY = size * 0.6;
      
      return `
        <rect width="${size}" height="${size}" fill="${bgColor}" rx="${size * 0.15}"/>
        <circle cx="${size/2 - eyeXOffset}" cy="${eyeY}" r="${eyeSize}" fill="#1a1a1a"/>
        <line x1="${size/2 + eyeXOffset - size * 0.1}" y1="${eyeY}" x2="${size/2 + eyeXOffset + size * 0.1}" y2="${eyeY}" stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
        <path d="M ${size/2 - mouthWidth/2} ${mouthY} Q ${size/2} ${mouthY + mouthHeight} ${size/2 + mouthWidth/2} ${mouthY}" 
              fill="none" stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
      `;
    }
  },
  {
    name: 'confused',
    draw: (hash, size, bgColor, faceColor) => {
      const eyeSize = size * 0.09;
      const eyeY = size * 0.35;
      const eyeXOffset = size * 0.18;
      const mouthY = size * 0.62;
      
      return `
        <rect width="${size}" height="${size}" fill="${bgColor}" rx="${size * 0.15}"/>
        <circle cx="${size/2 - eyeXOffset}" cy="${eyeY}" r="${eyeSize}" fill="#1a1a1a"/>
        <circle cx="${size/2 + eyeXOffset}" cy="${eyeY}" r="${eyeSize}" fill="#1a1a1a"/>
        <circle cx="${size/2 - eyeXOffset}" cy="${eyeY}" r="${eyeSize * 0.35}" fill="#fff"/>
        <circle cx="${size/2 + eyeXOffset}" cy="${eyeY}" r="${eyeSize * 0.35}" fill="#fff"/>
        <path d="M ${size/2 - size * 0.12} ${mouthY} L ${size/2} ${mouthY + size * 0.08} L ${size/2 + size * 0.12} ${mouthY}" 
              fill="none" stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
        <circle cx="${size * 0.7}" cy="${size * 0.25}" r="${size * 0.08}" fill="#1a1a1a" opacity="0.3"/>
        <line x1="${size * 0.72}" y1="${size * 0.18}" x2="${size * 0.72}" y2="${size * 0.12}" stroke="#1a1a1a" stroke-width="${size * 0.03}" opacity="0.3"/>
      `;
    }
  }
];

const svgContent = computed(() => {
  const hash = generateHash(props.seed);
  const bgColor = getRandomColor(props.seed, 1);
  const faceColor = getRandomColor(props.seed, 2);
  
  const faceIndex = hash % pixelFaces.length;
  const face = pixelFaces[faceIndex];
  
  const content = face.draw(hash, props.size, bgColor, faceColor);
  
  return `<svg viewBox="0 0 ${props.size} ${props.size}" xmlns="http://www.w3.org/2000/svg">${content}</svg>`;
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
