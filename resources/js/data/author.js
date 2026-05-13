/**
 * author.js - 作者页面数据
 * 
 * 功能说明：
 * - 存储作者页面相关的静态数据
 * - 包含技能、项目、社交媒体链接等信息
 */

import { Github, Twitter, Linkedin, Mail } from 'lucide-vue-next';

/**
 * 技能列表（使用国际化键名）
 */
export const skills = [
  { label: 'skill_1', val: '95', desc: 'skill_1_desc' },
  { label: 'skill_2', val: '92', desc: 'skill_2_desc' },
  { label: 'skill_3', val: '84', desc: 'skill_3_desc' },
];

/**
 * 进行中的项目列表
 */
export const activeProjects = [
  {
    name: 'WIKI-Z_EVOLUTION',
    progress: 85,
    status: 'BETA_STABLE',
    desc: 'Migrating legacy nodes to the new structural engine.',
  },
  {
    name: 'CONSTRUCT_SDK',
    progress: 42,
    status: 'ALPHA_REFRACTOR',
    desc: 'Developing a rigid primitive library for web architecture.',
  },
  {
    name: 'NEURAL_INDEX',
    progress: 12,
    status: 'PROTO_IDEATION',
    desc: 'Semantic search mapping for decentralized clusters.',
  },
  {
    name: 'SYSTEM_01',
    progress: 99,
    status: 'FINAL_AUDIT',
    desc: 'Optimizing terminal protocols for high-latency environments.',
  },
];

/**
 * 社交媒体链接配置
 */
export const socialLinks = [
  { 
    icon: Github, 
    label: 'GITHUB', 
    bg: 'bg-white', 
    text: 'text-construct-black', 
    hover: 'hover:bg-construct-black hover:text-white', 
    border: 'border-2 border-construct-black' 
  },
  { 
    icon: Twitter, 
    label: 'TWITTER', 
    bg: 'bg-white', 
    text: 'text-construct-black', 
    hover: 'hover:bg-construct-black hover:text-white', 
    border: 'border-2 border-construct-black' 
  },
  { 
    icon: Linkedin, 
    label: 'LINKEDIN', 
    bg: 'bg-construct-red', 
    text: 'text-white', 
    hover: 'hover:bg-construct-black hover:text-white', 
    border: 'border-2 border-construct-red hover:border-construct-black' 
  },
  { 
    icon: Mail, 
    label: 'CONTACT', 
    bg: 'bg-construct-black', 
    text: 'text-white', 
    hover: 'hover:bg-construct-red hover:text-white', 
    border: 'border-2 border-construct-black hover:border-construct-red' 
  },
];

/**
 * GitHub 用户名
 */
export const githubUsername = 'adlerdler';
