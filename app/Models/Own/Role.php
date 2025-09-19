<?php

namespace App\Models\Own;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Role extends Model {
    Use LogsActivity;
    protected static $logAttributes  = ['name','description'];
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

     /**
     * Konfigurasi activity log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description']) // field yang dicatat
            ->useLogName('role')               // nama log khusus
            ->setDescriptionForEvent(fn(string $eventName) => "Role model has been {$eventName}");
    }
}