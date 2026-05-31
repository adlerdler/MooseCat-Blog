<?php

namespace App\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    /**
     * 使用 Spatie 内置的 UUID 生成唯一路径
     * Spatie v11 HasUuid trait 在创建 Media 记录时自动生成 UUID
     */
    public function getPath(Media $media): string
    {
        return 'media/' . $media->uuid . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return 'media/' . $media->uuid . '/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return 'media/' . $media->uuid . '/responsive-images/';
    }
}
