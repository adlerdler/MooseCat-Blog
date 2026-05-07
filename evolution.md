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
  - 通过 AI 治理文件，确立了 Agentic 开发的“宪法”，确保 AI 在开发过程中能自主理解愿景、规范和决策路径，减少沟通损耗。
- **Status:** 已完成基础环境搭建，集成 Tailwind CSS，并为 AI 助手配置了完整的运行上下文。

### 2026-05-08: 品牌视觉重塑：从 MooseCat 到 Archyx (by Trae)
- **Developer:** Trae (AI)
- **Decision:** 将项目名称由 MooseCat 变更为 **Archyx**，并全面重写 README.md 视觉文档。
- **Rationale:** 
  - **Archyx** (Arch + Onyx/Modern) 更能体现架构的稳重与现代审美的碰撞。
  - 引入 Shields.io 徽章、Unsplash 视觉占位图和 Mermaid 架构图，使文档从“纯文本”进化为“可视化资产”，符合专业开源项目标准。
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

### 2026-05-08: 架构大转向：废弃 Inertia，确立“混合局部增强”模式 (by Human & Trae)
- **Developer:** Human (adlerdler) 决策 / Trae (AI) 执行
- **Decision:** 彻底移除 Inertia.js 相关组件与中间件，回归 **Blade + Vue 3 局部增强** 模式。
- **Rationale:** 
  - **Human Insight:** 人类导师指出 Inertia 在当前环境下配置冗余且容易导致白屏，直接使用极简 HTML 容器挂载 Vue 更为直观。
  - **Minimalism:** 混合模式让前台保持 100% SEO 友好，仅在编辑器、评论等复杂交互点“按需注入”Vue。
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
