@extends('layouts.app')

@section('title', 'Winning History')

@section('content')
<div>

    <h5 class="fw-bold mb-3">Winning History</h5>

    {{-- Summary Cards --}}
  {{-- Summary Cards --}}
  <div class="row g-2 mb-3">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="stats-card text-center">
                <h2 class="fs-5">{{ $totalWinners }}</h2>
                <span class="stat-label">Total Winners</span>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="stats-card text-center">
                <h2 class="fs-5">₹{{ number_format($totalWinAmount, 2) }}</h2>
                <span class="stat-label">Total Win Amount</span>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.winning.history') }}">
                <div class="row g-2 align-items-end">

                    {{-- Search --}}
                    <div class="col-md-3">
                        <label class="form-label form-label-sm mb-1 text-muted">Name / Phone</label>
                        <input type="text" name="search"
                               value="{{ request('search') }}"
                               class="form-control form-control-sm"
                               placeholder="Name / Email / Phone">
                    </div>

                    {{-- Game Filter --}}
                    <div class="col-md-2">
                        <label class="form-label form-label-sm mb-1 text-muted">Game</label>
                        <select name="game_id" class="form-select form-select-sm">
                            <option value="">All Games</option>
                            @foreach($games as $game)
                                <option value="{{ $game->id }}"
                                    {{ request('game_id') == $game->id ? 'selected' : '' }}>
                                    {{ $game->game_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- From --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label form-label-sm mb-1 text-muted">From</label>
                        <input type="date" name="from"
                               value="{{ request('from') }}"
                               class="form-control form-control-sm">
                    </div>

                    {{-- To --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label form-label-sm mb-1 text-muted">To</label>
                        <input type="date" name="to"
                               value="{{ request('to') }}"
                               class="form-control form-control-sm">
                    </div>

                    {{-- Buttons --}}
                    <div class="col-auto d-flex gap-1 align-items-end">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('admin.winning.history') }}"
                           class="btn btn-sm btn-secondary">Reset</a>
                    </div>
                </div>

                {{-- Active filter badges --}}
                @if(request('search') || request('game_id') || request('from') || request('to'))
                    <div class="mt-2 d-flex flex-wrap gap-2">
                        @if(request('search'))
                            <span class="badge bg-primary">
                                Search: {{ request('search') }}
                                <a href="{{ route('admin.winning.history', array_merge(request()->except('search','page'))) }}"
                                   class="text-white text-decoration-none ms-1">✕</a>
                            </span>
                        @endif
                        @if(request('game_id'))
                            @php $gName = $games->firstWhere('id', request('game_id'))?->game_name ?? request('game_id'); @endphp
                            <span class="badge bg-success">
                                Game: {{ $gName }}
                                <a href="{{ route('admin.winning.history', array_merge(request()->except('game_id','page'))) }}"
                                   class="text-white text-decoration-none ms-1">✕</a>
                            </span>
                        @endif
                        @if(request('from'))
                            <span class="badge bg-warning text-dark">
                                From: {{ \Carbon\Carbon::parse(request('from'))->format('d M Y') }}
                                <a href="{{ route('admin.winning.history', array_merge(request()->except('from','page'))) }}"
                                   class="text-dark text-decoration-none ms-1">✕</a>
                            </span>
                        @endif
                        @if(request('to'))
                            <span class="badge bg-warning text-dark">
                                To: {{ \Carbon\Carbon::parse(request('to'))->format('d M Y') }}
                                <a href="{{ route('admin.winning.history', array_merge(request()->except('to','page'))) }}"
                                   class="text-dark text-decoration-none ms-1">✕</a>
                            </span>
                        @endif
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Game</th>
                        <th>Game Type</th>
                        <th>Number</th>
                        <th>Bet Amount</th>
                        <th>Win Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($winnings as $index => $w)
                        <tr>
                            <td>{{ $winnings->firstItem() + $index }}</td>
                            <td>{{ $w->user->name ?? '-' }}</td>
                            <td>{{ $w->user->phone ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $w->game_name ?? '-' }}
                                </span>
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $w->play_type)) }}</td>
                            <td><strong>{{ $w->number }}</strong></td>
                            <td>₹{{ number_format($w->amount, 2) }}</td>
                            <td class="text-success fw-bold">₹{{ number_format($w->win_amount, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($w->updated_at)->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-muted py-3">No winners found.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($winnings->count() > 0)
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="7" class="text-end">Total</td>
                            <td class="text-success">₹{{ number_format($winnings->sum('win_amount'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
  </div>
    <div class="mt-3">
        {{ $winnings->withQueryString()->links() }}
    </div>

</div>
@endsection