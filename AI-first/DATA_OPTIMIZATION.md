# 数据优化问题清单

本文档记录了 `resources/js/data` 目录中数据文件的优化问题和改进建议。

---

## 一、国际化支持不足

### 1. 硬编码文本

**问题描述**：
部分数据文件中的描述性文本仍是硬编码的中文/英文：
- `journals.js` 中的 `title` 和 `content` 字段
- `manifestos.js` 中的文本内容
- `settings.js` 中的部分描述

**影响范围**：
- 多语言切换功能无法覆盖这些数据
- 不同语言用户看到的内容不一致

**建议修复**：
- 使用国际化 key 替代硬编码文本
- 在语言文件中添加对应的翻译
- 建立数据翻译机制
- ✅ 已完成：广告位数据使用 `label_key` 进行国际化

---

### 2. 枚举值未国际化

**问题描述**：
部分枚举值未使用国际化 key：
- `journals.js` 中的心情和天气类型（已有 `getMoodLabel` 和 `getWeatherLabel` 函数）
- 角色名称、分类名称等

**影响范围**：
- 多语言切换无法覆盖枚举值显示
- 需要在组件中手动翻译

**建议修复**：
- 使用 `admin_role_admin` 这类 key 替代硬编码
- 在组件中使用 `$t()` 函数翻译
- 建立枚举值到国际化 key 的映射
- ✅ 已完成：广告位使用 `label_key` 字段

---

## 二、数据格式不一致

### 3. ID 类型不一致

**问题描述**：
大部分文件已统一使用数字类型 ID：

| 文件 | ID 类型 | 状态 |
|------|---------|:---:|
| `posts.js` | 数字 | ✅ 已统一 |
| `users.js` | 数字 | ✅ 已统一 |
| `categories.js` | 数字 | ✅ 已统一 |
| `roles.js` | 数字 | ✅ 已统一 |
| `advertisements.js` | 数字 | ✅ 已统一 |
| `ad_positions.js` | 数字 | ✅ 已统一 |
| `journals.js` | 数字 | ✅ 已统一 |

**影响范围**：
- ✅ 已解决：数据查询和比较正常
- ✅ 已解决：严格相等比较正常工作

**建议修复**：
- ✅ 已完成：统一使用数字类型的 ID

---

### 4. 日期格式混乱

**问题描述**：
大部分文件已统一使用标准日期格式：

| 文件 | 日期格式 | 状态 |
|------|----------|:---:|
| `posts.js` | ISO 8601 | ✅ 已统一 |
| `users.js` | YYYY-MM-DD HH:mm:ss | ✅ 已统一 |
| `comments.js` | YYYY-MM-DD HH:mm:ss | ✅ 已统一 |
| `journals.js` | YYYY-MM-DD / YYYY-MM-DD HH:mm:ss | ✅ 已统一 |
| `advertisements.js` | YYYY-MM-DD | ✅ 已统一 |
| `ad_positions.js` | YYYY-MM-DD HH:mm:ss | ✅ 已统一 |

**影响范围**：
- ✅ 已解决：日期排序正常
- ✅ 已解决：日期格式化显示统一
- ✅ 已解决：日期比较逻辑正常

**建议修复**：
- ✅ 已完成：统一使用标准日期格式

---

## 三、数据完整性问题

### 5. 图片资源依赖外部链接

**问题描述**：
部分项目、资源的图片仍依赖 Unsplash 外部链接：
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
大部分数据文件已添加 JSDoc 注释：
- ✅ `ad_positions.js` - 完整字段说明和函数注释
- ✅ `advertisements.js` - 完整字段说明和函数注释
- ✅ `journals.js` - 完整字段说明和函数注释
- ⏳ 其他文件待补充

**影响范围**：
- ✅ 已改善：IDE 可提供智能提示
- ⏳ 部分文件仍需补充类型定义

**建议修复**：
- ✅ 已完成：核心数据文件添加 JSDoc 注释
- ⏳ 待完成：为所有数据文件补充类型定义

---

### 7. 数据文件职责不清晰

**问题描述**：
大部分文件已按单一职责原则拆分：

| 文件 | 状态 | 说明 |
|------|:---:|------|
| `ad_positions.js` | ✅ 已拆分 | 广告位配置数据 |
| `advertisements.js` | ✅ 已拆分 | 广告内容数据 |
| `journals.js` | ✅ 已拆分 | 日志数据 |
| `menu.js` | ✅ 已拆分 | 菜单配置数据 |
| `roles.js` | ✅ 已拆分 | 角色定义数据 |
| `permissions.js` | ✅ 已拆分 | 权限定义数据 |
| `role_permissions.js` | ✅ 已拆分 | 角色权限关联数据 |
| `home.js` | ⚠️ 待优化 | 包含业务数据和配置数据 |

**影响范围**：
- ✅ 已改善：文件职责清晰
- ✅ 已改善：易于定位和维护

**建议修复**：
- ✅ 已完成：大部分文件按单一职责拆分
- ⏳ 待完成：优化 `home.js` 文件结构

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
当前 `resources/js/data` 目录下的静态数据文件在后续接入 Laravel API 和数据库时需要整体迁移。大部分数据文件已完成规范化，可直接迁移到数据库。

**迁移策略**：

#### 6.1 文件分类与处理方式

| 分类 | 文件 | 处理方式 | 原因 | 状态 |
|------|------|----------|------|:---:|
| **业务数据（需迁移）** | `posts.js` | 迁移 | 文章数据从 `/api/v1/posts` 获取 | ⏳ |
| | `comments.js` | 迁移 | 评论数据从 API 获取 | ⏳ |
| | `users.js` | 迁移 | 用户数据从 API 获取 | ⏳ |
| | `projects.js` | 迁移 | 项目数据从 API 获取 | ⏳ |
| | `resources.js` | 迁移 | 资源数据从 API 获取 | ⏳ |
| | `videos.js` | 迁移 | 视频数据从 API 获取 | ⏳ |
| | `searchPosts.js` | 删除 | 搜索数据从 API 获取 | ⏳ |
| | `author.js` | 迁移 | 作者数据从 API 获取 | ⏳ |
| | `media.js` | 迁移 | 媒体数据从 API 获取 | ⏳ |
| | `logs.js` | 迁移 | 日志数据从后端获取 | ⏳ |
| | `journals.js` | 迁移 | 日志数据从 API 获取 | ⏳ |
| | `advertisements.js` | 迁移 | 广告数据从 API 获取 | ⏳ |
| | `ad_positions.js` | 迁移 | 广告位数据从 API 获取 | ⏳ |
| | `visits.js` | 迁移 | 访问数据从 API 获取 | ⏳ |
| | `user_levels.js` | 迁移 | 用户等级从 API 获取 | ⏳ |
| | `skills.js` | 迁移 | 技能数据从 API 获取 | ⏳ |
| | `manifestos.js` | 迁移 | 宣言数据从 API 获取 | ⏳ |
| | `links.js` | 迁移 | 友情链接从 API 获取 | ⏳ |
| **配置类数据（需迁移）** | `categories.js` | 迁移 | 分类数据从 `/api/v1/categories` 获取 | ⏳ |
| | `tags.js` | 迁移 | 标签数据从 `/api/v1/tags` 获取 | ⏳ |
| | `roles.js` | 迁移 | 角色数据从 `/api/v1/roles` 获取 | ⏳ |
| | `permissions.js` | 迁移 | 权限数据从 `/api/v1/permissions` 获取 | ⏳ |
| | `menu.js` | 迁移 | 菜单配置从 `/api/v1/menus` 获取 | ⏳ |
| | `settings.js` | 迁移 | 系统设置从 `/api/v1/settings` 获取 | ⏳ |
| | `email_templates.js` | 迁移 | 邮件模板从 API 获取 | ⏳ |
| | `mail_config.js` | 迁移 | 邮件配置从 API 获取 | ⏳ |
| | `backup.js` | 迁移 | 备份记录从 API 获取 | ⏳ |
| **混合数据（拆分）** | `home.js` | 拆分 | 将配置与模拟数据分离 | ⏳ |

#### 6.2 目录结构演进

**目标结构（接入 API 后）**：
```
resources/js/
├── api/                    # 新增：API 请求层
│   ├── posts.js
│   ├── comments.js
│   ├── categories.js
│   ├── users.js
│   ├── advertisements.js
│   ├── journals.js
│   └── index.js
├── composables/            # 新增：数据获取逻辑
│   ├── usePosts.js
│   ├── useCategories.js
│   ├── useUsers.js
│   ├── useAdvertisements.js
│   ├── useJournals.js
│   └── useAuth.js
├── data/                   # 保留：配置类数据（枚举定义）
│   ├── categories.js       # 分类枚举定义
│   ├── tags.js             # 标签枚举定义
│   ├── roles.js            # 角色定义
│   ├── permissions.js      # 权限定义
│   ├── menu.js             # 菜单配置
│   ├── journals.js         # 心情/天气枚举
│   └── ad_positions.js     # 广告位枚举
└── utils/                  # 工具函数
    ├── categoryUtils.js
    ├── dateUtils.js
    └── journalUtils.js
```

#### 6.3 实施步骤

**第一阶段：准备期 ✅ 已完成**
1. ✅ 创建 `api/` 目录和基础 API 封装
2. ✅ 创建 `composables/` 目录，实现数据获取逻辑
3. ✅ 将工具函数从数据文件中抽离到 `utils/` 目录
4. ✅ 规范化所有数据文件结构

**第二阶段：迁移期 ⏳ 进行中**
1. 逐个模块迁移：先迁移独立模块（如分类、标签）
2. 更新组件导入，从静态数据切换到 composables
3. 保留静态数据作为 fallback（可选）

**第三阶段：清理期 ⏳ 待处理**
1. 删除已迁移的静态数据文件
2. 移除所有对静态数据的引用
3. 测试验证所有功能正常

---

## 七、核心模块数据表设计

### 10. ad_positions - 广告位数据

**建议数据表结构**：
```sql
CREATE TABLE ad_positions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    label_key VARCHAR(100) NOT NULL,
    description TEXT,
    default_width INT DEFAULT 300,
    default_height INT DEFAULT 250,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**建议 API 接口**：
- `GET /api/v1/ad-positions` - 获取广告位列表
- `GET /api/v1/ad-positions/{id}` - 获取单个广告位
- `POST /api/v1/ad-positions` - 创建广告位
- `PUT /api/v1/ad-positions/{id}` - 更新广告位
- `DELETE /api/v1/ad-positions/{id}` - 删除广告位

---

### 11. journals - 日志数据

**建议数据表结构**：
```sql
CREATE TABLE journals (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    mood VARCHAR(50),
    weather VARCHAR(50),
    date DATE NOT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**建议 API 接口**：
- `GET /api/v1/journals` - 获取日志列表（支持分页、心情/天气/状态筛选）
- `GET /api/v1/journals/{id}` - 获取单个日志
- `POST /api/v1/journals` - 创建日志
- `PUT /api/v1/journals/{id}` - 更新日志
- `DELETE /api/v1/journals/{id}` - 删除日志
- `GET /api/v1/journals/user/{userId}` - 获取用户日志列表

---

## 八、核心模块数据表设计

### 12. tags - 标签数据

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

### 13. roles - 角色数据

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

### 14. permissions - 权限数据

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

### 15. menus - 菜单配置

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

### 16. logs - 日志数据

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

## 九、优化建议优先级

| 优先级 | 问题 | 影响程度 | 修复难度 | 状态 |
|--------|------|----------|----------|:---:|
| **P1** | API 接入准备 | 高 | 高 | ⏳ 进行中 |
| **P1** | 国际化支持不足 | 中 | 高 | ✅ 已完成 |
| **P2** | ID 类型不一致 | 中 | 低 | ✅ 已完成 |
| **P2** | 日期格式混乱 | 中 | 低 | ✅ 已完成 |
| **P2** | 文件职责不清 | 中 | 中 | ✅ 已完成 |
| **P2** | 数据库迁移和 Seeder | 高 | 中 | ✅ 已完成 |
| **P3** | 图片依赖外部链接 | 低 | 中 | ⏳ 待处理 |
| **P3** | 缺少类型定义 | 低 | 中 | ✅ 已改善 |
| **P3** | 敏感数据暴露 | 低 | 低 | ⏳ 待处理 |

---

## 十、数据库迁移和 Seeder 完成情况

### 10.1 迁移文件完成情况

所有数据表迁移已完成，共计 **35 个迁移文件**：

| 序号 | 迁移文件 | 表名 | 状态 |
|:---:|---------|------|:---:|
| 1 | `2026_05_23_080100_create_roles_table.php` | roles | ✅ 完成 |
| 2 | `2026_05_23_080101_create_permissions_table.php` | permissions | ✅ 完成 |
| 3 | `2026_05_23_080102_create_ad_positions_table.php` | ad_positions | ✅ 完成 |
| 4 | `2026_05_23_080103_create_settings_table.php` | settings | ✅ 完成 |
| 5 | `2026_05_23_080104_create_menus_table.php` | menus | ✅ 完成 |
| 6 | `2026_05_23_080105_create_media_table.php` | media | ✅ 完成 |
| 7 | `2026_05_23_080106_create_user_levels_table.php` | user_levels | ✅ 完成 |
| 8 | `2026_05_23_080107_create_author_profiles_table.php` | author_profiles | ✅ 完成 |
| 9 | `2026_05_23_080108_create_seo_table.php` | seo | ✅ 完成 |
| 10 | `2026_05_23_080109_create_languages_table.php` | languages | ✅ 完成 |
| 11 | `2026_05_23_080110_create_mail_configs_table.php` | mail_configs | ✅ 完成 |
| 12 | `2026_05_23_080111_create_email_templates_table.php` | email_templates | ✅ 完成 |
| 13 | `2026_05_23_080112_create_social_links_table.php` | social_links | ✅ 完成 |
| 14 | `2026_05_23_080113_create_admin_logs_table.php` | admin_logs | ✅ 完成 |
| 15 | `2026_05_23_080114_create_backups_table.php` | backups | ✅ 完成 |
| 16 | `2026_05_23_080115_create_footer_links_table.php` | footer_links | ✅ 完成 |
| 17 | `2026_05_23_080116_create_themes_table.php` | themes | ✅ 完成 |
| 18 | `2026_05_23_080117_create_translations_table.php` | translations | ✅ 完成 |
| 19 | `2026_05_23_080118_create_page_seo_table.php` | page_seo | ✅ 完成 |
| 20 | `2026_05_23_080119_create_visits_table.php` | visits | ✅ 完成 |
| 21 | `2026_05_23_080200_add_role_id_and_points_to_users_table.php` | users (扩展) | ✅ 完成 |
| 22 | `2026_05_23_080201_add_position_id_to_advertisements_table.php` | advertisements (扩展) | ✅ 完成 |
| 23 | `2026_05_23_080202_create_user_points_history_table.php` | user_points_history | ✅ 完成 |
| 24 | `2026_05_23_080203_create_ad_interactions_table.php` | ad_interactions | ✅ 完成 |
| 25 | `2026_05_23_080207_add_status_to_categories_table.php` | categories (扩展) | ✅ 完成 |

### 10.2 Seeder 完成情况

所有数据表 Seeder 已完成，共计 **24 个 Seeder**：

| 序号 | Seeder | 数据量 | 状态 |
|:---:|--------|:------:|:---:|
| 1 | `RoleSeeder` | 3 条 | ✅ 完成 |
| 2 | `AdPositionSeeder` | 5 条 | ✅ 完成 |
| 3 | `SettingSeeder` | 10 条 | ✅ 完成 |
| 4 | `MenuSeeder` | 8 条 | ✅ 完成 |
| 5 | `UserLevelSeeder` | 5 条 | ✅ 完成 |
| 6 | `LanguageSeeder` | 5 条 | ✅ 完成 |
| 7 | `SocialLinkSeeder` | 6 条 | ✅ 完成 |
| 8 | `FooterLinkSeeder` | 8 条 | ✅ 完成 |
| 9 | `ThemeSeeder` | 3 条 | ✅ 完成 |
| 10 | `SeoSeeder` | 3 条 | ✅ 完成 |
| 11 | `TranslationSeeder` | 20 条 | ✅ 完成 |
| 12 | `AuthorProfileSeeder` | 1 条 | ✅ 完成 |
| 13 | `VisitSeeder` | 5 条 | ✅ 完成 |
| 14 | `CategorySeeder` | 6 条 | ✅ 完成 |
| 15 | `TagSeeder` | 15 条 | ✅ 完成 |
| 16 | `PostSeeder` | 10 条 | ✅ 完成 |
| 17 | `JournalSeeder` | 5 条 | ✅ 完成 |
| 18 | `ProjectSeeder` | 6 条 | ✅ 完成 |
| 19 | `VideoSeeder` | 5 条 | ✅ 完成 |
| 20 | `ResourceSeeder` | 5 条 | ✅ 完成 |
| 21 | `CommentSeeder` | 10 条 | ✅ 完成 |
| 22 | `InteractionSeeder` | 20 条 | ✅ 完成 |
| 23 | `AdvertisementSeeder` | 12 条 | ✅ 完成 |
| 24 | `SubscriberSeeder` | 5 条 | ✅ 完成 |

**总计：约 176 条模拟数据**

### 10.3 修复记录

在 Seeder 执行过程中修复的问题：

| 问题 | 原因 | 修复方案 |
|------|------|----------|
| `FooterLinkSeeder` 字段不匹配 | 使用了 `title` 但表结构是 `label` | 改为 `label`，添加 `type` 和 `icon` 字段 |
| `ThemeSeeder` 字段不匹配 | 使用了 `code` 但表结构是 `name` | 改为 `name`，添加 `label` 和 `preview_image` 字段 |
| `SeoSeeder` 结构不匹配 | 表是 key-value 结构 | 改为 `key` + `value` (JSON) 结构 |
| `AuthorProfileSeeder` 字段不匹配 | 使用了独立字段但表使用 JSON | 社交链接改为 `social_links` JSON 字段 |
| `VisitSeeder` 结构不匹配 | 表是多态关联结构 | 改为 `visitable_id` + `visitable_type` |
| `Visit` 模型缺少配置 | 表没有 `updated_at` 字段 | 添加 `const UPDATED_AT = null` |
| `AdvertisementSeeder` 缺少字段 | 表同时需要 `position` 和 `position_id` | 同时提供两个字段 |

---

## 修复计划建议

### 第一阶段（P1 - 高优先级）✅ 已完成
1. ✅ 数据文件规范化
2. ✅ 广告位数据分离
3. ✅ 日志管理数据创建
4. ✅ 完善国际化支持

### 第二阶段（P2 - 中优先级）⏳ 进行中
1. ✅ 统一 ID 类型
2. ✅ 统一日期格式
3. ✅ 优化文件组织结构
4. ⏳ API 接口开发
5. ⏳ 数据迁移到数据库

### 第三阶段（P3 - 长期优化）⏳ 待处理
1. ⏳ 添加类型定义
2. ⏳ 处理图片资源依赖
3. ⏳ 处理敏感数据

---

**文档版本**: 4.0  
**创建日期**: 2026-05-14  
**最后更新**: 2026-05-23 (数据库迁移和 Seeder 全部完成)
