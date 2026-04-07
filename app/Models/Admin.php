<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable([
    'name',
    'email',
    'password',
    'phone',
    'role',
    'is_active',
    'last_login_at',
    'created_by',
])]
#[Hidden(['password'])]
class Admin extends Authenticatable
{
    use HasFactory;

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function createdAdmins(): HasMany
    {
        return $this->hasMany(Admin::class, 'created_by');
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function canAccess(string $featureKey, string $action = 'view'): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        $column = match ($action) {
            'view' => 'can_view',
            'create' => 'can_create',
            'update' => 'can_update',
            'delete' => 'can_delete',
            default => null,
        };

        if (! $column) {
            return false;
        }

        return AdminRolePermission::query()
            ->where('role', $this->role)
            ->whereHas('feature', fn ($query) => $query->where('feature_key', $featureKey))
            ->value($column) ?? false;
    }
}
