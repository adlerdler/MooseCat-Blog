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

/**
 * 获取指定日期的访问记录
 * @param {string} date - 日期（YYYY-MM-DD）
 * @returns {Array} 访问记录列表
 */
export function getVisitsByDate(date) {
  return visits.filter(v => v.visitedAt.startsWith(date));
}

/**
 * 获取指定页面的访问记录
 * @param {string} page - 页面路径
 * @returns {Array} 访问记录列表
 */
export function getVisitsByPage(page) {
  return visits.filter(v => v.page === page);
}

/**
 * 获取流量来源统计
 * @returns {Array} 流量来源统计
 */
export function getTrafficSources() {
  const sources = {};
  visits.forEach(v => {
    sources[v.source] = (sources[v.source] || 0) + 1;
  });
  
  const total = visits.length;
  return Object.entries(sources).map(([source, count]) => ({
    source,
    count,
    percentage: Math.round((count / total) * 100)
  }));
}

/**
 * 获取设备类型统计
 * @returns {Array} 设备类型统计
 */
export function getDeviceStats() {
  const devices = {};
  visits.forEach(v => {
    devices[v.device] = (devices[v.device] || 0) + 1;
  });
  
  const total = visits.length;
  return Object.entries(devices).map(([device, count]) => ({
    device,
    count,
    percentage: Math.round((count / total) * 100)
  }));
}

/**
 * 获取热门页面排行
 * @param {number} limit - 返回数量
 * @returns {Array} 热门页面列表
 */
export function getTopPages(limit = 5) {
  const pages = {};
  visits.forEach(v => {
    if (!pages[v.page]) {
      pages[v.page] = { page: v.page, title: v.title, views: 0 };
    }
    pages[v.page].views++;
  });
  
  return Object.values(pages)
    .sort((a, b) => b.views - a.views)
    .slice(0, limit);
}

/**
 * 获取每日访问量统计
 * @returns {Array} 每日访问量统计
 */
export function getDailyVisits() {
  const dailyStats = {};
  visits.forEach(v => {
    const date = v.visitedAt.split(' ')[0];
    dailyStats[date] = (dailyStats[date] || 0) + 1;
  });
  
  return Object.entries(dailyStats)
    .map(([date, count]) => ({ date, count }))
    .sort((a, b) => a.date.localeCompare(b.date));
}

/**
 * 获取浏览器统计
 * @returns {Array} 浏览器统计
 */
export function getBrowserStats() {
  const browsers = {};
  visits.forEach(v => {
    browsers[v.browser] = (browsers[v.browser] || 0) + 1;
  });
  
  const total = visits.length;
  return Object.entries(browsers).map(([browser, count]) => ({
    browser,
    count,
    percentage: Math.round((count / total) * 100)
  }));
}

/**
 * 获取操作系统统计
 * @returns {Array} 操作系统统计
 */
export function getOsStats() {
  const osList = {};
  visits.forEach(v => {
    osList[v.os] = (osList[v.os] || 0) + 1;
  });
  
  const total = visits.length;
  return Object.entries(osList).map(([os, count]) => ({
    os,
    count,
    percentage: Math.round((count / total) * 100)
  }));
}

/**
 * 获取国家/地区统计
 * @returns {Array} 国家/地区统计
 */
export function getCountryStats() {
  const countries = {};
  visits.forEach(v => {
    countries[v.country] = (countries[v.country] || 0) + 1;
  });
  
  const total = visits.length;
  return Object.entries(countries).map(([country, count]) => ({
    country,
    count,
    percentage: Math.round((count / total) * 100)
  }));
}

/**
 * 获取平均停留时长
 * @returns {number} 平均停留时长（秒）
 */
export function getAvgDuration() {
  const total = visits.reduce((sum, v) => sum + v.duration, 0);
  return Math.round(total / visits.length);
}

/**
 * 获取流量来源类型列表
 * @returns {Array} 流量来源类型
 */
export function getSourceTypes() {
  return [
    { value: 'direct', label: '直接访问', color: 'bg-construct-red' },
    { value: 'google', label: '搜索引擎', color: 'bg-blue-500' },
    { value: 'social', label: '社交媒体', color: 'bg-green-500' },
    { value: 'referral', label: '外部链接', color: 'bg-purple-500' },
  ];
}
