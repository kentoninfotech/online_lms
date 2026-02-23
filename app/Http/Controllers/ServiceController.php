<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display all published services
     */
    public function index()
    {
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
