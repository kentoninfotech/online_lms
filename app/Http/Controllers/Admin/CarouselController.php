<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarouselController extends Controller
{
    /**
     * Display carousel management
     */
    public function index()
    {
        $carousel = HomepageSetting::where('section', 'carousel')->orderBy('sort_order')->get();
        return view('admin.carousel.index', compact('carousel'));
    }

    /**
     * Upload carousel image
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|url',
        ]);

        // Store image in public/uploads/carousel
        $filename = time() . '_' . $request->file('image')->getClientOriginalName();
        $imagePath = $request->file('image')->storeAs('uploads/carousel', $filename, 'public');
        
        // Get the next sort order
        $nextSort = HomepageSetting::where('section', 'carousel')->max('sort_order') ?? 0;
        $nextSort++;

        $setting = HomepageSetting::create([
            'section' => 'carousel',
            'key' => 'carousel_' . time(),
            'value' => $request->input('title'),
            'description' => $request->input('description'),
            'image_path' => $imagePath,
            'button_text' => $request->input('button_text'),
            'button_link' => $request->input('button_link'),
            'sort_order' => $nextSort,
            'is_active' => true,
            'data_type' => 'image'
        ]);

        return redirect()->back()
            ->with('success', 'Carousel image uploaded successfully');
    }

    /**
     * Update carousel item
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $carousel = HomepageSetting::findOrFail($id);

        $data = [
            'value' => $request->input('title'),
            'description' => $request->input('description'),
            'button_text' => $request->input('button_text'),
            'button_link' => $request->input('button_link'),
            'is_active' => (bool) $request->input('is_active', false)
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($carousel->image_path && Storage::disk('public')->exists($carousel->image_path)) {
                Storage::disk('public')->delete($carousel->image_path);
            }
            // Store new image in public/uploads/carousel
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $data['image_path'] = $request->file('image')->storeAs('uploads/carousel', $filename, 'public');
        }

        $carousel->update($data);

        return redirect()->back()
            ->with('success', 'Carousel image updated successfully');
    }

    /**
     * Delete carousel item
     */
    public function destroy($id)
    {
        $carousel = HomepageSetting::findOrFail($id);
        
        // Delete image file if exists
        if ($carousel->image_path && file_exists(public_path($carousel->image_path))) {
            unlink(public_path($carousel->image_path));
        }
        
        $carousel->delete();

        return redirect()->back()
            ->with('success', 'Carousel image deleted successfully');
    }

    /**
     * Reorder carousel items
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer'
        ]);

        foreach ($request->input('order') as $index => $id) {
            HomepageSetting::find($id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
