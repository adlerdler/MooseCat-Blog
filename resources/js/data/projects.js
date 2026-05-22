/**
 * projects.js - 项目数据（单一数据源）
 * 
 * 功能说明：
 * - 存储所有项目数据（包括已完成和进行中）
 * - 包含项目描述、技术栈、链接、状态等信息
 * 
 * 状态说明（以数据库为准）：
 * - planning: 计划中
 * - in-progress: 进行中
 * - completed: 已完成
 * 
 * 数据字段（以数据库为准）：
 * - id: 项目唯一标识
 * - title: 项目标题
 * - description: 简短描述
 * - long_description: 详细描述（仅已完成项目）
 * - client: 客户名称
 * - role: 担任角色
 * - year: 完成年份
 * - image: 项目图片 URL（仅已完成项目）
 * - url: 项目网址
 * - github_url: GitHub 仓库地址
 * - technologies: 技术栈数组（JSON）
 * - status: 项目状态（planning/in-progress/completed）
 * - sort_order: 排序序号
 * - views_count: 浏览次数
 * - likes_count: 点赞次数
 * - created_at: 创建时间
 * - updated_at: 更新时间
 */

export const PROJECTS = [
  // 已完成项目
  {
    id: 1,
    title: 'CONSTRUCT_ENGINE',
    description: 'A modular UI framework built on Constructivist principles, emphasizing structural clarity and algorithmic precision.',
    long_description: 'Construct Engine is the physical manifestation of structural honesty in digital space. It provides a set of rigid yet flexible primitives that allow designers and engineers to build interfaces that feel architected rather than just styled. By stripping away decorative clutter, it exposes the raw logic of the system.',
    client: 'In-House',
    image: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80',
    url: 'https://wiki-z.a1l.xyz',
    github_url: 'https://github.com/adlerdecht/construct-engine',
    role: 'Lead Architect',
    year: 2026,
    technologies: ['TypeScript', 'D3.js', 'Tailwind CSS', 'Redux'],
    status: 'completed',
    sort_order: 1,
    views_count: 3420,
    likes_count: 156,
    created_at: '2024-01-15T10:00:00',
    updated_at: '2024-01-15T10:00:00',
  },
  {
    id: 2,
    title: 'VOID_NAV',
    description: 'Minimalist navigation library designed for high-performance, brute-force UI environments.',
    long_description: 'Void Nav handles the complexity of spatial navigation in multi-layered interfaces. It uses a vector-based mapping system to ensure that the user always knows their current coordinates within the information architecture.',
    client: 'In-House',
    image: 'https://images.unsplash.com/photo-1558655146-d09347e92766?auto=format&fit=crop&w=1200&q=80',
    url: 'https://wiki-z.a1l.xyz',
    github_url: 'https://github.com/adlerdecht/void-nav',
    role: 'Core Developer',
    year: 2025,
    technologies: ['Rust', 'WebAssembly', 'Canvas API'],
    status: 'completed',
    sort_order: 2,
    views_count: 2180,
    likes_count: 89,
    created_at: '2024-06-20T08:30:00',
    updated_at: '2024-06-20T08:30:00',
  },
  // 进行中项目（从 author.js 合并）
  {
    id: 3,
    title: 'WIKI-Z_EVOLUTION',
    description: 'Migrating legacy nodes to the new structural engine.',
    long_description: null,
    client: 'In-House',
    image: 'https://images.unsplash.com/photo-1558655146-d09347e92766?auto=format&fit=crop&w=1200&q=80',
    url: null,
    github_url: null,
    role: 'Lead Developer',
    year: 2025,
    technologies: ['Vue 3', 'TypeScript', 'GraphQL'],
    status: 'completed',
    sort_order: 3,
    views_count: 450,
    likes_count: 23,
    created_at: '2025-01-01T00:00:00',
    updated_at: '2025-01-01T00:00:00',
  },
  {
    id: 4,
    title: 'CONSTRUCT_SDK',
    description: 'Developing a rigid primitive library for web architecture.',
    long_description: null,
    client: 'Open Source',
    image: null,
    url: null,
    github_url: null,
    role: 'Architect',
    year: null,
    technologies: ['TypeScript', 'CSS-in-JS', 'Documentation'],
    status: 'in-progress',
    sort_order: 4,
    views_count: 230,
    likes_count: 15,
    created_at: '2025-06-01T00:00:00',
    updated_at: '2025-06-01T00:00:00',
  },
  {
    id: 5,
    title: 'NEURAL_INDEX',
    description: 'Semantic search mapping for decentralized clusters.',
    long_description: null,
    client: 'Client X',
    image: null,
    url: null,
    github_url: null,
    role: 'Research Engineer',
    year: null,
    technologies: ['Python', 'TensorFlow', 'Vector Databases'],
    status: 'planning',
    sort_order: 5,
    views_count: 100,
    likes_count: 8,
    created_at: '2026-01-01T00:00:00',
    updated_at: '2026-01-01T00:00:00',
  },
  {
    id: 6,
    title: 'SYSTEM_01',
    description: 'Optimizing terminal protocols for high-latency environments.',
    long_description: null,
    client: 'In-House',
    image: null,
    url: null,
    github_url: null,
    role: 'Performance Engineer',
    year: null,
    technologies: ['C', 'Rust', 'Network Protocols'],
    status: 'in-progress',
    sort_order: 6,
    views_count: 80,
    likes_count: 5,
    created_at: '2024-06-01T00:00:00',
    updated_at: '2024-06-01T00:00:00',
  },
];

import { useTaggables } from '../composables/useTaggables.js';

const { getTagsByProjectId, adminTags } = useTaggables();

PROJECTS.forEach(project => {
  if (project.id) {
    project.tags = getTagsByProjectId(project.id, adminTags).map(t => t.name);
  }
});
