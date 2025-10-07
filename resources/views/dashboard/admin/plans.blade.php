@extends('layouts.app')

@section('title', 'Plan')

@section('content')

<div class="page-header">
    <div class="page-block card mb-3">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
                <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal" data-bs-target="#createPlanModal">
                    <i class="ph ph-plus"></i> Add Plan
                </button>
                <h4 class="mb-0">Plan</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('admin.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Plan</li>
            </ul>
        </div>
        </div>
    </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <table class="table table-striped table-bordered">
        <thead class="table-light">
            <tr>
                <th>Plan</th>
                <th>Price</th>
                <th>Type</th>
                <th>Duration Count</th>
                <th>Reschedule Limit</th>
                <th>Payment Grace Days</th>
                {{-- ADDED: Action column --}}
                <th style="width: 120px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($plans as $plan)
                <tr>
                    <td>{{ $plan->name }}</td>
                    <td>₦ {{ number_format($plan->price, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $plan->duration_type === 'monthly' ? 'success' : ($plan->duration_type === 'weekly' ? 'primary' : 'warning') }}">
                            {{ ucfirst($plan->duration_type) }}
                        </span>
                    </td>
                    <td>{{ $plan->duration_count ?? 'N/A' }}</td>
                    <td>{{ $plan->reschedule_limit ?? 'N/A' }}</td>
                    <td>{{ $plan->payment_grace_days ?? 'N/A' }}</td>
                    
                    {{-- ADDED: Action column with Edit button --}}
                    <td>
                        <button class="btn btn-sm btn-info edit-plan-btn"
                            data-bs-toggle="modal" 
                            data-bs-target="#editPlanModal"
                            data-id="{{ $plan->id }}"
                            data-name="{{ $plan->name }}"
                            data-price="{{ $plan->price }}"
                            data-duration-type="{{ $plan->duration_type }}"
                            data-duration-count="{{ $plan->duration_count }}"
                            data-reschedule-limit="{{ $plan->reschedule_limit }}"
                            data-payment-grace-days="{{ $plan->payment_grace_days }}"
                            data-features="{{ $plan->features }}"
                        >
                            <i class="ph ph-pencil"></i>
                        </button>
                        
                        {{-- Placeholder for Delete button --}}
                        <form action="{{ route('plan.destroy', $plan->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                <i class="ph ph-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">No plan found.</td></tr>
            @endforelse
        </tbody>
    </table>
    
    </div>
</div>
        

{{-- ========================================================================================================= --}}
{{-- Create Plan Modal --}}
{{-- ========================================================================================================= --}}

<div class="modal fade" id="createPlanModal" tabindex="-1" aria-labelledby="createPlanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('plan.create') }}" method="POST">
                @csrf
                @if ($errors->any() && session('error_modal_type') == 'create')
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="modal-header">
                    <h5 class="modal-title" id="createPlanLabel">Create Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Plan Title</label>
                            <input type="text" name="name" value="{{ old('name') }}" id="name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="text" name="price" value="{{ old('price') }}" id="price" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="duration_type" class="form-label">Type</label>
                            <select name="duration_type" id="duration_type" class="form-select">
                                <option value="daily" {{ old('duration_type') == 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ old('duration_type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('duration_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="duration_count" class="form-label">Duration Count</label>
                            <input type="text" value="{{ old('duration_count', 1) }}" name="duration_count" id="duration_count" class="form-control" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="reschedule_limit" class="form-label">Reschedule Limit</label>
                            <input type="text" value="{{ old('reschedule_limit') }}" name="reschedule_limit" id="reschedule_limit" class="form-control" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="payment_grace_days" class="form-label">Payment Grace Days</label>
                            <input type="text" value="{{ old('payment_grace_days') }}" name="payment_grace_days" id="payment_grace_days" class="form-control" required>
                        </div>

                    </div>

                    <div class="mb-3">
                        <label for="features" class="form-label">Features (Optional)</label>
                        <textarea name="features" id="features" class="form-control" rows="3">{{ old('features') }}</textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================================================================= --}}
{{-- Edit Plan Modal --}}
{{-- ========================================================================================================= --}}

<div class="modal fade" id="editPlanModal" tabindex="-1" aria-labelledby="editPlanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            {{-- The action will be set dynamically via JavaScript --}}
            <form id="editPlanForm" action="" method="POST"> 
                @csrf
                @method('PUT') 
                
                @if ($errors->any() && session('error_modal_type') == 'edit')
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="modal-header">
                    <h5 class="modal-title" id="editPlanLabel">Edit Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">Plan Title</label>
                            <input type="text" name="name" 
                                value="{{ old('name', '') }}" 
                                id="edit_name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_price" class="form-label">Price</label>
                            <input type="text" name="price" 
                                value="{{ old('price', '') }}" 
                                id="edit_price" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="edit_duration_type" class="form-label">Type</label>
                            <select name="duration_type" id="edit_duration_type" class="form-select">
                                <option value="daily" {{ old('duration_type') == 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ old('duration_type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('duration_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="edit_duration_count" class="form-label">Duration Count</label>
                            <input type="text" 
                                value="{{ old('duration_count', '') }}" 
                                name="duration_count" id="edit_duration_count" class="form-control" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="edit_reschedule_limit" class="form-label">Reschedule Limit</label>
                            <input type="text" 
                                value="{{ old('reschedule_limit', '') }}" 
                                name="reschedule_limit" id="edit_reschedule_limit" class="form-control" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="edit_payment_grace_days" class="form-label">Payment Grace Days</label>
                            <input type="text" 
                                value="{{ old('payment_grace_days', '') }}" 
                                name="payment_grace_days" id="edit_payment_grace_days" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_features" class="form-label">Features (Optional)</label>
                        <textarea name="features" id="edit_features" class="form-control" rows="3">{{ old('features', '') }}</textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

<script>
    // Check if there are validation errors from the edit form and display the modal
    @if ($errors->any() && session('error_modal_type') == 'edit')
        let editModal = new bootstrap.Modal(document.getElementById('editPlanModal'));
        editModal.show();
    @endif
    
    // Check if there are validation errors from the create form and display the modal
    @if ($errors->any() && session('error_modal_type') == 'create')
        let createModal = new bootstrap.Modal(document.getElementById('createPlanModal'));
        createModal.show();
    @endif


    document.addEventListener('DOMContentLoaded', function () {
        let editPlanModal = document.getElementById('editPlanModal');
        let editPlanForm = document.getElementById('editPlanForm');

        editPlanModal.addEventListener('show.bs.modal', function (event) {
            
            // Button that triggered the modal
            let button = event.relatedTarget; 

            // Extract data from data-* attributes
            let id = button.getAttribute('data-id');
            let name = button.getAttribute('data-name');
            let price = button.getAttribute('data-price');
            let durationType = button.getAttribute('data-duration-type');
            let durationCount = button.getAttribute('data-duration-count');
            let rescheduleLimit = button.getAttribute('data-reschedule-limit');
            let paymentGraceDays = button.getAttribute('data-payment-grace-days');
            let features = button.getAttribute('data-features');

            // Find the form elements
            let modalTitle = editPlanModal.querySelector('.modal-title');
            let form = editPlanModal.querySelector('#editPlanForm');
            let inputName = editPlanModal.querySelector('#edit_name');
            let inputPrice = editPlanModal.querySelector('#edit_price');
            let selectDurationType = editPlanModal.querySelector('#edit_duration_type');
            let inputDurationCount = editPlanModal.querySelector('#edit_duration_count');
            let inputRescheduleLimit = editPlanModal.querySelector('#edit_reschedule_limit');
            let inputPaymentGraceDays = editPlanModal.querySelector('#edit_payment_grace_days');
            let textareaFeatures = editPlanModal.querySelector('#edit_features');
            
            // Set the dynamic form action (e.g., /plans/1/update)
            // IMPORTANT: You MUST ensure you have a route like Route::put('plans/{plan}', 'PlanController@update')->name('plan.update');
            form.action = '/admin/plans/' + id + 'update'; // Adjust this route URL as needed!
            
            // Set modal title
            modalTitle.textContent = 'Edit Plan: ' + name;
            
            // Populate input fields ONLY IF old() didn't repopulate them (i.e., no validation error)
            // If old() has a value, it means validation failed, and we don't overwrite it.
            if (!inputName.value) inputName.value = name;
            if (!inputPrice.value) inputPrice.value = price;
            if (!inputDurationCount.value) inputDurationCount.value = durationCount;
            if (!inputRescheduleLimit.value) inputRescheduleLimit.value = rescheduleLimit;
            if (!inputPaymentGraceDays.value) inputPaymentGraceDays.value = paymentGraceDays;
            if (!textareaFeatures.value) textareaFeatures.value = features;
            
            // Select the correct option ONLY IF old() didn't select it
            if (selectDurationType.value === '') {
                selectDurationType.value = durationType;
            }
        });

        if (editPlanModal) {
            editPlanModal.addEventListener('hidden.bs.modal', function () {
                // Use the native form.reset() method to clear all fields
                if (editPlanForm) {
                    editPlanForm.reset();
                }
            });
        }
    });
</script>