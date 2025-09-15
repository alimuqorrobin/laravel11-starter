<?php

namespace App\Models\Own;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model {
    protected $fillable = ['name','description'];

    public function users() {
        return $this->hasMany(User::class);
    }

    public function menus(): BelongsToMany {
        return $this->belongsToMany(Menu::class, 'menu_role')
            ->withPivot(['can_view','can_add','can_edit','can_update','can_delete','can_print'])
            ->withTimestamps();
    }

    // helper to get permission for a menu
    public function hasPermission(int $menuId, string $permission): bool {
        $perm = $this->menus()->where('menus.id', $menuId)->first();
        if (! $perm) return false;
        $col = 'can_' . $permission;
        return (bool) $perm->pivot->{$col};
    }
}