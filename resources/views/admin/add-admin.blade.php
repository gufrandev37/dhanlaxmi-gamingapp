@extends('layouts.app')

@section('title', 'Add Admin')

@section('content')

    <main class="bg-light p-4 user user-main-content">

        <div class="mb-4">
            <h4 class="fw-bold text-dark">Add Admin</h4>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">


                <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Create Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    {{-- Role --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role_id" class="form-control" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Phone --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control">
                    </div>
                    {{-- Profile Image --}}
                   <div class="mb-3">
    <label class="form-label fw-semibold">Profile Image</label>

   <input type="file" name="image" id="imageInput" class="form-control" accept="image/*" style="color: transparent;">

    <small id="fileName" class="text-muted">No file chosen</small>
</div>

                    {{-- Aadhaar Number --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Aadhaar Number</label>
                        <input type="text" name="aadhaar_number" value="{{ old('aadhaar_number') }}" class="form-control">
                    </div>

                    {{-- PAN Number --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">PAN Number</label>
                        <input type="text" name="pan_number" value="{{ old('pan_number') }}" class="form-control">
                    </div>

                    {{-- Driving License --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Driving License</label>
                        <input type="text" name="driving_license" value="{{ old('driving_license') }}" class="form-control">
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-control">
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="accepted" {{ old('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    {{-- Assign Modules --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-3 d-block">Assign Modules</label>

                        @php
                            $oldModules = old('modules', []);
                        @endphp

                        <div class="row">

                            <div class="col-md-4">

                                {{-- Users --}}
                                <div class="form-check">
                                    <input class="form-check-input module-checkbox" type="checkbox" name="modules[]"
                                        value="users.view" {{ in_array('users.view', $oldModules) ? 'checked' : '' }}>
                                    <label class="form-check-label">Users</label>
                                </div>

                                {{-- Wallet --}}
                                <div class="form-check">
                                    <input class="form-check-input module-checkbox" type="checkbox" name="modules[]"
                                        value="wallet.view" {{ in_array('wallet.view', $oldModules) ? 'checked' : '' }}>
                                    <label class="form-check-label">Wallet History</label>
                                </div>

                            </div>

                            <div class="col-md-4">

                                {{-- Payment --}}
                                <div class="form-check">
                                    <input class="form-check-input module-checkbox" type="checkbox" name="modules[]"
                                        value="payment.view" {{ in_array('payment.view', $oldModules) ? 'checked' : '' }}>
                                    <label class="form-check-label">Payment History</label>
                                </div>

                                {{-- Winning --}}
                                <div class="form-check">
                                    <input class="form-check-input module-checkbox" type="checkbox" name="modules[]"
                                        value="winning.view" {{ in_array('winning.view', $oldModules) ? 'checked' : '' }}>
                                    <label class="form-check-label">Winning History</label>
                                </div>

                                {{-- Notification --}}
                                <div class="form-check">
                                    <input class="form-check-input module-checkbox" type="checkbox" name="modules[]"
                                        value="notification.view" {{ in_array('notification.view', $oldModules) ? 'checked' : '' }}>
                                    <label class="form-check-label">Notification</label>
                                </div>

                            </div>

                            <div class="col-md-4">

                                {{-- Withdraw --}}
                                <div class="form-check">
                                    <input class="form-check-input module-checkbox" type="checkbox" name="modules[]"
                                        value="withdraw.view" {{ in_array('withdraw.view', $oldModules) ? 'checked' : '' }}>
                                    <label class="form-check-label">Payment Withdraw</label>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn text-white" style="background:#d4ac1c">
                            ASSIGN ROLES
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </main>

@endsection


@section('scripts')
    <script>
        const modules = document.querySelectorAll('.module-checkbox');

        modules.forEach(cb => {
            cb.addEventListener('change', function () {
                console.log(this.value + " selected");
            });
        });
        document.getElementById('imageInput').addEventListener('change', function () {
    const fileName = this.files.length ? this.files[0].name : "No file chosen";
    document.getElementById('fileName').textContent = fileName;
});
    </script>
@endsection