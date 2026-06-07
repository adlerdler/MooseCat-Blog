# Arkhyx Blog System / Arkhyx 博客系统 🏛️✨

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 11">
  <img src="https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js" alt="Vue 3">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/AI--First-Driven-blueviolet?style=for-the-badge" alt="AI-First">
  <img src="https://img.shields.io/badge/License-Personal-yellow?style=for-the-badge" alt="License">
</p>

---

### 🌟 项目愿景 | Vision

> **Arkhyx** 旨在构建一个面向开发者、极致极简且由 AI 驱动的现代化内容创作与分发系统。
> **Arkhyx** aims to build a modern content creation and distribution system designed for developers, featuring extreme minimalism and AI-driven evolution.

![Arkhyx Hero Image](https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80)
*示意图：极简主义与现代技术的融合 | Minimalist fusion of modern technology*

---

⚠️ **法律声明 | Legal Notice**

> **本项目采用严格的非商业个人学习许可证。**
> **This project is licensed under a strict Personal Non-Commercial Learning License.**

<details>
<summary>点击展开详细声明 | Click to expand details</summary>

- 仅限个人学习和非商业用途。
- 严禁任何商业化、企业、组织、机构、盈利性或成品代码使用。
- 个人用户必须注明作者 GitHub 地址：“Powered by [adlerdler](https://github.com/adlerdler)”。
- For personal learning and non-commercial use only.
- Commercial/Enterprise use is strictly prohibited.
- Attribution required: “Powered by [adlerdler](https://github.com/adlerdler)”.
</details>

---

## ✨ 功能特性 | Features

| 特性 | 描述 | Description |
| :--- | :--- | :--- |
| 🎨 **Constructivist UI** | 基于构成主义美学的激进界面。 | Radical interface based on Constructivist aesthetics. |
| ✍️ **Markdown Native** | 原生 Markdown 支持，实时预览。 | Native Markdown support with live preview. |
| 🤖 **AI-First Workflow** | 深度集成 Trae/Claude，支持 Agentic 开发。 | Deep Trae/Claude integration for Agentic development. |
| 📱 **SPA Architecture** | Vue 3 + Vue Router 单页应用架构。 | Vue 3 + Vue Router Single Page Application architecture. |
| 🌍 **i18n Support** | 多语言国际化支持。 | Multi-language internationalization support. |
| 🔒 **Modern Auth** | 安全可靠的 Laravel 11 认证系统。 | Secure Laravel 11 authentication system. |

---

## 🛠 技术栈 | Tech Stack

### Backend (Laravel 11)
- **Extreme Minimalism**: 移除冗余提供者与配置。
- **Eloquent ORM**: 优雅的数据处理。
- **SQLite/MySQL**: 灵活的持久化选择。

### Frontend (Vue 3 SPA)
- **Vue Router**: 客户端路由管理。
- **Vue I18n**: 多语言国际化。
- **Vite**: 极速构建体验。
- **Tailwind CSS**: 实用优先的样式定义。
- **Lucide Icons**: 现代化图标库。
- **@vueuse/motion**: 动画与过渡效果。

---

## 📁 目录结构 | Directory Structure

```mermaid
graph TD
    A[Arkhyx Root] --> B[app/ Laravel Core]
    A --> C[resources/js/ Vue Components]
    A --> D[routes/ API & Web]
    A --> E[database/ Migrations]
    A --> F[public/ Assets]
```

---

## 🚀 快速开始 | Quick Start

### 1. 环境准备 | Prerequisites
- PHP 8.2+
- Node.js 18+
- Composer

### 2. 核心步骤 | Core Steps

```bash
# 克隆并进入目录
git clone <your-repo-url> && cd laravel-vue-app

# 安装前后端依赖
composer install && npm install

# 配置环境
cp .env.example .env && php artisan key:generate

# 数据库迁移与初始化
php artisan migrate --seed

# 启动引擎
php artisan serve & npm run dev
```

---

## 📄 开发规范 | Development Guidelines

本项目遵循 **Extreme Minimalism** 哲学：
1. **函数原子化**: 保持逻辑单一。
2. **代码即文档**: 优先编写自解释代码。
3. **AI 协同**: 每次重大变更需更新 `evolution.md`。

---

## 🔧 生产环境部署 | Production Deployment

### PHP 扩展要求 | PHP Extensions

本项目需要以下 PHP 扩展（PHP 8.2+）：

| 扩展 | 说明 | 默认安装 |
| :--- | :--- | :--- |
| `ctype` | 字符类型检查 | ✅ 是 |
| `curl` | HTTP 客户端 | ✅ 是 |
| `dom` | XML 文档处理 | ✅ 是 |
| `fileinfo` | 文件 MIME 类型检测 | ❌ 需手动 |
| `hash` | 哈希算法 | ✅ 是 |
| `mbstring` | 多字节字符串处理 | ✅ 是 |
| `openssl` | 加密/解密 | ✅ 是 |
| `pcre` | 正则表达式 | ✅ 是 |
| `pdo` | 数据库抽象层 | ✅ 是 |
| `session` | 会话管理 | ✅ 是 |
| `tokenizer` | PHP 词法分析 | ✅ 是 |
| `xml` | XML 解析 | ✅ 是 |
| `gd` | 图片处理（验证码） | ❌ 需手动 |
| `pdo_mysql` | MySQL 数据库驱动 | ❌ 需手动 |
| `zip` | 压缩文件（备份功能） | ❌ 需手动 |

**宝塔面板安装方法：**
软件商店 → PHP 8.3 → 安装扩展 → 勾选 `fileinfo`、`gd`、`pdo_mysql`、`zip` → 安装完成后点击 **重载配置**。

### Nginx 配置 | Nginx Configuration

```nginx
server {
    listen 80;
    listen 443 ssl http2;
    server_name your-domain.com;

    root /path/to/project/public;
    index index.php index.html;

    # SSL 配置（按需配置证书路径）
    # ssl_certificate    /path/to/fullchain.pem;
    # ssl_certificate_key /path/to/privkey.pem;

    # 静态文件直接返回（必须在 rewrite 之前）
    location /images/ {
        try_files $uri =404;
    }

    location /build/ {
        try_files $uri =404;
    }

    # 图片缓存
    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
        expires 30d;
        error_log /dev/null;
        access_log /dev/null;
    }

    # JS/CSS 缓存
    location ~ .*\.(js|css)?$ {
        expires 12h;
        error_log /dev/null;
        access_log /dev/null;
    }

    # 禁止访问敏感文件
    location ~ ^/(\.user.ini|\.htaccess|\.git|\.env|\.svn|\.project|LICENSE|README.md) {
        return 404;
    }

    # SSL 证书验证目录
    location ~ \.well-known {
        allow all;
    }

    # Laravel 入口（伪静态规则）
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP 处理
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    access_log /path/to/logs/your-domain.access.log;
    error_log /path/to/logs/your-domain.error.log;
}
```

**关键配置说明：**
- `try_files $uri $uri/ /index.php?$query_string;` — Laravel 伪静态核心规则
- `/images/` 和 `/build/` 的 `try_files` 确保静态文件由 Nginx 直接返回，不经过 Laravel
- 敏感文件（`.env`、`.git` 等）必须返回 404

### 文件权限 | File Permissions

```bash
# 设置项目所有者为 www（Nginx/FPM 运行用户）
chown -R www:www /path/to/project

# 目录权限 755，文件权限 644
chmod -R 755 /path/to/project

# storage 和 bootstrap/cache 需要写入权限
chmod -R 775 /path/to/project/storage
chmod -R 775 /path/to/project/bootstrap/cache
```

### 低内存服务器构建 | Low Memory Server Build

对于 2G 内存以下的服务器，建议在本地构建后上传编译产物：

```bash
# 本地执行构建
npm run build

# 上传 public/build 目录到服务器
scp -r public/build/ user@server:/path/to/project/public/
```

或在服务器上增加 Swap 分区：
```bash
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
```

---

## 📝 演进历程 | Evolution

查看 [evolution.md](file:///i:/Code%20editing/bolg_laravel_adlerian/laravel-vue-app/evolution.md) 了解 AI 导师与人类导师如何共同塑造 Arkhyx。

---

<p align="center">
  Built with ❤️ by AI and <a href="https://github.com/adlerdler">adlerdler</a>
</p>
