/**
 * projects.js - 项目数据（单一数据源）
 * 
 * 功能说明：
 * - 存储所有项目数据（包括已完成和进行中）
 * - 包含项目描述、技术栈、链接、状态等信息
 * 
 * 状态说明：
 * - completed: 已完成
 * - active: 进行中
 * 
 * 数据字段：
 * - id: 项目唯一标识
 * - name/title: 项目名称
 * - description: 简短描述
 * - longDescription: 详细描述（仅已完成项目）
 * - image: 项目图片 URL（仅已完成项目）
 * - url: 项目网址
 * - githubUrl: GitHub 仓库地址
 * - tags: 标签数组（通过 getTagsByProjectId 函数从 taggables.js 获取）
 * - role: 担任角色
 * - year: 完成年份
 * - technologies: 技术栈数组
 * - status: 项目状态（completed/active）
 * - progress: 进度百分比（仅进行中项目）
 * - startDate: 开始日期（仅进行中项目）
 * - sortOrder: 排序顺序
 */

export const PROJECTS = [
  // 已完成项目
  {
    id: 1,
    name: 'CONSTRUCT_ENGINE',
    title: 'CONSTRUCT_ENGINE',
    description: 'A modular UI framework built on Constructivist principles, emphasizing structural clarity and algorithmic precision.',
    longDescription: 'Construct Engine is the physical manifestation of structural honesty in digital space. It provides a set of rigid yet flexible primitives that allow designers and engineers to build interfaces that feel architected rather than just styled. By stripping away decorative clutter, it exposes the raw logic of the system.',
    image: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80',
    url: 'https://wiki-z.a1l.xyz',
    githubUrl: 'https://github.com/adlerdecht/construct-engine',
    role: 'Lead Architect',
    year: '2026',
    technologies: ['TypeScript', 'D3.js', 'Tailwind CSS', 'Redux'],
    status: 'completed',
    sortOrder: 1,
  },
  {
    id: 2,
    name: 'VOID_NAV',
    title: 'VOID_NAV',
    description: 'Minimalist navigation library designed for high-performance, brute-force UI environments.',
    longDescription: 'Void Nav handles the complexity of spatial navigation in multi-layered interfaces. It uses a vector-based mapping system to ensure that the user always knows their current coordinates within the information architecture.',
    image: 'https://images.unsplash.com/photo-1558655146-d09347e92766?auto=format&fit=crop&w=1200&q=80',
    githubUrl: 'https://github.com/adlerdecht/void-nav',
    role: 'Core Developer',
    year: '2025',
    technologies: ['Rust', 'WebAssembly', 'Canvas API'],
    status: 'completed',
    sortOrder: 2,
  },
  // 进行中项目（从 author.js 合并）
  {
    id: 3,
    name: 'WIKI-Z_EVOLUTION',
    description: 'Migrating legacy nodes to the new structural engine.',
    progress: 85,
    status: 'active',
    startDate: '2025-01-01',
    sortOrder: 3,
  },
  {
    id: 4,
    name: 'CONSTRUCT_SDK',
    description: 'Developing a rigid primitive library for web architecture.',
    progress: 42,
    status: 'active',
    startDate: '2025-06-01',
    sortOrder: 4,
  },
  {
    id: 5,
    name: 'NEURAL_INDEX',
    description: 'Semantic search mapping for decentralized clusters.',
    progress: 12,
    status: 'active',
    startDate: '2026-01-01',
    sortOrder: 5,
  },
  {
    id: 6,
    name: 'SYSTEM_01',
    description: 'Optimizing terminal protocols for high-latency environments.',
    progress: 99,
    status: 'active',
    startDate: '2024-06-01',
    sortOrder: 6,
  },
];

import { useTaggables } from '../composables/useTaggables.js';

const { getTagsByProjectId, adminTags } = useTaggables();

PROJECTS.forEach(project => {
  if (project.id) {
    project.tags = getTagsByProjectId(project.id, adminTags).map(t => t.name);
  }
});
