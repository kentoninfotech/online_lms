<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateSettingRequest;
use App\Services\SettingService;
use App\Models\Setting;

class SettingController extends Controller
{
    protected $settings;

    // canonical keys we manage (keeps order stable)
    private array $keys = [
        'reschedule_limit',
        'class_reminders_minutes',
        'reschedule_guard_time_minutes',
        'attendance_grace_period_minutes',
        'billing_grace_period_days',
        'subscription_expiry_warning_days',
        'recurrence_horizon_days',
        'zoom_meeting_horizon_days',
        'attendance_min_percentage',
    ];

    private array $descriptions = [
        'reschedule_limit' => 'Maximum number of reschedules allowed per month.',
        'class_reminders_minutes' => 'Minutes before class to send reminders.',
        'reschedule_guard_time_minutes' => 'Minimum notice before class (in minutes) to allow reschedule. e.g 1140 minutes = 24 hours',
        'attendance_grace_period_minutes' => 'Grace period (in minutes) before a student is marked late.',
        'billing_grace_period_days' => 'Days after subscription expiry before marking overdue.',
        'subscription_expiry_warning_days' => 'How many days before expiry to warn the student/parent.',
        'recurrence_horizon_days' => 'Number of days ahead to generate class occurrences.',
        'zoom_meeting_horizon_days' => 'Number of days ahead to create Zoom meetings.',
        'attendance_min_percentage' => 'Minimum required attendance % (0 disables enforcement).',
    ];

    /**
     * Constructor to inject SettingService.
     */
    public function __construct(SettingService $settings)
    {
        $this->settings = $settings;
    }
    /**
     * Display a listing of settings.
     */
    public function index()
    {
        // $settings = Setting::whereIn('key', array_keys($this->descriptions))
        //     ->pluck('value', 'key')
        //     ->toArray();

        // return view('dashboard.admin.settings', [
        //     'settings' => $settings,
        //     'descriptions' => $this->descriptions,
        // ]);

        // fetch existing settings as associative array key => value
        $settings = Setting::whereIn('key', $this->keys)->pluck('value', 'key')->toArray();

        // ensure we always provide a value for each key (avoids undefined index in view)
        foreach ($this->keys as $k) {
            if (! array_key_exists($k, $settings)) {
                $settings[$k] = '';
            }
        }

        dd(function_exists('getUserTimezone'));

        return view('dashboard.admin.settings', [
            'settings' => $settings,
            'descriptions' => $this->descriptions,
            'keys' => $this->keys,
        ]);


    }
    /**
     * Update a specific setting.
     */
    public function update(UpdateSettingRequest $request)
    {
        // Expect 'settings' => [ key => value, ... ]
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'required',
        ]);

        foreach ($request->input('settings') as $key => $value) {
            // skip unknown keys (safety)
            if (! in_array($key, $this->keys)) {
                continue;
            }
            Setting::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        }

        // clear cache if you use caching for settings (optional)
        // Cache::forget('settings');

        return redirect()->back()
                         ->with('success', 'Settings updated successfully.');
    }
}
