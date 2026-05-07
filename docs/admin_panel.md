# Admin Panel Specification (管理后台文档)

## 1. Architecture & Tech Stack

The Admin Panel is seamlessly integrated into the existing Laravel + Vue 3 Hybrid architecture. To maintain security and code separation, the admin area will be isolated under a specific route group and middleware.

- **Frontend Interface:** Vue 3 + Tailwind CSS (Consider using an Admin UI kit like Element Plus or a custom Tailwind-based UI for rapid development of data tables and forms).
- **Communication:** Hybrid Mode (Data passed from Laravel Web Controllers to Blade templates, which host Vue components).
- **Security & Authorization:**
  - Routes prefixed with `/admin`.
  - Protected by Laravel's `auth` and Spatie's `role:admin|super-admin` middleware.
  - Strict FormRequest validation for all admin mutation actions.

## 2. Core Modules

The admin backend will feature the following core modules:

### 2.1 Dashboard & Metrics (仪表盘)

- Real-time and historical analytics.
- Key metrics: Total users, total active posts, recent comments awaiting approval, and system load.

### 2.2 Content Management (内容管理)

- **Posts (博客/文章):** Full CRUD, WYSIWYG editor integration (e.g., TipTap or Quill), tag assignment, and cover image upload.
- **Projects & Resources (项目与资源):** Management of portfolio items, file uploads to S3/OSS, link generation, and categorization.
- **Advertisements (广告管理):** Management of ad placements, banner uploads, active scheduling, and click/view tracking.
- **Videos (视频):** Managing video links, platforms (YouTube/Bilibili), and caching thumbnail data.
- **Comments (评论审核):** Moderation dashboard to approve, reject, or delete user comments.

### 2.3 User & RBAC Management (用户与权限管理)

- List all users, reset passwords, or suspend accounts.
- **Role-Based Access Control (RBAC):** UI for Spatie Laravel Permission to dynamically assign roles (e.g., `editor`, `admin`, `super-admin`) and individual permissions (e.g., `publish articles`, `delete users`).

### 2.4 System Settings (系统设置)

- A dynamic key-value store for global configurations that do not require code deployment.
- Examples: Maintenance mode toggle, site name, SEO metadata, contact emails, third-party API keys (managed securely).

### 2.5 Audit Logs / Operation Logs (操作日志)

- Irreversible ledger of critical admin actions.
- Tracks `user_id`, `action_type`, `model_type`, `model_id`, `old_values`, `new_values`, and `ip_address`.
- Crucial for security auditing in multi-admin environments.

## 3. Directory Structure

```text
app/
 └── Http/
      └── Controllers/
           └── Admin/              <- Admin isolated controllers
                ├── DashboardController.php
                ├── PostController.php
                └── SystemSettingController.php
resources/
 └── js/
      ├── Pages/
      │    ├── Admin/              <- Admin isolated React views
      │    │    ├── Dashboard.tsx
      │    │    └── Posts/
      │    └── Web/                <- Main user-facing React views
      └── Components/
           └── AdminLayer/         <- Admin UI components (Sidebars, DataTables)
```
