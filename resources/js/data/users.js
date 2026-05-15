/**
 * users.js - 用户数据定义
 * 
 * 角色字段使用 roles.js 中的数字 ID 进行关联：
 * - 1: admin - 系统管理员
 * - 2: editor - 内容编辑
 * - 3: author - 文章作者
 * - 4: moderator - 社区版主
 * - 5: subscriber - 订阅用户
 * - 6: api - API用户
 * - 7: guest - 访客用户
 */

export const adminUsers = [
  { id: 1, name: 'Admin User', email: 'admin@archyx.com', roleId: 1, status: 'active', joined: '2024-01-15T00:00:00', penName: 'ADLER DECHT' },
  { id: 2, name: 'Content Editor', email: 'editor@archyx.com', roleId: 2, status: 'active', joined: '2024-03-20T00:00:00', penName: 'RODCHENKO' },
  { id: 3, name: 'Author Writer', email: 'author@archyx.com', roleId: 3, status: 'active', joined: '2024-05-10T00:00:00', penName: 'ADLERIAN' },
  { id: 4, name: 'Guest User', email: 'guest@example.com', roleId: 7, status: 'inactive', joined: '2024-06-01T00:00:00', penName: null },
  { id: 5, name: 'Contributor', email: 'contributor@archyx.com', roleId: 3, status: 'active', joined: '2024-07-15T00:00:00', penName: 'V. TATLIN' },
  { id: 6, name: 'Moderator', email: 'moderator@archyx.com', roleId: 4, status: 'active', joined: '2024-08-20T00:00:00', penName: null },
  { id: 7, name: 'Subscriber', email: 'subscriber@example.com', roleId: 5, status: 'inactive', joined: '2024-09-01T00:00:00', penName: null },
  { id: 8, name: 'API User', email: 'api@archyx.com', roleId: 6, status: 'active', joined: '2024-10-15T00:00:00', penName: null },
];

/**
 * 根据用户ID获取用户信息
 * @param {number} userId - 用户ID
 * @returns {Object|undefined} 用户对象
 */
export const getUserById = (userId) => {
  return adminUsers.find(u => u.id === userId);
};

/**
 * 根据用户ID获取作者笔名
 * @param {number} userId - 用户ID
 * @returns {string} 作者笔名或用户名
 */
export const getAuthorName = (userId) => {
  const user = getUserById(userId);
  return user ? (user.penName || user.name) : 'Unknown';
};