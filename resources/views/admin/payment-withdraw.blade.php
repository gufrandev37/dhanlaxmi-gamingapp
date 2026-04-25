@extends('layouts.app')

@section('title', 'Payment Withdraw')

@section('content')

<div class="p-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Payment Withdraw</h4>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTER FORM --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET">
               <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-4">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="CIN / Name / Email / Phone">
                    </div>
                    <div class="col-6 col-md-3">
                        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                    </div>
                    <div class="col-6 col-md-3">
                        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                    </div>
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button class="btn w-100 text-white" style="background:#d4ac1c">Apply</button>
                        <a href="{{ route('admin.payment.withdraw') }}" class="btn btn-light w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- STATUS FILTER TABS --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h5 class="mb-3 fs-6 fs-md-5">Payment Withdraw Requests</h5>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="{{ route('admin.payment.withdraw', ['status'=>'processing']) }}"
                   class="btn btn-primary btn-sm {{ request('status')=='processing' ? 'active' : '' }}">
                    Processing
                </a>
                <a href="{{ route('admin.payment.withdraw', ['status'=>'approved']) }}"
                   class="btn btn-success btn-sm {{ request('status')=='approved' ? 'active' : '' }}">
                    Approved
                </a>
                <a href="{{ route('admin.payment.withdraw', ['status'=>'rejected']) }}"
                   class="btn btn-danger btn-sm {{ request('status')=='rejected' ? 'active' : '' }}">
                    Rejected
                </a>
                <a href="{{ route('admin.payment.withdraw') }}"
                   class="btn btn-dark btn-sm {{ !request('status') ? 'active' : '' }}">
                    All
                </a>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="table-responsive bg-white p-3 rounded shadow-sm">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Sr.No.</th>
                    <th>Date</th>
                    <th>Mode</th>
                    <th>CIN</th>
                    <th>Mobile</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdraws as $index => $w)
                    <tr>
                        <td>{{ $withdraws->firstItem() + $index }}</td>
                        <td>{{ $w->created_at->format('d-m-Y') }}</td>
                        <td>{{ $w->payment_mode }}</td>
                        <td>{{ $w->cin }}</td>
                        <td>{{ $w->mobile }}</td>
                        <td>{{ $w->user->name ?? '-' }}</td>
                        <td>₹{{ number_format($w->amount, 2) }}</td>
                        <td>
                            <span class="badge
                                @if($w->status == 'processing') bg-primary
                                @elseif($w->status == 'approved') bg-success
                                @elseif($w->status == 'rejected') bg-danger
                                @else bg-secondary
                                @endif">
                                {{ ucfirst($w->status) }}
                            </span>
                        </td>
                        <td>
                            @if($w->status === 'processing')
                                {{-- Approve --}}
                                <form action="{{ route('admin.payment.withdraw.status', $w->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Approve this withdraw request?')">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                </form>

                                {{-- Reject --}}
                                <form action="{{ route('admin.payment.withdraw.status', $w->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Reject this withdraw request?')">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">No action</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No Withdraw Requests Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $withdraws->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

@endsection