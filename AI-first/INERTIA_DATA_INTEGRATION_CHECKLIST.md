# Inertia.js 数据对接任务清单

**项目名称：** ARCHYX - Laravel Vue.js 混合应用
**最后更新：** 2026-05-27 (新增前台API认证系统，9个测试用例全部通过)
**版本：** 3.0
**状态：** 进行中

---

## 任务概览

| 阶段 | 任务数 | 完成 | 进行中 | 待处理 |
|:----:|:------:|:----:|:------:|:------:|
| 第一阶段：MockDataService | 15 | 15 | 0 | 0 | ✅ |
| 第二阶段：后端Service层 | 20 | 20 | 0 | 0 | ✅ |
| 第二阶段B：后端Repository层 | 13 | 13 | 0 | 0 | ✅ |
| 第三阶段：后端FormRequest | 17 | 17 | 0 | 0 | ✅ |
| 第四阶段：后端Policy | 10 | 10 | 0 | 0 | ✅ |
| 第五阶段：后端Observer | 8 | 0 | 0 | 8 | ❌ 跳过 |
| 第六阶段：API Resource | 12 | 12 | 0 | 0 | ✅ |
| 第六阶段B：中间件开发 | 5 | 5 | 0 | 0 | ✅ |
| 第七阶段：后台Controller | 30 | 24 | 0 | 6 | ⚠️ |
| 第八阶段：前台页面 | 40 | 26 | 0 | 14 | ⚠️ |
| 第九阶段：数据清理 | 5 | 0 | 0 | 5 | ⚠️ |
| 第十阶段：前台API认证 | 6 | 6 | 0 | 0 | ✅ |
| **总计** | **176** | **143** | **0** | **33** |

---

## 第一阶段：MockDataService 创建

> **状态：** ✅ 已完成
> **文件位置：** `app/Services/MockDataService.php`

| 序号 | 任务 | 依赖 | 优先级 | 状态 |
|:---:|------|------|:------:|:----:|
| 1.1 | 创建 MockDataService 类文件 | - | 高 | ✅ 已完成 |
| 1.2 | 实现 getPosts($limit) 方法 | 1.1 | 高 | ✅ 已完成 |
| 1.3 | 实现 getProjects($limit) 方法 | 1.1 | 高 | ✅ 已完成 |
| 1.4 | 实现 getVideos($limit) 方法 | 1.1 | 高 | ✅ 已完成 |
| 1.5 | 实现 getCategories() 方法 | 1.1 | 高 | ✅ 已完成 |
| 1.6 | 实现 getTags() 方法 | 1.1 | 高 | ✅ 已完成 |
| 1.7 | 实现 getAuthors() 方法 | 1.1 | 高 | ✅ 已完成 |
| 1.8 | 实现 getJournals($limit) 方法 | 1.1 | 中 | ✅ 已完成 |
| 1.9 | 实现 getComments($postId) 方法 | 1.1 | 中 | ✅ 已完成 |
| 1.10 | 实现 getSiteConfig() 方法 | 1.1 | 中 | ✅ 已完成 |
| 1.11 | 实现 getSeoConfig() 方法 | 1.1 | 中 | ✅ 已完成 |
| 1.12 | 实现分页数据包装方法 | 1.1 | 中 | ✅ 已完成 |
| 1.13 | 实现 getTagsables() 方法 | 1.1 | 中 | ✅ 已完成 |
| 1.14 | 实现 getBackups() 方法 | 1.1 | 中 | ✅ 已完成 |
| 1.15 | 实现 getMenus() 方法 | 1.1 | 中 | ✅ 已完成 |

---

## 第二阶段：后端 Service 层开发

> **状态：** ✅ 已完成
> **文件位置：** `app/Services/`

### 2.1 PostService 文章服务

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 2.1.1 | 创建 PostService 类 | app/Services/PostService.php | 高 | ✅ 已完成 |
| 2.1.2 | 实现 getPaginatedPosts() 分页查询 | 2.1.1 | 高 | ✅ 已完成 |
| 2.1.3 | 实现 getPostBySlug() 详情查询 | 2.1.1 | 高 | ✅ 已完成 |
| 2.1.4 | 实现 createPost() 创建文章 | 2.1.1 | 高 | ✅ 已完成 |
| 2.1.5 | 实现 updatePost() 更新文章 | 2.1.1 | 高 | ✅ 已完成 |
| 2.1.6 | 实现 deletePost() 删除文章 | 2.1.1 | 高 | ✅ 已完成 |
| 2.1.7 | 实现 searchPosts() 搜索功能 | 2.1.1 | 中 | ✅ 已完成 |

### 2.2 CategoryService 分类服务

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 2.2.1 | 创建 CategoryService 类 | app/Services/CategoryService.php | 高 | ✅ 已完成 |
| 2.2.2 | 实现 getCategories() 分类列表 | 2.2.1 | 高 | ✅ 已完成 |
| 2.2.3 | 实现 getCategoryTree() 树形结构 | 2.2.1 | 中 | ✅ 已完成 |
| 2.2.4 | 实现 createCategory() 创建分类 | 2.2.1 | 高 | ✅ 已完成 |
| 2.2.5 | 实现 updateCategory() 更新分类 | 2.2.1 | 高 | ✅ 已完成 |
| 2.2.6 | 实现 deleteCategory() 删除分类 | 2.2.1 | 高 | ✅ 已完成 |

### 2.3 VideoService 视频服务

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 2.3.1 | 创建 VideoService 类 | app/Services/VideoService.php | 高 | ✅ 已完成 |
| 2.3.2 | 实现 getVideos() 视频列表 | 2.3.1 | 高 | ✅ 已完成 |
| 2.3.3 | 实现 createVideo() 创建视频 | 2.3.1 | 高 | ✅ 已完成 |
| 2.3.4 | 实现 updateVideo() 更新视频 | 2.3.1 | 高 | ✅ 已完成 |
| 2.3.5 | 实现 deleteVideo() 删除视频 | 2.3.1 | 高 | ✅ 已完成 |

### 2.4 ProjectService 项目服务

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 2.4.1 | 创建 ProjectService 类 | app/Services/ProjectService.php | 高 | ✅ 已完成 |
| 2.4.2 | 实现 getProjects() 项目列表 | 2.4.1 | 高 | ✅ 已完成 |
| 2.4.3 | 实现 createProject() 创建项目 | 2.4.1 | 高 | ✅ 已完成 |
| 2.4.4 | 实现 updateProject() 更新项目 | 2.4.1 | 高 | ✅ 已完成 |
| 2.4.5 | 实现 deleteProject() 删除项目 | 2.4.1 | 高 | ✅ 已完成 |

### 2.5 其他Service

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 2.5.1 | TagService | app/Services/TagService.php | 高 | ✅ 已完成 |
| 2.5.2 | CommentService | app/Services/CommentService.php | 高 | ✅ 已完成 |
| 2.5.3 | UserService | app/Services/UserService.php | 高 | ✅ 已完成 |
| 2.5.4 | SettingService | app/Services/SettingService.php | 中 | ✅ 已完成 |
| 2.5.5 | MenuService | app/Services/MenuService.php | 中 | ✅ 已完成 |
| 2.5.6 | InteractionService | app/Services/InteractionService.php | 中 | ✅ 已完成 |

---

## 第二阶段B：后端 Repository 层开发

> **状态：** ✅ 已完成
> **文件位置：** `app/Repositories/`
> **架构模式：** 采用轻量级 Repository 模式，不强制 Interface，专注封装复杂查询逻辑。简单 CRUD 直接在 Service 中使用 Model。

### 2B.1 PostRepository 文章数据访问

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 2B.1.1 | 创建 PostRepository 类 | app/Repositories/PostRepository.php | 高 | ✅ 已完成 |
| 2B.1.2 | 实现 getPaginatedPosts() 分页查询 | 2B.1.1 | 高 | ✅ 已完成 |
| 2B.1.3 | 实现 searchPosts() 搜索功能 | 2B.1.1 | 中 | ✅ 已完成 |
| 2B.1.4 | 实现 getPostsByCategory() 分类筛选 | 2B.1.1 | 中 | ✅ 已完成 |
| 2B.1.5 | 实现 getPostsByTag() 标签筛选 | 2B.1.1 | 中 | ✅ 已完成 |

### 2B.2 CategoryRepository 分类数据访问

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 2B.2.1 | 创建 CategoryRepository 类 | app/Repositories/CategoryRepository.php | 高 | ✅ 已完成 |
| 2B.2.2 | 实现 getCategoriesWithCount() 带文章数 | 2B.2.1 | 高 | ✅ 已完成 |
| 2B.2.3 | 实现 getCategoryTree() 树形结构 | 2B.2.1 | 中 | ✅ 已完成 |

### 2B.3 其他 Repository

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 2B.3.1 | TagRepository | app/Repositories/TagRepository.php | 高 | ✅ 已完成 |
| 2B.3.2 | VideoRepository | app/Repositories/VideoRepository.php | 高 | ✅ 已完成 |
| 2B.3.3 | ProjectRepository | app/Repositories/ProjectRepository.php | 高 | ✅ 已完成 |
| 2B.3.4 | CommentRepository | app/Repositories/CommentRepository.php | 高 | ✅ 已完成 |
| 2B.3.5 | UserRepository | app/Repositories/UserRepository.php | 中 | ✅ 已完成 |

---

## 第三阶段：后端 FormRequest 表单验证

> **状态：** ✅ 已完成
> **文件位置：** `app/Http/Requests/`

### 3.1 文章相关验证

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 3.1.1 | StorePostRequest 创建文章验证 | app/Http/Requests/StorePostRequest.php | 高 | ✅ 已完成 |
| 3.1.2 | UpdatePostRequest 更新文章验证 | app/Http/Requests/UpdatePostRequest.php | 高 | ✅ 已完成 |

### 3.2 分类标签验证

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 3.2.1 | StoreCategoryRequest | app/Http/Requests/StoreCategoryRequest.php | 高 | ✅ 已完成 |
| 3.2.2 | UpdateCategoryRequest | app/Http/Requests/UpdateCategoryRequest.php | 高 | ✅ 已完成 |
| 3.2.3 | StoreTagRequest | app/Http/Requests/StoreTagRequest.php | 高 | ✅ 已完成 |
| 3.2.4 | UpdateTagRequest | app/Http/Requests/UpdateTagRequest.php | 高 | ✅ 已完成 |

### 3.3 视频项目验证

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 3.3.1 | StoreVideoRequest | app/Http/Requests/StoreVideoRequest.php | 高 | ✅ 已完成 |
| 3.3.2 | UpdateVideoRequest | app/Http/Requests/UpdateVideoRequest.php | 高 | ✅ 已完成 |
| 3.3.3 | StoreProjectRequest | app/Http/Requests/StoreProjectRequest.php | 高 | ✅ 已完成 |
| 3.3.4 | UpdateProjectRequest | app/Http/Requests/UpdateProjectRequest.php | 高 | ✅ 已完成 |

### 3.4 用户评论验证

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 3.4.1 | StoreUserRequest | app/Http/Requests/StoreUserRequest.php | 高 | ✅ 已完成 |
| 3.4.2 | UpdateUserRequest | app/Http/Requests/UpdateUserRequest.php | 高 | ✅ 已完成 |
| 3.4.3 | StoreCommentRequest | app/Http/Requests/StoreCommentRequest.php | 高 | ✅ 已完成 |
| 3.4.4 | SubscribeRequest | app/Http/Requests/SubscribeRequest.php | 中 | ✅ 已完成 |

### 3.5 其他验证

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 3.5.1 | StoreResourceRequest | app/Http/Requests/StoreResourceRequest.php | 中 | ✅ 已完成 |
| 3.5.2 | StoreSubscriberRequest | app/Http/Requests/StoreSubscriberRequest.php | 中 | ✅ 已完成 |
| 3.5.3 | StoreRoleRequest | app/Http/Requests/StoreRoleRequest.php | 高 | ✅ 已完成 |
| 3.5.4 | UpdateRoleRequest | app/Http/Requests/UpdateRoleRequest.php | 高 | ✅ 已完成 |
| 3.5.5 | StoreJournalRequest | app/Http/Requests/StoreJournalRequest.php | 中 | ✅ 已完成 |

---

## 第四阶段：后端 Policy 授权策略

> **状态：** ✅ 已完成（10/10 已完成）
> **文件位置：** `app/Policies/`
> **创建方式：** 使用 `php artisan make:policy` 命令批量创建

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 4.1 | PostPolicy 文章授权 | app/Policies/PostPolicy.php | 高 | ✅ 已完成 |
| 4.2 | CategoryPolicy 分类授权 | app/Policies/CategoryPolicy.php | 高 | ✅ 已完成 |
| 4.3 | TagPolicy 标签授权 | app/Policies/TagPolicy.php | 高 | ✅ 已完成 |
| 4.4 | VideoPolicy 视频授权 | app/Policies/VideoPolicy.php | 高 | ✅ 已完成 |
| 4.5 | ProjectPolicy 项目授权 | app/Policies/ProjectPolicy.php | 高 | ✅ 已完成 |
| 4.6 | UserPolicy 用户授权 | app/Policies/UserPolicy.php | 高 | ✅ 已完成 |
| 4.7 | RolePolicy 角色授权 | app/Policies/RolePolicy.php | 高 | ✅ 已完成 |
| 4.8 | CommentPolicy 评论授权 | app/Policies/CommentPolicy.php | 中 | ✅ 已完成 |
| 4.9 | MediaPolicy 媒体授权 | app/Policies/MediaPolicy.php | 中 | ✅ 已完成 |
| 4.10 | SettingPolicy 设置授权 | app/Policies/SettingPolicy.php | 中 | ✅ 已完成 |

---

## 第五阶段：后端 Observer 观察者

> **状态：** ❌ 已跳过（采用 Service 模式替代）
> **文件位置：** `app/Observers/`
> **跳过原因：** 当前 Service + Repository 架构已足够清晰，Observer 会增加代码复杂度和维护成本
> **替代方案：** 
> - 业务逻辑封装在 Service 层（已实现）
> - 使用数据库外键约束处理级联删除
> - 使用 Laravel Events 处理跨模块通知（可选）

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 5.1 | PostObserver 文章观察者 | app/Observers/PostObserver.php | 高 | ❌ 已跳过 |
| 5.2 | UserObserver 用户观察者 | app/Observers/UserObserver.php | 高 | ❌ 已跳过 |
| 5.3 | CategoryObserver 分类观察者 | app/Observers/CategoryObserver.php | 中 | ❌ 已跳过 |
| 5.4 | TagObserver 标签观察者 | app/Observers/TagObserver.php | 中 | ❌ 已跳过 |
| 5.5 | CommentObserver 评论观察者 | app/Observers/CommentObserver.php | 高 | ❌ 已跳过 |
| 5.6 | VisitObserver 访问记录观察者 | app/Observers/VisitObserver.php | 中 | ❌ 已跳过 |
| 5.7 | InteractionObserver 互动观察者 | app/Observers/InteractionObserver.php | 中 | ❌ 已跳过 |
| 5.8 | 在 AppServiceProvider 注册观察者 | app/Providers/AppServiceProvider.php | 高 | ❌ 已跳过 |

---

## 第六阶段：API Resource 资源转换

> **状态：** ✅ 已完成（12/12 已完成）
> **文件位置：** `app/Http/Resources/V1/`

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 6.1 | PostResource 文章资源 | app/Http/Resources/V1/PostResource.php | 高 | ✅ 已完成 |
| 6.2 | CategoryResource 分类资源 | app/Http/Resources/V1/CategoryResource.php | 高 | ✅ 已完成 |
| 6.3 | TagResource 标签资源 | app/Http/Resources/V1/TagResource.php | 高 | ✅ 已完成 |
| 6.4 | VideoResource 视频资源 | app/Http/Resources/V1/VideoResource.php | 高 | ✅ 已完成 |
| 6.5 | ProjectResource 项目资源 | app/Http/Resources/V1/ProjectResource.php | 高 | ✅ 已完成 |
| 6.6 | UserResource 用户资源 | app/Http/Resources/V1/UserResource.php | 高 | ✅ 已完成 |
| 6.7 | CommentResource 评论资源 | app/Http/Resources/V1/CommentResource.php | 高 | ✅ 已完成 |
| 6.8 | RoleResource 角色资源 | app/Http/Resources/V1/RoleResource.php | 高 | ✅ 已完成 |
| 6.9 | PermissionResource 权限资源 | app/Http/Resources/V1/PermissionResource.php | 中 | ✅ 已完成 |
| 6.10 | SubscriberResource 订阅者资源 | app/Http/Resources/V1/SubscriberResource.php | 中 | ✅ 已完成 |
| 6.11 | JournalResource 日记资源 | app/Http/Resources/V1/JournalResource.php | 中 | ✅ 已完成 |
| 6.12 | ResourceResource 资源资源 | app/Http/Resources/V1/ResourceResource.php | 高 | ✅ 已完成 |

---

## 第六阶段B：中间件开发

> **状态：** ✅ 已完成（5/5 已完成）
> **文件位置：** `app/Http/Middleware/`

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 6B.1 | HandleInertiaRequests Inertia中间件 | app/Http/Middleware/HandleInertiaRequests.php | 高 | ✅ 已完成 |
| 6B.2 | SeoMiddleware SEO配置中间件 | app/Http/Middleware/SeoMiddleware.php | 高 | ✅ 已完成 |
| 6B.3 | LanguageMiddleware 语言切换中间件 | app/Http/Middleware/LanguageMiddleware.php | 中 | ✅ 已完成 |
| 6B.4 | AdminMiddleware 后台权限验证 | app/Http/Middleware/AdminMiddleware.php | 高 | ✅ 已完成 |
| 6B.5 | ActivityLogMiddleware 操作日志记录 | app/Http/Middleware/ActivityLogMiddleware.php | 中 | ✅ 已完成 |

---

## 第七阶段：后台 Controller 开发

> **状态：** ✅ 基本完成（24/30 已完成）
> **文件位置：** `app/Http/Controllers/Admin/`

### 7.1 内容管理 Controller

| 序号 | 任务 | 文件位置 | 依赖 | 优先级 | 状态 |
|:---:|------|----------|:----:|:------:|:----:|
| 7.1.1 | PostController 文章管理 | app/Http/Controllers/Admin/PostController.php | 1.1 | 高 | ✅ 已完成 |
| 7.1.2 | CategoryController 分类管理 | app/Http/Controllers/Admin/CategoryController.php | 1.1 | 高 | ✅ 已完成 |
| 7.1.3 | TagController 标签管理 | app/Http/Controllers/Admin/TagController.php | 1.1 | 高 | ✅ 已完成 |
| 7.1.4 | VideoController 视频管理 | app/Http/Controllers/Admin/VideoController.php | 1.1 | 高 | ✅ 已完成 |
| 7.1.5 | ProjectController 项目管理 | app/Http/Controllers/Admin/ProjectController.php | 1.1 | 高 | ✅ 已完成 |
| 7.1.6 | ResourceController 资源管理 | app/Http/Controllers/Admin/ResourceController.php | 1.1 | 中 | ✅ 已完成 |
| 7.1.7 | JournalsController 日记管理 | app/Http/Controllers/Admin/JournalsController.php | 2.5, 3.5, 6.11 | 中 | ✅ 已完成 |

### 7.2 用户管理 Controller

| 序号 | 任务 | 文件位置 | 依赖 | 优先级 | 状态 |
|:---:|------|----------|:----:|:------:|:----:|
| 7.2.1 | UsersController 用户管理 | app/Http/Controllers/Admin/UsersController.php | 2.5, 3.4, 4.6, 6.6 | 高 | ✅ 已完成 |
| 7.2.2 | RolesController 角色管理 | app/Http/Controllers/Admin/RolesController.php | 3.5, 4.7, 6.8 | 高 | ✅ 已完成 |
| 7.2.3 | PermissionController 权限管理 | app/Http/Controllers/Admin/PermissionController.php | 4.7, 6.9 | 高 | ⚠️ 待处理 |
| 7.2.4 | UserLevelsController 用户等级 | app/Http/Controllers/Admin/UserLevelsController.php | 6.6 | 中 | ✅ 已完成 |
| 7.2.5 | SubscribersController 订阅者 | app/Http/Controllers/Admin/SubscribersController.php | 3.5, 6.10 | 中 | ✅ 已完成 |

### 7.3 系统管理 Controller

| 序号 | 任务 | 文件位置 | 依赖 | 优先级 | 状态 |
|:---:|------|----------|:----:|:------:|:----:|
| 7.3.1 | CommentsController 评论管理 | app/Http/Controllers/Admin/CommentsController.php | 2.5, 4.8, 6.7 | 高 | ✅ 已完成 |
| 7.3.2 | MediaController 媒体管理 | app/Http/Controllers/Admin/MediaController.php | 4.9, 6.12 | 高 | ✅ 已完成 |
| 7.3.3 | SettingsController 系统设置 | app/Http/Controllers/Admin/SettingsController.php | 2.5, 4.10 | 高 | ✅ 已完成 |
| 7.3.4 | SeoController SEO管理 | app/Http/Controllers/Admin/SeoController.php | 2.5 | 中 | ✅ 已完成 |
| 7.3.5 | SocialLinksController 社交链接 | app/Http/Controllers/Admin/SocialLinksController.php | 2.5 | 中 | ✅ 已完成 |
| 7.3.6 | FrontMenuController 菜单管理 | app/Http/Controllers/Admin/FrontMenuController.php | 2.5 | 中 | ✅ 已完成 |
| 7.3.7 | MailConfigController 邮件配置 | app/Http/Controllers/Admin/MailConfigController.php | - | 中 | ✅ 已完成 |
| 7.3.8 | EmailTemplatesController 邮件模板 | app/Http/Controllers/Admin/EmailTemplatesController.php | - | 中 | ✅ 已完成 |
| 7.3.9 | I18nController 国际化 | app/Http/Controllers/Admin/I18nController.php | - | 中 | ✅ 已完成 |
| 7.3.10 | LogsController 日志查询 | app/Http/Controllers/Admin/LogsController.php | - | 中 | ✅ 已完成 |
| 7.3.11 | BackupController 备份管理 | app/Http/Controllers/Admin/BackupController.php | - | 中 | ✅ 已完成 |
| 7.3.12 | RestoreController 恢复管理 | app/Http/Controllers/Admin/RestoreController.php | - | 中 | ✅ 已完成 |
| 7.3.13 | AdvertisementsController 广告管理 | app/Http/Controllers/Admin/AdvertisementsController.php | - | 中 | ✅ 已完成 |
| 7.3.14 | NotificationsController 通知管理 | app/Http/Controllers/Admin/NotificationsController.php | - | 中 | ✅ 已完成 |

### 7.4 前台 Controller（Inertia页面）

| 序号 | 任务 | 文件位置 | 依赖 | 优先级 | 状态 |
|:---:|------|----------|:----:|:------:|:----:|
| 7.4.1 | HomeController 首页 | app/Http/Controllers/Frontend/HomeController.php | 1.1 | 高 | ✅ 已完成 |
| 7.4.2 | FrontendController 博客列表 | app/Http/Controllers/Frontend/FrontendController.php | 1.1 | 高 | ✅ 已完成 |
| 7.4.3 | BlogController 博客详情 | app/Http/Controllers/Frontend/BlogController.php | 1.1 | 高 | ✅ 已完成 |
| 7.4.4 | ProjectsController 项目列表 | app/Http/Controllers/Frontend/ProjectsController.php | 1.1 | 高 | ✅ 已完成 |
| 7.4.5 | ProjectDetailController 项目详情 | app/Http/Controllers/Frontend/ProjectDetailController.php | 1.1 | 高 | ⚠️ 待处理 |
| 7.4.6 | VideosController 视频列表 | app/Http/Controllers/Frontend/VideosController.php | 1.1 | 高 | ⚠️ 待处理 |
| 7.4.7 | VideoDetailController 视频详情 | app/Http/Controllers/Frontend/VideoDetailController.php | 1.1 | 高 | ⚠️ 待处理 |
| 7.4.8 | ResourcesController 资源页面 | app/Http/Controllers/Frontend/ResourcesController.php | 1.1 | 中 | ⚠️ 待处理 |
| 7.4.9 | AuthorController 作者页面 | app/Http/Controllers/Frontend/AuthorController.php | 1.1 | 中 | ⚠️ 待处理 |
| 7.4.10 | JournalController 日记页面 | app/Http/Controllers/Frontend/JournalController.php | 1.1 | 中 | ⚠️ 待处理 |

### 7.5 公开 API Controller

| 序号 | 任务 | 文件位置 | 依赖 | 优先级 | 状态 |
|:---:|------|----------|:----:|:------:|:----:|
| 7.5.1 | PublicPostController 公开文章API | app/Http/Controllers/Api/V1/PublicPostController.php | 6.1 | 高 | ⚠️ 待处理 |
| 7.5.2 | PublicVideoController 公开视频API | app/Http/Controllers/Api/V1/PublicVideoController.php | 6.4 | 高 | ⚠️ 待处理 |
| 7.5.3 | PublicProjectController 公开项目API | app/Http/Controllers/Api/V1/PublicProjectController.php | 6.5 | 高 | ⚠️ 待处理 |
| 7.5.4 | CommentController 公开评论API | app/Http/Controllers/Api/V1/CommentController.php | 2.5, 3.4, 6.7 | 高 | ⚠️ 待处理 |
| 7.5.5 | SubscribeController 订阅API | app/Http/Controllers/Api/V1/SubscribeController.php | 3.4 | 中 | ⚠️ 待处理 |
| 7.5.6 | InteractionController 互动API | app/Http/Controllers/Api/V1/InteractionController.php | 2.5 | 中 | ⚠️ 待处理 |
| 7.5.7 | SocialLinkController 公开社交链接API | app/Http/Controllers/Api/V1/SocialLinkController.php | - | 中 | ⚠️ 待处理 |

---

## 第八阶段：前台页面数据对接

> **状态：** 🔄 进行中（26/40 已完成）
> **文件位置：** `resources/js/Pages/`

### 8.1 首页与列表页

| 序号 | 页面 | 文件位置 | 依赖 | 优先级 | 状态 |
|:---:|------|----------|:----:|:------:|:----:|
| 8.1.1 | Home.vue 首页 | resources/js/Pages/front/Home.vue | 7.4.1 | 高 | ✅ 已完成 |
| 8.1.2 | Blog.vue 博客列表 | resources/js/Pages/front/Blog.vue | 7.4.2 | 高 | ✅ 已完成 |
| 8.1.3 | Projects.vue 项目列表 | resources/js/Pages/front/Projects.vue | 7.4.4 | 高 | ✅ 已完成 |
| 8.1.4 | Videos.vue 视频列表 | resources/js/Pages/front/Videos.vue | 7.4.6 | 高 | ✅ 已完成 |
| 8.1.5 | Resources.vue 资源页面 | resources/js/Pages/front/Resources.vue | 7.4.8 | 中 | ✅ 已完成 |

### 8.2 详情页

| 序号 | 页面 | 文件位置 | 依赖 | 优先级 | 状态 |
|:---:|------|----------|:----:|:------:|:----:|
| 8.2.1 | PostDetail.vue 文章详情 | resources/js/Pages/front/PostDetail.vue | 7.4.3 | 高 | ✅ 已完成 |
| 8.2.2 | ProjectDetail.vue 项目详情 | resources/js/Pages/front/ProjectDetail.vue | 7.4.5 | 高 | ⚠️ 待处理 |
| 8.2.3 | VideoDetail.vue 视频详情 | resources/js/Pages/front/VideoDetail.vue | 7.4.7 | 高 | ⚠️ 待处理 |
| 8.2.4 | Author.vue 作者页面 | resources/js/Pages/front/Author.vue | 7.4.9 | 中 | ✅ 已完成 |
| 8.2.5 | Journal.vue 日记页面 | resources/js/Pages/front/Journal.vue | 7.4.10 | 中 | ⚠️ 待处理 |

### 8.3 后台页面

| 序号 | 页面 | 文件位置 | 依赖 | 优先级 | 状态 |
|:---:|------|----------|:----:|:------:|:----:|
| 8.3.1 | Index.vue 管理后台首页 | resources/js/Pages/admin/Index.vue | 7.3 | 高 | ✅ 已完成 |
| 8.3.2 | Posts.vue 文章管理 | resources/js/Pages/admin/Posts.vue | 7.1.1 | 高 | ✅ 已完成 |
| 8.3.3 | Categories.vue 分类管理 | resources/js/Pages/admin/Categories.vue | 7.1.2 | 高 | ✅ 已完成 |
| 8.3.4 | Tags.vue 标签管理 | resources/js/Pages/admin/Tags.vue | 7.1.3 | 高 | ✅ 已完成 |
| 8.3.5 | Videos.vue 视频管理 | resources/js/Pages/admin/Videos.vue | 7.1.4 | 高 | ✅ 已完成 |
| 8.3.6 | Projects.vue 项目管理 | resources/js/Pages/admin/Projects.vue | 7.1.5 | 高 | ✅ 已完成 |
| 8.3.7 | Resources.vue 资源管理 | resources/js/Pages/admin/Resources.vue | 7.1.6 | 中 | ✅ 已完成 |
| 8.3.8 | Journals.vue 日记管理 | resources/js/Pages/admin/Journals.vue | 7.1.7 | 中 | ✅ 已完成 |
| 8.3.9 | Users.vue 用户管理 | resources/js/Pages/admin/Users.vue | 7.2.1 | 高 | ✅ 已完成 |
| 8.3.10 | Roles.vue 角色管理 | resources/js/Pages/admin/Roles.vue | 7.2.2 | 高 | ✅ 已完成 |
| 8.3.11 | Comments.vue 评论管理 | resources/js/Pages/admin/Comments.vue | 7.3.1 | 高 | ✅ 已完成 |
| 8.3.12 | Media.vue 媒体库 | resources/js/Pages/admin/Media.vue | 7.3.2 | 高 | ✅ 已完成 |
| 8.3.13 | Settings.vue 系统设置 | resources/js/Pages/admin/Settings.vue | 7.3.3 | 高 | ✅ 已完成 |
| 8.3.14 | Subscribers.vue 订阅者 | resources/js/Pages/admin/Subscribers.vue | 7.2.5 | 中 | ✅ 已完成 |
| 8.3.15 | UserLevels.vue 用户等级 | resources/js/Pages/admin/UserLevels.vue | 7.2.4 | 中 | ✅ 已完成 |
| 8.3.16 | SeoManager.vue SEO管理 | resources/js/Pages/admin/SeoManager.vue | 7.3.4 | 中 | ✅ 已完成 |
| 8.3.17 | SocialLinks.vue 社交链接 | resources/js/Pages/admin/SocialLinks.vue | 7.3.5 | 中 | ✅ 已完成 |
| 8.3.18 | FrontMenu.vue 菜单管理 | resources/js/Pages/admin/FrontMenu.vue | 7.3.6 | 中 | ✅ 已完成 |
| 8.3.19 | MailConfig.vue 邮件配置 | resources/js/Pages/admin/MailConfig.vue | 7.3.7 | 中 | ✅ 已完成 |
| 8.3.20 | EmailTemplates.vue 邮件模板 | resources/js/Pages/admin/EmailTemplates.vue | 7.3.8 | 中 | ✅ 已完成 |
| 8.3.21 | I18nManager.vue 国际化 | resources/js/Pages/admin/I18nManager.vue | 7.3.9 | 中 | ✅ 已完成 |
| 8.3.22 | Logs.vue 日志查询 | resources/js/Pages/admin/Logs.vue | 7.3.10 | 中 | ✅ 已完成 |
| 8.3.23 | Backup.vue 备份管理 | resources/js/Pages/admin/Backup.vue | 7.3.11 | 中 | ✅ 已完成 |
| 8.3.24 | Restore.vue 恢复管理 | resources/js/Pages/admin/Restore.vue | 7.3.12 | 中 | ✅ 已完成 |
| 8.3.25 | Advertisements.vue 广告管理 | resources/js/Pages/admin/Advertisements.vue | 7.3.13 | 中 | ✅ 已完成 |
| 8.3.26 | Notifications.vue 通知管理 | resources/js/Pages/admin/Notifications.vue | 7.3.14 | 中 | ✅ 已完成 |

### 8.4 通用组件数据对接

| 序号 | 组件 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 8.4.1 | Footer.vue | resources/js/components/Footer.vue | 高 | ⚠️ 待处理 |
| 8.4.2 | CommentSection.vue | resources/js/components/CommentSection.vue | 高 | ⚠️ 待处理 |
| 8.4.3 | AdSlot.vue | resources/js/components/front/AdSlot.vue | 中 | ⚠️ 待处理 |
| 8.4.4 | AdPopup.vue | resources/js/components/front/AdPopup.vue | 中 | ⚠️ 待处理 |
| 8.4.5 | SearchOverlay.vue | resources/js/components/SearchOverlay.vue | 中 | ⚠️ 待处理 |
| 8.4.6 | ShareModal.vue | resources/js/components/ShareModal.vue | 低 | ⚠️ 待处理 |
| 8.4.7 | ResourceModal.vue | resources/js/components/ResourceModal.vue | 低 | ⚠️ 待处理 |
| 8.4.8 | SidebarMenu.vue | resources/js/components/SidebarMenu.vue | 高 | ⚠️ 待处理 |
| 8.4.9 | ToastContainer.vue | resources/js/components/ToastContainer.vue | 高 | ✅ 已完成 |

---

## 第九阶段：数据清理与验证

> **状态：** ⚠️ 待处理

### 9.1 功能验证

| 序号 | 任务 | 依赖 | 优先级 | 状态 |
|:---:|------|:------:|:------:|:----:|
| 9.1.1 | 验证前台所有页面正常访问 | 第八阶段 | 高 | ⚠️ 待处理 |
| 9.1.2 | 验证后台所有页面正常访问 | 第八阶段 | 高 | ⚠️ 待处理 |
| 9.1.3 | 验证前后台数据一致 | 第八阶段 | 高 | ⚠️ 待处理 |
| 9.1.4 | 验证表单提交功能正常 | 第七阶段 | 高 | ⚠️ 待处理 |
| 9.1.5 | 验证搜索筛选功能正常 | 第七阶段 | 高 | ⚠️ 待处理 |

### 9.2 代码清理

| 序号 | 任务 | 优先级 | 状态 |
|:---:|------|:------:|:----:|
| 9.2.1 | 删除 resources/js/data/*.js 文件 | 高 | ⚠️ 待处理 |
| 9.2.2 | 删除 vue-router 相关代码 | 高 | ⚠️ 待处理 |
| 9.2.3 | 移除 RouterLink，改用 Inertia Link | 高 | ⚠️ 待处理 |
| 9.2.4 | 清理 app.js 中的 router 配置 | 高 | ⚠️ 待处理 |

---

## 第十阶段：前台API认证系统（移动端APP）

> **状态：** ✅ 已完成

### 10.1 认证基础设施

| 序号 | 任务 | 文件位置 | 优先级 | 状态 |
|:---:|------|----------|:------:|:----:|
| 10.1.1 | 配置 auth.php api guard | config/auth.php | 高 | ✅ 已完成 |
| 10.1.2 | 创建 personal_access_tokens 迁移 | database/migrations/ | 高 | ✅ 已完成 |
| 10.1.3 | 创建 AuthController | app/Http/Controllers/Api/V1/AuthController.php | 高 | ✅ 已完成 |
| 10.1.4 | 创建 LoginRequest | app/Http/Requests/LoginRequest.php | 高 | ✅ 已完成 |
| 10.1.5 | 配置 routes/api.php 路由 | routes/api.php | 高 | ✅ 已完成 |
| 10.1.6 | 编写认证测试用例 | tests/Feature/Api/V1/AuthTest.php | 高 | ✅ 已完成 |

### 10.2 API端点清单

| 端点 | 方法 | 认证 | 说明 |
|------|:----:|:----:|------|
| `/api/login` | POST | 🔓 公开 | 用户登录，返回 Bearer Token |
| `/api/logout` | POST | 🔐 需认证 | 用户登出，撤销 Token |
| `/api/v1/posts` | GET | 🔐 需认证 | 获取文章列表 |
| `/api/v1/posts/{slug}` | GET | 🔐 需认证 | 获取文章详情 |
| `/api/v1/videos` | GET | 🔐 需认证 | 获取视频列表 |
| `/api/v1/projects` | GET | 🔐 需认证 | 获取项目列表 |
| `/api/v1/resources` | GET | 🔐 需认证 | 获取资源列表 |
| `/api/v1/categories` | GET | 🔐 需认证 | 获取分类列表 |
| `/api/v1/tags` | GET | 🔐 需认证 | 获取标签列表 |
| `/api/v1/me` | GET | 🔐 需认证 | 获取当前用户信息 |
| `/api/v1/roles` | GET | 🔐 需认证 | 获取角色列表（含权限） |
| `/api/v1/roles/{role}` | GET | 🔐 需认证 | 获取单个角色详情 |
| `/api/v1/permissions` | GET | 🔐 需认证 | 获取所有权限列表 |
| `/api/v1/roles/{role}/permissions` | PUT | 🔐 需认证 | 同步角色权限（syncPermissions） |

### 10.3 测试验证

| 序号 | 测试用例 | 状态 |
|:---:|------|:----:|
| 10.3.1 | test_login_success | ✅ 通过 |
| 10.3.2 | test_login_with_invalid_credentials | ✅ 通过 |
| 10.3.3 | test_login_with_missing_fields | ✅ 通过 |
| 10.3.4 | test_login_with_invalid_email_format | ✅ 通过 |
| 10.3.5 | test_unauthenticated_access_to_protected_api | ✅ 通过 |
| 10.3.6 | test_authenticated_access_to_protected_api | ✅ 通过 |
| 10.3.7 | test_invalid_token_access_to_protected_api | ✅ 通过 |
| 10.3.8 | test_logout_success | ✅ 通过 |
| 10.3.9 | test_get_current_user_info | ✅ 通过 |

---

## 附录

### A. 执行顺序建议

```
第一阶段（MockDataService）✅
    ↓
第二阶段（Service层） ← 当前重点
    ↓
第三阶段（FormRequest） ← 并行进行
    ↓
第四阶段（Policy） ← 并行进行
    ↓
第五阶段（Observer）
    ↓
第六阶段（API Resource）✅ 部分完成
    ↓
第七阶段（Controller）← 交叉执行 ↓
第八阶段（前台页面）✅ 大部分完成 ← 交叉执行
    ↓
第九阶段（清理验证）
```

### B. 依赖关系说明

- **第一阶段** 是所有阶段的基础，必须首先完成 ✅
- **第二～四阶段** 可以并行开发，互相之间无依赖（当前重点）
- **第五阶段** 可以后续开发，不影响核心功能
- **第六阶段** 已部分完成，可以继续完善
- **第七阶段** 已基本完成，可以继续完善
- **第八阶段** 已大部分完成，可以继续完善
- **第九阶段** 是最后阶段，验证所有功能正常后清理

### C. 进度追踪

| 阶段 | 开始日期 | 完成日期 | 状态 |
|:----:|:--------:|:--------:|:----:|
| 第一阶段 | - | 2026-05-25 | ✅ 已完成 |
| 第二阶段 | - | - | ⏳ 待开始 |
| 第三阶段 | - | 2026-05-27 | 🔄 部分完成 |
| 第四阶段 | - | 2026-05-27 | 🔄 部分完成 |
| 第五阶段 | - | - | ⏳ 待开始 |
| 第六阶段 | - | 2026-05-26 | 🔄 大部分完成 |
| 第七阶段 | - | 2026-05-27 | ✅ 基本完成 |
| 第八阶段 | - | 2026-05-27 | 🔄 大部分完成 |
| 第九阶段 | - | - | ⏳ 待开始 |
| 第十阶段 | 2026-05-27 | 2026-05-27 | ✅ 已完成 |

### D. 当前重点任务

1. **第十阶段**：前台API认证系统已全部完成（9个测试用例通过）
2. **第九阶段**：数据清理与验证
3. **第二～四阶段**：开发各个 Service/FormRequest/Policy 层
