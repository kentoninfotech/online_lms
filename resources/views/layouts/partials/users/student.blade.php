<div class="row g-3 mt-2">
    <div class="col-md-6">
        <label class="form-label small text-muted">Address</label>
        <input type="text" name="address" class="form-control" 
               value="{{ old('address', $student->address ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small text-muted">Phone</label>
        <input type="text" name="number" class="form-control" 
               value="{{ old('number', $student->number ?? '') }}">
    </div>
</div>
