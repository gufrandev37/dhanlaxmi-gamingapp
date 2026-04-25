@extends('layouts.app')

@section('title', 'Change Password')

@section('content')



<div>
    <div class="chpass-page-card mb-4">

        <div class="chpass-page-header">
            <div class="chpass-icon-box">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h3>Change Password</h3>
            <p class="text-muted">Update your account password</p>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Messages --}}
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.password.update') }}" method="POST">
            @csrf

            <!-- Current Password -->
            <div class="row mb-4 align-items-center">
                <div class="col-md-4">
                    <label class="chpass-form-label">Current Password</label>
                </div>
                <div class="col-md-8">
                    <input type="password" 
                           name="current_password"
                           class="form-control"
                           placeholder="Enter current password" required>
                </div>
            </div>

            <!-- New Password -->
            <div class="row mb-4 align-items-center">
                <div class="col-md-4">
                    <label class="chpass-form-label">New Password</label>
                </div>
                <div class="col-md-8">
                    <input type="password" 
                           name="new_password"
                           class="form-control"
                           placeholder="Enter new password" required>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="row mb-5 align-items-center">
                <div class="col-md-4">
                    <label class="chpass-form-label">Confirm New Password</label>
                </div>
                <div class="col-md-8">
                    <input type="password" 
                           name="new_password_confirmation"
                           class="form-control"
                           placeholder="Confirm new password" required>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="chpass-btn-submit">
                    Update Password
                </button>
            </div>
        </form>

    </div>
</div>

@endsection
