<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\HomepageSetting;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display all published services
     */
    public function index()
    {
        // Check if services page is enabled
        $showServices = HomepageSetting::getSetting('visibility', 'show_services', true);
        if (!$showServices) {
            abort(404, 'Services page is not available.');
        }
        
        $services = Service::published()->orderBy('created_at', 'desc')->get();
        return view('services.index', compact('services'));
    }

    /**
     * Display a single service
     */
    public function show(Service $service)
    {
        if (!$service->published) {
            abort(404);
        }
        
        return view('services.show', compact('service'));
    }
}
