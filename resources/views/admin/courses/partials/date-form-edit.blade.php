{{-- Partial view for displaying a course date form in edit mode --}}
<div class="card mb-3 border-secondary" id="dateCard-{{ $index }}">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fa;">
        <h6 class="card-title mb-0">
            <i class="bi bi-calendar"></i> Date {{ $index + 1 }}
        </h6>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDate({{ $index }})">
            <i class="bi bi-trash me-1"></i>Remove
        </button>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Start Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error("course_dates.{$index}.start_date") is-invalid @enderror" 
                       name="course_dates[{{ $index }}][start_date]" value="{{ $date->start_date ? $date->start_date->format('Y-m-d') : '' }}" required>
                @error("course_dates.{$index}.start_date")
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label>End Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error("course_dates.{$index}.end_date") is-invalid @enderror" 
                       name="course_dates[{{ $index }}][end_date]" value="{{ $date->end_date ? $date->end_date->format('Y-m-d') : '' }}" required>
                @error("course_dates.{$index}.end_date")
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Date Label (Optional)</label>
                <input type="text" class="form-control" name="course_dates[{{ $index }}][date_label]" 
                       value="{{ $date->date_label }}" placeholder="e.g., Cohort 1, Batch A">
            </div>
            <div class="col-md-6 mb-3">
                <label>Notes (Optional)</label>
                <input type="text" class="form-control" name="course_dates[{{ $index }}][notes]" 
                       value="{{ $date->notes }}" placeholder="Additional information">
            </div>
        </div>

        <!-- Venues for this date -->
        <div class="mt-4">
            <h6 class="mb-3">
                <i class="bi bi-geo-alt"></i> Venues
            </h6>
            <div id="venuesContainer-{{ $index }}" class="mb-3">
                @if($date->venues->count() > 0)
                    @foreach($date->venues as $venueIdx => $venue)
                        <div class="card border-light mb-2" id="venueCard-{{ $index }}-{{ $venueIdx }}">
                            <div class="card-body p-3">
                                <div class="row align-items-end">
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label mb-1">Venue Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" 
                                               name="course_dates[{{ $index }}][venues][{{ $venueIdx }}][venue_name]" 
                                               value="{{ $venue->venue_name }}" placeholder="e.g., Main Campus" required>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label mb-1">Address</label>
                                        <input type="text" class="form-control form-control-sm" 
                                               name="course_dates[{{ $index }}][venues][{{ $venueIdx }}][address]" 
                                               value="{{ $venue->address }}" placeholder="Street address">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label mb-1">City</label>
                                        <input type="text" class="form-control form-control-sm" 
                                               name="course_dates[{{ $index }}][venues][{{ $venueIdx }}][city]" 
                                               value="{{ $venue->city }}" placeholder="City">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100" 
                                                onclick="removeVenue({{ $index }}, {{ $venueIdx }})">
                                            <i class="bi bi-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label mb-1">State/Province</label>
                                        <input type="text" class="form-control form-control-sm" 
                                               name="course_dates[{{ $index }}][venues][{{ $venueIdx }}][state]" 
                                               value="{{ $venue->state }}" placeholder="State">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label mb-1">Country</label>
                                        <input type="text" class="form-control form-control-sm" 
                                               name="course_dates[{{ $index }}][venues][{{ $venueIdx }}][country]" 
                                               value="{{ $venue->country }}" placeholder="Country">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label mb-1">Capacity</label>
                                        <input type="number" class="form-control form-control-sm" 
                                               name="course_dates[{{ $index }}][venues][{{ $venueIdx }}][capacity]" 
                                               value="{{ $venue->capacity }}" placeholder="Number of seats" min="1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label mb-1">Notes</label>
                                        <input type="text" class="form-control form-control-sm" 
                                               name="course_dates[{{ $index }}][venues][{{ $venueIdx }}][notes]" 
                                               value="{{ $venue->notes }}" placeholder="Optional notes">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-light border">
                        <i class="bi bi-info-circle"></i> No venues added. Click "Add Venue" to add one.
                    </div>
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addVenue({{ $index }}); return false;">
                <i class="bi bi-plus-circle me-1"></i>Add Venue
            </button>
        </div>
    </div>
</div>
