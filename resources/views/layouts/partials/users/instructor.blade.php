<div class="row g-3 mt-2">
    <div class="col-md-6">
        <label class="form-label small text-muted">Address</label>
        <input type="text" name="address" class="form-control" 
               value="{{ old('address', $instructor->address ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small text-muted">Phone</label>
        <input type="text" name="number" class="form-control" 
               value="{{ old('number', $instructor->number ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small text-muted">Specialization</label>
        <input type="text" name="specialization" class="form-control" 
               value="{{ old('specialization', $instructor->specialization ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Bio</label>
        <textarea name="bio" class="form-control">{{ old('bio', $instructor->bio) }}</textarea>
    </div>
</div>
