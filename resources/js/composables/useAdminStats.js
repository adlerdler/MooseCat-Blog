/**
 * useAdminStats.js - 管理后台统计数据计算函数
 * 
 * 从各个数据表中动态计算统计数据，替代 admin.js 中的硬编码数据。
 * 用于仪表盘统计、图表数据、趋势分析等场景。
 */

import { POSTS } from '../data/posts';
import { adminUsers } from '../data/users';
import { categories } from '../data/categories';
import { commentsData } from '../data/comments';
import { PROJECTS } from '../data/projects';
import { VIDEOS } from '../data/videos';
import { resourcesData } from '../data/resources';
import { visits } from '../data/visits';
import { userLevels } from '../data/user_levels';
import { roles } from '../data/roles';
import { adminTags } from '../data/tags';
import { taggables } from '../data/taggables';

/**
 * 获取统计数据卡片
 * @returns {Array} 统计数据列表
 */
export function getAnalyticsStats() {
  const totalVisits = visits.length;
  const uniqueVisitors = new Set(visits.map(v => v.ip)).size;
  const pageViews = POSTS.reduce((sum, p) => sum + (p.viewsCount || 0), 0) +
                    VIDEOS.reduce((sum, v) => sum + (v.viewsCount || 0), 0) +
                    PROJECTS.reduce((sum, p) => sum + (p.viewsCount || 0), 0);
  const avgDuration = visits.length > 0 
    ? Math.round(visits.reduce((sum, v) => sum + v.duration, 0) / visits.length)
    : 0;

  return [
    { label: 'Total Visits', value: totalVisits.toLocaleString(), change: '+12%', icon_key: 'eye' },
    { label: 'Unique Visitors', value: uniqueVisitors.toLocaleString(), change: '+8%', icon_key: 'users' },
    { label: 'Page Views', value: pageViews.toLocaleString(), change: '+15%', icon_key: 'barChart3' },
    { label: 'Avg. Duration', value: `${Math.floor(avgDuration / 60)}m ${avgDuration % 60}s`, change: '+5%', icon_key: 'clock' },
  ];
}

/**
 * 获取用户统计数据
 * @returns {Array} 用户统计数据列表
 */
export function getUserStats() {
  const totalUsers = adminUsers.length;
  const activeUsers = adminUsers.filter(u => u.status === 'active').length;
  const newUsers = adminUsers.filter(u => {
    const createdAt = new Date(u.createdAt);
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    return createdAt >= thirtyDaysAgo;
  }).length;
  const subscriberCount = 0;

  return [
    { label: 'Total Users', value: totalUsers, change: '+18%', icon_key: 'users', color: 'bg-construct-red' },
    { label: 'Active Users', value: activeUsers, change: '+12%', icon_key: 'activity', color: 'bg-blue-500' },
    { label: 'New Users', value: newUsers, change: '+24%', icon_key: 'userPlus', color: 'bg-green-500' },
    { label: 'Subscribers', value: subscriberCount, change: '+8%', icon_key: 'bell', color: 'bg-purple-500' },
  ];
}

/**
 * 获取文章趋势数据（按月统计）
 * @returns {Array} 月度文章统计数据
 */
export function getPostTrendData() {
  const monthlyStats = {};
  
  POSTS.forEach(post => {
    const date = new Date(post.publishedAt || post.createdAt);
    const month = date.toLocaleString('en-US', { month: 'short' });
    
    if (!monthlyStats[month]) {
      monthlyStats[month] = { month, posts: 0, views: 0 };
    }
    monthlyStats[month].posts++;
    monthlyStats[month].views += post.viewsCount || 0;
  });

  return Object.values(monthlyStats).sort((a, b) => {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return months.indexOf(a.month) - months.indexOf(b.month);
  });
}

/**
 * 获取分类分布数据
 * @returns {Array} 分类分布统计
 */
export function getCategoryDistribution() {
  const colors = ['#dc2626', '#2563eb', '#16a34a', '#9333ea', '#f59e0b', '#ec4899'];
  
  const categoryStats = categories.map((cat, index) => {
    const postCount = POSTS.filter(p => p.categoryId === cat.id).length;
    return {
      name: cat.name.toUpperCase(),
      value: postCount,
      color: colors[index % colors.length]
    };
  }).filter(cat => cat.value > 0);

  return categoryStats;
}

/**
 * 获取用户增长数据（按月统计）
 * @returns {Array} 月度用户增长统计
 */
export function getUserGrowthData() {
  const monthlyStats = {};
  let cumulativeUsers = 0;

  const sortedUsers = [...adminUsers].sort((a, b) => 
    new Date(a.createdAt) - new Date(b.createdAt)
  );

  sortedUsers.forEach(user => {
    const date = new Date(user.createdAt);
    const month = date.toLocaleString('en-US', { month: 'short' });
    
    if (!monthlyStats[month]) {
      monthlyStats[month] = { month, users: 0, newUsers: 0 };
    }
    monthlyStats[month].newUsers++;
  });

  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  
  return Object.entries(monthlyStats)
    .sort((a, b) => months.indexOf(a[0]) - months.indexOf(b[0]))
    .map(([month, stats]) => {
      cumulativeUsers += stats.newUsers;
      return {
        month,
        users: cumulativeUsers,
        newUsers: stats.newUsers
      };
    });
}

/**
 * 获取流量数据（按周统计）
 * @returns {Array} 每日流量统计
 */
export function getTrafficData() {
  const dailyStats = {};
  const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

  visits.forEach(visit => {
    const date = new Date(visit.visitedAt);
    const day = days[date.getDay()];
    
    if (!dailyStats[day]) {
      dailyStats[day] = { day, visits: 0, unique: 0, ips: new Set() };
    }
    dailyStats[day].visits++;
    dailyStats[day].ips.add(visit.ip);
  });

  return Object.values(dailyStats).map(stat => ({
    day: stat.day,
    visits: stat.visits,
    unique: stat.ips.size
  }));
}

/**
 * 获取热门页面
 * @param {number} limit - 返回数量
 * @returns {Array} 热门页面列表
 */
export function getTopPages(limit = 5) {
  const pageStats = {};

  visits.forEach(visit => {
    if (!pageStats[visit.page]) {
      pageStats[visit.page] = { page: visit.page, title: visit.title, views: 0 };
    }
    pageStats[visit.page].views++;
  });

  const totalViews = Object.values(pageStats).reduce((sum, p) => sum + p.views, 0);

  return Object.values(pageStats)
    .sort((a, b) => b.views - a.views)
    .slice(0, limit)
    .map(page => ({
      ...page,
      percentage: Math.round((page.views / totalViews) * 100)
    }));
}

/**
 * 获取流量来源统计
 * @returns {Array} 流量来源统计
 */
export function getTrafficSources() {
  const sourceStats = {};
  const colors = {
    direct: 'bg-construct-red',
    google: 'bg-blue-500',
    social: 'bg-green-500',
    referral: 'bg-purple-500'
  };

  visits.forEach(visit => {
    if (!sourceStats[visit.source]) {
      sourceStats[visit.source] = { source: visit.source, count: 0 };
    }
    sourceStats[visit.source].count++;
  });

  const total = visits.length;

  return Object.values(sourceStats).map(source => ({
    source: source.source.charAt(0).toUpperCase() + source.source.slice(1),
    percentage: Math.round((source.count / total) * 100),
    color: colors[source.source] || 'bg-gray-500'
  }));
}

/**
 * 获取内容统计数据
 * @returns {Array} 内容统计数据列表
 */
export function getContentStats() {
  return [
    { label: 'Total Posts', value: POSTS.length, change: '+12%', icon_key: 'fileText', color: 'bg-construct-red' },
    { label: 'Total Videos', value: VIDEOS.length, change: '+8%', icon_key: 'play', color: 'bg-blue-500' },
    { label: 'Total Projects', value: PROJECTS.length, change: '+15%', icon_key: 'folderKanban', color: 'bg-green-500' },
    { label: 'Total Resources', value: resourcesData.length, change: '+23%', icon_key: 'eye', color: 'bg-purple-500' },
  ];
}

/**
 * 获取内容推荐建议
 * @returns {Array} 推荐建议列表
 */
export function getDesignRecommendations() {
  const postCount = POSTS.length;
  const videoCount = VIDEOS.length;
  const resourcesWithImages = resourcesData.filter(r => r.image).length;

  const recommendations = [];

  if (postCount < 20) {
    recommendations.push({
      type: 'content',
      titleKey: 'rec_theory_title',
      descKey: 'rec_theory_desc',
      priority: 'high'
    });
  }

  if (videoCount < 10) {
    recommendations.push({
      type: 'engagement',
      titleKey: 'rec_video_title',
      descKey: 'rec_video_desc',
      priority: 'medium'
    });
  }

  if (resourcesWithImages < resourcesData.length * 0.5) {
    recommendations.push({
      type: 'design',
      titleKey: 'rec_images_title',
      descKey: 'rec_images_desc',
      priority: 'low'
    });
  }

  return recommendations;
}

/**
 * 获取仪表盘所有统计数据
 * @returns {Object} 仪表盘统计数据集合
 */
export function getDashboardStats() {
  return {
    analyticsStats: getAnalyticsStats(),
    userStats: getUserStats(),
    postTrendData: getPostTrendData(),
    categoryDistribution: getCategoryDistribution(),
    userGrowthData: getUserGrowthData(),
    trafficData: getTrafficData(),
    topPages: getTopPages(),
    trafficSources: getTrafficSources(),
    designRecommendations: getDesignRecommendations()
  };
}

/**
 * 获取评论统计数据
 * @returns {Object} 评论统计数据
 */
export function getCommentStats() {
  const totalComments = commentsData.length;
  const approvedComments = commentsData.filter(c => c.is_approved).length;
  const pendingComments = totalComments - approvedComments;
  const approvalRate = totalComments > 0 ? Math.round((approvedComments / totalComments) * 100) : 0;
  
  const commentsByPost = {};
  commentsData.forEach(comment => {
    if (!commentsByPost[comment.post_id]) {
      commentsByPost[comment.post_id] = 0;
    }
    commentsByPost[comment.post_id]++;
  });
  
  const topCommentedPosts = Object.entries(commentsByPost)
    .sort((a, b) => b[1] - a[1])
    .slice(0, 5)
    .map(([postId, count]) => ({
      post_id: parseInt(postId),
      post_title: POSTS.find(p => p.id === parseInt(postId))?.title || 'Unknown',
      comment_count: count
    }));

  return {
    totalComments,
    approvedComments,
    pendingComments,
    approvalRate,
    topCommentedPosts
  };
}

/**
 * 获取最近评论列表
 * @param {number} limit - 返回数量
 * @returns {Array} 最近评论列表
 */

export function getRecentComments(limit = 5) {
  return [...commentsData]
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    .slice(0, limit)
    .map(comment => ({
      ...comment,
      created_at_formatted: new Date(comment.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }));
}

/**
 * 获取用户等级分布
 * @returns {Array} 用户等级分布
 */
export function getUserLevelDistribution() {
  const colors = ['#ffd700', '#c0c0c0', '#cd7f32', '#4682b4', '#808080'];
  
  return userLevels.map((level, index) => {
    const userCount = adminUsers.filter(u => u.level_id === level.id).length;
    return {
      name: level.name,
      level: level.level,
      icon: level.icon,
      color: level.color,
      chartColor: colors[index % colors.length],
      userCount,
      description: level.description
    };
  }).filter(l => l.userCount > 0);
}

/**
 * 获取角色分布统计
 * @returns {Array} 角色分布
 */
export function getRoleDistribution() {
  const roleColors = {
    admin: '#dc2626',
    editor: '#2563eb',
    author: '#16a34a',
    moderator: '#9333ea',
    subscriber: '#6b7280',
    api: '#f59e0b',
    guest: '#06b6d4'
  };

  return roles.map(role => {
    const userCount = adminUsers.filter(u => u.role_id === role.id).length;
    return {
      name: role.label,
      value: role.value,
      color: roleColors[role.value] || '#6b7280',
      userCount,
      description: role.description
    };
  }).filter(r => r.userCount > 0);
}

/**
 * 获取热门标签统计
 * @param {number} limit - 返回数量
 * @returns {Array} 热门标签列表
 */

export function getPopularTags(limit = 10) {
  const tagUsage = {};
  
  taggables.forEach(taggable => {
    if (!tagUsage[taggable.tag_id]) {
      tagUsage[taggable.tag_id] = 0;
    }
    tagUsage[taggable.tag_id]++;
  });

  return adminTags
    .map(tag => ({
      ...tag,
      actualUsage: tagUsage[tag.id] || 0
    }))
    .sort((a, b) => b.actualUsage - a.actualUsage)
    .slice(0, limit);
}

/**
 * 获取内容状态统计
 * @returns {Object} 内容状态统计
 */
export function getContentStatusStats() {
  const publishedPosts = POSTS.filter(p => p.status === 'published').length;
  const draftPosts = POSTS.filter(p => p.status === 'draft').length;
  const archivedPosts = POSTS.filter(p => p.status === 'archived').length;
  
  const publishedVideos = VIDEOS.filter(v => v.status === 'published').length;
  const draftVideos = VIDEOS.filter(v => v.status === 'draft').length;
  
  const publishedProjects = PROJECTS.filter(p => p.status === 'published').length;
  const draftProjects = PROJECTS.filter(p => p.status === 'draft').length;

  return {
    posts: { published: publishedPosts, draft: draftPosts, archived: archivedPosts, total: POSTS.length },
    videos: { published: publishedVideos, draft: draftVideos, total: VIDEOS.length },
    projects: { published: publishedProjects, draft: draftProjects, total: PROJECTS.length }
  };
}
