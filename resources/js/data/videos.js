/**
 * videos.js - 视频数据（以数据库为准）
 * 
 * 功能说明：
 * - 存储所有视频的静态数据
 * - 支持 YouTube 和 Bilibili 两个平台
 * 
 * 数据字段（以数据库为准）：
 * - id: 视频唯一标识
 * - title: 视频标题
 * - description: 视频描述
 * - video_id: 平台视频ID
 * - platform: 视频平台（youtube/bilibili等）
 * - thumbnail: 缩略图URL
 * - duration: 视频时长
 * - views_count: 播放次数
 * - likes_count: 点赞次数
 * - published_at: 发布时间
 * - created_at: 创建时间
 * - updated_at: 更新时间
 */
export const VIDEOS = [
  {
    id: 1,
    title: 'THE GEOMETRY OF PERCEPTION - VIDEO',
    description: 'Exploring the fundamental concepts of visual perception and how they relate to architectural design principles.',
    video_id: 'dQw4w9WgXcQ',
    platform: 'youtube',
    thumbnail: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?q=80&w=800&h=450&fit=crop',
    duration: '12:34',
    views_count: 12500,
    likes_count: 456,
    published_at: '2026-04-28T10:30:00',
    created_at: '2026-04-28T10:30:00',
    updated_at: '2026-04-28T10:30:00',
  },
  {
    id: 2,
    title: 'DYNAMIC EQUILIBRIUM - BILI',
    description: 'Understanding the balance between structural integrity and dynamic architectural forms in modern construction.',
    video_id: 'BV1XG411s76m',
    platform: 'bilibili',
    thumbnail: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800&h=450&fit=crop',
    duration: '18:45',
    views_count: 8900,
    likes_count: 234,
    published_at: '2026-04-25T14:20:00',
    created_at: '2026-04-25T14:20:00',
    updated_at: '2026-04-25T14:20:00',
  },
  {
    id: 3,
    title: 'DIGITAL FABRICATION TECHNIQUES',
    description: 'Advanced techniques in computational design and digital fabrication processes.',
    video_id: 'CvBfHwUxHIk',
    platform: 'youtube',
    thumbnail: 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=800&h=450&fit=crop',
    duration: '25:12',
    views_count: 15600,
    likes_count: 678,
    published_at: '2026-04-20T09:15:00',
    created_at: '2026-04-20T09:15:00',
    updated_at: '2026-04-20T09:15:00',
  },
  {
    id: 4,
    title: 'SUSTAINABLE ARCHITECTURE',
    description: 'Exploring eco-friendly materials and sustainable design practices in modern architecture.',
    video_id: 'BV1eK41157Qa',
    platform: 'bilibili',
    thumbnail: 'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?q=80&w=800&h=450&fit=crop',
    duration: '22:08',
    views_count: 6700,
    likes_count: 189,
    published_at: '2026-04-15T16:45:00',
    created_at: '2026-04-15T16:45:00',
    updated_at: '2026-04-15T16:45:00',
  },
  {
    id: 5,
    title: 'PARAMETRIC DESIGN WORKFLOW',
    description: 'A comprehensive guide to parametric design workflows using modern software tools.',
    video_id: '7vF8K2lT8fY',
    platform: 'youtube',
    thumbnail: 'https://images.unsplash.com/photo-1534452203293-494d7ddbf7e0?q=80&w=800&h=450&fit=crop',
    duration: '32:15',
    views_count: 21300,
    likes_count: 890,
    published_at: '2026-04-10T11:30:00',
    created_at: '2026-04-10T11:30:00',
    updated_at: '2026-04-10T11:30:00',
  },
  {
    id: 6,
    title: 'ADVANCED MATERIAL SCIENCE',
    description: 'The latest developments in building materials and their applications in contemporary architecture.',
    video_id: 'BV1zt411o7cE',
    platform: 'bilibili',
    thumbnail: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=800&h=450&fit=crop',
    duration: '19:56',
    views_count: 9800,
    likes_count: 345,
    published_at: '2026-04-05T13:20:00',
    created_at: '2026-04-05T13:20:00',
    updated_at: '2026-04-05T13:20:00',
  }
];
