# ## AI-Agentic Workflow (Archyx Edition)

本项目的自动化核心基于 **Trae/Claude** 与本地开发流。以下是一个任务从输入到落地的完整生命周期。

### ### Stage 01: Task Ingestion (需求摄取)
*   **Trigger:** 用户在对话框输入需求或 Issue。
*   **Action:** 自动分析 Laravel/Vue 关联性，分配 `agents.md` 角色。
*   **Output:** 生成 `TODO` 列表并挂载到当前对话上下文。

### ### Stage 02: Iterative Execution (迭代执行)
1.  **Drafting:** `Coder-Agent` 根据 `CLAUDE.md` 规范生成 Laravel 后端与 Vue 前端代码。
2.  **Hybrid Rendering:** 在 Web 端路由中，优先返回 Blade 视图并将数据注入 Vue 组件。
3.  **Self-Correction:** AI 会进行本地环境运行检查。若报错，则自动修正。
4.  **Refining:** 优化 UI，确保符合 Glassmorphism 风格。

### ### Stage 03: The Skeptic's Audit (怀疑论审计)
*   **Agent:** `Auditor-Agent`。
*   **Logic:** AI 会自我模拟三种失败场景：
    *   *Scenario A:* 数据库查询性能瓶颈（如 N+1）。
    *   *Scenario B:* Vue 组件 Props 验证缺失。
    *   *Scenario C:* 逻辑冗余（违反极简主义）。

### ### Stage 04: Human-in-the-loop (人工终审)
*   人类导师审核 AI 提供的代码变更。
*   `Accept` -> 代码正式生效。
*   `Reject` -> AI 根据反馈意见重新执行 Stage 02。

---

### ### Technical Stack for Workflow
*   **Orchestration:** Trae / Claude 3.5 Sonnet.
*   **Environment:** PHP 8.2+ / Node.js 18+ / Laravel 11 / Vue 3.