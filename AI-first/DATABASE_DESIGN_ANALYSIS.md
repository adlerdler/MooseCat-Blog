# Data 目录数据库设计规范分析

## 一、当前数据结构概览

| 文件名 | 数据表名称 | 数据量 | 主要用途 |
|--------|-----------|--------|----------|
| admin.js | - | 配置数据 | 管理后台图表和统计数据 |
| advertisements.js | advertisements | 广告数据 | 广告位和广告内容 |
| author.js | author_skills, author_social_links | 作者信息 | 技能和社交媒体链接 |
| backup.js | backup_records | 备份记录 | 系统备份数据 |
| categories.js | categories | 分类数据 | 文章分类 |
| comments.js | comments | 评论数据 | 用户评论 |
| home.js | - | 首页配置 | 首页静态数据 |
| logs.js | admin_logs | 操作日志 | 系统操作记录 |
| media.js | media_files | 媒体文件 | 媒体库管理 |
| menu.js | menus | 菜单配置 | 前台和后台菜单 |
| permissions.js | permissions | 权限定义 | 系统权限 |
| posts.js | posts | 文章数据 | 博客文章 |
| projects.js | projects | 项目数据 | 项目展示 |
| resources.js | resources | 资源数据 | 下载资源 |
| role_permissions.js | role_permissions | 角色权限关联 | 多对多关系 |
| roles.js | roles | 角色定义 | 用户角色 |
| searchPosts.js | - | 搜索示例 | 搜索组件示例数据 |
| settings.js | settings | 系统设置 | 配置项 |
| tags.js | tags | 标签数据 | 文章标签 |
| users.js | users | 用户数据 | 系统用户 |
| videos.js | videos | 视频数据 | 视频展示 |

---

## 二、数据库设计问题分析

### 2.1 缺少统一的主键规范

**问题描述：**
- 部分表使用数字自增ID（如users、posts）
- 部分表使用字符串ID（如projects使用'rick-astley'）
- 部分表没有明确的主键定义

**数据库规范：**
```sql
-- 推荐：统一使用 BIGINT UNSIGNED AUTO_INCREMENT
CREATE TABLE posts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ...
);

-- 不推荐：使用字符串作为主键
CREATE TABLE projects (
  id VARCHAR(50) PRIMARY KEY,  -- 不利于索引和关联
  ...
);
```

**建议：**
- 所有表统一使用 `id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- 如需唯一标识符，添加 `slug VARCHAR(100) UNIQUE` 字段

---

### 2.2 缺少时间戳字段

**问题描述：**
- 部分表只有单个时间字段（如date、joined）
- 缺少 `created_at` 和 `updated_at` 标准字段
- 部分表完全没有时间记录

**数据库规范：**
```sql
CREATE TABLE posts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ...
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL  -- 软删除
);
```

**受影响文件：**
- posts.js - 只有date字段
- comments.js - 只有date字段
- users.js - 只有joined字段
- projects.js - 时间字段不统一

---

### 2.3 外键关联不规范

**问题描述：**
- posts表使用userId关联users表，但没有外键约束说明
- comments表使用postId关联posts表，但类型不一致（字符串vs数字）
- role_permissions表正确模拟了多对多关系

**数据库规范：**
```sql
-- 正确的外键关联
CREATE TABLE posts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

CREATE TABLE comments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NULL,  -- 允许匿名用户
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

**建议：**
- 统一外键命名规范：`表名_id`（如user_id、post_id）
- 明确关联关系（一对一、一对多、多对多）
- 设置适当的删除策略（CASCADE、SET NULL、RESTRICT）

---

### 2.4 枚举值未统一管理

**问题描述：**
- 状态枚举分散在各个文件中（如status: 'active/inactive'、status: 'approved/pending/spam'）
- 没有统一的枚举定义表
- 前端硬编码枚举值

**数据库规范：**
```sql
-- 方案1：使用ENUM类型（MySQL）
CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
  ...
);

-- 方案2：使用状态字典表（推荐，更灵活）
CREATE TABLE status_types (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  table_name VARCHAR(50) NOT NULL,  -- 关联的表名
  status_code VARCHAR(50) NOT NULL,  -- 状态代码
  status_label VARCHAR(100) NOT NULL,  -- 状态标签
  sort_order INT DEFAULT 0,
  UNIQUE KEY uk_table_status (table_name, status_code)
);

-- 插入状态字典数据
INSERT INTO status_types (table_name, status_code, status_label, sort_order) VALUES
('users', 'active', '启用', 1),
('users', 'inactive', '禁用', 2),
('users', 'suspended', '暂停', 3),
('comments', 'approved', '已通过', 1),
('comments', 'pending', '待审核', 2),
('comments', 'spam', '垃圾评论', 3);
```

**建议：**
- 使用状态字典表管理所有枚举值
- 前端通过API获取枚举值，避免硬编码
- 便于后续扩展和国际化

---

### 2.5 缺少索引设计

**问题描述：**
- 没有定义任何索引
- 查询性能无法保证
- 外键字段没有索引

**数据库规范：**
```sql
CREATE TABLE posts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NOT NULL,
  status VARCHAR(20) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  -- 单列索引
  INDEX idx_user_id (user_id),
  INDEX idx_category_id (category_id),
  INDEX idx_status (status),
  INDEX idx_created_at (created_at),
  
  -- 复合索引
  INDEX idx_user_status (user_id, status),
  INDEX idx_category_created (category_id, created_at)
);
```

**建议索引：**
| 表名 | 建议索引字段 | 索引类型 |
|------|-------------|----------|
| posts | user_id, category_id, status, created_at | 单列+复合 |
| comments | post_id, status, created_at | 单列+复合 |
| users | email, status, role_id | 单列 |
| menus | type, parent_id, sort_order | 单列+复合 |
| projects | status, created_at | 单列 |

---

### 2.6 数据冗余问题

**问题描述：**
- admin.js中包含可从其他表计算的统计数据
- home.js中的categories从categories.js导入，但又导出为categories
- 部分数据存在重复定义

**数据库规范：**
```sql
-- 不推荐：存储冗余统计数据
CREATE TABLE admin_stats (
  id INT PRIMARY KEY,
  total_posts INT,  -- 可从posts表COUNT得到
  total_users INT,  -- 可从users表COUNT得到
  ...
);

-- 推荐：使用视图或动态查询
CREATE VIEW v_admin_stats AS
SELECT 
  (SELECT COUNT(*) FROM posts WHERE status = 'published') AS total_posts,
  (SELECT COUNT(*) FROM users WHERE status = 'active') AS total_users,
  (SELECT COUNT(*) FROM comments WHERE status = 'approved') AS total_comments;
```

**建议：**
- 移除admin.js中的冗余统计数据
- 使用视图或动态查询获取统计数据
- 保持单一数据源原则

---

### 2.7 缺少软删除机制

**问题描述：**
- 所有删除操作都是物理删除
- 无法恢复误删数据
- 无法记录删除历史

**数据库规范：**
```sql
CREATE TABLE posts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ...
  deleted_at TIMESTAMP NULL DEFAULT NULL,  -- 软删除字段
  
  -- 查询时排除已删除数据
  INDEX idx_deleted_at (deleted_at)
);

-- 查询示例
SELECT * FROM posts WHERE deleted_at IS NULL;  -- 正常查询
SELECT * FROM posts WHERE deleted_at IS NOT NULL;  -- 查看已删除
```

**建议：**
- 所有业务表添加 `deleted_at` 字段
- 查询时默认过滤 `deleted_at IS NULL`
- 提供数据恢复功能

---

### 2.8 缺少数据验证约束

**问题描述：**
- 没有NOT NULL约束
- 没有DEFAULT值
- 没有CHECK约束

**数据库规范：**
```sql
CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role_id INT UNSIGNED NOT NULL DEFAULT 5,  -- 默认为subscriber
  status VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  CHECK (LENGTH(password) >= 8),  -- 密码长度约束
  CHECK (status IN ('active', 'inactive', 'suspended'))  -- 状态约束
);
```

**建议约束：**
| 字段类型 | 约束 |
|---------|------|
| 必填字段 | NOT NULL |
| 唯一字段 | UNIQUE |
| 状态字段 | CHECK/ENUM |
| 密码字段 | CHECK (LENGTH >= 8) |
| 邮箱字段 | CHECK (LIKE '%@%.%') |
| 外键字段 | NOT NULL + FOREIGN KEY |

---

### 2.9 多对多关系处理不规范

**问题描述：**
- role_permissions.js正确模拟了关联表
- 但posts和tags之间没有关联表
- 缺少中间表定义

**数据库规范：**
```sql
-- 文章-标签多对多关系
CREATE TABLE post_tag (
  post_id BIGINT UNSIGNED NOT NULL,
  tag_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (post_id, tag_id),
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
  INDEX idx_tag_id (tag_id)
);

-- 查询示例
SELECT p.*, t.name AS tag_name
FROM posts p
LEFT JOIN post_tag pt ON p.id = pt.post_id
LEFT JOIN tags t ON pt.tag_id = t.id;
```

**建议中间表：**
| 中间表 | 关联关系 |
|--------|---------|
| post_tag | 文章-标签 |
| user_role | 用户-角色（已有role_permissions） |
| project_technology | 项目-技术栈 |
| resource_drive | 资源-网盘链接 |

---

### 2.10 缺少数据审计字段

**问题描述：**
- 没有记录创建者和修改者
- 无法追踪数据变更历史
- 缺少操作日志

**数据库规范：**
```sql
CREATE TABLE posts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ...
  created_by BIGINT UNSIGNED NULL,  -- 创建者
  updated_by BIGINT UNSIGNED NULL,  -- 最后修改者
  deleted_by BIGINT UNSIGNED NULL,  -- 删除者
  
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 操作日志表
CREATE TABLE audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  table_name VARCHAR(50) NOT NULL,
  record_id BIGINT UNSIGNED NOT NULL,
  action ENUM('create', 'update', 'delete') NOT NULL,
  old_values JSON NULL,
  new_values JSON NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  ip_address VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  INDEX idx_table_record (table_name, record_id),
  INDEX idx_user_id (user_id),
  INDEX idx_created_at (created_at)
);
```

**建议审计字段：**
- `created_by` - 创建者ID
- `updated_by` - 最后修改者ID
- `deleted_by` - 删除者ID
- 独立的审计日志表记录所有变更

---

## 三、数据库设计最佳实践建议

### 3.1 命名规范

```sql
-- 表名：小写复数，下划线分隔
users, posts, post_tags, role_permissions

-- 字段名：小写，下划线分隔
user_id, created_at, post_title

-- 索引名：idx_表名_字段
idx_users_email, idx_posts_user_id_status

-- 外键名：fk_表名_关联表名
fk_posts_user_id, fk_comments_post_id
```

### 3.2 数据类型选择

| 数据类型 | 使用场景 | 示例 |
|---------|---------|------|
| BIGINT UNSIGNED | 主键、外键 | id, user_id |
| VARCHAR(255) | 短文本 | name, email, title |
| TEXT | 长文本 | content, description |
| JSON | 复杂结构 | settings, metadata |
| TIMESTAMP | 时间戳 | created_at, updated_at |
| ENUM | 固定枚举 | status, type |
| DECIMAL | 金额 | price, amount |
| BOOLEAN/TINYINT | 布尔值 | is_active, is_deleted |

### 3.3 表结构模板

```sql
CREATE TABLE table_name (
  -- 主键
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  
  -- 业务字段
  name VARCHAR(100) NOT NULL,
  description TEXT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'active',
  
  -- 关联字段
  user_id BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NULL,
  
  -- 审计字段
  created_by BIGINT UNSIGNED NULL,
  updated_by BIGINT UNSIGNED NULL,
  
  -- 时间戳
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  
  -- 索引
  INDEX idx_user_id (user_id),
  INDEX idx_status (status),
  INDEX idx_created_at (created_at),
  
  -- 外键
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 四、优先级改进建议

### 4.1 高优先级（必须修改）

1. **统一主键规范** - 所有表使用BIGINT UNSIGNED AUTO_INCREMENT
2. **添加时间戳字段** - created_at、updated_at
3. **规范外键关联** - 统一命名和类型
4. **添加索引** - 提升查询性能

### 4.2 中优先级（建议修改）

1. **添加软删除** - deleted_at字段
2. **统一管理枚举** - 状态字典表
3. **添加数据验证** - NOT NULL、UNIQUE、CHECK
4. **完善中间表** - 多对多关系

### 4.3 低优先级（可选优化）

1. **添加审计字段** - created_by、updated_by
2. **操作日志表** - audit_logs
3. **数据冗余清理** - 移除admin.js统计数据
4. **视图和存储过程** - 复杂查询优化

---

## 五、数据库ER图（核心表）

```
users (1) ────< (N) posts
  │                    │
  │                    │
(N)                  (N)
  │                    │
  └───< role_permissions >─── (N) roles
                              │
                              │
                            (N)
                              │
                        categories
```

---

## 六、总结

当前data目录的数据结构已经具备了基本的数据库思维，但仍需要以下改进：

1. **规范化** - 统一主键、外键、索引规范
2. **完整性** - 添加时间戳、审计字段、软删除
3. **性能** - 合理设计索引、避免数据冗余
4. **可维护性** - 统一管理枚举、完善中间表
5. **安全性** - 添加数据验证约束、密码加密

建议按照优先级逐步改进，先完成高优先级项目，再逐步优化中低优先级项目。
