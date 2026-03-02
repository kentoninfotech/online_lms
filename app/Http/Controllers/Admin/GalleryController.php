<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    /**
     * Display all galleries
     */
    public function index()
    {
        $galleries = Gallery::latest()->paginate(15);
        return view('admin.galleries.index', compact('galleries'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.galleries.create');
    }

    /**
     * Store a new gallery
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_name' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'published' => 'boolean',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['title']) . '-' . time();

        $gallery = Gallery::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $filename = time() . '_' . ($index + 1) . '_' . $image->getClientOriginalName();
                $path = 'storage/' . $image->storeAs('uploads/galleries', $filename, 'public');
                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => $path,
                    'sequence' => $index,
                ]);
            }
        }

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Gallery created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Gallery $gallery)
    {
        $gallery->load('images');
        return view('admin.galleries.edit', compact('gallery'));
    }

    /**
     * Update a gallery
     */
    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_name' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'published' => 'boolean',
        ]);

        $gallery->update($validated);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $lastSequence = $gallery->images()->max('sequence') ?? 0;
            
            foreach ($request->file('images') as $index => $image) {
                $filename = time() . '_' . ($index + 1) . '_' . $image->getClientOriginalName();
                $path = 'storage/' . $image->storeAs('uploads/galleries', $filename, 'public');
                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => $path,
                    'sequence' => $lastSequence + $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Gallery updated successfully');
    }

    /**
     * Delete a gallery
     */
    public function destroy(Gallery $gallery)
    {
        // Delete all images
        foreach ($gallery->images as $image) {
            if (file_exists(public_path($image->image_path))) {
                unlink(public_path($image->image_path));
            }
        }

        $gallery->delete();

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Gallery deleted successfully');
    }

    /**
     * Delete a gallery image
     */
    public function deleteImage(GalleryImage $image)
    {
        $galleryId = $image->gallery_id;
        
        if (file_exists(public_path($image->image_path))) {
            unlink(public_path($image->image_path));
        }

        $image->delete();

        return redirect()->back()
            ->with('success', 'Image deleted successfully');
    }

    /**
     * Reorder gallery images
     */
    public function reorderImages(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        foreach ($validated['order'] as $sequence => $imageId) {
            GalleryImage::where('id', $imageId)->update(['sequence' => $sequence]);
        }

        return response()->json(['success' => true]);
    }
}
