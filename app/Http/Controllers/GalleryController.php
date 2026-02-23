<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display all published galleries
     */
    public function index()
    {
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
