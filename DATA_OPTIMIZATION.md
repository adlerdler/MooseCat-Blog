# 数据优化问题清单

本文档记录了 `resources/js/data` 目录中数据文件的优化问题和改进建议。

---

## 一、国际化支持不足

### 1. 硬编码文本

**问题描述**：
所有数据文件中的描述性文本都是硬编码的英文：
- `description` 字段
- `label` 字段
- `title` 字段

**影响范围**：
- 多语言切换功能无法覆盖这些数据
- 中文用户看到英文内容

**建议修复**：
- 使用国际化 key 替代硬编码文本
- 在语言文件中添加对应的翻译
- 建立数据翻译机制

---

### 2. 枚举值未国际化

**问题描述**：
角色名称、分类名称等枚举值未使用国际化 key：
- 'ADMIN', 'EDITOR', 'AUTHOR' 等角色名称
- 'THEORY', 'DESIGN' 等分类名称

**影响范围**：
- 多语言切换无法覆盖枚举值显示
- 需要在组件中手动翻译

**建议修复**：
- 使用 `admin_role_admin` 这类 key 替代硬编码
- 在组件中使用 `$t()` 函数翻译
- 建立枚举值到国际化 key 的映射

---

## 二、数据格式不一致

### 3. ID 类型不一致

**问题描述**：
不同文件中 ID 的类型不统一：

| 文件 | ID 类型 | 示例 |
|------|---------|------|
| `posts.js` | 字符串 | `'1'` |
| `users.js` | 数字 | `1` |
| `categories.js` | 数字 | `1` |
| `roles.js` | 字符串 | `'admin'` |

**影响范围**：
- 数据查询和比较可能出现类型错误
- 严格相等比较（===）可能失败

**建议修复**：
- 统一使用数字类型的 ID（推荐，便于数据库自增）
- 在数据查询时进行类型转换

---

### 4. 日期格式混乱

**问题描述**：
不同文件中的日期格式不统一：

| 文件 | 日期格式 | 示例 |
|------|----------|------|
| `posts.js` | ISO 8601 | `'2026-04-28T10:30:00'` |
| `users.js` | YYYY-MM-DD | `'2024-01-15'` |
| `comments.js` | 混合格式 | `'2024-01-15 10:30'` 和 `'May 10, 2026, 14:30'` |

**影响范围**：
- 日期排序可能出错
- 日期格式化显示不统一
- 日期比较逻辑复杂

**建议修复**：
- 统一使用 ISO 8601 格式（YYYY-MM-DDTHH:mm:ss）
- 建立日期格式化工具函数

---

## 三、数据完整性问题

### 5. 图片资源依赖外部链接

**问题描述**：
所有项目、资源的图片都依赖 Unsplash 外部链接：
- `projects.js` 中的 `image` 字段
- `resources.js` 中的 `image` 字段
- `videos.js` 中的 `thumbnail` 字段

**影响范围**：
- 外部链接失效会导致图片加载失败
- 网络问题影响页面体验
- 无法控制图片质量和尺寸

**建议修复**：
- 将图片下载到本地存储
- 或使用 CDN 加速
- 建立图片资源管理机制

---

## 四、代码质量问题

### 6. 缺少类型定义

**问题描述**：
所有数据文件都是纯 JSON 数据，缺少 TypeScript 类型定义或 JSDoc 注释

**影响范围**：
- IDE 无法提供智能提示
- 容易出现类型错误
- 代码可维护性差

**建议修复**：
- 添加 TypeScript 类型定义文件
- 或使用 JSDoc 注释
- 建立数据模型接口

---

### 7. 数据文件职责不清晰

**问题描述**：
部分文件同时包含业务数据和配置数据，职责混杂：

| 文件 | 问题 |
|------|------|
| `admin.js` | 包含统计数据、配置数据、模拟数据 |
| `home.js` | 包含业务数据和配置数据（分类、技术栈） |
| `roles.js` | 包含角色定义和权限配置 |

**影响范围**：
- 文件职责过于混杂
- 难以定位和维护
- 不符合单一职责原则

**建议修复**：
- 将 `admin.js` 拆分为 `admin-config.js`、`admin-stats.js`、`admin-mock.js`
- 将 `home.js` 中的业务数据部分移至组件
- 将 `roles.js` 中的权限配置分离，通过关联表管理

---

## 五、安全与隐私问题

### 8. 敏感数据暴露

**问题描述**：
`users.js` 中包含真实格式的邮箱地址：
```javascript
{ email: 'admin@archyx.com' }
```

**影响范围**：
- 敏感信息暴露在源代码中
- 可能被恶意利用
- 不符合安全最佳实践

**建议修复**：
- 使用占位符邮箱（如 `admin@example.com`）
- 或使用脱敏处理（如 `a***@archyx.com`）
- 将敏感数据移至环境变量或配置文件

---

## 六、API/数据库接入准备

### 9. 静态数据文件迁移规划

**问题描述**：
当前 `resources/js/data` 目录下的静态数据文件在后续接入 Laravel API 和数据库时需要整体迁移。直接删除会导致应用崩溃，需要有计划地逐步迁移。

**迁移策略**：

#### 6.1 文件分类与处理方式

| 分类 | 文件 | 处理方式 | 原因 |
|------|------|----------|------|
| **业务数据（需删除）** | `posts.js` | 删除 | 文章数据从 `/api/v1/posts` 获取 |
| | `comments.js` | 删除 | 评论数据从 API 获取 |
| | `users.js` | 删除 | 用户数据从 API 获取 |
| | `projects.js` | 删除 | 项目数据从 API 获取 |
| | `resources.js` | 删除 | 资源数据从 API 获取 |
| | `videos.js` | 删除 | 视频数据从 API 获取 |
| | `searchPosts.js` | 删除 | 搜索数据从 API 获取 |
| | `author.js` | 删除 | 作者数据从 API 获取 |
| | `media.js` | 删除 | 媒体数据从 API 获取 |
| | `logs.js` | 删除 | 日志数据从后端获取 |
| **配置类数据（删除）** | `categories.js` | 删除 | 分类数据从 `/api/v1/categories` 获取 |
| | `tags.js` | 删除 | 标签数据从 `/api/v1/tags` 获取 |
| | `roles.js` | 删除 | 角色数据从 `/api/v1/roles` 获取 |
| | `permissions.js` | 删除 | 权限数据从 `/api/v1/permissions` 获取 |
| | `menu.js` | 删除 | 菜单配置从 `/api/v1/menus` 获取 |
| **混合数据（拆分）** | `admin.js` | 拆分 | 将配置与模拟数据分离 |
| | `home.js` | 拆分 | 将业务数据部分抽离 |

#### 6.2 目录结构演进

**目标结构（接入 API 后）**：
```
resources/js/
├── api/                    # 新增：API 请求层
│   ├── posts.js
│   ├── comments.js
│   ├── categories.js
│   ├── users.js
│   └── index.js
├── composables/            # 新增：数据获取逻辑
│   ├── usePosts.js
│   ├── useCategories.js
│   ├── useUsers.js
│   └── useAuth.js
├── data/                   # 保留：配置类数据
│   ├── categories.js       # 分类枚举定义
│   ├── tags.js             # 标签枚举定义
│   ├── roles.js            # 角色定义
│   ├── permissions.js      # 权限定义
│   └── menu.js             # 菜单配置
└── utils/                  # 工具函数
    ├── categoryUtils.js
    └── dateUtils.js
```

#### 6.3 实施步骤

**第一阶段：准备期**
1. 创建 `api/` 目录和基础 API 封装
2. 创建 `composables/` 目录，实现数据获取逻辑
3. 将工具函数从数据文件中抽离到 `utils/` 目录

**第二阶段：迁移期**
1. 逐个模块迁移：先迁移独立模块（如分类、标签）
2. 更新组件导入，从静态数据切换到 composables
3. 保留静态数据作为 fallback（可选）

**第三阶段：清理期**
1. 删除已迁移的静态数据文件
2. 移除所有对静态数据的引用
3. 测试验证所有功能正常

---

## 七、核心模块数据表设计

### 10. tags - 标签数据

**建议数据表结构**：
```sql
CREATE TABLE tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    usage_count INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 文章标签关联表
CREATE TABLE post_tags (
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (tag_id) REFERENCES tags(id)
);
```

**建议 API 接口**：
- `GET /api/v1/tags` - 获取标签列表（支持分页、搜索、状态筛选）
- `GET /api/v1/tags/{id}` - 获取单个标签
- `POST /api/v1/tags` - 创建标签
- `PUT /api/v1/tags/{id}` - 更新标签
- `DELETE /api/v1/tags/{id}` - 删除标签

---

### 11. roles - 角色数据

**建议数据表结构**：
```sql
CREATE TABLE roles (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    label VARCHAR(50) NOT NULL,
    description TEXT,
    color VARCHAR(20),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 角色权限关联表
CREATE TABLE role_permissions (
    role_id VARCHAR(50) NOT NULL,
    permission_id VARCHAR(50) NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id)
);
```

**建议 API 接口**：
- `GET /api/v1/roles` - 获取角色列表
- `GET /api/v1/roles/{id}` - 获取角色详情（含权限）
- `POST /api/v1/roles` - 创建角色
- `PUT /api/v1/roles/{id}` - 更新角色
- `DELETE /api/v1/roles/{id}` - 删除角色
- `POST /api/v1/roles/{id}/permissions` - 批量设置角色权限

---

### 12. permissions - 权限数据

**建议数据表结构**：
```sql
CREATE TABLE permissions (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    program_id VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**建议 API 接口**：
- `GET /api/v1/permissions` - 获取权限列表
- `GET /api/v1/permissions/{id}` - 获取单个权限
- `POST /api/v1/permissions` - 创建权限
- `PUT /api/v1/permissions/{id}` - 更新权限
- `DELETE /api/v1/permissions/{id}` - 删除权限

---

### 13. menus - 菜单配置

**建议数据表结构**：
```sql
CREATE TABLE menus (
    id INT PRIMARY KEY AUTO_INCREMENT,
    parent_id INT NULL,
    name VARCHAR(50) NOT NULL,
    label_key VARCHAR(100) NOT NULL,
    icon_key VARCHAR(50),
    route VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    permissions TEXT, -- JSON 数组，允许访问的权限
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**建议 API 接口**：
- `GET /api/v1/menus` - 获取菜单列表
- `GET /api/v1/menus/{id}` - 获取单个菜单
- `POST /api/v1/menus` - 创建菜单
- `PUT /api/v1/menus/{id}` - 更新菜单
- `DELETE /api/v1/menus/{id}` - 删除菜单

---

### 14. logs - 日志数据

**建议数据表结构**：
```sql
CREATE TABLE logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    action VARCHAR(50) NOT NULL,
    user_id INT,
    user_name VARCHAR(100),
    ip VARCHAR(50),
    user_agent TEXT,
    details TEXT,
    target_type VARCHAR(50),
    target_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 日志动作类型配置（可选）
CREATE TABLE log_actions (
    action VARCHAR(50) PRIMARY KEY,
    name_zh VARCHAR(100),
    name_en VARCHAR(100),
    name_zh_tw VARCHAR(100)
);
```

**建议 API 接口**：
- `GET /api/v1/logs` - 获取日志列表（支持分页、按时间/用户/动作筛选）
- `GET /api/v1/logs/{id}` - 获取日志详情
- `GET /api/v1/logs/export` - 导出日志（CSV/Excel）
- `DELETE /api/v1/logs` - 批量删除日志

---

## 八、优化建议优先级

| 优先级 | 问题 | 影响程度 | 修复难度 |
|--------|------|----------|----------|
| **P1** | API 接入准备 | 高 | 高 |
| **P1** | 国际化支持不足 | 中 | 高 |
| **P2** | ID 类型不一致 | 中 | 低 |
| **P2** | 日期格式混乱 | 中 | 低 |
| **P2** | 文件职责不清 | 中 | 中 |
| **P3** | 图片依赖外部链接 | 低 | 中 |
| **P3** | 缺少类型定义 | 低 | 中 |
| **P3** | 敏感数据暴露 | 低 | 低 |

---

## 修复计划建议

### 第一阶段（P1 - 高优先级）
1. API 接入准备
2. 完善国际化支持

### 第二阶段（P2 - 中优先级）
1. 统一 ID 类型
2. 统一日期格式
3. 优化文件组织结构

### 第三阶段（P3 - 长期优化）
1. 添加类型定义
2. 处理图片资源依赖
3. 处理敏感数据

---

**文档版本**: 2.0  
**创建日期**: 2026-05-14  
**最后更新**: 2026-05-15
