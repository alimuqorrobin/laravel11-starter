<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Own\Role;
use App\Models\Own\Menu;

class MenuRoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::create(['name'=>'admin','description'=>'Administrator']);
        $user  = Role::create(['name'=>'user','description'=>'Normal user']);

        $mDashboard = Menu::create(['title'=>'Dashboard','icon'=>'fa fa-tachometer','route'=>'dashboard','order'=>1]);
        $mMaster = Menu::create(['title'=>'Master','icon'=>'fa fa-folder','order'=>2]);
        $mUsers = Menu::create(['title'=>'Users','icon'=>'fa fa-users','route'=>'users.index','parent_id'=>$mMaster->id,'order'=>1]);
        $mRoles = Menu::create(['title'=>'Roles','icon'=>'fa fa-user-tag','route'=>'roles.index','parent_id'=>$mMaster->id,'order'=>2]);

        $mSettings = Menu::create(['title'=>'Settings','icon'=>'fa fa-cog','order'=>3]);
        $mProfile = Menu::create(['title'=>'Profile','icon'=>'fa fa-id-badge','route'=>'profile','parent_id'=>$mSettings->id,'order'=>1]);
        $mProfileDetail = Menu::create(['title'=>'Detail','icon'=>'fa fa-info','route'=>'profile.detail','parent_id'=>$mProfile->id,'order'=>1]);

        // assign permissions: admin full access
        $admin->menus()->attach($mDashboard->id, ['can_view'=>true]);
        $admin->menus()->attach($mMaster->id, ['can_view'=>true]);
        $admin->menus()->attach($mUsers->id, ['can_view'=>true,'can_add'=>true,'can_edit'=>true,'can_update'=>true,'can_delete'=>true,'can_print'=>true]);
        $admin->menus()->attach($mRoles->id, ['can_view'=>true,'can_add'=>true,'can_edit'=>true,'can_delete'=>true]);
        $admin->menus()->attach($mSettings->id, ['can_view'=>true]);
        $admin->menus()->attach($mProfile->id, ['can_view'=>true,'can_edit'=>true]);
        $admin->menus()->attach($mProfileDetail->id, ['can_view'=>true]);

        // user: limited
        $user->menus()->attach($mDashboard->id, ['can_view'=>true]);
        $user->menus()->attach($mProfile->id, ['can_view'=>true,'can_edit'=>true]);
    }
}
