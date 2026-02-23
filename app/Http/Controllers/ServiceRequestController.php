<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    /**
     * Store a new service request
     */
    public function store(Request $request, Service $service)
    {
        if (!$service->published) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'message' => 'nullable|string',
        ]);

        $validated['service_id'] = $service->id;
        
        ServiceRequest::create($validated);

        return redirect()->back()
            ->with('success', 'Thank you! We have received your request and will contact you soon.');
    }
}
