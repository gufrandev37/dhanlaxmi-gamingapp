@extends('layouts.app')

@section('title','Payment History')

@section('content')

<div class="bg-light user-main-content p-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Payment History</h4>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <h2>{{ $totalUsers ?? 0 }}</h2>
                <span class="stat-label">Total Users</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stats-card">
                <h2>{{ $activeUsers ?? 0 }}</h2>
                <span class="stat-label">Active Users</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stats-card">
                <h2>{{ $inactiveUsers ?? 0 }}</h2>
                <span class="stat-label">Inactive Users</span>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-striped align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Sr.No.</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Transaction ID</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($payments as $index => $payment)
                        <tr>
                            <td>{{ $payments->firstItem() + $index }}</td>

                            <td>{{ $payment->user->name ?? '-' }}</td>

                            <td>{{ $payment->user->email ?? '-' }}</td>

                            <td>{{ $payment->transaction_id }}</td>

                            <td>{{ ucfirst($payment->payment_method) }}</td>

                            <td>
                                @if($payment->status == 'success')
                                    <span class="badge bg-success">Success</span>
                                @elseif($payment->status == 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>

                            <td>{{ $payment->created_at->format('d/m/Y') }}</td>

                            <td class="text-end">
                                ₹ {{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                No Payment Records Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            {{-- PAGINATION --}}
            <div class="mt-3">
                {{ $payments->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>

@endsection
