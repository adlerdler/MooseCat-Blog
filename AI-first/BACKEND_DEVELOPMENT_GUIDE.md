# 后端功能开发指南

**项目名称：** ARCHYX - Laravel Vue.js 混合应用
**最后更新：** 2026-06-06 (核心开发 100% 完成)
**版本：** 5.0
**Laravel版本：** 11 (精简模式)
**状态：** ✅ 全部完成

---

## 1. 技术架构

### 1.1 架构模式

> **重要说明：** 采用 **Inertia.js 现代单体架构**，前端使用 Vue 3 组件，后端使用 Laravel，通过 Inertia.js 实现无缝整合。
> **Repository模式：** 采用**轻量级Repository模式**，不强制Interface，专注封装复杂查询逻辑。简单CRUD直接在Service中使用Model。

#### 1.1.1 架构层次图

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        HTTP 请求 / 前端调用                             │
└─────────────────────────┬───────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────────────────┐
│                        Controller（控制器层）                           │
│  ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐        │
│  │ Frontend/        │ │ Admin/           │ │ Api/V1/          │        │
│  │ FrontendController││ PostController   │ │ PostController   │        │
│  │ HomeController   │ │ CategoryController│ │ CategoryController│        │
│  │ BlogController   │ │ TagController    │ │ TagController    │        │
│  │ ProjectsController││ DashboardController││ UserController   │        │
│  └──────────────────┘ └──────────────────┘ └──────────────────┘        │
│  - 请求参数验证（FormRequest）                                         │
│  - 调用 Service 处理业务                                               │
│  - 返回响应（Inertia/JSON）                                            │
└─────────────────────────┬───────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────────────────┐
│                        Service（业务逻辑层）                            │
│  ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐        │
│  │ MockDataService  │ │ PostService      │ │ CategoryService  │        │
│  │ CommentService   │ │ VideoService     │ │ TagService       │        │
│  └──────────────────┘ └──────────────────┘ └──────────────────┘        │
│  - 业务规则处理                                                         │
│  - 事务管理（DB::transaction）                                          │
│  - 简单CRUD直接使用Model                                                │
│  - 复杂查询委托给Repository                                             │
│  - 数据转换（DTO）                                                     │
└─────────────────────────┬───────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────────────────┐
│                        Repository（数据访问层）                          │
│  ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐         │
│  │ PostRepository   │ │ CategoryRepository│ │ TagRepository    │        │
│  │ VideoRepository  │ │ ProjectRepository │ │ CommentRepository│        │
│  └──────────────────┘ └──────────────────┘ └──────────────────┘         │
│  - 复杂数据库查询封装                                                    │
│  - 查询条件组合与筛选                                                    │
│  - 数据缓存策略实现                                                      │
│  - 不强制Interface，保持简洁                                             │
└───────────────────────────┬───────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────────────────┐
│                        Database（数据库）                               │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌───────────┐             │
│  │ posts     │ │ categories│ │ tags      │ │ users     │             │
│  │ videos    │ │ projects  │ │ comments  │ │ roles     │             │
│  └───────────┘ └───────────┘ └───────────┘ └───────────┘             │
└─────────────────────────────────────────────────────────────────────────┘
```

#### 1.1.2 控制器组织策略

| 场景类型 | 组织方式 | 示例控制器 | 路由配置 |
|----------|----------|------------|----------|
| **内容管理（CRUD）** | 按数据表 | `PostController`, `CategoryController`, `TagController` | `Route::resource()` |
| **后台设置页面** | 按页面 | `SettingsController`, `SocialLinksController`, `SeoController` | `Route::get()` |
| **前台展示页面** | 按页面/模块 | `HomeController`, `BlogController`, `ProjectsController` | `Route::get()` |
| **复杂业务流程** | 按功能 | `OrderController`, `PaymentController` | 自定义路由 |

| 端 | 渲染方式 | 数据获取方式 | 控制器目录 | 数量 |
|----|----------|--------------|------------|------|
| **前台网站** | Inertia.js + Vue 3 | Inertia Props | `App\Http\Controllers\Web\` | 1 (FrontendController) |
| **后台管理** | Inertia.js + Vue 3 | Inertia Props | `App\Http\Controllers\Admin\` | 27 |
| **API接口** | JSON响应 | RESTful API | `App\Http\Controllers\Api\V1\` | 10 |
| **Web兼容** | Inertia/JSON | 混合模式 | `App\Http\Controllers\Web\` | 8 |

#### 1.1.3 API 设计规范

> **核心原则：** 后台管理使用 Inertia.js，不走 API。API 仅供移动端 APP 和第三方系统使用。

**公开接口（无需认证）：**
| 端点 | 方法 | 说明 |
|------|:----:|------|
| `/api/login` | POST | 用户登录，返回 Bearer Token |
| `/api/logout` | POST | 用户登出，撤销 Token |
| `/api/subscribe` | POST | 订阅邮件 |
| `/api/unsubscribe` | POST | 取消订阅 |
| `/api/authors` | GET | 获取作者列表 |
| `/api/authors/{slug}` | GET | 获取作者详情 |

**受保护接口（需要 `auth:sanctum` 认证）：**
| 端点 | 方法 | 说明 |
|------|:----:|------|
| `/api/v1/posts` | GET | 获取文章列表 |
| `/api/v1/posts/{slug}` | GET | 获取文章详情 |
| `/api/v1/posts/{post}/comments` | GET/POST | 评论列表/发表 |
| `/api/v1/videos` | GET | 获取视频列表 |
| `/api/v1/projects` | GET | 获取项目列表 |
| `/api/v1/resources` | GET | 获取资源列表 |
| `/api/v1/categories` | GET | 获取分类列表 |
| `/api/v1/tags` | GET | 获取标签列表 |
| `/api/v1/users/{user}` | GET | 获取用户信息 |
| `/api/v1/me` | GET | 获取当前用户信息 |
| `/api/v1/roles` | GET | 获取角色列表 |
| `/api/v1/permissions` | GET | 获取权限列表 |
| `/api/v1/roles/{role}/permissions` | PUT | 同步角色权限 |

### 1.2 技术栈

| 分类 | 技术 | 版本 | 状态 |
|------|------|------|:----:|
| 后端框架 | Laravel | 11 | ✅ |
| 前端框架 | Vue | 3+ | ✅ |
| 全栈整合 | Inertia.js | 0.6+ | ✅ 已配置 |
| 路由辅助 | Ziggy | 1.0+ | ✅ 已配置 |
| 数据库 | MySQL | 8.0+ | ✅ |
| 认证系统 | Laravel Sanctum | 3.0+ | ✅ 已配置（前台API认证） |
| 权限管理 | Spatie Permission | 5.0+ | ✅ 已配置（RBAC） |
| 缓存系统 | Redis | 6.0+ | ⚠️ 可选配置（Cache facade已配置） |
| 媒体管理 | Spatie Media Library | 11.0+ | ✅ 已配置（本地存储 + 自动裁剪） |

### 1.3 Laravel 设计原则遵循

| 原则 | 实现方式 | 项目现状 |
|------|----------|----------|
| **单一职责原则 (SRP)** | Controller只做路由分发，业务逻辑在Service层 | ✅ 已采用 |
| **依赖注入 (DI)** | 通过构造函数注入Service、Repository接口 | ✅ 已采用 |
| **依赖倒置原则 (DIP)** | 依赖抽象接口而非具体实现 | ✅ 已采用 |
| **API版本控制** | `App\Http\Controllers\Api\V1\` 目录隔离 | ✅ 已采用 |
| **资源转换层** | API Resource格式化响应 | ✅ 已采用 |
| **表单验证** | FormRequest类处理验证逻辑 | ✅ 已完善（44个FormRequest） |
| **策略授权** | Policy类管理模型权限 | ❌ 已移除（统一走 Spatie Permission） |
| **观察者模式** | Observer处理模型事件 | ❌ 已跳过（采用Service模式） |
| **事务支持** | DB::transaction() 保证数据一致性 | ✅ 已采用 |

---

## 2. 目录结构

```
app/
├── Console/
│   └── Commands/                    # Artisan命令（待创建）
├── Events/                          # 事件类（待创建）
├── Exceptions/
│   └── Handler.php
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                   # 管理后台控制器（27个）✅
│   │   │   ├── DashboardController.php
│   │   │   ├── PostController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── TagController.php
│   │   │   ├── VideoController.php
│   │   │   ├── ProjectController.php
│   │   │   ├── ResourceController.php
│   │   │   ├── SettingsController.php
│   │   │   ├── SocialLinksController.php
│   │   │   ├── SeoController.php
│   │   │   ├── JournalsController.php
│   │   │   ├── UsersController.php
│   │   │   ├── SubscribersController.php
│   │   │   ├── RolesController.php
│   │   │   ├── PermissionsController.php
│   │   │   ├── MediaController.php
│   │   │   ├── I18nController.php
│   │   │   ├── EmailTemplatesController.php
│   │   │   ├── FrontMenuController.php
│   │   │   ├── AdvertisementsController.php
│   │   │   ├── LogsController.php
│   │   │   ├── BackupController.php
│   │   │   ├── RestoreController.php
│   │   │   ├── NotificationsController.php
│   │   │   ├── MailConfigController.php
│   │   │   ├── UserLevelsController.php
│   │   │   ├── CommentsController.php
│   │   │   └── AuthorProfileController.php
│   │   ├── Api/
│   │   │   └── V1/                  # API V1版本控制器（8个）✅
│   │   │       ├── PostController.php
│   │   │       ├── CategoryController.php
│   │   │       ├── TagController.php
│   │   │       ├── VideoController.php
│   │   │       ├── ProjectController.php
│   │   │       ├── ResourceController.php
│   │   │       ├── CommentController.php
│   │   │       └── UserController.php
│   │   └── Web/                     # Web前台控制器（8个）✅
│   │       ├── FrontendController.php
│   │       ├── PostController.php
│   │       ├── VideoController.php
│   │       ├── ProjectController.php
│   │       ├── ResourceController.php
│   │       ├── CommentController.php
│   │       ├── CategoryController.php
│   │       └── LikeController.php
│   ├── Middleware/
│   │   ├── HandleInertiaRequests.php ✅
│   │   └── AdminMiddleware.php      # 待创建
│   ├── Requests/                    # FormRequest（待创建）
│   └── Resources/
│       └── V1/                      # API资源转换（8个）✅
│           ├── PostResource.php
│           ├── CategoryResource.php
│           └── ...
├── Listeners/                       # 事件监听器（待创建）
├── Models/                          # Eloquent模型（28个）✅
│   ├── Post.php
│   ├── Category.php
│   ├── Tag.php
│   └── ...
├── Notifications/                   # 通知类（待创建）
├── Observers/                       # 模型观察者（待创建）
├── Policies/                        # 授权策略（待创建）
├── Providers/
│   ├── AppServiceProvider.php
│   └── AuthServiceProvider.php
├── Repositories/                    # 数据访问层（待创建）
│   ├── Interfaces/
│   └── Eloquent/
└── Services/                        # 业务逻辑层 ✅
    ├── MockDataService.php          # 模拟数据服务
    ├── PostService.php
    ├── CommentService.php
    └── InteractionService.php

resources/
├── js/                              # Vue 组件和入口
│   ├── Pages/
│   │   ├── front/                   # 前台页面 ✅
│   │   │   ├── Home.vue
│   │   │   ├── Blog.vue
│   │   │   ├── Projects.vue
│   │   │   ├── Videos.vue
│   │   │   ├── Resources.vue
│   │   │   ├── PostDetail.vue
│   │   │   └── ...
│   │   └── admin/                   # 后台页面 ✅
│   │       ├── Index.vue            # 仪表盘
│   │       ├── Posts/               # 文章管理（CRUD）
│   │       │   ├── Index.vue
│   │       │   ├── Create.vue
│   │       │   └── Edit.vue
│   │       ├── Categories.vue       # 分类管理
│   │       ├── Tags.vue             # 标签管理
│   │       ├── Videos.vue           # 视频管理
│   │       ├── Projects.vue         # 项目管理
│   │       ├── Resources.vue        # 资源管理
│   │       ├── Settings.vue         # 系统设置
│   │       ├── SocialLinks.vue      # 社交链接
│   │       ├── SeoManager.vue       # SEO管理
│   │       ├── Login.vue            # 登录页面（独立布局）
│   │       ├── Layout.vue           # 后台布局
│   │       └── ...
│   ├── components/                  # 可复用组件
│   ├── app.js                       # Inertia入口 ✅
│   └── bootstrap.js
└── views/
    └── app.blade.php               # 主布局模板 ✅
```

---

## 3. 核心架构组件

### 3.1 架构组件清单

| 模式 | 目录 | 现状 | 说明 |
|------|------|:----:|------|
| **Service Layer** | `app/Services/` | ✅ 已有 | 业务逻辑封装（33个） |
| **MockDataService** | `app/Services/MockDataService.php` | ✅ 已清理 | 仅中间件容错 fallback 引用 |
| **API Resource** | `app/Http/Resources/V1/` | ✅ 已有 | 响应格式化（12个） |
| **FormRequest** | `app/Http/Requests/` | ✅ 已有 | 表单验证（24个） |
| **Policy** | `app/Policies/` | ❌ 已移除 | 24个Policy已删除，走 Spatie Permission |
| **Observer** | `app/Observers/` | ❌ 已跳过 | 采用 Service 模式替代 |
| **Event/Listener** | `app/Events/`, `app/Listeners/` | ✅ 已完成 | 2对事件驱动 |
| **Notification** | `app/Notifications/` | ✅ 已完成 | 3个通知类 |
| **Middleware** | `app/Http/Middleware/` | ✅ 已完成 | 7个中间件 |
| **Command** | `app/Console/Commands/` | ✅ 已完成 | BackupCommand |
| **Seeder** | `database/seeders/` | ✅ 已完成 | 25个Seeder，约200条数据 |

---

## 4. 模块开发清单

### 4.1 内容管理模块

#### 4.1.1 文章 (Posts)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | posts表 | Model使用$fillable/$casts |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5篇高质量文章 | 完整Markdown、封面图、SEO |
| 前台 Inertia Controller | 高 | ✅ 已完成 | FrontendController::blog() | Inertia::render('front/Blog') |
| 后台 Inertia Controller | 高 | ✅ 已完成 | Admin/PostController | Inertia::render('admin/Posts') |
| FormRequest验证 | 高 | ✅ 已完成 | StorePostRequest | 44个FormRequest |
| API Resource | 高 | ✅ 已完成 | PostResource | 统一响应格式 |
| 分类/标签关联 | 高 | ✅ 已完成 | 多态关联taggables | Post模型已实现morphToMany |
| Markdown解析 | 中 | ✅ 已集成 | league/commonmark | 前端 Vditor Markdown编辑器 |
| 封面图上传 | 中 | ✅ 已完成 | Spatie Media Library | Post/User模型HasMedia |

#### 4.1.2 视频 (Videos)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | videos表 | 标准Eloquent模型 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5条视频数据 | 包含YouTube/Bilibili |
| 后台 Inertia Controller | 高 | ✅ 已完成 | Admin/VideoController | Inertia::render('admin/Videos') |
| 平台集成 | 中 | ✅ 已完成 | YouTube/Bilibili | Video模型url字段支持 |

#### 4.1.3 项目 (Projects)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | projects表 | 标准Model |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 6条项目数据 | 包含技术栈、GitHub链接 |
| 后台 Inertia Controller | 高 | ✅ 已完成 | Admin/ProjectController | Inertia::render('admin/Projects') |

#### 4.1.4 资源 (Resources)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | resources表 | download_count字段 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5条资源数据 | 包含下载链接、文件大小 |
| 后台 Inertia Controller | 高 | ✅ 已完成 | Admin/ResourceController | Inertia::render('admin/Resources') |

#### 4.1.5 日记 (Journals)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | journals表 | mood/weather JSON |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5条详细日志 | 含title、date、likes_count字段 |
| 后台 Inertia Controller | 高 | ✅ 已完成 | Admin/JournalsController | Inertia::render('admin/Journals') |

#### 4.1.6 分类 (Categories)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | categories表 | 层级结构 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 6条专业分类 | THEORY, DESIGN, CULTURE等 |
| 后台 Inertia Controller | 高 | ✅ 已完成 | Admin/CategoryController | Inertia::render('admin/Categories') |
| 树形结构 | 中 | ✅ 已完成 | 递归关系 | Category模型hasChildren/belongsToParent |

#### 4.1.7 标签 (Tags)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | tags表 | usage_count |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 15条专业标签 | Architecture, Design等 |
| 后台 Inertia Controller | 高 | ✅ 已完成 | Admin/TagController | Inertia::render('admin/Tags') |

---

### 4.2 用户与认证模块

#### 4.2.1 用户管理
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| users表扩展 | 高 | ✅ 已完成 | 通过 model_has_roles 关联 | Spatie RBAC |
| User Policy | 高 | ❌ 已移除 | 授权逻辑 | 统一走 Spatie Permission |
| 用户资料更新 | 高 | ✅ 已完成 | Admin/UsersController | UpdateUserRequest |
| 密码重置 | 高 | ✅ 已完成 | Laravel内置 | 邮件通知 |
| 测试用户数据 | 高 | ✅ 已完成 | 4个用户 | Admin, Editor, Author, Subscriber |

#### 4.2.2 订阅者 (Newsletter)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | subscribers表 | email唯一性 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5条订阅数据 | 包含邮箱、状态 |
| 后台 Inertia Controller | 高 | ✅ 已完成 | Admin/SubscribersController | Inertia响应 |
| 订阅控制器 | 中 | ✅ 已完成 | /api/subscribe | SubscribeRequest |

#### 4.2.3 用户等级 (VIP)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | user_levels表 | benefits JSON |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5条等级数据 | 包含权益描述 |
| 后台 Inertia Controller | 高 | ✅ 已完成 | Admin/UserLevelsController | Inertia响应 |
| 等级分配逻辑 | 高 | ✅ 已完成 | 自动升级 | UserLevelSeeder |

#### 4.2.4 RBAC权限系统
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| Spatie Permission | 高 | ✅ 已完成 | 完整配置 | Trait + 中间件 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 7角色+权限 | Administrator, Editor, Author等 |
| Roles Controller | 高 | ✅ 已完成 | Admin/RolesController | Inertia响应 |
| Role Policy | 高 | ❌ 已移除 | 角色授权 | 统一走 Spatie Permission |

---

### 4.3 系统配置模块

#### 4.3.1 网站设置
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | settings表 | key-value |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 10条配置数据 | 站点名称、品牌等 |
| Settings Controller | 高 | ✅ 已完成 | Admin/SettingsController | Inertia响应 |
| Setting Service | 高 | ✅ 已完成 | 配置获取 | SettingService + Cache |

#### 4.3.2 SEO管理器
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | seo表 | key-value JSON |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 3条SEO数据 | 首页、博客、关于 |
| SEO Controller | 高 | ✅ 已完成 | Admin/SeoController | Inertia响应 |

#### 4.3.3 国际化 (I18n)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| languages表 | 高 | ✅ 已完成 | 语言配置 | Seeder 5条数据 |
| translations表 | 高 | ✅ 已完成 | 翻译存储 | Seeder 20条数据 |
| I18n Controller | 高 | ✅ 已完成 | Admin/I18nController | Inertia响应 |

#### 4.3.4 媒体库
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | media表 | 标准结构 |
| Media Controller | 高 | ✅ 已完成 | Admin/MediaController | Inertia响应 |
| Spatie Media Library | 高 | ✅ 已完成 | 完整配置 | Post/User模型HasMediaTrait |

---

### 4.4 互动社交模块

#### 4.4.1 评论
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| comments表 | 高 | ✅ 已完成 | 多态关联 | commentable |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 6条真实评论 | 含嵌套回复parent_id |
| Comments Controller | 高 | ✅ 已完成 | Admin/CommentsController | Inertia响应 |
| Comment Service | 高 | ✅ 已完成 | 业务逻辑 | CommentService |
| 审核流程 | 中 | ✅ 已完成 | require_approval | 审核状态字段 |

#### 4.4.2 社交链接
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| social_links表 | 高 | ✅ 已完成 | 平台配置 | platform唯一 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 6条社交链接 | GitHub、Twitter等 |
| SocialLinks Controller | 高 | ✅ 已完成 | Admin/SocialLinksController | Inertia响应 |
| 公开API | 中 | ✅ 已完成 | /api/social-links | Api/V1/SocialLinkController |

---

### 4.5 系统维护模块

#### 4.5.1 审计日志
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| admin_logs表 | 高 | ✅ 已完成 | 审计追踪 | 迁移完成 |
| Logs Controller | 高 | ✅ 已完成 | Admin/LogsController | Inertia响应 |
| Spatie Activitylog | 高 | ✅ 已完成 | 完整配置 | Trait + Middleware |

#### 4.5.2 备份与恢复
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| backups表 | 高 | ✅ 已完成 | 备份记录 | 迁移完成 |
| Backup Controller | 高 | ✅ 已完成 | Admin/BackupController | Inertia响应 |
| Restore Controller | 高 | ✅ 已完成 | Admin/RestoreController | Inertia响应 |
| Spatie Backup | 高 | ✅ 已完成 | 完整备份 | BackupCommand |

---

## 5. 核心代码示例

### 1.1 Inertia Controller 示例

```php
// app/Http/Controllers/Frontend/FrontendController.php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

class FrontendController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function home(): Response
    {
        $posts = $this->mockDataService->getPosts(3);
        $projects = $this->mockDataService->getProjects(3);
        $videos = $this->mockDataService->getVideos(3);

        return Inertia::render('front/Home', [
            'posts' => $posts,
            'projects' => $projects,
            'videos' => $videos,
        ]);
    }

    public function blog(): Response
    {
        $posts = $this->mockDataService->getPosts();
        $categories = $this->mockDataService->getCategories();
        $authors = $this->mockDataService->getAuthors();

        return Inertia::render('front/Blog', [
            'posts' => $posts,
            'categories' => $categories,
            'authors' => $authors,
        ]);
    }
}
```

### 5.2 MockDataService 示例

```php
// app/Services/MockDataService.php
namespace App\Services;

class MockDataService
{
    public function getPosts($limit = null)
    {
        $posts = [
            [
                'id' => 1,
                'title' => 'THE GEOMETRY OF PERCEPTION',
                'slug' => 'the-geometry-of-perception',
                'excerpt' => 'Exploring how structural design influences cognitive processes...',
                'category' => ['id' => 1, 'name' => 'Design', 'color' => '#8b5cf6'],
                'tags' => [['id' => 1, 'name' => 'UI Design'], ['id' => 2, 'name' => 'Geometry']],
                'views_count' => 1234,
                'likes_count' => 89,
                'published_at' => '2024-01-15',
                'cover_image' => 'https://example.com/image.jpg'
            ],
            // 更多文章数据...
        ];

        if ($limit) {
            return array_slice($posts, 0, $limit);
        }
        return $posts;
    }

    public function getProjects($limit = null)
    {
        // 项目数据...
    }

    public function getVideos($limit = null)
    {
        // 视频数据...
    }
}
```

### 5.3 Vue 组件示例

```vue
<!-- resources/js/Pages/front/Home.vue -->
<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    posts: { type: Array, default: () => [] },
    projects: { type: Array, default: () => [] },
    videos: { type: Array, default: () => [] }
})
</script>

<template>
    <div class="min-h-screen bg-construct-paper">
        <!-- Hero Section -->
        <header class="relative min-h-screen bg-white overflow-hidden">
            <!-- 页面内容渲染 -->
        </header>
        
        <!-- Latest Posts -->
        <section>
            <h2>Latest Posts</h2>
            <div v-for="post in posts" :key="post.id">
                <Link :href="`/blog/${post.slug}`">
                    {{ post.title }}
                </Link>
            </div>
        </section>
    </div>
</template>
```

### 5.4 Service Layer 示例

```php
// app/Services/PostService.php
namespace App\Services;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Pagination\LengthAwarePaginator;

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
            ->when($filters['category'] ?? null, fn($q, $slug) => 
                $q->whereHas('category', fn($q) => $q->where('slug', $slug))
            )
            ->when($filters['tag'] ?? null, fn($q, $slug) =>
                $q->whereHas('tags', fn($q) => $q->where('slug', $slug))
            )
            ->latest('published_at')
            ->paginate($perPage);
    }
}
```

### 5.5 API Resource 示例

```php
// app/Http/Resources/V1/PostResource.php
namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'author' => new UserResource($this->whenLoaded('author')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

---

## 6. 数据库迁移规范

### 6.1 迁移文件命名

```
格式: {timestamp}_{action}_{table_name}.php
示例:
- 2024_01_01_000001_create_posts_table.php
- 2024_01_02_000002_add_role_id_to_users_table.php
- 2024_01_03_000003_create_post_tag_table.php
```

### 6.2 字段类型规范

| 字段类型 | 使用场景 | 示例 |
|----------|----------|------|
| bigint | 主键、外键 | `bigInteger('user_id')` |
| varchar | 短文本 | `string('name', 255)` |
| text | 长文本 | `text('content')` |
| json | 结构化数据 | `json('settings')` |
| boolean | 开关状态 | `boolean('is_active')` |
| timestamp | 时间戳 | `timestamp('published_at')` |
| unsignedBigInteger | 计数/积分 | `unsignedBigInteger('points')` |

---

## 7. API设计规范

### 7.1 版本控制

```
/api/v1/posts
/api/v1/admin/posts
/api/v2/posts  (未来版本迭代)
```

### 7.2 RESTful路由

| 方法 | 路由 | Controller方法 | 描述 |
|------|------|----------------|------|
| GET | /posts | index | 列表 |
| POST | /posts | store | 创建 |
| GET | /posts/{id} | show | 详情 |
| PUT | /posts/{id} | update | 更新 |
| DELETE | /posts/{id} | destroy | 删除 |

### 7.3 响应格式

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

## 8. 开发时间线

### 第一阶段：核心基础设施 (已完成) ✅
- ✅ Laravel Sanctum + Spatie Permission配置
- ✅ Roles & Permissions CRUD
- ✅ Settings表 + SettingService
- ✅ MockDataService 完善
- ✅ Inertia.js 基础设施配置
- ✅ 所有控制器创建与路由配置

### 第二阶段：内容CMS (已完成) ✅
- ✅ Posts CRUD + FormRequest + Resource
- ✅ Categories树形 + Tags系统
- ✅ Videos, Projects, Resources CRUD
- ✅ Spatie Media Library集成

### 第三阶段：用户功能 (已完成) ✅
- ✅ User CRUD + avatar上传
- ✅ UserLevels + 积分系统
- ✅ Subscribers + 邮件订阅
- ✅ AuthorProfile + 公开API

### 第四阶段：系统功能 (已完成) ✅
- ✅ SEO Manager + Middleware
- ✅ I18n系统 + 动态语言切换
- ✅ MailConfig + EmailTemplates + 测试邮件
- ✅ SocialLinks公开API

### 第五阶段：完善与维护 (已完成) ✅
- ✅ spatie/activitylog + 16模型接入
- ✅ Backup + Restore + BackupService + BackupRestoreService
- ✅ Notifications + Event/Listener + MailService统一

---

## 9. 必需依赖包

```json
{
  "require": {
    "laravel/sanctum": "^3.0",
    "spatie/laravel-permission": "^5.0",
    "spatie/laravel-medialibrary": "^10.0",
    "spatie/laravel-activitylog": "^4.0",
    "spatie/laravel-backup": "^8.0",
    "league/commonmark": "^2.0",
    "tightenco/ziggy": "^1.0",
    "predis/predis": "^2.0",
    "inertiajs/inertia-laravel": "^0.6"
  },
  "require-dev": {
    "laravel/pint": "^1.0",
    "phpunit/phpunit": "^10.0"
  }
}
```

---

## 10. 代码规范

### 10.1 PSR-12 标准
- 使用 `declare(strict_types=1);`
- 命名空间遵循 PSR-4
- 使用 PHP 8 类型声明
- 注释遵循 phpDocumentor 规范

### 10.2 提交信息规范

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

---

## 11. 开发进度总结

### 11.1 已完成工作（截至 2026-06-03）

| 模块 | 完成状态 | 说明 |
|:---:|:---:|------|
| **数据库迁移** | ✅ 100% | 25+ 迁移文件，覆盖所有业务表 |
| **数据填充 (Seeder)** | ✅ 100% | 25个 Seeder，约200条高质量模拟数据 |
| **模型文件 (Models)** | ✅ 100% | 所有模型与迁移文件字段一致 |
| **控制器 (Controllers)** | ✅ 100% | 50+个控制器（Admin 27 + Api/V1 10 + Web 8） |
| **路由配置** | ✅ 100% | 123条后台路由 + 前台路由，全部真实数据 |
| **Service 层** | ✅ 100% | 25个 Service，27 模块全部真实数据对接 |
| **FormRequest 层** | ✅ 100% | 44个 FormRequest 验证类 |
| **Policy 层** | ❌ 已移除 | 24个 Policy 已删除，走 Spatie Permission 中间件 |
| **Inertia.js配置** | ✅ 100% | 中间件、布局、路由解析完成 |
| **前台真实数据** | ✅ 100% | FrontendController 所有方法对接真实 DB |
| **MockDataService** | ✅ 已清理 | 仅中间件容错 fallback 引用 |
| **备份系统** | ✅ 100% | BackupService + BackupRestoreService（4种备份类型） |
| **活动日志** | ✅ 100% | spatie/activitylog（16模型接入） |
| **通知系统** | ✅ 100% | Laravel 原生 database notifications + MailService |
| **邮件配置** | ✅ 100% | Symfony Mailer 直连 + 测试邮件 + 构成主义模板 |

### 11.2 待开发任务统计

| 优先级 | 总数 | ✅ 完成 | ⚠️ 待处理 |
|:------:|:----:|:-------:|:---------:|
| 高 | 37 | 37 | 0 |
| 中 | 25 | 25 | 0 |
| 低 | 23 | 23 | 0 |
| **总计** | **85** | **85** | **0** |

### 11.3 下一步开发建议

1. **高优先级** - 核心功能
   - ✅ 密码重置邮件（EmailTemplate 已就绪）
   - ✅ 清理 MockDataService + 31 JSON 文件

2. **中优先级** - 重要功能
   - ✅ Markdown 解析（前端 Vditor 已集成）
   - ✅ 文章搜索（Full-text search）

3. **低优先级** - 辅助功能
   - 编写测试用例
   - 性能优化与部署准备

---

## 12. 快速参考：Laravel Artisan 命令

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

# 运行迁移
php artisan migrate

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

## 13. 登录凭证（开发环境）

| 账号类型 | 邮箱 | 密码 | 角色 |
|:---:|:---:|:---:|:---:|
| 超级管理员 | Archyx@admin.com | Archyx_admin123 | Administrator |
| 编辑 | editor@example.com | password123 | Editor |
| 作者 | author@example.com | password123 | Author |
| 订阅者 | subscriber@example.com | password123 | Subscriber |