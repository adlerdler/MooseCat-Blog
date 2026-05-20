/**
 * journals.js - 日志数据表
 * 
 * 数据库字段说明：
 * - id: bigint - 日志唯一标识
 * - user_id: bigint - 用户ID（外键）
 * - title: string - 日志标题
 * - content: text - 日志内容
 * - mood: string - 心情
 * - weather: string - 天气
 * - date: date - 日志日期
 * - is_public: boolean - 是否公开
 * - created_at: timestamp - 创建时间
 * - updated_at: timestamp - 更新时间
 */

export const journals = [
  {
    id: 1,
    user_id: 1,
    title: '探索建筑与技术的交汇点',
    content: '今天在设计新项目的过程中，深刻感受到了建筑设计与现代技术融合的魅力。通过参数化设计工具，我能够将复杂的建筑形态转化为可计算的模型，这不仅提高了设计效率，还开拓了新的创意可能性。\n\n下午参加了技术分享会，讨论了WebGL在建筑可视化中的应用前景。这种实时渲染技术能够让客户在设计方案阶段就体验到沉浸式的空间感受，极大地改善了沟通效果。',
    mood: 'excited',
    weather: 'sunny',
    date: '2024-03-15',
    is_public: true,
    created_at: '2024-03-15 09:30:00',
    updated_at: '2024-03-15 18:45:00'
  },
  {
    id: 2,
    user_id: 1,
    title: '关于可持续设计的思考',
    content: '最近一直在研究绿色建筑的设计理念。可持续设计不仅仅是使用环保材料，更重要的是从整体系统角度考虑建筑的生命周期。\n\n今天在阅读Adlerian心理学相关文献时，突然意识到建筑设计与心理学有着深刻的联系。空间如何影响人的情绪和行为，这正是我们作为设计师需要深入思考的问题。',
    mood: 'thoughtful',
    weather: 'cloudy',
    date: '2024-03-18',
    is_public: true,
    created_at: '2024-03-18 14:20:00',
    updated_at: '2024-03-18 20:10:00'
  },
  {
    id: 3,
    user_id: 1,
    title: '代码与建筑的奇妙结合',
    content: '作为一个既热爱编程又热爱建筑的人，我一直在寻找两者的结合点。今天终于开始了一个个人项目——用Three.js创建一个交互式的建筑模型展示平台。\n\n这个项目的目标是让非专业人士也能轻松地浏览和理解复杂的建筑设计。通过简单的鼠标操作，用户可以360度查看建筑模型，甚至可以进行简单的材质和颜色替换。',
    mood: 'happy',
    weather: 'sunny',
    date: '2024-03-22',
    is_public: false,
    created_at: '2024-03-22 10:15:00',
    updated_at: '2024-03-22 22:30:00'
  },
  {
    id: 4,
    user_id: 1,
    title: '雨天随笔',
    content: '窗外下着雨，泡了一杯咖啡，坐在电脑前整理最近的设计笔记。\n\n建筑是一门关于空间的艺术，而编程是一门关于逻辑的艺术。两者看似截然不同，但在深层次上却有着惊人的相似性——都是在创造一种结构，一种能够承载人类活动和情感的结构。',
    mood: 'calm',
    weather: 'rainy',
    date: '2024-03-25',
    is_public: true,
    created_at: '2024-03-25 15:00:00',
    updated_at: '2024-03-25 16:30:00'
  },
  {
    id: 5,
    user_id: 1,
    title: '新项目启动',
    content: '今天正式启动了新的建筑设计项目。这是一个混合用途的建筑综合体，包含商业、办公和住宅功能。\n\n项目的挑战在于如何在有限的用地面积内创造出丰富的空间体验。我们团队提出了"垂直村落"的概念，试图在现代高层建筑中重现传统村落的社区感和人情味。\n\n接下来的几周将是非常忙碌的，但我很期待这个项目的发展。',
    mood: 'motivated',
    weather: 'clear',
    date: '2024-03-28',
    is_public: true,
    created_at: '2024-03-28 08:45:00',
    updated_at: '2024-03-28 19:20:00'
  }
];
