/**
 * author.js - 作者信息数据（数据库格式模拟）
 * 
 * 功能说明：
 * - 模拟数据库作者信息表结构
 * - 存储作者扩展信息（宣言等）
 * - 基础信息（名称、头像）从 users.js 获取
 * - 技能数据通过 author_id 关联 skills.js
 * - 社交链接通过 user_id 关联 links.js
 * - 通过 user_id 关联 users.js 中的用户记录
 * 
 * 表结构：
 * - author_info: 作者扩展信息表
 */

import { getLinksByUserId } from './links';
import { getSkillsByAuthorId } from './skills';

/**
 * 作者信息表（扩展信息，基础信息从 users.js 获取）
 * 字段说明：
 * - id: 作者唯一标识
 * - user_id: 关联用户ID（对应 users.js 中的用户）
 * - slug: URL友好别名
 * - bio: 作者简介
 * - role_label: 职位标签（国际化key）
 * - role_title: 职位名称（国际化key）
 * - status_label: 状态标签（国际化key）
 * - status_text: 状态文本（国际化key）
 * - manifestos: 宣言内容数组（JSON格式，包含title、content、sort_order）
 * - is_active: 是否启用
 * - created_at: 创建时间
 * - updated_at: 更新时间
 */
export const authorInfo = [
  {
    id: 1,
    user_id: 9,
    slug: 'adler-decht',
    bio: 'Constructivist architect and designer, building digital systems and exploring the intersection of theory and practice.',
    role_label: 'role_designation',
    role_title: 'architect_designer',
    status_label: 'author_status',
    status_text: 'building_systems',
    manifestos: [
      {
        title: 'manifesto',
        content: 'I explore the intersection of architecture and technology, building digital systems that bridge the physical and virtual worlds.',
        sort_order: 1
      },
      {
        title: 'manifesto_highlight',
        content: 'Every line of code is a brick. Every system is a structure. Design is both the blueprint and the foundation.',
        sort_order: 2
      },
      {
        title: 'manifesto_conclusion',
        content: 'Through computational thinking and architectural principles, I create experiences that transcend traditional boundaries.',
        sort_order: 3
      }
    ],
    is_active: true,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  }
];

/**
 * 获取作者扩展信息
 */
export const getAuthor = (id = 1) => {
  return authorInfo.find(a => a.id === id && a.is_active);
};

/**
 * 根据用户ID获取作者扩展信息
 */
export const getAuthorByUserId = (userId) => {
  return authorInfo.find(a => a.user_id === userId && a.is_active);
};

/**
 * 获取所有启用的作者
 */
export const getActiveAuthors = () => {
  return authorInfo.filter(a => a.is_active);
};

/**
 * 获取作者的技能列表（通过 author_id 关联 skills.js）
 */
export const getAuthorSkills = (authorId = 1) => {
  return getSkillsByAuthorId(authorId);
};

/**
 * 获取作者的社交链接列表（通过 user_id 关联 links.js）
 */
export const getAuthorLinks = (userId = 9) => {
  return getLinksByUserId(userId);
};
