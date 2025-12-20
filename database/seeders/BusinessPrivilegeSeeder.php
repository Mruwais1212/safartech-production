<?php

namespace Database\Seeders;

use App\Models\Panel\Privilege;
use Illuminate\Database\Seeder;

class BusinessPrivilegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permission = Privilege::updateOrCreate(['controller' => 'Panel\Backend\PermissionController', 'method' => null], [
            'name_ar' => 'الصلاحيات', 'name_en' => 'Permission Groups', 'url' => null, 'code' => 'permission',
            'type' => 1, 'icon' => 'fa fa-unlock-alt', 'parent_id' => null, 'sort' => 8, 'status' => 1,
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\PermissionController', 'method' => 'index'], [
            'name_ar' => 'الصلاحيات', 'name_en' => 'Permission Groups', 'url' => '/admin-panel/permission',
            'type' => 1, 'icon' => null, 'parent_id' => $permission->id, 'sort' => 1, 'status' => 1, 'code' => 'permission-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\PermissionController', 'method' => 'create'], [
            'name_ar' => 'انشاء  صلاحية', 'name_en' => 'Create Permission Group', 'url' => '/admin-panel/permission/create',
            'type' => 1, 'icon' => null, 'parent_id' => $permission->id, 'sort' => 1, 'status' => 1, 'code' => 'permission-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\PermissionController', 'method' => 'edit'], [
            'name_ar' => 'تعديل صلاحية', 'name_en' => 'Edit Permission Group', 'url' => null,
            'type' => 2, 'icon' => null, 'parent_id' => $permission->id, 'sort' => 2, 'status' => 1, 'code' => 'permission-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\PermissionController', 'method' => 'destroy'], [
            'name_ar' => 'حذف صلاحية', 'name_en' => 'Delete Permission Group', 'url' => null,
            'type' => 2, 'icon' => null, 'parent_id' => $permission->id, 'sort' => 3, 'status' => 1, 'code' => 'permission-delete',
        ]);

        $supervisor = Privilege::updateOrCreate(['controller' => 'Panel\Backend\SupervisorController', 'method' => null], [
            'name_ar' => 'المشرفين', 'name_en' => 'Supervisors', 'url' => null, 'type' => 1,
            'icon' => 'fa fa-user', 'parent_id' => null, 'sort' => 9, 'status' => 1, 'code' => 'supervisor',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\SupervisorController', 'method' => 'index'], [
            'name_ar' => 'المشرفين', 'name_en' => 'Supervisors', 'url' => '/admin-panel/supervisor',
            'type' => 1, 'icon' => null, 'parent_id' => $supervisor->id, 'sort' => 1, 'status' => 1, 'code' => 'supervisor-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\SupervisorController', 'method' => 'create'], [
            'name_ar' => ' انشاء مشرف', 'name_en' => ' Create Supervisor', 'url' => '/admin-panel/supervisor/create',
            'type' => 1, 'icon' => null, 'parent_id' => $supervisor->id, 'sort' => 1, 'status' => 1, 'code' => 'supervisor-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\SupervisorController', 'method' => 'edit'], [
            'name_ar' => ' تعديل مشرف', 'name_en' => 'Edit Supervisor', 'url' => null,
            'type' => 2, 'icon' => null, 'parent_id' => $supervisor->id, 'sort' => 2, 'status' => 1, 'code' => 'supervisor-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\SupervisorController', 'method' => 'destroy'], [
            'name_ar' => 'حذف مشرف', 'name_en' => 'Delete Supervisor', 'url' => null,
            'type' => 2, 'icon' => null, 'parent_id' => $supervisor->id, 'sort' => 3, 'status' => 1, 'code' => 'supervisor-delete',
        ]);

        $user = Privilege::updateOrCreate(['controller' => 'Panel\Backend\UserController', 'method' => null], [
            'name_ar' => 'المستخدمين', 'name_en' => 'Users', 'url' => null, 'type' => 1,
            'icon' => 'fa fa-users', 'parent_id' => null, 'sort' => 7, 'status' => 1, 'code' => 'user',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\UserController', 'method' => 'index'], [
            'name_ar' => 'المستخدمين', 'name_en' => 'Users', 'url' => '/admin-panel/users',
            'type' => 1, 'icon' => null, 'parent_id' => $user->id, 'sort' => 1, 'status' => 1, 'code' => 'user-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\UserController', 'method' => 'create'], [
            'name_ar' => ' انشاء مستخدم', 'name_en' => ' Create User', 'url' => '/admin-panel/users/create',
            'type' => 1, 'icon' => null, 'parent_id' => $user->id, 'sort' => 1, 'status' => 1, 'code' => 'user-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\UserController', 'method' => 'edit'], [
            'name_ar' => ' تعديل مستخدم', 'name_en' => 'Edit User', 'url' => null,
            'type' => 2, 'icon' => null, 'parent_id' => $user->id, 'sort' => 2, 'status' => 1, 'code' => 'user-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\UserController', 'method' => 'destroy'], [
            'name_ar' => 'حذف مستخدم', 'name_en' => 'Delete User', 'url' => null,
            'type' => 2, 'icon' => null, 'parent_id' => $user->id, 'sort' => 3, 'status' => 1, 'code' => 'user-delete',
        ]);

        $balance = Privilege::updateOrCreate(['controller' => 'Panel\Backend\BalanceController', 'method' => null], [
            'name_ar' => ' أرصدة المستخدمين', 'name_en' => 'Users Balance', 'url' => null, 'type' => 1,
            'icon' => 'fa fa-money', 'parent_id' => null, 'sort' => 8, 'status' => 1, 'code' => 'balance',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\BalanceController', 'method' => 'index'], [
            'name_ar' => 'أرصدة المستخدمين', 'name_en' => 'Users Balance', 'url' => '/admin-panel/balance',
            'type' => 1, 'icon' => null, 'parent_id' => $balance->id, 'sort' => 1, 'status' => 1, 'code' => 'balance-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\BalanceController', 'method' => 'addBalance'], [
            'name_ar' => 'اضافة رصيد', 'name_en' => 'Add Balance', 'url' => '/admin-panel/add-balance',
            'type' => 1, 'icon' => null, 'parent_id' => $balance->id, 'sort' => 2, 'status' => 1, 'code' => 'add-balance',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\BalanceController', 'method' => 'deductBalance'], [
            'name_ar' => 'خصم رصيد', 'name_en' => 'Deduct Balance', 'url' => '/admin-panel/deduct-balance',
            'type' => 1, 'icon' => null, 'parent_id' => $balance->id, 'sort' => 3, 'status' => 1, 'code' => 'deduct-balance',
        ]);
    }
}
