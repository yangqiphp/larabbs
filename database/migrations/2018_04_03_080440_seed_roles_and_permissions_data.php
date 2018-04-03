<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Model;
use Illuminate\Support\Facades\DB;

class SeedRolesAndPermissionsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //清除缓存
        app()['cache']->forget('spatie.permission.cache');

        //创建权限
        Permission::create(['name' => 'manage_contents']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'edit_settings']);

        //创建站长角色 并赋予权限
        $founder = Role::create(['name' => 'Founder']);
        $founder->givePermissionTo('manage_contents');
        $founder->givePermissionTo('manage_users');
        $founder->givePermissionTo('edit_settings');

        //创建管理员角色，并赋予权限
        $maintainer = Role::create(['name' => 'Maintainer']);
        $maintainer->givePermissionTo('manage_contents');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //清除缓存
        app()['cache']->forget('spatie.permission.cache');

        //清空权限相关数据表
        $tablenames = config('permission.table_names');

        Model::unguard();
        DB::table($tablenames['role_has_premissions'])->delete();
        DB::table($tablenames['model_has_roles'])->delete();
        DB::table($tablenames['model_has_premissions'])->delete();
        DB::table($tablenames['roles'])->delete();
        DB::table($tablenames['premissions'])->delete();

        Model::reguard();
    }
}
