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

### ### 04. Orchestration Protocol (via Trae/Claude)
*   **Handover:** Architect (设计) -> Coder (实现) -> Auditor (审计) -> Master Branch (合并)。
*   **Frontend Note:** PC 端开发优先使用“混合模式”（Blade + Vue 3），利用 `@json` 传递初始数据，保持架构直观。
*   **Conflict Resolution:** 当逻辑冲突时，回退至 `command.md` 的 `/refactor-minimal` 逻辑。
*   **Current Phase:** 后端 API 与管理后台路由已完成，进入前端 Vue 组件开发阶段。

### ### 05. Current Development Status
*   **Backend:** ✅ 数据库模型完整（12 个表）
*   **API:** ✅ 8 个模块完整（Posts、Comments、Videos、Projects、Resources、Categories、Tags、Users）
*   **Web:** ✅ 6 个控制器就绪（Post、Video、Project、Resource、Category、Comment）
*   **Admin:** ✅ 6 个资源路由（Posts、Videos、Projects、Resources、Categories、Tags）
*   **Frontend:** ⚠️ Vue 组件待实现（编辑器、评论区）
*   **Docs:** ✅ API 文档与管理后台规范已更新