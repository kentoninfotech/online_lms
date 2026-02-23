@extends('layouts.landing')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">

<div class="container mx-auto px-4 py-12">
    <div class="mb-8">
        <a href="{{ route('galleries.index') }}" class="text-blue-500 hover:underline">← Back to Galleries</a>
    </div>

    <!-- Gallery Info -->
    <div class="mb-12">
        <h1 class="text-4xl font-bold mb-4">{{ $gallery->title }}</h1>
        
        <div class="flex flex-wrap gap-4 text-gray-600 mb-6">
            @if($gallery->event_name)
                <div class="flex items-center">
                    <span class="text-xl mr-2">📍</span>
                    <span>{{ $gallery->event_name }}</span>
                </div>
            @endif
            
            @if($gallery->event_date)
                <div class="flex items-center">
                    <span class="text-xl mr-2">📅</span>
                    <span>{{ $gallery->event_date->format('F d, Y') }}</span>
                </div>
            @endif
            
            <div class="flex items-center">
                <span class="text-xl mr-2">📸</span>
                <span>{{ $gallery->images()->count() }} Photos</span>
            </div>
        </div>

        @if($gallery->description)
            <p class="text-gray-700 text-lg">{{ $gallery->description }}</p>
        @endif
    </div>

    <!-- Slideshow Controls -->
    <div class="mb-6 flex gap-4">
        <button id="slideshow-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition">
            ▶️ Start Slideshow
        </button>
        <button id="slideshow-stop" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded transition hidden" style="display: none;">
            ⏹️ Stop Slideshow
        </button>
    </div>

    <!-- Gallery Grid with Lightbox -->
    @if($gallery->images()->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($gallery->images as $image)
                <a href="{{ asset($image->image_path) }}" 
                   data-lightbox="gallery-{{ $gallery->id }}" 
                   data-title="{{ $image->caption ?? '' }}"
                   class="group relative overflow-hidden rounded-lg shadow-lg hover:shadow-xl transition">
                    <img src="{{ asset($image->image_path) }}" 
                         alt="{{ $image->caption ?? 'Gallery image' }}"
                         class="w-full h-48 object-cover group-hover:scale-110 transition duration-300">
                    <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-40 transition flex items-center justify-center">
                        <span class="text-white text-3xl opacity-0 group-hover:opacity-100 transition">🔍</span>
                    </div>
                    @if($image->caption)
                        <p class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2 text-sm truncate opacity-0 group-hover:opacity-100 transition">
                            {{ $image->caption }}
                        </p>
                    @endif
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-600 text-lg">No images in this gallery yet.</p>
        </div>
    @endif
</div>

<!-- Lightbox Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/lightbox.min.js"></script>

<script>
let slideshowRunning = false;
let slideshowTimeout;
let currentImageIndex = 0;

document.getElementById('slideshow-btn').addEventListener('click', function() {
    startSlideshow();
});

document.getElementById('slideshow-stop').addEventListener('click', function() {
    stopSlideshow();
});

function startSlideshow() {
    if (slideshowRunning) return;
    
    slideshowRunning = true;
    document.getElementById('slideshow-btn').style.display = 'none';
    document.getElementById('slideshow-stop').style.display = 'inline-block';
    
    // Get all lightbox links
    const links = document.querySelectorAll('[data-lightbox]');
    if (links.length === 0) return;
    
    // Click the first image to start
    links[0].click();
    
    // Set up auto-advance
    const advanceSlideshow = () => {
        if (!slideshowRunning) return;
        
        const next = document.querySelector('[data-lightbox] ~ a[data-lightbox], .lb-next');
        if (document.querySelector('.lb-next').offsetParent !== null) {
            document.querySelector('.lb-next').click();
        } else {
            // Loop back to first image
            currentImageIndex = 0;
        }
        
        slideshowTimeout = setTimeout(advanceSlideshow, 3000); // 3 seconds per image
    };
    
    slideshowTimeout = setTimeout(advanceSlideshow, 3000);
}

function stopSlideshow() {
    slideshowRunning = false;
    clearTimeout(slideshowTimeout);
    document.getElementById('slideshow-btn').style.display = 'inline-block';
    document.getElementById('slideshow-stop').style.display = 'none';
    
    // Close lightbox if open
    const closeBtn = document.querySelector('.lb-close');
    if (closeBtn) {
        closeBtn.click();
    }
}

// Stop slideshow when user manually closes lightbox
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('lb-close')) {
        stopSlideshow();
    }
});
</script>

<!-- Related Galleries -->
<div class="mt-16">
    <h2 class="text-3xl font-bold mb-8">Other Galleries</h2>
    @php
        $relatedGalleries = \App\Models\Gallery::published()
            ->where('id', '!=', $gallery->id)
            ->limit(3)
            ->get();
    @endphp
    
    @if($relatedGalleries->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6">
            @foreach($relatedGalleries as $relGallery)
                <a href="{{ route('galleries.show', $relGallery) }}" class="group">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                        @if($relGallery->images()->first())
                            <div class="relative h-40 overflow-hidden bg-gray-300">
                                <img src="{{ asset($relGallery->images()->first()->image_path) }}" alt="{{ $relGallery->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                            </div>
                        @endif
                        <div class="p-4">
                            <h4 class="font-semibold group-hover:text-blue-500 transition">{{ $relGallery->title }}</h4>
                            <p class="text-sm text-gray-600">{{ $relGallery->images()->count() }} photos</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
