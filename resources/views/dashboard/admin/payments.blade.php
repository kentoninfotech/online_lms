@extends('layouts.app')

@section('title', 'Payments')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
            <!-- Button trigger modal -->
            <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal" data-bs-target="#createPaymentModal">
                <i class="ph ph-plus"></i> Make Payment
            </button>
            <h4 class="mb-0">Payments</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('admin.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Payments</li>
            </ul>
        </div>
        </div>
    </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->

<!-- Students -->
<div class="card">
    <div class="card-body">
        <table class="table table-striped table-bordered">
        <thead class="table-light">
            <tr>
                <th>Student</th>
                <th>Parent</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $pay)
                <tr>
                    <td>
                        <!-- <a href="{{ route('show.student', $pay->subscription->student) }}" class="text-decoration-none"> -->
                            {{ $pay->subscription->student->name ?? '-' }}
                        <!-- </a> -->
                    </td>
                    <td>
                        <!-- <a href="{{ route('show.parent', $pay->parent) }}" class="text-decoration-none"> -->
                            {{ $pay->parent->name ?? '-' }}
                        <!-- </a> -->
                    </td>
                    <td>₦ {{ number_format($pay->amount, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $pay->status === 'approved' ? 'success' : ($pay->status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($pay->status) }}
                        </span>
                    </td>
                    <td>{{ $pay->created_at->format('d M Y h:i A') }}</td>
                    <td>
                        <a href="{{ route('payments.show', $pay) }}" class="dropdown-item text-info">
                            <i class="ph ph-eye"></i> View Payment
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No payments found.</td></tr>
            @endforelse
        </tbody>
    </table>
    
    
    </div>
    <div class="mt-3">
        {{ $payments->links() }}
    </div>
</div>


<!-- Create Lesson Modal -->
<div class="modal fade" id="createPaymentModal" tabindex="-1" aria-labelledby="createPaymentLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('payment.upload') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createPaymentLabel">Make Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body row g-3">
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

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="parentSelect" class="form-label">Select Parent</label>
                    <select id="parentSelect" class="form-select" name="parent_id">
                        <option value="">-- Choose Parent --</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="subscriptionSelect" class="form-label">Subscription</label>
                    <select id="subscriptionSelect" name="subscription_id" class="form-select" required disabled>
                        <option value="">Select Subscription</option>
                    </select>
                </div>
            </div>

            <!-- Amount -->
            <div class="col-md-6">
                    <label class="form-label">Amount</label>
                    <input type="text" name="amount" value="{{ old('amount') }}" class="form-control" required>
            </div>

            <!-- Evidence Payment -->
            <div class="col-md-6">
                <label class="form-label">Evidence Payment</label>
                <input type="file" name="file_path" class="form-control" required>
            </div>

        </div> <!-- modal-body -->

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Submit Payment</button>
        </div>
      </div>
    </form>
  </div>
</div>
        

@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    const parentSelect = document.getElementById('parentSelect');
    const subscriptionSelect = document.getElementById('subscriptionSelect');

    parentSelect.addEventListener('change', function () {
        const parentId = this.value;
        subscriptionSelect.innerHTML = '';
        subscriptionSelect.disabled = true;

        // Default placeholder
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Select Subscription';
        subscriptionSelect.appendChild(placeholder);

        if (!parentId) return;

        fetch(`/payments/${parentId}/student-sub`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    alert(data.message);
                    return;
                }

                if (data.status === 'empty') {
                    const msgOption = document.createElement('option');
                    msgOption.textContent = data.message;
                    msgOption.disabled = true;
                    msgOption.selected = true;
                    subscriptionSelect.appendChild(msgOption);
                    return;
                }

                if (data.status === 'success' && data.subscriptions.length > 0) {
                    data.subscriptions.forEach(sub => {
                        const opt = document.createElement('option');
                        opt.value = sub.id;
                        opt.textContent = `${sub.student.name} — ${sub.plan.name} (${sub.status}) Amount: ₦${Number(sub.plan.price).toLocaleString()}`;
                        subscriptionSelect.appendChild(opt);
                    });
                    subscriptionSelect.disabled = false;
                }
            })
            .catch(err => {
                console.error('Error fetching subscriptions:', err);
                alert('Failed to fetch subscriptions. Please try again.');
            });
    });
});
</script>



