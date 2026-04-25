@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Users</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- KPI CARDS --}}
    <div class="row g-4 mb-4">
       <div class="col-lg-4 col-md-6 col-12">
            <div class="stats-card">
                <h2>{{ $totalUsers }}</h2>
                <span class="stat-label">Total Users</span>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-12">
            <div class="stats-card">
                <h2>{{ $activeUsers }}</h2>
                <span class="stat-label">Active Users</span>
            </div>
        </div>
       <div class="col-lg-4 col-md-6 col-12">
            <div class="stats-card">
                <h2>{{ $inactiveUsers }}</h2>
                <span class="stat-label">Inactive Users</span>
            </div>
        </div>
    </div>

    {{-- USER TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table id="usersTable" class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>CIN</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
    var CSRF_TOKEN = '{{ csrf_token() }}';
    var DATA_URL   = '{{ route("admin.users.data") }}';
    var TOGGLE_URL = '{{ route("admin.user.toggle", ["id" => "__ID__"]) }}';
    var DELETE_URL = '{{ route("admin.user.delete", ["id" => "__ID__"]) }}';

    $(document).ready(function () {

        var table = $('#usersTable').DataTable({
           responsive: true,
            processing: true,
            serverSide: true,
            ajax: { url: DATA_URL, type: 'GET' },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'cin',         defaultContent: '-' },
                { data: 'name',        defaultContent: '-' },
                { data: 'phone',       defaultContent: '-' },
                { data: 'created_at',  defaultContent: '-' },
                { data: 'status',      orderable: false, searchable: false },
                { data: 'role',        defaultContent: '-' },
                { data: 'action',      orderable: false, searchable: false }
            ],
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copy',  className: 'btn btn-warning btn-sm me-2' },
                { extend: 'csv',   className: 'btn btn-warning btn-sm me-2' },
                { extend: 'excel', className: 'btn btn-warning btn-sm me-2' },
                { extend: 'print', className: 'btn btn-warning btn-sm' }
            ],
            pageLength: 10,
        });

        // ── TOGGLE STATUS ─────────────────────────────────────────────
        $('#usersTable').on('click', '.toggleStatus', function () {
            var id  = $(this).data('id');
            var url = TOGGLE_URL.replace('__ID__', id);

            $.ajax({
                url:    url,
                method: 'POST',
                data:   { _token: CSRF_TOKEN, _method: 'PATCH' },
                success: function (res) {
                    if (res.success) {
                        table.draw(false);
                        showToast(res.message, 'success');
                    } else {
                        showToast(res.message || 'Failed.', 'danger');
                    }
                },
                error: function (xhr) {
                    showToast('Error ' + xhr.status + ': ' + xhr.statusText, 'danger');
                    console.error(xhr.responseText);
                }
            });
        });

        // ── DELETE USER ───────────────────────────────────────────────
        $('#usersTable').on('click', '.deleteUser', function () {
            var id  = $(this).data('id');
            var url = DELETE_URL.replace('__ID__', id);

            if (!confirm('Are you sure you want to delete this user? This cannot be undone.')) return;

            $.ajax({
                url:    url,
                method: 'POST',
                data:   { _token: CSRF_TOKEN, _method: 'DELETE' },
                success: function (res) {
                    if (res.success) {
                        table.draw(false);
                        showToast(res.message, 'success');
                    } else {
                        showToast(res.message || 'Failed.', 'danger');
                    }
                },
                error: function (xhr) {
                    showToast('Error ' + xhr.status + ': ' + xhr.statusText, 'danger');
                    console.error(xhr.responseText);
                }
            });
        });

        function showToast(msg, type) {
            var t = $('<div>')
                .addClass('alert alert-' + type + ' position-fixed shadow')
                .css({ top: '20px', right: '20px', zIndex: 9999, minWidth: '280px' })
                .text(msg);
            $('body').append(t);
            setTimeout(function () {
                t.fadeOut(400, function () { $(this).remove(); });
            }, 3000);
        }
    });
</script>
@endsection