/**
 * author.js - 作者页面数据
 * 
 * 功能说明：
 * - 存储作者页面相关的静态数据
 * - 包含技能、社交媒体链接等信息
 * - 项目数据统一从 projects.js 获取
 */

import { Github, Twitter, Linkedin, Mail } from 'lucide-vue-next';
import { PROJECTS } from './projects';

/**
 * 导出项目数据（从 projects.js 统一获取）
 */
export { PROJECTS };

/**
 * 技能列表（表格化结构）
 */
export const skills = [
  {
    id: 1,
    label: 'skill_1',
    value: 95,
    description: 'skill_1_desc',
    category: 'frontend',
    sortOrder: 1,
  },
  {
    id: 2,
    label: 'skill_2',
    value: 92,
    description: 'skill_2_desc',
    category: 'backend',
    sortOrder: 2,
  },
  {
    id: 3,
    label: 'skill_3',
    value: 84,
    description: 'skill_3_desc',
    category: 'devops',
    sortOrder: 3,
  },
];

/**
 * 社交媒体链接配置（表格化结构）
 */
export const socialLinks = [
  {
    id: 1,
    platform: 'github',
    icon: Github,
    label: 'GITHUB',
    url: 'https://github.com/adlerdler',
    style: {
      background: 'bg-white',
      textColor: 'text-construct-black',
      hover: 'hover:bg-construct-black hover:text-white',
      border: 'border-2 border-construct-black',
    },
    sortOrder: 1,
  },
  {
    id: 2,
    platform: 'twitter',
    icon: Twitter,
    label: 'TWITTER',
    url: 'https://twitter.com/adlerdler',
    style: {
      background: 'bg-white',
      textColor: 'text-construct-black',
      hover: 'hover:bg-construct-black hover:text-white',
      border: 'border-2 border-construct-black',
    },
    sortOrder: 2,
  },
  {
    id: 3,
    platform: 'linkedin',
    icon: Linkedin,
    label: 'LINKEDIN',
    url: 'https://linkedin.com/in/adlerdler',
    style: {
      background: 'bg-construct-red',
      textColor: 'text-white',
      hover: 'hover:bg-construct-black hover:text-white',
      border: 'border-2 border-construct-red hover:border-construct-black',
    },
    sortOrder: 3,
  },
  {
    id: 4,
    platform: 'email',
    icon: Mail,
    label: 'CONTACT',
    url: 'mailto:contact@adlerdler.com',
    style: {
      background: 'bg-construct-black',
      textColor: 'text-white',
      hover: 'hover:bg-construct-red hover:text-white',
      border: 'border-2 border-construct-black hover:border-construct-red',
    },
    sortOrder: 4,
  },
];


