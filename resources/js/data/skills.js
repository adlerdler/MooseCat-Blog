/**
 * skills.js - 技能数据（数据库格式模拟）
 * 
 * 功能说明：
 * - 存储作者技能数据
 * - 用于作者页面展示技能水平
 * - 通过 author_id 关联 author.js 中的作者记录
 * 
 * 表结构：
 * - skills: 技能表
 * 
 * 字段说明：
 * - id: 技能唯一标识
 * - author_id: 关联作者ID（对应 author.js 中的作者）
 * - label: 国际化标签key
 * - value: 技能值（百分比）
 * - description: 技能描述key
 * - category: 分类
 * - sort_order: 排序顺序
 */

export const skills = [
  {
    id: 1,
    author_id: 1,
    label: 'skill_1',
    value: 95,
    description: 'skill_1_desc',
    category: 'frontend',
    sort_order: 1,
  },
  {
    id: 2,
    author_id: 1,
    label: 'skill_2',
    value: 92,
    description: 'skill_2_desc',
    category: 'backend',
    sort_order: 2,
  },
  {
    id: 3,
    author_id: 1,
    label: 'skill_3',
    value: 84,
    description: 'skill_3_desc',
    category: 'devops',
    sort_order: 3,
  },
];

/**
 * 根据作者ID获取技能列表
 */
export const getSkillsByAuthorId = (authorId) => {
  return skills
    .filter(s => s.author_id === authorId)
    .sort((a, b) => a.sort_order - b.sort_order);
};

/**
 * 获取所有技能列表
 */
export const getAllSkills = () => {
  return skills.sort((a, b) => a.sort_order - b.sort_order);
};
