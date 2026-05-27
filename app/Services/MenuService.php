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
}