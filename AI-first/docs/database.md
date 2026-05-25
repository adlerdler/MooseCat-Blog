# Database Design

> **Last Updated**: 2026-05-24
>
> This document outlines the database table structure for the Laravel backend, utilizing standard Laravel conventions (plural table names, snake_case columns). This structure supports content management, tagging, multi-level categories, nesting comments, user interactions (likes/bookmarks), analytics, gamification (user levels/points), internationalization (i18n), and Role-Based Access Control (RBAC) with Spatie/laravel-permission.
>
> **Total Tables**: 30 core tables + 5 Spatie RBAC pivot tables = **35 tables**

## Tables

### `users`

Standard Laravel user table for authentication and administration.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique record identifier |
| `name` | varchar(255) | not null | User display name for authentication and identification |
| `email` | varchar(255) | unique, not null | User email address used for login and notifications |
| `email_verified_at` | timestamp | nullable | Timestamp when email was verified (null if unverified) |
| `password` | varchar(255) | not null | Bcrypt hashed password for authentication |
| `avatar` | varchar(255) | nullable | URL or path to user's profile avatar image |
| `bio` | text | nullable | User biography or short description |
| `github` | varchar(255) | nullable | User's GitHub profile URL or username |
| `twitter` | varchar(255) | nullable | User's Twitter/X profile URL or username |
| `linkedin` | varchar(255) | nullable | User's LinkedIn profile URL |
| `status` | enum | 'active','inactive','suspended', default:'active' | Account status for enabling/disabling access |
| `role_id` | bigint | fk->roles.id, nullable | Foreign key to roles table for RBAC |
| `points` | unsignedBigInteger | default:0 | User points for gamification and level system |
| `email_notifications` | boolean | default:true | Enable/disable email notifications |
| `comment_approval_alert` | boolean | default:true | Alert when comment needs approval |
| `new_user_alert` | boolean | default:true | Alert when new user registers |
| `weekly_report` | boolean | default:false | Enable weekly report emails |
| `digest_email` | boolean | default:false | Enable digest emails |
| `digest_frequency` | enum | 'daily','weekly','monthly', default:'weekly' | Digest email frequency |
| `last_login_at` | timestamp | nullable | Timestamp of user's last successful login |
| `remember_token` | varchar(100) | nullable | Token for "Remember Me" functionality |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `categories`

Categories for organizing posts and resources with hierarchical support.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique category identifier |
| `parent_id` | bigint | fk->categories.id, nullable | Reference to parent category for hierarchical nesting (null for root categories) |
| `name` | varchar(255) | unique, not null | Category display name shown in UI |
| `slug` | varchar(255) | unique, not null | URL-friendly identifier for routing and SEO |
| `description` | varchar(255) | nullable | Detailed description of the category purpose |
| `status` | enum | 'active','inactive', default:'active' | Category visibility status for content organization |
| `sort_order` | int | default:0 | Display order for sorting categories in UI |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `posts` (Blog Posts)

Stores blog articles with full metadata for content management.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique post identifier |
| `title` | varchar(255) | not null | Post title displayed in listings and SEO |
| `slug` | varchar(255) | unique, not null | URL-friendly identifier for permalink structure |
| `excerpt` | text | nullable | Short summary or teaser for listings and meta descriptions |
| `content` | longtext | not null | Full post content in Markdown or HTML format |
| `cover_image` | varchar(255) | nullable | URL or path to post featured image |
| `color` | varchar(50) | default:'black' | Theme color for post card styling |
| `status` | enum | 'draft','published','archived', default:'draft' | Publication status for workflow control |
| `views_count` | bigint | default:0 | Number of times post has been viewed |
| `likes_count` | bigint | default:0 | Number of likes received from interactions |
| `meta_title` | varchar(255) | nullable | Custom SEO title for search engines (overrides default) |
| `meta_description` | varchar(500) | nullable | Custom meta description for SEO |
| `meta_keywords` | varchar(255) | nullable | Comma-separated SEO keywords |
| `author_id` | bigint | fk->users.id, not null | Reference to post author (creator) |
| `category_id` | bigint | fk->categories.id, nullable | Reference to primary category |
| `published_at` | timestamp | nullable | Scheduled or actual publication datetime |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `tags`

Tags for flexible content grouping across different content types.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique tag identifier |
| `name` | varchar(255) | unique, not null | Tag display name shown in UI |
| `slug` | varchar(255) | unique, not null | URL-friendly identifier for routing |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `taggables` (Polymorphic Pivot Table)

Polymorphic many-to-many relationship tracking tags for Posts, Projects, and Resources.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `tag_id` | bigint | fk->tags.id, not null | Reference to associated tag |
| `taggable_id` | bigint | not null | ID of the tagged content record |
| `taggable_type` | varchar(255) | not null | Class name of the tagged model (e.g., 'App\Models\Post') |
| **Primary Key** | | (`tag_id`, `taggable_id`, `taggable_type`) | Composite primary key for uniqueness |

---

### `comments` (Post Comments)

User-submitted comments with threading support for discussions.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique comment identifier |
| `post_id` | bigint | fk->posts.id, not null | Reference to the post being commented on |
| `parent_id` | bigint | fk->comments.id, nullable | Reference to parent comment for threading (null for top-level) |
| `user_id` | bigint | fk->users.id, nullable | Reference to authenticated commenter (null for guest) |
| `name` | varchar(255) | nullable | Guest commenter display name |
| `email` | varchar(255) | nullable | Guest commenter email address |
| `body` | text | not null | Comment content text |
| `is_approved` | boolean | default:true | Moderation status for spam control |
| `ip_address` | varchar(45) | nullable | Commenter IP for moderation and analytics |
| `user_agent` | varchar(255) | nullable | Browser user agent string for moderation |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `projects`

Portfolio projects showcasing work experience and achievements.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique project identifier |
| `title` | varchar(255) | not null | Project title displayed in portfolio |
| `description` | text | not null | Brief project description for listings |
| `long_description` | longtext | nullable | Detailed project information and case study |
| `client` | varchar(255) | nullable | Client name or company |
| `role` | varchar(255) | nullable | User's role in the project |
| `year` | int | not null | Year project was completed |
| `image` | varchar(255) | nullable | URL or path to project screenshot/cover |
| `url` | varchar(255) | nullable | Live project URL |
| `github_url` | varchar(255) | nullable | GitHub repository URL |
| `technologies` | json | nullable | Array of technology names used |
| `status` | enum | 'planning','in-progress','completed', default:'completed' | Project development status |
| `sort_order` | int | default:0 | Display order in portfolio grid |
| `views_count` | bigint | default:0 | Number of project page views |
| `likes_count` | bigint | default:0 | Number of project likes |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `resources`

Downloadable assets and digital products with tracking.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique resource identifier |
| `category_id` | bigint | fk->categories.id, nullable | Reference to resource category |
| `title` | varchar(255) | not null | Resource title displayed in downloads |
| `description` | text | nullable | Resource description and usage instructions |
| `format` | varchar(50) | not null | File format (e.g., 'PDF', 'ZIP', 'PSD') |
| `file_size` | varchar(50) | not null | Human-readable file size (e.g., '2.4 MB') |
| `image` | varchar(255) | nullable | Preview thumbnail URL |
| `direct_link` | varchar(255) | nullable | Primary download URL |
| `drives` | json | nullable | Alternative links (Google Drive, Baidu Pan, etc.) |
| `downloads_count` | bigint | default:0 | Total download count |
| `likes_count` | bigint | default:0 | Number of resource likes |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `videos`

Video content management supporting multiple platforms.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique video identifier |
| `title` | varchar(255) | not null | Video title for display |
| `description` | text | nullable | Video description and show notes |
| `video_id` | varchar(255) | not null | Platform-specific video identifier (YouTube ID, Bilibili ID) |
| `platform` | enum | 'youtube','bilibili', not null | Video hosting platform |
| `thumbnail` | varchar(255) | nullable | Custom thumbnail image URL |
| `duration` | varchar(20) | nullable | Video duration (e.g., '12:34') |
| `views_count` | bigint | default:0 | Number of video views |
| `likes_count` | bigint | default:0 | Number of likes |
| `published_at` | timestamp | nullable | Video publication date |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `interactions` (Polymorphic Likes/Bookmarks)

Tracks user engagement activities across any content type.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique interaction identifier |
| `user_id` | bigint | fk->users.id, not null | User who performed the interaction |
| `interactable_type` | varchar(255) | not null | Class name of the interacted model |
| `interactable_id` | bigint | not null | ID of the interacted content record |
| `type` | enum | 'like','bookmark', not null | Type of interaction |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |
| **Unique Key** | | (`user_id`, `interactable_id`, `interactable_type`, `type`) | Prevents duplicate interactions |

---

### `ad_positions` (Ad Positions)

Configuration for ad placement positions with i18n support.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique position identifier |
| `name` | varchar(255) | unique, not null | Position code identifier (e.g., 'header', 'sidebar') |
| `label_key` | varchar(255) | not null | i18n translation key for UI display |
| `description` | text | nullable | Position description and usage notes |
| `default_width` | int | not null | Default ad container width in pixels |
| `default_height` | int | not null | Default ad container height in pixels |
| `is_active` | boolean | default:true | Enable/disable position for ad serving |
| `sort_order` | int | default:0 | Display order for position listing |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `advertisements` (Ad Management)

Manage individual ad creatives and their placement scheduling.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique ad identifier |
| `title` | varchar(255) | not null | Internal ad name for identification |
| `image_url` | varchar(255) | not null | Banner image URL |
| `link_url` | varchar(255) | not null | Target URL when ad is clicked |
| `position_id` | bigint | fk->ad_positions.id, nullable | Reference to ad position configuration |
| `position` | varchar(100) | nullable | **Deprecated**: Use `position_id` instead - Legacy position name string |
| `is_active` | boolean | default:true | Ad activation status |
| `clicks_count` | bigint | default:0 | Total click-through count |
| `views_count` | bigint | default:0 | Total impression count |
| `start_date` | timestamp | nullable | Campaign start datetime |
| `end_date` | timestamp | nullable | Campaign end datetime |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `journals` (Dev Logs / Microblogs)

Personal development logs and daily updates.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique journal identifier |
| `user_id` | bigint | fk->users.id, not null | Journal entry author |
| `title` | varchar(255) | nullable | Log entry title or subject line |
| `content` | text | not null | Journal content in Markdown format |
| `mood` | varchar(100) | nullable | Mood indicator (e.g., 'productive', 'tired') |
| `weather` | varchar(100) | nullable | Weather condition during writing |
| `date` | date | nullable | Actual date of the journal entry |
| `is_public` | boolean | default:true | Visibility flag for public display |
| `likes_count` | bigint | default:0 | Number of likes received |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `subscribers`

Newsletter subscription management for email marketing.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique subscriber identifier |
| `email` | varchar(255) | unique, not null | Subscriber email address |
| `name` | varchar(255) | nullable | Subscriber display name |
| `source` | varchar(100) | nullable | Subscription origin channel (website/newsletter/social) |
| `is_active` | boolean | default:true | Subscription status for email delivery |
| `subscribed_at` | timestamp | nullable | When the subscription was confirmed |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `user_points_history` (User Points History)

Track user points changes for gamification system.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique record identifier |
| `user_id` | bigint | fk->users.id, not null | User who earned/lost points |
| `points` | int | not null | Points amount (positive or negative) |
| `type` | varchar(100) | not null | Points type/category |
| `description` | text | nullable | Reason for points change |
| `reference_id` | bigint | nullable | Related record ID |
| `reference_type` | varchar(255) | nullable | Related model type |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `ad_interactions` (Ad Interactions)

Track ad clicks and views for analytics.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique interaction identifier |
| `advertisement_id` | bigint | fk->advertisements.id, not null | Associated advertisement |
| `user_id` | bigint | fk->users.id, nullable | User who interacted (null for guest) |
| `type` | enum | 'click','view', not null | Interaction type |
| `ip_address` | varchar(45) | not null | User IP address |
| `user_agent` | varchar(255) | nullable | Browser user agent string |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `social_links` (Social Links)

Social media links configuration.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique link identifier |
| `platform` | varchar(255) | not null | Platform name (e.g., 'github', 'twitter') |
| `url` | varchar(255) | not null | Social media profile URL |
| `icon` | varchar(255) | nullable | Icon class or path |
| `sort_order` | int | default:0 | Display order |
| `is_active` | boolean | default:true | Link visibility |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `media` (Media Library)

Media file management for uploaded assets.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique media identifier |
| `user_id` | bigint | fk->users.id, not null | User who uploaded the file |
| `filename` | varchar(255) | not null | Server-stored filename with unique identifier |
| `original_name` | varchar(255) | not null | Original filename from upload |
| `mime_type` | varchar(100) | not null | File MIME type (e.g., 'image/jpeg') |
| `file_size` | unsignedBigInteger | not null | File size in bytes |
| `url` | varchar(255) | not null | Full URL to access the file |
| `thumbnail_url` | varchar(255) | nullable | URL to thumbnail preview (for images/videos) |
| `alt_text` | varchar(255) | nullable | Alt text for accessibility and SEO |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `menus` (Menu Configuration)

Backend and frontend navigation menu management.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique menu item identifier |
| `type` | enum | 'front','admin', not null | Menu location context |
| `parent_id` | bigint | fk->menus.id, nullable | Parent menu item for nested menus |
| `label_key` | varchar(255) | not null | i18n translation key for menu label |
| `icon_name` | varchar(100) | nullable | Icon identifier (e.g., 'fa-home') |
| `path` | varchar(255) | nullable | Route path or external URL |
| `sort_order` | int | default:0 | Display order within menu level |
| `is_active` | boolean | default:true | Menu item visibility toggle |
| `component_name` | varchar(255) | nullable | Associated Vue component name for SPA routing |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `visits` (Visit Tracking)

Page view analytics with polymorphic relationships.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique visit identifier |
| `visitable_id` | unsignedBigInteger | not null | ID of visited content |
| `visitable_type` | varchar(255) | not null | Class name of visited model |
| `ip_address` | varchar(45) | not null | Visitor IP address |
| `user_agent` | varchar(255) | nullable | Browser client information |
| `referrer` | varchar(500) | nullable | Previous page URL (traffic source) |
| `created_at` | timestamp | nullable | Visit timestamp |
| **Index** | | (`visitable_id`, `visitable_type`) | Composite index for polymorphic queries |

---

### `settings` (System Settings)

System configuration with fixed fields for site settings.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique setting identifier |
| `name` | varchar(255) | default:'ARCHYX' | Site name |
| `description` | text | nullable | Site description |
| `site_url` | varchar(255) | nullable | Site URL |
| `copyright` | varchar(255) | nullable | Copyright text |
| `logo` | varchar(255) | nullable | Logo image path |
| `favicon` | varchar(255) | nullable | Favicon image path |
| `timezone` | varchar(255) | default:'Asia/Shanghai' | Site timezone |
| `maintenance_mode` | boolean | default:false | Enable/disable maintenance mode |
| `show_author_bio` | boolean | default:false | Show author bio on posts |
| `show_comments` | boolean | default:true | Enable comments |
| `allow_registration` | boolean | default:true | Allow user registration |
| `require_comment_approval` | boolean | default:false | Require approval for comments |
| `enable_newsletter` | boolean | default:true | Enable newsletter feature |
| `enable_social_login` | boolean | default:false | Enable social login |
| `enable_search` | boolean | default:true | Enable search functionality |
| `enable_cache` | boolean | default:true | Enable caching |
| `cache_duration` | int | default:3600 | Cache duration in seconds |
| `enable_minification` | boolean | default:true | Enable CSS/JS minification |
| `lazy_load_images` | boolean | default:true | Enable lazy loading for images |
| `enable_cdn` | boolean | default:false | Enable CDN |
| `cdn_url` | varchar(255) | nullable | CDN URL |
| `max_upload_size` | int | default:10 | Max upload size in MB |
| `allowed_file_types` | json | nullable | Allowed file types array |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `admin_logs` (Admin Operation Logs)

Audit trail for administrative actions.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique log identifier |
| `user_id` | bigint | fk->users.id, nullable | Admin user who performed action |
| `action` | varchar(255) | not null | Action type identifier (e.g., 'update', 'delete') |
| `description` | text | not null | Human-readable action description |
| `ip_address` | varchar(45) | not null | Admin IP address |
| `user_agent` | varchar(255) | nullable | Admin browser/client information |
| `created_at` | timestamp | nullable | Action timestamp |

---

### `user_levels` (User Levels)

Gamification level system for user engagement.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique level identifier |
| `name` | varchar(100) | not null | Level display name (e.g., 'Bronze', 'Silver') |
| `min_points` | int | not null | Minimum points required to reach this level |
| `max_points` | int | nullable | Maximum points for this level (null for max level) |
| `color` | varchar(50) | not null | Level badge color for UI (hex or named) |
| `icon` | varchar(100) | nullable | Level badge icon identifier |
| `description` | varchar(500) | nullable | Level description and benefits |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `footer_links` (Footer Configuration)

Footer section link management.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique link identifier |
| `type` | enum | 'social_link','nav_link','brand_info', not null | Link category for layout grouping |
| `label` | varchar(255) | not null | Link text label |
| `url` | varchar(500) | nullable | Link destination URL |
| `icon` | varchar(100) | nullable | Icon name for social links |
| `sort_order` | int | default:0 | Display order in footer |
| `is_active` | boolean | default:true | Link visibility toggle |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `page_seo` (Page SEO)

Per-page SEO configuration overrides.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique SEO config identifier |
| `page_key` | varchar(255) | unique, not null | Page identifier for routing (e.g., 'home', 'blog') |
| `title` | varchar(255) | not null | Custom SEO title tag |
| `description` | varchar(500) | not null | Meta description for search results |
| `keywords` | varchar(500) | nullable | Meta keywords (comma-separated) |
| `og_image` | varchar(255) | nullable | Open Graph share image URL |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `seo` (Global SEO Configuration)

Site-wide default SEO settings.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique config identifier |
| `meta_title` | varchar(255) | nullable | Global meta title |
| `meta_description` | text | nullable | Global meta description |
| `meta_keywords` | varchar(255) | nullable | Global meta keywords |
| `google_analytics` | varchar(255) | nullable | Google Analytics tracking ID |
| `baidu_analytics` | varchar(255) | nullable | Baidu Analytics tracking ID |
| `enable_sitemap` | boolean | default:true | Enable sitemap generation |
| `enable_robots` | boolean | default:true | Enable robots.txt |
| `enable_llm_txt` | boolean | default:false | Enable llm.txt for AI crawlers |
| `canonical_url` | varchar(255) | nullable | Canonical URL base |
| `og_image` | varchar(255) | nullable | Default Open Graph image |
| `og_type` | varchar(255) | default:'website' | Default OG type |
| `twitter_card` | varchar(255) | default:'summary_large_image' | Default Twitter card type |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `page_seo` (Page-level SEO)

Per-page SEO configuration for individual routes.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique config identifier |
| `page_key` | varchar(255) | unique, not null | Page identifier key |
| `title` | varchar(255) | not null | Page-specific meta title |
| `description` | varchar(500) | not null | Page-specific meta description |
| `keywords` | varchar(500) | nullable | Page-specific keywords |
| `og_image` | varchar(255) | nullable | Page-specific OG image |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `site` (Site Configuration)

Basic site information and branding settings.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique config identifier |
| `key` | varchar(255) | unique, not null | Configuration key (e.g., 'site_name') |
| `value` | json | not null | Configuration value |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `themes` (Theme Configuration)

Theme switching and customization settings.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique theme identifier |
| `name` | varchar(100) | unique, not null | Theme technical name for code reference |
| `label` | varchar(255) | not null | Theme display name for UI selection |
| `color` | varchar(50) | not null | Theme color scheme identifier |
| `sort_order` | int | default:0 | Display order in theme selection |
| `is_active` | boolean | default:false | Currently active theme flag |
| `is_default` | boolean | default:false | Default theme fallback flag |
| `preview_image` | varchar(255) | nullable | Theme preview screenshot URL |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `languages` (Language Configuration)

Internationalization language management.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique language identifier |
| `code` | varchar(10) | unique, not null | Language code (e.g., 'en', 'zh-CN') |
| `name` | varchar(255) | not null | Language display name |
| `native_name` | varchar(255) | not null | Language name in its own script |
| `flag` | varchar(20) | nullable | Flag icon identifier |
| `file_path` | varchar(200) | nullable | Translation file path |
| `direction` | varchar(3) | default:'ltr' | Text direction (ltr/rtl) |
| `is_active` | boolean | default:true | Language availability |
| `is_default` | boolean | default:false | Default language flag |
| `sort_order` | int | default:0 | Display order in language selector |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `footer_links` (Footer Links)

Footer navigation and social links management.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique link identifier |
| `type` | enum | 'social_link','nav_link','brand_info', not null | Link type category |
| `platform` | varchar(100) | nullable | Platform name (e.g., 'github', 'twitter') |
| `icon_name` | varchar(100) | nullable | Icon identifier for display |
| `label` | varchar(255) | not null | Link display text |
| `url` | varchar(500) | nullable | Link target URL |
| `icon` | varchar(100) | nullable | Icon class or path |
| `sort_order` | int | default:0 | Display order in footer |
| `is_active` | boolean | default:true | Link visibility |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `user_levels` (User Levels)

User gamification level system.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique level identifier |
| `name` | varchar(100) | not null | Level display name |
| `level` | int | not null | Level number/rank |
| `min_points` | int | not null | Minimum points required |
| `max_points` | int | nullable | Maximum points for this level |
| `discount` | int | default:0 | Discount percentage for level |
| `color` | varchar(50) | not null | Level badge color |
| `icon` | varchar(100) | nullable | Level badge icon |
| `description` | varchar(500) | nullable | Level description |
| `benefits` | json | nullable | Array of level benefits |
| `is_active` | boolean | default:true | Level availability |
| `sort_order` | int | default:0 | Display order |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `author_profiles` (Author Profiles)

Extended author information with i18n support and social integration.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique profile identifier |
| `user_id` | bigint | fk->users.id, not null, unique | Associated user account |
| `slug` | varchar(255) | unique, not null | URL-friendly author identifier |
| `bio` | text | nullable | Personal biography/introductory text |
| `avatar` | varchar(255) | nullable | Profile avatar image URL |
| `role_label` | varchar(255) | nullable | i18n key for role label (e.g., 'role_designer') |
| `role_title` | varchar(255) | nullable | i18n key for job title (e.g., 'architect_designer') |
| `status_label` | varchar(255) | nullable | i18n key for status label |
| `status_text` | varchar(255) | nullable | i18n key for current status text |
| `is_active` | boolean | default:true | Profile visibility on author page |
| `social_links` | json | nullable | Social media links object {github, twitter, linkedin, website} |
| `expertise` | json | nullable | Array of expertise areas |
| `skills` | json | nullable | Array of skill objects with {label, value, description, category} |
| `manifestos` | json | nullable | Array of author philosophy statements |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `backups` (Backup Records)

System backup job tracking and status.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique backup identifier |
| `filename` | varchar(255) | not null | Backup archive filename |
| `size` | unsignedBigInteger | not null | Backup file size in bytes |
| `status` | enum | 'pending','running','completed','failed', not null | Current backup job status |
| `type` | enum | 'full','database','files', not null | Backup type coverage |
| `started_at` | timestamp | not null | Backup job start time |
| `completed_at` | timestamp | nullable | Backup job completion time |
| `error_message` | text | nullable | Error details if backup failed |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `email_templates` (Email Templates)

Email template management for notifications and marketing.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique template identifier |
| `name` | varchar(255) | unique, not null | Template identifier for selection |
| `subject` | varchar(255) | not null | Email subject line template |
| `content` | longtext | not null | Email body with Blade template syntax |
| `variables` | json | nullable | Available placeholder variables |
| `is_active` | boolean | default:true | Template activation status |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `mail_settings` (Mail Configuration)

SMTP and mail service configuration.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique config identifier |
| `mailer` | varchar(50) | not null | Mail driver name (smtp, mailgun, ses, etc.) |
| `host` | varchar(255) | nullable | SMTP server hostname |
| `port` | int | nullable | SMTP server port number |
| `username` | varchar(255) | nullable | SMTP authentication username |
| `password` | varchar(255) | nullable | SMTP authentication password (encrypted) |
| `encryption` | varchar(20) | nullable | Encryption type (tls, ssl, null) |
| `from_address` | varchar(255) | not null | Default sender email address |
| `from_name` | varchar(255) | not null | Default sender display name |
| `is_active` | boolean | default:false | Use this configuration |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `translations` (Internationalization)

Database-driven translation management.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique translation identifier |
| `group` | varchar(100) | not null | Translation group (e.g., 'messages', 'validation') |
| `key` | varchar(150) | not null | Translation key within group |
| `text` | json | not null | Translation values keyed by locale {en: '', zh: ''} |
| `description` | varchar(500) | nullable | Translation context or notes |
| `is_active` | boolean | default:true | Enable/disable this translation |
| `sort_order` | int | default:0 | Display/order priority |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |
| **Unique Index** | | (`group`, `key`) | Composite unique constraint |

---

## Role-Based Access Control (RBAC)

Using **Spatie/laravel-permission** package with extended custom fields for enterprise-grade permissions management.

### `roles`

User roles for access control (managed by Spatie).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique role identifier |
| `name` | varchar(255) | unique, not null | Role system name for code reference (e.g., 'admin', 'editor') |
| `value` | varchar(255) | unique, not null | Role value for database storage and comparisons |
| `guard_name` | varchar(100) | nullable, default:'web' | Guard name for auth driver (web, api, etc.) |
| `label` | varchar(255) | nullable | Human-readable role label for UI display (i18n key) |
| `color` | varchar(50) | nullable | Role badge color for UI (hex or named) |
| `description` | text | nullable | Role responsibilities and scope |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `permissions`

System permission definitions with Spatie compatibility.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | pk, auto-increment | Primary key, unique permission identifier |
| `name` | varchar(255) | unique, not null | Permission name for authorization (e.g., 'create posts') |
| `guard_name` | varchar(100) | nullable, default:'web' | Guard name for auth driver |
| `label` | varchar(255) | nullable | Human-readable permission label for UI |
| `description` | text | nullable | Permission purpose and scope description |
| `program_id` | varchar(100) | nullable | Associated program/project identifier |
| `created_at` | timestamp | nullable | Record creation timestamp |
| `updated_at` | timestamp | nullable | Record last modification timestamp |

---

### `role_has_permissions` (Pivot - Managed by Spatie)

Many-to-many relationship between roles and permissions.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `permission_id` | bigint | fk->permissions.id, not null | Permission reference |
| `role_id` | bigint | fk->roles.id, not null | Role reference |
| **Primary Key** | | (`permission_id`, `role_id`) | Composite primary key |

---

### `model_has_roles` (Pivot - Managed by Spatie)

Polymorphic many-to-many relationship linking users to roles.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `role_id` | bigint | fk->roles.id, not null | Role reference |
| `model_type` | varchar(255) | not null | Model class name (e.g., 'App\Models\User') |
| `model_id` | bigint | not null | User/Model ID |
| **Primary Key** | | (`role_id`, `model_id`, `model_type`) | Composite primary key |

---

### `model_has_permissions` (Pivot - Managed by Spatie)

Direct permissions assigned to users bypassing roles.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `permission_id` | bigint | fk->permissions.id, not null | Permission reference |
| `model_type` | varchar(255) | not null | Model class name |
| `model_id` | bigint | not null | User/Model ID |
| **Primary Key** | | (`permission_id`, `model_id`, `model_type`) | Composite primary key |

---

## Laravel Framework Built-in Tables

The following tables are created automatically by Laravel framework or packages through artisan commands. These are infrastructure tables managed by Laravel.

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

---

## Data File Mapping

Reference mapping between frontend static data files and database tables.

| Data File | Database Table | Status | Notes |
|-----------|---------------|:------:|-------|
| `ad_positions.js` | `ad_positions` | ✅ Complete | Ad position configuration |
| `advertisements.js` | `advertisements` | ✅ Complete | Ad content management |
| `author_profiles.js` | `author_profiles` | ✅ Complete | Author profiles with i18n |
| `backups.js` | `backups` | ✅ Complete | Backup records |
| `categories.js` | `categories` | ✅ Complete | Content categories |
| `comments.js` | `comments` | ✅ Complete | User comments |
| `email_templates.js` | `email_templates` | ✅ Complete | Email templates |
| `footer_config.js` | `footer_links` | ✅ Complete | Footer link configuration |
| `interactions.js` | `interactions` | ✅ Complete | Likes and bookmarks |
| `journals.js` | `journals` | ✅ Complete | Development logs |
| `languages.js` | `languages` | ✅ Complete | Language configuration |
| `logs.js` | `admin_logs` | ✅ Complete | Admin operation logs |
| `mail_config.js` | `mail_configs` | ✅ Complete | Mail configuration |
| `media.js` | `media` | ✅ Complete | Media library |
| `menu.js` | `menus` | ✅ Complete | Menu configuration |
| `notifications.js` | `notifications` | ✅ Laravel Built-in | Laravel notifications |
| `page_seo.js` | `page_seo` | ✅ Complete | Per-page SEO |
| `permissions.js` | `permissions` | ✅ Complete | Permission definitions |
| `posts.js` | `posts` | ✅ Complete | Blog posts |
| `projects.js` | `projects` | ✅ Complete | Portfolio projects |
| `resources.js` | `resources` | ✅ Complete | Downloadable resources |
| `role_permissions.js` | `role_has_permissions` | ✅ Complete | Role-permission mapping (Spatie) |
| `roles.js` | `roles` | ✅ Complete | Role definitions (Spatie extended) |
| `seo_config.js` | `seo` | ✅ Complete | Global SEO settings |
| `settings.js` | `settings` | ✅ Complete | System settings |
| `site_config.js` | `site` | ⚠️ Pending | Site configuration (may merge with settings) |
| `social_links.js` | `social_links` | ✅ Complete | Social links |
| `subscribers.js` | `subscribers` | ✅ Complete | Email subscribers |
| `tags.js` | `tags` | ✅ Complete | Content tags |
| `taggables.js` | `taggables` | ✅ Complete | Tag relationships |
| `themes.js` | `themes` | ✅ Complete | Theme configuration |
| `translations.js` | `translations` | ✅ Complete | Translation management |
| `user_levels.js` | `user_levels` | ✅ Complete | User level system |
| `user_points_history.js` | `user_points_history` | ✅ Complete | User points history |
| `users.js` | `users` | ✅ Complete | User accounts |
| `videos.js` | `videos` | ✅ Complete | Video content |
| `visits.js` | `visits` | ✅ Complete | Visit analytics |

---

## Deleted/Renamed Files

Historical record of deprecated data files.

| Old File | Current Status | Replacement | Note |
|----------|---------------|-------------|------|
| `author.js` | ❌ Deleted | `author_profiles.js` | Merged into comprehensive author profiles |
| `home.js` | ❌ Deleted | N/A | Home page config removed |
| `links.js` | ❌ Deleted | N/A | Friendship links removed |
| `manifestos.js` | ❌ Deleted | `author_profiles.js` (manifestos field) | Integrated into author profiles |
| `skills.js` | ❌ Deleted | `author_profiles.js` (skills field) | Integrated into author profiles |

---

## Database Design Principles

| Principle | Application | Status |
|-----------|-------------|:------:|
| **Normalization** | Data follows 1NF/2NF/3NF to reduce redundancy | ✅ |
| **Consistency** | Data files and DB schema aligned (100% match) | ✅ |
| **Integrity** | Foreign keys and constraints enforce data validity | ✅ |
| **Relationships** | Proper foreign key relationships between related entities | ✅ |
| **Extensibility** | JSON fields allow flexible data storage | ✅ |
| **i18n Support** | translations table + label_key fields enable internationalization | ✅ |
| **Audit Trail** | admin_logs table tracks all administrative changes | ✅ |
| **RBAC Integration** | Spatie/laravel-permission with extended custom fields | ✅ |
| **Soft Deletes** | Not implemented - hard deletes used | ⚠️ Optional |

---

## Index Strategy

Performance indexes for frequently queried columns.

| Table | Column(s) | Index Type | Purpose |
|-------|-----------|------------|---------|
| `users` | `email` | Unique | Login lookup |
| `users` | `role_id` | Index | RBAC queries |
| `posts` | `slug` | Unique | URL routing |
| `posts` | `author_id`, `status`, `published_at` | Composite | Blog queries |
| `categories` | `slug` | Unique | URL routing |
| `categories` | `parent_id` | Index | Hierarchical queries |
| `tags` | `slug` | Unique | URL routing |
| `comments` | `post_id`, `is_approved` | Composite | Comment queries |
| `interactions` | `user_id`, `type` | Composite | User activity |
| `taggables` | `tag_id`, `taggable_type` | Composite | Tag queries |
| `visits` | `visitable_id`, `visitable_type` | Composite | Analytics queries |
| `advertisements` | `position_id`, `is_active` | Composite | Ad serving |
| `subscribers` | `email` | Unique | Email uniqueness |
| `menus` | `type`, `parent_id` | Composite | Menu tree queries |
| `translations` | `group`, `key` | Unique | Translation lookup |
| `page_seo` | `page_key` | Unique | Page SEO lookup |
| `languages` | `code` | Unique | Language lookup |
| `user_levels` | `min_points`, `max_points` | Range | Level calculation |