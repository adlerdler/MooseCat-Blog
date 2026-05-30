<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SocialLinksController extends Controller
{
    public function index(): Response
    {
        $socialLinks = FooterLink::socialLinks()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($link) {
                return [
                    'id' => $link->id,
                    'platform' => $link->platform,
                    'url' => $link->url,
                    'icon' => $link->icon ?? $link->icon_name,
                    'label' => $link->label,
                    'sort_order' => $link->sort_order,
                    'is_active' => $link->is_active,
                ];
            });
        
        $categoryLinks = FooterLink::categoryLinks()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($link) {
                return [
                    'id' => $link->id,
                    'label' => $link->label,
                    'url' => $link->url,
                    'sort_order' => $link->sort_order,
                    'is_active' => $link->is_active,
                ];
            });
        
        $dataLinks = FooterLink::dataLinks()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($link) {
                return [
                    'id' => $link->id,
                    'label' => $link->label,
                    'url' => $link->url,
                    'sort_order' => $link->sort_order,
                    'is_active' => $link->is_active,
                ];
            });

        return Inertia::render('admin/SocialLinks', [
            'socialLinks' => $socialLinks,
            'navLinks' => [
                'categories' => $categoryLinks,
                'data' => $dataLinks,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $type = $request->input('type', 'social');
        
        if ($type === 'social') {
            $validated = $request->validate([
                'platform' => 'required|string|max:255',
                'url' => 'required|url|max:500',
                'icon' => 'nullable|string|max:255',
                'label' => 'nullable|string|max:255',
                'sort_order' => 'nullable|integer',
                'is_active' => 'boolean',
            ]);

            FooterLink::create([
                'type' => 'social_link',
                'platform' => $validated['platform'],
                'url' => $validated['url'],
                'icon' => $validated['icon'] ?? $validated['platform'],
                'icon_name' => $validated['platform'],
                'label' => $validated['label'] ?? strtoupper($validated['platform']),
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);
        } else {
            $validated = $request->validate([
                'label' => 'required|string|max:255',
                'url' => 'required|string|max:500',
                'sort_order' => 'nullable|integer',
                'is_active' => 'boolean',
            ]);

            FooterLink::create([
                'type' => 'nav_link',
                'platform' => $type,
                'label' => $validated['label'],
                'url' => $validated['url'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);
        }

        return redirect()->route('admin.social-links')->with('success', '链接已创建');
    }

    public function update(Request $request, $id)
    {
        $footerLink = FooterLink::findOrFail($id);
        
        $validated = $request->validate([
            'platform' => 'nullable|string|max:255',
            'url' => 'required|string|max:500',
            'icon' => 'nullable|string|max:255',
            'label' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $updateData = [
            'url' => $validated['url'],
            'is_active' => $validated['is_active'] ?? $footerLink->is_active,
        ];
        
        if ($validated['platform']) {
            $updateData['platform'] = $validated['platform'];
            $updateData['icon_name'] = $validated['platform'];
        }
        
        if ($validated['icon']) {
            $updateData['icon'] = $validated['icon'];
        }
        
        if ($validated['label']) {
            $updateData['label'] = $validated['label'];
        }
        
        if ($validated['sort_order'] !== null) {
            $updateData['sort_order'] = $validated['sort_order'];
        }

        $footerLink->update($updateData);

        return redirect()->route('admin.social-links')->with('success', '链接已更新');
    }

    public function destroy($id)
    {
        $footerLink = FooterLink::findOrFail($id);
        $footerLink->delete();

        return redirect()->route('admin.social-links')->with('success', '链接已删除');
    }
}
