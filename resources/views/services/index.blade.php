@extends('layouts.landing')
@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-4">Our Services</h1>
        <p class="text-gray-600 text-lg">Explore our professional services designed to meet your needs</p>
    </div>

    @if($services->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
            @foreach($services as $service)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    @if($service->featured_image)
                        <img src="{{ asset($service->featured_image) }}" alt="{{ $service->title }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                            <span class="text-gray-500">No image</span>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">{{ $service->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $service->subtitle }}</p>
                        
                        <p class="text-gray-700 mb-6">{{ Str::limit(strip_tags($service->body), 120) }}</p>
                        
                        <a href="{{ route('services.show', $service) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition">
                            Learn More
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-600 text-lg">No services available at the moment.</p>
        </div>
    @endif
</div>
@endsection
