@extends('layouts.app')

@section('title', 'View Message - ' . $contact->name)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Message from {{ $contact->name }}</h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.feedback.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Feedback
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Message View -->
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">{{ $contact->subject ?? 'No Subject' }}</h5>
                            <small class="text-muted">
                                Received: {{ $contact->created_at->format('F j, Y \a\t g:i A') }}
                            </small>
                        </div>
                        <div class="col-auto">
                            <span class="badge 
                                @if($contact->status === 'unread') bg-warning
                                @elseif($contact->status === 'replied') bg-success
                                @else bg-secondary
                                @endif
                            ">
                                {{ ucfirst($contact->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Sender Info -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-person-circle me-2"></i>{{ $contact->name }}
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="d-block text-muted mb-2">
                                    <strong>Email:</strong><br>
                                    <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                </small>
                            </div>
                            @if($contact->phone)
                            <div class="col-md-6">
                                <small class="d-block text-muted mb-2">
                                    <strong>Phone:</strong><br>
                                    <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Message Content -->
                    <div class="message-body">
                        <h6 class="fw-bold mb-3">Message:</h6>
                        <div class="bg-light p-4 rounded border">
                            {!! nl2br(e($contact->message)) !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Previous Responses -->
            @if($contact->responses->count() > 0)
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-dots me-2"></i>Previous Responses ({{ $contact->responses->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($contact->responses as $response)
                    <div class="mb-4 pb-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <strong>{{ $response->admin->name }}</strong>
                            <small class="text-muted">
                                {{ $response->responded_at?->format('M d, Y \a\t g:i A') ?? 'Draft' }}
                            </small>
                        </div>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($response->response_text)) !!}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Response Form -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-reply-fill me-2"></i>Send Response
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.feedback.respond', $contact) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Response Message <span class="text-danger">*</span></label>
                            <div id="editor" class="border rounded"
                                data-height="400"
                                data-placeholder="Type your response here..."
                            ></div>
                            <textarea id="response_text_hidden" name="response_text" style="display:none;">{{ old('response_text') }}</textarea>
                            @error('response_text')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <small class="d-block text-muted mb-3">
                            💡 Tip: Use formatting to make your response more professional
                        </small>

                        <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold">
                            <i class="bi bi-send me-2"></i>Send Response
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
let quillEditor = null;

function initializeQuill() {
    // Initialize Quill editor
    quillEditor = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'blockquote', 'code-block'],
                ['clean']
            ]
        },
        placeholder: 'Type your response here...'
    });

    // Set initial content if editing
    const initialContent = document.getElementById('response_text_hidden').value;
    if (initialContent && initialContent.trim()) {
        quillEditor.root.innerHTML = initialContent;
    }

    // Bind form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
}

function handleFormSubmit(e) {
    // Prevent default to validate first
    e.preventDefault();
    
    if (!quillEditor) {
        console.error('Quill editor not initialized');
        alert('There was an error with the form. Please refresh and try again.');
        return false;
    }
    
    // Get the HTML content from Quill
    const htmlContent = quillEditor.root.innerHTML;
    const textContent = quillEditor.getText().trim();
    
    // Validate that there's actual text (not just HTML tags or empty)
    if (!textContent || textContent.length < 10) {
        alert('Please enter at least 10 characters in the response message.');
        quillEditor.focus();
        return false;
    }
    
    // Set the hidden field with the HTML content
    document.getElementById('response_text_hidden').value = htmlContent;
    
    // Now submit the form
    this.submit();
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeQuill);
} else {
    initializeQuill();
}
</script>
@endpush
@endsection
