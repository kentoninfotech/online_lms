<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\HomepageSetting;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display all published galleries
     */
    public function index()
    {
        // Check if galleries page is enabled
        $showGalleries = HomepageSetting::getSetting('visibility', 'show_galleries', true);
        if (!$showGalleries) {
            abort(404, 'Galleries page is not available.');
        }
        
        $galleries = Gallery::published()->orderBy('created_at', 'desc')->get();
        return view('galleries.index', compact('galleries'));
    }

    /**
     * Display a single gallery
     */
    public function show(Gallery $gallery)
    {
        if (!$gallery->published) {
            abort(404);
        }
        
        return view('galleries.show', compact('gallery'));
    }
}
