# 后端功能开发清单

**重要说明：** 此文档为主要真实来源！其他文档请以此为准！

**最后更新：** 2026-06-03 (27 模块全部真实数据对接完成)
**Laravel版本：** 11 (精简模式)
**版本：** 3.0
**状态说明：** ✅ 已完成 | 🔄 进行中 | ⚠️ 待处理 | ❌ 已移除/跳过

---

## 架构模式说明

> **重要：** 采用 **Inertia.js 现代单体架构**。前台和后台均使用 Inertia.js 将 Laravel 后端数据直接传递给 Vue 组件，保留 SPA 体验。API 路由独立保留供移动端 APP 和第三方系统使用。

| 端 | 渲染方式 | 数据获取方式 | 控制器目录 |
|----|----------|--------------|------------|
| **前台网站** | Inertia.js + Vue 3 | 通过 Inertia props 注入 | `App\Http\Controllers\Web\` |
| **后台管理** | Inertia.js + Vue 3 | 通过 Inertia props 注入 | `App\Http\Controllers\Admin\` |
| **移动端 APP** | - | 通过 RESTful API 获取 | `App\Http\Controllers\Api\V1\` |
| **第三方集成** | - | 通过 RESTful API 获取 | `App\Http\Controllers\Api\V1\` |

---

## Laravel 架构组件清单

### 核心架构模式

| 模式 | 目录 | 现状 | 说明 |
|------|------|:----:|------|
| **Service Layer** | `app/Services/` | ✅ 已有 | 业务逻辑封装（25个Service） |
| **Repository Layer** | `app/Repositories/` | ✅ 已有 | 数据访问层（轻量级模式，7个Repository） |
| **API Resource** | `app/Http/Resources/V1/` | ✅ 已有 | 响应格式化（12个） |
| **FormRequest** | `app/Http/Requests/` | ✅ 已有 | 表单验证（24个FormRequest） |
| **Policy** | `app/Policies/` | ❌ 已移除 | 授权策略（24个已删除，统一走 Spatie Permission 中间件） |
| **Observer** | `app/Observers/` | ❌ 已跳过 | 模型事件（采用Service模式替代） |
| **Event/Listener** | `app/Events/`, `app/Listeners/` | ✅ 已完成 | 事件驱动（CommentCreated + SendCommentNotification, AdViewed + TrackAdViewed） |
| **Notification** | `app/Notifications/` | ✅ 已完成 | 通知系统（NewCommentNotification, NewSubscriberNotification, SystemNotification） |
| **Middleware** | `app/Http/Middleware/` | ✅ 已有 | 中间件（HandleInertiaRequests, SeoMiddleware, LanguageMiddleware, AdminMiddleware, ActivityLogMiddleware, CheckMaintenanceMode, PageVisitMiddleware） |
| **Command** | `app/Console/Commands/` | ✅ 已有 | Artisan命令（BackupCommand） |
| **Factory** | `database/factories/` | ⚠️ 待创建 | 测试数据 |
| **Seeder** | `database/seeders/` | ✅ 已完成 | 25个Seeder，约200条高质量模拟数据（2026-05-25优化） |

---

## 优先级：高（核心功能）

### 1. 认证与权限系统

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| AUTH-01 | 安装配置 Laravel Sanctum | 认证 | ✅ 已完成 | - | composer require |
| AUTH-02 | 创建 roles 表迁移 | Migration | ✅ 已完成 | - | `php artisan make:migration` |
| AUTH-03 | 创建 Role Model + Policy | Model+Policy | ✅ 已完成 | AUTH-02 | HasRoles Trait |
| AUTH-04 | 创建 permissions 表迁移 | Migration | ✅ 已完成 | - | Spatie标准表 |
| AUTH-05 | 创建 Permission Model | Model | ✅ 已完成 | AUTH-04 | 标准Model |
| AUTH-06 | users表通过 model_has_roles 关联角色 | Migration | ✅ 已完成 | AUTH-02 | Spatie RBAC中间表 |
| AUTH-07 | 创建 RoleController CRUD | Controller | ✅ 已完成 | AUTH-03 | app/Http/Controllers/Admin/ |
| AUTH-08 | 创建 RoleResource | Resource | ✅ 已完成 | AUTH-07 | app/Http/Resources/V1/ |
| AUTH-09 | 创建 StoreRoleRequest | FormRequest | ✅ 已完成 | AUTH-07 | 验证规则 |
| AUTH-10 | 角色-权限分配API | Controller | ✅ 已完成 | AUTH-05, AUTH-07 | syncPermissions |
| AUTH-10-1 | 创建 SyncPermissionsRequest | FormRequest | ✅ 已完成 | AUTH-10 | 验证规则 |
| AUTH-10-2 | 创建 Api/V1/RolesController | Controller | ✅ 已完成 | AUTH-10 | app/Http/Controllers/Api/V1/ |
| AUTH-13 | 创建 UpdateRoleRequest | FormRequest | ✅ 已完成 | AUTH-07 | 验证规则 |
| AUTH-14 | 创建 StoreUserRequest | FormRequest | ✅ 已完成 | - | 验证规则 |
| AUTH-15 | 创建 UpdateUserRequest | FormRequest | ✅ 已完成 | - | 验证规则 |
| AUTH-11 | users表添加 points 字段 | Migration | ✅ 已完成 | - | unsignedBigInteger |
| AUTH-12 | users表添加 last_login_at | Migration | ✅ 已完成 | - | timestamp nullable |

### 1.5 前台API认证系统（移动端APP）

| ID | 任务 | 组件类型 | 状态 | Laravel实现 |
|----|------|----------|:----:|------------|
| API-01 | 配置 auth.php api guard | Config | ✅ 已完成 | Sanctum driver |
| API-02 | 创建 AuthController | Controller | ✅ 已完成 | app/Http/Controllers/Api/V1/AuthController.php |
| API-03 | 创建 LoginRequest | FormRequest | ✅ 已完成 | app/Http/Requests/LoginRequest.php |
| API-04 | 配置 routes/api.php 路由 | Route | ✅ 已完成 | public: login, protected: v1/* |
| API-05 | 创建 personal_access_tokens 迁移 | Migration | ✅ 已完成 | Sanctum 标准表结构 |
| API-06 | 编写认证测试用例 | Test | ✅ 已完成 | tests/Feature/Api/V1/AuthTest.php |

**前台API端点：**

> **重要说明：** 后台管理使用 Inertia.js，不走 API。以下 API 仅供移动端 APP 和第三方系统使用。

#### 公开接口（无需认证）
| 端点 | 方法 | 说明 |
|------|:----:|------|
| `/api/login` | POST | 用户登录，返回 Bearer Token |
| `/api/subscribe` | POST | 订阅邮件 |
| `/api/unsubscribe` | POST | 取消订阅 |
| `/api/authors` | GET | 获取作者列表 |
| `/api/authors/{slug}` | GET | 获取作者详情 |

#### 受保护接口（需要 `auth:sanctum` 认证）
| 端点 | 方法 | 说明 |
|------|:----:|------|
| `/api/logout` | POST | 用户登出，撤销 Token |
| `/api/v1/posts` | GET | 获取文章列表 |
| `/api/v1/posts/{slug}` | GET | 获取文章详情 |
| `/api/v1/posts/{post}/comments` | GET | 获取文章评论 |
| `/api/v1/posts/{post}/comments` | POST | 发表评论 |
| `/api/v1/videos` | GET | 获取视频列表 |
| `/api/v1/videos/{video}` | GET | 获取视频详情 |
| `/api/v1/projects` | GET | 获取项目列表 |
| `/api/v1/projects/{project}` | GET | 获取项目详情 |
| `/api/v1/resources` | GET | 获取资源列表 |
| `/api/v1/resources/{resource}` | GET | 获取资源详情 |
| `/api/v1/categories` | GET | 获取分类列表 |
| `/api/v1/categories/{slug}` | GET | 获取分类详情 |
| `/api/v1/tags` | GET | 获取标签列表 |
| `/api/v1/tags/{slug}` | GET | 获取标签详情 |
| `/api/v1/users/{user}` | GET | 获取用户信息 |
| `/api/v1/me` | GET | 获取当前用户信息 |
| `/api/v1/roles` | GET | 获取角色列表 |
| `/api/v1/roles/{role}` | GET | 获取角色详情 |
| `/api/v1/permissions` | GET | 获取权限列表 |
| `/api/v1/roles/{role}/permissions` | PUT | 同步角色权限 |

### 2. 设置与配置

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| SET-01 | 创建 settings 表迁移 | Migration | ✅ 已完成 | - | key-value结构 |
| SET-02 | 创建 Setting Model | Model | ✅ 已完成 | SET-01 | JSON Cast |
| SET-03 | 创建 SettingService | Service | ✅ 已完成 | SET-02 | 单例+缓存 |
| SET-04 | 创建 SettingController | Controller | ✅ 已完成 | SET-03 | Admin API |
| SET-05 | 创建 menus 表迁移 | Migration | ✅ 已完成 | - | parent_id层级 |
| SET-06 | 创建 Menu Model + Builder | Model | ✅ 已完成 | SET-05 | 递归关系 |
| SET-07 | 创建 MenuController | Controller | ✅ 已完成 | SET-06 | 树形CRUD |
| SET-08 | 菜单缓存 | Cache | ✅ 已完成 | SET-07 | Cache::remember |
| SET-09 | 创建 MenuService | Service | ✅ 已完成 | SET-06 | 业务逻辑 |

### 3. 内容管理 - 核心

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| CONT-01 | 创建 categories 表迁移 | Migration | ✅ 已完成 | - | parent_id, slug |
| CONT-02 | 创建 Category Model | Model | ✅ 已完成 | CONT-01 | 树形关系 |
| CONT-03 | 创建 CategoryController | Controller | ✅ 已完成 | CONT-02 | Admin API |
| CONT-04 | 创建 CategoryResource | Resource | ✅ 已完成 | CONT-03 | API响应 |
| CONT-04-1 | StoreCategoryRequest | FormRequest | ✅ 已完成 | CONT-03 | 验证规则 |
| CONT-04-2 | UpdateCategoryRequest | FormRequest | ✅ 已完成 | CONT-03 | 验证规则 |
| CONT-04-3 | CategoryRepository | Repository | ✅ 已完成 | CONT-02 | 数据访问层 |
| CONT-05 | 创建 tags 表迁移 | Migration | ✅ 已完成 | - | name, slug, usage_count |
| CONT-06 | 创建 Tag Model | Model | ✅ 已完成 | CONT-05 | 多态关联 |
| CONT-07 | 创建 TagController | Controller | ✅ 已完成 | CONT-06 | Admin API |
| CONT-08 | 创建 TagResource | Resource | ✅ 已完成 | CONT-07 | API响应 |
| CONT-08-1 | StoreTagRequest | FormRequest | ✅ 已完成 | CONT-07 | 验证规则 |
| CONT-08-2 | UpdateTagRequest | FormRequest | ✅ 已完成 | CONT-07 | 验证规则 |
| CONT-08-3 | TagRepository | Repository | ✅ 已完成 | CONT-06 | 数据访问层 |
| CONT-09 | 安装 Spatie Media Library | Package | ✅ 已完成 | - | composer + intervention/image |
| CONT-10 | 创建 media 表迁移 | Migration | ✅ 已完成 | CONT-09 | Spatie表结构 |
| CONT-11 | 创建 Medium Model | Model | ✅ 已完成 | CONT-10 | InteractsWithMedia |
| CONT-12 | 创建 MediaController | Controller | ✅ 已完成 | CONT-11 | 上传处理 |
| CONT-12-1 | 创建 UploadMediaRequest | FormRequest | ✅ 已完成 | CONT-12 | 验证规则 |
| CONT-12-2 | 配置 storage 存储 | Config | ✅ 已完成 | CONT-12 | public磁盘 |
| CONT-13 | 创建 posts 表迁移 | Migration | ✅ 已完成 | CONT-01, AUTH-05 | 完整字段 |
| CONT-14 | 创建 Post Model | Model | ✅ 已完成 | CONT-13 | fillable/casts/relations |
| CONT-15 | 创建 PostService | Service | ✅ 已完成 | CONT-14 | 业务逻辑 |
| CONT-16 | 创建 PostController CRUD | Controller | ✅ 已完成 | CONT-15 | Admin API |
| CONT-17 | 创建 PostResource | Resource | ✅ 已完成 | CONT-16 | API响应 |
| CONT-18 | 创建 StorePostRequest | FormRequest | ✅ 已完成 | CONT-16 | 验证规则 |
| CONT-18-2 | 创建 UpdatePostRequest | FormRequest | ✅ 已完成 | CONT-16 | 验证规则 |
| CONT-19 | 创建 PostPolicy | Policy | ✅ 已完成 | CONT-14 | 授权逻辑 |
| CONT-20 | 创建 PostObserver | Observer | ❌ 已跳过 | CONT-14 | 采用Service模式 |
| CONT-20-1 | 创建 PostRepository | Repository | ✅ 已完成 | CONT-14 | 数据访问层 |
| CONT-21 | 安装 league/commonmark | Package | ⚠️ 待处理 | - | Markdown解析 |

### 4. 内容管理 - 扩展

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| CONT-22 | 创建 videos 表迁移 | Migration | ✅ 已完成 | AUTH-05 | platform, thumbnail |
| CONT-23 | 创建 Video Model + Service | Model+Service | ✅ 已完成 | CONT-22 | 标准CRUD |
| CONT-24 | 创建 VideoController | Controller | ✅ 已完成 | CONT-23 | Admin API |
| CONT-25 | 创建 VideoResource | Resource | ✅ 已完成 | CONT-24 | API响应 |
| CONT-25-1 | StoreVideoRequest | FormRequest | ✅ 已完成 | CONT-24 | 验证规则 |
| CONT-25-2 | UpdateVideoRequest | FormRequest | ✅ 已完成 | CONT-24 | 验证规则 |
| CONT-25-3 | VideoRepository | Repository | ✅ 已完成 | CONT-23 | 数据访问层 |
| CONT-26 | 创建 projects 表迁移 | Migration | ✅ 已完成 | AUTH-05 | title, description |
| CONT-27 | 创建 Project Model + Service | Model+Service | ✅ 已完成 | CONT-26 | 标准CRUD |
| CONT-28 | 创建 ProjectController | Controller | ✅ 已完成 | CONT-27 | Admin API |
| CONT-29 | 创建 ProjectResource | Resource | ✅ 已完成 | CONT-28 | API响应 |
| CONT-29-1 | StoreProjectRequest | FormRequest | ✅ 已完成 | CONT-28 | 验证规则 |
| CONT-29-2 | UpdateProjectRequest | FormRequest | ✅ 已完成 | CONT-28 | 验证规则 |
| CONT-29-3 | ProjectRepository | Repository | ✅ 已完成 | CONT-27 | 数据访问层 |
| CONT-30 | 创建 resources 表迁移 | Migration | ✅ 已完成 | AUTH-05 | file_path, download_count |
| CONT-31 | 创建 Resource Model | Model | ✅ 已完成 | CONT-30 | 标准CRUD |
| CONT-32 | 创建 ResourceController | Controller | ✅ 已完成 | CONT-31 | Admin API |
| CONT-33 | 创建 ResourceResource | Resource | ✅ 已完成 | CONT-32 | API响应 |
| CONT-33-1 | StoreResourceRequest | FormRequest | ✅ 已完成 | CONT-32 | 验证规则 |
| CONT-34 | 创建 journals 表迁移 | Migration | ✅ 已完成 | AUTH-05 | mood, weather JSON |
| CONT-35 | 创建 Journal Model | Model | ✅ 已完成 | CONT-34 | JSON Cast |
| CONT-36 | 创建 JournalController | Controller | ✅ 已完成 | CONT-35 | Admin API |
| CONT-37 | 创建 JournalResource | Resource | ✅ 已完成 | CONT-36 | API响应 |
| CONT-37-1 | StoreJournalRequest | FormRequest | ✅ 已完成 | CONT-36 | 验证规则 |

---

## 优先级：中（重要功能）

### 5. 用户管理

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| USER-01 | 创建 user_levels 表迁移 | Migration | ✅ 已完成 | - | level, icon, color, benefits |
| USER-02 | 创建 UserLevel Model | Model | ✅ 已完成 | USER-01 | JSON Cast benefits |
| USER-03 | 创建 UserLevelController | Controller | ✅ 已完成 | USER-02 | Admin API |
| USER-04 | 创建 UserLevelResource | Resource | ✅ 已完成 | USER-03 | API响应 |
| USER-05 | 创建 user_points_history 表 | Migration | ✅ 已完成 | - | points change log |
| USER-06 | 创建 UserService 积分管理 | Service | ✅ 已完成 | AUTH-11 | 积分更新/等级更新 |
| USER-07 | 创建 subscribers 表迁移 | Migration | ✅ 已完成 | - | email, subscribed_at, status |
| USER-08 | 创建 Subscriber Model | Model | ✅ 已完成 | USER-07 | Notifiable |
| USER-09 | 创建 SubscriberController | Controller | ✅ 已完成 | USER-08 | Admin API |
| USER-10 | 创建 public 订阅API | Controller | ✅ 已完成 | USER-09 | /api/subscribe |
| USER-11 | 创建 author_profiles 表迁移 | Migration | ✅ 已完成 | AUTH-05 | user_id唯一 |
| USER-12 | 创建 AuthorProfile Model | Model | ✅ 已完成 | USER-11 | JSON Cast |
| USER-13 | 创建 AuthorProfileController | Controller | ✅ 已完成 | USER-12 | Admin Inertia (数据库) |

### 6. SEO与国际化

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| SEO-01 | 创建 page_seo 表迁移 | Migration | ✅ 已完成 | - | route_name唯一 |
| SEO-02 | 创建 PageSeo Model | Model | ✅ 已完成 | SEO-01 | 标准Model |
| SEO-03 | 创建 SeoController | Controller | ✅ 已完成 | SEO-02 | Admin Inertia |
| SEO-03-1 | 页面SEO CRUD路由 | Route | ✅ 已完成 | SEO-03 | store/update/destroy |
| SEO-03-2 | PageSeoSeeder | Seeder | ✅ 已完成 | SEO-02 | 5条页面SEO数据 |
| SEO-04 | 创建 SeoMiddleware | Middleware | ✅ 已完成 | SEO-03 | 动态加载SEO |
| SEO-05 | 创建 languages 表迁移 | Migration | ✅ 已完成 | - | code, name, is_default |
| SEO-06 | 创建 Language Model | Model | ✅ 已完成 | SEO-05 | 标准Model |
| SEO-07 | 创建 translations 表迁移 | Migration | ✅ 已完成 | SEO-05 | key, locale, value |
| SEO-08 | 创建 Translation Model | Model | ✅ 已完成 | SEO-07 | 标准Model |
| SEO-09 | 创建 I18nController | Controller | ✅ 已完成 | SEO-06, SEO-08 | Admin Inertia (真实数据) |
| SEO-10 | 创建语言切换Middleware | Middleware | ✅ 已完成 | SEO-06 | locale设置 |
| SEO-11 | 创建导出JSON命令 | Command | ⏸️ 已跳过 | SEO-09 | vue-i18n同步 |

> **国际化架构决策（SEO-11 已跳过）：**
> - 采用 **纯文件管理 + languages 表 + translations 表作为默认语言** 方案
> - `languages` 表：存储语言配置（代码、名称、默认状态）
> - `translations` 表：存储默认语言翻译（作为源数据备份）
> - `public/locales/*.json`：所有语言的翻译文件（前端直接使用）
> - SEO-11 导出命令暂不实现，后续按需开发

### 7. 社交与互动

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| SOC-01 | 创建 social_links 表迁移 | Migration | ✅ 已完成 | - | platform, url, icon |
| SOC-02 | 创建 SocialLink Model | Model | ✅ 已完成 | SOC-01 | 标准Model |
| SOC-03 | 创建 SocialLinksController | Controller | ✅ 已完成 | SOC-02 | Admin Inertia |
| SOC-03-1 | 社交链接 CRUD 路由 | Route | ✅ 已完成 | SOC-03 | store/update/destroy |
| SOC-04 | 公开社交链接 API | Controller | ✅ 已完成 | SOC-02 | Api/V1/SocialLinkController |
| SOC-05 | SocialLinkSeeder | Seeder | ✅ 已完成 | SOC-02 | 5条社交链接数据 |
| SOC-06 | 创建 comments 表迁移 | Migration | ✅ 已完成 | AUTH-05 | 多态关联 |
| SOC-06 | 创建 Comment Model | Model | ✅ 已完成 | SOC-05 | MorphTo |
| SOC-07 | 创建 CommentService | Service | ✅ 已完成 | SOC-06 | 业务逻辑 |
| SOC-08 | 创建 CommentController | Controller | ✅ 已完成 | SOC-07 | Admin + Public API |
| SOC-09 | 创建 CommentResource | Resource | ✅ 已完成 | SOC-08 | API响应 |
| SOC-09-1 | StoreCommentRequest | FormRequest | ✅ 已完成 | SOC-08 | 验证规则 |
| SOC-09-2 | CommentRepository | Repository | ✅ 已完成 | SOC-06 | 数据访问层 |
| SOC-10 | 创建 CommentCreated Event | Event | ✅ 已完成 | SOC-06 | 事件类 |
| SOC-11 | 创建 SendCommentNotification Listener | Listener | ✅ 已完成 | SOC-10 | 通知发送 |
| SOC-12 | 创建 comments 索引 | Migration | ✅ 已完成 | SOC-05 | 多态查询优化 |

### 8. 邮件与通知

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| MAIL-01 | 创建 mail_configs 表迁移 | Migration | ✅ 已完成 | - | driver, host, credentials |
| MAIL-02 | 创建 MailConfig Model | Model | ✅ 已完成 | MAIL-01 | 标准Model |
| MAIL-03 | 创建 MailConfigController | Controller | ✅ 已完成 | MAIL-02 | Admin Inertia (真实数据) |
| MAIL-04 | 创建测试邮件功能 | Mailable | ✅ 已完成 | MAIL-03 | Mailable类 |
| MAIL-05 | 创建 email_templates 表迁移 | Migration | ✅ 已完成 | - | subject, body, variables |
| MAIL-06 | 创建 EmailTemplate Model | Model | ✅ 已完成 | MAIL-05 | 标准Model |
| MAIL-07 | 创建 EmailTemplateController | Controller | ✅ 已完成 | MAIL-06 | Admin Inertia (真实数据) |
| MAIL-08 | 创建通用 Mailable | Mailable | ✅ 已完成 | MAIL-07 | GenericEmail |
| MAIL-09 | 创建 notifications 表迁移 | Migration | ✅ Laravel内置 | AUTH-05 | 数据库通知 |
| MAIL-10 | 创建通知Model | Model | ✅ 已完成 | MAIL-09 | Notifiable |
| MAIL-11 | 创建 NotificationController | Controller | ✅ 已完成 | MAIL-10 | Admin Inertia (数据库) |
| MAIL-12 | 创建 NewCommentNotification | Notification | ✅ 已完成 | SOC-10 | 邮件/数据库通知 |
| MAIL-13 | 创建 NewSubscriberNotification | Notification | ✅ 已完成 | - | 邮件/数据库通知 |

---

## 优先级：低（辅助功能）

### 9. 广告管理

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| AD-01 | 创建 ad_positions 表迁移 | Migration | ✅ 已完成 | - | key, name, size |
| AD-02 | 创建 AdPosition Model | Model | ✅ 已完成 | AD-01 | hasMany advertisements |
| AD-03 | 创建 Advertisement Model | Model | ✅ 已完成 | AD-01 | BelongsTo AdPosition |
| AD-04 | 创建 AdvertisementController | Controller | ✅ 已完成 | AD-03 | Admin Inertia (真实数据) |
| AD-05 | 创建广告轮换逻辑 | Service | ✅ 已完成 | AD-04 | 权重随机 |
| AD-06 | 创建 AdViewed Event | Event | ✅ 已完成 | AD-05 | 展示追踪 |
| AD-07 | 创建 interactions 表迁移 | Migration | ✅ 已完成 | - | 多态关联 |

### 10. 系统与维护

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| SYS-01 | 安装 spatie/laravel-activitylog | Package | ✅ 已完成 | - | composer |
| SYS-02 | 配置 activitylog | Config | ✅ 已完成 | SYS-01 | 配置文件已发布 |
| SYS-03 | 创建 LogController | Controller | ✅ 已完成 | SYS-02 | Admin Inertia (数据库) |
| SYS-04 | 创建日志清理命令 | Command | ⏸️ 已跳过 | SYS-03 | 使用 Controller clear 方法 |
| SYS-05 | 安装 spatie/laravel-backup | Package | ✅ 已完成 | - | composer |
| SYS-06 | 创建 BackupController | Controller | ✅ 已完成 | SYS-05 | Admin Inertia |
| SYS-07 | 创建 RestoreController | Controller | ✅ 已完成 | SYS-05 | Admin Inertia |
| SYS-08 | 创建定时备份任务 | Schedule | ✅ 已完成 | SYS-06 | 每日凌晨2点备份数据库 |
| SYS-09 | 创建 CleanupUnusedTagsCommand | Command | ⏸️ 已跳过 | CONT-07 | 清理未使用标签 |

### 11. 额外迁移（已完成）

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| EXT-01 | 创建 footer_links 表迁移 | Migration | ✅ 已完成 | - | 页脚链接 |
| EXT-02 | 创建 themes 表迁移 | Migration | ✅ 已完成 | - | 主题配置 |
| EXT-03 | 创建 visits 表迁移 | Migration | ✅ 已完成 | - | 多态访问记录 |
| EXT-04 | 创建 backups 表迁移 | Migration | ✅ 已完成 | - | 备份记录 |
| EXT-05 | 创建 admin_logs 表迁移 | Migration | ⏸️ 已合并 | - | 已合并到 activity_log |
| EXT-06 | 创建 seo 表迁移 | Migration | ✅ 已完成 | - | SEO配置 key-value |
| EXT-07 | 创建 ad_interactions 表迁移 | Migration | ✅ 已完成 | AD-01 | 广告互动追踪 |

### 12. Inertia.js 前端集成

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| FE-01 | 安装 Inertia.js 后端扩展 | Package | ✅ 已完成 | - | composer require inertiajs/inertia-laravel |
| FE-02 | 创建 HandleInertiaRequests 中间件 | Middleware | ✅ 已完成 | FE-01 | php artisan inertia:middleware |
| FE-03 | 配置 Inertia 根模板 app.blade.php | Template | ✅ 已完成 | FE-01 | @inertiaHead + @inertia |
| FE-04 | 创建 Web FrontendController | Controller | ✅ 已完成 | CONT-15 | Inertia::render('front/Home') |
| FE-05 | 创建 Web PostController | Controller | ✅ 已完成 | CONT-15 | Inertia::render('front/Blog') |
| FE-06 | 创建 Admin PostController | Controller | ✅ 已完成 | CONT-15 | Inertia::render('admin/Posts') |
| FE-07 | 创建 Admin CategoryController | Controller | ✅ 已完成 | CONT-02 | Inertia::render('admin/Categories') |
| FE-08 | 创建 Admin TagController | Controller | ✅ 已完成 | CONT-06 | Inertia::render('admin/Tags') |
| FE-09 | 创建 Admin VideoController | Controller | ✅ 已完成 | CONT-23 | Inertia::render('admin/Videos') |
| FE-10 | 创建 Admin ProjectController | Controller | ✅ 已完成 | CONT-27 | Inertia::render('admin/Projects') |
| FE-11 | 创建 Admin ResourceController | Controller | ✅ 已完成 | CONT-31 | Inertia::render('admin/Resources') |
| FE-12 | 前端配置 createInertiaApp | JS | ✅ 已完成 | FE-01 | app.js 入口配置 |
| FE-13 | 集成 ziggy-js 路由辅助 | Package | ✅ 已完成 | FE-01 | npm install ziggy-js |
| FE-14 | 为资源路由添加 `->names()` 配置 | Route | ✅ 已完成 | FE-13 | 确保 Ziggy 路由助手正常解析 |
| FE-15 | 修复后台菜单无数据问题 | Middleware | ✅ 已完成 | FE-02 | 通过 HandleInertiaRequests 中间件共享菜单数据 |
| FE-16 | 修复前台页面 SEO 数据空值问题 | Component | ✅ 已完成 | FE-12 | Projects、Author、Resources、Videos、Blog 页面空值处理 |
| FE-17 | 优化后台页面表单提交逻辑 | Component | 🔄 进行中 | FE-12 | 使用 Inertia forms（已完成：Users、Roles、Journals） |

---

## 开发汇总

| 优先级 | 总数 | ✅ 完成 | 🔄 进行中 | ⚠️ 待处理 |
|:------:|:----:|:-------:|:---------:|:---------:|
| 高 | 55 | 55 | 0 | 0 |
| 中 | 25 | 23 | 0 | 2 |
| 低 | 23 | 21 | 0 | 2 |
| **总计** | **103** | **99** | **0** | **4** |

> **注：** 截至 2026-06-03，27 个模块后台管理 + 11 个前台页面全部完成真实数据对接，MockDataService 仅剩中间件 fallback 引用。
>
> **核心架构组件已完成：**
> - ✅ Service Layer (25个Service)
> - ✅ Repository Layer (7个Repository)
> - ✅ API Resource (12个)
> - ✅ FormRequest (24个)
> - ❌ Policy (24个已移除，统一走 Spatie Permission 中间件)
> - ✅ Middleware (7个)
> - ✅ Controller (50+个)
> - ❌ Observer (已跳过，采用Service模式)
> - ✅ Event/Listener (2对：CommentCreated + SendCommentNotification, AdViewed + TrackAdViewed)
> - ✅ Notification (3个：NewCommentNotification, NewSubscriberNotification, SystemNotification)
> - ✅ Feature Test (9个测试用例)
>
> **前台API认证系统已完成：**
> - ✅ Laravel Sanctum 集成
> - ✅ AuthController (login/logout/me)
> - ✅ LoginRequest 表单验证
> - ✅ API路由配置 (公开登录，保护其他端点)
> - ✅ personal_access_tokens 迁移
> - ✅ 9个认证测试用例全部通过
> 
> **Inertia.js 架构迁移已完成：**
> - 后端扩展包已安装配置
> - HandleInertiaRequests 中间件已创建
> - 根模板 app.blade.php 已配置
> - 所有前台和后台控制器已改用 Inertia::render() 返回数据
> - 资源路由已添加 `->names()` 配置，Ziggy 路由助手正常工作
> - 后台菜单数据通过 HandleInertiaRequests 中间件共享
> - 前台页面全部使用真实数据库数据
> - 前端 Vue 组件已改为通过 props 接收数据
> - MockDataService 仅剩中间件容错 fallback 引用

---

## 应该存在的全部数据表

> **重要：** 以下为项目完整数据库表清单，共 **48 张表**。按表名字母顺序排列。

| 序号 | 表名 | 说明 |
|:---:|------|------|
| 1 | `activity_log` | 活动日志表（spatie/laravel-activitylog） |
| 2 | `ad_interactions` | 广告互动表 |
| 3 | `ad_positions` | 广告位配置表 |
| 4 | `advertisements` | 广告表 |
| 5 | `author_profiles` | 作者资料表 |
| 6 | `backups` | 备份记录表（记录+文件状态追踪） |
| 7 | `cache` | 缓存表 |
| 8 | `cache_locks` | 缓存锁表 |
| 9 | `categories` | 分类表 |
| 10 | `comments` | 评论表 |
| 11 | `email_templates` | 邮件模板表 |
| 12 | `failed_jobs` | 失败任务表 |
| 13 | `footer_links` | 页脚链接表 |
| 14 | `interactions` | 用户互动表（点赞/收藏） |
| 15 | `job_batches` | 任务批次表 |
| 16 | `jobs` | 队列表 |
| 17 | `journals` | 日志表 |
| 18 | `languages` | 语言配置表 |
| 19 | `mail_configs` | 邮件配置表 |
| 20 | `media` | 媒体文件表（Spatie Media Library） |
| 21 | `menus` | 菜单配置表 |
| 22 | `migrations` | 迁移记录表（Laravel 内置） |
| 23 | `model_has_permissions` | 模型-权限关联表（Spatie） |
| 24 | `model_has_roles` | 模型-角色关联表（Spatie） |
| 25 | `notifications` | 通知表（Laravel 内置） |
| 26 | `page_seo` | 页面SEO表 |
| 27 | `password_reset_tokens` | 密码重置令牌表 |
| 28 | `permissions` | 权限定义表 |
| 29 | `personal_access_tokens` | API 令牌表（Sanctum） |
| 30 | `posts` | 文章表 |
| 31 | `projects` | 项目表 |
| 32 | `resources` | 资源表 |
| 33 | `role_has_permissions` | 角色-权限关联表（Spatie） |
| 34 | `roles` | 角色定义表 |
| 35 | `seo` | SEO配置表 |
| 36 | `sessions` | 会话表 |
| 37 | `settings` | 系统设置表 |
| 38 | `social_links` | 社交链接表 |
| 39 | `subscribers` | 订阅者表 |
| 40 | `taggables` | 标签多态关联表 |
| 41 | `tags` | 标签表 |
| 42 | `themes` | 主题配置表 |
| 43 | `translations` | 翻译数据表 |
| 44 | `user_levels` | 用户等级表 |
| 45 | `user_points_history` | 积分历史表 |
| 46 | `users` | 用户表 |
| 47 | `videos` | 视频表 |
| 48 | `visits` | 访问记录表 |

---

## 已完成迁移清单

| 序号 | 迁移文件 | 表名 | 说明 |
|:---:|---------|------|------|
| 1 | `2026_05_23_080100_create_roles_table.php` | roles | 角色定义表 |
| 2 | `2026_05_23_080101_create_permissions_table.php` | permissions | 权限定义表 |
| 3 | `2026_05_23_080102_create_ad_positions_table.php` | ad_positions | 广告位配置表 |
| 4 | `2026_05_23_080103_create_settings_table.php` | settings | 系统设置表 |
| 5 | `2026_05_23_080104_create_menus_table.php` | menus | 菜单配置表 |
| 6 | `2026_05_23_080105_create_media_table.php` | media | 媒体文件表 |
| 7 | `2026_05_23_080106_create_user_levels_table.php` | user_levels | 用户等级表 |
| 8 | `2026_05_23_080107_create_author_profiles_table.php` | author_profiles | 作者资料表 |
| 9 | `2026_05_23_080108_create_seo_table.php` | seo | SEO配置表 |
| 10 | `2026_05_23_080109_create_languages_table.php` | languages | 语言配置表 |
| 11 | `2026_05_23_080110_create_mail_configs_table.php` | mail_configs | 邮件配置表 |
| 12 | `2026_05_23_080111_create_email_templates_table.php` | email_templates | 邮件模板表 |
| 13 | `2026_05_23_080112_create_social_links_table.php` | social_links | 社交链接表 |
| 14 | `2026_05_28_190456_create_activity_log_table.php` | activity_log | 活动日志表（合并版） |
| 15 | `2026_05_23_080114_create_backups_table.php` | backups | 备份记录表 |
| 16 | `2026_05_23_080115_create_footer_links_table.php` | footer_links | 页脚链接表 |
| 17 | `2026_05_23_080116_create_themes_table.php` | themes | 主题配置表 |
| 18 | `2026_05_23_080117_create_translations_table.php` | translations | 翻译数据表 |
| 19 | `2026_05_23_080118_create_page_seo_table.php` | page_seo | 页面SEO表 |
| 20 | `2026_05_23_080119_create_visits_table.php` | visits | 访问记录表 |
| 21 | `2026_05_23_080200_add_role_id_and_points_to_users_table.php` | users (扩展) | 添加 role_id 和 points |
| 22 | `2026_05_23_080201_add_position_id_to_advertisements_table.php` | advertisements (扩展) | 添加 position_id |
| 23 | `2026_05_23_080202_create_user_points_history_table.php` | user_points_history | 积分历史表 |
| 24 | `2026_05_23_080203_create_ad_interactions_table.php` | ad_interactions | 广告互动表 |
| 25 | `2026_05_23_080207_add_status_to_categories_table.php` | categories (扩展) | 添加 status 字段 |
| 26 | `2026_05_28_173825_add_indexes_to_comments_table.php` | comments (索引) | 添加复合索引 |

**已有迁移（Laravel 内置）**：users, password_reset_tokens, sessions, cache, cache_locks, jobs, job_batches, failed_jobs, notifications, personal_access_tokens

**Seeder 完成情况**：25个 Seeder，约200条高质量模拟数据（2026-05-25优化完成）

### 优化记录

| 日期 | 优化内容 | 影响范围 |
|:---:|---------|---------|
| 2026-05-25 | RoleSeeder 添加 value 字段 | 修复迁移填充错误 |
| 2026-05-25 | PostSeeder 优化为5篇高质量文章 | 完整Markdown、封面图、SEO数据 |
| 2026-05-25 | JournalSeeder 补充 title、date、likes_count 字段 | 5条详细日志 |
| 2026-05-25 | CategorySeeder 优化为6个专业分类 | THEORY, DESIGN, CULTURE等 |
| 2026-05-25 | TagSeeder 优化为15个专业标签 | Architecture, Design等 |
| 2026-05-25 | CommentSeeder 添加嵌套评论 | parent_id关联 |
| 2026-05-25 | DatabaseSeeder 新增 Editor、Author 用户 | 4个测试用户 |

---

## 组件类型统计（实际代码统计）

| 组件类型 | 数量 | 说明 |
|----------|:----:|------|
| Migration | 25+ | 数据库表结构（包含扩展迁移） |
| Model | 34 | Eloquent模型（完整列表） |
| Controller | 50+ | 控制器（Admin:27 + Api/V1:10 + Web:8） |
| Service | 25 | 业务逻辑层 |
| Repository | 7 | 数据访问层（轻量级模式） |
| Resource | 12 | API响应格式化（V1版本：Post, Category, Tag, Video, Project, User, Comment, Role, Permission, Subscriber, Journal, Resource） |
| FormRequest | 24 | 表单验证 |
| Policy | 0 | ❌ 已移除（24个Policy已删除，统一走 Spatie Permission 中间件） |
| Middleware | 7 | 中间件（HandleInertiaRequests, SeoMiddleware, LanguageMiddleware, AdminMiddleware, ActivityLogMiddleware, CheckMaintenanceMode, PageVisitMiddleware） |
| Observer | 0 | ❌ 已跳过（采用 Service 模式） |
| Event/Listener | 2/2 | 事件驱动（CommentCreated + SendCommentNotification, AdViewed + TrackAdViewed） |
| Notification | 3 | 通知系统（NewCommentNotification, NewSubscriberNotification, SystemNotification） |
| Command | 1 | Artisan命令（BackupCommand） |
| Mailable | 1 | 邮件模板（GenericEmail已创建） |
| Test | 9 | Feature测试（AuthTest: 9个测试用例） |

---

## 快速参考：Laravel Artisan 命令

```bash
# 创建迁移
php artisan make:migration create_posts_table

# 创建模型 + 迁移
php artisan make:model Post -m

# 创建 Controller
php artisan make:controller Admin/PostController

# 创建 Resource
php artisan make:resource V1/PostResource

# 创建 FormRequest
php artisan make:request Admin/StorePostRequest

# 创建 Policy
php artisan make:policy PostPolicy --model=Post

# 创建 Observer
php artisan make:observer PostObserver --model=Post

# 创建 Event
php artisan make:event CommentCreated

# 创建 Listener
php artisan make:listener SendCommentNotification --event=CommentCreated

# 创建 Notification
php artisan make:notification NewCommentNotification

# 创建 Middleware
php artisan make:middleware SeoMiddleware

# 创建 Command
php artisan make:command CleanupUnusedTags

# 创建 Service (手动)
# app/Services/PostService.php

# 创建 Seeder
php artisan make:seeder PostSeeder

# 创建 Factory
php artisan make:factory PostFactory --model=Post

# 运行迁移
php artisan migrate

# 回滚迁移
php artisan migrate:rollback

# 刷新迁移
php artisan migrate:refresh

# 查看路由
php artisan route:list

# 清除缓存
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 优化
php artisan optimize
```

---

## 快速参考：API路由模板

### Admin API 路由 (routes/api.php)

```php
<?php

use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
    // 文章管理
    Route::apiResource('posts', PostController::class);
    Route::get('posts/{post}/comments', [PostController::class, 'comments']);

    // 分类管理
    Route::apiResource('categories', CategoryController::class);

    // 标签管理
    Route::apiResource('tags', TagController::class);

    // 其他Admin路由...
});
```

### Public API 路由 (routes/api.php)

```php
<?php

use App\Http\Controllers\Api\V1\PostController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{post:slug}', [PostController::class, 'show']);
    // 其他公开路由...
});

// 公开订阅
Route::post('/subscribe', [SubscribeController::class, 'store']);

// 公开评论
Route::post('/comments', [CommentController::class, 'store']);
```
