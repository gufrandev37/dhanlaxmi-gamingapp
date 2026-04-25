@extends('layouts.app')

@section('title', 'Payment Withdraw')

@section('content')
<div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h5 class="fw-bold mb-3">Payment Withdraw</h5>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-4">
            <div class="card text-center">
                <div class="card-body py-2">
                    <div class="fw-bold fs-5">{{ $totalPending }}</div>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card text-center">
                <div class="card-body py-2">
                    <div class="fw-bold fs-5">{{ $totalApproved }}</div>
                    <small class="text-muted">Approved</small>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card text-center">
                <div class="card-body py-2">
                    <div class="fw-bold fs-5">{{ $totalRejected }}</div>
                    <small class="text-muted">Rejected</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.withdraw.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <input type="text" name="search"
                               value="{{ request('search') }}"
                               class="form-control form-control-sm"
                               placeholder="Name / Email / Phone / CIN">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from"
                               value="{{ request('from') }}"
                               class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to"
                               value="{{ request('to') }}"
                               class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="all">All Status</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="approved"   {{ request('status') == 'approved'   ? 'selected' : '' }}>Approved</option>
                            <option value="rejected"   {{ request('status') == 'rejected'   ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('admin.withdraw.index') }}"
                           class="btn btn-sm btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-bordered table-sm align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>CIN</th>
                        <th>Payment Mode</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdraws as $index => $w)
                        <tr>
                            <td>{{ $withdraws->firstItem() + $index }}</td>
                            <td>{{ $w->user->name ?? '-' }}</td>
                            <td>{{ $w->user->phone ?? '-' }}</td>
                            <td>{{ $w->cin }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $w->payment_mode)) }}</td>
                            <td>₹{{ number_format($w->amount, 2) }}</td>
                            <td>
                                @if($w->status == 'processing')
                                    <span class="badge bg-warning text-dark">Processing</span>
                                @elseif($w->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>{{ $w->created_at->format('d M Y') }}</td>
                            <td>
                                @if($w->status === 'processing')
                                    <button class="btn btn-sm btn-outline-primary"
                                            onclick="openModal(
                                                {{ $w->id }},
                                                '{{ $w->user->name ?? '-' }}',
                                                '{{ $w->user->phone ?? '-' }}',
                                                '{{ $w->user->email ?? '-' }}',
                                                '{{ $w->amount }}'
                                            )">
                                        View
                                    </button>
                                @else
                                    <span class="text-muted small">Done</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-muted py-3">No withdrawal requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $withdraws->withQueryString()->links() }}
    </div>

</div>

{{-- Modal --}}
<div class="modal fade" id="withdrawModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Withdrawal Detail</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label fw-semibold">User Name</label>
                    <input type="text" id="modalName" class="form-control form-control-sm" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" id="modalPhone" class="form-control form-control-sm" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="text" id="modalEmail" class="form-control form-control-sm" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold">Amount</label>
                    <input type="text" id="modalAmount" class="form-control form-control-sm" readonly>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                {{-- Approve --}}
                <form id="approveForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="btn btn-sm btn-success"
                            onclick="return confirm('Approve this withdrawal?')">
                        ✅ Approve
                    </button>
                </form>

                {{-- Reject --}}
                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Reject this withdrawal?')">
                        ❌ Reject
                    </button>
                </form>

                <button type="button" class="btn btn-sm btn-secondary"
                        data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(id, name, phone, email, amount) {
    document.getElementById('modalName').value   = name;
    document.getElementById('modalPhone').value  = phone;
    document.getElementById('modalEmail').value  = email;
    document.getElementById('modalAmount').value = '₹' + amount;

    // Set form actions
    const base = '{{ url("admin/withdraw") }}/' + id + '/status';
    document.getElementById('approveForm').action = base;
    document.getElementById('rejectForm').action  = base;

    new bootstrap.Modal(document.getElementById('withdrawModal')).show();
}
</script>
@endsection