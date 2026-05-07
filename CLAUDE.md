# ## Project Alignment: Archyx Blog System (Archyx 博客系统)

### ### 01. Context & Vision
*   **Mission:** 构建一个面向开发者、极致极简且由 AI 驱动的现代化内容创作与分发系统。
*   **Aesthetics:** 严格遵循 **Extreme Minimalism**。UI 必须采用 **Glassmorphism**（毛玻璃）风格，保持白净、高透、低信息密度的视觉链路。

### ### 02. Tech Stack Protocol
*   **Infrastructure:** PHP 8.2+ / Laravel 11 (精简模式) / MySQL.
*   **Frontend:** Blade + Vue 3 (局部增强模式).
*   **CSS:** Tailwind CSS.
*   **Build:** Vite + laravel-vite-plugin.
*   **Editor:** Vditor (Markdown).
*   **Core AI:** 深度集成 Trae/Claude 进行 Agentic 开发。

### ### 03. Architecture & Routing
*   **Front-facing (/):** 纯 Blade 渲染，无需认证，SEO 优先。
*   **Admin (/admin/*):** 独立 Controller，必须通过 `auth` 中件认证。
*   **Vue Boundary:**
    - **Blade Only:** 文章列表、详情、分类页。
    - **Vue Components:** 编辑器 (Vditor)、评论区、图片上传、搜索框。
    - **Blade Forms:** 标签管理、分类管理。

### ### 03. Development Constraints (Non-negotiable)
*   **Pragmatism First:** 禁止过度设计，任何功能模块（如：文章同步、视频嵌入）必须有明确的业务价值（ROI）。
*   **Clean Code:** 函数逻辑必须原子化。禁止在单一文件中堆砌超过 200 行代码（尤其是 Vue 组件与 Laravel Controller）。
*   **Verification:** 所有 AI 生成的代码必须经过静默验证。若存在逻辑悖论，优先触发 Skeptical Mode（质疑模式）重新审计。

### ### 04. AI Interaction Strategy
*   作为 AI 助手，你在本项目中不只是 Copilot，你是 **Primary Maintainer**。
*   修改任何底层架构（如：数据库 Schema、核心 Middleware）前，必须在 `evolution.md` 中记录决策逻辑。