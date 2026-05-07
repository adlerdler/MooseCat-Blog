# ## Command Registry (Operational Manual)

### ### /init-feature [FeatureName]
*   **Action:** 创建新功能分支并初始化 Laravel/Vue 模板。
*   **Trigger:** AI 自动抓取 GitHub Issue。
*   **Output:** 生成 Migration, Controller, Model 及基础 Vue 组件。

### ### /refactor-minimal [TargetFile]
*   **Action:** 按照“极端极简主义”重构 Laravel 或 Vue 代码。
*   **Logic:** 删除冗余逻辑，合并重复的 Tailwind Class，优化 Token 消耗。

### ### /audit-laravel
*   **Action:** 运行 `php artisan insights` 或自定义静态分析。
*   **Focus:** 检查 N+1 查询、路由性能及代码复杂度。

### ### /update-doc
*   **Action:** 同步更新 README.md 与相关技术文档。
*   **Constraint:** 严禁出现“AI 废话”，只保留核心逻辑变更。

### ### /test-all
*   **Action:** 运行后端 `php artisan test` 与前端测试。
*   **Constraint:** 必须在合并 PR 前通过所有测试。

---
**Protocol Note:** 所有命令执行结果必须清晰展示在对话中，以便人类导师审核。