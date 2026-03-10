<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\Panel\Privilege;
use App\Models\Panel\PrivilegeGroup;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        if (!User::where('email','superadmin@gmail.com')->orWhere('phone','0123456789')->count() > 0) {
            User::create([
                'name' => 'SuperAdmin', 'phone' => '0123456789', 'email' => 'superadmin@gmail.com',
                'password' => bcrypt('password'), 'user_type_id' => UserType::SUPER_ADMIN, 'group_privilege_id' => 1,
            ]);
        }

        if (!User::where('email','admin@gmail.com')->orWhere('phone','0123456788')->count() > 0) {
            User::create([
                'name' => 'Admin', 'phone' => '0123456788', 'email' => 'admin@gmail.com',
                'password' => bcrypt('password'), 'user_type_id' => UserType::ADMIN, 'group_privilege_id' => 2,
            ]);
        }

        auth('admin')->login(User::find(1));
        $this->call([
            PrivilegeSeeder::class,
            BusinessPrivilegeSeeder::class,
            SettingSeeder::class,
            BusinessSeeder::class,
            FixedSeeder::class,
        ]);

        $privilege_group = PrivilegeGroup::create([
            'name_ar' => 'سوبر ادمن', 'name_en' => 'Super Admin', 'guard' => 1,
        ]);

        $privilege_group->privileges()->sync(Privilege::where('guard', 1)->pluck('id')->toArray());

        $admin_privileges = Privilege::whereNotIn('id', array_merge(range(1, 10), range(13, 33)))->where('guard', 1)->pluck('id')->toArray();

        $privilege_group = PrivilegeGroup::create([
            'name_ar' => ' ادمن', 'name_en' => ' Admin', 'guard' => 1,
        ]);

        $privilege_group->privileges()->sync($admin_privileges);
    }
}
