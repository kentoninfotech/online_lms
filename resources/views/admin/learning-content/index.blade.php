@extends('layouts.app')

@section('title', 'Learning Content - Admin')

@section('content')

<div class="pc-container" style="margin-top: -2rem;">
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Learning Content</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h4 class="m-0">Learning Content Management</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="pc-container" style="margin-top: 0.5rem;">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>All Course Content</h5>
                    <span class="d-block text-muted" style="font-size: 0.875rem;"><i class="bi bi-info-circle"></i> Total Content: {{ $contents->total() }}</span>
                </div>
                <div class="card-body">
                    @if($contents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Course</th>
                                        <th>Type</th>
                                        <th>Sequence</th>
                                        <th>Published</th>
                                        <th>Created</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contents as $content)
                                        <tr>
                                            <td>
                                                <strong>{{ Str::limit($content->title, 40) }}</strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.courses.show', $content->course) }}" class="badge badge-light-primary">
                                                    {{ $content->course->title }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-secondary">{{ ucfirst($content->content_type) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-info">#{{ $content->sequence }}</span>
                                            </td>
                                            <td>
                                                @if($content->is_published)
                                                    <span class="badge badge-light-success">Published</span>
                                                @else
                                                    <span class="badge badge-light-warning">Draft</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $content->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.learning-content.show', $content) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                <a href="{{ route('admin.course-contents.edit', [$content->course, $content]) }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.course-contents.destroy', [$content->course, $content]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $contents->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No learning content found. <a href="{{ route('admin.courses.index') }}">Create content for your courses</a>.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
