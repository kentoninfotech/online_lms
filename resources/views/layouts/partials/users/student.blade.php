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
    <div class="col-md-6">
        <label class="form-label small text-muted">Date of Birth</label>
        <input type="date" name="dob" class="form-control" 
               value="{{ old('dob', $student->dob ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small text-muted">Sex</label>
        <select name="gender" id="gender" class="form-control">
            <option value="">--Select Gender--</option>
            <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
        </select>
    </div>
</div>
