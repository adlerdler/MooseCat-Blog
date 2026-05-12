/**
 * projects.js - 项目数据
 * 
 * 功能说明：
 * - 存储所有已完成项目的静态数据
 * - 包含项目描述、技术栈、链接等信息
 * 
 * 数据字段：
 * - id: 项目唯一标识
 * - title: 项目名称
 * - description: 简短描述
 * - longDescription: 详细描述
 * - image: 项目图片 URL
 * - url: 项目网址
 * - githubUrl: GitHub 仓库地址
 * - tags: 标签数组
 * - role: 担任角色
 * - year: 完成年份
 * - technologies: 技术栈数组
 */
export const PROJECTS = [
  {
    id: '1',
    title: 'CONSTRUCT_ENGINE',
    description: 'A modular UI framework built on Constructivist principles, emphasizing structural clarity and algorithmic precision.',
    longDescription: 'Construct Engine is the physical manifestation of structural honesty in digital space. It provides a set of rigid yet flexible primitives that allow designers and engineers to build interfaces that feel architected rather than just styled. By stripping away decorative clutter, it exposes the raw logic of the system.',
    image: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80',
    url: 'https://wiki-z.a1l.xyz',
    githubUrl: 'https://github.com/adlerdecht/construct-engine',
    tags: ['UI', 'Framework', 'Design-System'],
    role: 'Lead Architect',
    year: '2026',
    technologies: ['TypeScript', 'D3.js', 'Tailwind CSS', 'Redux'],
  },
  {
    id: '2',
    title: 'VOID_NAV',
    description: 'Minimalist navigation library designed for high-performance, brute-force UI environments.',
    longDescription: 'Void Nav handles the complexity of spatial navigation in multi-layered interfaces. It uses a vector-based mapping system to ensure that the user always knows their current coordinates within the information architecture.',
    image: 'https://images.unsplash.com/photo-1558655146-d09347e92766?auto=format&fit=crop&w=1200&q=80',
    githubUrl: 'https://github.com/adlerdecht/void-nav',
    tags: ['Navigation', 'Brutalism', 'Performance'],
    role: 'Core Developer',
    year: '2025',
    technologies: ['Rust', 'WebAssembly', 'Canvas API'],
  },
];
