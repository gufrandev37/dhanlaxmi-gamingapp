@extends('layouts.app')

@section('title', 'Wallet Management')

@section('content')

    <div>
        <div class="chpass-page-card mb-4">

            <div class="chpass-page-header">
                <div class="chpass-icon-box">
                    <i class="bi bi-wallet2"></i>
                </div>
                <h3>Wallet Management</h3>
                <p class="text-muted">Admin Manual Credit / Debit</p>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="alert alert-success text-center">
                    {!! session('success') !!}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger text-center">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.wallet.update') }}" method="POST">
                @csrf

                {{-- Mobile Number --}}
                <div class="row mb-4 align-items-center">
                    <div class="col-md-4">
                        <label class="chpass-form-label">Mobile Number</label>
                    </div>
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" id="mobile_input" name="mobile" value="{{ old('mobile') }}"
                                class="form-control @error('mobile') is-invalid @enderror"
                                placeholder="Enter 10-digit mobile number" maxlength="15" required>
                            <button type="button" class="btn text-white" style="background:#d4ac1c" onclick="fetchUser()">
                                <i class="bi bi-search"></i> Find
                            </button>
                        </div>
                        @error('mobile')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- User Info Card (shows after search) --}}
                <div class="row mb-4 align-items-center" id="user_info_row" style="display:none !important">
                    <div class="col-md-4"></div>
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm p-3"
                            style="background:#f0fdf4;border-left:4px solid #1a6e3a !important">
                            <div class="d-flex align-items-center gap-3">
                                <div
                                    style="background:#1a6e3a;border-radius:50%;width:45px;height:45px;display:flex;align-items:center;justify-content:center">
                                    <i class="bi bi-person-fill text-white fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-5" id="display_name">-</div>
                                    <div class="text-muted small">CIN: <span id="display_cin">-</span></div>
                                </div>
                                <div class="ms-auto text-end">
                                    <span class="badge bg-success mb-1">User Found</span>
                                    <div class="fw-bold text-success fs-6">
                                        ₹<span id="display_balance">0.00</span>
                                    </div>
                                    <div class="text-muted" style="font-size:11px">Current Balance</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Not Found Alert --}}
                <div class="row mb-4" id="user_not_found" style="display:none !important">
                    <div class="col-md-4"></div>
                    <div class="col-md-8">
                        <div class="alert alert-danger mb-0 py-2">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No user found with this mobile number.
                        </div>
                    </div>
                </div>

                {{-- Transaction Type --}}
                <div class="row mb-4 align-items-center">
                    <div class="col-md-4">
                        <label class="chpass-form-label">Transaction Type</label>
                    </div>
                    <div class="col-md-8">
                        <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="credit" {{ old('type') == 'credit' ? 'selected' : '' }}>
                                ➕ Add Money (Credit)
                            </option>
                            <option value="debit" {{ old('type') == 'debit' ? 'selected' : '' }}>
                                ➖ Withdraw (Debit)
                            </option>
                        </select>
                        @error('type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Amount --}}
                <div class="row mb-4 align-items-center">
                    <div class="col-md-4">
                        <label class="chpass-form-label">Amount (₹)</label>
                    </div>
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text fw-bold">₹</span>
                            <input type="number" name="amount" value="{{ old('amount') }}"
                                class="form-control @error('amount') is-invalid @enderror" placeholder="Enter amount"
                                min="1" required>
                        </div>
                        @error('amount')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="chpass-btn-submit">
                        <i class="bi bi-check-circle me-1"></i> Submit
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- JavaScript: Live user lookup --}}
    <script>
        function fetchUser() {
            const mobile = document.getElementById('mobile_input').value.trim();

            if (mobile.length < 10) {
                alert('Please enter a valid mobile number.');
                return;
            }

            fetch(`{{ route('admin.wallet.find.user') }}?mobile=${mobile}`)
                .then(res => res.json())
                .then(data => {
                    const infoRow = document.getElementById('user_info_row');
                    const notFound = document.getElementById('user_not_found');

                    if (data.found) {
                        document.getElementById('display_name').textContent = data.name;
                        document.getElementById('display_cin').textContent = data.cin;
                        document.getElementById('display_balance').textContent = data.balance;
                        infoRow.style.setProperty('display', 'flex', 'important');
                        notFound.style.setProperty('display', 'none', 'important');
                    } else {
                        infoRow.style.setProperty('display', 'none', 'important');
                        notFound.style.setProperty('display', 'flex', 'important');
                    }
                })
                .catch(() => alert('Something went wrong. Please try again.'));
        }

        // Also trigger on Enter key in mobile field
        document.getElementById('mobile_input').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                fetchUser();
            }
        });

        // Show previously found user if old input exists (after validation error)
        @if(old('mobile'))
            document.addEventListener('DOMContentLoaded', () => fetchUser());
        @endif
    </script>

@endsection