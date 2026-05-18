/**
 * admin.js - 管理后台配置数据
 * 
 * 功能说明：
 * - 存储管理后台的配置类数据
 * - 包含图表数据、设置配置、统计选项等
 * - 内容统计数据从各自数据表动态计算
 */

export const timeRanges = [
  { value: '7d', label: '7 DAYS' },
  { value: '30d', label: '30 DAYS' },
  { value: '90d', label: '90 DAYS' },
  { value: '1y', label: '1 YEAR' },
];

export const analyticsStats = [
  { label: 'Total Visits', value: '24,567', change: '+12%', iconKey: 'eye' },
  { label: 'Unique Visitors', value: '18,234', change: '+8%', iconKey: 'users' },
  { label: 'Page Views', value: '89,432', change: '+15%', iconKey: 'barChart3' },
  { label: 'Avg. Duration', value: '4m 32s', change: '+5%', iconKey: 'clock' },
];

export const userStats = [
  { label: 'Total Users', value: 2847, change: '+18%', iconKey: 'users', color: 'bg-construct-red' },
  { label: 'Active Users', value: 1523, change: '+12%', iconKey: 'activity', color: 'bg-blue-500' },
  { label: 'New Users', value: 156, change: '+24%', iconKey: 'userPlus', color: 'bg-green-500' },
  { label: 'Subscribers', value: 892, change: '+8%', iconKey: 'bell', color: 'bg-purple-500' },
];

export const postTrendData = [
  { month: 'Jan', posts: 12, views: 4520 },
  { month: 'Feb', posts: 18, views: 6230 },
  { month: 'Mar', posts: 15, views: 5840 },
  { month: 'Apr', posts: 22, views: 7890 },
  { month: 'May', posts: 28, views: 9120 },
  { month: 'Jun', posts: 25, views: 8450 },
];

export const categoryDistribution = [
  { name: 'THEORY', value: 45, color: '#dc2626' },
  { name: 'DESIGN', value: 32, color: '#2563eb' },
  { name: 'TECHNOLOGY', value: 18, color: '#16a34a' },
  { name: 'CULTURE', value: 15, color: '#9333ea' },
];

export const userGrowthData = [
  { month: 'Jan', users: 1200, newUsers: 85 },
  { month: 'Feb', users: 1450, newUsers: 120 },
  { month: 'Mar', users: 1680, newUsers: 95 },
  { month: 'Apr', users: 1920, newUsers: 145 },
  { month: 'May', users: 2240, newUsers: 180 },
  { month: 'Jun', users: 2847, newUsers: 156 },
];

export const designRecommendations = [
  { 
    type: 'content', 
    titleKey: 'rec_theory_title', 
    descKey: 'rec_theory_desc', 
    priority: 'high'
  },
  { 
    type: 'engagement', 
    titleKey: 'rec_video_title', 
    descKey: 'rec_video_desc', 
    priority: 'medium'
  },
  { 
    type: 'design', 
    titleKey: 'rec_images_title', 
    descKey: 'rec_images_desc', 
    priority: 'low'
  },
];

export const trafficData = [
  { day: 'Mon', visits: 3200, unique: 2800 },
  { day: 'Tue', visits: 3800, unique: 3400 },
  { day: 'Wed', visits: 4500, unique: 3900 },
  { day: 'Thu', visits: 5200, unique: 4600 },
  { day: 'Fri', visits: 4800, unique: 4200 },
  { day: 'Sat', visits: 6100, unique: 5400 },
  { day: 'Sun', visits: 5600, unique: 4900 },
];

export const topPages = [
  { page: '/blog/the-geometry-of-perception', views: 1234, percentage: 35 },
  { page: '/projects/digital-archive', views: 892, percentage: 25 },
  { page: '/videos/constructivist-design', views: 654, percentage: 18 },
  { page: '/blog/computational-thinking', views: 432, percentage: 12 },
  { page: '/resources/code-snippets', views: 321, percentage: 10 },
];

export const trafficSources = [
  { source: 'Direct', percentage: 45, color: 'bg-construct-red' },
  { source: 'Google', percentage: 30, color: 'bg-blue-500' },
  { source: 'Social Media', percentage: 15, color: 'bg-green-500' },
  { source: 'Referral', percentage: 10, color: 'bg-purple-500' },
];

export const defaultSettings = {
  site: {
    name: 'ARCHYX',
    description: 'Constructivist Digital Archive',
    logo: '',
    favicon: '',
    defaultLanguage: 'en',
    timezone: 'UTC+8',
    maintenanceMode: false
  },
  appearance: {
    theme: 'dark',
    accentColor: '#E53E3E',
    font: 'system',
    showAuthorBio: true,
    showComments: true,
    showRelatedPosts: true
  },
  notifications: {
    emailNotifications: true,
    commentApproval: true,
    newUserAlert: true,
    weeklyReport: false,
    digestFrequency: 'weekly'
  },
  seo: {
    metaTitle: 'ARCHYX - Constructivist Digital Archive',
    metaDescription: 'Exploring digital constructivism through articles, videos, and projects',
    googleAnalytics: '',
    enableSitemap: true,
    enableRobots: true,
    canonicalUrl: 'https://archyx.com'
  },
  performance: {
    enableCache: true,
    cacheDuration: 3600,
    enableMinification: true,
    lazyLoadImages: true,
    enableCDN: false
  },
  mail: {
    host: 'smtp.archyx.com',
    port: 587,
    username: 'admin@archyx.com',
    password: '****************',
    encryption: 'tls',
    fromAddress: 'no-reply@archyx.com',
    fromName: 'Archyx System'
  }
};

export const tabsConfig = [
  { id: 'site', labelKey: 'admin_settings_site', iconKey: 'globe' },
  { id: 'appearance', labelKey: 'admin_settings_appearance', iconKey: 'palette' },
  { id: 'notifications', labelKey: 'admin_settings_notifications', iconKey: 'bell' },
  { id: 'seo', labelKey: 'admin_settings_seo', iconKey: 'search' },
  { id: 'mail', labelKey: 'admin_settings_mail', iconKey: 'mail' },
  { id: 'performance', labelKey: 'admin_settings_performance', iconKey: 'zap' }
];