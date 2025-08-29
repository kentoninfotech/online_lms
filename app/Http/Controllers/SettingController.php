<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $settings;

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
        $settings = $this->settings->all();
        return view('admin.settings', compact('settings'));
    }
    /**
     * Update a specific setting.
     */
    public function update(UpdateSettingRequest $request)
    {
        $this->settings->set($request->key, $request->value);
        return redirect()->route('settings.index')->with(['success' => 'Setting updated.']);
    }
}
