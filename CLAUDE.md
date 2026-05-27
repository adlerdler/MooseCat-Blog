# ## Project Alignment: Archyx Blog System (Archyx 博客系统)

### ### 01. Context & Vision
*   **Mission:** 构建一个面向开发者、极致极简且由 AI 驱动的现代化内容创作与分发系统。
*   **Aesthetics:** 严格遵循 **Extreme Minimalism**。UI 必须采用 **Glassmorphism**（毛玻璃）风格，保持白净、高透、低信息密度的视觉链路。

### ### 02. Tech Stack Protocol
*   **Infrastructure:** PHP 8.2+ / Laravel 11.31 (精简模式) / MySQL.
*   **Frontend:** Vue 3 + Inertia.js (前后端分离SPA).
*   **CSS:** Tailwind CSS.
*   **Build:** Vite + laravel-vite-plugin.
*   **Editor:** Vditor (Markdown).
*   **Core AI:** 深度集成 Trae/Claude 进行 Agentic 开发。

### ### 03. Architecture & Routing
*   **Front-facing (/):** Inertia.js + Vue 3 SPA，SEO 友好。包含文章、视频、项目、资源、分类、标签页面。
*   **API (/api/v1/*):** RESTful 接口，支持移动端（Flutter）对接。覆盖 Posts、Comments、Videos、Projects、Resources、Categories、Tags、Users 8 个模块。
*   **Admin (/admin/*):** Inertia.js + Vue 3 SPA，独立 Controller，必须通过 `auth` 中间件认证。完整 CRUD 支持：Posts、Videos、Projects、Resources、Categories、Tags、Users、Roles、Journals、Settings、SocialLinks、Backups、Logs。

### ### 03.5. Current Development Progress
*   **Backend API:** ✅ 完整（8+ 模块）
*   **Web Controllers:** ✅ 完整（40+ 控制器）
*   **Admin Panel:** ✅ 完整（路由 + 页面组件都已就绪）
*   **Vue Components:** ✅ 已实现（所有页面组件已完成）
*   **Inertia.js:** ✅ 已完整集成（前台和后台都使用 Inertia）

### ### 04. Development Constraints (Non-negotiable)
*   **Pragmatism First:** 禁止过度设计，任何功能模块（如：文章同步、视频嵌入）必须有明确的业务价值（ROI）。
*   **Clean Code:** 函数逻辑必须原子化。禁止在单一文件中堆砌超过 200 行代码（尤其是 Vue 组件与 Laravel Controller）。
*   **Verification:** 所有 AI 生成的代码必须经过静默验证。若存在逻辑悖论，优先触发 Skeptical Mode（质疑模式）重新审计。

### ### 05. AI Interaction Strategy
*   作为 AI 助手，你在本项目中不只是 Copilot，你是 **Primary Maintainer**。
*   修改任何底层架构（如：数据库 Schema、核心 Middleware）前，必须在 `evolution.md` 中记录决策逻辑。
