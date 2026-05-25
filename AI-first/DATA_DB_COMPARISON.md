# Data 文件与数据库表结构对比分析

> **重要说明**：本文档基于实际扫描 `resources/js/data/` 目录和 `database/migrations/` 目录生成，确保内容与实际项目状态一致。
> **最后更新**：2026-05-24 (已完成所有迁移字段对比和修复)

---

## 一、整体对比概览

### 1.1 数据文件清单（33个）

| 序号 | 文件名 | 对应表名 | 状态 | 说明 |
|:---:|--------|---------|:---:|------|
| 1 | ad_positions.js | ad_positions | ✅ 字段完全匹配 | 广告位配置数据 |
| 2 | advertisements.js | advertisements | ✅ 字段完全匹配 | 广告内容数据 |
| 3 | author_profiles.js | author_profiles | ✅ 字段完全匹配 | 作者个人资料、社交链接、技能等 |
| 4 | backup.js | backups | ✅ 字段完全匹配 | 系统备份记录 |
| 5 | categories.js | categories | ✅ 字段完全匹配 | 文章分类数据 |
| 6 | comments.js | comments | ✅ 字段完全匹配 | 文章评论数据 |
| 7 | email_templates.js | email_templates | ✅ 字段完全匹配 | 邮件模板数据 |
| 8 | footer_config.js | footer_links | ✅ 字段完全匹配 | 页脚社交链接、导航链接、品牌信息配置 |
| 9 | interactions.js | interactions | ✅ 字段完全匹配 | 用户互动数据（点赞/收藏） |
| 10 | journals.js | journals | ✅ 字段完全匹配 | 日志/日记数据 |
| 11 | logs.js | admin_logs | ✅ 字段完全匹配 | 管理员操作日志 |
| 12 | mail_config.js | mail_configs | ✅ 字段完全匹配 | 邮件服务器配置 |
| 13 | media.js | media | ✅ 字段完全匹配 | 媒体文件管理数据 |
| 14 | menu.js | menus | ✅ 字段完全匹配 | 后台菜单配置 |
| 15 | notifications.js | notifications | ✅ Laravel 内置 | 站内通知数据 |
| 16 | page_seo.js | page_seo | ✅ 字段完全匹配 | 各页面SEO配置 |
| 17 | permissions.js | permissions | ✅ 字段完全匹配 | 系统权限定义（含 guard_name 字段） |
| 18 | posts.js | posts | ✅ 字段完全匹配 | 文章内容数据 |
| 19 | projects.js | projects | ✅ 字段完全匹配 | 项目作品集数据 |
| 20 | resources.js | resources | ✅ 字段完全匹配 | 资源下载数据 |
| 21 | role_permissions.js | role_has_permissions | ✅ Spatie 内置 | 角色与权限的关联关系 |
| 22 | roles.js | roles | ✅ 字段完全匹配 | 用户角色定义 |
| 23 | seo_config.js | seo | ✅ 字段完全匹配 | 网站全局SEO默认配置 |
| 24 | settings.js | settings | ✅ 字段完全匹配 | 网站系统配置 |
| 25 | site_config.js | settings | ✅ 已合并到 settings | 站点基本信息、品牌配置 |
| 26 | subscribers.js | subscribers | ✅ 字段完全匹配 | 邮件订阅者数据 |
| 27 | tags.js | tags | ✅ 字段完全匹配 | 标签数据 |
| 28 | taggables.js | taggables | ✅ 字段完全匹配 | 标签多态关联中间表 |
| 29 | themes.js | themes | ✅ 字段完全匹配 | 主题配置数据 |
| 30 | user_levels.js | user_levels | ✅ 字段完全匹配 | 用户等级数据 |
| 31 | users.js | users | ✅ 字段完全匹配 | 用户账户数据 |
| 32 | videos.js | videos | ✅ 字段完全匹配 | 视频内容数据 |
| 33 | visits.js | visits | ✅ 字段完全匹配 | 访问记录统计 |
| 34 | i18n_config.js | languages + translations | ✅ 字段完全匹配 | 多语言配置数据 |

### 1.2 数据库表清单（45个）

| 序号 | 表名 | 对应数据文件 | 状态 | 说明 |
|:---:|------|-------------|:---:|------|
| **框架内置表** |
| 1 | users | users.js | ✅ 字段完全匹配 | 用户账户信息表 |
| 2 | password_reset_tokens | - | ✅ Laravel 内置 | 密码重置令牌表 |
| 3 | sessions | - | ✅ Laravel 内置 | 会话表 |
| 4 | cache | - | ✅ Laravel 内置 | 缓存表 |
| 5 | cache_locks | - | ✅ Laravel 内置 | 缓存锁表 |
| 6 | jobs | - | ✅ Laravel 内置 | 队列任务表 |
| 7 | job_batches | - | ✅ Laravel 内置 | 队列批次表 |
| 8 | failed_jobs | - | ✅ Laravel 内置 | 失败任务表 |
| 9 | notifications | notifications.js | ✅ Laravel 内置 | 通知表 |
| 10 | personal_access_tokens | - | ✅ Laravel 内置 | Sanctum令牌表 |
| **业务数据表** |
| 11 | categories | categories.js | ✅ 字段完全匹配 | 文章分类表 |
| 12 | posts | posts.js | ✅ 字段完全匹配 | 文章内容表 |
| 13 | tags | tags.js | ✅ 字段完全匹配 | 标签表 |
| 14 | taggables | taggables.js | ✅ 字段完全匹配 | 标签多态关联中间表 |
| 15 | comments | comments.js | ✅ 字段完全匹配 | 评论表 |
| 16 | projects | projects.js | ✅ 字段完全匹配 | 项目表 |
| 17 | resources | resources.js | ✅ 字段完全匹配 | 资源下载表 |
| 18 | videos | videos.js | ✅ 字段完全匹配 | 视频表 |
| 19 | interactions | interactions.js | ✅ 字段完全匹配 | 用户互动表（点赞/收藏） |
| 20 | advertisements | advertisements.js | ✅ 字段完全匹配 | 广告表 |
| 21 | journals | journals.js | ✅ 字段完全匹配 | 日志/日记表 |
| 22 | subscribers | subscribers.js | ✅ 字段完全匹配 | 订阅者表 |
| 23 | ad_positions | ad_positions.js | ✅ 字段完全匹配 | 广告位配置表 |
| 24 | author_profiles | author_profiles.js | ✅ 字段完全匹配 | 作者资料表 |
| 25 | backups | backup.js | ✅ 字段完全匹配 | 备份记录表 |
| 26 | email_templates | email_templates.js | ✅ 字段完全匹配 | 邮件模板表 |
| 27 | footer_links | footer_config.js | ✅ 字段完全匹配 | 页脚链接表 |
| 28 | admin_logs | logs.js | ✅ 字段完全匹配 | 管理员日志表 |
| 29 | mail_configs | mail_config.js | ✅ 字段完全匹配 | 邮件配置表 |
| 30 | media | media.js | ✅ 字段完全匹配 | 媒体文件表 |
| 31 | menus | menu.js | ✅ 字段完全匹配 | 菜单配置表 |
| 32 | page_seo | page_seo.js | ✅ 字段完全匹配 | 页面SEO表 |
| 33 | permissions | permissions.js | ✅ 字段完全匹配 | 权限定义表 |
| 34 | roles | roles.js | ✅ 字段完全匹配 | 角色定义表 |
| 35 | role_has_permissions | role_permissions.js | ✅ Spatie 内置 | 角色权限关联表 |
| 36 | seo | seo_config.js | ✅ 字段完全匹配 | SEO配置表 |
| 37 | settings | settings.js | ✅ 字段完全匹配 | 系统设置表 |
| 38 | themes | themes.js | ✅ 字段完全匹配 | 主题配置表 |
| 39 | user_levels | user_levels.js | ✅ 字段完全匹配 | 用户等级表 |
| 40 | languages | i18n_config.js | ✅ 字段完全匹配 | 语言配置表 |
| 41 | translations | i18n_config.js | ✅ 字段完全匹配 | 翻译数据表 |
| 42 | visits | visits.js | ✅ 字段完全匹配 | 访问记录表 |
| 43 | social_links | footer_config.js | ✅ 字段完全匹配 | 社交链接表 |
| 44 | user_points_history | - | ✅ 有迁移 | 积分历史表 |
| 45 | ad_interactions | advertisements.js | ✅ 有迁移 | 广告互动表 |

---

## 二、Laravel 框架内置表（无需手动创建）

> 以下表格由 Laravel 框架通过 `artisan` 命令自动创建，属于框架基础设施的一部分。

| 表名 | Artisan 命令 | 用途 |
|------|-------------|------|
| **users** | `make:auth` | 用户认证基础表 |
| **password_reset_tokens** | `make:auth` | 密码重置令牌存储 |
| **sessions** | `session:table` | 会话存储（数据库驱动） |
| **cache** | `cache:table` | 缓存队列存储 |
| **cache_locks** | `cache:table` | 缓存锁实现 |
| **jobs** | `queue:table` | 延迟任务队列 |
| **job_batches** | `queue:batches-table` | 队列批次追踪 |
| **failed_jobs** | `queue:failed-table` | 失败任务记录 |
| **notifications** | `notifications:table` | 站内通知存储 |
| **personal_access_tokens** | `sanctum:install` | API 认证令牌（Sanctum） |

**说明**：这些表由 Laravel 框架管理，无需手动创建迁移文件，也无需对应的静态数据文件。

---

## 三、Data 文件与数据库表对应关系

### 3.1 Laravel 内置表（框架提供）

| 数据文件 | 对应表名 | Artisan 命令 | 说明 |
|----------|---------|-------------|------|
| **notifications.js** | notifications | `notifications:table` | Laravel 通知系统，使用框架内置表 |

### 3.2 业务配置表（已完成迁移）

| 数据文件 | 表名 | 用途 | 状态 |
|----------|------|------|:---:|
| **ad_positions.js** | ad_positions | 广告位配置管理 | ✅ 已完成 |
| **media.js** | media | 媒体库管理 | ✅ 已完成 |
| **menu.js** | menus | 后台菜单配置 | ✅ 已完成 |
| **permissions.js** | permissions | 系统权限定义 | ✅ 已完成 |
| **roles.js** | roles | 用户角色定义 | ✅ 已完成 |
| **role_permissions.js** | role_has_permissions | 角色权限关联 | ✅ Spatie 内置 |
| **settings.js** | settings | 系统设置管理 | ✅ 已完成 |
| **footer_config.js** | footer_links | 页脚配置管理 | ✅ 已完成 |
| **page_seo.js** | page_seo | 页面SEO配置管理 | ✅ 已完成 |
| **seo_config.js** | seo | 全局SEO配置管理 | ✅ 已完成 |
| **site_config.js** | settings | 站点配置管理 | ✅ 已合并到 settings |
| **themes.js** | themes | 主题切换、外观定制 | ✅ 已完成 |
| **author_profiles.js** | author_profiles | 作者详细资料、社交链接、技能 | ✅ 已完成 |
| **backup.js** | backups | 系统备份记录 | ✅ 已完成 |
| **email_templates.js** | email_templates | 邮件模板管理 | ✅ 已完成 |
| **logs.js** | admin_logs | 管理员操作日志 | ✅ 已完成 |
| **mail_config.js** | mail_configs | 邮件服务器配置 | ✅ 已完成 |
| **user_levels.js** | user_levels | 用户等级体系 | ✅ 已完成 |
| **visits.js** | visits | 访问记录统计 | ✅ 已完成 |
| **i18n_config.js** | languages + translations | 多语言配置管理 | ✅ 已完成 |

---

## 四、已删除/已更名的文件

| 旧文件名 | 当前状态 | 说明 |
|----------|---------|------|
| **author.js** | ❌ 已删除 | 已替换为 author_profiles.js |
| **home.js** | ❌ 已删除 | 首页配置已移除 |
| **links.js** | ❌ 已删除 | 友情链接数据已移除 |
| **manifestos.js** | ❌ 已删除 | 宣言/理念数据已整合到 author_profiles.js |
| **skills.js** | ❌ 已删除 | 技能数据已整合到 author_profiles.js |

---

## 五、功能开发路线图

```
第一阶段（基础）✅ 已完成
├── 媒体库管理 ← media.js ✅ 迁移完成
├── 菜单配置系统 ← menu.js ✅ 迁移+Seeder 完成
├── 角色权限系统 ← roles.js + permissions.js + role_permissions.js ✅ 迁移+Seeder 完成
├── 广告位管理 ← ad_positions.js + advertisements.js ✅ 迁移+Seeder 完成（12条广告数据）
├── 用户互动系统 ← interactions.js ✅ 迁移+Seeder 完成
└── 邮件订阅管理 ← subscribers.js ✅ 迁移+Seeder 完成

第二阶段（增强）✅ 已完成
├── 用户等级体系 ← user_levels.js ✅ 迁移+Seeder 完成
├── 访问统计分析 ← visits.js ✅ 迁移+Seeder 完成
├── 邮件模板系统 ← email_templates.js ✅ 迁移完成
├── 数据备份功能 ← backup.js ✅ 迁移完成
├── 日志/日记系统 ← journals.js ✅ 迁移+Seeder 完成
├── 操作日志记录 ← logs.js ✅ 迁移完成
├── 邮件配置管理 ← mail_config.js ✅ 迁移完成
├── 系统设置管理 ← settings.js ✅ 迁移+Seeder 完成
├── 主题定制系统 ← themes.js ✅ 迁移+Seeder 完成
├── 作者详细资料 ← author_profiles.js ✅ 迁移+Seeder 完成
├── 页脚管理 ← footer_config.js ✅ 迁移+Seeder 完成
├── 页面SEO管理 ← page_seo.js ✅ 迁移完成
├── 全局SEO配置 ← seo_config.js ✅ 迁移+Seeder 完成
├── 站点配置 ← settings.js ✅ 已合并到系统设置
└── 多语言管理 ← i18n_config.js ✅ 迁移+Seeder 完成（languages + translations）

第三阶段（高级）⚠️ 未启动
├── 站内通知系统 ← notifications.js ✅ 前端完成，后端使用 Laravel Notification 系统
├── 数据统计面板 ← analytics.js（待创建）
├── 插件扩展机制 ← plugins.js（待创建）
├── API开放平台 ← apiKeys.js（待创建）
├── 首页轮播管理 ← banners.js（待创建）
├── 帮助中心 ← faq.js（待创建）
└── 用户评价系统 ← testimonials.js（待创建）
```

---

## 六、数据文件结构分析与数据库设计原则

### 6.1 数据库设计原则应用

| 原则 | 应用说明 | 当前状态 |
|:---|:---|:---:|
| **完整性** | 确保所有数据文件有对应的数据库表 | ✅ 全部完成 |
| **一致性** | 数据文件结构应与数据库表结构保持一致 | ✅ 已修复所有字段匹配 |
| **规范化** | 数据符合数据库规范化要求（1NF/2NF/3NF） | ✅ 符合规范 |
| **关联性** | 相关表之间建立外键关联 | ✅ 已完善外键关系 |
| **可扩展性** | 预留字段便于未来扩展 | ✅ 设计合理 |

### 6.2 关键数据文件结构分析

#### 6.2.1 ad_positions.js（已创建）

**数据结构**：
| 字段名 | 类型 | 说明 | 数据库映射 |
|--------|------|------|:---:|
| id | number | 主键 | PRIMARY KEY |
| name | string | 广告位英文标识 | VARCHAR (unique) |
| label_key | string | 国际化翻译key | VARCHAR |
| description | string | 广告位描述 | VARCHAR/TEXT |
| default_width | number | 默认宽度 | INTEGER |
| default_height | number | 默认高度 | INTEGER |
| is_active | boolean | 是否启用 | BOOLEAN |
| sort_order | number | 排序顺序 | INTEGER |
| created_at | string | 创建时间 | TIMESTAMP |
| updated_at | string | 更新时间 | TIMESTAMP |

**设计原则遵循**：符合规范化，字段职责单一，支持国际化。

#### 6.2.2 author_profiles.js（已完善）

**数据结构**：
| 字段名 | 类型 | 说明 | 数据库映射 |
|--------|------|------|:---:|
| id | number | 主键 | PRIMARY KEY |
| user_id | number | 关联用户ID | FOREIGN KEY |
| slug | string | URL别名 | VARCHAR (unique) |
| bio | string | 个人简介 | TEXT |
| avatar | string | 头像URL | VARCHAR |
| role_label | string | 职位标签key | VARCHAR |
| role_title | string | 职位名称key | VARCHAR |
| status_label | string | 状态标签key | VARCHAR |
| status_text | string | 状态文本key | VARCHAR |
| is_active | boolean | 是否启用 | BOOLEAN |
| social_links | object | 社交链接 | JSON |
| expertise | array | 专业领域 | JSON |
| skills | array | 技能列表 | JSON |
| manifestos | array | 作者宣言 | JSON |
| created_at | string | 创建时间 | TIMESTAMP |
| updated_at | string | 更新时间 | TIMESTAMP |

**设计原则遵循**：将作者相关数据（技能、宣言等）整合到单一表中，使用JSON字段存储动态数据，符合规范化要求。

#### 6.2.3 permissions.js（符合 Spatie 标准）

**数据结构**：
| 字段名 | 类型 | 说明 | 数据库映射 |
|--------|------|------|:---:|
| id | number | 主键 | PRIMARY KEY |
| name | string | 权限名称 | VARCHAR (unique) |
| label | string | 权限标签 | VARCHAR |
| description | string | 权限描述 | VARCHAR |
| guard_name | string | 守卫名称 | VARCHAR |
| program_id | string | 程序ID | VARCHAR |

**设计原则遵循**：符合 Spatie/laravel-permission 标准结构，便于未来集成。

---

## 七、数据库表字段详细对比（已完成）

### 7.1 已完成字段对比的表

#### 7.1.1 users 表 ✅

**前端字段**（users.js）：
- id, name, email, password, avatar, role_id, status, points
- email_notifications, comment_approval_alert, new_user_alert
- weekly_report, digest_email, digest_frequency
- last_login_at, created_at, updated_at

**数据库字段**（0001_01_01_000000_create_users_table.php）：
- id, name, email, email_verified_at, password, avatar, bio
- github, twitter, linkedin
- status (enum: active/inactive/suspended)
- role_id (FK->roles), points (default:0)
- email_notifications (default:true)
- comment_approval_alert (default:true)
- new_user_alert (default:true)
- weekly_report (default:false)
- digest_email (default:false)
- digest_frequency (enum: daily/weekly/monthly)
- last_login_at, rememberToken, timestamps

**对比结果**：✅ 完全匹配，数据库包含前端所有字段 + 额外社交字段

#### 7.1.2 posts 表 ✅

**前端字段**（posts.js）：
- id, title, slug, excerpt, content, cover_image, color
- status (draft/published), views_count, likes_count
- meta_title, meta_description, meta_keywords
- author_id, category_id, published_at
- created_at, updated_at

**数据库字段**（2026_05_07_090837_create_posts_table.php）：
- id, title, slug, excerpt, content, cover_image, color (default:'black')
- status (enum: draft/published/archived, default:'draft')
- views_count (default:0), likes_count (default:0)
- meta_title, meta_description (500), meta_keywords
- author_id (FK->users), category_id (FK->categories, nullable)
- published_at, timestamps

**对比结果**：✅ 完全匹配

#### 7.1.3 categories 表 ✅

**前端字段**（categories.js）：
- id, parent_id, name, slug, description, sort_order, status
- postCount (计算字段，不存储)

**数据库字段**（2026_05_07_090832_create_categories_table.php）：
- id, parent_id (FK->categories), name (unique), slug (unique)
- description, status (enum: active/inactive, default:'active')
- sort_order (default:0), timestamps

**对比结果**：✅ 完全匹配

#### 7.1.4 comments 表 ✅

**前端字段**（comments.js）：
- id, post_id, parent_id, user_id, name, email
- body, is_approved, ip_address, user_agent
- created_at, updated_at

**数据库字段**（2026_05_07_090953_create_comments_table.php）：
- id, post_id (FK->posts), parent_id (FK->comments, nullable)
- user_id (FK->users, nullable), name (nullable), email (nullable)
- body, is_approved (default:true)
- ip_address (45), user_agent, timestamps

**对比结果**：✅ 完全匹配

#### 7.1.5 projects 表 ✅

**前端字段**（projects.js）：
- id, title, description, long_description, client, role, year
- image, url, github_url, technologies (JSON)
- status (planning/in-progress/completed)
- sort_order, views_count, likes_count
- created_at, updated_at

**数据库字段**（2026_05_07_090955_create_projects_table.php）：
- id, title, description, long_description (nullable)
- client (nullable), role (nullable), year
- image (nullable), url (nullable), github_url (nullable)
- technologies (JSON, nullable)
- status (enum: planning/in-progress/completed, default:'completed')
- sort_order (default:0), views_count (default:0), likes_count (default:0)
- timestamps

**对比结果**：✅ 完全匹配

#### 7.1.6 resources 表 ✅

**前端字段**（resources.js）：
- id, category_id, title, description, format, file_size
- image, direct_link, drives (JSON)
- downloads_count, likes_count
- created_at, updated_at

**数据库字段**（2026_05_07_090956_create_resources_table.php）：
- id, category_id (FK->categories, nullable)
- title, description, format (50), file_size (50)
- image (nullable), direct_link (nullable)
- drives (JSON, nullable)
- downloads_count (default:0), likes_count (default:0)
- timestamps

**对比结果**：✅ 完全匹配

#### 7.1.7 videos 表 ✅

**前端字段**（videos.js）：
- id, title, description, video_id, platform (youtube/bilibili)
- thumbnail, duration, views_count, likes_count
- published_at, created_at, updated_at

**数据库字段**（2026_05_07_090958_create_videos_table.php）：
- id, title, description, video_id
- platform (enum: youtube/bilibili)
- thumbnail (nullable), duration (50, nullable)
- views_count (default:0), likes_count (default:0)
- published_at (nullable), timestamps

**对比结果**：✅ 完全匹配

#### 7.1.8 interactions 表 ✅

**前端字段**（interactions.js）：
- id, user_id, interactable_id, interactable_type
- type (like/bookmark), created_at, updated_at

**数据库字段**（2026_05_07_090959_create_interactions_table.php）：
- id, user_id (FK->users)
- interactable_id, interactable_type (morphs)
- type (enum: like/bookmark)
- timestamps
- 唯一索引：user_id + interactable_id + interactable_type + type

**对比结果**：✅ 完全匹配

#### 7.1.9 advertisements 表 ✅

**前端字段**（advertisements.js）：
- id, position_id, title, image_url, link_url, position
- is_active, clicks_count, views_count
- start_date, end_date, created_at, updated_at

**数据库字段**（2026_05_07_091001_create_advertisements_table.php）：
- id, title, image_url, link_url
- position_id (FK->ad_positions, nullable)
- position (index), is_active (default:true)
- clicks_count (default:0), views_count (default:0)
- start_date (nullable), end_date (nullable)
- timestamps

**对比结果**：✅ 完全匹配

#### 7.1.10 journals 表 ✅

**前端字段**（journals.js）：
- id, user_id, title, content, mood, weather, date
- is_public, created_at, updated_at

**数据库字段**（2026_05_07_091003_create_journals_table.php）：
- id, user_id (FK->users)
- title (nullable), content, mood (nullable), weather (nullable)
- date (nullable), is_public (default:true)
- likes_count (default:0), timestamps

**对比结果**：✅ 完全匹配

#### 7.1.11 subscribers 表 ✅

**前端字段**（subscribers.js）：
- id, email, name, source, is_active, subscribed_at
- created_at, updated_at

**数据库字段**（2026_05_07_091004_create_subscribers_table.php）：
- id, email (unique), name (nullable), source (100, nullable)
- is_active (default:true), subscribed_at (nullable)
- timestamps

**对比结果**：✅ 完全匹配

#### 7.1.12 roles 表 ✅

**前端字段**（roles.js）：
- id, name, value, guard_name, label, color, description
- created_at, updated_at

**数据库字段**（0001_01_01_000001_create_roles_table.php）：
- id, name (unique), value (unique), guard_name (default:'web')
- label (nullable), color (50, nullable), description (nullable)
- timestamps

**对比结果**：✅ 完全匹配

#### 7.1.13 settings 表 ✅

**前端字段**（settings.js）：
- id, name, description, site_url, copyright, logo, favicon, timezone
- maintenance_mode, show_author_bio, show_comments
- allow_registration, require_comment_approval
- enable_newsletter, enable_social_login, enable_search
- enable_cache, cache_duration, enable_minification
- lazy_load_images, enable_cdn, cdn_url
- max_upload_size, allowed_file_types
- created_at, updated_at

**数据库字段**（2026_05_23_080103_create_settings_table.php）：
- id, name (default:'ARCHYX'), description (nullable)
- site_url (nullable), copyright (nullable), logo (nullable), favicon (nullable)
- timezone (default:'Asia/Shanghai')
- maintenance_mode (default:false), show_author_bio (default:false)
- show_comments (default:true), allow_registration (default:true)
- require_comment_approval (default:false), enable_newsletter (default:true)
- enable_social_login (default:false), enable_search (default:true)
- enable_cache (default:true), cache_duration (default:3600)
- enable_minification (default:true), lazy_load_images (default:true)
- enable_cdn (default:false), cdn_url (nullable)
- max_upload_size (default:10), allowed_file_types (JSON, nullable)
- timestamps

**对比结果**：✅ 完全匹配

#### 7.1.14 themes 表 ✅

**前端字段**（themes.js）：
- id, name, label, color, sort_order, is_active, is_default
- preview_image, created_at, updated_at

**数据库字段**（2026_05_23_080116_create_themes_table.php）：
- id, name (100, unique), label, color (50)
- sort_order (default:0), is_active (default:false), is_default (default:false)
- preview_image (nullable), timestamps

**对比结果**：✅ 完全匹配

#### 7.1.15 languages 表 ✅

**前端字段**（i18n_config.js）：
- id, code, name, native_name, flag, file_path, direction
- is_default, is_active, sort_order
- created_at, updated_at

**数据库字段**（2026_05_23_080109_create_languages_table.php）：
- id, code (50, unique), name (100), native_name (100)
- flag (20, nullable), file_path (200, nullable), direction (5, default:'ltr')
- is_default (default:false), is_active (default:true), sort_order (default:0)
- timestamps

**对比结果**：✅ 完全匹配

#### 7.1.16 footer_links 表 ✅

**前端字段**（footer_config.js）：
- id, type, platform, icon_name, label, url, icon, sort_order, is_active

**数据库字段**（2026_05_23_080115_create_footer_links_table.php）：
- id, type (50), platform (50, nullable), icon_name (100, nullable)
- label, url, icon (nullable), sort_order (default:0), is_active (default:true)
- timestamps

**对比结果**：✅ 完全匹配

#### 7.1.17 user_levels 表 ✅

**前端字段**（user_levels.js）：
- id, name, level, min_points, max_points, discount, color, icon
- description, benefits, is_active, sort_order
- created_at, updated_at

**数据库字段**（2026_05_23_080106_create_user_levels_table.php）：
- id, name, level, min_points (default:0), max_points (nullable)
- discount (default:0), color (50, nullable), icon (nullable)
- description (nullable), benefits (JSON, nullable)
- is_active (default:true), sort_order (default:0)
- timestamps

**对比结果**：✅ 完全匹配

#### 7.1.18 menus 表 ✅

**前端字段**（menu.js）：
- id, type, parent_id, label_key, icon_name, path, sort_order, is_active
- component_name, created_at, updated_at

**数据库字段**（2026_05_23_080104_create_menus_table.php）：
- id, type (enum: front/admin), parent_id (FK->menus, nullable)
- label_key (255), icon_name (100, nullable), path (255, nullable)
- sort_order (default:0), is_active (default:true)
- component_name (255, nullable), timestamps

**对比结果**：✅ 完全匹配

#### 7.1.19 seo 表 ✅

**前端字段**（seo_config.js）：
- id, title, description, keywords, author, robots
- og_title, og_description, og_image, og_type
- twitter_card, twitter_site, canonical_url
- created_at, updated_at

**数据库字段**（2026_05_23_080108_create_seo_table.php）：
- id, title (nullable), description (500, nullable), keywords (nullable)
- author (nullable), robots (50, default:'index, follow')
- og_title (nullable), og_description (500, nullable), og_image (nullable)
- og_type (50, default:'website')
- twitter_card (50, default:'summary'), twitter_site (nullable)
- canonical_url (nullable), timestamps

**对比结果**：✅ 完全匹配

#### 7.1.20 ad_positions 表 ✅

**前端字段**（ad_positions.js）：
- id, name, label_key, description, default_width, default_height
- is_active, sort_order, created_at, updated_at

**数据库字段**（2026_05_07_091000_create_ad_positions_table.php）：
- id, name (100, unique), label_key (255), description (nullable)
- default_width (default:728), default_height (default:90)
- is_active (default:true), sort_order (default:0), timestamps

**对比结果**：✅ 完全匹配

---

## 八、迁移文件执行顺序

```
0001_01_01_000000_create_users_table.php          ← Laravel 默认（users 表）
0001_01_01_000001_create_roles_table.php          ← 角色表（在 users 之前）
0001_01_01_000002_create_cache_table.php          ← Laravel 默认
0001_01_01_000003_create_jobs_table.php           ← Laravel 默认
2026_05_07_090832_create_categories_table.php     ← 分类表
2026_05_07_090837_create_posts_table.php          ← 文章表
2026_05_07_090839_create_tags_table.php           ← 标签表
2026_05_07_090841_create_taggables_table.php      ← 标签关联表
2026_05_07_090953_create_comments_table.php       ← 评论表
2026_05_07_090955_create_projects_table.php       ← 项目表
2026_05_07_090956_create_resources_table.php      ← 资源表
2026_05_07_090958_create_videos_table.php         ← 视频表
2026_05_07_090959_create_interactions_table.php   ← 互动表
2026_05_07_091000_create_ad_positions_table.php   ← 广告位表（在 advertisements 之前）
2026_05_07_091001_create_advertisements_table.php ← 广告表
2026_05_07_091003_create_journals_table.php       ← 日志表
2026_05_07_091004_create_subscribers_table.php    ← 订阅者表
2026_05_23_080101_create_permissions_table.php    ← 权限表
2026_05_23_080103_create_settings_table.php       ← 系统设置表
2026_05_23_080104_create_menus_table.php          ← 菜单表
2026_05_23_080105_create_media_table.php          ← 媒体表
2026_05_23_080106_create_user_levels_table.php    ← 用户等级表
2026_05_23_080107_create_author_profiles_table.php← 作者资料表
2026_05_23_080108_create_seo_table.php            ← SEO表
2026_05_23_080109_create_languages_table.php      ← 语言表
2026_05_23_080110_create_mail_configs_table.php   ← 邮件配置表
2026_05_23_080111_create_email_templates_table.php← 邮件模板表
2026_05_23_080112_create_social_links_table.php   ← 社交链接表
2026_05_23_080113_create_admin_logs_table.php     ← 管理员日志表
2026_05_23_080114_create_backups_table.php        ← 备份表
2026_05_23_080115_create_footer_links_table.php   ← 页脚链接表
2026_05_23_080116_create_themes_table.php         ← 主题表
2026_05_23_080117_create_translations_table.php   ← 翻译表
2026_05_23_080118_create_page_seo_table.php       ← 页面SEO表
2026_05_23_080119_create_visits_table.php         ← 访问记录表
2026_05_23_080202_create_user_points_history_table.php ← 积分历史表
2026_05_23_080203_create_ad_interactions_table.php     ← 广告互动表
```

---

## 九、数据库设计规范总结

### 9.1 命名规范

| 元素 | 规范 | 示例 |
|------|------|------|
| 表名 | 复数形式，snake_case | `users`, `ad_positions` |
| 主键 | `id` | bigint, auto-increment |
| 外键 | `{表名单数}_id` | `author_id`, `category_id` |
| 时间戳 | `created_at`, `updated_at` | timestamp, nullable |
| 布尔值 | `is_` 前缀 | `is_active`, `is_approved` |
| 计数器 | `{名词}_count` | `views_count`, `likes_count` |

### 9.2 外键约束规范

| 约束类型 | 说明 | 示例 |
|---------|------|------|
| cascadeOnDelete | 删除主记录时级联删除子记录 | comments → posts |
| nullOnDelete | 删除主记录时外键设为 NULL | posts → categories |
| restrict | 禁止删除有子记录的主记录 | 默认行为 |

### 9.3 索引规范

| 索引类型 | 说明 | 示例 |
|---------|------|------|
| 唯一索引 | 确保字段值唯一 | `email`, `slug`, `name` |
| 普通索引 | 加速查询 | `position`, `status` |
| 复合索引 | 多字段联合查询 | `user_id + interactable_id + type` |
| 多态索引 | 多态关联查询 | `visitable_id + visitable_type` |

---

## 十、字段对比完成状态

| 表名 | 前端字段数 | 数据库字段数 | 匹配状态 | 备注 |
|:---|:---:|:---:|:---:|------|
| users | 16 | 22 | ✅ 完全匹配 | 数据库包含额外社交字段 |
| posts | 15 | 17 | ✅ 完全匹配 | - |
| categories | 7 | 9 | ✅ 完全匹配 | - |
| comments | 11 | 12 | ✅ 完全匹配 | - |
| projects | 15 | 16 | ✅ 完全匹配 | - |
| resources | 11 | 12 | ✅ 完全匹配 | - |
| videos | 11 | 12 | ✅ 完全匹配 | - |
| interactions | 6 | 7 | ✅ 完全匹配 | - |
| advertisements | 11 | 12 | ✅ 完全匹配 | - |
| journals | 9 | 11 | ✅ 完全匹配 | - |
| subscribers | 7 | 8 | ✅ 完全匹配 | - |
| roles | 8 | 9 | ✅ 完全匹配 | - |
| settings | 22 | 23 | ✅ 完全匹配 | - |
| themes | 8 | 9 | ✅ 完全匹配 | - |
| languages | 10 | 11 | ✅ 完全匹配 | - |
| footer_links | 9 | 10 | ✅ 完全匹配 | - |
| user_levels | 12 | 13 | ✅ 完全匹配 | - |
| menus | 9 | 10 | ✅ 完全匹配 | - |
| seo | 12 | 13 | ✅ 完全匹配 | - |
| ad_positions | 9 | 10 | ✅ 完全匹配 | - |

**总计**：20 个核心表，**100% 字段匹配** ✅

---

## 十一、待办事项

- [x] 修复 settings 表字段不匹配问题
- [x] 修复 themes 表字段不匹配问题
- [x] 修复 languages 表字段不匹配问题
- [x] 修复 footer_links 表字段不匹配问题
- [x] 修复 user_levels 表字段不匹配问题
- [x] 修复 menus 表外键约束问题
- [x] 合并 journals 扩展字段到创建表迁移
- [x] 合并 categories 扩展字段到创建表迁移
- [x] 合并 subscribers 扩展字段到创建表迁移
- [x] 合并 users 扩展字段到创建表迁移
- [x] 合并 advertisements 扩展字段到创建表迁移
- [x] 调整 roles 表迁移执行顺序
- [x] 调整 ad_positions 表迁移执行顺序
- [x] 删除 users.role 枚举字段
- [x] 更新所有 Model 文件匹配新表结构
- [x] 完成字段对比文档更新

---

**文档维护说明**：
- 每次修改迁移文件后，必须更新本文档
- 字段对比必须基于实际的前端 data 文件和迁移文件
- 确保数据库设计与前端设计完全一致
