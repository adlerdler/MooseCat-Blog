# 前后端对接与同步指南 (Full-Stack Integration Guide)

本文档旨在指导 `laravel-vue-app` 从静态 Mock 数据向 Laravel 后端 API 驱动架构的平滑过渡。

---

## 一、 数据库与模型同步 (Database & Models)

### 1. 待创建的表 (Missing Tables)

| 模块 | 建议表名 | 核心字段 | 状态 |
| :--- | :--- | :--- | :--- |
| **媒体库** | `media` | `id`, `filename`, `original_name`, `path`, `mime_type`, `size`, `disk` | ⏳ 待创建 |
| **系统设置** | `settings` | `key`, `value`, `group`, `type` | ⏳ 待创建 |
| **菜单管理** | `menus` | `id`, `title`, `url`, `icon`, `order`, `parent_id`, `target` | ⏳ 待创建 |

### 2. 现有表字段补全/优化建议
*   **Users 表**: 增加 `avatar` (头像路径), `last_login_at` (最后登录时间), `status` (激活/禁用)。
*   **Posts 表**: 确保 `status` 字段支持 `['draft', 'published', 'archived']`。
*   **Comments 表**: 增加 `is_approved` (审核状态), `parent_id` (支持楼中楼回复)。

---

## 二、 API 契约设计 (API Contract Design)

### 1. 媒体库 (Media Library)
*   `GET /admin/api/media`: 返回文件列表。
*   `POST /admin/api/media/upload`: 处理二进制上传，存入 `storage/app/public/media`。
*   `DELETE /admin/api/media/{id}`: 同步删除磁盘文件与数据库记录。

### 2. 文章/内容管理 (Content Management)
*   所有资源使用标准的 `RESTful` 路由（`index`, `store`, `show`, `update`, `destroy`）。
*   **注意**：所有返回的日期应统一为 ISO 8601 格式，由前端 `dateUtils.js` 统一处理显示。

---

## 三、 前端迁移路径 (Frontend Migration)

### 1. 目录结构调整
按照 `OPTIMIZATION.md` 建议，新增以下结构：
*   `resources/js/api/`: 存放所有 axios 请求模块。
*   `resources/js/composables/`: 存放封装了 loading 和数据状态的 Hook（如 `useMedia.js`）。

### 2. 对接示例 (针对本项目的 Hybrid 模式)
```javascript
// resources/js/composables/useMedia.js
export function useMedia() {
  const files = ref([]);
  const isLoading = ref(false);

  const fetchFiles = async () => {
    isLoading.value = true;
    try {
      const res = await axios.get('/admin/media/list'); // 走 Web 路由以复用 Session 认证
      files.value = res.data;
    } catch (e) {
      console.error(e);
    } finally {
      isLoading.value = false;
    }
  };

  return { files, isLoading, fetchFiles };
}
```

---

## 四、 对接完成清单与优先级 (Checklist & Priority)

| 优先级 | 任务描述 | 涉及模块 | 状态 |
| :--- | :--- | :--- | :--- |
| **P0** | **媒体库全栈实现** | MediaController, Media Table | ⏳ |
| **P0** | **管理后台 Web 认证迁移** | Auth Middleware, Session Logic | ⏳ |
| **P0** | **文章管理 CRUD 对接** | Post Management | ⏳ |
| **P1** | **评论审核系统对接** | Comment System | ⏳ |
| **P1** | **系统设置持久化** | Settings Management | ⏳ |
| **P1** | **前台菜单动态化同步** | Front Menu Management | ⏳ |
| **P2** | **仪表盘统计数据真实化** | Analytics Dashboard | ⏳ |
| **P2** | **全局 SEO 元数据同步** | Meta Management | ⏳ |

---

## 五、 后续注意事项
1.  **Storage Link**: 对接媒体库前，必须执行 `php artisan storage:link`。
2.  **CSRF**: 所有非 GET 请求必须携带 CSRF Token。
3.  **App 兼容性**: 核心业务逻辑应封装在 Service 层，以便将来 `routes/api.php` 也能直接复用。

---
*最后更新：2026-05-16*
