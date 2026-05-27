# 后端功能开发路线图

**项目名称：** ARCHYX - Laravel Vue.js 混合应用
**最后更新：** 2026-05-27 (文档同步，所有问题已修复)
**版本：** 2.3
**Laravel版本：** 11 (精简模式)

---

## 1. 项目概述

### 1.1 技术架构
- **后端框架：** Laravel 11
- **前台展示：** Inertia.js + Vue 3（SPA 体验，SEO友好）
- **后台管理：** Inertia.js + Vue 3（SPA 体验，安全性高）
- **数据库：** MySQL 8.0+ with Eloquent ORM
- **认证系统：** Laravel Sanctum + Spatie Permission (RBAC)
- **缓存系统：** Redis 性能优化

### 1.2 架构模式说明

| 端 | 渲染方式 | 数据获取方式 | 说明 |
|----|----------|--------------|------|
| **前台网站** | Inertia.js + Vue 3 | 通过 Inertia props 注入 | SPA 体验，SEO友好 |
| **后台管理** | Inertia.js + Vue 3 | 通过 Inertia props 注入 | SPA 体验，安全性高 |
| **移动端 APP** | - | 通过 API 获取 | 外部客户端调用 `/api/v1/*` |
| **第三方集成** | - | 通过 API 获取 | Webhook 和 REST API |

> **重要说明：** 采用 **Inertia.js 现代单体架构**。前台和后台均使用 Inertia.js 将 Laravel 后端数据直接传递给 Vue 组件，保留 SPA 丝滑体验。API 路由独立保留供移动端 APP 和第三方系统使用。

### 1.3 Laravel 设计原则遵循

| 原则 | 实现方式 | 项目现状 |
|------|----------|----------|
| **单一职责原则 (SRP)** | Controller只做路由分发，业务逻辑在Service层 | ✅ 已采用 |
| **依赖注入 (DI)** | 通过构造函数注入Service；采用轻量级Repository模式 | ✅ 已采用 |
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
| **前台路由** | `/` | `App\Http\Controllers\Frontend\` | Inertia.js 渲染，返回 Vue 组件 |
| **后台路由** | `/admin` | `App\Http\Controllers\Admin\` | Inertia.js 渲染，需登录，返回 Vue 组件 |
| **API路由** | `/api/v1` | `App\Http\Controllers\Api\V1\` | JSON响应，供APP/第三方使用 |
| **公开API** | `/api` | `App\Http\Controllers\Api\V1\` | 无需认证的公开接口 |

---

## 2. 模块开发清单

### 2.1 内容管理模块

#### 2.1.1 文章 (Posts)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | posts表 | Model使用$fillable/$casts |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5篇高质量文章（2026-05-25优化） | 完整Markdown、封面图、SEO、浏览量/点赞数 |
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
| 迁移文件 & 模型 | 高 | ✅ 已完成 | videos表 | 标准Eloquent模型 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5条视频数据 | 包含YouTube/Bilibili |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/videos | Resource+Service模式 |
| 平台集成 | 中 | ⚠️ 待处理 | YouTube/Bilibili | 自定义Service封装 |
| 缩略图处理 | 中 | ⚠️ 待处理 | 自动获取/手动 | Intervention Image |

#### 2.1.3 项目 (Projects)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | projects表 | 标准Model |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 6条项目数据 | 包含技术栈、GitHub链接 |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/projects | 轻量级Repository模式 |
| 图片画廊 | 中 | ⚠️ 待处理 | 多图关联 | Spatie Media多集合 |

#### 2.1.4 资源 (Resources)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | resources表 | download_count字段 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5条资源数据 | 包含下载链接、文件大小 |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/resources | 标准CRUD |
| 下载统计 | 中 | ⚠️ 待处理 | 访问时递增 | 事件监听 |

#### 2.1.5 日记 (Journals)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | journals表 | mood/weather JSON |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5条详细日志（2026-05-25优化） | 含title、date、likes_count字段 |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/journals | 标准CRUD |
| 日记专属字段 | 中 | ⚠️ 待处理 | music_link等 | JSON Cast |

#### 2.1.6 分类 (Categories)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | categories表 | 层级结构 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 6条专业分类（2026-05-25优化） | THEORY, DESIGN, CULTURE等 |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/categories | 树形构建器 |
| 树形结构 | 中 | ⚠️ 待处理 | 递归关系 | NestedSetModel |

#### 2.1.7 标签 (Tags)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | tags表 | usage_count |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 15条专业标签（2026-05-25优化） | Architecture, Design等 |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/tags | 标准CRUD |
| 自动清理 | 低 | ⚠️ 待处理 | 未使用标签删除 | Artisan命令 |

---

### 2.2 用户与认证模块

#### 2.2.1 用户管理
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| users表扩展 | 高 | ✅ 已完成 | 通过 model_has_roles 关联 | Spatie RBAC |
| User Policy | 高 | ⚠️ 待处理 | 授权逻辑 | Policy类 |
| 用户资料更新 | 高 | ⚠️ 待处理 | /api/admin/users/{id} | FormRequest验证 |
| 密码重置 | 高 | ⚠️ 待处理 | Laravel内置 | 邮件通知 |
| 头像上传 | 中 | ⚠️ 待处理 | Spatie Media | Avatar集合 |
| 积分系统 | 中 | ⚠️ 待处理 | 积分API | Observer更新 |
| 活动追踪 | 低 | ⚠️ 待处理 | last_login_at | LoginListener |
| 测试用户数据 | 高 | ✅ 已完成 | 4个用户（2026-05-25） | Admin, Editor, Author, Subscriber |

#### 2.2.2 订阅者 (Newsletter)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | subscribers表 | email唯一性 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5条订阅数据 | 包含邮箱、状态 |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/subscribers | 标准CRUD |
| 订阅控制器 | 中 | ⚠️ 待处理 | /api/subscribe | 邮件验证 |
| CSV导出 | 低 | ⚠️ 待处理 | Excel导出 | Laravel Excel |

#### 2.2.3 用户等级 (VIP)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | user_levels表 | benefits JSON |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5条等级数据 | 包含权益描述 |
| 等级分配逻辑 | 高 | ⚠️ 待处理 | 自动升级 | Observer/Job |
| 等级权益API | 中 | ⚠️ 待处理 | 折扣计算 | 计算型Service |
| 积分历史 | 中 | ⚠️ 待处理 | user_points_history | 流水记录 |

#### 2.2.4 RBAC权限系统
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| Spatie Permission | 高 | ✅ 已完成 | 完整配置 | Trait + 中间件 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 7角色+权限（2026-05-25优化） | Administrator, Editor, Author等 |
| Role Policy | 高 | ⚠️ 待处理 | 角色授权 | 自定义Gate |
| Permission CRUD | 高 | ⚠️ 待处理 | /api/admin/permissions | 标准CRUD |
| 角色-权限UI | 高 | ⚠️ 待处理 | 同步API | JS树形组件 |

---

### 2.3 系统配置模块

#### 2.3.1 网站设置
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | settings表 | key-value |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 10条配置数据 | 站点名称、品牌等 |
| Setting Service | 高 | ⚠️ 待处理 | 配置获取 | 单例+缓存 |
| 网站配置API | 高 | ⚠️ 待处理 | /api/admin/settings | 标准CRUD |
| 功能开关 | 中 | ⚠️ 待处理 | feature_* | 配置缓存 |

#### 2.3.2 SEO管理器
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | seo表 | key-value JSON |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 3条SEO数据 | 首页、博客、关于 |
| SEO Controller | 高 | ⚠️ 待处理 | /api/admin/seo | 标准CRUD |
| 动态SEO中间件 | 中 | ⚠️ 待处理 | 按路由加载 | Middleware |
| Schema.org | 中 | ⚠️ 待处理 | JSON-LD | Seeder初始化 |

#### 2.3.3 国际化 (I18n)
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| languages表 | 高 | ✅ 已完成 | 语言配置 | Seeder 5条数据 |
| translations表 | 高 | ✅ 已完成 | 翻译存储 | Seeder 20条数据 |
| I18n Controller | 高 | ⚠️ 待处理 | /api/admin/i18n | 标准CRUD |
| JSON导出 | 中 | ⚠️ 待处理 | vue-i18n同步 | Artisan命令 |
| 语言切换中间件 | 中 | ⚠️ 待处理 | locale设置 | Middleware |

#### 2.3.4 媒体库
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| 迁移文件 & 模型 | 高 | ✅ 已完成 | media表 | 标准结构 |
| API CRUD接口 | 高 | ⚠️ 待处理 | /api/admin/media | 标准CRUD |
| Spatie Media Library | 高 | ⚠️ 待处理 | 完整配置 | Trait |
| 媒体上传API | 高 | ⚠️ 待处理 | /api/admin/media/upload | 异步处理 |
| 图片优化 | 中 | ⚠️ 待处理 | 缩略图 | Imagecache |
| 文件验证 | 中 | ⚠️ 待处理 | 白名单 | FormRequest |

#### 2.3.5 邮件配置
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| mail_configs表 | 高 | ✅ 已完成 | 多配置 | is_default |
| Mailable类 | 中 | ⚠️ 待处理 | 邮件模板 | Mailable |
| 测试邮件 | 中 | ⚠️ 待处理 | 发送测试 | Queue邮件 |

#### 2.3.6 前台菜单
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| menus表 | 高 | ✅ 已完成 | 层级结构 | parent_id |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 8条菜单数据 | 包含父子关系 |
| Menu Builder | 中 | ⚠️ 待处理 | 递归构建 | Cache缓存 |

---

### 2.4 互动社交模块

#### 2.4.1 评论
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| comments表 | 高 | ✅ 已完成 | 多态关联 | commentable |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 6条真实评论（2026-05-25优化） | 含嵌套回复parent_id |
| Comment Service | 高 | ⚠️ 待处理 | 业务逻辑 | 独立Service |
| 审核流程 | 中 | ⚠️ 待处理 | require_approval | 事件监听 |
| 嵌套回复 | 低 | ⚠️ 待处理 | parent_id | 递归关系 |

#### 2.4.2 广告管理
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| ad_positions表 | 高 | ✅ 已完成 | 广告位 | position_key唯一 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5条广告位数据 | 包含首页、侧边栏 |
| advertisements表 | 高 | ✅ 已完成 | 广告内容 | position_id |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 12条广告数据 | 覆盖不同广告位 |
| 广告轮换 | 中 | ⚠️ 待处理 | 权重选择 | Seeder |
| 追踪事件 | 中 | ⚠️ 待处理 | view/click | Event |

#### 2.4.3 通知
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| notifications表 | 高 | ✅ Laravel内置 | 数据库通知 | Notifiable |
| 通知Service | 中 | ⚠️ 待处理 | 发送逻辑 | Notification类 |
| 批量操作 | 低 | ⚠️ 待处理 | 全部已读 | Chunk处理 |

#### 2.4.4 社交链接
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| social_links表 | 高 | ✅ 已完成 | 平台配置 | platform唯一 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 6条社交链接 | GitHub、Twitter等 |
| 公开API | 中 | ⚠️ 待处理 | /api/social-links | 缓存响应 |

#### 2.4.5 作者资料
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| author_profiles表 | 高 | ✅ 已完成 | 用户扩展 | user_id唯一 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 1条作者数据 | 包含社交链接、技能 |
| 技能JSON字段 | 中 | ⚠️ 待处理 | skills数组 | JSON Cast |

#### 2.4.6 互动数据
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| interactions表 | 中 | ✅ 已完成 | 多态关联 | 聚合查询 |
| Seeder 模拟数据 | 中 | ✅ 已完成 | 20条互动数据 | 点赞、收藏 |
| 每日统计 | 低 | ⚠️ 待处理 | Aggregate Job | Schedule |

---

### 2.5 系统维护模块

#### 2.5.1 审计日志
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| admin_logs表 | 高 | ✅ 已完成 | 审计追踪 | 迁移完成 |
| Spatie Activitylog | 高 | ⚠️ 待处理 | 完整配置 | Trait |
| 日志查询API | 中 | ⚠️ 待处理 | /api/admin/logs | 筛选+分页 |
| 日志清理 | 低 | ⚠️ 待处理 | 删除旧日志 | Schedule |

#### 2.5.2 备份与恢复
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| backups表 | 高 | ✅ 已完成 | 备份记录 | 迁移完成 |
| Spatie Backup | 高 | ⚠️ 待处理 | 完整备份 | 配置 |
| 备份命令 | 高 | ⚠️ 待处理 | /api/admin/backup | Artisan |
| 恢复命令 | 高 | ⚠️ 待处理 | /api/admin/restore | 确认机制 |
| 定时备份 | 中 | ⚠️ 待处理 | cron配置 | Schedule |

#### 2.5.3 邮件模板
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| email_templates表 | 高 | ✅ 已完成 | 模板存储 | 迁移完成 |
| Mailable类 | 中 | ⚠️ 待处理 | 邮件模板 | Mailable |
| 变量解析 | 中 | ⚠️ 待处理 | {{var}} | Str::replace |
| 预览功能 | 低 | ⚠️ 待处理 | 渲染预览 | 临时数据 |

#### 2.5.4 页脚链接
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| footer_links表 | 高 | ✅ 已完成 | 页脚配置 | 迁移完成 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 8条链接数据 | 包含社交、法律等 |
| 公开API | 中 | ⚠️ 待处理 | /api/footer-links | 缓存响应 |

#### 2.5.5 主题配置
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| themes表 | 高 | ✅ 已完成 | 主题配置 | 迁移完成 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 3条主题数据 | 包含预览图 |
| 主题切换 | 中 | ⚠️ 待处理 | 动态切换 | SettingService |

#### 2.5.6 访问统计
| 任务 | 优先级 | 状态 | 后端需求 | Laravel最佳实践 |
|------|:------:|:----:|----------|-----------------|
| visits表 | 高 | ✅ 已完成 | 多态关联 | 迁移完成 |
| Seeder 模拟数据 | 高 | ✅ 已完成 | 5条访问数据 | 包含IP、UA |
| Visit Observer | 高 | ⚠️ 待处理 | 自动记录 | Observer |
| 统计API | 中 | ⚠️ 待处理 | /api/admin/visits | 聚合查询 |

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
│   │   ├── Admin/                   # 管理后台控制器 (现有) - Inertia.js
│   │   │   ├── PostController.php
│   │   │   └── ...
│   │   ├── Api/
│   │   │   └── V1/                  # API V1版本控制器 (现有)
│   │   │       ├── PostController.php
│   │   │       └── ...
│   │   └── Frontend/                # 前台页面控制器 - Inertia.js
│   │       ├── HomeController.php
│   │       ├── BlogController.php
│   │       └── ...
│   ├── Middleware/
│   │   ├── AdminMiddleware.php
│   │   ├── SeoMiddleware.php
│   │   └── HandleInertiaRequests.php # Inertia.js 中间件
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
│   └── app.blade.php               # Inertia.js 根模板
└── js/                             # Vue 应用（Inertia.js 页面组件）
    ├── Pages/                      # Inertia.js 页面组件
    │   ├── front/                  # 前台页面
    │   │   ├── Home.vue
    │   │   ├── Blog.vue
    │   │   └── ...
    │   └── admin/                  # 后台页面
    │       ├── Layout.vue
    │       ├── Posts.vue
    │       └── ...
    ├── components/                 # 共享组件
    └── app.js                      # Inertia.js 应用入口
```

> **架构说明：**
> - **前台**和**后台**均使用 **Inertia.js** 架构，Controller 通过 `Inertia::render()` 返回 Vue 组件和数据 props
> - **API** 仅供移动端 APP 和第三方系统调用，使用 `Api/V1/` 目录下的控制器和 `Resources/V1/` 资源转换
> - Vue 组件作为完整页面使用，通过 `defineProps()` 接收后端传递的数据
> - 路由完全由 Laravel 的 `routes/web.php` 控制，无需 Vue Router

---

### 3.2 核心模式

#### 3.2.0 Inertia.js Controller (页面控制器) ✅ 已采用

```php
// app/Http/Controllers/Frontend/HomeController.php
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __construct(protected PostService $postService) {}

    public function index(): Response
    {
        $posts = $this->postService->getRecentPosts(6);
        $projects = Project::latest()->take(3)->get();
        $videos = Video::latest()->take(3)->get();
        
        return Inertia::render('front/Home', [
            'posts' => PostResource::collection($posts),
            'projects' => ProjectResource::collection($projects),
            'videos' => VideoResource::collection($videos),
        ]);
    }
}
```

```vue
<!-- resources/js/Pages/front/Home.vue -->
<script setup>
defineProps({
  posts: { type: Array, default: () => [] },
  projects: { type: Array, default: () => [] },
  videos: { type: Array, default: () => [] },
});
</script>

<template>
  <div>
    <PostList :posts="posts" />
    <ProjectList :projects="projects" />
    <VideoList :videos="videos" />
  </div>
</template>
```

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

---

## 9. 开发进度总结

### 9.1 已完成工作（截至 2026-05-25）

| 模块 | 完成状态 | 说明 |
|:---:|:---:|------|
| **数据库迁移** | ✅ 100% | 25+ 迁移文件，覆盖所有业务表 |
| **数据填充 (Seeder)** | ✅ 100% | 25个 Seeder，约200条高质量模拟数据 |
| **模型文件 (Models)** | ✅ 100% | 所有模型与迁移文件字段一致 |
| **前端数据文件** | ✅ 100% | data 目录数据完整 |
| **字段备注优化** | ✅ 100% | 所有迁移文件字段添加中文备注 |
| **数据库设计规范** | ✅ 100% | 字段命名规范化，删除冗余字段 |

### 9.2 数据优化详情

#### 角色与权限
- **7个角色**：Administrator, Editor, Author, Moderator, Subscriber, API, Guest
- **Spatie RBAC**：通过 `model_has_roles` 中间表管理角色关系
- **删除冗余**：users 表已删除 `role_id` 字段

#### 内容数据
- **5篇高质量文章**：完整 Markdown 内容、封面图、SEO 数据、浏览量/点赞数
- **6个专业分类**：THEORY, DESIGN, CULTURE, SYSTEM-DESIGN, ENGINEERING, HISTORY
- **15个专业标签**：Architecture, Design, Technology 等
- **6条真实评论**：含嵌套评论（parent_id）

#### 用户数据
- **4个测试用户**：Admin, Editor, Author, Subscriber
- **角色分配**：通过 Spatie `assignRole()` 方法分配
- **作者资料**：每个用户都有对应的 AuthorProfile

#### 其他数据
- **5条详细日志**：含 title、date、likes_count 字段
- **系统设置**：站点名称、品牌、功能开关等
- **SEO配置**：首页、博客、关于页面 SEO
- **菜单数据**：8条菜单，含父子关系
- **社交链接**：GitHub、Twitter、LinkedIn 等

### 9.3 下一步开发建议

根据 BACKEND_CHECKLIST.md，建议按以下优先级开发：

1. **高优先级** - 核心功能
   - 安装配置 Laravel Sanctum（认证系统）
   - 创建各模块的 Model、Controller、Service
   - 创建 FormRequest 验证类
   - 创建 API Resource 响应格式化

2. **中优先级** - 重要功能
   - 创建 Policy 授权策略
   - 创建 Observer 模型事件
   - 创建 Event/Listener 事件驱动
   - 创建 Notification 通知系统

3. **低优先级** - 辅助功能
   - 安装 spatie/laravel-activitylog
   - 安装 spatie/laravel-backup
   - 创建 Artisan 命令
   - 创建中间件
