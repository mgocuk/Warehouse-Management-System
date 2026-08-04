<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'last_login'        => 'datetime',
            'is_active'         => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Use mb_strtolower for correct Turkish character comparison (İ, Ö, Ü, etc.)
     */
    public function isAdmin(): bool
    {
        return $this->role && mb_strtolower($this->role->name, 'UTF-8') === 'admin';
    }

    public function isManager(): bool
    {
        if (!$this->role) return false;
        $roleName = mb_strtolower($this->role->name, 'UTF-8');
        return in_array($roleName, ['depo sorumlusu', 'admin']);
    }

    public function isViewer(): bool
    {
        return $this->role && mb_strtolower($this->role->name, 'UTF-8') === 'görüntüleme';
    }
}
