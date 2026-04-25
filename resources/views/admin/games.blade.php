@extends('layouts.app')

@section('title', 'Game Management')

@section('content')

    <div class="container-fluid mt-4">

        {{-- Header + Button --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold">Game List</h3>

            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addGameModal">
                <i class="bi bi-plus-circle"></i> Add Game
            </button>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow rounded-3 border-0">
            <div class="card-body">
                 <div class="table-responsive">
                   <table class="table table-hover align-middle">
                     <thead class="bg-dark text-white">
                        <tr>
                            <th>#</th>
                            <th>Game Name</th>
                            <th>Status</th>
                            <th>Result Time</th>
                            <th>Close Time</th>
                            <th>Play Next Day</th>
                            <th>Play Days</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($games as $key => $game)
                            <tr>
                                <td>{{ $games->firstItem() + $key }}</td>

                                <!-- Game Name -->
                                <td>{{ $game->game_name }}</td>

                                <!-- Status Toggle -->
                                <td>
                                    <form action="{{ route('admin.games.toggle', $game->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PATCH')

                                        @if($game->status === 'play')
                                            <button type="submit" class="badge bg-success border-0">
                                                Play (Click to Close)
                                            </button>
                                        @else
                                            <button type="submit" class="badge bg-danger border-0">
                                                Close (Click to Play)
                                            </button>
                                        @endif
                                    </form>
                                </td>

                                <!-- Result Time -->
                                <td>{{ \Carbon\Carbon::parse($game->result_time)->format('h:i A') }}</td>

                                <!-- Close Time -->
                                <td>{{ \Carbon\Carbon::parse($game->close_time)->format('h:i A') }}</td>

                                <!-- Play Next Day -->
                                <td>
                                    <span class="badge bg-{{ $game->play_next_day === 'yes' ? 'info' : 'secondary' }}">
                                        {{ ucfirst($game->play_next_day) }}
                                    </span>
                                </td>

                                <!-- Play Days -->
                                <td>
                                    {{ is_array($game->play_days) ? implode(', ', $game->play_days) : '-' }}
                                </td>

                                <!-- Actions -->
                                <td>
                                    <button class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editGameModal{{ $game->id }}">
                                        Edit
                                    </button>

                                    <form action="{{ route('admin.games.delete', $game->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this game?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- ✅ Edit Modal INSIDE the loop so each game gets its own modal --}}
                            <div class="modal fade" id="editGameModal{{ $game->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">

                                        <form method="POST" action="{{ route('admin.games.update', $game->id) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Game</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row">

                                                    <!-- Game Name -->
                                                    <div class="col-md-6 mb-3">
                                                        <label>Game Name</label>
                                                        <input type="text" name="game_name"
                                                               value="{{ $game->game_name }}"
                                                               class="form-control" required>
                                                    </div>

                                                    <!-- Status -->
                                                    <div class="col-md-6 mb-3">
                                                        <label>Status</label>
                                                        <select name="status" class="form-control">
                                                            <option value="play" {{ $game->status == 'play' ? 'selected' : '' }}>Play</option>
                                                            <option value="close" {{ $game->status == 'close' ? 'selected' : '' }}>Close</option>
                                                        </select>
                                                    </div>

                                                    <!-- Result Time -->
                                                    <div class="col-md-6 mb-3">
                                                        <label>Result Time</label>
                                                        <input type="time" name="result_time"
                                                               value="{{ $game->result_time }}"
                                                               class="form-control">
                                                    </div>

                                                    <!-- Close Time -->
                                                    <div class="col-md-6 mb-3">
                                                        <label>Close Time</label>
                                                        <input type="time" name="close_time"
                                                               value="{{ $game->close_time }}"
                                                               class="form-control">
                                                    </div>

                                                    <!-- Play Next Day -->
                                                    <div class="col-md-6 mb-3">
                                                        <label>Play Next Day</label>
                                                        <select name="play_next_day" class="form-control">
                                                            <option value="no" {{ $game->play_next_day == 'no' ? 'selected' : '' }}>No</option>
                                                            <option value="yes" {{ $game->play_next_day == 'yes' ? 'selected' : '' }}>Yes</option>
                                                        </select>
                                                    </div>

                                                    <!-- Play Days -->
                                                    <div class="col-md-12 mb-3">
                                                        <label>Play Days</label><br>

                                                        @php
                                                            $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                                        @endphp

                                                        @foreach($days as $day)
                                                            <label class="me-3">
                                                                <input type="checkbox"
                                                                       name="play_days[]"
                                                                       value="{{ $day }}"
                                                                       {{ in_array($day, $game->play_days ?? []) ? 'checked' : '' }}>
                                                                {{ $day }}
                                                            </label>
                                                        @endforeach
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-success">Update Game</button>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>
                            {{-- ✅ End Edit Modal --}}

                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No games found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
              </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $games->links() }}
                </div>

            </div>
        </div>

    </div>

    {{-- Add Game Modal --}}
    <div class="modal fade" id="addGameModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form method="POST" action="{{ route('admin.games.store') }}">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Add New Game</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <!-- Game Name -->
                            <div class="col-md-6 mb-3">
                                <label>Game Name</label>
                                <input type="text" name="game_name" class="form-control" required>
                            </div>

                            <!-- Result Time -->
                            <div class="col-md-6 mb-3">
                                <label>Result Time</label>
                                <input type="time" name="result_time" class="form-control" required>
                            </div>

                            <!-- Close Time -->
                            <div class="col-md-6 mb-3">
                                <label>Close Time</label>
                                <input type="time" name="close_time" class="form-control" required>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label>Game Play</label>
                                <select name="status" class="form-control" required>
                                    <option value="play">Play</option>
                                    <option value="close">Close</option>
                                </select>
                            </div>

                            <!-- Play Next Day -->
                            <div class="col-md-6 mb-3">
                                <label>Play Next Day</label>
                                <select name="play_next_day" class="form-control">
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>

                            <!-- Play Days -->
                            <div class="col-md-12 mb-3">
                                <label>Play Days</label><br>

                                @php
                                    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                @endphp

                                @foreach($days as $day)
                                    <label class="me-3">
                                        <input type="checkbox" name="play_days[]" value="{{ $day }}">
                                        {{ $day }}
                                    </label>
                                @endforeach
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            Add Game
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@endsection