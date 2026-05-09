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
