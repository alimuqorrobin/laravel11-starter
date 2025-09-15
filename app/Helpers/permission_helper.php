<?php

use App\Models\Own\Menu;
use Illuminate\Support\Facades\Auth;

if (! function_exists('can_permission')) {
    /**
     * General permission checker
     * @param string $routeName
     * @param string $permission (view|add|edit|update|delete|print)
     * @return bool
     */
    function can_permission(string $routeName, string $permission): bool
    {
        $user = Auth::user();
        if (! $user || ! $user->role) return false;

        $menu = Menu::where('route', $routeName)->first();
        if (! $menu) return false;

        return $user->role->hasPermission($menu->id, $permission);
    }
}

if (! function_exists('can_view')) {
    function can_view(string $routeName): bool {
        return can_permission($routeName, 'view');
    }
}

if (! function_exists('can_add')) {
    function can_add(string $routeName): bool {
        return can_permission($routeName, 'add');
    }
}

if (! function_exists('can_edit')) {
    function can_edit(string $routeName): bool {
        return can_permission($routeName, 'edit');
    }
}

if (! function_exists('can_update')) {
    function can_update(string $routeName): bool {
        return can_permission($routeName, 'update');
    }
}

if (! function_exists('can_delete')) {
    function can_delete(string $routeName): bool {
        return can_permission($routeName, 'delete');
    }
}

if (! function_exists('can_print')) {
    function can_print(string $routeName): bool {
        return can_permission($routeName, 'print');
    }
}
