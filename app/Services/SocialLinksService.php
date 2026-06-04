<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FooterLink;

/**
 * SocialLinksService - 社交链接 & 页脚导航管理服务类
 *
 * 提供社交链接和页脚导航链接的 CRUD 功能（基于 FooterLink 模型）。
 * Provides social links and footer navigation CRUD (based on FooterLink model).
 */
class SocialLinksService
{
    public function __construct() {}

    /**
     * 获取社交链接列表
     * Get social links list.
     */
    public function getSocialLinks(): array
    {
        return FooterLink::socialLinks()
            ->orderBy('sort_order')
            ->get()
            ->map(fn($link) => [
                'id'         => $link->id,
                'platform'   => $link->platform,
                'url'        => $link->url,
                'icon'       => $link->icon ?? $link->icon_name,
                'label'      => $link->label,
                'sort_order' => $link->sort_order,
                'is_active'  => $link->is_active,
            ])->toArray();
    }

    /**
     * 获取分类导航链接列表
     * Get category navigation links list.
     */
    public function getCategoryLinks(): array
    {
        return FooterLink::categoryLinks()
            ->orderBy('sort_order')
            ->get()
            ->map(fn($link) => [
                'id'         => $link->id,
                'label'      => $link->label,
                'url'        => $link->url,
                'sort_order' => $link->sort_order,
                'is_active'  => $link->is_active,
            ])->toArray();
    }

    /**
     * 获取数据导航链接列表
     * Get data navigation links list.
     */
    public function getDataLinks(): array
    {
        return FooterLink::dataLinks()
            ->orderBy('sort_order')
            ->get()
            ->map(fn($link) => [
                'id'         => $link->id,
                'label'      => $link->label,
                'url'        => $link->url,
                'sort_order' => $link->sort_order,
                'is_active'  => $link->is_active,
            ])->toArray();
    }

    /**
     * 创建链接（社交链接或导航链接）
     * Create a link (social link or navigation link).
     */
    public function create(array $data): FooterLink
    {
        $type = $data['type'] ?? 'social';

        if ($type === 'social') {
            return FooterLink::create([
                'type'       => 'social_link',
                'platform'   => $data['platform'],
                'url'        => $data['url'],
                'icon'       => $data['icon'] ?? $data['platform'],
                'icon_name'  => $data['platform'],
                'label'      => $data['label'] ?? strtoupper($data['platform']),
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active'  => $data['is_active'] ?? true,
            ]);
        }

        return FooterLink::create([
            'type'       => 'nav_link',
            'platform'   => $type,
            'label'      => $data['label'] ?? $data['platform'] ?? 'Link',
            'url'        => $data['url'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => $data['is_active'] ?? true,
        ]);
    }

    /**
     * 更新链接
     * Update a link.
     */
    public function update(FooterLink $footerLink, array $data): FooterLink
    {
        $updateData = [
            'url'       => $data['url'],
            'is_active' => $data['is_active'] ?? $footerLink->is_active,
        ];

        if (! empty($data['platform'] ?? null)) {
            $updateData['platform']  = $data['platform'];
            $updateData['icon_name'] = $data['platform'];
        }

        if (! empty($data['icon'] ?? null)) {
            $updateData['icon'] = $data['icon'];
        }

        if (! empty($data['label'] ?? null)) {
            $updateData['label'] = $data['label'];
        }

        if (array_key_exists('sort_order', $data) && $data['sort_order'] !== null) {
            $updateData['sort_order'] = (int) $data['sort_order'];
        }

        $footerLink->update($updateData);

        return $footerLink;
    }

    /**
     * 删除链接
     * Delete a link.
     */
    public function delete(FooterLink $footerLink): void
    {
        $footerLink->delete();
    }
}
