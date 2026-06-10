<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $students = DB::table('students')
            ->whereNotNull('device_id')
            ->where('device_id', '!=', '')
            ->get();

        foreach ($students as $student) {
            $device = DB::table('devices')
                ->where('device_id', $student->device_id)
                ->first();
            
            if (!$device) {
                $deviceId = DB::table('devices')->insertGetId([
                    'device_id' => $student->device_id,
                    'os_name' => 'Android',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $deviceId = $device->id;
            }
            
            DB::table('student_devices')->insertOrIgnore([
                'student_id' => $student->id,
                'device_id' => $deviceId,
                'is_current' => true,
                'first_login_at' => $student->created_at,
                'last_login_at' => $student->updated_at,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('student_devices')->truncate();
        DB::table('devices')->truncate();
    }
};
