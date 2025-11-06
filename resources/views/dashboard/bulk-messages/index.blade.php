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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <p><strong>Whoops! Something went wrong.</strong></p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Message Subject</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" class="form-control" required placeholder="Enter message subject">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Send Method</label><br>
                            <div class="form-check form-check-inline">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    name="method[]" 
                                    value="email" 
                                    id="emailCheck"
                                    {{ (is_array(old('method')) && in_array('email', old('method'))) ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="emailCheck">Email</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    name="method[]" 
                                    value="sms" 
                                    id="smsCheck"
                                    {{ (is_array(old('method')) && in_array('sms', old('method'))) ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="smsCheck">SMS</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Message Body</label>
                        <textarea name="message" class="form-control" rows="5" required placeholder="Type your message...">{{ old('message') }}</textarea>
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
                        <h5 class="card-title mb-0" id="recipientTitle">Recipients List</h5>
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

{{-- Bulk Message Logs --}}
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-light">
        <h5 class="mb-0 fw-semibold">📄 Message Logs</h5>
        <button id="refreshLogs" class="btn btn-sm btn-outline-secondary">Refresh</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle" id="logsTable">
                <thead class="table-light">
                    <tr>
                        <th>Subject</th>
                        <th>Methods</th>
                        <th>Total</th>
                        <th>Sent</th>
                        <th>Failed</th>
                        <th>Status</th>
                        <th>Sent At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="logsBody">
                    <tr><td colspan="8" class="text-center py-3 text-muted">Loading logs...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Recipient Modal --}}
<div class="modal fade" id="recipientsModal" tabindex="-1" aria-labelledby="recipientsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-semibold" id="recipientsModalLabel">Message Recipients</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Status</th>
                <th>Last Updated</th>
              </tr>
            </thead>
            <tbody id="modalRecipientsBody">
              <tr><td colspan="5" class="text-center py-3 text-muted">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection


<script>
document.addEventListener('DOMContentLoaded', function() {
    const recipientType = document.getElementById('recipientType');
    const recipientsBody = document.getElementById('recipientsBody');
    const selectAll = document.getElementById('selectAll');
    const logsBody = document.getElementById('logsBody');
    const refreshLogs = document.getElementById('refreshLogs');
    const modalRecipientsBody = document.getElementById('modalRecipientsBody');
    const recipientTitle = document.getElementById('recipientTitle'); 

    // Initial load
    updateRecipientTitle('parent');
    fetchRecipients('parent');
    fetchLogs();

    // Handle role change
    recipientType.addEventListener('change', function() {
        const role = this.value;
        updateRecipientTitle(role);
        fetchRecipients(role);
    });

    // Select all recipients
    selectAll.addEventListener('change', function() {
        document.querySelectorAll('.recipient-checkbox').forEach(ch => ch.checked = this.checked);
    });

    // Refresh logs
    refreshLogs.addEventListener('click', fetchLogs);

    // Update title dynamically + animate count
    function updateRecipientTitle(role, count = null) {
        const titles = {
            parent: 'Parents List',
            student: 'Students List',
            instructor: 'Instructors List'
        };
        const baseTitle = titles[role] || 'Recipients List';

        if (count === null) {
            recipientTitle.textContent = baseTitle;
            return;
        }

        // Extract old count (if exists)
        const match = recipientTitle.textContent.match(/\((\d+)\)/);
        const oldCount = match ? parseInt(match[1]) : 0;

        animateCount(oldCount, count, (val) => {
            recipientTitle.textContent = `${baseTitle} (${val})`;
        });
    }

    // Animate number counting (smooth transition)
    function animateCount(from, to, onUpdate) {
        const duration = 500; // 0.5s animation
        const start = performance.now();

        function step(timestamp) {
            const progress = Math.min((timestamp - start) / duration, 1);
            const value = Math.floor(from + (to - from) * progress);
            onUpdate(value);
            if (progress < 1) requestAnimationFrame(step);
        }

        requestAnimationFrame(step);
    }

    // Fetch recipients by role
    async function fetchRecipients(role) {
        recipientsBody.innerHTML = `<tr><td colspan="4" class="text-center py-3 text-muted">Loading ${role}s...</td></tr>`;
        try {
            const response = await fetch(`{{ route('bulk-messages.fetch') }}?role=${role}`);
            if (!response.ok) throw new Error('Failed to fetch recipients');
            const data = await response.json();

            if (data.length === 0) {
                recipientsBody.innerHTML = `<tr><td colspan="4" class="text-center py-3 text-muted">No ${role}s found.</td></tr>`;
                updateRecipientTitle(role, 0);
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

            updateRecipientTitle(role, data.length);
        } catch (err) {
            console.error(err);
            recipientsBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Failed to load recipients.</td></tr>`;
            updateRecipientTitle(role);
        }
    }

    // Fetch logs list
    async function fetchLogs() {
        logsBody.innerHTML = `<tr><td colspan="8" class="text-center py-3 text-muted">Loading logs...</td></tr>`;
        try {
            const response = await fetch(`{{ route('bulk-messages.logs') }}`);
            const data = await response.json();

            if (data.length === 0) {
                logsBody.innerHTML = `<tr><td colspan="8" class="text-center py-3 text-muted">No messages sent yet.</td></tr>`;
                return;
            }

            logsBody.innerHTML = data.map(log => `
                <tr>
                    <td>${log.subject}</td>
                    <td>${log.methods}</td>
                    <td>${log.total}</td>
                    <td>${log.sent}</td>
                    <td>${log.failed}</td>
                    <td><span class="badge bg-${log.status === 'completed' ? 'success' : 'secondary'}">${log.status}</span></td>
                    <td>${log.created_at}</td>
                    <td><button class="btn btn-sm btn-outline-primary viewRecipients" data-id="${log.id}">View</button></td>
                </tr>
            `).join('');

            document.querySelectorAll('.viewRecipients').forEach(btn => {
                btn.addEventListener('click', function() {
                    openRecipientsModal(this.dataset.id);
                });
            });
        } catch (err) {
            console.error(err);
            logsBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Failed to load logs.</td></tr>`;
        }
    }

    // Fetch and show recipients for a specific message
    async function openRecipientsModal(messageId) {
        const modal = new bootstrap.Modal(document.getElementById('recipientsModal'));
        modalRecipientsBody.innerHTML = `<tr><td colspan="5" class="text-center py-3 text-muted">Loading recipients...</td></tr>`;
        modal.show();

        try {
            let baseUrl = `{{ route('bulk-messages.recipients', ':id') }}`;
            const url = baseUrl.replace(':id', messageId);
            const response = await fetch(url);
            const data = await response.json();

            if (data.length === 0) {
                modalRecipientsBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">No recipients found.</td></tr>`;
                return;
            }

            modalRecipientsBody.innerHTML = data.map(r => `
                <tr>
                    <td>${r.name}</td>
                    <td>${r.email ?? '-'}</td>
                    <td>${r.number ?? '-'}</td>
                    <td><span class="badge bg-${r.status === 'sent' ? 'success' : (r.status === 'failed' ? 'danger' : 'secondary')}">${r.status}</span></td>
                    <td>${r.updated_at}</td>
                </tr>
            `).join('');
        } catch (err) {
            console.error(err);
            modalRecipientsBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Failed to load recipients.</td></tr>`;
        }
    }
});
</script>

