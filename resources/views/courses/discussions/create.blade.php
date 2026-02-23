@extends('layouts.app')

@section('title', 'New Discussion - ' . $course->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Start a New Discussion</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('courses.discussions.store', $course) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="title" class="form-label">
                                <strong>Discussion Title <span class="text-danger">*</span></strong>
                            </label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   placeholder="What would you like to discuss?"
                                   value="{{ old('title') }}"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label">
                                <strong>Discussion Content <span class="text-danger">*</span></strong>
                            </label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="6" 
                                      placeholder="Share your thoughts, questions, or insights..."
                                      required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Be respectful and constructive in your discussion.</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Community Guidelines:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Be respectful to other participants</li>
                                <li>Stay on topic</li>
                                <li>Use clear and professional language</li>
                                <li>Share knowledge and help others</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>Create Discussion
                            </button>
                            <a href="{{ route('courses.discussions.index', $course) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Discussions
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
