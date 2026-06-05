<?php

namespace App\Listeners;

use App\Events\SeoFilesNeedRegenerate;
use App\Services\SeoService;

/**
 * RegenerateSeoFiles - 监听 SEO 文件重新生成事件
 * 
 * 负责调用 SeoService 生成/删除 robots.txt、llm.txt、sitemap.xml。
 */
class RegenerateSeoFiles
{
    public function __construct(
        protected SeoService $seoService
    ) {}

    public function handle(SeoFilesNeedRegenerate $event): void
    {
        $this->seoService->regenerateSeoFiles();
    }
}
