<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserLevelsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_user_levels');
    }
    public function index(): Response
    {
        $levels = UserLevel::orderBy('sort_order')
            ->get()
            ->map(fn($l) => [
                'id' => $l->id,
                'name' => $l->name,
                'level' => $l->level,
                'min_points' => $l->min_points,
                'max_points' => $l->max_points,
                'discount' => $l->discount,
                'color' => $l->color,
                'icon' => $l->icon,
                'description' => $l->description,
                'benefits' => $l->benefits ?? [],
                'is_active' => $l->is_active,
                'sort_order' => $l->sort_order,
                'users_count' => $l->users()->count(),
                'created_at' => $l->created_at?->format('Y-m-d'),
            ]);

        return Inertia::render('admin/UserLevels', [
            'levels' => $levels,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'level' => 'required|integer',
            'min_points' => 'required|integer|min:0',
            'max_points' => 'nullable|integer|gte:min_points',
            'discount' => 'nullable|integer|min:0|max:100',
            'color' => 'required|string|max:50',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'benefits' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        UserLevel::create($validated);

        return back()->with('success', '用户等级已创建');
    }

    public function update(Request $request, UserLevel $userLevel): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'level' => 'sometimes|required|integer',
            'min_points' => 'sometimes|required|integer|min:0',
            'max_points' => 'nullable|integer|gte:min_points',
            'discount' => 'nullable|integer|min:0|max:100',
            'color' => 'sometimes|required|string|max:50',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'benefits' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $userLevel->update($validated);

        return back()->with('success', '用户等级已更新');
    }

    public function destroy(UserLevel $userLevel): RedirectResponse
    {
        $userLevel->delete();

        return back()->with('success', '用户等级已删除');
    }
}