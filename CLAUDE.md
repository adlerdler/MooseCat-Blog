# ## Project Alignment: Archyx Blog System (Archyx 博客系统)

### ### 01. Context & Vision
*   **Mission:** 构建一个面向开发者、极致极简且由 AI 驱动的现代化内容创作与分发系统。
*   **Aesthetics:** 严格遵循 **Extreme Minimalism**。UI 必须采用 **Glassmorphism**（毛玻璃）风格，保持白净、高透、低信息密度的视觉链路。

### ### 02. Tech Stack Protocol
*   **Infrastructure:** 本地开发基于 PHP 8.2+，生产环境支持标准 LEMP 栈或 Docker 部署。
*   **Backend:** Laravel 11 (精简模式) + Eloquent ORM + SQLite/MySQL。
*   **Frontend:** Vue 3 + Vite + Tailwind CSS (Inertia.js 暂未集成，目前为 API 模式)。
*   **Core AI:** 深度集成 Trae/Claude 进行 Agentic 开发。

### ### 03. Development Constraints (Non-negotiable)
*   **Pragmatism First:** 禁止过度设计，任何功能模块（如：文章同步、视频嵌入）必须有明确的业务价值（ROI）。
*   **Clean Code:** 函数逻辑必须原子化。禁止在单一文件中堆砌超过 200 行代码（尤其是 Vue 组件与 Laravel Controller）。
*   **Verification:** 所有 AI 生成的代码必须经过静默验证。若存在逻辑悖论，优先触发 Skeptical Mode（质疑模式）重新审计。

### ### 04. AI Interaction Strategy
*   作为 AI 助手，你在本项目中不只是 Copilot，你是 **Primary Maintainer**。
*   修改任何底层架构（如：数据库 Schema、核心 Middleware）前，必须在 `evolution.md` 中记录决策逻辑。