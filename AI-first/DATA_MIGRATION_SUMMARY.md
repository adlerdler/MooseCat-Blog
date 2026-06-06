# 数据迁移与数据库设计总览

> **文档说明**：本文件整合了 `DATA_OPTIMIZATION.md`、`DATABASE_DESIGN_ANALYSIS.md`、`DATA_DB_COMPARISON.md` 三个文档的核心内容，已完成的工作简化展示，重点突出待处理任务。

---

## 一、迁移完成情况

### 1.1 数据文件与数据库表映射 ✅

| 数据文件 | 对应表名 | 状态 |
|----------|---------|:---:|
| posts.js | posts | ✅ 已完成 |
| comments.js | comments | ✅ 已完成 |
| users.js | users | ✅ 已完成 |
| projects.js | projects | ✅ 已完成 |
| resources.js | resources | ✅ 已完成 |
| videos.js | videos | ✅ 已完成 |
| categories.js | categories | ✅ 已完成 |
| tags.js | tags | ✅ 已完成 |
| roles.js | roles | ✅ 已完成 |
| permissions.js | permissions | ✅ 已完成 |
| menus.js | menus | ✅ 已完成 |
| settings.js | settings | ✅ 已完成 |
| ad_positions.js | ad_positions | ✅ 已完成 |
| advertisements.js | advertisements | ✅ 已完成 |
| journals.js | journals | ✅ 已完成 |
| subscribers.js | subscribers | ✅ 已完成 |
| author_profiles.js | author_profiles | ✅ 已完成 |
| interactions.js | interactions | ✅ 已完成 |
| user_levels.js | user_levels | ✅ 已完成 |
| visits.js | visits | ✅ 已完成 |
| themes.js | themes | ✅ 已完成 |
| i18n_config.js | languages + translations | ✅ 已完成 |
| seo_config.js | seo | ✅ 已完成 |
| page_seo.js | page_seo | ✅ 已完成 |
| footer_config.js | footer_links | ✅ 已完成 |
| media.js | media | ✅ 已完成 |
| backup.js | backups | ✅ 已完成 |
| email_templates.js | email_templates | ✅ 已完成 |
| mail_config.js | mail_configs | ✅ 已完成 |
| logs.js | admin_logs | ✅ 已完成 |

### 1.2 迁移文件统计
- **迁移文件数**：35 个
- **Seeder 文件数**：24 个
- **模拟数据总量**：约 176 条

---

## 二、已完成的优化工作 ✅

### 2.1 数据格式规范化
- ✅ 统一 ID 类型为数字
- ✅ 统一日期格式（ISO 8601 / YYYY-MM-DD HH:mm:ss）
- ✅ 统一外键命名规范（`表名_id`）

### 2.2 国际化支持
- ✅ 广告位数据使用 `label_key` 进行国际化
- ✅ 枚举值使用国际化 key

### 2.3 代码质量提升
- ✅ 核心数据文件添加 JSDoc 注释
- ✅ 按单一职责原则拆分数据文件
- ✅ 创建 `api/`、`composables/`、`utils/` 目录

### 2.4 数据库设计规范
- ✅ 统一使用 `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- ✅ 添加 `created_at`、`updated_at` 时间戳字段
- ✅ 完善外键关联和索引设计
- ✅ 添加软删除字段 `deleted_at`

---

## 三、待处理任务 ⏳

### 3.1 数据完整性
| 任务 | 优先级 | 说明 |
|------|--------|------|
| 图片资源本地化 | P3 | projects.js、resources.js、videos.js 中图片依赖外部链接 |
| 敏感数据脱敏 | P3 | users.js 中包含真实格式邮箱地址 |

### 3.2 代码质量
| 任务 | 优先级 | 说明 |
|------|--------|------|
| 补充类型定义 | P3 | 部分数据文件缺少 JSDoc 注释 |
| 优化 home.js | P3 | 包含业务数据和配置数据，需拆分 |

### 3.3 功能开发
| 任务 | 状态 | 说明 |
|------|:---:|------|
| 站内通知系统 | ✅ 已完成 | Laravel database notifications + NotificationService |
| 数据统计面板 | ❌ 待创建 | analytics.js |
| 插件扩展机制 | ❌ 待创建 | plugins.js |
| API 开放平台 | ❌ 待创建 | apiKeys.js |
| 首页轮播管理 | ❌ 待创建 | banners.js |
| 帮助中心 | ❌ 待创建 | faq.js |
| 用户评价系统 | ❌ 待创建 | testimonials.js |

---

## 四、数据库设计规范

### 4.1 命名规范
- **表名**：小写复数，下划线分隔（如 `users`, `ad_positions`）
- **字段名**：小写，下划线分隔（如 `user_id`, `created_at`）
- **主键**：`id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- **外键**：`{表名单数}_id`（如 `author_id`, `category_id`）
- **布尔值**：`is_` 前缀（如 `is_active`, `is_approved`）
- **计数器**：`{名词}_count`（如 `views_count`, `likes_count`）

### 4.2 表结构模板
```sql
CREATE TABLE table_name (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- 业务字段
    name VARCHAR(100) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    
    -- 关联字段
    user_id BIGINT UNSIGNED NOT NULL,
    
    -- 审计字段
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    
    -- 时间戳
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    
    -- 索引
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    
    -- 外键
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.3 外键约束策略
| 策略 | 说明 | 示例 |
|------|------|------|
| CASCADE | 删除主记录时级联删除子记录 | comments → posts |
| SET NULL | 删除主记录时外键设为 NULL | posts → categories |
| RESTRICT | 禁止删除有子记录的主记录 | 默认行为 |

---

## 五、核心表 ER 图

```
users (1) ────< (N) posts
  │                    │
  │                    │
(N)                  (N)
  │                    │
  └───< role_has_permissions >─── (N) roles
                              │
                              │
                            (N)
                              │
                        categories
```

---

## 六、总结

**已完成工作**：
- ✅ 所有数据文件已迁移到数据库（100% 字段匹配）
- ✅ 数据库设计符合规范化要求（1NF/2NF/3NF）
- ✅ 完善外键关联和索引设计
- ✅ 添加时间戳和软删除字段

**待处理工作**：
- ⏳ 图片资源本地化
- ⏳ 敏感数据脱敏处理
- ⏳ 部分功能模块待开发

---

**文档版本**: 1.2  
**创建日期**: 2026-05-25  
**最后更新**: 2026-06-06  
**状态**: ✅ 全部完成

---

**已删除文档**:
- `DATA_OPTIMIZATION.md`
- `DATABASE_DESIGN_ANALYSIS.md`
- `DATA_DB_COMPARISON.md`

> 以上三个文档已合并到此文件，内容已简化并更新。