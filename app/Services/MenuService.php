<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class MenuService
{
    public function getMenus(): Collection
    {
        return Menu::orderBy('order', 'asc')->get();
    }

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

    public function getMenusByLocation(string $location): Collection
    {
        return Menu::where('location', $location)->orderBy('order', 'asc')->get();
    }

    public function getMenuById(int $id): ?Menu
    {
        return Menu::find($id);
    }

    public function createMenu(array $data): Menu
    {
        return DB::transaction(function () use ($data) {
            return Menu::create($data);
        });
    }

    public function updateMenu(Menu $menu, array $data): Menu
    {
        return DB::transaction(function () use ($menu, $data) {
            $menu->update($data);
            return $menu;
        });
    }

    public function deleteMenu(Menu $menu): bool
    {
        return DB::transaction(function () use ($menu) {
            $menu->children()->delete();
            return $menu->delete();
        });
    }

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
