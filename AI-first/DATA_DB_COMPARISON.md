# Data 文件与数据库表结构对比分析

> **重要说明**：本文档基于实际扫描 `resources/js/data/` 目录和 `database/migrations/` 目录生成，确保内容与实际项目状态一致。
> **最后更新**：2026-05-23

---

## 一、整体对比概览

### 1.1 数据文件清单（34个）

| 序号 | 文件名 | 对应表名 | 状态 | 说明 |
|:---:|--------|---------|:---:|------|
| 1 | ad_positions.js | ad_positions | ⚠️ 有数据文件，缺迁移 | 广告位配置数据 |
| 2 | advertisements.js | advertisements | ✅ 有迁移 | 广告内容数据 |
| 3 | author_profiles.js | author_profiles | ❌ 需创建迁移 | 作者个人资料、社交链接、技能等 |
| 4 | backup.js | backups | ❌ 需创建迁移 | 系统备份记录 |
| 5 | categories.js | categories | ✅ 有迁移 | 文章分类数据 |
| 6 | comments.js | comments | ✅ 有迁移 | 文章评论数据 |
| 7 | email_templates.js | email_templates | ❌ 需创建迁移 | 邮件模板数据 |
| 8 | footer_config.js | footer_links | ❌ 需创建迁移 | 页脚社交链接、导航链接、品牌信息配置 |
| 9 | interactions.js | interactions | ✅ 有迁移 | 用户互动数据（点赞/收藏） |
| 10 | journals.js | journals | ✅ 有迁移 | 日志/日记数据 |
| 11 | logs.js | admin_logs | ❌ 需创建迁移 | 管理员操作日志 |
| 12 | mail_config.js | mail_settings | ❌ 需创建迁移 | 邮件服务器配置 |
| 13 | media.js | media | ❌ 需创建迁移 | 媒体文件管理数据 |
| 14 | menu.js | menus | ❌ 需创建迁移 | 后台菜单配置 |
| 15 | notifications.js | notifications | ✅ Laravel 内置 | 站内通知数据 |
| 16 | page_seo.js | page_seo | ❌ 需创建迁移 | 各页面SEO配置 |
| 17 | permissions.js | permissions | ❌ 需创建迁移 | 系统权限定义（含 guard_name 字段） |
| 18 | posts.js | posts | ✅ 有迁移 | 文章内容数据 |
| 19 | projects.js | projects | ✅ 有迁移 | 项目作品集数据 |
| 20 | resources.js | resources | ✅ 有迁移 | 资源下载数据 |
| 21 | role_permissions.js | role_permissions | ❌ 需创建迁移 | 角色与权限的关联关系 |
| 22 | roles.js | roles | ❌ 需创建迁移 | 用户角色定义 |
| 23 | seo_config.js | seo | ❌ 需创建迁移 | 网站全局SEO默认配置 |
| 24 | settings.js | settings | ❌ 需创建迁移 | 网站系统配置（新增） |
| 25 | site_config.js | site | ❌ 需创建迁移 | 站点基本信息、品牌配置 |
| 26 | subscribers.js | subscribers | ✅ 有迁移 | 邮件订阅者数据 |
| 27 | tags.js | tags | ✅ 有迁移 | 标签数据 |
| 28 | taggables.js | taggables | ✅ 有迁移 | 标签多态关联中间表 |
| 29 | themes.js | themes | ❌ 需创建迁移 | 主题配置数据 |
| 30 | user_levels.js | user_levels | ❌ 需创建迁移 | 用户等级数据 |
| 31 | users.js | users | ✅ Laravel 内置 | 用户账户数据 |
| 32 | videos.js | videos | ✅ 有迁移 | 视频内容数据 |
| 33 | visits.js | visits | ❌ 需创建迁移 | 访问记录统计 |
| 34 | i18n_config.js | i18n | ❌ 需创建迁移 | 多语言配置数据 |

### 1.2 数据库表清单（29个）

| 序号 | 表名 | 对应数据文件 | 状态 | 说明 |
|:---:|------|-------------|:---:|------|
| **框架内置表** |
| 1 | users | users.js | ✅ Laravel 内置 | 用户账户信息表 |
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
| 11 | categories | categories.js | ✅ 存在对应文件 | 文章分类表 |
| 12 | posts | posts.js | ✅ 存在对应文件 | 文章内容表 |
| 13 | tags | tags.js | ✅ 存在对应文件 | 标签表 |
| 14 | taggables | taggables.js | ✅ 存在对应文件 | 标签多态关联中间表 |
| 15 | comments | comments.js | ✅ 存在对应文件 | 评论表 |
| 16 | projects | projects.js | ✅ 存在对应文件 | 项目表 |
| 17 | resources | resources.js | ✅ 存在对应文件 | 资源下载表 |
| 18 | videos | videos.js | ✅ 存在对应文件 | 视频表 |
| 19 | interactions | interactions.js | ✅ 存在对应文件 | 用户互动表（点赞/收藏） |
| 20 | advertisements | advertisements.js | ✅ 存在对应文件 | 广告表 |
| 21 | journals | journals.js | ✅ 存在对应文件 | 日志/日记表 |
| 22 | subscribers | subscribers.js | ✅ 存在对应文件 | 订阅者表 |

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

### 3.2 业务配置表（需创建迁移）

| 数据文件 | 建议表名 | 用途 | 优先级 |
|----------|---------|------|:---:|
| **ad_positions.js** | ad_positions | 广告位配置管理 | 高 |
| **media.js** | media | 媒体库管理 | 高 |
| **menu.js** | menus | 后台菜单配置 | 高 |
| **permissions.js** | permissions | 系统权限定义 | 高 |
| **roles.js** | roles | 用户角色定义 | 高 |
| **role_permissions.js** | role_permissions | 角色权限关联 | 高 |
| **settings.js** | settings | 系统设置管理 | 高 |
| **footer_config.js** | footer_links | 页脚配置管理 | 中 |
| **page_seo.js** | page_seo | 页面SEO配置管理 | 中 |
| **seo_config.js** | seo | 全局SEO配置管理 | 中 |
| **site_config.js** | site | 站点配置管理 | 中 |
| **themes.js** | themes | 主题切换、外观定制 | 中 |
| **author_profiles.js** | author_profiles | 作者详细资料、社交链接、技能 | 中 |
| **backup.js** | backups | 系统备份记录 | 中 |
| **email_templates.js** | email_templates | 邮件模板管理 | 中 |
| **logs.js** | admin_logs | 管理员操作日志 | 中 |
| **mail_config.js** | mail_settings | 邮件服务器配置 | 中 |
| **user_levels.js** | user_levels | 用户等级体系 | 中 |
| **visits.js** | visits | 访问记录统计 | 中 |
| **i18n_config.js** | i18n | 多语言配置管理 | 中 |

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
├── 媒体库管理 ← media.js（⚠️ 缺迁移）
├── 菜单配置系统 ← menu.js（⚠️ 缺迁移）
├── 角色权限系统 ← roles.js + permissions.js + role_permissions.js（⚠️ 缺迁移）
├── 广告位管理 ← ad_positions.js + advertisements.js（⚠️ ad_positions缺迁移）✅ ad_positions.js已创建
├── 用户互动系统 ← interactions.js ✅
└── 邮件订阅管理 ← subscribers.js ✅

第二阶段（增强）⚠️ 部分完成
├── 用户等级体系 ← user_levels.js（⚠️ 缺迁移）
├── 访问统计分析 ← visits.js（⚠️ 缺迁移）
├── 邮件模板系统 ← email_templates.js（⚠️ 缺迁移）
├── 数据备份功能 ← backup.js（⚠️ 缺迁移）
├── 日志/日记系统 ← journals.js ✅
├── 操作日志记录 ← logs.js（⚠️ 缺迁移）
├── 邮件配置管理 ← mail_config.js（⚠️ 缺迁移）
├── 系统设置管理 ← site_config.js + seo_config.js（⚠️ 缺迁移）
├── 主题定制系统 ← themes.js（⚠️ 缺迁移）
├── 作者详细资料 ← author_profiles.js（⚠️ 缺迁移）✅ 数据结构已完善
├── 页脚管理 ← footer_config.js（⚠️ 缺迁移）
├── 页面SEO管理 ← page_seo.js（⚠️ 缺迁移）
├── 全局SEO配置 ← seo_config.js（⚠️ 缺迁移）
├── 站点配置 ← site_config.js（⚠️ 缺迁移）
└── 多语言管理 ← i18n_config.js（⚠️ 缺迁移）

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
| **完整性** | 确保所有数据文件有对应的数据库表 | ⚠️ 19个文件缺迁移 |
| **一致性** | 数据文件结构应与数据库表结构保持一致 | ✅ 基本一致 |
| **规范化** | 数据符合数据库规范化要求（1NF/2NF/3NF） | ✅ 符合规范 |
| **关联性** | 相关表之间建立外键关联 | ⚠️ 部分表缺少外键 |
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

## 七、数据库表字段详细分析与补充建议

### 7.1 已有迁移表需要补充的字段

#### 7.1.1 users 表

**当前字段**：
- id, name, email, email_verified_at, password, avatar, bio, github, twitter, linkedin
- role (enum: user/admin), status (enum: active/inactive/suspended)
- last_login_at, rememberToken, timestamps

**需要补充的字段**：
| 字段名 | 类型 | 说明 | 优先级 |
|--------|------|------|:---:|
| role_id | foreignId | 关联 roles 表（替代当前 role 枚举） | 高 |
| points | unsignedBigInteger | 用户积分 | 中 |

**建议迁移**：
```php
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
    $table->unsignedBigInteger('points')->default(0);
});
```

#### 7.1.2 posts 表

**当前字段**：
- id, title, slug, excerpt, content, cover_image, color
- status (enum: draft/published/archived)
- views_count, likes_count
- meta_title, meta_description, meta_keywords
- author_id, category_id, published_at, timestamps

**状态**：字段完整，无需补充

#### 7.1.3 categories 表

**当前字段**：
- id, parent_id, name, slug, description, sort_order, timestamps

**需要补充的字段**：
| 字段名 | 类型 | 说明 | 优先级 |
|--------|------|------|:---:|
| status | enum | 分类状态（active/inactive） | 中 |

**建议迁移**：
```php
Schema::table('categories', function (Blueprint $table) {
    $table->enum('status', ['active', 'inactive'])->default('active');
});
```

#### 7.1.4 projects 表

**当前字段**：
- id, title, description, long_description, client, role, year
- image, url, github_url, technologies (JSON)
- status (enum: planning/in-progress/completed)
- sort_order, views_count, likes_count, timestamps

**状态**：字段完整，无需补充

#### 7.1.5 resources 表

**当前字段**：
- id, category_id, title, description, format, file_size
- image, direct_link, drives (JSON)
- downloads_count, likes_count, timestamps

**状态**：字段完整，无需补充

#### 7.1.6 videos 表

**当前字段**：
- id, title, description, video_id, platform (enum: youtube/bilibili)
- thumbnail, duration, views_count, likes_count
- published_at, timestamps

**状态**：字段完整，无需补充

#### 7.1.7 comments 表

**当前字段**：
- id, post_id, parent_id, user_id, name, email
- body, is_approved, ip_address, user_agent, timestamps

**状态**：字段完整，无需补充

#### 7.1.8 interactions 表

**当前字段**：
- id, user_id, interactable_id, interactable_type (多态)
- type (enum: like/bookmark), timestamps
- 唯一索引：user_id + interactable_id + interactable_type + type

**状态**：字段完整，无需补充

#### 7.1.9 advertisements 表

**当前字段**：
- id, title, image_url, link_url, position (index)
- is_active, clicks_count, views_count
- start_date, end_date, timestamps

**需要补充的字段**：
| 字段名 | 类型 | 说明 | 优先级 |
|--------|------|------|:---:|
| position_id | foreignId | 关联 ad_positions 表 | 高 |

**建议迁移**：
```php
Schema::table('advertisements', function (Blueprint $table) {
    $table->foreignId('position_id')->nullable()->constrained('ad_positions')->nullOnDelete();
});
```

#### 7.1.10 journals 表

**当前字段**：
- id, user_id, content, mood, weather
- is_public, likes_count, timestamps

**需要补充的字段**：
| 字段名 | 类型 | 说明 | 优先级 |
|--------|------|------|:---:|
| title | string | 日志标题 | 高 |
| date | date | 日志日期 | 高 |

**建议迁移**：
```php
Schema::table('journals', function (Blueprint $table) {
    $table->string('title')->nullable();
    $table->date('date')->nullable();
});
```

#### 7.1.11 subscribers 表

**当前字段**：
- id, email (unique), is_active, timestamps

**需要补充的字段**：
| 字段名 | 类型 | 说明 | 优先级 |
|--------|------|------|:---:|
| name | string | 订阅者姓名 | 中 |
| source | string | 订阅来源（website/newsletter/social） | 中 |
| subscribed_at | timestamp | 订阅时间 | 中 |

**建议迁移**：
```php
Schema::table('subscribers', function (Blueprint $table) {
    $table->string('name')->nullable();
    $table->string('source')->nullable();
    $table->timestamp('subscribed_at')->nullable();
});
```

---

### 7.2 需要创建迁移的表结构建议

#### 7.2.1 ad_positions 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| name | string (unique) | 广告位名称（英文标识） |
| label_key | string | 国际化翻译key |
| description | string | 广告位描述 |
| default_width | integer | 默认宽度 |
| default_height | integer | 默认高度 |
| is_active | boolean | 是否启用 |
| sort_order | integer | 排序顺序 |
| timestamps | timestamps | 时间戳 |

#### 7.2.2 menus 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| type | enum (front/admin) | 菜单类型 |
| parent_id | foreignId (nullable) | 父级菜单ID |
| label_key | string | 国际化翻译key |
| icon_name | string (nullable) | 图标名称 |
| path | string (nullable) | 路由路径 |
| sort_order | integer | 排序顺序 |
| is_active | boolean | 是否启用 |
| component_name | string (nullable) | 关联组件名 |
| timestamps | timestamps | 时间戳 |

#### 7.2.3 roles 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| value | string (unique) | 角色值（英文标识） |
| label | string | 角色标签 |
| name | string | 角色名称 |
| color | string | 角色颜色 |
| description | string | 角色描述 |
| timestamps | timestamps | 时间戳 |

#### 7.2.4 permissions 表（兼容 Spatie）

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| name | string (unique) | 权限名称 |
| guard_name | string | 守卫名称（默认 'web'） |
| label | string | 权限标签 |
| description | string | 权限描述 |
| program_id | string | 程序ID |
| timestamps | timestamps | 时间戳 |

#### 7.2.5 role_permissions 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| role_id | foreignId | 关联 roles 表 |
| permission_id | foreignId | 关联 permissions 表 |
| 复合主键 | (role_id, permission_id) | 联合主键 |
| timestamps | timestamps | 时间戳 |

#### 7.2.6 settings 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| key | string (unique) | 设置键名 |
| value | json | 设置值（JSON格式） |
| type | enum | 值类型（string/number/boolean/array/object） |
| group | string | 设置分组 |
| label | string | 显示名称 |
| description | string (nullable) | 设置说明 |
| is_public | boolean | 是否公开 |
| sort_order | integer | 排序顺序 |
| timestamps | timestamps | 时间戳 |

#### 7.2.7 media 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| user_id | foreignId | 上传用户ID |
| filename | string | 文件名 |
| original_name | string | 原始文件名 |
| mime_type | string | MIME类型 |
| file_size | unsignedBigInteger | 文件大小 |
| url | string | 文件URL |
| thumbnail_url | string (nullable) | 缩略图URL |
| alt_text | string (nullable) | 替代文本 |
| timestamps | timestamps | 时间戳 |

#### 7.2.8 admin_logs 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| user_id | foreignId (nullable) | 操作用户ID |
| action | string | 操作类型 |
| description | text | 操作描述 |
| ip_address | string (45) | IP地址 |
| user_agent | string (nullable) | 浏览器信息 |
| created_at | timestamp | 操作时间 |

#### 7.2.9 visits 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| visitable_id | unsignedBigInteger | 访问对象ID（多态） |
| visitable_type | string | 访问对象类型 |
| ip_address | string (45) | IP地址 |
| user_agent | string (nullable) | 浏览器信息 |
| referrer | string (nullable) | 来源URL |
| created_at | timestamp | 访问时间 |
| 索引 | (visitable_id, visitable_type) | 多态索引 |

#### 7.2.10 user_levels 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| name | string | 等级名称 |
| min_points | integer | 最低积分要求 |
| max_points | integer (nullable) | 最高积分 |
| color | string | 等级颜色 |
| icon | string (nullable) | 等级图标 |
| description | string (nullable) | 等级描述 |
| timestamps | timestamps | 时间戳 |

#### 7.2.11 footer_links 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| type | enum (social_link/nav_link/brand_info) | 配置类型 |
| label | string | 标签 |
| url | string (nullable) | 链接URL |
| icon | string (nullable) | 图标名称 |
| sort_order | integer | 排序顺序 |
| is_active | boolean | 是否启用 |
| timestamps | timestamps | 时间戳 |

#### 7.2.12 page_seo 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| page_key | string (unique) | 页面标识 |
| title | string | SEO标题 |
| description | string (500) | SEO描述 |
| keywords | string (nullable) | SEO关键词 |
| og_image | string (nullable) | OpenGraph图片 |
| timestamps | timestamps | 时间戳 |

#### 7.2.13 seo 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| key | string (unique) | 配置键 |
| value | json | 配置值 |
| timestamps | timestamps | 时间戳 |

#### 7.2.14 site 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| key | string (unique) | 配置键 |
| value | json | 配置值 |
| timestamps | timestamps | 时间戳 |

#### 7.2.15 themes 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| name | string (unique) | 主题名称 |
| label | string | 主题标签 |
| preview_image | string (nullable) | 预览图片 |
| is_active | boolean | 是否启用 |
| config | json (nullable) | 主题配置 |
| timestamps | timestamps | 时间戳 |

#### 7.2.16 author_profiles 表（完整结构）

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| user_id | foreignId | 关联用户ID |
| slug | string (unique) | URL友好别名 |
| bio | text | 个人简介 |
| avatar | string (nullable) | 头像URL |
| role_label | string | 职位标签（国际化key） |
| role_title | string | 职位名称（国际化key） |
| status_label | string | 状态标签（国际化key） |
| status_text | string | 状态文本（国际化key） |
| is_active | boolean | 是否启用 |
| social_links | json | 社交链接 |
| expertise | json | 专业领域 |
| skills | json | 技能列表 |
| manifestos | json | 作者宣言 |
| timestamps | timestamps | 时间戳 |

#### 7.2.17 backups 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| filename | string | 备份文件名 |
| size | unsignedBigInteger | 备份大小 |
| status | enum (pending/running/completed/failed) | 备份状态 |
| type | enum (full/database/files) | 备份类型 |
| started_at | timestamp | 开始时间 |
| completed_at | timestamp (nullable) | 完成时间 |
| error_message | text (nullable) | 错误信息 |
| timestamps | timestamps | 时间戳 |

#### 7.2.18 email_templates 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| name | string (unique) | 模板名称 |
| subject | string | 邮件主题 |
| content | longText | 邮件内容（Blade模板） |
| variables | json (nullable) | 可用变量 |
| is_active | boolean | 是否启用 |
| timestamps | timestamps | 时间戳 |

#### 7.2.19 mail_settings 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| mailer | string | 邮件驱动 |
| host | string | SMTP主机 |
| port | integer | SMTP端口 |
| username | string (nullable) | 用户名 |
| password | string (nullable) | 密码 |
| encryption | string (nullable) | 加密方式 |
| from_address | string | 发件人地址 |
| from_name | string | 发件人名称 |
| is_active | boolean | 是否启用 |
| timestamps | timestamps | 时间戳 |

#### 7.2.20 i18n 表

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | id | 主键 |
| group | string | 翻译分组 |
| key | string | 翻译键 |
| text | json | 多语言文本（JSON格式） |
| description | string (nullable) | 描述 |
| is_active | boolean | 是否启用 |
| sort_order | integer | 排序顺序 |
| timestamps | timestamps | 时间戳 |
| 复合唯一索引 | (group, key) | 分组+键名唯一索引 |

---

## 八、下一步建议

### 8.1 高优先级：创建缺失的数据库迁移

以下数据文件已有前端实现，但缺少数据库迁移，建议优先创建：

1. **ad_positions** - 广告位配置
2. **media** - 媒体库管理
3. **menu** - 菜单配置
4. **permissions** - 权限定义（兼容 Spatie）
5. **roles** - 角色定义
6. **role_permissions** - 角色权限关联
7. **settings** - 系统设置

### 8.2 中优先级：创建剩余迁移

8. **footer_links** - 页脚配置
9. **page_seo** - 页面SEO配置
10. **seo_config** - 全局SEO配置
11. **site_config** - 站点配置
12. **themes** - 主题配置
13. **author_profiles** - 作者资料（含技能、宣言等JSON字段）
14. **backups** - 备份记录
15. **email_templates** - 邮件模板
16. **admin_logs** - 操作日志
17. **mail_settings** - 邮件配置
18. **user_levels** - 用户等级
19. **visits** - 访问记录
20. **i18n** - 多语言配置

### 8.3 已有迁移表需要补充字段

| 表名 | 需要补充的字段 | 优先级 |
|------|---------------|:---:|
| **users** | role_id (关联roles表), points | 高 |
| **advertisements** | position_id (关联ad_positions表) | 高 |
| **journals** | title, date | 高 |
| **categories** | status (active/inactive) | 中 |
| **subscribers** | name, source, subscribed_at | 中 |

### 8.4 数据文件一致性检查

| 数据文件 | 当前状态 | 需要修正 |
|----------|---------|:---:|
| ad_positions.js | ✅ 已创建，结构完整 | 无需修正 |
| author_profiles.js | ✅ 已完善，整合了技能、宣言数据 | 无需修正 |
| permissions.js | ✅ 符合 Spatie 标准 | 无需修正 |
| roles.js | ✅ 结构完整 | 无需修正 |
| menu.js | ✅ 结构完整 | 无需修正 |
| themes.js | ✅ 结构完整 | 无需修正 |

### 8.5 说明

本文档基于数据库设计原则（完整性、一致性、规范化、关联性、可扩展性）进行分析，确保数据文件与数据库表结构的对应关系准确。建议按照优先级顺序创建缺失的数据库迁移，并补充已有表中缺失的字段。