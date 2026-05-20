# Evolution of Archyx Blog

> 记录项目的演进历程与人类智慧的注入。

## 人类导师 (Human Mentors)

本项目由 AI 驱动，但其灵魂由以下人类导师塑造：

- **adlerdler** (项目发起者/核心导师)

## 架构演进记录 (Architectural Decisions)

### 2026-05-08: 项目初始化与 AI 协作环境配置 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 采用 Laravel 11 + Vue 3 的极简架构，并同步引入 `CLAUDE.md`, `AI.md`, `agents.md`, `WORKFLOW.md` 等 AI 治理文件。
- **Rationale:**
  - Laravel 11 的精简结构（移除大量 Provider 和 Config）完美契合 Extreme Minimalism。
  - 通过 AI 治理文件，确立了 Agentic 开发的"宪法"，确保 AI 在开发过程中能自主理解愿景、规范和决策路径，减少沟通损耗。
- **Status:** 已完成基础环境搭建，集成 Tailwind CSS，并为 AI 助手配置了完整的运行上下文。

### 2026-05-08: 品牌视觉重塑：从 MooseCat 到 Archyx (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 将项目名称由 MooseCat 变更为 **Archyx**，并全面重写 README.md 视觉文档。
- **Rationale:**
  - **Archyx** (Arch + Onyx/Modern) 更能体现架构的稳重与现代审美的碰撞。
  - 引入 Shields.io 徽章、Unsplash 视觉占位图和 Mermaid 架构图，使文档从"纯文本"进化为"可视化资产"，符合专业开源项目标准。
- **Status:** 全局搜索替换完成，README.md 已实现中英文双语图文并茂展示。

### 2026-05-08: 数据库基础建设与模型关联设计 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 根据 `docs/migrations.md` 蓝图，一次性落地 12 个核心模块的数据库迁移与 Eloquent 模型。
- **Rationale:**
  - 采用 **Data-First** 策略，先行构建坚实的数据底层。
  - 包含文章 (Posts)、分类 (Categories)、标签 (Tags)、评论 (Comments)、互动 (Interactions)、资源 (Resources)、视频 (Videos)、日记 (Journals) 等，涵盖了现代化博客的所有互动维度。
  - 引入 `Schema::defaultStringLength(191)` 解决旧版数据库索引兼容性问题。
- **Status:** 数据库迁移已执行，模型关联（多态关联如 Taggables/Interactions）已通过单元测试思维验证。

### 2026-05-08: 全面数据模拟与 Seeder 构建 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 为所有 11 个数据库表编写了专用的 Seeder 填充类，并成功执行 `php artisan db:seed`。
- **Rationale:**
  - 为前端展示提供真实、高质量的模拟数据（包含 Markdown 内容、分类、标签关联等）。
  - 验证数据库迁移的完整性与模型关联的正确性（如多态关联的点赞与标签）。
- **Status:** 已完成 11 个 Seeder 类的编写与数据填充，项目现在拥有了可供演示的基础内容。

### 2026-05-08: 引入业务逻辑层 (Service Layer) 的解耦实践 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 在 `app/Services` 目录下创建 `PostService`, `CommentService`, `InteractionService`。
- **Rationale:**
  - 贯彻 **Thin Controller** 原则，避免 Controller 膨胀。
  - 通过 Service 层封装核心业务逻辑（如：文章发布工作流、评论嵌套树生成、跨模型点赞逻辑），实现 Web 端（Blade）与移动端（API）的代码逻辑 100% 共享。
- **Status:** 核心 Service 已落地，代码结构更加原子化，符合 Clean Code 规范。

### 2026-05-08: 架构大转向：废弃 Inertia，确立"混合局部增强"模式 (by Human & Trae)
- **Developer:** Human (adlerdler) 决策 / Trae (AI) 执行
- **Decision:** 彻底移除 Inertia.js 相关组件与中间件，回归 **Blade + Vue 3 局部增强** 模式。
- **Rationale:**
  - **Human Insight:** 人类导师指出 Inertia 在当前环境下配置冗余且容易导致白屏，直接使用极简 HTML 容器挂载 Vue 更为直观。
  - **Minimalism:** 混合模式让前台保持 100% SEO 友好，仅在编辑器、评论等复杂交互点"按需注入"Vue。
  - **Transparency:** 这种架构让前后端界限清晰，数据流通过 `@json` 显式传递，极大地降低了调试成本。
- **Status:**
  - 已卸载 `inertiajs/inertia-laravel`。
  - 删除了 `app.blade.php` 和相关中间件。
  - 更新了 `vite.config.js` 配置 Vue 运行时编译器别名。
  - 同步更新了所有 docs 架构文档。

### 2026-05-08: 控制器与 API 资源的标准化建设 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 实现分层控制器 `Web` 与 `Api/V1`，并引入 `JsonResource` 转换层。
- **Rationale:**
  - 确保 API 响应的结构稳定性，方便移动端（Flutter）对接。
  - Web 控制器统一返回 `view()`，配合 `compact()` 将数据送入 Blade。
- **Status:** `PostController` 和 `CommentController` 已完成双端适配。

### 2026-05-08: 完整 API 路由体系与管理后台建设 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 完成所有模块的 API 和 Web 路由配置，并建立 `/admin/*` 管理后台路由体系。
- **Rationale:**
  - 使用 `php artisan make:controller` 命令标准化创建控制器，确保代码风格一致。
  - API 层覆盖 Posts、Comments、Videos、Projects、Resources、Categories、Tags、Users 八个模块。
  - 管理后台采用 RESTful 资源路由，完整支持 CRUD 操作，并通过 `auth` 中间件保护。
- **Status:**
  - 创建 6 个新 API 控制器（Video、Project、Resource、Category、Tag、User）。
  - 创建 4 个新 Web 控制器（Video、Project、Resource、Category）。
  - 创建 6 个 Admin 控制器（Post、Video、Project、Resource、Category、Tag）。
  - 更新 `routes/web.php` 和 `routes/api.php`，共配置 73 个路由。

### 2026-05-08: API 文档与管理后台规范完善 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 全面更新 `docs/controllers.md` 和 `docs/admin_panel.md`，补充完整的接口文档。
- **Rationale:**
  - 为前端开发和移动端对接提供清晰的 API 契约。
  - 定义管理后台的路由规范和安全策略。
  - 确保团队协作时所有成员都能快速理解接口结构。
- **Status:**
  - `docs/controllers.md`：添加完整的 API 端点列表、请求参数、响应示例。
  - `docs/admin_panel.md`：添加详细的管理后台路由表和安全考虑事项。

### 2026-05-09: UI 优化与全局交互改进 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 修复全屏菜单、语言切换、布局比例、主题切换、动画效果等多项 UI 问题。
- **Rationale:**
  - 统一全屏菜单的打开/关闭动画和交互体验。
  - 优化语言切换器在侧边栏的显示位置。
  - 调整页面布局比例，确保在不同屏幕尺寸下都能良好显示。
  - 完善主题切换功能，支持多种主题色动态切换。
  - 优化页面过渡动画和元素进入动画。
- **Status:**
  - 修复 SidebarMenu 组件的全屏菜单动画。
  - 优化语言切换按钮的位置和样式。
  - 调整 Blog.vue 等页面的布局比例。
  - 完善 useTheme composable，支持主题持久化。
  - 添加淡入淡出等过渡动画效果。

### 2026-05-09: Laravel 错误页面集成与错误处理 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 创建与 Laravel 错误码集成的错误页面（404、403、500）。
- **Rationale:**
  - 保持网站视觉一致性，错误页面需要与整体设计风格统一。
  - 提供用户友好的错误提示和导航选项。
  - 利用 Laravel 的错误处理机制返回正确的 HTTP 状态码。
- **Status:**
  - 创建 `resources/views/errors/403.blade.php`、`404.blade.php`、`500.blade.php`。
  - 创建 `resources/views/errors/layout.blade.php` 统一错误页面布局。
  - 在 `app/Exceptions/Handler.php` 中配置错误页面渲染。
  - 错误页面集成 SidebarMenu 导航和网站主题样式。

### 2026-05-09: 搜索功能集成与侧边栏增强 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 为侧边栏菜单添加搜索功能，支持全站内容搜索。
- **Rationale:**
  - 提升用户体验，提供快速导航和内容发现能力。
  - 搜索结果实时显示，支持键盘导航。
  - 搜索面板与侧边栏动画无缝集成。
- **Status:**
  - 创建 `SearchOverlay.vue` 搜索覆盖组件。
  - 集成到 SidebarMenu 的移动端视图。
  - 实现搜索结果的高亮显示。
  - 添加键盘快捷键支持（Escape 关闭）。

### 2026-05-09: 博客页面 (Blog.vue) 创建与内容展示 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 创建博客列表页面，支持分类筛选和响应式网格布局。
- **Rationale:**
  - 博客是网站的核心功能之一，需要美观的展示效果。
  - 支持多分类筛选，方便用户浏览不同类型内容。
  - 响应式设计确保在移动端也有良好体验。
- **Status:**
  - 创建 `resources/js/Pages/Blog.vue` 博客列表页。
  - 实现网格布局的响应式切换（1列/2列/4列）。
  - 添加分类标签筛选功能。
  - 集成 `BlogPost.vue` 组件展示单篇文章。

### 2026-05-09: 作者页面 (Author.vue) 与 GitHub 热力图 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 创建作者介绍页面，集成 GitHub 贡献热力图展示。
- **Rationale:**
  - 展示作者个人品牌和开源贡献记录。
  - GitHub 热力图是开发者展示活跃度的重要方式。
  - 与网站整体风格保持一致的设计语言。
- **Status:**
  - 创建 `resources/js/Pages/Author.vue` 作者页面。
  - 集成 vue-activity-calendar GitHub 热力图组件。
  - 展示 GitHub 统计数据（Commits、PRs、Repos）。
  - 添加技能展示和项目进度追踪模块。
  - 页面布局包含宣言、技能、项目和 CTA 等多个区块。

### 2026-05-10: 前端架构转型：Vue 3 SPA + Vue Router (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 从 Blade + Vue 局部增强模式转型为 Vue 3 SPA 架构，引入 Vue Router 进行客户端路由。
- **Rationale:**
  - 统一前端技术栈，采用 Inertia.js 风格的 SPA 架构。
  - 通过 Vue Router 实现客户端路由，提升页面切换体验。
  - 创建可复用的全局组件（SidebarMenu、Footer、SearchOverlay、SettingsPanel）。
- **Status:**
  - 安装 Vue Router、Vue I18n、@vueuse/motion 等核心依赖。
  - 配置 `resources/js/app.js` 为 Vue Router 入口。
  - 创建 Pages 目录结构（Home.vue、Blog.vue、Author.vue 等）。
  - 创建 Components 目录结构（SidebarMenu.vue、Footer.vue、SearchOverlay.vue 等）。

### 2026-05-10: 热力图库更换与颜色系统重构 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 将 vue-activity-calendar 更换为 vue3-calendar-heatmap，并重构颜色渐变系统。
- **Rationale:**
  - vue-activity-calendar 与项目兼容性不佳，改用更轻量的 vue3-calendar-heatmap。
  - 用户要求热力图颜色跟随网站主题色动态变化。
  - 0 贡献天数需要透明显示，有贡献的天数使用主题色渐变。
- **Status:**
  - 安装 vue3-calendar-heatmap 和 tippy.js 依赖。
  - 重构 rangeColor 为 computed 属性，动态读取 `currentTheme.value.color`。
  - 调整颜色梯度：0 贡献显示 `#e8e8e8` 浅灰，1-5 级使用主题色 + 不同灰度偏移。
  - 修复 mock 数据生成逻辑，使用 `count: null` 表示 0 贡献。
  - 切换为 GitHub 官方绿色系配色测试后，最终回退到主题色方案。

### 2026-05-10: 热力图颜色优化与 6 级渐变 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 优化热力图颜色梯度，解决最高贡献显示过黑的问题，并添加第 6 级颜色。
- **Rationale:**
  - 用户反馈最高贡献显示为纯黑色不好看，需要调整颜色算法。
  - vue3-calendar-heatmap 实际使用 6 级颜色索引（0-5），需要确保每级都有正确的颜色映射。
- **Status:**
  - 最初采用 RGB 偏移算法，导致高贡献级别颜色过深偏黑。
  - 改为透明度渐变方案：`15% → 30% → 50% → 75% → 100%`。
  - 确保最高级别颜色为主题色纯色，不会变黑。
  - 修复 Author.vue 模板标签闭合问题。

### 2026-05-10: 作者页面 (Author.vue) 侧边栏集成 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 为作者页面集成全局侧边栏菜单和页脚组件。
- **Rationale:**
  - 保持与 Blog.vue、Home.vue 等页面一致的导航体验。
  - 侧边栏提供统一的全局导航和设置入口。
- **Status:**
  - 在 Author.vue 中导入并集成 SidebarMenu 和 Footer 组件。
  - 使用 `ml-16` wrapper 为固定侧边栏（64px）留出空间。
  - 添加 `isFooterVisible` 状态管理页脚显示/隐藏。
  - 修复模板标签闭合问题，确保 Vue 编译通过。

### 2026-05-10: 启动页 (SplashScreen) 集成到首页流程 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 将启动页逻辑从独立路由 `/welcome` 迁移到首页 `/`，实现访问首页时先显示启动动画。
- **Rationale:**
  - 用户希望点击首页时能够看到启动页动画，而不是单独打开页面。
  - 将 SplashScreen 组件集成到 Home.vue，首次访问时显示动画，之后直接显示内容。
  - 使用 sessionStorage 标记首次访问状态，避免刷新页面重复播放动画。
- **Status:**
  - 修改路由配置，移除 `/welcome` 路由，`/` 直接指向 Home。
  - 在 Home.vue 中集成 SplashScreen 组件。
  - 添加 `showSplash` 和 `showContent` 状态控制启动动画和内容显示。
  - 使用 sessionStorage 存储 `splash_shown` 标记，避免重复播放。
  - Welcome.vue 文件保留备用。

### 2026-05-10: 全局滚动条样式隐藏 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 隐藏全局滚动条，实现更纯净的视觉体验。
- **Rationale:**
  - 用户反馈全局滚动条影响美观，需要隐藏。
  - 使用 CSS 同时兼容 Chrome、Safari、Firefox、IE/Edge 等主流浏览器。
- **Status:**
  - 在 `resources/css/app.css` 中添加 `::-webkit-scrollbar { display: none; }` 等规则。
  - 添加 `scrollbar-width: none` 和 `-ms-overflow-style: none` 实现跨浏览器兼容。

### 2026-05-12: 代码高亮系统重构与 PrismJS 深度集成 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 废弃原有的简单代码块渲染，引入 PrismJS 并通过 `vite-plugin-prismjs` 实现自动化管理。
- **Rationale:**
  - **Professional Highlighting:** PrismJS 提供更精准的语法解析和更丰富的主题支持（选用 Okaidia）。
  - **Automation:** 引入 `vite-plugin-prismjs` 插件，实现语言包的按需自动加载，避免手动导入 20+ 个语言文件的冗余。
  - **Vue SFC Support:** 针对 Vue 单文件组件，手动扩展了 PrismJS 的 `markup` 语言定义，支持 `<script>` 和 `<style>` 块的嵌套高亮。
  - **Clean Architecture:** 移除了自定义的 `code-block-wrapper` 等冗余 DOM 结构，回归 PrismJS 原生渲染模式，确保样式表现的一致性。
- **Status:**
  - 安装并配置 `vite-plugin-prismjs` 插件。
  - 重构 `MarkdownRenderer.vue`，简化导入逻辑并修复 `escapeHtml` 导致的二次转义问题。
  - 优化代码块 CSS 样式：采用 JetBrains Mono 字体、1.6 行高、圆角边框及横向滚动支持。
  - 修复了 `prism-markup-templating` 依赖缺失导致的 PHP 等语言高亮失效问题。

### 2026-05-12: 代码块增强与页面布局优化 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 增强代码块功能并优化文章详情页布局。
- **Rationale:**
  - 完善代码块用户体验：行号显示、复制功能、语言标签，使用 Prism 官方插件实现。
  - 优化文章阅读体验：增大主文章区域，缩小左右侧边栏，提升内容可读性。
  - 改进国际化覆盖：完善 Footer 组件和管理后台的语言支持。
- **Status:**
  - 集成 Prism 插件：`line-numbers`（行号）、`show-language`（语言标签）、`copy-to-clipboard`（复制功能）、`toolbar`（工具栏）。
  - 使用 Okaidia 主题，移除自定义样式，完全使用 Prism 官方默认样式。
  - 调整代码字体大小从 `0.875rem` 到 `1rem`。
  - 文章详情页布局优化：左右侧边栏从 `col-span-3` 减到 `col-span-2`，主内容从 `col-span-6` 增到 `col-span-8`，最大宽度从 `max-w-2xl` 增到 `max-w-4xl`。
  - 语言文件新增：作者页面相关翻译（`role_designation`、`architect_designer`、`manifesto`、`skill_*`）、Footer 组件翻译（`footer_tagline`、`footer_categories`、`footer_theory`、`footer_design`、`footer_blog`、`footer_about`、`footer_data`、`footer_rss`、`footer_api`）。
  - 修复菜单组件：将 `nav_about` 改为 `nav_author`，与语言文件保持一致。
  - 重构 Footer 组件：使用 `useI18n`，将所有硬编码文本替换为翻译调用。
  - 管理后台完善：添加 `useI18n` 支持，更新统计标签和菜单项为翻译调用。
  - i18n 配置优化：添加命名导出 `{ i18n }`，同时支持默认导出和命名导出。
  - 浏览器语言检测优化：支持完整语言匹配 → 基础语言匹配 → 默认英文的降级策略，增加 `SUPPORTED_LOCALES` 常量定义。

### 2026-05-13: 管理后台登录系统实现 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 实现管理后台登录功能，保护管理后台入口。
- **Rationale:**
  - 确保管理后台安全访问，防止未授权访问。
  - 实现完整的登录流程：登录页面 → 验证 → 管理后台首页。
  - 支持本地存储登录状态，避免重复登录。
- **Status:**
  - 创建 `Login.vue` 登录页面，包含邮箱和密码输入框。
  - 登录凭据：邮箱 `Archyx@admin.com`，密码 `Archyx_admin123`。
  - 添加路由配置：`/admin/login` 登录页，`/admin` 管理后台页（需要认证）。
  - 实现路由守卫：检查 `requiresAuth` 路由元信息，未登录用户自动跳转到登录页。
  - 已登录用户访问登录页自动跳转到管理后台首页。
  - 管理后台首页添加用户信息显示和登出按钮。
  - 登录状态存储在 localStorage：`admin_logged_in`（登录标记）、`admin_email`（邮箱）、`admin_login_time`（登录时间）。
  - 登出功能：清除 localStorage 中的登录信息，跳转到登录页。

### 2026-05-13: 管理后台完整功能建设 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 扩展管理后台功能，新增分类、标签、评论、角色、媒体库管理页面，并将数据分析整合到控制台首页。
- **Rationale:**
  - 完善管理后台的内容管理能力，覆盖博客系统的完整运营需求。
  - 将数据分析整合到控制台首页，提供一站式的数据概览体验。
  - 保持界面设计的一致性和操作的便捷性。
- **Status:**
  - 新增页面：
    - `AdminCategories.vue` - 分类管理（增删改查、状态切换）
    - `AdminTags.vue` - 标签管理（增删改查、使用统计）
    - `AdminComments.vue` - 评论管理（审核、回复、删除）
    - `AdminRoles.vue` - 角色管理（权限配置、用户统计）
    - `AdminMedia.vue` - 媒体库（文件上传、预览、删除、批量操作）
  - 更新路由配置：新增 `/admin/categories`、`/admin/tags`、`/admin/comments`、`/admin/roles`、`/admin/media` 路由。
  - 更新国际化翻译：新增 EN/ZH/ZH-TW 三种语言的管理后台翻译。
  - 更新侧边栏菜单：添加所有新页面的导航项。
  - 整合数据分析到控制台：在 `Index.vue` 中集成流量趋势图表、内容统计、热门页面、流量来源分析模块。
  - 移除独立的 `AdminAnalytics.vue` 页面，避免功能重复。
  - 最终管理后台包含 13 个功能模块：控制台、文章、视频、项目、资源、分类、标签、评论、用户、角色、媒体库、系统设置。

### 2026-05-13: 管理后台浅色模式支持 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 为管理后台添加浅色模式（Light Mode）支持，提供明暗主题切换功能。
- **Rationale:**
  - 满足不同用户的视觉偏好，减轻长时间使用的视觉疲劳。
  - 主题状态持久化存储到 localStorage，刷新页面后保持用户选择。
  - 保持界面设计的一致性和操作的便捷性。
- **Status:**
  - 在 `AdminLayout.vue` 中添加主题切换按钮（太阳/月亮图标）。
  - 实现主题状态管理：`isDarkMode` 响应式状态 + localStorage 持久化。
  - 在 `app.css` 中添加浅色模式 CSS 变量和样式类。
  - 更新所有管理后台布局元素的条件样式（header、sidebar、content）。
  - 添加国际化翻译支持（EN/ZH/ZH-TW）。

### 2026-05-13: 设置页面图标与开关样式优化 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 修复设置页面的图标颜色和开关样式，确保在浅色/深色模式下正确显示。
- **Rationale:**
  - 保存按钮图标和 Tab 选中状态图标未正确显示为白色，影响视觉体验。
  - 开关关闭状态背景色未跟随主题模式切换，导致浅色模式下仍显示深色灰色。
- **Status:**
  - 修复 `AdminSettings.vue` 保存按钮图标：使用内联样式 `:style="{ color: '#ffffff' }"` 强制设置为白色。
  - 修复 Tab 选中状态图标：使用条件内联样式 `:style="activeTab === tab.id ? { color: '#ffffff' } : {}"`。
  - 修复所有开关关闭状态背景色：使用动态类 `(isDarkMode ? 'bg-gray-600' : 'bg-gray-400')`。
  - 修复开关标签文字颜色：使用动态类 `(isDarkMode ? 'text-gray-300' : 'text-gray-700')`。

### 2026-05-14: 管理后台首页国际化完善 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 修复管理后台首页的中英文切换问题，确保所有界面元素支持多语言显示。
- **Rationale:**
  - 首页统计卡片的翻译在组件初始化时计算，不会随语言切换而更新。
  - 建议模块的数据包含硬编码英文文本，需要改为翻译键方式。
- **Status:**
  - 将 `Index.vue` 中的 `stats` 数组改为 `computed` 属性，确保语言切换时自动更新。
  - 修改 `admin.js` 数据文件，将 `designRecommendations` 的 `title` 和 `description` 改为 `titleKey` 和 `descKey` 翻译键。
  - 补充三种语言文件（en.json, zh.json, zh-TW.json）的翻译键：
    - chart_ 系列：图表相关翻译（chart_posts, chart_views, chart_total_users 等）
    - rec_ 系列：建议相关翻译（rec_theory_title, rec_theory_desc 等）
  - 修改 Index.vue 模板使用 `t(rec.titleKey)` 和 `t(rec.descKey)` 调用翻译。

### 2026-05-14: 管理后台用户下拉菜单与退出确认 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 将右上角用户信息改为可点击的下拉菜单，并添加退出登录确认弹窗。
- **Rationale:**
  - 提供更清晰的用户操作入口，整合主题切换和退出登录功能。
  - 退出登录需要二次确认，防止误操作。
- **Status:**
  - 移除 AdminLayout 头部左侧的主题切换按钮，仅保留在用户下拉菜单中。
  - 实现用户下拉菜单：
    - 点击头像区域弹出菜单，包含用户信息、主题切换、退出登录。
    - 添加点击外部关闭菜单功能。
    - 添加下拉动画效果（淡入淡出 + 向上滑入）。
  - 实现退出登录确认弹窗：
    - 弹窗包含标题、提示文本、取消按钮和确认退出按钮。
    - 确认退出按钮默认黑色背景，悬停时变为主题红色。
    - 添加弹窗动画（淡入淡出 + 缩放）。
    - 修复路由名称错误（`admin.login` → `admin-login`）。
    - 修复 localStorage 键名不匹配问题（`admin_token` → `admin_logged_in`）。

### 2026-05-14: 管理后台侧边栏优化 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 优化侧边栏布局和交互体验。
- **Rationale:**
  - 收起/展开按钮位置需要更符合用户习惯，放在底部更合理。
  - 移动端不需要收起/展开功能，应隐藏以节省空间。
  - 菜单项悬停样式需要更突出，使用主题色增强视觉反馈。
- **Status:**
  - 将收起/展开按钮移到侧边栏底部，添加弹性空间将其推到底部。
  - 移动端隐藏收起/展开功能（使用 `hidden lg:block` 类）。
  - 修改菜单项悬停样式：
    - 默认状态：深色模式灰色文字，浅色模式深灰色文字。
    - 悬停状态：主题红色背景，白色文字。

### 2026-05-14: 管理后台样式与交互全面优化 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 优化侧边栏样式、菜单层级结构、用户详情视图。
- **Rationale:**
  - 收起/展开按钮位置需要更符合用户习惯，放在底部更合理。
  - 移动端不需要收起/展开功能，应隐藏以节省空间。
  - 菜单需要层级结构，将文章/视频/项目/资源归类为"页面管理"，设置/关于归类为"系统设置"。
  - 用户管理需要支持查看详情功能，增强用户体验。
  - 子菜单样式应与一级菜单保持一致，确保界面统一性。
- **Status:**
  - **侧边栏样式优化**：
    - 修改边框样式，仅在侧边栏展开时显示右边框（移除 `border-r` 始终显示）。
    - 移除收起按钮上方的分隔线（删除 `border-t` 类）。
  - **菜单结构重构**：
    - 新增一级菜单"页面管理"（CONTENT）：包含文章、视频、项目、资源管理。
    - 新增一级菜单"系统设置"（SYSTEM）：包含设置、关于页面。
    - 修复子菜单 `label` 未翻译问题，修改 `menuItems` 计算逻辑递归处理子菜单。
    - 统一子菜单高度与一级菜单一致（`py-2` → `py-3`）。
    - 统一子菜单样式与一级菜单（文字大小 `text-xs` → `text-sm`、hover样式、图标颜色 `text-gray-500/text-gray-400` → `text-gray-400/text-gray-600`）。
    - 优化选中逻辑：仅直接访问父菜单时显示选中样式，子菜单选中时父菜单不显示选中样式（移除 `isAnyChildActive` 函数）。
  - **用户详情视图**：
    - 点击用户管理表格中的眼睛图标，弹出模态框显示用户详情。
    - 详情包含：头像、名称、邮箱、角色标签、状态、加入日期。
    - 支持关闭和编辑操作。
  - **翻译文件更新**：
    - 新增 `admin_close`（关闭/Close/關閉）。
    - 新增 `admin_content`（页面管理/CONTENT/頁面管理）。
    - 新增 `admin_system`（系统设置/SYSTEM/系統設定）。

### 2026-05-15: 角色表单权限关联修复 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 修复 RoleForm.vue 中权限数据获取逻辑，从分离的 role_permissions.js 关联表获取角色权限。
- **Rationale:**
  - 角色数据已移除内嵌权限配置，权限通过关联表管理。
  - 原代码直接访问 `props.editData.permissions` 导致 TypeError。
- **Status:**
  - 在 RoleForm.vue 中导入 `getPermissionIdsByRoleId` 和 `permissions`。
  - 添加 `getRolePermissionNames()` 辅助函数，通过角色ID获取权限名称列表。
  - 修改 watch 逻辑，使用新函数初始化表单权限数据。

### 2026-05-15: 后台页面公共引入抽离 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 创建 `useAdminImports.js` 公共引入模块，抽离后台页面重复的 import 内容。
- **Rationale:**
  - 后台页面存在大量重复的 Vue API、图标、工具函数和组件导入。
  - 统一管理便于维护，减少代码重复。
- **Status:**
  - 创建 `resources/js/composables/useAdminImports.js`，包含：
    - Vue API（ref, computed, watch, useRouter, useI18n）
    - Lucide 图标（30+ 常用图标）
    - Composables（useTheme）
    - 工具函数（formatToShort, getCategoryLabel 等）
    - 数据文件（adminUsers, adminRoles, permissions, adminCategories, adminTags, adminLogs）
    - 组件（ConfirmDialog, ContentForm, RoleForm, UserForm, MetaForm, TagInput）
  - 更新 9 个后台页面使用公共引入：
    - AdminPosts.vue、AdminUsers.vue、AdminRoles.vue、AdminCategories.vue
    - AdminTags.vue、AdminLogs.vue、AdminMedia.vue、AdminAnalytics.vue、AdminSettings.vue
  - 修复导入不存在组件的问题，移除 CategoryForm、TagForm 等不存在的组件导出。

### 2026-05-16: 媒体库深度增强与全局组件应用 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 升级媒体库为包含文档预览能力的完整管理模块，并应用全局确认组件。
- **Rationale:**
  - `AdminSettings` 与 `AdminFrontMenu` 的敏感操作缺乏安全确认，引入统一的 `ConfirmDialog` 拦截。
  - 原版媒体库仅支持基础的图片展示，需扩展高级文件类型（如 PDF, Docx）的原生无缝预览。
- **Status:**
  - 安装依赖 `vue-pdf-embed` 与 `docx-preview` 以支持多格式。
  - 创建 `MediaPreviewModal.vue` 实现带毛玻璃特效的全屏媒体预览。
  - 大量使用 Optional Chaining (`?.`) 修复因为文件异步加载导致的 Null Pointer 异常。

### 2026-05-16: 确立全栈对接契约与 AI-FIRST 数据库蓝图 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 编写《前后端对接与同步指南》(INTEGRATION_GUIDE.md) 及《核心数据库设计》(AI-FIRST/database.md)。
- **Rationale:**
  - 随着前端 Mock 开发接近尾声，必须制定平滑过渡到 Laravel 后端 API 的战略，以兼顾未来的 App 开发。
  - 决定在后台管理中采用 "Web 会话直连模式"，复用现有的 Session 认证，简化全栈开发难度。
- **Status:**
  - 产出 `INTEGRATION_GUIDE.md` 明确了 P0~P2 的对接任务优先级与迁移路径。
  - 在 `AI-FIRST` 目录下生成权威版 `database.md`，统合了 `media`、`menus`、`settings` 等缺失的数据模型与 `roles` 枚举规范。

### 2026-05-16: 后台组件命名空间清理 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 批量移除 `resources/js/Pages/admin/` 目录下 19 个 Vue 组件的 `Admin` 前缀。
- **Rationale:**
  - 既然文件已经放置在 `admin` 目录中，再添加 `Admin` 前缀属于冗余命名，且违背了极致极简主义（Extreme Minimalism）。
- **Status:**
  - 使用批量重命名脚本将 `AdminPosts.vue` 转为 `Posts.vue`，以此类推。
  - 同步重构 `router.js` 与 `App.vue` 中的导入路径（为避免与前台页面重名，保持了 Router 中的对象变量名为 `AdminX`，仅改变导入源文件）。

### 2026-05-17: 管理后台仪表盘页面与本地化实现 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 实现分类管理、广告管理、内容管理等后台仪表盘页面，并完善国际化支持。
- **Rationale:**
  - 扩展管理后台的内容管理维度，覆盖广告位配置、分类层级管理等运营需求。
  - 确保所有新增页面支持 EN/ZH/ZH-TW 三语切换。
- **Status:**
  - 新增/重构分类管理页面，支持层级展示与状态筛选。
  - 新增广告管理页面，支持广告位配置与投放状态管理。
  - 完善内容管理相关页面的国际化翻译。

### 2026-05-17: 菜单管理系统与动态配置页面 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 实现前台/后台菜单管理系统，支持动态配置与拖拽排序。
- **Rationale:**
  - 将菜单配置从硬编码迁移到可管理的动态配置系统。
  - 支持前台菜单与后台菜单分别管理，提供可视化编辑体验。
- **Status:**
  - 创建 `FrontMenu.vue` 菜单节点管理页面。
  - 实现前台/后台菜单 Tab 切换。
  - 支持菜单项的增删改查、拖拽排序、启用/禁用状态切换。
  - 菜单标签支持国际化 `label_key` 映射。

### 2026-05-18: 项目数据重构与功能优化 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 重构项目数据层，优化各管理页面的数据流与交互体验。
- **Rationale:**
  - 统一各管理页面的数据获取与状态管理模式。
  - 优化页面加载性能与用户交互反馈。
- **Status:**
  - 重构项目、视频、资源等页面的数据层。
  - 优化表单验证与错误提示逻辑。
  - 统一各页面的搜索筛选交互模式。

### 2026-05-19: 通用搜索筛选组件设计与全站应用 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 创建 `AdminSearchFilter.vue` 通用搜索筛选组件，并将所有管理页面（除广告管理外）统一改用该组件。
- **Rationale:**
  - 消除各页面搜索筛选实现的重复代码，提升代码复用率。
  - 统一管理后台的搜索筛选交互体验与视觉风格。
  - 支持多种筛选类型：下拉选择、Checkbox 切换。
- **Status:**
  - 创建 `AdminSearchFilter.vue` 通用组件，支持：
    - 搜索输入框（v-model 双向绑定）
    - 下拉筛选（多筛选器支持，自定义选项）
    - Checkbox 筛选（用于显示/隐藏类开关）
    - 响应式布局（移动端自动堆叠）
    - 暗色/亮色主题自适应
  - 改造 10 个管理页面使用通用组件：
    - `Categories.vue` - 状态筛选
    - `Tags.vue` - 状态筛选
    - `Comments.vue` - 状态筛选
    - `Roles.vue` - 仅搜索
    - `Logs.vue` - 模块 + 动作双筛选
    - `SocialLinks.vue` - 平台筛选
    - `Media.vue` - 文件类型筛选
    - `Restore.vue` - 备份类型筛选
    - `Backup.vue` - 备份类型筛选
    - `FrontMenu.vue` - Checkbox 显示/隐藏禁用
  - 统一各页面头部布局：标题在左，添加按钮在右。
  - 构建验证通过，无编译错误。

### 2026-05-19: AdminSearchFilter 组件 Bug 修复 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 修复 `AdminSearchFilter.vue` 中 `filterValues` 未通过 `props` 访问的引用错误。
- **Rationale:**
  - 在扩展 Checkbox 类型筛选时，`getOptionLabel` 函数中直接使用了 `filterValues` 而非 `props.filterValues`，导致运行时 `ReferenceError`。
- **Status:**
  - 修复 `getOptionLabel` 函数中的变量引用，改为 `props.filterValues`。
  - 确保组件在 Vue 3 `<script setup>` 语法下正确访问 props。

### 2026-05-20: 页面 SEO 管理重构与全局 SEO 分离 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 将全局 SEO 设置从 `SeoManager.vue` 中移除，使页面 SEO 管理专注于页面级配置，并重构为卡片式网格布局。
- **Rationale:**
  - **关注点分离：** 全局 SEO 配置与页面 SEO 配置属于不同层级的设置，混在一起会造成概念混淆和操作不便。
  - **视觉统一：** 采用与其他后台页面（如 Subscribers）一致的卡片式网格布局，配合深色/浅色模式适配，提升视觉层次感。
- **Status:**
  - 移除 `SeoManager.vue` 中所有全局 SEO 相关模板、脚本和样式：`startEditGlobal`、`cancelEditGlobal`、`saveEditGlobal` 等函数。
  - 重构页面列表为网格卡片布局，使用 `grid-cols-3` 响应式网格。
  - 实现 `getRouteColor` 函数，为不同路由类型（post、blog、author 等）提供独特的颜色标识。
  - 卡片内联编辑功能：点击编辑按钮展开表单，支持标题、描述、关键词、Schema 类型四个字段。
  - 活动/禁用状态切换按钮，带绿色/灰色状态指示。
  - 恢复 `Search` 图标导入（模板中使用了但导入被误删）。

### 2026-05-20: 搜索功能统一为公共组件 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 将页脚管理（SocialLinks.vue）和页面 SEO 管理（SeoManager.vue）的搜索功能统一替换为 `AdminSearchFilter` 公共组件。
- **Rationale:**
  - **代码复用：** 避免在每个页面重复实现搜索逻辑，减少维护成本。
  - **体验一致：** 统一的搜索组件提供一致的交互体验和视觉风格。
  - **功能增强：** `AdminSearchFilter` 支持搜索 + 下拉筛选，功能更强大。
- **Status:**
  - `SocialLinks.vue`：移除自定义搜索框，集成 `AdminSearchFilter`，搜索框移至页面头部下方独立一行。
  - `SeoManager.vue`：移除自定义搜索框，集成 `AdminSearchFilter`，搜索框移至页面头部右侧。
  - 两个页面的搜索均支持实时过滤，无需额外分页重置逻辑。

### 2026-05-20: 页脚管理（SocialLinks）样式全面重构 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 对 `SocialLinks.vue` 进行多轮样式重构，使其与 Subscribers、Advertisements 等后台页面保持完全一致的视觉风格。
- **Rationale:**
  - 用户多次反馈样式不一致、布局混乱、视觉丑陋，需要从根本上统一设计语言。
  - 参照 Subscribers.vue 的布局模式，采用相同的 padding、字体、间距、按钮样式、边框处理等。
- **Status:**
  - **页面头部：** 图标 + 标题 + 副标题在左，保存按钮在右。保存按钮使用 `font-black text-xs uppercase tracking-[0.2em]` + `px-8 py-4`。
  - **搜索区域：** `AdminSearchFilter` 独立一行，位于头部与选项卡之间。
  - **选项卡（Tabs）：** 按钮字体统一为 `font-black text-xs uppercase tracking-[0.2em]`，添加按钮集成在 tab 栏右侧。
  - **卡片：** 去除花哨的 `backdrop-blur`、渐变阴影、背景装饰图标、`group-hover` 复杂动画。改为简洁的 `border` + `rounded-lg` + `hover:-translate-y-1` + `hover:shadow-lg`。
  - **卡片内部：** 图标容器 `w-12 h-12 rounded-lg`，标题 `text-lg font-bold`，底部边框使用统一的 `border-gray-200/700`。
  - **模态框：** 统一为 `rounded-lg shadow-xl`，输入框 `px-3 py-2`，按钮风格与其他页面一致。
  - **修复 bug：** 非社交链接选项卡卡片中的图标引用 `tab.icon` 变量作用域错误（tab 不在 v-for 作用域内），改为直接使用 `Navigation` 组件。

### 2026-05-20: 页面 SEO 管理样式对齐 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 调整 `SeoManager.vue` 样式细节，使其与其他后台页面保持完全一致。
- **Rationale:**
  - 移除 `space-y-8` 冗余包裹层。
  - 统一搜索框位置到页面头部右侧。
  - 修复无效的 `</div>` 结束标签导致的 Vite 编译错误。
- **Status:**
  - 模板结构优化：移除多余的 `</div>` 标签，修复 `[plugin:vite:vue] Invalid end tag` 错误。
  - 搜索框集成 AdminSearchFilter 并移至头部右侧。
  - 卡片样式与 SocialLinks 保持一致的 `border`、`rounded-lg`、`hover:-translate-y-1` 风格。

### 2026-05-20: 动态路由注册机制 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 实现前后台动态路由注册，新增或修改菜单项无需手动修改 `router.js`。
- **Rationale:**
  - **后台：** 从 `data/menu.js` 读取菜单配置，配合 `componentRegistry.js` 自动解析组件路径，动态生成路由。
  - **前台：** 同理，前台页面也能通过 `data/menu.js` 中的 `frontMenuItems` 动态注册路由。
  - **解耦：** 菜单配置与路由定义分离，新增页面只需添加菜单配置和组件文件，无需修改路由文件。
- **Status:**
  - **后台动态路由：** 遍历 `adminMenuItems`，通过 `getComponentPath()` 自动解析组件路径，`generateRouteName()` 生成 kebab-case 路由名称。
  - **前台动态路由：** 遍历 `frontMenuItems`，同样使用组件注册表自动匹配组件。
  - **静态兜底路由：** 保留手动定义的关键路由（如 `/admin/login`、`/admin/users/:id`）作为兜底。
  - **路由顺序修复：** 将 `/admin/login` 路由放在 `/admin` 之前，确保登录页面优先匹配。
  - **带参数路由：** 手动添加 `/admin/users/:id` 路由（`name: 'admin-user-detail'`），解决动态路由无法处理参数的问题。
  - **路由名称修复：** 修复 `generateRouteName` 函数中错误的 camelCase 转换，保持 kebab-case 格式（如 `admin-users` 而非 `adminUsers`）。

### 2026-05-20: settings.js 清理与优化 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 移除 `data/settings.js` 中无用的 `appearance` 配置，精简代码。
- **Rationale:**
  - `appearance` 对象（theme、fontSize、fontFamily、layoutDensity）未被任何组件引用，属于遗留代码。
  - 保持代码库整洁，遵循 "只保留有用代码" 的原则。
- **Status:**
  - 从 `defaultSettings` 中删除 `appearance` 对象。
  - 保留 `notifications` 和 `performance` 两个实际使用的配置模块。

### 2026-05-21: Author.vue GitHub API 404 修复 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 修复 Author.vue 中 GitHub 数据获取 404 错误。
- **Rationale:**
  - `author_profiles.js` 中的 GitHub 用户名与实际不符，导致 `https://api.github.com/users/adlerdecht` 返回 404。
  - GitHub 热力图和统计数据是作者页面的核心展示内容，必须正确获取。
- **Status:**
  - 检查 `author_profiles.js` 中的 GitHub 链接配置。
  - 调整为正确的 GitHub 用户名。
  - 修复 `fetchGitHubData` 错误处理逻辑，提供更清晰的错误提示。

### 2026-05-21: 管理后台搜索与筛选组件统一改造 (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 为所有管理后台页面统一引入 `AdminSearchFilter` 公共搜索组件，并改造为表格布局。
- **Rationale:**
  - 替换各页面零散的自定义搜索实现，统一交互体验。
  - 表格布局更符合后台管理场景的数据展示需求。
- **Status:**
  - `Subscribers.vue`：集成 AdminSearchFilter，改造为表格布局，包含邮箱、状态、订阅日期、操作列。
  - `Advertisements.vue`：集成 AdminSearchFilter，改造为表格布局，包含标题、位置、状态、展示次数、操作列。
  - 搜索和分页完美配合，搜索时自动重置页码。
  - 构建验证通过，无编译错误。
