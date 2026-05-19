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
- `role` (enum: 'user', 'admin') - Default: 'user'
- `status` (enum: 'active', 'inactive', 'suspended') - Default: 'active'
- `last_login_at` (timestamp, nullable)
- `remember_token` (varchar)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `categories`

Categories for posts and resources.

- `id` (bigint, pk)
- `parent_id` (bigint, fk -> categories.id, nullable)
- `name` (varchar, unique)
- `slug` (varchar, unique)
- `description` (varchar, nullable)
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

### `advertisements` (Ad Management)

Manage ad placements across the application.

- `id` (bigint, pk)
- `title` (varchar) - Internal name for the ad
- `image_url` (varchar) - Banner image
- `link_url` (varchar) - Target URL
- `position` (varchar) - e.g., 'home_top', 'blog_sidebar', 'post_bottom'
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
- `content` (text) - Markdown content
- `mood` (varchar, nullable)
- `weather` (varchar, nullable)
- `is_public` (boolean, default: true)
- `likes_count` (bigint, default: 0)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `subscribers`

Newsletter subscriptions.

- `id` (bigint, pk)
- `email` (varchar, unique)
- `is_active` (boolean, default: true)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `system_settings`

Global system configuration key-value storage.

- `id` (bigint, pk)
- `key` (varchar, unique)
- `value` (text, nullable)
- `description` (varchar, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### `audit_logs`

Tracks important system changes and admin actions.

- `id` (bigint, pk)
- `user_id` (bigint, fk -> users.id, nullable)
- `action_type` (varchar)
- `model_type` (varchar, nullable)
- `model_id` (bigint, nullable)
- `old_values` (json, nullable)
- `new_values` (json, nullable)
- `ip_address` (varchar, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### Roles and Permissions Tables (`Spatie/laravel-permission`)

Manages RBAC structure automatically maintained by the Spatie package.

- `roles`
- `permissions`
- `model_has_permissions`
- `model_has_roles`
- `role_has_permissions`
