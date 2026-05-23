<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    protected $table = 'seo';
    
    protected $fillable = [
        'page_type',
        'title',
        'description',
        'keywords',
        'og_image',
    ];
}
