# Controller Design & API Documentation

All API controllers should be placed in the `app/Http/Controllers/Api/V1` namespace to allow for future versioning. Controllers should only be responsible for receiving requests, calling services, and returning formatted responses.

### Architecture Flow
```
Controller → Service → Repository/Model → Database
```

- **Controller**: Receive requests, validate input, call services, return responses
- **Service**: Business logic, transaction management, orchestration
- **Repository**: Complex database queries (Lightweight pattern, no Interface required)
- **Model**: Simple CRUD operations directly

---

## API Endpoints Overview

| Module | Base Path | Description |
|--------|-----------|-------------|
| Posts | `/api/v1/posts` | Blog posts management |
| Comments | `/api/v1/posts/{post}/comments` | Post comments |
| Videos | `/api/v1/videos` | Video library |
| Projects | `/api/v1/projects` | Portfolio projects |
| Resources | `/api/v1/resources` | Downloadable resources |
| Categories | `/api/v1/categories` | Content categories |
| Tags | `/api/v1/tags` | Content tags |
| Users | `/api/v1/users` | User profiles |

---

## 1. PostController

Manages Blog posts.

### Endpoints

| Method | Endpoint | Controller Method | Description |
|--------|----------|-------------------|-------------|
| GET | `/api/v1/posts` | `index` | List posts with pagination |
| GET | `/api/v1/posts/{slug}` | `show` | Get post by slug |

### GET /api/v1/posts

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `page` | int | Page number (default: 1) |
| `per_page` | int | Items per page (default: 12) |
| `category` | string | Filter by category slug |
| `tag` | string | Filter by tag slug |
| `search` | string | Search by title |

**Response Example:**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Hello World",
            "slug": "hello-world",
            "excerpt": "Welcome to my blog",
            "cover_image": "https://example.com/cover.jpg",
            "status": "published",
            "views_count": 1234,
            "likes_count": 42,
            "published_at": "2024-01-15T10:30:00Z",
            "category": {
                "id": 1,
                "name": "Tech",
                "slug": "tech"
            },
            "tags": [
                { "id": 1, "name": "Laravel", "slug": "laravel" }
            ],
            "author": {
                "id": 1,
                "name": "John Doe",
                "avatar": null
            }
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "total": 48
    }
}
```

### GET /api/v1/posts/{slug}

**Response Example:**

```json
{
    "id": 1,
    "title": "Hello World",
    "slug": "hello-world",
    "excerpt": "Welcome to my blog",
    "content": "# Hello World\n\nThis is my first post.",
    "cover_image": "https://example.com/cover.jpg",
    "color": "#333333",
    "status": "published",
    "views_count": 1235,
    "likes_count": 42,
    "meta_title": "Hello World | My Blog",
    "meta_description": "Welcome to my blog",
    "meta_keywords": "blog, laravel, vue",
    "published_at": "2024-01-15T10:30:00Z",
    "created_at": "2024-01-15T10:00:00Z",
    "updated_at": "2024-01-15T11:00:00Z",
    "category": {
        "id": 1,
        "name": "Tech",
        "slug": "tech"
    },
    "tags": [
        { "id": 1, "name": "Laravel", "slug": "laravel" }
    ],
    "author": {
        "id": 1,
        "name": "John Doe",
        "avatar": null,
        "bio": "Full-stack developer"
    },
    "comments_count": 15
}
```

---

## 2. CommentController

Manages comments on blog posts.

### Endpoints

| Method | Endpoint | Controller Method | Description |
|--------|----------|-------------------|-------------|
| GET | `/api/v1/posts/{post}/comments` | `index` | List post comments |
| POST | `/api/v1/posts/{post}/comments` | `store` | Submit new comment |

### GET /api/v1/posts/{post}/comments

**Response Example:**

```json
{
    "data": [
        {
            "id": 1,
            "body": "Great post!",
            "name": "Anonymous",
            "created_at": "2024-01-15T12:00:00Z",
            "replies": []
        }
    ]
}
```

### POST /api/v1/posts/{post}/comments

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | yes | Commenter name |
| `email` | string | yes | Commenter email |
| `body` | string | yes | Comment content |
| `parent_id` | int | no | Parent comment ID (for replies) |

**Response Example:**

```json
{
    "id": 16,
    "body": "Thanks for your comment!",
    "name": "John Doe",
    "created_at": "2024-01-15T15:30:00Z"
}
```

---

## 3. VideoController

Manages the video library contents.

### Endpoints

| Method | Endpoint | Controller Method | Description |
|--------|----------|-------------------|-------------|
| GET | `/api/v1/videos` | `index` | List videos |
| GET | `/api/v1/videos/{video}` | `show` | Get video detail |

### GET /api/v1/videos

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `platform` | string | Filter by platform (youtube/bilibili) |

**Response Example:**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Laravel Tutorial",
            "description": "Learn Laravel from scratch",
            "video_id": "abc123",
            "platform": "youtube",
            "thumbnail": "https://i.ytimg.com/vi/abc123/hqdefault.jpg",
            "duration": "15:30",
            "views_count": 5000,
            "likes_count": 200,
            "published_at": "2024-01-10T08:00:00Z"
        }
    ]
}
```

---

## 4. ProjectController

Manages portfolio projects.

### Endpoints

| Method | Endpoint | Controller Method | Description |
|--------|----------|-------------------|-------------|
| GET | `/api/v1/projects` | `index` | List projects |
| GET | `/api/v1/projects/{project}` | `show` | Get project detail |

### GET /api/v1/projects

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `status` | string | Filter by status (planning/in-progress/completed) |
| `tag` | string | Filter by tag slug |

**Response Example:**

```json
{
    "data": [
        {
            "id": 1,
            "title": "My Portfolio",
            "description": "Personal portfolio website",
            "client": "Self",
            "role": "Developer",
            "year": 2024,
            "image": "https://example.com/project.jpg",
            "url": "https://example.com",
            "github_url": "https://github.com/user/portfolio",
            "technologies": ["Laravel", "Vue", "Tailwind"],
            "status": "completed",
            "views_count": 1000,
            "likes_count": 50,
            "tags": [
                { "id": 1, "name": "Laravel", "slug": "laravel" }
            ]
        }
    ]
}
```

---

## 5. ResourceController

Manages downloadable resources (e.g., PDFs, ZIPs).

### Endpoints

| Method | Endpoint | Controller Method | Description |
|--------|----------|-------------------|-------------|
| GET | `/api/v1/resources` | `index` | List resources |
| GET | `/api/v1/resources/{resource}` | `show` | Get resource detail |

### GET /api/v1/resources

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `category` | int | Filter by category ID |
| `format` | string | Filter by format (PDF/ZIP/etc) |
| `tag` | string | Filter by tag slug |

**Response Example:**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Laravel Cheat Sheet",
            "description": "Useful Laravel commands and tips",
            "format": "PDF",
            "file_size": "2.4 MB",
            "image": "https://example.com/cover.png",
            "direct_link": "https://example.com/download.pdf",
            "downloads_count": 1500,
            "likes_count": 80,
            "category": {
                "id": 1,
                "name": "Guides",
                "slug": "guides"
            },
            "tags": [
                { "id": 1, "name": "Laravel", "slug": "laravel" }
            ]
        }
    ]
}
```

---

## 6. CategoryController

Manages content categories.

### Endpoints

| Method | Endpoint | Controller Method | Description |
|--------|----------|-------------------|-------------|
| GET | `/api/v1/categories` | `index` | List all categories |
| GET | `/api/v1/categories/{slug}` | `show` | Get category detail |

**Response Example (GET /api/v1/categories):**

```json
{
    "data": [
        {
            "id": 1,
            "name": "Tech",
            "slug": "tech",
            "description": "Technology articles",
            "sort_order": 1,
            "children": [
                {
                    "id": 2,
                    "name": "Web Development",
                    "slug": "web-development",
                    "parent_id": 1
                }
            ],
            "posts_count": 25
        }
    ]
}
```

---

## 7. TagController

Manages content tags.

### Endpoints

| Method | Endpoint | Controller Method | Description |
|--------|----------|-------------------|-------------|
| GET | `/api/v1/tags` | `index` | List all tags |
| GET | `/api/v1/tags/{slug}` | `show` | Get tag detail |

**Response Example (GET /api/v1/tags):**

```json
{
    "data": [
        {
            "id": 1,
            "name": "Laravel",
            "slug": "laravel",
            "posts_count": 15
        }
    ]
}
```

---

## 8. UserController

Manages user profiles.

### Endpoints

| Method | Endpoint | Controller Method | Description |
|--------|----------|-------------------|-------------|
| GET | `/api/v1/users/{user}` | `show` | Get user profile |

**Response Example:**

```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "avatar": null,
    "bio": "Full-stack developer passionate about Laravel and Vue",
    "github": "github.com/johndoe",
    "twitter": "@johndoe",
    "linkedin": "linkedin.com/in/johndoe",
    "posts_count": 50,
    "created_at": "2023-01-01T00:00:00Z"
}
```

---

## Web Routes (Blade Views)

| Route | Controller | Description |
|-------|------------|-------------|
| `/posts` | `Web\PostController@index` | Blog list page |
| `/posts/{slug}` | `Web\PostController@show` | Blog detail page |
| `/videos` | `Web\VideoController@index` | Video list page |
| `/videos/{video}` | `Web\VideoController@show` | Video detail page |
| `/projects` | `Web\ProjectController@index` | Project list page |
| `/projects/{project}` | `Web\ProjectController@show` | Project detail page |
| `/resources` | `Web\ResourceController@index` | Resource list page |
| `/resources/{resource}` | `Web\ResourceController@show` | Resource detail page |
| `/categories` | `Web\CategoryController@index` | Category list page |
| `/categories/{slug}` | `Web\CategoryController@show` | Category detail page |
| `/tags/{slug}` | `Web\CategoryController@tag` | Tag detail page |

---

## Admin Routes (Authenticated)

All admin routes are prefixed with `/admin` and require authentication.

| Resource | Routes | Controller |
|----------|--------|------------|
| Posts | `/admin/posts` (CRUD) | `Admin\PostController` |
| Videos | `/admin/videos` (CRUD) | `Admin\VideoController` |
| Projects | `/admin/projects` (CRUD) | `Admin\ProjectController` |
| Resources | `/admin/resources` (CRUD) | `Admin\ResourceController` |
| Categories | `/admin/categories` (CRUD) | `Admin\CategoryController` |
| Tags | `/admin/tags` (CRUD) | `Admin\TagController` |

---

## Example Controller Method

```php
public function index(Request $request, PostService $postService)
{
    $posts = $postService->getPaginatedPosts($request->all());

    return PostResource::collection($posts);
}
```
