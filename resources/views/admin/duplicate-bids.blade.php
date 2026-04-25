@extends('layouts.app')

@section('title', 'Duplicate Bids')

@section('content')

<div>

    {{-- Header --}}
   <div class="d-flex justify-content-between align-items-center mb-4 gap-2 flex-wrap">
        <h4 class="fw-bold mb-0">Duplicate Bids</h4>
        <span class="badge bg-danger fs-6">{{ $duplicates->count() }} Duplicate Groups</span>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse($duplicates as $dup)
        @php
            $bidIds = explode(',', $dup->bid_ids);
        @endphp

        <div class="card shadow-sm mb-4 border-0">

            {{-- Card Header - Bid Info --}}
            <div class="card-header text-white fw-bold d-flex flex-wrap gap-3 align-items-center"
                 style="background:#1a3a5c">
                <span><i class="bi bi-person-fill me-1"></i> {{ $dup->user->name ?? '-' }}</span>
                <span class="text-white-50">|</span>
                <span><i class="bi bi-controller me-1"></i> {{ $dup->game->game_name ?? '-' }}</span>
                <span class="text-white-50">|</span>
                <span><i class="bi bi-hash me-1"></i> Number: {{ $dup->number }}</span>
                <span class="text-white-50">|</span>
                <span class="text-uppercase">
                    <i class="bi bi-play-circle me-1"></i>
                    {{ str_replace('_', ' ', $dup->play_type) }}
                </span>
                <span class="ms-auto d-flex gap-2">
                    <span class="badge bg-warning text-dark">
                        {{ $dup->total_bids }} Bids
                    </span>
                    <span class="badge bg-success">
                        ₹ {{ number_format($dup->total_amount) }}
                    </span>
                </span>
            </div>

            <div class="card-body">
                <div class="row g-4">

                    {{-- LEFT: Change Number on First Bid --}}
                    <div class="col-md-4">
                        <div class="card h-100 border border-warning">
                            <div class="card-header bg-warning text-dark fw-semibold">
                                <i class="bi bi-pencil-square me-1"></i>
                                Change Number (First Bid)
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Change the number of the <strong>first bid</strong>
                                    (ID: #{{ $bidIds[0] }}) to a new number.
                                </p>
                                <form action="{{ url('admin/duplicate-bids/change-number/' . $bidIds[0]) }}"
                                      method="POST">
                                    @csrf
                                    <div class="input-group">
                                        <input type="number"
                                               name="number"
                                               class="form-control"
                                               placeholder="New number (0-100)"
                                               min="0" max="100" required>
                                        <button class="btn btn-warning fw-semibold">
                                            Change
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- MIDDLE: Delete Extra Duplicate Bids --}}
                    <div class="col-md-4">
                        <div class="card h-100 border border-danger">
                            <div class="card-header bg-danger text-white fw-semibold">
                                <i class="bi bi-trash me-1"></i>
                                Delete Duplicate Bids
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    These are the <strong>extra duplicate bids</strong>.
                                    Keep the first one, delete the rest.
                                </p>

                                {{-- Show all bids with delete button --}}
                                <div class="d-flex flex-column gap-2">
                                    {{-- First bid - cannot delete --}}
                                    <div class="d-flex justify-content-between align-items-center
                                                border rounded px-3 py-2 bg-light">
                                        <span class="small">
                                            Bid #{{ $bidIds[0] }}
                                            <span class="badge bg-success ms-1">Keep</span>
                                        </span>
                                    </div>

                                    {{-- Extra bids - can delete --}}
                                    @foreach(array_slice($bidIds, 1) as $bidId)
                                        <div class="d-flex justify-content-between align-items-center
                                                    border border-danger rounded px-3 py-2">
                                            <span class="small text-danger">
                                                Bid #{{ $bidId }}
                                                <span class="badge bg-danger ms-1">Duplicate</span>
                                            </span>
                                            <form action="{{ url('admin/duplicate-bids/delete/' . $bidId) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete bid #{{ $bidId }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Add New Bid for this User --}}
                    <div class="col-md-4">
                        <div class="card h-100 border border-success">
                            <div class="card-header bg-success text-white fw-semibold">
                                <i class="bi bi-plus-circle me-1"></i>
                                Add New Bid for This User
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Add a completely <strong>new bid</strong> for this user
                                    on a different number.
                                </p>
                                <form action="{{ route('admin.duplicate.bids.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id"   value="{{ $dup->user_id }}">
                                    <input type="hidden" name="game_id"   value="{{ $dup->game_id }}">
                                    <input type="hidden" name="play_type" value="{{ $dup->play_type }}">

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">New Number</label>
                                        <input type="number"
                                               name="number"
                                               class="form-control"
                                               placeholder="0 - 100"
                                               min="0" max="100" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Amount (₹)</label>
                                        <input type="number"
                                               name="amount"
                                               class="form-control"
                                               placeholder="Enter amount"
                                               min="1" required>
                                    </div>
                                    <button class="btn btn-success w-100 fw-semibold">
                                        <i class="bi bi-plus-circle me-1"></i> Add Bid
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    @empty
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-check-circle fs-1 text-success"></i>
                <p class="mt-3 fs-5">No duplicate bids found. All clean!</p>
            </div>
        </div>
    @endforelse

</div>

@endsection