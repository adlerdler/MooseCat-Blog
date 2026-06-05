<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * SeoFilesNeedRegenerate - SEO 静态文件需要重新生成
 * 
 * 当内容变更（文章/项目/视频发布、更新、删除）或 SEO 设置修改时触发。
 */
class SeoFilesNeedRegenerate
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
        // 无需参数，触发时自动重新生成所有 SEO 文件
    }
}
