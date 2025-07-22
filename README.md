# MooseCat Blog / 驼鹿猫博客系统 🦌🐱

> 一款面向开发者、内容创作者的现代化博客系统，支持 Markdown 编辑、文章一键同步社媒、视频嵌入（YouTube/Bilibili）、图文记录与资源管理。
> A modern blog CMS designed for developers & creators. Markdown editing, video embedding (YouTube/Bilibili), content sync, and visual journaling.

---

⚠️ **本项目采用严格的非商业个人学习许可证，详见 LICENSE 文件。**

- 仅限个人学习和非商业用途。
- 严禁任何商业化、企业、组织、机构、盈利性或成品代码使用。
- 禁止将本代码作为产品、SaaS、服务、成品、商业项目、闭源项目、衍生品等交付、集成、分发、销售、租赁、托管、转让、再许可、再分发、再包装、再发布、再授权、再利用、再开发、再加工、再创作、再生产、再传播、再展示、再演绎、再翻译、再汇编、再组合、再合并、再链接、再嵌入、再调用、再引用、再依赖等。
- 个人用户在网站或可见界面必须注明作者的 GitHub 地址，格式为“Powered by [https://github.com/yourgithub]”，并包含超链接。

---

⚠️ **This project is licensed under a strict Personal Non-Commercial Learning License. See LICENSE for details.**

- For personal learning and non-commercial use only.
- Any commercial, enterprise, organizational, institutional, for-profit, or product code use is strictly prohibited.
- It is forbidden to deliver, integrate, distribute, sell, rent, host, transfer, sublicense, repackage, republish, relicense, reuse, redevelop, rework, recreate, reproduce, disseminate, display, perform, translate, compile, combine, merge, link, embed, call, reference, depend on, or otherwise use this code as part of any product, SaaS, service, commercial project, closed-source project, or derivative work.
- Personal users must display the author's GitHub address in a prominent place on their website, in the format “Powered by [https://github.com/yourgithub]” with a hyperlink.

---

## ✨ 功能特性 | Features

- 博客文章展示 | Blog post display
- Markdown 编辑与预览 | Markdown editing & preview
- 响应式 UI，适配多端 | Responsive UI
- 用户注册与登录（基础用户模型）| User registration & login (basic model)
- 现代前后端分离架构 | Modern frontend-backend separation

---

## 🛠 技术栈 | Tech Stack

- **前端 | Frontend**：Vue 3、Vite、Tailwind CSS
- **后端 | Backend**：Laravel 11、Eloquent ORM
- **构建工具 | Build Tool**：Vite
- **数据库 | Database**：MySQL / SQLite（默认支持 SQLite，配置可切换 | SQLite by default, configurable）

---

## 📁 目录结构 | Directory Structure

```
laravel-vue-app/
├── app/                # Laravel 核心代码 | Laravel core
├── resources/
│   ├── js/             # 前端 Vue 组件与入口 | Vue components & entry
│   ├── css/            # Tailwind CSS
│   └── views/          # Blade 模板 | Blade templates
├── routes/             # 路由定义 | Routes
├── database/           # 数据库迁移与种子 | Migrations & seeders
├── public/             # 公共资源与入口 | Public assets & entry
├── config/             # 配置文件 | Config files
├── tests/              # 测试 | Tests
├── package.json        # 前端依赖 | Frontend deps
├── composer.json       # 后端依赖 | Backend deps
└── README.md           # 项目说明 | Project doc
```

---

## 🚀 快速开始 | Quick Start

### 1. 克隆项目 | Clone

```bash
git clone <your-repo-url>
cd laravel-vue-app
```

### 2. 安装依赖 | Install dependencies

- **后端 | Backend**

  ```bash
  composer install
  ```

- **前端 | Frontend**

  ```bash
  npm install
  # or pnpm install
  ```

### 3. 环境配置 | Environment setup

复制 `.env.example` 为 `.env`，并根据实际情况配置数据库等信息：
Copy `.env.example` to `.env` and configure as needed:

```bash
cp .env.example .env
php artisan key:generate
```

### 4. 数据库迁移与填充 | Migrate & seed

```bash
php artisan migrate
php artisan db:seed
```

### 5. 启动开发环境 | Start dev server

```bash
# 启动后端 | Backend
php artisan serve

# 启动前端 | Frontend (new terminal)
npm run dev
```

访问 `http://localhost:8000` 查看效果。
Visit `http://localhost:8000` to view.

---

## 🧩 构建与部署 | Build & Deploy

- 构建前端静态资源 | Build frontend:

  ```bash
  npm run build
  ```

- 部署时请确保 `.env` 配置正确，运行数据库迁移和静态资源发布。
  Ensure `.env` is correct, run migrations and publish assets for deployment.

---

## 📝 测试 | Test

```bash
# 后端测试 | Backend test
phpunit

# 前端测试（如有配置）| Frontend test (if configured)
# npm run test
```

---

## 📄 其他说明 | Other Notes

- 前端样式采用 Tailwind CSS，遵循团队样式指南。
  Tailwind CSS is used for frontend styling, following team guidelines.
- 代码风格遵循《代码整洁之道》，优先使用 async/await，完善错误处理。
  Code style follows Clean Code, prefers async/await, robust error handling.
- 欢迎二次开发与贡献。
  Contributions are welcome.

---

如需详细文档或有任何问题，请提交 Issue。
For more docs or questions, please submit an Issue.
