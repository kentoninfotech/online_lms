<!-- Add Child to Parent -->
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
            <!-- Current Children -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Current Children</h6>
                @if ($parent && $parent->students->count() > 0)
                    <div class="list-group">
                        @foreach ($parent->students as $student)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $student->name }}</h6>
                                    <small class="text-muted">{{ $student->user->email }}</small>
                                </div>
                                <form method="POST" action="{{ route('parent.remove-child', [$parent->id, $student->id]) }}" 
                                      class="d-inline" onsubmit="return confirm('Remove this child?');">
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
                    <p class="text-muted">No children added yet.</p>
                @endif
            </div>

            <hr class="my-4">

            <!-- Add New Child -->
            <div>
                <h6 class="fw-bold mb-3">Add a Child</h6>
                <form method="POST" action="{{ route('parent.add-child', $parent->id) }}">
                    @csrf
                    <input type="hidden" name="_tab" value="add-child">

                    <div class="mb-3">
                        <label for="student_id" class="form-label">Select Student</label>
                        <select name="student_id" id="student_id" class="form-select" required>
                            <option value="">-- Select a student --</option>
                            @php
                                // Get all students
                                $allStudents = \App\Models\Student::all();
                                // Get students already linked to this parent
                                $linkedStudentIds = $parent->students()->pluck('student_id')->toArray();
                            @endphp
                            @foreach ($allStudents as $student)
                                @if (!in_array($student->id, $linkedStudentIds))
                                    <option value="{{ $student->id }}">
                                        {{ $student->name }} ({{ $student->user->email }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('student_id')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-plus me-2"></i> Add Child
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
