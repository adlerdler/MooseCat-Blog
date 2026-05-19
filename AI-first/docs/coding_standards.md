# Coding Standards

To ensure a cohesive, robust, and maintainable codebase across the entire stack, all developers must adhere to the following detailed coding standards.

## 1. PHP & Laravel (Backend Core)

### 1.1 General PHP Rules
- **PSR-12 Compliance:** Follow standard PSR-12 formatting rules.
- **Strict Typing:** All new PHP files MUST declare strict types at the very top: `declare(strict_types=1);`.
- **Type Hinting:** Explicitly define parameter types and return types for every method. Use `mixed` or `?Type` only when absolutely necessary and avoid omitting types.

### 1.2 Laravel Conventions
- **Naming Conventions:**
  - **Classes/Models/Controllers:** `PascalCase` (e.g., `PostController`, `User`).
  - **Methods/Variables:** `camelCase` (e.g., `getPublishedPosts()`, `$activeUsers`).
  - **Database Columns & Tables:** `snake_case` (e.g., `created_at`, `post_id`, `blog_posts`).
  - **Relationships:** `camelCase` for methods (e.g., `public function author()`, `public function blogPosts()`).
- **Controllers:** Controllers must be highly focused ("thin"). They should ONLY receive a request, call a specialized Service, and return a Response/Resource. Controller methods should rarely exceed 15-20 lines.
- **Validation:** ALWAYS use `FormRequests` for validating incoming request data and authorization checks. Never use `$request->validate()` inside controller endpoints.
- **Business Logic:** Encapsulate reusable business rules and complex operations inside the `app/Services` directory.
- **Database & Eloquent:**
  - **N+1 Queries:** Prevent N+1 query problems by actively using Eager Loading (`with()`, `load()`).
  - **Event Side-Effects:** Use Model Observers or Laravel Events for side-effects (e.g., clearing cache, sending emails on creation) rather than polluting controller logic.
  - **API Consistency:** Keep API responses structurally consistent by always passing eloquent models/collections through `Http/Resources`.

## 2. Vue 3 & TypeScript (Web Frontend)

### 2.1 File & Component Structure
- **Language:** Strictly use **TypeScript** (`.vue` and `.ts`). Avoid `.js` files.
- **SFC (Single File Components):** Use the `<script setup>` syntax with TypeScript.
- **Naming Conventions:** 
  - **Components:** `PascalCase` file and component names (e.g., `UserProfile.vue`).
  - **Composables/Utilities:** `camelCase` (e.g., `useAuth.ts`, `formatDate.ts`).
- **Template Logic:** Keep templates clean. Move complex logic into `computed` properties or methods in the script block.

### 2.2 Selective Hydration Specifics
- **Vue as a Library:** In this project, Vue is treated as a component library for enhancing Blade pages, not as a routing framework.
- **Blade-First:** Always prefer Blade for static content and simple forms (e.g., Tag/Category CRUD) to ensure maximum SEO and minimum JS bundle size.
- **Vue-Second:** Use Vue only for complex interactive islands:
  - Markdown editing with **Vditor**.
  - Asynchronous interactions like Comments and Likes.
  - Client-side heavy logic like Image Cropping/Uploading.
- **Component Registration:** Global components are registered in `app.js`. Use them sparingly within Blade templates using the custom element syntax: `<comment-section :post-id="{{ $post->id }}"></comment-section>`.

### 2.3 Styling
- **Tailwind CSS:** Rely entirely on Tailwind CSS utility classes. Avoid creating custom external CSS/SCSS files unless absolutely necessary for complex animations or resetting third-party widget styles.
- **Conditional Classes:** Use standard Vue class binding `:class="{ 'active': isActive }"` or utility functions like `clsx` and `tailwind-merge` if needed.

## 3. Flutter & Dart (Mobile App)

### 3.1 General Dart Rules
- **Linting:** Every project must incorporate the official `flutter_lints` package (or `very_good_analysis`). Treat linter warnings as errors to be resolved before committing code.
- **Naming Conventions:**
  - **Files & Directories:** `snake_case` (e.g., `user_profile_screen.dart`, `auth_repository.dart`).
  - **Classes & Enums:** `PascalCase` (e.g., `UserProfileScreen`, `AuthStatus`).
  - **Variables & Methods:** `camelCase` (e.g., `isLoading`, `fetchData()`).
- **Immutability:** Aggressively use `final` for variables that don't change post-initialization and `const` for compile-time constants. Widget constructors should be `const` whenever structurally possible to optimize the build cycle.

### 3.2 Architecture & Widgets
- **State Management:** Standardize on a single, decoupled state management solution (e.g., `Bloc`/`Cubit`, `Riverpod`, or `Provider`). Avoid deeply nested or complex `setState()` logic within the UI files.
- **Widget Composition:** Keep the `build()` methods small and readable. If a build method body exceeds ~50 lines, refactor branches of the UI into smaller, strictly-typed private or public `StatelessWidget` classes. Avoid creating helper methods that return Widgets where possible.
- **Network Layer:** Utilize a robust HTTP client wrapper (like `Dio`) combined with code generation (`Retrofit` + `json_serializable` or `freezed`) to safely map API JSON responses directly to strongly-typed Dart domain models.
- **Error Handling:** Never silently swallow exceptions. Use explicit `try-catch` blocks at the repository layer and marshal errors into standardized 'Failure' objects or `Result` types matching back to the UI.
