/**
 * visits.js - 访问记录数据定义
 * 
 * 记录每次页面访问的详细信息，用于访问分析、流量统计、用户行为分析等场景。
 * 支持按天/周/月统计访问量，分析访问来源、热门页面等。
 * 
 * 字段说明：
 * - id: 访问记录唯一标识
 * - page: 访问页面路径
 * - title: 页面标题
 * - ip: 访问者IP地址
 * - userAgent: 浏览器信息
 * - referer: 来源页面
 * - source: 流量来源（direct/google/social/referral）
 * - device: 设备类型（desktop/mobile/tablet）
 * - browser: 浏览器类型
 * - os: 操作系统
 * - country: 国家/地区
 * - duration: 停留时长（秒）
 * - visitedAt: 访问时间
 */

export const visits = [
  // 今天的访问记录
  { 
    id: 1, 
    page: '/blog/the-geometry-of-perception', 
    title: '感知的几何性', 
    ip: '192.168.1.100', 
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', 
    referer: 'https://www.google.com', 
    source: 'google', 
    device: 'desktop', 
    browser: 'Chrome', 
    os: 'Windows', 
    country: 'CN', 
    duration: 180, 
    visitedAt: '2024-01-15 09:23:15' 
  },
  { 
    id: 2, 
    page: '/projects/digital-archive', 
    title: '数字档案馆', 
    ip: '192.168.1.101', 
    userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0) Safari/604.1', 
    referer: 'https://twitter.com', 
    source: 'social', 
    device: 'mobile', 
    browser: 'Safari', 
    os: 'iOS', 
    country: 'US', 
    duration: 95, 
    visitedAt: '2024-01-15 09:45:32' 
  },
  { 
    id: 3, 
    page: '/videos/constructivist-design', 
    title: '构成主义设计', 
    ip: '192.168.1.102', 
    userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/120.0', 
    referer: '', 
    source: 'direct', 
    device: 'desktop', 
    browser: 'Chrome', 
    os: 'macOS', 
    country: 'CN', 
    duration: 240, 
    visitedAt: '2024-01-15 10:12:08' 
  },
  { 
    id: 4, 
    page: '/blog/computational-thinking', 
    title: '计算思维', 
    ip: '192.168.1.103', 
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/121.0', 
    referer: 'https://www.bing.com', 
    source: 'google', 
    device: 'desktop', 
    browser: 'Firefox', 
    os: 'Windows', 
    country: 'UK', 
    duration: 150, 
    visitedAt: '2024-01-15 10:35:42' 
  },
  { 
    id: 5, 
    page: '/resources/code-snippets', 
    title: '代码片段库', 
    ip: '192.168.1.104', 
    userAgent: 'Mozilla/5.0 (iPad; CPU OS 17_0) Safari/604.1', 
    referer: 'https://github.com', 
    source: 'referral', 
    device: 'tablet', 
    browser: 'Safari', 
    os: 'iOS', 
    country: 'JP', 
    duration: 120, 
    visitedAt: '2024-01-15 11:08:19' 
  },
  { 
    id: 6, 
    page: '/blog/the-geometry-of-perception', 
    title: '感知的几何性', 
    ip: '192.168.1.105', 
    userAgent: 'Mozilla/5.0 (Linux; Android 14) Chrome/120.0', 
    referer: 'https://www.google.com', 
    source: 'google', 
    device: 'mobile', 
    browser: 'Chrome', 
    os: 'Android', 
    country: 'CN', 
    duration: 200, 
    visitedAt: '2024-01-15 11:32:55' 
  },
  { 
    id: 7, 
    page: '/projects/digital-archive', 
    title: '数字档案馆', 
    ip: '192.168.1.106', 
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Edge/120.0', 
    referer: '', 
    source: 'direct', 
    device: 'desktop', 
    browser: 'Edge', 
    os: 'Windows', 
    country: 'US', 
    duration: 175, 
    visitedAt: '2024-01-15 12:15:33' 
  },
  { 
    id: 8, 
    page: '/blog/design-principles', 
    title: '设计原则', 
    ip: '192.168.1.107', 
    userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/17.0', 
    referer: 'https://www.reddit.com', 
    source: 'social', 
    device: 'desktop', 
    browser: 'Safari', 
    os: 'macOS', 
    country: 'CA', 
    duration: 210, 
    visitedAt: '2024-01-15 13:42:18' 
  },
  { 
    id: 9, 
    page: '/videos/constructivist-design', 
    title: '构成主义设计', 
    ip: '192.168.1.108', 
    userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0) Chrome/120.0', 
    referer: 'https://www.google.com', 
    source: 'google', 
    device: 'mobile', 
    browser: 'Chrome', 
    os: 'iOS', 
    country: 'DE', 
    duration: 160, 
    visitedAt: '2024-01-15 14:28:47' 
  },
  { 
    id: 10, 
    page: '/blog/the-geometry-of-perception', 
    title: '感知的几何性', 
    ip: '192.168.1.109', 
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', 
    referer: 'https://news.ycombinator.com', 
    source: 'referral', 
    device: 'desktop', 
    browser: 'Chrome', 
    os: 'Windows', 
    country: 'CN', 
    duration: 280, 
    visitedAt: '2024-01-15 15:05:22' 
  },
  
  // 昨天的访问记录
  { 
    id: 11, 
    page: '/blog/the-geometry-of-perception', 
    title: '感知的几何性', 
    ip: '192.168.1.110', 
    userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/120.0', 
    referer: 'https://www.google.com', 
    source: 'google', 
    device: 'desktop', 
    browser: 'Chrome', 
    os: 'macOS', 
    country: 'CN', 
    duration: 190, 
    visitedAt: '2024-01-14 09:15:30' 
  },
  { 
    id: 12, 
    page: '/projects/digital-archive', 
    title: '数字档案馆', 
    ip: '192.168.1.111', 
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/121.0', 
    referer: '', 
    source: 'direct', 
    device: 'desktop', 
    browser: 'Firefox', 
    os: 'Windows', 
    country: 'US', 
    duration: 145, 
    visitedAt: '2024-01-14 10:22:45' 
  },
  { 
    id: 13, 
    page: '/videos/constructivist-design', 
    title: '构成主义设计', 
    ip: '192.168.1.112', 
    userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0) Safari/604.1', 
    referer: 'https://twitter.com', 
    source: 'social', 
    device: 'mobile', 
    browser: 'Safari', 
    os: 'iOS', 
    country: 'UK', 
    duration: 110, 
    visitedAt: '2024-01-14 11:38:12' 
  },
  { 
    id: 14, 
    page: '/blog/computational-thinking', 
    title: '计算思维', 
    ip: '192.168.1.113', 
    userAgent: 'Mozilla/5.0 (Linux; Android 14) Chrome/120.0', 
    referer: 'https://www.google.com', 
    source: 'google', 
    device: 'mobile', 
    browser: 'Chrome', 
    os: 'Android', 
    country: 'IN', 
    duration: 170, 
    visitedAt: '2024-01-14 13:52:28' 
  },
  { 
    id: 15, 
    page: '/resources/code-snippets', 
    title: '代码片段库', 
    ip: '192.168.1.114', 
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Edge/120.0', 
    referer: 'https://github.com', 
    source: 'referral', 
    device: 'desktop', 
    browser: 'Edge', 
    os: 'Windows', 
    country: 'CN', 
    duration: 135, 
    visitedAt: '2024-01-14 15:18:55' 
  },
];
