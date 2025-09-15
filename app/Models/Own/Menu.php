<?php
namespace App\Models\Own;

use App\Models\Own\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model {
    protected $fillable = ['title','route','icon','parent_id','order','is_active'];

    public function children(): HasMany {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function parent(): BelongsTo {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function roles() {
        return $this->belongsToMany(Role::class, 'menu_role')
            ->withPivot(['can_view','can_add','can_edit','can_update','can_delete','can_print'])
            ->withTimestamps();
    }
}