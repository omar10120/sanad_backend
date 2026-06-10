<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * ------start---- PermissionTableSeeder ---------
         **/

         $permissions = PermissionEnum::getAllPermissions();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        //-------end----- PermissionTableSeeder ---------//
    }
}
