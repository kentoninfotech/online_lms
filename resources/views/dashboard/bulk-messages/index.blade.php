@extends('layouts.app')

@section('title', 'Bulk Message Center')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
        <div class="card-body">
            <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title border-bottom pb-2 mb-2">
                     <h4 class="mb-0">📢 Bulk Message Center</h4>
                </div>
            </div>
            <div class="col-md-12">
                <ul class="breadcrumb">
                <li class="breadcrumb-item"
                    ><a href="{{ route( auth()->user()->user_type .'.dashboard') }}"><i class="ph ph-house"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Bulk Message Center</li>
                </ul>
            </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->


<div class="card mt-4">
    <div class="card-body">
        {{-- Message Form --}}
        <form id="bulkMessageForm" method="POST" action="{{ route('bulk-messages.send') }}">
            @csrf
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Message Subject</label>
                            <input type="text" name="subject" class="form-control" required placeholder="Enter message subject">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Send Method</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="method[]" value="email" id="emailCheck">
                                <label class="form-check-label" for="emailCheck">Email</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="method[]" value="sms" id="smsCheck">
                                <label class="form-check-label" for="smsCheck">SMS</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Message Body</label>
                        <textarea name="message" class="form-control" rows="5" required placeholder="Type your message..."></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Recipient Type</label>
                            <select id="recipientType" class="form-select">
                                <option value="parent" selected>Parents</option>
                                <option value="student">Students</option>
                                <option value="instructor">Instructors</option>
                            </select>
                        </div>
                        <div class="col-md-8 d-flex align-items-end justify-content-end">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recipients Table --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-semibold">Recipients List</h5>
                        <div>
                            <input type="checkbox" id="selectAll"> 
                            <label for="selectAll" class="fw-semibold ms-1">Select All</label>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle" id="recipientsTable">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                </tr>
                            </thead>
                            <tbody id="recipientsBody">
                                {{-- Data populated dynamically --}}
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">Loading recipients...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection


<script>
document.addEventListener('DOMContentLoaded', function() {
    const recipientType = document.getElementById('recipientType');
    const recipientsBody = document.getElementById('recipientsBody');
    const selectAll = document.getElementById('selectAll');

    // Load default (Parents)
    fetchRecipients('parent');

    recipientType.addEventListener('change', function() {
        fetchRecipients(this.value);
    });

    selectAll.addEventListener('change', function() {
        document.querySelectorAll('.recipient-checkbox').forEach(ch => {
            ch.checked = this.checked;
        });
    });

    async function fetchRecipients(role) {
        recipientsBody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-3 text-muted">Loading ${role}s...</td>
            </tr>
        `;

        try {
            const response = await fetch(`{{ route('bulk-messages.fetch') }}?role=${role}`);
            if (!response.ok) throw new Error('Failed to fetch recipients');

            const data = await response.json();

            if (data.length === 0) {
                recipientsBody.innerHTML = `
                    <tr><td colspan="4" class="text-center py-3 text-muted">No ${role}s found.</td></tr>
                `;
                return;
            }

            recipientsBody.innerHTML = data.map(u => `
                <tr>
                    <td><input type="checkbox" name="recipients[]" value="${u.id}" class="recipient-checkbox"></td>
                    <td>${u.name}</td>
                    <td>${u.email ?? '-'}</td>
                    <td>${u.number ?? '-'}</td>
                </tr>
            `).join('');
        } catch (err) {
            console.error(err);
            recipientsBody.innerHTML = `
                <tr><td colspan="4" class="text-center text-danger">Failed to load recipients.</td></tr>
            `;
        }
    }
});
</script>