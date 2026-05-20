/**
 * seo_config.js - 全局 SEO 配置数据（数据库格式）
 * 
 * 功能说明：
 * - 模拟数据库 seo_settings 表结构
 * - 存储网站全局 SEO 优化相关配置
 * - 包含 Meta 标签、搜索引擎、站点地图等设置
 * - 支持后台管理页面编辑
 * 
 * 字段说明：
 * - id: 配置唯一标识
 * - meta_title: 网站标题（Meta Title）
 * - meta_description: 网站描述（Meta Description）
 * - meta_keywords: 关键词（Meta Keywords）
 * - google_analytics: Google Analytics ID
 * - baidu_analytics: 百度统计 ID
 * - enable_sitemap: 是否启用站点地图
 * - enable_robots: 是否启用 Robots.txt
 * - canonical_url: 规范链接（Canonical URL）
 * - og_image: Open Graph 图片 URL
 * - og_type: Open Graph 类型
 * - twitter_card: Twitter Card 类型
 * - updated_at: 最后更新时间
 */

export const seoConfig = {
  id: 1,
  metaTitle: 'ARCHYX - Constructivist Digital Archive',
  metaDescription: 'Exploring digital constructivism through articles, videos, and projects',
  metaKeywords: 'architecture, digital archive, constructivism, design, technology',
  googleAnalytics: '',
  baiduAnalytics: '',
  enableSitemap: true,
  enableRobots: true,
  enableLlmTxt: true,
  canonicalUrl: 'https://archyx.com',
  ogImage: '',
  ogType: 'website',
  twitterCard: 'summary_large_image',
  createdAt: '2024-01-01T00:00:00Z',
  updatedAt: '2024-01-01T00:00:00Z'
};
