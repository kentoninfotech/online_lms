<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(['key' => 'reschedule_limit'], ['value' => 4]);
        Setting::updateOrCreate(['key' => 'reschedule_guard_time_minutes'], ['value' => 120]);
        Setting::updateOrCreate(['key' => 'attendance_grace_period_minutes'], ['value' => 10]);
        Setting::updateOrCreate(['key' => 'billing_grace_period_days'], ['value' => 7]);
        Setting::updateOrCreate(['key' => 'recurrence_horizon_days'], ['value' => 30]);
        Setting::updateOrCreate(['key' => 'zoom_meeting_horizon_days'], ['value' => 7]);
        Setting::updateOrCreate(['key' => 'attendance_min_percentage'], ['value' => 0]); // 0 = disabled (100 = strict)
    }
}
