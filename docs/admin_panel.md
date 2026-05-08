# Admin Panel Specification (管理后台文档)

## 1. Architecture & Tech Stack

The Admin Panel is seamlessly integrated into the existing Laravel + Vue 3 Hybrid architecture. To maintain security and code separation, the admin area will be isolated under a specific route group and middleware.

- **Frontend Interface:** Vue 3 + Tailwind CSS (Consider using an Admin UI kit like Element Plus or a custom Tailwind-based UI for rapid development of data tables and forms).
- **Communication:** Hybrid Mode (Data passed from Laravel Web Controllers to Blade templates, which host Vue components).
- **Security & Authorization:**
  - Routes prefixed with `/admin`.
  - Protected by Laravel's `auth` middleware.
  - Strict FormRequest validation for all admin mutation actions.

## 2. Core Modules

The admin backend will feature the following core modules:

### 2.1 Dashboard & Metrics (仪表盘)

- Real-time and historical analytics.
- Key metrics: Total users, total active posts, recent comments awaiting approval, and system load.

### 2.2 Content Management (内容管理)

- **Posts (博客/文章):** Full CRUD, Markdown editor integration, tag assignment, and cover image upload.
- **Projects & Resources (项目与资源):** Management of portfolio items, file uploads, link generation, and categorization.
- **Advertisements (广告管理):** Management of ad placements, banner uploads, active scheduling, and click/view tracking.
- **Videos (视频):** Managing video links, platforms (YouTube/Bilibili), and caching thumbnail data.
- **Comments (评论审核):** Moderation dashboard to approve, reject, or delete user comments.
- **Categories & Tags (分类与标签):** Hierarchical category management and tag management.

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
                ├── VideoController.php
                ├── ProjectController.php
                ├── ResourceController.php
                ├── CategoryController.php
                ├── TagController.php
                └── SystemSettingController.php
resources/
 └── views/
      └── admin/                   <- Admin Blade views
           ├── dashboard.blade.php
           ├── posts/
           │    ├── index.blade.php
           │    ├── create.blade.php
           │    └── edit.blade.php
           └── ...
```

## 4. Admin Routes (管理后台路由)

All admin routes are prefixed with `/admin` and protected by `auth` middleware.

### 4.1 Route List

| Module | Base Route | Methods | Description |
|--------|------------|---------|-------------|
| Dashboard | `/admin` | GET | Dashboard overview |
| Posts | `/admin/posts` | CRUD | Blog post management |
| Videos | `/admin/videos` | CRUD | Video library management |
| Projects | `/admin/projects` | CRUD | Portfolio project management |
| Resources | `/admin/resources` | CRUD | Downloadable resources management |
| Categories | `/admin/categories` | CRUD | Content categories management |
| Tags | `/admin/tags` | CRUD | Content tags management |

### 4.2 Detailed Route Table

#### Posts Management (`/admin/posts`)

| Method | Route | Action | Description |
|--------|-------|--------|-------------|
| GET | `/admin/posts` | `index` | List all posts |
| GET | `/admin/posts/create` | `create` | Show create form |
| POST | `/admin/posts` | `store` | Create new post |
| GET | `/admin/posts/{post}` | `show` | View post detail |
| GET | `/admin/posts/{post}/edit` | `edit` | Show edit form |
| PUT/PATCH | `/admin/posts/{post}` | `update` | Update post |
| DELETE | `/admin/posts/{post}` | `destroy` | Delete post |

#### Videos Management (`/admin/videos`)

| Method | Route | Action | Description |
|--------|-------|--------|-------------|
| GET | `/admin/videos` | `index` | List all videos |
| GET | `/admin/videos/create` | `create` | Show create form |
| POST | `/admin/videos` | `store` | Create new video |
| GET | `/admin/videos/{video}` | `show` | View video detail |
| GET | `/admin/videos/{video}/edit` | `edit` | Show edit form |
| PUT/PATCH | `/admin/videos/{video}` | `update` | Update video |
| DELETE | `/admin/videos/{video}` | `destroy` | Delete video |

#### Projects Management (`/admin/projects`)

| Method | Route | Action | Description |
|--------|-------|--------|-------------|
| GET | `/admin/projects` | `index` | List all projects |
| GET | `/admin/projects/create` | `create` | Show create form |
| POST | `/admin/projects` | `store` | Create new project |
| GET | `/admin/projects/{project}` | `show` | View project detail |
| GET | `/admin/projects/{project}/edit` | `edit` | Show edit form |
| PUT/PATCH | `/admin/projects/{project}` | `update` | Update project |
| DELETE | `/admin/projects/{project}` | `destroy` | Delete project |

#### Resources Management (`/admin/resources`)

| Method | Route | Action | Description |
|--------|-------|--------|-------------|
| GET | `/admin/resources` | `index` | List all resources |
| GET | `/admin/resources/create` | `create` | Show create form |
| POST | `/admin/resources` | `store` | Create new resource |
| GET | `/admin/resources/{resource}` | `show` | View resource detail |
| GET | `/admin/resources/{resource}/edit` | `edit` | Show edit form |
| PUT/PATCH | `/admin/resources/{resource}` | `update` | Update resource |
| DELETE | `/admin/resources/{resource}` | `destroy` | Delete resource |

#### Categories Management (`/admin/categories`)

| Method | Route | Action | Description |
|--------|-------|--------|-------------|
| GET | `/admin/categories` | `index` | List all categories |
| GET | `/admin/categories/create` | `create` | Show create form |
| POST | `/admin/categories` | `store` | Create new category |
| GET | `/admin/categories/{category}` | `show` | View category detail |
| GET | `/admin/categories/{category}/edit` | `edit` | Show edit form |
| PUT/PATCH | `/admin/categories/{category}` | `update` | Update category |
| DELETE | `/admin/categories/{category}` | `destroy` | Delete category |

#### Tags Management (`/admin/tags`)

| Method | Route | Action | Description |
|--------|-------|--------|-------------|
| GET | `/admin/tags` | `index` | List all tags |
| GET | `/admin/tags/create` | `create` | Show create form |
| POST | `/admin/tags` | `store` | Create new tag |
| GET | `/admin/tags/{tag}` | `show` | View tag detail |
| GET | `/admin/tags/{tag}/edit` | `edit` | Show edit form |
| PUT/PATCH | `/admin/tags/{tag}` | `update` | Update tag |
| DELETE | `/admin/tags/{tag}` | `destroy` | Delete tag |

## 5. Controller Structure

Admin controllers follow the standard Laravel resource controller pattern:

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Display a listing of the resource
    public function index() {}

    // Show the form for creating a new resource
    public function create() {}

    // Store a newly created resource in storage
    public function store(Request $request) {}

    // Display the specified resource
    public function show(Post $post) {}

    // Show the form for editing the specified resource
    public function edit(Post $post) {}

    // Update the specified resource in storage
    public function update(Request $request, Post $post) {}

    // Remove the specified resource from storage
    public function destroy(Post $post) {}
}
```

## 6. Security Considerations

- **Authentication:** All admin routes are protected by Laravel's `auth` middleware.
- **Authorization:** Implement policy-based authorization for specific actions.
- **Input Validation:** Use FormRequest classes for all create/update operations.
- **CSRF Protection:** Laravel's built-in CSRF protection applies to all POST/PUT/DELETE requests.
- **File Uploads:** Validate file types and sizes, store files securely.
- **Rate Limiting:** Implement rate limiting to prevent brute force attacks.

## 7. Future Enhancements

- **Activity Logging:** Track all admin actions in the `audit_logs` table.
- **Role-Based Access Control:** Integrate Spatie Laravel Permission package.
- **Bulk Actions:** Support for bulk delete, bulk status updates.
- **Advanced Filtering:** Add search, filter, and sorting capabilities to index pages.
- **Export Functionality:** Export data to CSV/Excel formats.
