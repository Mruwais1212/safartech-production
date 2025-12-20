<?php

namespace Database\Seeders;

use App\Models\Panel\Privilege;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewPrivilegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        auth('admin')->login(User::find(1));
        $refund = Privilege::updateOrCreate(['controller' => 'Panel\Auth\RefundController', 'method' => null], [
            'name_ar' => 'استرجاع المبالغ',
            'name_en' => 'Refund Amounts',
            'url' => null,
            'code' => 'refund',
            'type' => 1,
            'icon' => 'fa fa-money',
            'parent_id' => null,
            'sort' => 5,
            'status' => 1,
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\RefundController', 'method' => 'index'], [
            'name_ar' => 'استرجاع المبالغ',
            'name_en' => 'Refund Amounts',
            'url' => '/admin-panel/refunds',
            'code' => 'refunds-index',
            'type' => 1,
            'icon' => null,
            'parent_id' => $refund->id,
            'sort' => 1,
            'status' => 1,
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\RefundController', 'method' => 'edit'], [
            'name_ar' => 'تعديل استرجاع المبالغ',
            'name_en' => 'Edit Refunds status',
            'url' => null,
            'type' => 2,
            'code' => 'refunds-edit',
            'icon' => null,
            'parent_id' => $refund->id,
            'sort' => 2,
            'status' => 1,
        ]);

    }
}
