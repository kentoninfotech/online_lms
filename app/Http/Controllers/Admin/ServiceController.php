<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Display all services
     */
    public function index()
    {
        $services = Service::latest()->paginate(15);
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a new service
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'body' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'published' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            $filename = time() . '_' . $request->file('featured_image')->getClientOriginalName();
            $request->file('featured_image')->move(public_path('uploads/services'), $filename);
            $validated['featured_image'] = 'uploads/services/' . $filename;
        }

        // Generate slug
        $validated['slug'] = Str::slug($validated['title']) . '-' . time();

        Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update a service
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'body' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'published' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($service->featured_image && file_exists(public_path($service->featured_image))) {
                unlink(public_path($service->featured_image));
            }
            
            $filename = time() . '_' . $request->file('featured_image')->getClientOriginalName();
            $request->file('featured_image')->move(public_path('uploads/services'), $filename);
            $validated['featured_image'] = 'uploads/services/' . $filename;
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully');
    }

    /**
     * Delete a service
     */
    public function destroy(Service $service)
    {
        // Delete featured image
        if ($service->featured_image && file_exists(public_path($service->featured_image))) {
            unlink(public_path($service->featured_image));
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully');
    }

    /**
     * View service requests
     */
    public function requests(Service $service)
    {
        $requests = $service->requests()->latest()->paginate(15);
        return view('admin.services.requests', compact('service', 'requests'));
    }
}
