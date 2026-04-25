@extends('layouts.app')

@section('title', 'All Users')

@section('content')

<main class="bg-light user-main-content p-4">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold">All Users</h4>

        <a href="{{ route('admin.add-admin') }}">
            <button type="submit" class="btn w-100 text-white"
                                style="background:#d4ac1c">
                <i class="fa fa-user-plus me-2"></i> Add Admin
            </button>
        </a>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stats-card text-center">
                <h2>{{ $totalUsers ?? 0 }}</h2>
                <span>Total Users</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card text-center">
                <h2>{{ $activeUsers ?? 0 }}</h2>
                <span>Active Users</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card text-center">
                <h2>{{ $inactiveUsers ?? 0 }}</h2>
                <span>Inactive Users</span>
            </div>
        </div>
    </div>

    {{-- FILTER CARD --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users') }}">
                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Search User</label>
                        <input type="text" name="search" class="form-control"
                               placeholder="CIN / Name / Email / Phone"
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">From Date</label>
                        <input type="date" name="from_date" class="form-control"
                               value="{{ request('from_date') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">To Date</label>
                        <input type="date" name="to_date" class="form-control"
                               value="{{ request('to_date') }}">
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn w-100 text-white"
                                style="background:#d4ac1c">Apply</button>
                        <a href="{{ route('admin.users') }}"
                           class="btn btn-light w-100">Reset</a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
            <table id="userTable" class="table table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>CIN No.</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>

                @forelse($users ?? [] as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->cin ?? '-' }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                        <td>
                            @if($user->status == 'Active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="text-center">

                            <form action="{{ route('admin.users.delete', $user->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm text-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No users found</td>
                    </tr>
                @endforelse

                </tbody>
            </table>
            </div>
        </div>
    </div>

</main>

@endsection


@section('scripts')

<script>
$(document).ready(function () {
    $('#userTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'print']
    });
});
</script>

@endsection
