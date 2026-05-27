# 后端功能开发清单

**重要说明：** 此文档为主要真实来源！其他文档请以此为准！

**最后更新：** 2026-05-27 (文档同步，所有问题已修复)
**Laravel版本：** 11 (精简模式)
**版本：** 2.2
**状态说明：** ✅ 已完成 | 🔄 进行中 | ⚠️ 待处理 | ❌ 阻塞

---

## 架构模式说明

> **重要：** 采用 **Inertia.js 现代单体架构**。前台和后台均使用 Inertia.js 将 Laravel 后端数据直接传递给 Vue 组件，保留 SPA 体验。API 路由独立保留供移动端 APP 和第三方系统使用。

| 端 | 渲染方式 | 数据获取方式 | 控制器目录 |
|----|----------|--------------|------------|
| **前台网站** | Inertia.js + Vue 3 | 通过 Inertia props 注入 | `App\Http\Controllers\Frontend\` |
| **后台管理** | Inertia.js + Vue 3 | 通过 Inertia props 注入 | `App\Http\Controllers\Admin\` |
| **移动端 APP** | - | 通过 RESTful API 获取 | `App\Http\Controllers\Api\V1\` |
| **第三方集成** | - | 通过 RESTful API 获取 | `App\Http\Controllers\Api\V1\` |

---

## Laravel 架构组件清单

### 核心架构模式

| 模式 | 目录 | 现状 | 说明 |
|------|------|:----:|------|
| **Service Layer** | `app/Services/` | ✅ 已有 | 业务逻辑封装 |
| **Repository Layer** | `app/Repositories/` | ⚠️ 待创建 | 数据访问层（轻量级模式） |
| **API Resource** | `app/Http/Resources/V1/` | ✅ 已有 | 响应格式化 |
| **FormRequest** | `app/Http/Requests/` | ⚠️ 待创建 | 表单验证 |
| **Policy** | `app/Policies/` | ⚠️ 待创建 | 授权策略 |
| **Observer** | `app/Observers/` | ⚠️ 待创建 | 模型事件 |
| **Event/Listener** | `app/Events/`, `app/Listeners/` | ⚠️ 待创建 | 事件驱动 |
| **Notification** | `app/Notifications/` | ⚠️ 待创建 | 通知系统 |
| **Middleware** | `app/Http/Middleware/` | ⚠️ 待创建 | 中间件 |
| **Command** | `app/Console/Commands/` | ⚠️ 待创建 | Artisan命令 |
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
| AUTH-08 | 创建 RoleResource | Resource | ⚠️ 待处理 | AUTH-07 | app/Http/Resources/V1/ |
| AUTH-09 | 创建 StoreRoleRequest | FormRequest | ✅ 已完成 | AUTH-07 | 验证规则 |
| AUTH-10 | 角色-权限分配API | Controller | ⚠️ 待处理 | AUTH-05, AUTH-07 | syncPermissions |
| AUTH-11 | users表添加 points 字段 | Migration | ✅ 已完成 | - | unsignedBigInteger |
| AUTH-12 | users表添加 last_login_at | Migration | ✅ 已完成 | - | timestamp nullable |

### 2. 设置与配置

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| SET-01 | 创建 settings 表迁移 | Migration | ✅ 已完成 | - | key-value结构 |
| SET-02 | 创建 Setting Model | Model | ⚠️ 待处理 | SET-01 | JSON Cast |
| SET-03 | 创建 SettingService | Service | ⚠️ 待处理 | SET-02 | 单例+缓存 |
| SET-04 | 创建 SettingController | Controller | ⚠️ 待处理 | SET-03 | Admin API |
| SET-05 | 创建 menus 表迁移 | Migration | ✅ 已完成 | - | parent_id层级 |
| SET-06 | 创建 Menu Model + Builder | Model | ⚠️ 待处理 | SET-05 | 递归关系 |
| SET-07 | 创建 MenuController | Controller | ⚠️ 待处理 | SET-06 | 树形CRUD |
| SET-08 | 菜单缓存 | Cache | ⚠️ 待处理 | SET-07 | Cache::remember |

### 3. 内容管理 - 核心

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| CONT-01 | 创建 categories 表迁移 | Migration | ✅ 已完成 | - | parent_id, slug |
| CONT-02 | 创建 Category Model | Model | ⚠️ 待处理 | CONT-01 | 树形关系 |
| CONT-03 | 创建 CategoryController | Controller | ⚠️ 待处理 | CONT-02 | Admin API |
| CONT-04 | 创建 CategoryResource | Resource | ⚠️ 待处理 | CONT-03 | API响应 |
| CONT-05 | 创建 tags 表迁移 | Migration | ✅ 已完成 | - | name, slug, usage_count |
| CONT-06 | 创建 Tag Model | Model | ⚠️ 待处理 | CONT-05 | 多态关联 |
| CONT-07 | 创建 TagController | Controller | ⚠️ 待处理 | CONT-06 | Admin API |
| CONT-08 | 创建 TagResource | Resource | ⚠️ 待处理 | CONT-07 | API响应 |
| CONT-09 | 安装 Spatie Media Library | Package | ⚠️ 待处理 | - | composer |
| CONT-10 | 创建 media 表迁移 | Migration | ✅ 已完成 | CONT-09 | Spatie表结构 |
| CONT-11 | 创建 Medium Model | Model | ⚠️ 待处理 | CONT-10 | InteractsWithMedia |
| CONT-12 | 创建 MediaController | Controller | ⚠️ 待处理 | CONT-11 | 上传处理 |
| CONT-13 | 创建 posts 表迁移 | Migration | ✅ 已完成 | CONT-01, AUTH-05 | 完整字段 |
| CONT-14 | 创建 Post Model | Model | ⚠️ 待处理 | CONT-13 | fillable/casts/relations |
| CONT-15 | 创建 PostService | Service | ⚠️ 待处理 | CONT-14 | 业务逻辑 |
| CONT-16 | 创建 PostController CRUD | Controller | ⚠️ 待处理 | CONT-15 | Admin API |
| CONT-17 | 创建 PostResource | Resource | ⚠️ 待处理 | CONT-16 | API响应 |
| CONT-18 | 创建 StorePostRequest | FormRequest | ⚠️ 待处理 | CONT-16 | 验证规则 |
| CONT-19 | 创建 PostPolicy | Policy | ⚠️ 待处理 | CONT-14 | 授权逻辑 |
| CONT-20 | 创建 PostObserver | Observer | ⚠️ 待处理 | CONT-14 | 模型事件 |
| CONT-21 | 安装 league/commonmark | Package | ⚠️ 待处理 | - | Markdown解析 |

### 4. 内容管理 - 扩展

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| CONT-22 | 创建 videos 表迁移 | Migration | ✅ 已完成 | AUTH-05 | platform, thumbnail |
| CONT-23 | 创建 Video Model + Service | Model+Service | ⚠️ 待处理 | CONT-22 | 标准CRUD |
| CONT-24 | 创建 VideoController | Controller | ⚠️ 待处理 | CONT-23 | Admin API |
| CONT-25 | 创建 VideoResource | Resource | ⚠️ 待处理 | CONT-24 | API响应 |
| CONT-26 | 创建 projects 表迁移 | Migration | ✅ 已完成 | AUTH-05 | title, description |
| CONT-27 | 创建 Project Model + Service | Model+Service | ⚠️ 待处理 | CONT-26 | 标准CRUD |
| CONT-28 | 创建 ProjectController | Controller | ⚠️ 待处理 | CONT-27 | Admin API |
| CONT-29 | 创建 ProjectResource | Resource | ⚠️ 待处理 | CONT-28 | API响应 |
| CONT-30 | 创建 resources 表迁移 | Migration | ✅ 已完成 | AUTH-05 | file_path, download_count |
| CONT-31 | 创建 Resource Model | Model | ⚠️ 待处理 | CONT-30 | 标准CRUD |
| CONT-32 | 创建 ResourceController | Controller | ⚠️ 待处理 | CONT-31 | Admin API |
| CONT-33 | 创建 ResourceResource | Resource | ⚠️ 待处理 | CONT-32 | API响应 |
| CONT-34 | 创建 journals 表迁移 | Migration | ✅ 已完成 | AUTH-05 | mood, weather JSON |
| CONT-35 | 创建 Journal Model | Model | ⚠️ 待处理 | CONT-34 | JSON Cast |
| CONT-36 | 创建 JournalController | Controller | ⚠️ 待处理 | CONT-35 | Admin API |
| CONT-37 | 创建 JournalResource | Resource | ⚠️ 待处理 | CONT-36 | API响应 |

---

## 优先级：中（重要功能）

### 5. 用户管理

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| USER-01 | 创建 user_levels 表迁移 | Migration | ✅ 已完成 | - | level, icon, color, benefits |
| USER-02 | 创建 UserLevel Model | Model | ⚠️ 待处理 | USER-01 | JSON Cast benefits |
| USER-03 | 创建 UserLevelController | Controller | ⚠️ 待处理 | USER-02 | Admin API |
| USER-04 | 创建 UserLevelResource | Resource | ⚠️ 待处理 | USER-03 | API响应 |
| USER-05 | 创建 user_points_history 表 | Migration | ✅ 已完成 | - | points change log |
| USER-06 | 创建 UserObserver | Observer | ⚠️ 待处理 | AUTH-11 | 积分更新监听 |
| USER-07 | 创建 subscribers 表迁移 | Migration | ✅ 已完成 | - | email, subscribed_at, status |
| USER-08 | 创建 Subscriber Model | Model | ⚠️ 待处理 | USER-07 | Notifiable |
| USER-09 | 创建 SubscriberController | Controller | ⚠️ 待处理 | USER-08 | Admin API |
| USER-10 | 创建 public 订阅API | Controller | ⚠️ 待处理 | USER-09 | /api/subscribe |
| USER-11 | 创建 author_profiles 表迁移 | Migration | ✅ 已完成 | AUTH-05 | user_id唯一 |
| USER-12 | 创建 AuthorProfile Model | Model | ⚠️ 待处理 | USER-11 | JSON Cast |
| USER-13 | 创建 AuthorProfileController | Controller | ⚠️ 待处理 | USER-12 | Admin API |

### 6. SEO与国际化

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| SEO-01 | 创建 page_seo 表迁移 | Migration | ✅ 已完成 | - | route_name唯一 |
| SEO-02 | 创建 PageSeo Model | Model | ⚠️ 待处理 | SEO-01 | 标准Model |
| SEO-03 | 创建 SeoController | Controller | ⚠️ 待处理 | SEO-02 | Admin API |
| SEO-04 | 创建 SeoMiddleware | Middleware | ⚠️ 待处理 | SEO-03 | 动态加载SEO |
| SEO-05 | 创建 languages 表迁移 | Migration | ✅ 已完成 | - | code, name, is_default |
| SEO-06 | 创建 Language Model | Model | ⚠️ 待处理 | SEO-05 | 标准Model |
| SEO-07 | 创建 translations 表迁移 | Migration | ✅ 已完成 | SEO-05 | key, locale, value |
| SEO-08 | 创建 Translation Model | Model | ⚠️ 待处理 | SEO-07 | 标准Model |
| SEO-09 | 创建 I18nController | Controller | ⚠️ 待处理 | SEO-06, SEO-08 | Admin API |
| SEO-10 | 创建语言切换Middleware | Middleware | ⚠️ 待处理 | SEO-06 | locale设置 |
| SEO-11 | 创建导出JSON命令 | Command | ⚠️ 待处理 | SEO-09 | vue-i18n同步 |

### 7. 社交与互动

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| SOC-01 | 创建 social_links 表迁移 | Migration | ✅ 已完成 | - | platform, url, icon |
| SOC-02 | 创建 SocialLink Model | Model | ⚠️ 待处理 | SOC-01 | 标准Model |
| SOC-03 | 创建 SocialLinkController | Controller | ⚠️ 待处理 | SOC-02 | Admin API |
| SOC-04 | 创建 public 社交链接API | Controller | ⚠️ 待处理 | SOC-03 | /api/social-links |
| SOC-05 | 创建 comments 表迁移 | Migration | ✅ 已完成 | AUTH-05 | 多态关联 |
| SOC-06 | 创建 Comment Model | Model | ⚠️ 待处理 | SOC-05 | MorphTo |
| SOC-07 | 创建 CommentService | Service | ⚠️ 待处理 | SOC-06 | 业务逻辑 |
| SOC-08 | 创建 CommentController | Controller | ⚠️ 待处理 | SOC-07 | Admin + Public API |
| SOC-09 | 创建 CommentResource | Resource | ⚠️ 待处理 | SOC-08 | API响应 |
| SOC-10 | 创建 CommentCreated Event | Event | ⚠️ 待处理 | SOC-06 | 事件类 |
| SOC-11 | 创建 SendCommentNotification Listener | Listener | ⚠️ 待处理 | SOC-10 | 通知发送 |
| SOC-12 | 创建 comments 索引 | Migration | ⚠️ 待处理 | SOC-05 | 多态查询优化 |

### 8. 邮件与通知

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| MAIL-01 | 创建 mail_configs 表迁移 | Migration | ✅ 已完成 | - | driver, host, credentials |
| MAIL-02 | 创建 MailConfig Model | Model | ⚠️ 待处理 | MAIL-01 | 标准Model |
| MAIL-03 | 创建 MailConfigController | Controller | ⚠️ 待处理 | MAIL-02 | Admin API |
| MAIL-04 | 创建测试邮件功能 | Mailable | ⚠️ 待处理 | MAIL-03 | Mailable类 |
| MAIL-05 | 创建 email_templates 表迁移 | Migration | ✅ 已完成 | - | subject, body, variables |
| MAIL-06 | 创建 EmailTemplate Model | Model | ⚠️ 待处理 | MAIL-05 | 标准Model |
| MAIL-07 | 创建 EmailTemplateController | Controller | ⚠️ 待处理 | MAIL-06 | Admin API |
| MAIL-08 | 创建通用 Mailable | Mailable | ⚠️ 待处理 | MAIL-07 | 变量替换 |
| MAIL-09 | 创建 notifications 表迁移 | Migration | ✅ Laravel内置 | AUTH-05 | 数据库通知 |
| MAIL-10 | 创建通知Model | Model | ⚠️ 待处理 | MAIL-09 | Notifiable |
| MAIL-11 | 创建 NotificationController | Controller | ⚠️ 待处理 | MAIL-10 | Admin API |
| MAIL-12 | 创建 NewCommentNotification | Notification | ⚠️ 待处理 | SOC-10 | 邮件/数据库通知 |

---

## 优先级：低（辅助功能）

### 9. 广告管理

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| AD-01 | 创建 ad_positions 表迁移 | Migration | ✅ 已完成 | - | key, name, size |
| AD-02 | 创建 AdPosition Model | Model | ⚠️ 待处理 | AD-01 | hasMany advertisements |
| AD-03 | 创建 Advertisement Model | Model | ⚠️ 待处理 | AD-01 | BelongsTo AdPosition |
| AD-04 | 创建 AdvertisementController | Controller | ⚠️ 待处理 | AD-03 | Admin API |
| AD-05 | 创建广告轮换逻辑 | Service | ⚠️ 待处理 | AD-04 | 权重随机 |
| AD-06 | 创建 AdViewed Event | Event | ⚠️ 待处理 | AD-05 | 展示追踪 |
| AD-07 | 创建 interactions 表迁移 | Migration | ✅ 已完成 | - | 多态关联 |

### 10. 系统与维护

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| SYS-01 | 安装 spatie/laravel-activitylog | Package | ⚠️ 待处理 | - | composer |
| SYS-02 | 配置 activitylog | Config | ⚠️ 待处理 | SYS-01 | Activityable Trait |
| SYS-03 | 创建 LogController | Controller | ⚠️ 待处理 | SYS-02 | Admin API |
| SYS-04 | 创建日志清理命令 | Command | ⚠️ 待处理 | SYS-03 | Schedule |
| SYS-05 | 安装 spatie/laravel-backup | Package | ⚠️ 待处理 | - | composer |
| SYS-06 | 创建 BackupController | Controller | ⚠️ 待处理 | SYS-05 | Admin API |
| SYS-07 | 创建 RestoreController | Controller | ⚠️ 待处理 | SYS-05 | Admin API |
| SYS-08 | 创建定时备份任务 | Command | ⚠️ 待处理 | SYS-06 | Schedule |
| SYS-09 | 创建 CleanupUnusedTagsCommand | Command | ⚠️ 待处理 | CONT-07 | 清理未使用标签 |

### 11. 额外迁移（已完成）

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| EXT-01 | 创建 footer_links 表迁移 | Migration | ✅ 已完成 | - | 页脚链接 |
| EXT-02 | 创建 themes 表迁移 | Migration | ✅ 已完成 | - | 主题配置 |
| EXT-03 | 创建 visits 表迁移 | Migration | ✅ 已完成 | - | 多态访问记录 |
| EXT-04 | 创建 backups 表迁移 | Migration | ✅ 已完成 | - | 备份记录 |
| EXT-05 | 创建 admin_logs 表迁移 | Migration | ✅ 已完成 | - | 管理员日志 |
| EXT-06 | 创建 seo 表迁移 | Migration | ✅ 已完成 | - | SEO配置 key-value |
| EXT-07 | 创建 ad_interactions 表迁移 | Migration | ✅ 已完成 | AD-01 | 广告互动追踪 |

### 12. Inertia.js 前端集成

| ID | 任务 | 组件类型 | 状态 | 依赖 | Laravel实现 |
|----|------|----------|:----:|------|------------|
| FE-01 | 安装 Inertia.js 后端扩展 | Package | ✅ 已完成 | - | composer require inertiajs/inertia-laravel |
| FE-02 | 创建 HandleInertiaRequests 中间件 | Middleware | ✅ 已完成 | FE-01 | php artisan inertia:middleware |
| FE-03 | 配置 Inertia 根模板 app.blade.php | Template | ✅ 已完成 | FE-01 | @inertiaHead + @inertia |
| FE-04 | 创建 Frontend HomeController | Controller | ✅ 已完成 | CONT-15 | Inertia::render('front/Home') |
| FE-05 | 创建 Frontend BlogController | Controller | ✅ 已完成 | CONT-15 | Inertia::render('front/Blog') |
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
| 高 | 44 | 40 | 1 | 3 |
| 中 | 25 | 10 | 0 | 15 |
| 低 | 23 | 9 | 0 | 14 |
| **总计** | **92** | **59** | **1** | **32** |

> **注：** 截至 2026-05-27，所有数据库迁移已完成，25个 Seeder 已优化完毕，约200条高质量模拟数据已填充。
> 
> **认证与权限系统已完成：**
> - Laravel Sanctum 已安装配置
> - Spatie Laravel Permission 已安装配置
> - User 模型已添加 HasApiTokens 和 HasRoles Trait
> - Role 和 Permission 模型已创建
> - RolePolicy 和 UserPolicy 已创建
> - StoreRoleRequest、UpdateRoleRequest、StoreUserRequest、UpdateUserRequest 已创建
> - PermissionSeeder 已创建并配置到 DatabaseSeeder
> - User 模型的 last_login_at 字段已存在
> 
> **Inertia.js 架构迁移已完成：**
> - 后端扩展包已安装配置
> - HandleInertiaRequests 中间件已创建
> - 根模板 app.blade.php 已配置
> - 所有前台和后台控制器已改用 Inertia::render() 返回数据
> - 资源路由已添加 `->names()` 配置，Ziggy 路由助手正常工作
> - 后台菜单数据通过 HandleInertiaRequests 中间件共享
> - 前台页面 SEO 数据空值问题已修复
> - 后台页面表单提交逻辑优化进行中（Users、Roles、Journals 已完成）
> - 前端 Vue 组件已改为通过 props 接收数据

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
| 14 | `2026_05_23_080113_create_admin_logs_table.php` | admin_logs | 管理员日志表 |
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

## 组件类型统计

| 组件类型 | 数量 | 说明 |
|----------|:----:|------|
| Migration | 20 | 数据库表结构 |
| Model | 20 | Eloquent模型 |
| Controller | 20 | 控制器 |
| Service | 7 | 业务逻辑 |
| Resource | 10 | API响应格式化 |
| FormRequest | 2 | 表单验证 |
| Policy | 2 | 授权策略 |
| Observer | 3 | 模型事件 |
| Event | 3 | 事件类 |
| Listener | 2 | 事件监听 |
| Middleware | 2 | 中间件 |
| Command | 4 | Artisan命令 |
| Notification | 2 | 通知类 |
| Mailable | 2 | 邮件模板 |
| Package | 5 | 依赖包安装 |
| Config/Other | 4 | 配置类 |

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
