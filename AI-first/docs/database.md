# Database Design

This document outlines the database table structure for the Laravel backend, utilizing standard Laravel conventions (plural table names, snake_case columns). This structure supports content management, tagging, multi-level categories, nesting comments, user interactions (likes/bookmarks), analytics, and Role-Based Access Control (RBAC).

## Tables

### `users`

Standard Laravel user table for authentication and administration.

- `id` (bigint, pk)
- `name` (varchar)
- `email` (varchar, unique)
- `email_verified_at` (timestamp, nullable)
- `password` (varchar)
- `avatar` (varchar, nullable)
- `bio` (text, nullable)
- `github` (varchar, nullable)
- `twitter` (varchar, nullable)
- `linkedin` (varchar, nullable)
- `role` (enum: 'user', 'admin') - Default: 'user' - **Deprecated: Use Spatie roles**
- `status` (enum: 'active', 'inactive', 'suspended') - Default: 'active'
- `points` (unsignedBigInteger, default: 0) - User points for level system
- `last_login_at` (timestamp, nullable)
- `remember_token` (varchar)
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Note:** User roles are managed via Spatie's `model_has_roles` pivot table, not a direct `role_id` foreign key.

### `categories`

Categories for posts and resources.

- `id` (bigint, pk)
- `parent_id` (bigint, fk -> categories.id, nullable)
- `name` (varchar, unique)
- `slug` (varchar, unique)
- `description` (varchar, nullable)
- `status` (enum: 'active', 'inactive') - Default: 'active'
- `sort_order` (int, default: 0)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `posts` (Blog Posts)

Stores the blog articles and their metadata.

- `id` (bigint, pk)
- `title` (varchar)
- `slug` (varchar, unique)
- `excerpt` (text)
- `content` (longtext) - Stores Markdown or HTML
- `cover_image` (varchar, nullable)
- `color` (varchar, default: 'black') - Theme color
- `status` (enum: 'draft', 'published', 'archived') - Default: 'draft'
- `views_count` (bigint, default: 0)
- `likes_count` (bigint, default: 0)
- `meta_title` (varchar, nullable)
- `meta_description` (varchar(500), nullable)
- `meta_keywords` (varchar, nullable)
- `author_id` (bigint, fk -> users.id)
- `category_id` (bigint, fk -> categories.id, nullable)
- `published_at` (timestamp, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `tags`

Available tags for grouping items.

- `id` (bigint, pk)
- `name` (varchar, unique)
- `slug` (varchar, unique)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `taggables` (Polymorphic Pivot Table)

Polymorphic many-to-many relationship tracking tags for Posts, Projects, and Resources.

- `tag_id` (bigint, fk -> tags.id)
- `taggable_id` (bigint)
- `taggable_type` (varchar)
- Primary Key is composite: (`tag_id`, `taggable_id`, `taggable_type`)

### `comments` (Post Comments)

User submitted comments on posts.

- `id` (bigint, pk)
- `post_id` (bigint, fk -> posts.id)
- `parent_id` (bigint, fk -> comments.id, nullable)
- `user_id` (bigint, fk -> users.id, nullable)
- `name` (varchar, nullable)
- `email` (varchar, nullable)
- `body` (text)
- `is_approved` (boolean, default: true)
- `ip_address` (varchar(45), nullable)
- `user_agent` (varchar, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `projects`

Portfolio projects.

- `id` (bigint, pk)
- `title` (varchar)
- `description` (text)
- `long_description` (longtext, nullable)
- `client` (varchar, nullable)
- `role` (varchar, nullable)
- `year` (int)
- `image` (varchar, nullable)
- `url` (varchar, nullable)
- `github_url` (varchar, nullable)
- `technologies` (json, nullable)
- `status` (enum: 'planning', 'in-progress', 'completed') - Default: 'completed'
- `sort_order` (int, default: 0)
- `views_count` (bigint, default: 0)
- `likes_count` (bigint, default: 0)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `resources`

Downloadable assets.

- `id` (bigint, pk)
- `category_id` (bigint, fk -> categories.id, nullable)
- `title` (varchar)
- `description` (text)
- `format` (varchar) - e.g., 'PDF', 'ZIP'
- `file_size` (varchar) - e.g., '2.4 MB'
- `image` (varchar, nullable)
- `direct_link` (varchar, nullable)
- `drives` (json, nullable) - JSON array for google drive/baidu links
- `downloads_count` (bigint, default: 0)
- `likes_count` (bigint, default: 0)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `videos`

Video links/embeds.

- `id` (bigint, pk)
- `title` (varchar)
- `description` (text)
- `video_id` (varchar) - ID from the respective platform
- `platform` (enum: 'youtube', 'bilibili')
- `thumbnail` (varchar, nullable)
- `duration` (varchar, nullable)
- `views_count` (bigint, default: 0)
- `likes_count` (bigint, default: 0)
- `published_at` (timestamp, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `interactions` (Polymorphic Likes/Bookmarks)

Tracks user likes and bookmarks across any content type.

- `id` (bigint, pk)
- `user_id` (bigint, fk -> users.id)
- `interactable_type` (varchar)
- `interactable_id` (bigint)
- `type` (enum: 'like', 'bookmark')
- `created_at` (timestamp)
- `updated_at` (timestamp)
- Unique Key: (`user_id`, `interactable_id`, `interactable_type`, `type`)

### `ad_positions` (Ad Positions)

Configuration for ad placement positions.

- `id` (bigint, pk)
- `name` (varchar, unique) - Position identifier (e.g., 'home_top', 'blog_sidebar')
- `label_key` (varchar) - i18n translation key
- `description` (varchar, nullable)
- `default_width` (int) - Default width in pixels
- `default_height` (int) - Default height in pixels
- `is_active` (boolean, default: true)
- `sort_order` (int, default: 0)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `advertisements` (Ad Management)

Manage ad placements across the application.

- `id` (bigint, pk)
- `title` (varchar) - Internal name for the ad
- `image_url` (varchar) - Banner image
- `link_url` (varchar) - Target URL
- `position_id` (bigint, fk -> ad_positions.id, nullable) - Links to ad position
- `position` (varchar) - **Deprecated: Use `position_id` instead** - e.g., 'home_top', 'blog_sidebar', 'post_bottom'
- `is_active` (boolean, default: true)
- `clicks_count` (bigint, default: 0)
- `views_count` (bigint, default: 0)
- `start_date` (timestamp, nullable)
- `end_date` (timestamp, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `journals` (Dev Logs / Microblogs)

Short, frequent updates or development logs.

- `id` (bigint, pk)
- `user_id` (bigint, fk -> users.id)
- `title` (varchar, nullable) - Log title
- `content` (text) - Markdown content
- `mood` (varchar, nullable)
- `weather` (varchar, nullable)
- `date` (date, nullable) - Log date
- `is_public` (boolean, default: true)
- `likes_count` (bigint, default: 0)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `subscribers`

Newsletter subscriptions.

- `id` (bigint, pk)
- `email` (varchar, unique)
- `name` (varchar, nullable) - Subscriber name
- `source` (varchar, nullable) - Subscription source (website/newsletter/social)
- `is_active` (boolean, default: true)
- `subscribed_at` (timestamp, nullable) - Subscription time
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `media` (Media Library)

Media file management.

- `id` (bigint, pk)
- `user_id` (bigint, fk -> users.id) - Uploader user ID
- `filename` (varchar) - Stored filename
- `original_name` (varchar) - Original filename
- `mime_type` (varchar) - MIME type
- `file_size` (unsignedBigInteger) - File size in bytes
- `url` (varchar) - File URL
- `thumbnail_url` (varchar, nullable) - Thumbnail URL
- `alt_text` (varchar, nullable) - Alternative text
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `menus` (Menu Configuration)

Backend menu configuration.

- `id` (bigint, pk)
- `type` (enum: 'front', 'admin') - Menu type
- `parent_id` (bigint, fk -> menus.id, nullable) - Parent menu ID
- `label_key` (varchar) - i18n translation key
- `icon_name` (varchar, nullable) - Icon name
- `path` (varchar, nullable) - Route path
- `sort_order` (int, default: 0)
- `is_active` (boolean, default: true)
- `component_name` (varchar, nullable) - Associated component name
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `visits` (Visit Tracking)

Tracks page visits and analytics.

- `id` (bigint, pk)
- `visitable_id` (unsignedBigInteger) - Visited object ID (polymorphic)
- `visitable_type` (varchar) - Visited object type
- `ip_address` (varchar(45)) - Visitor IP address
- `user_agent` (varchar, nullable) - Browser user agent
- `referrer` (varchar, nullable) - Referrer URL
- `created_at` (timestamp)
- Index: (`visitable_id`, `visitable_type`)

### `settings` (System Settings)

Global system configuration key-value storage.

- `id` (bigint, pk)
- `key` (varchar, unique) - Setting key
- `value` (json) - Setting value (JSON format)
- `type` (enum: 'string', 'number', 'boolean', 'array', 'object') - Value type
- `group` (varchar) - Setting group
- `label` (varchar) - Display name
- `description` (varchar, nullable) - Setting description
- `is_public` (boolean, default: false) - Whether publicly accessible
- `sort_order` (int, default: 0)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `admin_logs` (Admin Operation Logs)

Tracks admin operations and system changes.

- `id` (bigint, pk)
- `user_id` (bigint, fk -> users.id, nullable) - Operating user ID
- `action` (varchar) - Action type
- `description` (text) - Operation description
- `ip_address` (varchar(45)) - IP address
- `user_agent` (varchar, nullable) - Browser information
- `created_at` (timestamp)

### `user_levels` (User Levels)

User level system for gamification.

- `id` (bigint, pk)
- `name` (varchar) - Level name
- `min_points` (int) - Minimum points required
- `max_points` (int, nullable) - Maximum points
- `color` (varchar) - Level color
- `icon` (varchar, nullable) - Level icon
- `description` (varchar, nullable) - Level description
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `footer_links` (Footer Configuration)

Footer social links, navigation links, and brand info.

- `id` (bigint, pk)
- `type` (enum: 'social_link', 'nav_link', 'brand_info') - Link type
- `label` (varchar) - Label
- `url` (varchar, nullable) - Link URL
- `icon` (varchar, nullable) - Icon name
- `sort_order` (int, default: 0)
- `is_active` (boolean, default: true)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `page_seo` (Page SEO)

SEO configuration for individual pages.

- `id` (bigint, pk)
- `page_key` (varchar, unique) - Page identifier
- `title` (varchar) - SEO title
- `description` (varchar(500)) - SEO description
- `keywords` (varchar, nullable) - SEO keywords
- `og_image` (varchar, nullable) - OpenGraph image
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `seo` (Global SEO Configuration)

Global SEO default configuration.

- `id` (bigint, pk)
- `key` (varchar, unique) - Configuration key
- `value` (json) - Configuration value
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `site` (Site Configuration)

Basic site information and brand configuration.

- `id` (bigint, pk)
- `key` (varchar, unique) - Configuration key
- `value` (json) - Configuration value
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `themes` (Theme Configuration)

Theme customization and switching.

- `id` (bigint, pk)
- `name` (varchar, unique) - Theme name
- `label` (varchar) - Theme label
- `preview_image` (varchar, nullable) - Preview image
- `is_active` (boolean, default: false) - Whether active
- `config` (json, nullable) - Theme configuration
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `author_profiles` (Author Profiles)

Author personal information and social links.

- `id` (bigint, pk)
- `user_id` (bigint, fk -> users.id) - Associated user ID
- `bio` (text) - Personal biography
- `avatar` (varchar, nullable) - Avatar URL
- `github` (varchar, nullable) - GitHub link
- `twitter` (varchar, nullable) - Twitter link
- `linkedin` (varchar, nullable) - LinkedIn link
- `created_at` (timestamp)
- `updated_at` (timestamp)

## Role-Based Access Control (RBAC)

Using **Spatie/laravel-permission** package with extended custom fields.

### `roles`

User roles for access control (managed by Spatie).

- `id` (bigint, pk)
- `name` (varchar, unique) - Role name (e.g., 'admin', 'editor', 'author')
- `guard_name` (varchar, nullable) - Guard name (default: 'web')
- `color` (varchar, nullable) - Role color for UI display
- `label` (varchar, nullable) - Role label for i18n
- `description` (text, nullable) - Role description
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `permissions`

System permission definitions (managed by Spatie).

- `id` (bigint, pk)
- `name` (varchar, unique) - Permission name (e.g., 'create posts', 'edit users')
- `guard_name` (varchar, nullable) - Guard name (default: 'web')
- `program_id` (varchar, nullable) - Associated program/project ID
- `description` (text, nullable) - Permission description
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `role_has_permissions` (Pivot - Managed by Spatie)

Many-to-many relationship between roles and permissions.

- `permission_id` (bigint, fk -> permissions.id)
- `role_id` (bigint, fk -> roles.id)
- Primary Key is composite: (`permission_id`, `role_id`)

### `model_has_roles` (Pivot - Managed by Spatie)

Links users to roles (supports multiple roles per user).

- `role_id` (bigint, fk -> roles.id)
- `model_type` (varchar) - Model class name (e.g., 'App\Models\User')
- `model_id` (bigint) - User ID
- Primary Key is composite: (`role_id`, `model_id`, `model_type`)

### `model_has_permissions` (Pivot - Managed by Spatie)

Direct permissions assigned to users (bypassing roles).

- `permission_id` (bigint, fk -> permissions.id)
- `model_type` (varchar) - Model class name
- `model_id` (bigint) - User ID
- Primary Key is composite: (`permission_id`, `model_id`, `model_type`)

## Laravel Framework Built-in Tables

The following tables are created automatically by Laravel framework or packages through artisan commands.

| Table | Artisan Command | Purpose |
|-------|-----------------|---------|
| `users` | `make:auth` | User authentication base table |
| `password_reset_tokens` | `make:auth` | Password reset token storage |
| `sessions` | `session:table` | Session storage (database driver) |
| `cache` | `cache:table` | Cache queue storage |
| `cache_locks` | `cache:table` | Cache lock implementation |
| `jobs` | `queue:table` | Delayed task queue |
| `job_batches` | `queue:batches-table` | Queue batch tracking |
| `failed_jobs` | `queue:failed-table` | Failed task records |
| `notifications` | `notifications:table` | In-app notification storage |
| `personal_access_tokens` | `sanctum:install` | API authentication tokens (Sanctum) |
| `roles` | `spatie:permission` | RBAC roles table |
| `permissions` | `spatie:permission` | RBAC permissions table |
| `role_has_permissions` | `spatie:permission` | Role-permission pivot |
| `model_has_roles` | `spatie:permission` | User-role pivot (polymorphic) |
| `model_has_permissions` | `spatie:permission` | User-permission pivot (polymorphic) |

## Deleted/Renamed Files

The following data files have been removed and replaced:

| Old File | Current Status | Note |
|----------|----------------|------|
| `author.js` | Replaced by `author_profiles.js` | Author profile data |
| `home.js` | Removed | Home page config removed |
| `links.js` | Removed | Friendship links removed |
| `manifestos.js` | Removed | Manifesto/philosophy data removed |
| `skills.js` | Removed | Skills/abilities data removed |
