<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = ['title', 'description', 'image_path', 'access_type', 'index_code', 'region'];

    public function getImageUrlAttribute()
    {
        // Если путь уже полный (с http), просто возвращаем его
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }

        // Формируем правильный URL с использованием домена из APP_URL
        return env('APP_URL').Storage::url($this->image_path);
    }

    public function getContent()
    {
        // return $this->description; // Или другое нужное поле
    }
}
