<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //------start---- CreateAdminUserSeeder ---------//

        $student1 = Student::create([
            'first_name' => 'محمد لويس',
            'last_name' => 'الشعلان',
            'father_name' => 'محمد علاء',
            'city' => 'damascus',
            'type_id' => 1,
            'phone' => '0952732752',
            'email' => 'magholm302@gmail.com',
            'phone_verified_at' => now(),
            'password' => bcrypt('Sanad2025.'),
        ]);

        $student2 = Student::create([
            'first_name' => 'محمد علاء',
            'last_name' => 'الشحرور',
            'father_name' => 'عبد الرحمن',
            'city' => 'damascus_suburb',
            'type_id' => 1,
            'phone' => '0964630090',
            'email' => 'alaaalshahror@gmail.com',
            'phone_verified_at' => now(),
            'password' => bcrypt('12345678.'),
            'photo' => 'alaa.jpg',
        ]);

        $user1 = User::create([
            'name_ar' => 'محمد لويس الشعلان',
            'name_en' => 'Mohammed alshaalan',
            'phone' => '0952732752',
            'email' => 'magholm302@gmail.com',
            'password' => bcrypt('Sanad2025'),
        ]);

        $user2 = User::create([
            'name_ar' => 'محمد علاء الشحرور',
            'name_en' => 'Mohammad Alaa alShahrour',
            'phone' => '0964630090',
            'email' => 'alaaalshahror@gmail.com',
            'password' => bcrypt('12345678'),
            'photo' => 'alaa.jpg',
        ]);

        $role1 = Role::create(['name' => 'Owner']);
        $permissions1 = Permission::pluck('id','id')->all();
        $role1->syncPermissions($permissions1);
        $user1->assignRole([$role1->id]);
        $user2->assignRole([$role1->id]);

        //-------end----- CreateAdminUserSeeder ---------//
    }
}
