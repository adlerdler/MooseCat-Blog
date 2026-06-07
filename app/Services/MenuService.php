<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

/**
 * MenuService - 菜单服务类
 * 
 * 提供网站菜单的管理功能，包括菜单列表、树形结构、创建、更新、删除和排序操作。
 * Provides menu management functionality, including menu listing, tree structure, 
 * creation, update, deletion and ordering operations.
 */
class MenuService
{
    /**
     * 获取所有菜单
     * Get all menus
     */
    public function getMenus(): Collection
    {
        return Menu::orderBy('order', 'asc')->get();
    }

    /**
     * 获取菜单树形结构
     * Get menu tree structure
     */
    public function getMenuTree(string $location = 'frontend'): Collection
    {
        return Menu::query()
            ->where('location', $location)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('order', 'asc');
            }])
            ->orderBy('order', 'asc')
            ->get();
    }

    /**
     * 根据位置获取菜单
     * Get menus by location
     */
    public function getMenusByLocation(string $location): Collection
    {
        return Menu::where('location', $location)->orderBy('order', 'asc')->get();
    }

    /**
     * 根据ID获取菜单
     * Get menu by ID
     */
    public function getMenuById(int $id): ?Menu
    {
        return Menu::find($id);
    }

    /**
     * 创建菜单
     * Create menu
     */
    public function createMenu(array $data): Menu
    {
        return DB::transaction(function () use ($data) {
            return Menu::create($data);
        });
    }

    /**
     * 更新菜单
     * Update menu
     */
    public function updateMenu(Menu $menu, array $data): Menu
    {
        return DB::transaction(function () use ($menu, $data) {
            $menu->update($data);
            return $menu;
        });
    }

    /**
     * 删除菜单（含子菜单）
     * Delete menu (including children)
     */
    public function deleteMenu(Menu $menu): bool
    {
        return DB::transaction(function () use ($menu) {
            $menu->children()->delete();
            return $menu->delete();
        });
    }

    /**
     * 更新菜单排序
     * Update menu order
     */
    public function updateOrder(array $orderData): void
    {
        DB::transaction(function () use ($orderData) {
            foreach ($orderData as $item) {
                Menu::where('id', $item['id'])->update([
                    'order' => $item['order'],
                    'parent_id' => $item['parent_id'] ?? null,
                ]);
            }
        });
    }

    /**
     * 根据路径推导权限名称
     * Derive permission name from path
     */
    protected function derivePermissionFromPath(string $path): ?string
    {
        // 移除 /admin/ 前缀
        $cleanPath = preg_replace('#^/admin/#', '', $path);
        
        // 路径到权限的映射规则
        $pathToPermission = [
            'index' => 'view_analytics',
            'posts' => 'manage_posts',
            'videos' => 'manage_videos',
            'projects' => 'manage_projects',
            'resources' => 'manage_resources',
            'journals' => 'manage_journals',
            'categories' => 'manage_categories',
            'tags' => 'manage_tags',
            'comments' => 'manage_comments',
            'advertisements' => 'manage_ads',
            'users' => 'manage_users',
            'subscribers' => 'manage_subscribers',
            'user-levels' => 'manage_user_levels',
            'roles' => 'manage_roles',
            'settings' => 'manage_settings',
            'social-links' => 'manage_social_links',
            'seo' => 'manage_seo',
            'i18n' => 'manage_i18n',
            'media' => 'manage_media',
            'email-templates' => 'manage_email_templates',
            'front-menu' => 'manage_menu',
            'notifications' => 'manage_notifications',
            'mail-config' => 'manage_mail_config',
            'logs' => 'manage_logs',
            'backup' => 'manage_backup',
            'restore' => 'manage_restore',
            'about' => null, // 关于页不需要权限
        ];

        return $pathToPermission[$cleanPath] ?? null;
    }

    /**
     * 过滤用户有权限访问的菜单
     * Filter menus by user permissions
     */
    public function filterMenusByPermissions(array $menus, ?object $user = null): array
    {
        // 如果没有用户，返回空数组
        if (!$user) {
            return [];
        }

        // 超级管理员拥有所有权限
        if ($user->isAdministrator()) {
            return $menus;
        }

        // 如果用户没有任何权限，返回空数组
        if ($user->getAllPermissions()->isEmpty()) {
            return [];
        }

        $filteredMenus = [];

        foreach ($menus as $menu) {
            // 如果是父菜单（没有 path），检查子菜单
            if (empty($menu['path']) && isset($menu['children'])) {
                $filteredChildren = $this->filterMenusByPermissions($menu['children'], $user);
                if (!empty($filteredChildren)) {
                    $filteredMenu = $menu;
                    $filteredMenu['children'] = $filteredChildren;
                    $filteredMenus[] = $filteredMenu;
                }
                continue;
            }

            // 如果有 path，检查权限
            if (!empty($menu['path'])) {
                $permission = $this->derivePermissionFromPath($menu['path']);
                
                // 如果没有对应的权限规则，跳过此菜单（不再默认允许）
                if ($permission === null) {
                    continue;
                }

                // 检查用户是否有该权限
                if ($user->can($permission)) {
                    $filteredMenus[] = $menu;
                }
            } else {
                // 没有 path 的菜单项（如分组标题），只有在有子菜单时才显示
                if (isset($menu['children']) && !empty($menu['children'])) {
                    $filteredChildren = $this->filterMenusByPermissions($menu['children'], $user);
                    if (!empty($filteredChildren)) {
                        $filteredMenu = $menu;
                        $filteredMenu['children'] = $filteredChildren;
                        $filteredMenus[] = $filteredMenu;
                    }
                }
            }
        }

        return $filteredMenus;
    }

    /**
     * 获取过滤后的后台菜单
     * Get filtered admin menus by user permissions
     */
    public function getFilteredAdminMenus(?object $user = null): array
    {
        $menus = Menu::where('type', 'admin')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        // 构建树形结构
        $tree = $this->buildMenuTree($menus);

        // 过滤权限
        return $this->filterMenusByPermissions($tree, $user);
    }

    /**
     * 构建菜单树形结构
     * Build menu tree structure
     */
    protected function buildMenuTree(array $menus, int $parentId = null): array
    {
        $tree = [];

        foreach ($menus as $menu) {
            if ($menu['parent_id'] === $parentId) {
                $children = $this->buildMenuTree($menus, $menu['id']);
                if (!empty($children)) {
                    $menu['children'] = $children;
                }
                $tree[] = $menu;
            }
        }

        return $tree;
    }
}