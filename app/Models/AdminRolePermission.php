<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'role',
    'admin_feature_id',
    'can_view',
    'can_create',
    'can_update',
    'can_delete',
])]
class AdminRolePermission extends Model
{
    use HasFactory;

    public function feature(): BelongsTo
    {
        return $this->belongsTo(AdminFeature::class, 'admin_feature_id');
    }

    protected function casts(): array
    {
        return [
            'can_view' => 'boolean',
            'can_create' => 'boolean',
            'can_update' => 'boolean',
            'can_delete' => 'boolean',
        ];
    }
}
