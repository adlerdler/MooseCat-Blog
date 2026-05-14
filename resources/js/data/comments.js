/**
 * comments.js - 评论数据
 * 
 * 统一的评论数据结构，包含：
 * - name: 评论作者
 * - email: 邮箱地址
 * - content: 评论内容
 * - postId: 关联文章ID
 * - status: 审核状态（approved, pending, spam）
 * - date: 发布日期
 * - likes: 点赞数
 */

export const commentsData = [
  {
    id: 1,
    name: 'John Doe',
    email: 'john@example.com',
    content: 'Great article! Very insightful analysis of constructivist principles.',
    postId: '1',
    status: 'approved',
    date: '2024-01-15T10:30:00',
    likes: 12
  },
  {
    id: 2,
    name: 'Jane Smith',
    email: 'jane@example.com',
    content: 'Would love to see more examples of parametric design.',
    postId: '2',
    status: 'pending',
    date: '2024-01-15T14:22:00',
    likes: 5
  },
  {
    id: 3,
    name: 'Bob Wilson',
    email: 'bob@example.com',
    content: 'The code examples are very helpful. Thanks!',
    postId: '3',
    status: 'approved',
    date: '2024-01-16T09:15:00',
    likes: 8
  },
  {
    id: 4,
    name: 'Alice Chen',
    email: 'alice@example.com',
    content: 'Can you elaborate on the sustainability aspect?',
    postId: '1',
    status: 'pending',
    date: '2024-01-16T11:45:00',
    likes: 3
  },
  {
    id: 5,
    name: 'Mike Johnson',
    email: 'mike@example.com',
    content: 'Excellent work on the BIM integration section.',
    postId: '4',
    status: 'approved',
    date: '2024-01-17T16:30:00',
    likes: 15
  },
  {
    id: 6,
    name: 'Sarah Lee',
    email: 'sarah@example.com',
    content: 'This is spam. Please remove.',
    postId: '5',
    status: 'spam',
    date: '2024-01-18T08:20:00',
    likes: 0
  },
  {
    id: 7,
    name: 'David Kim',
    email: 'david@example.com',
    content: 'The visualizations are stunning!',
    postId: '6',
    status: 'approved',
    date: '2024-01-18T13:55:00',
    likes: 20
  },
  {
    id: 8,
    name: 'Emma White',
    email: 'emma@example.com',
    content: 'Looking forward to the next article in this series.',
    postId: '7',
    status: 'pending',
    date: '2024-01-19T10:10:00',
    likes: 6
  },
  {
    id: 9,
    name: 'Visitor',
    email: 'visitor@example.com',
    content: 'Interesting perspective on cognitive architecture! The grid analogy really resonates.',
    postId: '8',
    status: 'approved',
    date: '2026-05-10T14:30:00',
    likes: 3
  },
  {
    id: 10,
    name: 'Architect_X',
    email: 'arch@example.com',
    content: 'Great insights! Would love to see more about the intersection of traditional architecture principles with modern computational methods.',
    postId: '2',
    status: 'approved',
    date: '2026-05-11T09:15:00',
    likes: 7
  },
  {
    id: 11,
    name: 'Digital_Builder',
    email: 'builder@example.com',
    content: 'The concept of "structural honesty" in digital spaces is fascinating. Looking forward to future experiments!',
    postId: '9',
    status: 'approved',
    date: '2026-05-12T16:45:00',
    likes: 5
  }
];

/**
 * 获取已审核的评论（用于前台展示）
 */
export const getApprovedComments = () => {
  return commentsData.filter(comment => comment.status === 'approved');
};

/**
 * 获取待审核的评论（用于后台管理）
 */
export const getPendingComments = () => {
  return commentsData.filter(comment => comment.status === 'pending');
};

/**
 * 获取垃圾评论（用于后台管理）
 */
export const getSpamComments = () => {
  return commentsData.filter(comment => comment.status === 'spam');
};

/**
 * 审核评论
 * @param {number} commentId - 评论ID
 * @param {string} status - 审核状态（approved, pending, spam）
 */
export const moderateComment = (commentId, status) => {
  const comment = commentsData.find(c => c.id === commentId);
  if (comment) {
    comment.status = status;
  }
  return comment;
};

/**
 * 根据文章ID获取评论
 * @param {string|number} postId - 文章ID
 */
export const getCommentsByPostId = (postId) => {
  return commentsData.filter(comment => String(comment.postId) === String(postId));
};
