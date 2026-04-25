@extends('layouts.app')

@section('title', 'Manage Admin')

@section('content')

    <div class="container-fluid mt-4">

        {{-- Alerts --}}
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

       <div class="card shadow rounded-3 border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width:900px;">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>S.No.</th>
                            <th>Admin ID</th>
                            <th>Admin Info</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Aadhaar</th>
                            <th>PAN</th>
                            <th>Driving License</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:0.85rem;">
                        @foreach($admins as $key => $admin)
                            <tr>
                                <td>{{ $admins->firstItem() + $key }}</td>
                                <td>{{ $admin->admin_id }}</td>
                              <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $admin->image_url }}" width="38" height="38"
                                            class="rounded-circle flex-shrink-0"
                                            style="object-fit:cover;">
                                        <strong>{{ $admin->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $admin->phone ?? '-' }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>{{ $admin->aadhaar_number ?? '-' }}</td>
                                <td>{{ $admin->pan_number ?? '-' }}</td>
                                <td>{{ $admin->driving_license ?? '-' }}</td>

                                {{-- Status Badge --}}
                                <td>
                                    @if($admin->status == 'accepted')
                                        <span class="badge bg-success px-2 py-1">
                                            <i class="bi bi-check-circle me-1"></i> Accepted
                                        </span>
                                    @elseif($admin->status == 'rejected')
                                        <span class="badge bg-danger px-2 py-1">
                                            <i class="bi bi-x-circle me-1"></i> Rejected
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark px-2 py-1">
                                            <i class="bi bi-clock me-1"></i> Pending
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <div class="d-flex flex-nowrap gap-1 align-items-center">

                                        {{-- View --}}
                                        <button type="button" class="btn btn-info btn-sm view-admin" data-id="{{ $admin->id }}"
                                            title="View" style="border-radius:50%;width:34px;height:34px;padding:0">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        {{-- Edit --}}
                                        <button type="button" class="btn btn-warning btn-sm edit-admin"
                                            data-id="{{ $admin->id }}" title="Edit"
                                            style="border-radius:50%;width:34px;height:34px;padding:0">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Delete --}}
                                        @if($admin->email !== 'superadmin@admin.com')
                                            <form action="{{ route('admin.destroy', $admin->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete {{ $admin->name }}?')"
                                                style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete"
                                                    style="border-radius:50%;width:34px;height:34px;padding:0">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Approve / Reject (Super Admin only) --}}
                                        @if(auth()->user()->isSuperAdmin())

                                            @if($admin->status == 'rejected')
                                                <span class="badge bg-danger px-2 py-1" title="Permanently Rejected">
                                                    <i class="bi bi-lock-fill me-1"></i> Locked
                                                </span>

                                            @elseif($admin->status == 'accepted')
                                                <form action="{{ route('admin.updateStatus', $admin->id) }}" method="POST"
                                                    style="display:inline"
                                                    onsubmit="return confirm('Reject {{ $admin->name }}? This cannot be undone!')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Reject"
                                                        style="border-radius:50%;width:34px;height:34px;padding:0">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </form>

                                            @else
                                                <form action="{{ route('admin.updateStatus', $admin->id) }}" method="POST"
                                                    style="display:inline" onsubmit="return confirm('Approve {{ $admin->name }}?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button type="submit" class="btn btn-success btn-sm" title="Approve"
                                                        style="border-radius:50%;width:34px;height:34px;padding:0">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.updateStatus', $admin->id) }}" method="POST"
                                                    style="display:inline"
                                                    onsubmit="return confirm('Reject {{ $admin->name }}? This cannot be undone!')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Reject"
                                                        style="border-radius:50%;width:34px;height:34px;padding:0">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </form>

                                            @endif

                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-4 mb-3">
                    {{ $admins->links() }}
                </div>
                </div>{{-- closes table-responsive --}}
            </div>
        </div>
    </div>

    {{-- ═══════════════ VIEW MODAL ═══════════════ --}}
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Admin Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewDetails">
                    <div class="text-center py-4">
                        <div class="spinner-border text-info"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ═══════════════ EDIT MODAL ═══════════════ --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#d4ac1c">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Admin</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div id="editAlert" class="d-none"></div>

                    <form id="editForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="edit_id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                <input type="text" id="edit_name" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" id="edit_email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" id="edit_phone" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                <select id="edit_role_id" name="role_id" class="form-control" required>
                                    @foreach(\App\Models\Role::all() as $role)
                                        <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Aadhaar Number</label>
                                <input type="text" id="edit_aadhaar" name="aadhaar_number" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">PAN Number</label>
                                <input type="text" id="edit_pan" name="pan_number" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Driving License</label>
                                <input type="text" id="edit_license" name="driving_license" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select id="edit_status" name="status" class="form-control" required>
                                    <option value="pending">Pending</option>
                                    <option value="accepted">Accepted</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">New Password
                                    <small class="text-muted">(leave blank to keep)</small>
                                </label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="Enter new password">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Profile Image</label>
                                <input type="file" name="image" id="edit_image_input" class="form-control" accept="image/*">
                                <div class="mt-2">
                                    <img id="edit_image_preview" src="" width="60" height="60" class="rounded-circle border"
                                        style="object-fit:cover;">
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn text-white" style="background:#d4ac1c" id="saveEditBtn">
                        <span id="saveBtnText"><i class="bi bi-check-circle me-1"></i> Save Changes</span>
                        <span id="saveBtnSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>

        // ─── VIEW MODAL ───────────────────────────────────
        document.querySelectorAll('.view-admin').forEach(function (button) {
            button.addEventListener('click', function () {
                const adminId = this.getAttribute('data-id');

                document.getElementById('viewDetails').innerHTML =
                    '<div class="text-center py-4"><div class="spinner-border text-info"></div></div>';

                const modal = new bootstrap.Modal(document.getElementById('viewModal'));
                modal.show();

                fetch("{{ url('admin/details') }}/" + adminId)
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        const imageUrl = data.image_url || '/images/default-avatar.png';
                        const statusColor = data.status === 'accepted' ? 'bg-success' : data.status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark';
                        const statusLabel = data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : '-';

                        document.getElementById('viewDetails').innerHTML =
                            '<div class="text-center mb-4">' +
                            '<img src="' + imageUrl + '" class="rounded-circle" width="110" height="110" style="object-fit:cover;border:4px solid #d4ac1c;" onerror="this.src=\'/images/default-avatar.png\'">' +
                            '<h5 class="mt-3 mb-0 fw-bold">' + data.name + '</h5>' +
                            '<small class="text-muted">' + data.email + '</small><br>' +
                            '<span class="badge ' + statusColor + ' mt-2 px-3 py-1">' + statusLabel + '</span>' +
                            '</div><hr>' +
                            '<div class="row">' +
                            '<div class="col-md-6 mb-2"><div class="text-muted small">Admin ID</div><div class="fw-semibold">' + (data.admin_id ?? '-') + '</div></div>' +
                            '<div class="col-md-6 mb-2"><div class="text-muted small">Phone</div><div class="fw-semibold">' + (data.phone ?? '-') + '</div></div>' +
                            '<div class="col-md-6 mb-2"><div class="text-muted small">Aadhaar Number</div><div class="fw-semibold">' + (data.aadhaar_number ?? '-') + '</div></div>' +
                            '<div class="col-md-6 mb-2"><div class="text-muted small">PAN Number</div><div class="fw-semibold">' + (data.pan_number ?? '-') + '</div></div>' +
                            '<div class="col-md-6 mb-2"><div class="text-muted small">Driving License</div><div class="fw-semibold">' + (data.driving_license ?? '-') + '</div></div>' +
                            '</div>';
                    })
                    .catch(function () {
                        document.getElementById('viewDetails').innerHTML =
                            '<div class="alert alert-danger text-center">❌ Failed to load admin details. Please try again.</div>';
                    });
            });
        });


        // ─── EDIT MODAL ───────────────────────────────────
        document.querySelectorAll('.edit-admin').forEach(function (button) {
            button.addEventListener('click', function () {
                const adminId = this.getAttribute('data-id');

                const alertBox = document.getElementById('editAlert');
                alertBox.className = 'd-none';
                alertBox.innerHTML = '';

                fetch("{{ url('admin/details') }}/" + adminId)
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        document.getElementById('edit_id').value = data.id;
                        document.getElementById('edit_name').value = data.name;
                        document.getElementById('edit_email').value = data.email;
                        document.getElementById('edit_phone').value = data.phone ?? '';
                        document.getElementById('edit_aadhaar').value = data.aadhaar_number ?? '';
                        document.getElementById('edit_pan').value = data.pan_number ?? '';
                        document.getElementById('edit_license').value = data.driving_license ?? '';
                        document.getElementById('edit_status').value = data.status ?? 'pending';
                        document.getElementById('edit_role_id').value = data.role_id ?? '';
                        document.getElementById('edit_image_preview').src = data.image_url || '/images/default-avatar.png';

                        const modal = new bootstrap.Modal(document.getElementById('editModal'));
                        modal.show();
                    })
                    .catch(function () {
                        alert('Failed to load admin details. Please try again.');
                    });
            });
        });


        // ─── PREVIEW IMAGE BEFORE UPLOAD ─────────────────
        document.getElementById('edit_image_input').addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('edit_image_preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });


    // ─── SAVE EDIT ────────────────────────────────────
        document.getElementById('saveEditBtn').addEventListener('click', function () {
            const id = document.getElementById('edit_id').value;
            const formEl = document.getElementById('editForm');
            const formData = new FormData(formEl);
            formData.append('_method', 'PATCH');

            const btnText = document.getElementById('saveBtnText');
            const btnSpinner = document.getElementById('saveBtnSpinner');
            const alertBox = document.getElementById('editAlert');

            btnText.classList.add('d-none');
            btnSpinner.classList.remove('d-none');
            alertBox.className = 'd-none';
            alertBox.innerHTML = '';

            fetch("{{ url('admin') }}/" + id + "/update", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: formData
            })
                .then(function (response) {
                    if (!response.ok) throw new Error("Network error");
                    return response.json();
                })
                .then(function (data) {
                    btnText.classList.remove('d-none');
                    btnSpinner.classList.add('d-none');

                    if (data.success) {
                        alertBox.className = 'alert alert-success';
                        alertBox.innerHTML = '✅ ' + data.message;
                        setTimeout(function () { location.reload(); }, 1200);
                    } else {
                        alertBox.className = 'alert alert-danger';
                        alertBox.innerHTML = '❌ ' + data.message;
                    }
                })
                .catch(function () {
                    btnText.classList.remove('d-none');
                    btnSpinner.classList.add('d-none');
                    alertBox.className = 'alert alert-danger';
                    alertBox.innerHTML = '❌ Something went wrong. Please try again.';
                });
        });
        </script>
@endsection