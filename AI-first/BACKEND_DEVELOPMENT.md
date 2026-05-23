# 后端功能开发路线图

**项目名称：** ARCHYX - Laravel Vue.js 混合应用
**最后更新：** 2026-05-23
**版本：** 1.0
**Laravel版本：** 10+

---

## 1. 项目概述

### 1.1 技术架构
- **后端框架：** Laravel 10+
- **前台展示：** Laravel Blade 模板 + Vue 3 组件（服务端渲染）
- **后台管理：** Laravel Blade 模板 + Vue 3 组件（服务端渲染）
- **数据库：** MySQL 8.0+ with Eloquent ORM
- **认证系统：** Laravel Sanctum + Spatie Permission (RBAC)
- **缓存系统：** Redis 性能优化

### 1.2 架构模式说明

| 端 | 渲染方式 | 数据获取方式 | 说明 |
|----|----------|--------------|------|
| **前台网站** | Laravel Blade 模板 | 直接查询数据库 | 服务端渲染，SEO友好 |
| **后台管理** | Laravel Blade 模板 | 直接查询数据库 | 服务端渲染，安全性高 |
| **移动端 APP** | - | 通过 API 获取 | 外部客户端调用 `/api/v1/*` |
| **第三方集成** | - | 通过 API 获取 | Webhook 和 REST API |

> **重要说明：** 前台和后台均采用 Laravel 传统服务端渲染模式，使用 Blade 模板引擎，数据直接从数据库查询，**不使用 Inertia.js**。仅移动端 APP 和第三方系统通过 RESTful API 获取数据。

### 1.3 Laravel 设计原则遵循

| 原则 | 实现方式 | 项目现状 |
|------|----------|----------|
| **单一职责原则 (SRP)** | Controller只做路由分发，业务逻辑在Service层 | ✅ 已采用 |
| **依赖注入 (DI)** | 通过构造函数注入Service、Repository | ✅ 已采用 |
| **API版本控制** | `App\Http\Controllers\Api\V1\` 目录隔离 | ✅ 已采用 |
| **资源转换层** | API Resource格式化响应 | ✅ 已采用 |
| **表单验证** | FormRequest类处理验证逻辑 | ⚠️ 待完善 |
| **策略授权** | Policy类管理模型权限 | ⚠️ 待完善 |
| **观察者模式** | Observer处理模型事件 | ⚠️ 待完善 |
| **事务支持** | DB::transaction() 保证数据一致性 | ✅ 已采用 |

### 1.4 功能模块概要

| 模块 | 页面数 | 说明 |
|------|:------:|------|
| **内容管理** | Posts, Videos, Projects, Resources, Journals | 文章、视频、项目、资源下载、日记管理 |
| **用户管理** | Users, UserDetail, UserLevels, Subscribers | 用户管理、积分系统、订阅者 |
| **系统设置** | Settings, SEO, I18n, Media, Mail Config | 网站配置、SEO、国际化、媒体管理 |
| **系统维护** | Roles, Notifications, Logs, Backup | 权限管理、通知、审计日志、备份恢复 |
| **前台展示** | Home, Blog, Videos, Projects, Author | 首页、博客列表、视频页面、项目展示、作者页 |

### 1.5 路由结构

| 路由类型 | 前缀 | 控制器目录 | 说明 |
|----------|------|------------|------|
| **前台路由** | `/` | `App\Http\Controllers\Frontend\` | Blade 模板渲染 |
| **后台路由** | `/admin` | `App\Http\Controllers\Admin\` | Blade 模板渲染，需登录 |
| **API路由** | `/api/v1` | `App\Http\Controllers\Api\V1\` | JSON响应，供APP/第三方使用 |
| **公开API** | `/api` | `App\Http\Controllers\Api\V1\` | 无需认证的公开接口 |

---

## 2. 模块开发清单

### 2.1 内容管理模块

#### 2.1.1 文章 (Posts)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ⚠️ 待处理 | posts表 | Model使用$fillable/$casts |
| API CRUD接口 (Admin) | 高 | ⚠️ 待处理 | /api/admin/posts | API V1版本控制 |
| FormRequest验证 | 高 | ⚠️ 待处理 | StorePostRequest | 独立验证类 |
| API Resource | 高 | ⚠️ 待处理 | PostResource | 统一响应格式 |
| 分类/标签关联 | 高 | ⚠️ 待处理 | 多态关联taggables | morphToMany |
| 搜索与筛选 | 中 | ⚠️ 待处理 | Full-text search | Scout集成 |
| Markdown解析 | 中 | ⚠️ 待处理 | league/commonmark | 自定义Blade组件 |
| 封面图上传 | 中 | ⚠️ 待处理 | Spatie Media Library |MediaLibraryTrait |
| SEO字段 | 中 | ⚠️ 待处理 | seo_meta关联 | SEO观察者 |

#### 2.1.2 视频 (Videos)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ⚠️ 待处理 | videos表 | 标准Eloquent模型 |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/videos | Resource+Service模式 |
| 平台集成 | 中 | ⚠️ 待处理 | YouTube/Bilibili | 自定义Service封装 |
| 缩略图处理 | 中 | ⚠️ 待处理 | 自动获取/手动 | Intervention Image |

#### 2.1.3 项目 (Projects)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ⚠️ 待处理 | projects表 | 标准Model |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/projects | Repository模式 |
| 图片画廊 | 中 | ⚠️ 待处理 | 多图关联 | Spatie Media多集合 |

#### 2.1.4 资源 (Resources)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ⚠️ 待处理 | resources表 | download_count字段 |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/resources | 标准CRUD |
| 下载统计 | 中 | ⚠️ 待处理 | 访问时递增 | 事件监听 |

#### 2.1.5 日记 (Journals)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ⚠️ 待处理 | journals表 | mood/weather JSON |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/journals | 标准CRUD |
| 日记专属字段 | 中 | ⚠️ 待处理 | music_link等 | JSON Cast |

#### 2.1.6 分类 (Categories)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ⚠️ 待处理 | categories表 | 层级结构 |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/categories | 树形构建器 |
| 树形结构 | 中 | ⚠️ 待处理 | 递归关系 | NestedSetModel |

#### 2.1.7 标签 (Tags)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ⚠️ 待处理 | tags表 | usage_count |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/tags | 标准CRUD |
| 自动清理 | 低 | ⚠️ 待处理 | 未使用标签删除 | Artisan命令 |

---

### 2.2 用户与认证模块

#### 2.2.1 用户管理
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| users表扩展 | 高 | ⚠️ 待处理 | role_id, points | 新迁移文件 |
| User Policy | 高 | ⚠️ 待处理 | 授权逻辑 | Policy类 |
| 用户资料更新 | 高 | ⚠️ 待处理 | /api/admin/users/{id} | FormRequest验证 |
| 密码重置 | 高 | ⚠️ 待处理 | Laravel内置 | 邮件通知 |
| 头像上传 | 中 | ⚠️ 待处理 | Spatie Media | Avatar集合 |
| 积分系统 | 中 | ⚠️ 待处理 | 积分API | Observer更新 |
| 活动追踪 | 低 | ⚠️ 待处理 | last_login_at | LoginListener |

#### 2.2.2 订阅者 (Newsletter)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ⚠️ 待处理 | subscribers表 | email唯一性 |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/subscribers | 标准CRUD |
| 订阅控制器 | 中 | ⚠️ 待处理 | /api/subscribe | 邮件验证 |
| CSV导出 | 低 | ⚠️ 待处理 | Excel导出 | Laravel Excel |

#### 2.2.3 用户等级 (VIP)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ⚠️ 待处理 | user_levels表 | benefits JSON |
| 等级分配逻辑 | 高 | ⚠️ 待处理 | 自动升级 | Observer/Job |
| 等级权益API | 中 | ⚠️ 待处理 | 折扣计算 | 计算型Service |
| 积分历史 | 中 | ⚠️ 待处理 | user_points_history | 流水记录 |

#### 2.2.4 RBAC权限系统
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| Spatie Permission | 高 | ⚠️ 待处理 | 完整配置 | Trait + 中间件 |
| Role Policy | 高 | ⚠️ 待处理 | 角色授权 | 自定义Gate |
| Permission CRUD | 高 | ⚠️ 待处理 | /api/admin/permissions | 标准CRUD |
| 角色-权限UI | 高 | ⚠️ 待处理 | 同步API | JS树形组件 |

---

### 2.3 系统配置模块

#### 2.3.1 网站设置
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ⚠️ 待处理 | settings表 | key-value |
| Setting Service | 高 | ⚠️ 待处理 | 配置获取 | 单例+缓存 |
| 网站配置API | 高 | ⚠️ 待处理 | /api/admin/settings | 标准CRUD |
| 功能开关 | 中 | ⚠️ 待处理 | feature_* | 配置缓存 |

#### 2.3.2 SEO管理器
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ⚠️ 待处理 | page_seo表 | route_name唯一 |
| SEO Controller | 高 | ⚠️ 待处理 | /api/admin/seo | 标准CRUD |
| 动态SEO中间件 | 中 | ⚠️ 待处理 | 按路由加载 | Middleware |
| Schema.org | 中 | ⚠️ 待处理 | JSON-LD | Seeder初始化 |

#### 2.3.3 国际化 (I18n)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| languages表 | 高 | ⚠️ 待处理 | 语言配置 |Seeder |
| translations表 | 高 | ⚠️ 待处理 | 翻译存储 | (key, locale, value) |
| I18n Controller | 高 | ⚠️ 待处理 | /api/admin/i18n | 标准CRUD |
| JSON导出 | 中 | ⚠️ 待处理 | vue-i18n同步 | Artisan命令 |
| 语言切换中间件 | 中 | ⚠️ 待处理 | locale设置 | Middleware |

#### 2.3.4 媒体库
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| Spatie Media Library | 高 | ⚠️ 待处理 | 完整配置 | Trait |
| 媒体上传API | 高 | ⚠️ 待处理 | /api/admin/media/upload | 异步处理 |
| 图片优化 | 中 | ⚠️ 待处理 | 缩略图 | Imagecache |
| 文件验证 | 中 | ⚠️ 待处理 | 白名单 | FormRequest |

#### 2.3.5 邮件配置
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| mail_configs表 | 高 | ⚠️ 待处理 | 多配置 | is_default |
| Mailable类 | 中 | ⚠️ 待处理 | 邮件模板 | Mailable |
| 测试邮件 | 中 | ⚠️ 待处理 | 发送测试 | Queue邮件 |

#### 2.3.6 前台菜单
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| menus表 | 高 | ⚠️ 待处理 | 层级结构 | parent_id |
| Menu Builder | 中 | ⚠️ 待处理 | 递归构建 | Cache缓存 |

---

### 2.4 互动社交模块

#### 2.4.1 评论
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| comments表 | 高 | ⚠️ 待处理 | 多态关联 | commentable |
| Comment Service | 高 | ⚠️ 待处理 | 业务逻辑 | 独立Service |
| 审核流程 | 中 | ⚠️ 待处理 | require_approval | 事件监听 |
| 嵌套回复 | 低 | ⚠️ 待处理 | parent_id | 递归关系 |

#### 2.4.2 广告管理
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| ad_positions表 | 高 | ⚠️ 待处理 | 广告位 | position_key唯一 |
| advertisements表 | 高 | ⚠️ 待处理 | 广告内容 | position_id |
| 广告轮换 | 中 | ⚠️ 待处理 | 权重选择 | Seeder |
| 追踪事件 | 中 | ⚠️ 待处理 | view/click | Event |

#### 2.4.3 通知
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| notifications表 | 高 | ⚠️ 待处理 | 数据库通知 | Notifiable |
| 通知Service | 中 | ⚠️ 待处理 | 发送逻辑 | Notification类 |
| 批量操作 | 低 | ⚠️ 待处理 | 全部已读 | Chunk处理 |

#### 2.4.4 社交链接
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| social_links表 | 高 | ⚠️ 待处理 | 平台配置 | platform唯一 |
| 公开API | 中 | ⚠️ 待处理 | /api/social-links | 缓存响应 |

#### 2.4.5 作者资料
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| author_profiles表 | 高 | ⚠️ 待处理 | 用户扩展 | user_id唯一 |
| 技能JSON字段 | 中 | ⚠️ 待处理 | skills数组 | JSON Cast |

#### 2.4.6 互动数据
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| interactions表 | 中 | ⚠️ 待处理 | 多态关联 | 聚合查询 |
| 每日统计 | 低 | ⚠️ 待处理 | Aggregate Job | Schedule |

---

### 2.5 系统维护模块

#### 2.5.1 审计日志
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| Spatie Activitylog | 高 | ⚠️ 待处理 | 审计追踪 | Trait |
| 日志查询API | 中 | ⚠️ 待处理 | /api/admin/logs | 筛选+分页 |
| 日志清理 | 低 | ⚠️ 待处理 | 删除旧日志 | Schedule |

#### 2.5.2 备份与恢复
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| Spatie Backup | 高 | ⚠️ 待处理 | 完整备份 | 配置 |
| 备份命令 | 高 | ⚠️ 待处理 | /api/admin/backup | Artisan |
| 恢复命令 | 高 | ⚠️ 待处理 | /api/admin/restore | 确认机制 |
| 定时备份 | 中 | ⚠️ 待处理 | cron配置 | Schedule |

#### 2.5.3 邮件模板
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| email_templates表 | 高 | ⚠️ 待处理 | 模板存储 | Mailable |
| 变量解析 | 中 | ⚠️ 待处理 | {{var}} | Str::replace |
| 预览功能 | 低 | ⚠️ 待处理 | 渲染预览 | 临时数据 |

---

## 3. Laravel 架构规范

### 3.1 目录结构 (遵循项目现有结构)

```
app/
├── Console/
│   └── Commands/                    # Artisan命令
│       ├── CleanupUnusedTags.php
│       └── GenerateSitemap.php
├── Events/
│   ├── CommentCreated.php
│   └── PostPublished.php
├── Exceptions/
│   └── Handler.php
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                   # 管理后台控制器 (现有)
│   │   │   ├── PostController.php
│   │   │   └── ...
│   │   ├── Api/
│   │   │   └── V1/                  # API V1版本控制器 (现有)
│   │   │       ├── PostController.php
│   │   │       └── ...
│   │   └── Frontend/                # 前台页面控制器
│   │       ├── HomeController.php
│   │       ├── BlogController.php
│   │       └── ...
│   ├── Middleware/
│   │   ├── AdminMiddleware.php
│   │   └── SeoMiddleware.php
│   ├── Requests/
│   │   ├── Admin/                   # Admin表单验证
│   │   │   ├── StorePostRequest.php
│   │   │   └── UpdatePostRequest.php
│   │   └── Api/                     # API表单验证
│   │       └── StoreCommentRequest.php
│   └── Resources/
│       └── V1/                      # API资源转换 (现有)
│           ├── PostResource.php
│           └── ...
├── Listeners/
│   ├── IncrementPostViews.php
│   └── SendCommentNotification.php
├── Models/                          # (现有)
│   ├── Post.php
│   └── ...
├── Notifications/
│   ├── NewCommentNotification.php
│   └── PasswordResetNotification.php
├── Observers/
│   ├── PostObserver.php
│   └── UserObserver.php
├── Policies/                        # 授权策略
│   ├── PostPolicy.php
│   ├── UserPolicy.php
│   └── RolePolicy.php
├── Providers/
│   ├── AppServiceProvider.php
│   └── AuthServiceProvider.php
└── Services/                        # 业务逻辑层 (现有)
    ├── PostService.php
    ├── CommentService.php
    └── ...

resources/
├── views/
│   ├── layouts/                    # 布局模板
│   │   ├── frontend.blade.php      # 前台布局
│   │   └── admin.blade.php         # 后台布局
│   ├── frontend/                   # 前台视图
│   │   ├── home.blade.php
│   │   ├── blog/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   └── ...
│   └── admin/                      # 后台视图
│       ├── dashboard.blade.php
│       ├── posts/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       └── ...
└── js/                             # Vue 组件（用于增强交互）
    ├── components/
    └── app.js
```

> **架构说明：**
> - **前台**和**后台**均使用 Laravel Blade 模板进行服务端渲染，数据通过 Controller 直接从数据库查询
> - **API** 仅供移动端 APP 和第三方系统调用，使用 `Api/V1/` 目录下的控制器和 `Resources/V1/` 资源转换
> - Vue 组件仅用于页面内的交互增强（如表单验证、动态加载等），不用于页面级渲染

---

### 3.2 核心模式

#### 3.2.1 Service Layer (服务层) ✅ 已采用

```php
// app/Services/PostService.php
class PostService
{
    public function __construct(
        protected Post $post,
        protected Tag $tag
    ) {}

    public function getPaginatedPosts(int $perPage, array $filters): LengthAwarePaginator
    {
        return Post::query()
            ->with(['author', 'category', 'tags'])
            ->when($filters['category'] ?? null, fn($q, $slug) => ...)
            ->latest('published_at')
            ->paginate($perPage);
    }
}
```

#### 3.2.2 API Resource (资源转换) ✅ 已采用

```php
// app/Http/Resources/V1/PostResource.php
class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => new UserResource($this->whenLoaded('author')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
```

#### 3.2.3 FormRequest (表单验证)

```php
// app/Http/Requests/Admin/StorePostRequest.php
class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Post::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'unique:posts,slug'],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'tags' => ['array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }
}
```

#### 3.2.4 Policy (授权策略)

```php
// app/Policies/PostPolicy.php
class PostPolicy
{
    public function create(User $user): bool
    {
        return $user->can('create posts');
    }

    public function update(User $user, Post $post): bool
    {
        return $user->can('edit posts') || $user->id === $post->author_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->can('delete posts');
    }
}
```

#### 3.2.5 Observer (观察者)

```php
// app/Observers/PostObserver.php
class PostObserver
{
    public function created(Post $post): void
    {
        // 清除缓存
        Cache::forget('recent_posts');
    }

    public function published(Post $post): void
    {
        if ($post->wasChanged('status') && $post->status === 'published') {
            event(new PostPublished($post));
        }
    }
}
```

#### 3.2.6 Event & Listener (事件监听)

```php
// app/Events/CommentCreated.php
class CommentCreated
{
    use SerializesModels;

    public function __construct(public Comment $comment) {}
}

// app/Listeners/SendCommentNotification.php
class SendCommentNotification
{
    public function handle(CommentCreated $event): void
    {
        $event->comment->post->author->notify(
            new NewCommentNotification($event->comment)
        );
    }
}
```

---

## 4. 数据库迁移规范

### 4.1 迁移文件命名

```
格式: {timestamp}_{action}_{table_name}.php
示例:
- 2024_01_01_000001_create_posts_table.php
- 2024_01_02_000002_add_role_id_to_users_table.php
- 2024_01_03_000003_create_post_tag_table.php
```

### 4.2 字段类型规范

| 字段类型 | 使用场景 | 示例 |
|----------|----------|------|
| bigint | 主键、外键 | `bigInteger('user_id')` |
| varchar | 短文本 | `string('name', 255)` |
| text | 长文本 | `text('content')` |
| json | 结构化数据 | `json('settings')` |
| boolean | 开关状态 | `boolean('is_active')` |
| timestamp | 时间戳 | `timestamp('published_at')` |
| unsignedBigInteger | 计数/积分 | `unsignedBigInteger('points')` |

### 4.3 索引规范

| 索引类型 | 使用场景 | Laravel |
|----------|----------|---------|
| primary | 主键 | `$table->id()` |
| unique | 唯一约束 | `$table->unique('email')` |
| index | 普通索引 | `$table->index('status')` |
| foreign | 外键关联 | `$table->foreign('user_id')->references('id')->on('users')` |
| morph | 多态关联 | `morphs('commentable')` |

---

## 5. API设计规范

### 5.1 版本控制 ✅ 已采用

```
/api/v1/posts
/api/v1/admin/posts
/api/v2/posts  (未来版本迭代)
```

### 5.2 RESTful路由

| 方法 | 路由 | Controller方法 | 描述 |
|------|------|----------------|------|
| GET | /posts | index | 列表 |
| POST | /posts | store | 创建 |
| GET | /posts/{id} | show | 详情 |
| PUT | /posts/{id} | update | 更新 |
| DELETE | /posts/{id} | destroy | 删除 |

### 5.3 响应格式

```json
// 成功响应
{
    "data": { ... },
    "meta": { "current_page": 1, "total": 100 }
}

// 错误响应
{
    "message": "Validation failed",
    "errors": { "title": ["The title field is required."] }
}
```

---

## 6. 开发时间线

### 第一阶段：核心基础设施 (第1-2周)
- [ ] Laravel Sanctum + Spatie Permission配置
- [ ] Roles & Permissions CRUD + Policy
- [ ] Settings表 + SettingService
- [ ] Menu表 + MenuBuilder

### 第二阶段：内容CMS (第2-3周)
- [ ] Posts CRUD + FormRequest + Resource
- [ ] Categories树形 + Tags系统
- [ ] Videos, Projects, Resources CRUD
- [ ] Spatie Media Library集成

### 第三阶段：用户功能 (第3-4周)
- [ ] User CRUD + Policy + avatar上传
- [ ] UserLevels + 积分系统 + Observer
- [ ] Subscribers + 邮件订阅
- [ ] AuthorProfile + 评论系统

### 第四阶段：系统功能 (第4-5周)
- [ ] SEO Manager + Middleware
- [ ] I18n系统 + Artisan命令
- [ ] MailConfig + Mailable
- [ ] SocialLinks公开API

### 第五阶段：完善与维护 (第5-6周)
- [ ] Activitylog + 日志查询API
- [ ] Backup + Restore + Schedule
- [ ] Notifications + Event/Listener
- [ ] Advertisements + 追踪事件

---

## 7. 必需依赖包

```json
{
  "require": {
    "laravel/sanctum": "^3.0",
    "spatie/laravel-permission": "^5.0",
    "spatie/laravel-medialibrary": "^10.0",
    "spatie/laravel-activitylog": "^4.0",
    "spatie/laravel-backup": "^8.0",
    "spatie/laravel-google-fonts": "^2.0",
    "league/commonmark": "^2.0",
    "maatwebsite/excel": "^3.0",
    "predis/predis": "^2.0"
  },
  "require-dev": {
    "laravel/pint": "^1.0",
    "phpunit/phpunit": "^10.0",
    "mockery/mockery": "^1.6"
  }
}
```

---

## 8. 代码规范

### 8.1 PSR-12 标准
- 使用 `declare(strict_types=1);`
- 命名空间遵循 PSR-4
- 使用 PHP 8 类型声明
- 注释遵循 phpDocumentor 规范

### 8.2 提交信息规范 (Commit Message)

```
格式: <type>(<scope>): <subject>

type:
- feat: 新功能
- fix: Bug修复
- docs: 文档更新
- style: 代码格式
- refactor: 重构
- test: 测试
- chore: 构建/工具

example:
feat(post): 添加文章Markdown支持
fix(comment): 修复评论删除问题
docs(api): 更新API文档
```
