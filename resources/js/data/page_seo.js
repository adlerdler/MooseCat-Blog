/**
 * page_seo.js - 页面 SEO 配置数据（数据库格式）
 * 
 * 功能说明：
 * - 模拟数据库 page_seo 表结构
 * - 存储各页面的 SEO 配置
 * - 支持后台管理页面编辑
 * 
 * 数据库对应关系：
 * - pageSeoList → page_seo 表
 * 
 * 字段说明：
 * - id: 配置唯一标识
 * - routeName: 路由名称（home, blog, projects...）
 * - path: 页面路径（/, /blog, /projects...）
 * - title: 页面标题
 * - description: 页面描述
 * - keywords: 关键词
 * - schemaType: Schema.org 类型
 * - ogImage: Open Graph 图片 URL
 * - isActive: 是否启用
 * - createdAt: 创建时间
 * - updatedAt: 最后更新时间
 */

export const pageSeoList = [
  {
    id: 1,
    routeName: 'home',
    path: '/',
    title: 'ARCHYX - Constructivist Digital Archive',
    description: 'Exploring digital constructivism through articles, videos, and projects',
    keywords: 'architecture, digital archive, constructivism, design, technology',
    schemaType: 'WebSite',
    ogImage: '',
    isActive: true,
    createdAt: '2024-01-01T00:00:00Z',
    updatedAt: '2024-01-01T00:00:00Z'
  },
  {
    id: 2,
    routeName: 'blog',
    path: '/blog',
    title: 'BLOG // Articles & Essays - ARCHYX',
    description: '探索建筑与技术的边界，阅读最新文章和设计思考',
    keywords: 'blog, articles, essays, architecture, design, technology',
    schemaType: 'CollectionPage',
    ogImage: '',
    isActive: true,
    createdAt: '2024-01-01T00:00:00Z',
    updatedAt: '2024-01-01T00:00:00Z'
  },
  {
    id: 3,
    routeName: 'projects',
    path: '/projects',
    title: 'PROJECTS // Digital Artifacts - ARCHYX',
    description: '探索我们的数字建筑项目和技术实验',
    keywords: 'projects, portfolio, digital, artifacts, development, architecture',
    schemaType: 'CollectionPage',
    ogImage: '',
    isActive: true,
    createdAt: '2024-01-01T00:00:00Z',
    updatedAt: '2024-01-01T00:00:00Z'
  },
  {
    id: 4,
    routeName: 'videos',
    path: '/videos',
    title: 'VIDEOS // Motion Studies - ARCHYX',
    description: '观看建筑设计和数字技术的视频教程与研究',
    keywords: 'videos, motion, studies, tutorials, design, architecture',
    schemaType: 'CollectionPage',
    ogImage: '',
    isActive: true,
    createdAt: '2024-01-01T00:00:00Z',
    updatedAt: '2024-01-01T00:00:00Z'
  },
  {
    id: 5,
    routeName: 'resources',
    path: '/resources',
    title: 'RESOURCES // Design Assets - ARCHYX',
    description: '下载建筑设计资源、工具包和模板',
    keywords: 'resources, design, assets, downloads, ui kit, architecture',
    schemaType: 'CollectionPage',
    ogImage: '',
    isActive: true,
    createdAt: '2024-01-01T00:00:00Z',
    updatedAt: '2024-01-01T00:00:00Z'
  },
  {
    id: 6,
    routeName: 'author',
    path: '/author',
    title: 'AUTHOR // Digital Architect - ARCHYX',
    description: '了解 ARCHYX 的作者和开发者背景',
    keywords: 'author, architect, developer, portfolio, biography',
    schemaType: 'ProfilePage',
    ogImage: '',
    isActive: true,
    createdAt: '2024-01-01T00:00:00Z',
    updatedAt: '2024-01-01T00:00:00Z'
  }
];
