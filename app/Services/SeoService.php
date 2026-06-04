<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Seo;
use App\Models\PageSeo;
use Illuminate\Support\Collection;

/**
 * SeoService - SEO 管理服务类
 *
 * 提供全局 SEO 配置和页面级 SEO 的 CRUD 功能。
 * Provides global SEO config and page-level SEO CRUD.
 */
class SeoService
{
    public function __construct() {}

    /**
     * 获取全局 SEO 配置
     * Get global SEO configuration.
     */
    public function getGlobalSeo(): ?Seo
    {
        return Seo::getGlobalSeo();
    }

    /**
     * 更新或创建全局 SEO 配置
     * Update or create global SEO configuration.
     */
    public function updateGlobalSeo(array $data): Seo
    {
        return Seo::updateGlobalSeo($data);
    }

    /**
     * 获取所有页面 SEO 配置
     * Get all page-level SEO configurations.
     */
    public function getAllPageSeo(): Collection
    {
        return PageSeo::orderBy('page_key')->get();
    }

    /**
     * 创建页面 SEO 配置
     * Create a page-level SEO configuration.
     */
    public function createPageSeo(array $data): PageSeo
    {
        return PageSeo::create($data);
    }

    /**
     * 更新页面 SEO 配置
     * Update a page-level SEO configuration.
     */
    public function updatePageSeo(PageSeo $pageSeo, array $data): PageSeo
    {
        $pageSeo->update($data);
        return $pageSeo;
    }

    /**
     * 删除页面 SEO 配置
     * Delete a page-level SEO configuration.
     */
    public function deletePageSeo(PageSeo $pageSeo): void
    {
        $pageSeo->delete();
    }
}
