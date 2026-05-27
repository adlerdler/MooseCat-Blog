# ## Agentic Hierarchy & Roles (Archyx Edition)

### ### Role 01: The Architect (System Design)
*   **Responsibility:** 负责高层架构设计、数据库建模与 API 契约设计。
*   **Authority:** 拥有修改 `CLAUDE.md` 的唯一建议权。
*   **Constraint:** 不直接编写业务代码，仅产出伪代码逻辑、Laravel Migration 结构与 Vue 组件 Props 定义。

### ### Role 02: The Coder (Execution)
*   **Responsibility:** 实现 `Architect` 下发的任务，编写 Laravel Controller、Model 与 Vue 3 组件。
*   **Style:** 极致追求执行速度与代码的可读性，严格遵守 Laravel 11 规范。
*   **Workflow:** 必须通过 `command.md` 调用特定指令集（如：`php artisan make:*`）。

### ### Role 03: The Auditor (Quality Assurance)
*   **Responsibility:** 扮演 Skeptical Critic。
*   **Task:** 模拟边缘场景测试代码健壮性（如：验证失败、越权访问、Vue 响应式失效）。
*   **Veto Power:** 若发现安全漏洞或性能冗余，有权要求重新重构。

### ### Orchestration Protocol (via Trae/Claude)
*   **Handover:** Architect (设计) -> Coder (实现) -> Auditor (审计) -> Master Branch (合并)。
*   **Frontend:** PC 端使用 **Inertia.js 现代单体架构**，通过 Laravel Controller 直接将数据传递给 Vue 组件，保留 SPA 丝滑体验。
*   **Conflict Resolution:** 当逻辑冲突时，回退到 `command.md` 的 `/refactor-minimal` 逻辑。

### ### Current Development Status
*   **Backend:** ✅ 数据库模型完整（25+ 个表 + 迁移）
*   **API:** ✅ 8+ 个模块完整
*   **Web:** ✅ 40+ 个控制器就绪
*   **Admin:** ✅ 123 条后台路由完整
*   **Frontend:** ✅ Vue 组件已实现（所有页面组件已完成）
*   **Inertia.js:** ✅ 已完整集成
*   **Current Phase:** 后端业务逻辑层开发（Service、FormRequest、Policy、Observer）
*   **Docs:** ✅ 所有文档已同步更新（2026-05-27）
