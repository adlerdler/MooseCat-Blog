/**
 * author_profiles.js - 作者详细资料数据（方案B - 完整合并版）
 *
 * 统一的作者资料数据结构（以数据库为准）：
 * - id: 资料唯一标识
 * - user_id: 关联用户ID（外键）
 * - slug: URL友好别名
 * - bio: 个人简介
 * - avatar: 头像URL
 * - role_label: 职位标签（国际化key）
 * - role_title: 职位名称（国际化key）
 * - status_label: 状态标签（国际化key）
 * - status_text: 状态文本（国际化key）
 * - is_active: 是否启用
 * - social_links: 社交链接（JSON对象）
 * - expertise: 专业领域（JSON数组）
 * - skills: 技能列表（JSON数组，含等级/分类）
 * - manifestos: 作者宣言（JSON数组）
 * - created_at: 创建时间
 * - updated_at: 更新时间
 */

export const authorProfiles = [
  {
    id: 1,
    user_id: 9,
    slug: 'adler-decht',
    bio: 'Constructivist architect and designer, building digital systems and exploring the intersection of theory and practice.',
    avatar: '/images/avatars/adler.jpg',
    role_label: 'role_designation',
    role_title: 'architect_designer',
    status_label: 'author_status',
    status_text: 'building_systems',
    is_active: true,
    social_links: {
      github: 'https://github.com/adlerdler',
      twitter: 'https://twitter.com/adler_decht',
      linkedin: 'https://linkedin.com/in/adler-decht',
      website: 'https://adler-decht.com'
    },
    expertise: ['Architecture', 'Design', 'Constructivism', 'Digital Systems'],
    skills: [
      {
        label: 'skill_1',
        value: 95,
        description: 'skill_1_desc',
        category: 'frontend',
        sort_order: 1
      },
      {
        label: 'skill_2',
        value: 92,
        description: 'skill_2_desc',
        category: 'backend',
        sort_order: 2
      },
      {
        label: 'skill_3',
        value: 84,
        description: 'skill_3_desc',
        category: 'devops',
        sort_order: 3
      }
    ],
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
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 2,
    user_id: 3,
    slug: 'author-writer',
    bio: 'Technical writer and content creator, specializing in architecture and design documentation.',
    avatar: '/images/avatars/author.jpg',
    role_label: 'role_designation',
    role_title: 'technical_writer',
    status_label: 'author_status',
    status_text: 'creating_content',
    is_active: true,
    social_links: {
      github: 'https://github.com/author-writer',
      twitter: 'https://twitter.com/author_writer'
    },
    expertise: ['Technical Writing', 'Documentation', 'Content Strategy'],
    skills: [
      {
        label: 'skill_writing',
        value: 98,
        description: 'skill_writing_desc',
        category: 'content',
        sort_order: 1
      },
      {
        label: 'skill_documentation',
        value: 95,
        description: 'skill_documentation_desc',
        category: 'content',
        sort_order: 2
      }
    ],
    manifestos: [
      {
        title: 'manifesto',
        content: 'Clear documentation is the bridge between complex systems and user understanding.',
        sort_order: 1
      }
    ],
    created_at: '2024-05-10T14:20:00Z',
    updated_at: '2024-05-10T14:20:00Z'
  },
  {
    id: 3,
    user_id: 5,
    slug: 'open-source-contributor',
    bio: 'Open source contributor and community builder, passionate about collaborative development.',
    avatar: '/images/avatars/contributor.jpg',
    role_label: 'role_designation',
    role_title: 'community_builder',
    status_label: 'author_status',
    status_text: 'building_community',
    is_active: true,
    social_links: {
      github: 'https://github.com/contributor',
      website: 'https://contributor.dev'
    },
    expertise: ['Open Source', 'Community', 'Development'],
    skills: [
      {
        label: 'skill_opensource',
        value: 96,
        description: 'skill_opensource_desc',
        category: 'community',
        sort_order: 1
      },
      {
        label: 'skill_collaboration',
        value: 94,
        description: 'skill_collaboration_desc',
        category: 'community',
        sort_order: 2
      }
    ],
    manifestos: [
      {
        title: 'manifesto',
        content: 'Open source is not just about code, it is about building communities that shape the future.',
        sort_order: 1
      }
    ],
    created_at: '2024-07-15T16:45:00Z',
    updated_at: '2024-07-15T16:45:00Z'
  }
];
