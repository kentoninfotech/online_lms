<!-- Manage Parents for Student -->
<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Whoops! Something went wrong.</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Current Parents -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Current Parents</h6>
                @if ($student && $student->parents->count() > 0)
                    <div class="list-group">
                        @foreach ($student->parents as $parent)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $parent->name }}</h6>
                                    <small class="text-muted">{{ $parent->user->email }} • {{ $parent->number ?? 'N/A' }}</small>
                                </div>
                                <form method="POST" action="{{ route('student.remove-parent', [$student->id, $parent->id]) }}" 
                                      class="d-inline" onsubmit="return confirm('Remove this parent?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="ph ph-trash"></i> Remove
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No parents added yet.</p>
                @endif
            </div>

            <hr class="my-4">

            <!-- Add New Parent -->
            <div>
                <h6 class="fw-bold mb-3">Add a Parent</h6>
                <form method="POST" action="{{ route('student.add-parent', $student->id) }}">
                    @csrf
                    <input type="hidden" name="_tab" value="parents">

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Select Parent</label>
                        <select name="parent_id" id="parent_id" class="form-select" required>
                            <option value="">-- Select a parent --</option>
                            @php
                                // Get all parents
                                $allParents = \App\Models\ParentModel::all();
                                // Get parents already linked to this student
                                $linkedParentIds = $student->parents()->pluck('parent_id')->toArray();
                            @endphp
                            @foreach ($allParents as $parent)
                                @if (!in_array($parent->id, $linkedParentIds))
                                    <option value="{{ $parent->id }}">
                                        {{ $parent->name }} ({{ $parent->user->email }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('parent_id')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-plus me-2"></i> Add Parent
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
