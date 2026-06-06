# 前后端对接与同步指南 (Full-Stack Integration Guide)

本文档旨在指导 `laravel-vue-app` 从静态 Mock 数据向 Laravel 后端 API 驱动架构的平滑过渡。

**最后更新：** 2026-06-06
**状态：** ✅ 全部对接完成

### 架构模式说明

采用 **轻量级 Repository 模式**：
- **简单 CRUD**：直接在 Service 中使用 Model
- **复杂查询**：委托给 Repository 类封装
- **Repository 层**：`app/Repositories/` - 数据访问层

数据流向：`Controller → Service → Repository/Model → Database`

---

## 一、 数据库与模型同步 (Database & Models)

### 1. 已创建的数据文件 (Completed Data Files)

| 模块 | 数据文件 | 核心字段 | 状态 |
| :--- | :--- | :--- | :--- |
| **广告位** | `ad_positions.js` | `id`, `name`, `label_key`, `description`, `default_width`, `default_height`, `is_active`, `sort_order` | ✅ 已完成 |
| **日志管理** | `journals.js` | `id`, `user_id`, `title`, `content`, `mood`, `weather`, `date`, `is_public` | ✅ 已完成 |
| **菜单管理** | `menu.js` | `id`, `type`, `parent_id`, `label_key`, `icon_name`, `path`, `sort_order` | ✅ 已完成 |
| **角色权限** | `roles.js`, `permissions.js`, `role_permissions.js` | 角色/权限定义及关联 | ✅ 已完成 |
| **系统设置** | `settings.js` | 系统配置项 | ✅ 已完成 |
| **媒体文件** | `media.js` | 媒体资源管理 | ✅ 已完成 |
| **操作日志** | `logs.js` | 管理员操作记录 | ✅ 已完成 |
| **备份记录** | `backup.js` | 数据库备份信息 | ✅ 已完成 |
| **用户等级** | `user_levels.js` | 用户等级体系 | ✅ 已完成 |
| **访问统计** | `visits.js` | 访问记录统计 | ✅ 已完成 |
| **邮件模板** | `email_templates.js` | 邮件模板管理 | ✅ 已完成 |
| **友情链接** | `links.js` | 友情链接管理 | ✅ 已完成 |
| **邮件配置** | `mail_config.js` | 邮件服务器配置 | ✅ 已完成 |
| **宣言/理念** | `manifestos.js` | 网站宣言内容 | ✅ 已完成 |
| **技能/能力** | `skills.js` | 技能展示管理 | ✅ 已完成 |

### 2. 待创建的数据库表 (Missing Tables)

| 模块 | 建议表名 | 核心字段 | 状态 |
| :--- | :--- | :--- | :--- |
| **广告位** | `ad_positions` | `id`, `name`, `label_key`, `description`, `default_width`, `default_height`, `is_active`, `sort_order` | ✅ 已创建 |
| **日志管理** | `journals` | `id`, `user_id`, `title`, `content`, `mood`, `weather`, `date`, `is_public` | ✅ 已创建 |
| **广告管理** | `advertisements` | `id`, `position_id`, `title`, `image_url`, `link_url`, `position`, `is_active` | ✅ 已创建 |
| **用户等级** | `user_levels` | `id`, `name`, `min_points`, `max_points`, `level`, `badge` | ✅ 已创建 |
| **访问记录** | `visits` | `id`, `ip`, `user_agent`, `page`, `referrer`, `visited_at` | ✅ 已创建 |
| **邮件模板** | `email_templates` | `id`, `name`, `subject`, `content`, `variables` | ✅ 已创建 |
| **友情链接** | `links` | `id`, `name`, `url`, `description`, `logo`, `is_active` | ✅ 已创建 |
| **宣言/理念** | `manifestos` | `id`, `title`, `content`, `is_active` | ✅ 已创建 |
| **技能/能力** | `skills` | `id`, `name`, `description`, `level`, `icon` | ✅ 已创建 |

### 3. 现有表字段补全/优化建议
*   **Users 表**: 增加 `avatar` (头像路径), `last_login_at` (最后登录时间), `status` (激活/禁用)。
*   **Posts 表**: 确保 `status` 字段支持 `['draft', 'published', 'archived']`。
*   **Comments 表**: 增加 `is_approved` (审核状态), `parent_id` (支持楼中楼回复)。
*   **Advertisements 表**: 增加 `position_id` 外键关联 `ad_positions` 表。

---

## 二、 API 契约设计 (API Contract Design)

### 1. 广告管理 (Advertisement Management)
*   `GET /api/v1/ad-positions`: 获取广告位列表
*   `GET /api/v1/ad-positions/{id}`: 获取单个广告位
*   `POST /api/v1/ad-positions`: 创建广告位
*   `PUT /api/v1/ad-positions/{id}`: 更新广告位
*   `DELETE /api/v1/ad-positions/{id}`: 删除广告位
*   `GET /api/v1/advertisements`: 获取广告列表（支持按广告位筛选）
*   `POST /api/v1/advertisements`: 创建广告
*   `PUT /api/v1/advertisements/{id}`: 更新广告
*   `DELETE /api/v1/advertisements/{id}`: 删除广告

### 2. 日志管理 (Journal Management)
*   `GET /api/v1/journals`: 获取日志列表（支持分页、心情/天气/状态筛选）
*   `GET /api/v1/journals/{id}`: 获取单个日志
*   `POST /api/v1/journals`: 创建日志
*   `PUT /api/v1/journals/{id}`: 更新日志
*   `DELETE /api/v1/journals/{id}`: 删除日志
*   `GET /api/v1/journals/user/{userId}`: 获取用户日志列表

### 3. 媒体库 (Media Library)
*   `GET /admin/api/media`: 返回文件列表。
*   `POST /admin/api/media/upload`: 处理二进制上传，存入 `storage/app/public/media`。
*   `DELETE /admin/api/media/{id}`: 同步删除磁盘文件与数据库记录。

### 4. 文章/内容管理 (Content Management)
*   所有资源使用标准的 `RESTful` 路由（`index`, `store`, `show`, `update`, `destroy`）。
*   **注意**：所有返回的日期应统一为 ISO 8601 格式，由前端 `dateUtils.js` 统一处理显示。

---

## 三、 前端迁移路径 (Frontend Migration)

### 1. 目录结构调整
按照 `OPTIMIZATION.md` 建议，新增以下结构：
*   `resources/js/api/`: 存放所有 axios 请求模块。
*   `resources/js/composables/`: 存放封装了 loading 和数据状态的 Hook（如 `useMedia.js`, `useJournals.js`, `useAdvertisements.js`）。
*   `resources/js/data/`: 保留配置类数据（枚举定义、菜单配置等）。

### 2. 数据文件结构
当前 `resources/js/data/` 目录包含以下数据文件：

**业务数据文件**：
- `posts.js` - 文章数据
- `comments.js` - 评论数据
- `users.js` - 用户数据
- `projects.js` - 项目数据
- `resources.js` - 资源数据
- `videos.js` - 视频数据
- `advertisements.js` - 广告数据
- `ad_positions.js` - 广告位数据
- `journals.js` - 日志数据
- `media.js` - 媒体数据
- `logs.js` - 操作日志
- `visits.js` - 访问统计
- `user_levels.js` - 用户等级
- `skills.js` - 技能管理
- `manifestos.js` - 宣言管理
- `links.js` - 友情链接

**配置类数据文件**：
- `categories.js` - 分类枚举
- `tags.js` - 标签枚举
- `roles.js` - 角色定义
- `permissions.js` - 权限定义
- `role_permissions.js` - 角色权限关联
- `menu.js` - 菜单配置
- `settings.js` - 系统设置
- `email_templates.js` - 邮件模板
- `mail_config.js` - 邮件配置
- `backup.js` - 备份记录

### 3. 对接示例 (针对本项目的 Hybrid 模式)
```javascript
// resources/js/composables/useJournals.js
export function useJournals() {
  const journals = ref([]);
  const isLoading = ref(false);
  const total = ref(0);

  const fetchJournals = async (params = {}) => {
    isLoading.value = true;
    try {
      const res = await axios.get('/api/v1/journals', { params });
      journals.value = res.data.data;
      total.value = res.data.total;
    } catch (e) {
      console.error(e);
    } finally {
      isLoading.value = false;
    }
  };

  return { journals, isLoading, total, fetchJournals };
}
```

---

## 四、 对接完成清单与优先级 (Checklist & Priority)

| 优先级 | 任务描述 | 涉及模块 | 状态 |
| :--- | :--- | :--- | :--- |
| **P0** | **广告位管理全栈实现** | AdPositionController, ad_positions Table | ✅ |
| **P0** | **日志管理全栈实现** | JournalController, journals Table | ✅ |
| **P0** | **媒体库全栈实现** | MediaController, Media Table | ✅ |
| **P0** | **管理后台 Web 认证迁移** | Auth Middleware, Session Logic | ✅ |
| **P0** | **文章管理 CRUD 对接** | Post Management | ✅ |
| **P0** | **图片验证码登录校验** | Login Page Captcha | ✅ |
| **P1** | **广告管理 CRUD 对接** | Advertisement Management | ✅ |
| **P1** | **评论审核系统对接** | Comment System | ✅ |
| **P1** | **系统设置持久化** | Settings Management | ✅ |
| **P1** | **前台菜单动态化同步** | Front Menu Management | ✅ |
| **P1** | **社交登录配置管理** | SocialLogin (Google/GitHub) | ✅ |
| **P2** | **仪表盘统计数据真实化** | Analytics Dashboard | ✅ |
| **P2** | **全局 SEO 元数据同步** | Meta Management | ✅ |
| **P2** | **RSS Feed 配置** | SEO Settings | ✅ |
| **P2** | **用户等级系统对接** | User Levels | ✅ |
| **P2** | **访问统计系统对接** | Visits Analytics | ✅ |
| **P2** | **极光流体动态背景** | Admin Layout UI | ✅ |
| **P2** | **毛玻璃 UI 样式** | Admin Pages | ✅ |

---

## 五、 后续注意事项
1.  **Storage Link**: 对接媒体库前，必须执行 `php artisan storage:link`。✅ 已完成
2.  **CSRF**: 所有非 GET 请求必须携带 CSRF Token。✅ Inertia.js 自动处理
3.  **App 兼容性**: 核心业务逻辑应封装在 Service 层，以便将来 `routes/api.php` 也能直接复用。✅ 已采用
4.  **数据迁移**: 使用 Laravel Migration 工具将现有 data 文件数据迁移到数据库。✅ 已完成
5.  **国际化**: 确保所有用户可见文本使用 i18n key，支持多语言切换。✅ vue-i18n 已配置

---
*最后更新：2026-06-06*
