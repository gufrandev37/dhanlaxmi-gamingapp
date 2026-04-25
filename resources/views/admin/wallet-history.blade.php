@extends('layouts.app')

@section('title', 'Wallet History')

@section('content')

<div class="container-fluid">

    <h4 class="fw-bold mb-4">Wallet History</h4>

    {{-- KPI CARDS --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <h2>{{ $totalUsers ?? 0 }}</h2>
                <span>Total Users</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stats-card">
                <h2>{{ $activeUsers ?? 0 }}</h2>
                <span>Active Users</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stats-card">
                <h2>{{ $inactiveUsers ?? 0 }}</h2>
                <span>Inactive Users</span>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET">
               <div class="row g-2">

                    <div class="col-12 col-md-4">
                
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="CIN / Name / Email / Phone">
                    </div>
                            <div class="col-6 col-md-3">
                            <input type="date"
                               name="from"
                               value="{{ request('from') }}"
                               class="form-control">
                    </div>
                            <div class="col-6 col-md-3">
                        <input type="date"
                               name="to"
                               value="{{ request('to') }}"
                               class="form-control">
                    </div>

                    <div class="col-12 col-md-2 d-flex gap-2"></div>
                        <button class="btn btn-warning w-100">
                            Apply
                        </button>

                        <a href="{{ route('admin.wallet.history') }}"
                           class="btn btn-light w-100">
                            Reset
                        </a>
                    </div>

                </div>

            </form>
        </div>
    </div>

    {{-- TABLE --}}
            <div class="card shadow-sm">
        <div class="card-body p-0 p-md-3">
            <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <!-- <th>CIN</th> -->
                        <th>Name</th>
                        <th>Phone</th>
                        <!-- <th>Email</th> -->
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($wallets as $index => $wallet)
                        <tr>
                            <td>{{ $wallets->firstItem() + $index }}</td>
                            <!-- <td>{{ $wallet->cin ?? '-' }}</td> -->
                            <td>{{ $wallet->user->name ?? '-' }}</td>
                            <td>{{ $wallet->user->phone ?? '-' }}</td>
                            <!-- <td>{{ $wallet->user->email ?? '-' }}</td> -->
                            <td>{{ optional($wallet->created_at)->format('d-m-Y') }}</td>
                            <td>
                                @if($wallet->type === 'credit')
                                    <span class="badge bg-success">Credit</span>
                                @else
                                    <span class="badge bg-danger">Debit</span>
                                @endif
                            </td>
                            <td class="text-end">
                                ₹{{ number_format($wallet->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                No wallet history found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
            </div>
            {{-- PAGINATION --}}
            @if($wallets->count())
            <div class="d-flex justify-content-between align-items-center mt-3">

                <div>
                    Showing {{ $wallets->firstItem() }}
                    to {{ $wallets->lastItem() }}
                    of {{ $wallets->total() }} results
                </div>

                <div>
                    {{ $wallets->withQueryString()->links('pagination::bootstrap-5') }}
                </div>

            </div>
            @endif

        </div>
    </div>

</div>

@endsection
