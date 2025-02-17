<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Laravel\Passport\HasApiTokens;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Models\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    // Переопределяем метод, чтобы учесть Spatie и Orchid
    public function hasAccess(string $permission, bool $strict = false): bool
    {
        // 1. Проверяем права через Spatie (если у роли есть доступ)
        if ($this->hasPermissionTo($permission)) {
            return true;
        }

        // 2. Проверяем, есть ли это право в JSON `permissions` (Orchid)
        $permissions = $this->permissions ? json_decode($this->permissions, true) : [];

        return Arr::get($permissions, $permission, false);
    }

    public function removeRole($role): int
    {
        // Используем функционал Spatie для удаления роли
        return $this->roles()->detach($role);
    }

    public function getPermissionsAttribute()
    {
        return $this->roles->flatMap->permissions->pluck('name')->unique();
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'sms_code',
        'sms_code_expires_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'array',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'email' => Like::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'updated_at',
        'created_at',
    ];
}
