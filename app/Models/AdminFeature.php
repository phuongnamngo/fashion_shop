<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['feature_key', 'name', 'description'])]
class AdminFeature extends Model
{
    use HasFactory;

    public function rolePermissions(): HasMany
    {
        return $this->hasMany(AdminRolePermission::class);
    }
}
