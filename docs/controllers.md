# Controller Design

All API controllers should be placed in the `app/Http/Controllers/Api/V1` namespace to allow for future versioning. Controllers should only be responsible for receiving requests, calling services, and returning formatted responses.

## `PostController`

Manages Blog posts.

- `GET /api/v1/posts`
  - List posts (supports pagination, search query, and filtering by tags).
  - Returns a paginated collection of `PostResource`.
- `GET /api/v1/posts/{slug}`
  - Retrieves a full post detail by slug.
  - Returns a single `PostResource` with loaded relationships (e.g., tags).
- `POST /api/v1/posts` _(Admin only)_
  - Creates a new post.
- `PUT /api/v1/posts/{post}` _(Admin only)_
  - Updates an existing post.
- `DELETE /api/v1/posts/{post}` _(Admin only)_
  - Deletes a post.

## `CommentController`

Manages comments on blog posts.

- `GET /api/v1/posts/{post}/comments`
  - Lists comments for a specific post.
  - Returns a collection of `CommentResource`.
- `POST /api/v1/posts/{post}/comments`
  - Submits a new comment. Requires `name`, `email`, and `body`.
- `DELETE /api/v1/comments/{comment}` _(Admin only)_
  - Deletes a specific comment.

## `ProjectController`

Manages portfolio projects.

- `GET /api/v1/projects`
  - Lists projects.
- `GET /api/v1/projects/{project}`
  - Retrieves project details.

## `ResourceController`

Manages downloadable resources (e.g., PDFs, ZIPs).

- `GET /api/v1/resources`
  - Lists available resources (supports filtering by category).

## `AdvertisementController`

Manages advertisements to display across the platform.

- `GET /api/v1/ads`
  - Lists currently active advertisements. Can be filtered by `position` (e.g., `?position=home_top`).
- `POST /api/v1/ads/{ad}/click`
  - Tracks a click on a specific advertisement.

## `VideoController`

Manages the video library contents.

- `GET /api/v1/videos`
  - Lists videos with their platform specific IDs (YouTube/Bilibili).

## Example Controller Method

```php
public function index(Request $request, PostService $postService)
{
    $posts = $postService->getPaginatedPosts($request->all());

    return PostResource::collection($posts);
}
```
