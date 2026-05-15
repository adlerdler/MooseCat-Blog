/**
 * avatarGenerator.js - 抽象头像生成工具
 * 
 * 功能说明：
 * - 根据用户名称（seed）生成唯一的像素风格头像
 * - 使用 SVG 绘制，包含多种表情类型
 * - 随机颜色组合确保每个头像独特
 * 
 * 技术实现：
 * - 基于字符串 hash 生成确定性的随机数
 * - SVG 绘制像素表情图形
 * - 支持多种表情：happy, surprised, cool, sleepy, angry, heart, wink, robot, confused, star, cry, nerd, cat, alien
 */

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
    name: 'robot',
    draw: (hash, size, bgColor, faceColor) => {
      const blockSize = size * 0.1;
      const eyeY = size * 0.35;
      const eyeXOffset = size * 0.2;
      const mouthY = size * 0.6;
      const mouthWidth = size * 0.25;
      
      return `
        <rect width="${size}" height="${size}" fill="#1a1a1a" rx="${size * 0.15}"/>
        <rect x="${size * 0.35}" y="${size * 0.1}" width="${size * 0.06}" height="${size * 0.15}" fill="#4ECDC4" rx="${size * 0.02}"/>
        <rect x="${size * 0.59}" y="${size * 0.1}" width="${size * 0.06}" height="${size * 0.15}" fill="#4ECDC4" rx="${size * 0.02}"/>
        <rect x="${size/2 - eyeXOffset - blockSize}" y="${eyeY - blockSize/2}" width="${blockSize * 1.5}" height="${blockSize * 1.5}" fill="#4ECDC4" rx="${size * 0.02}"/>
        <rect x="${size/2 + eyeXOffset - blockSize/2}" y="${eyeY - blockSize/2}" width="${blockSize * 1.5}" height="${blockSize * 1.5}" fill="#4ECDC4" rx="${size * 0.02}"/>
        <rect x="${size/2 - eyeXOffset - blockSize * 0.6}" y="${eyeY - blockSize * 0.3}" width="${blockSize * 0.4}" height="${blockSize * 0.4}" fill="#fff" rx="${size * 0.01}"/>
        <rect x="${size/2 + eyeXOffset - blockSize * 0.2}" y="${eyeY - blockSize * 0.3}" width="${blockSize * 0.4}" height="${blockSize * 0.4}" fill="#fff" rx="${size * 0.01}"/>
        <rect x="${size/2 - mouthWidth/2}" y="${mouthY - size * 0.05}" width="${mouthWidth}" height="${size * 0.1}" fill="#4ECDC4" rx="${size * 0.02}"/>
        <rect x="${size/2 - mouthWidth/3}" y="${mouthY}" width="${size * 0.05}" height="${size * 0.05}" fill="#1a1a1a" rx="${size * 0.01}"/>
        <rect x="${size/2 - size * 0.025}" y="${mouthY}" width="${size * 0.05}" height="${size * 0.05}" fill="#1a1a1a" rx="${size * 0.01}"/>
        <rect x="${size/2 + mouthWidth/3 - size * 0.05}" y="${mouthY}" width="${size * 0.05}" height="${size * 0.05}" fill="#1a1a1a" rx="${size * 0.01}"/>
        <rect x="${size * 0.15}" y="${size * 0.55}" width="${size * 0.08}" height="${size * 0.08}" fill="#FF6B6B" opacity="0.6" rx="${size * 0.01}"/>
        <rect x="${size * 0.77}" y="${size * 0.55}" width="${size * 0.08}" height="${size * 0.08}" fill="#FF6B6B" opacity="0.6" rx="${size * 0.01}"/>
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
  },
  {
    name: 'star',
    draw: (hash, size, bgColor, faceColor) => {
      const eyeY = size * 0.35;
      const eyeXOffset = size * 0.2;
      const mouthY = size * 0.6;
      const starSize = size * 0.12;
      
      return `
        <rect width="${size}" height="${size}" fill="${bgColor}" rx="${size * 0.15}"/>
        <polygon points="${size/2 - eyeXOffset},${eyeY - starSize} ${size/2 - eyeXOffset + starSize * 0.3},${eyeY - starSize * 0.3} ${size/2 - eyeXOffset + starSize},${eyeY} ${size/2 - eyeXOffset + starSize * 0.3},${eyeY + starSize * 0.3} ${size/2 - eyeXOffset},${eyeY + starSize} ${size/2 - eyeXOffset - starSize * 0.3},${eyeY + starSize * 0.3} ${size/2 - eyeXOffset - starSize},${eyeY} ${size/2 - eyeXOffset - starSize * 0.3},${eyeY - starSize * 0.3}" fill="#FFD700"/>
        <polygon points="${size/2 + eyeXOffset},${eyeY - starSize} ${size/2 + eyeXOffset + starSize * 0.3},${eyeY - starSize * 0.3} ${size/2 + eyeXOffset + starSize},${eyeY} ${size/2 + eyeXOffset + starSize * 0.3},${eyeY + starSize * 0.3} ${size/2 + eyeXOffset},${eyeY + starSize} ${size/2 + eyeXOffset - starSize * 0.3},${eyeY + starSize * 0.3} ${size/2 + eyeXOffset - starSize},${eyeY} ${size/2 + eyeXOffset - starSize * 0.3},${eyeY - starSize * 0.3}" fill="#FFD700"/>
        <path d="M ${size/2 - size * 0.15} ${mouthY} Q ${size/2} ${mouthY + size * 0.12} ${size/2 + size * 0.15} ${mouthY}" 
              fill="none" stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
      `;
    }
  },
  {
    name: 'cry',
    draw: (hash, size, bgColor, faceColor) => {
      const eyeSize = size * 0.08;
      const eyeY = size * 0.35;
      const eyeXOffset = size * 0.2;
      const mouthY = size * 0.6;
      
      return `
        <rect width="${size}" height="${size}" fill="${bgColor}" rx="${size * 0.15}"/>
        <circle cx="${size/2 - eyeXOffset}" cy="${eyeY}" r="${eyeSize}" fill="#1a1a1a"/>
        <circle cx="${size/2 + eyeXOffset}" cy="${eyeY}" r="${eyeSize}" fill="#1a1a1a"/>
        <ellipse cx="${size/2 - eyeXOffset}" cy="${eyeY + size * 0.2}" rx="${size * 0.04}" ry="${size * 0.1}" fill="#4ECDC4" opacity="0.8"/>
        <ellipse cx="${size/2 + eyeXOffset}" cy="${eyeY + size * 0.25}" rx="${size * 0.04}" ry="${size * 0.12}" fill="#4ECDC4" opacity="0.8"/>
        <path d="M ${size/2 - size * 0.12} ${mouthY + size * 0.05} L ${size/2} ${mouthY - size * 0.05} L ${size/2 + size * 0.12} ${mouthY + size * 0.05}" 
              fill="none" stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
      `;
    }
  },
  {
    name: 'nerd',
    draw: (hash, size, bgColor, faceColor) => {
      const eyeSize = size * 0.1;
      const eyeY = size * 0.35;
      const eyeXOffset = size * 0.2;
      const mouthY = size * 0.6;
      
      return `
        <rect width="${size}" height="${size}" fill="${bgColor}" rx="${size * 0.15}"/>
        <circle cx="${size/2 - eyeXOffset}" cy="${eyeY}" r="${eyeSize}" fill="#1a1a1a"/>
        <circle cx="${size/2 + eyeXOffset}" cy="${eyeY}" r="${eyeSize}" fill="#1a1a1a"/>
        <circle cx="${size/2 - eyeXOffset}" cy="${eyeY}" r="${eyeSize * 0.3}" fill="#fff"/>
        <circle cx="${size/2 + eyeXOffset}" cy="${eyeY}" r="${eyeSize * 0.3}" fill="#fff"/>
        <rect x="${size * 0.22}" y="${eyeY - size * 0.02}" width="${size * 0.56}" height="${size * 0.04}" fill="#1a1a1a" rx="${size * 0.01}"/>
        <line x1="${size * 0.22}" y1="${eyeY - size * 0.12}" x2="${size * 0.22}" y2="${eyeY + size * 0.12}" stroke="#1a1a1a" stroke-width="${size * 0.03}"/>
        <line x1="${size * 0.78}" y1="${eyeY - size * 0.12}" x2="${size * 0.78}" y2="${eyeY + size * 0.12}" stroke="#1a1a1a" stroke-width="${size * 0.03}"/>
        <path d="M ${size/2 - size * 0.1} ${mouthY} L ${size/2} ${mouthY} L ${size/2 + size * 0.1} ${mouthY}" 
              fill="none" stroke="#1a1a1a" stroke-width="${size * 0.06}" stroke-linecap="round"/>
      `;
    }
  },
  {
    name: 'cat',
    draw: (hash, size, bgColor, faceColor) => {
      const eyeSize = size * 0.08;
      const eyeY = size * 0.38;
      const eyeXOffset = size * 0.2;
      const mouthY = size * 0.6;
      
      return `
        <rect width="${size}" height="${size}" fill="${bgColor}" rx="${size * 0.15}"/>
        <polygon points="${size * 0.15},${size * 0.05} ${size * 0.25},${size * 0.25} ${size * 0.1},${size * 0.25}" fill="#1a1a1a"/>
        <polygon points="${size * 0.85},${size * 0.05} ${size * 0.75},${size * 0.25} ${size * 0.9},${size * 0.25}" fill="#1a1a1a"/>
        <ellipse cx="${size/2 - eyeXOffset}" cy="${eyeY}" rx="${eyeSize}" ry="${eyeSize * 1.3}" fill="#1a1a1a"/>
        <ellipse cx="${size/2 + eyeXOffset}" cy="${eyeY}" rx="${eyeSize}" ry="${eyeSize * 1.3}" fill="#1a1a1a"/>
        <ellipse cx="${size/2}" cy="${mouthY - size * 0.05}" rx="${size * 0.03}" ry="${size * 0.04}" fill="#FF6B6B"/>
        <path d="M ${size/2} ${mouthY - size * 0.02} L ${size/2 - size * 0.12} ${mouthY + size * 0.05}" fill="none" stroke="#1a1a1a" stroke-width="${size * 0.03}" stroke-linecap="round"/>
        <path d="M ${size/2} ${mouthY - size * 0.02} L ${size/2 + size * 0.12} ${mouthY + size * 0.05}" fill="none" stroke="#1a1a1a" stroke-width="${size * 0.03}" stroke-linecap="round"/>
        <circle cx="${size * 0.2}" cy="${mouthY - size * 0.02}" r="${size * 0.02}" fill="#1a1a1a"/>
        <circle cx="${size * 0.8}" cy="${mouthY - size * 0.02}" r="${size * 0.02}" fill="#1a1a1a"/>
      `;
    }
  },
  {
    name: 'alien',
    draw: (hash, size, bgColor, faceColor) => {
      const eyeY = size * 0.35;
      const eyeXOffset = size * 0.22;
      const mouthY = size * 0.65;
      
      return `
        <rect width="${size}" height="${size}" fill="${bgColor}" rx="${size * 0.15}"/>
        <ellipse cx="${size/2 - eyeXOffset}" cy="${eyeY}" rx="${size * 0.12}" ry="${size * 0.08}" fill="#32CD32"/>
        <ellipse cx="${size/2 + eyeXOffset}" cy="${eyeY}" rx="${size * 0.12}" ry="${size * 0.08}" fill="#32CD32"/>
        <ellipse cx="${size/2 - eyeXOffset}" cy="${eyeY}" rx="${size * 0.06}" ry="${size * 0.04}" fill="#1a1a1a"/>
        <ellipse cx="${size/2 + eyeXOffset}" cy="${eyeY}" rx="${size * 0.06}" ry="${size * 0.04}" fill="#1a1a1a"/>
        <ellipse cx="${size/2}" cy="${mouthY}" rx="${size * 0.08}" ry="${size * 0.04}" fill="#1a1a1a"/>
        <circle cx="${size * 0.15}" cy="${size * 0.2}" r="${size * 0.03}" fill="#32CD32" opacity="0.6"/>
        <circle cx="${size * 0.85}" cy="${size * 0.2}" r="${size * 0.03}" fill="#32CD32" opacity="0.6"/>
      `;
    }
  }
];

const colors = [
  '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
  '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9',
  '#F8B500', '#FF69B4', '#00CED1', '#FF7F50', '#9370DB',
  '#32CD32', '#FFD700', '#FF6347', '#00FA9A', '#BA55D3'
];

/**
 * 根据字符串生成 hash 值
 * @param {string} seed - 种子字符串
 * @returns {number} hash 值
 */
export const generateHash = (seed) => {
  if (!seed || typeof seed !== 'string') {
    seed = 'default';
  }
  let hash = 0;
  for (let i = 0; i < seed.length; i++) {
    hash = seed.charCodeAt(i) + ((hash << 5) - hash);
  }
  return Math.abs(hash);
};

/**
 * 根据种子获取随机颜色
 * @param {string} seed - 种子字符串
 * @param {number} index - 颜色索引
 * @returns {string} 颜色值
 */
export const getRandomColor = (seed, index = 0) => {
  return colors[(generateHash(seed + index) % colors.length)];
};

/**
 * 获取表情类型列表
 * @returns {string[]} 表情名称数组
 */
export const getFaceTypes = () => {
  return pixelFaces.map(face => face.name);
};

/**
 * 根据种子获取表情类型
 * @param {string} seed - 种子字符串
 * @returns {string} 表情名称
 */
export const getFaceType = (seed) => {
  const hash = generateHash(seed);
  return pixelFaces[hash % pixelFaces.length].name;
};

/**
 * 生成 SVG 头像内容
 * @param {string} seed - 种子字符串（通常是用户名）
 * @param {number} size - 头像尺寸，默认 48
 * @returns {string} SVG 字符串
 */
export const generateAvatarSvg = (seed, size = 48) => {
  const hash = generateHash(seed);
  const bgColor = getRandomColor(seed, 1);
  const faceColor = getRandomColor(seed, 2);
  
  const faceIndex = hash % pixelFaces.length;
  const face = pixelFaces[faceIndex];
  
  const content = face.draw(hash, size, bgColor, faceColor);
  
  return `<svg viewBox="0 0 ${size} ${size}" xmlns="http://www.w3.org/2000/svg">${content}</svg>`;
};

/**
 * 生成完整的头像 SVG 元素字符串
 * @param {string} seed - 种子字符串
 * @param {number} size - 头像尺寸
 * @returns {string} 完整的 SVG HTML 字符串
 */
export const generateAvatarHtml = (seed, size = 48) => {
  const svg = generateAvatarSvg(seed, size);
  return `<div class="avatar-container" style="width: ${size}px; height: ${size}px;">${svg}</div>`;
};

/**
 * 生成头像数据 URL（可用于 img src）
 * @param {string} seed - 种子字符串
 * @param {number} size - 头像尺寸
 * @returns {string} data URL
 */
export const generateAvatarDataUrl = (seed, size = 48) => {
  const svg = generateAvatarSvg(seed, size);
  const encoded = encodeURIComponent(svg);
  return `data:image/svg+xml,${encoded}`;
};
