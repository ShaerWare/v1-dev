<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AuthBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'access_type',
        'index_code',
    ];

    /**
     * Получаем URL изображения.
     */
    public function getImageUrlAttribute()
    {
        return Storage::url($this->image_path);
    }
}
