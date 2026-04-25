@extends('layouts.app')

@section('title', 'Game Result')

@section('content')
<div>

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

    <h5 class="fw-bold mb-3">Game Result</h5>

    {{-- Date Filter --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.game-result.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label mb-1 small">Date</label>
                        <input type="date" name="date"
                               class="form-control form-control-sm"
                               value="{{ request('date') }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('admin.game-result.index') }}"
                           class="btn btn-sm btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <h6 class="fw-bold mb-2">Jodi</h6>
    <div class="card mb-4">
        <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-sm align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>City Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Jodi Result</th>
                        <th>Andar Result</th>
                        <th>Bahar Result</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($games as $index => $game)
                        @php
                            // ✅ Use array_key_exists so "0" is never treated as empty/false
                            $hasJodi  = array_key_exists('jodi',  $game->results);
                            $hasAndar = array_key_exists('andar', $game->results);
                            $hasBahar = array_key_exists('bahar', $game->results);
                            $declared = $hasJodi && $hasAndar && $hasBahar;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $game->game_name }}</td>
                            <td>{{ $game->created_at->format('d M Y') }}</td>
                            <td>{{ ucfirst($game->status) }}</td>

                            {{-- Jodi --}}
                            <td>
                                @if($hasJodi)
                                    <strong>{{ $game->results['jodi'] }}</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Andar (first digit) — "0" must show! --}}
                            <td>
                                @if($hasAndar)
                                    <strong>{{ $game->results['andar'] }}</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Bahar (last digit) --}}
                            <td>
                                @if($hasBahar)
                                    <strong>{{ $game->results['bahar'] }}</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>
                                @if(!$declared)
                                    <button class="btn btn-sm btn-primary"
                                            onclick="openModal({{ $game->id }}, '{{ $game->game_name }}')">
                                        Declare Result
                                    </button>
                                @else
                                    <span class="badge bg-success">Declared</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted py-3">No games found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $games->links() }}</div>

</div>

{{-- Modal --}}
<div class="modal fade" id="resultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="modalTitle">Declare Result</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.game-result.declare') }}" method="POST">
                @csrf
                <input type="hidden" name="game_id" id="modalGameId">

                <div class="modal-body">

                    <div class="alert alert-info py-2 small mb-3" id="splitPreview" style="display:none;">
                        <strong>Preview:</strong>
                        Jodi → <span id="previewJodi">—</span> &nbsp;|&nbsp;
                        Andar → <span id="previewAndar">—</span> &nbsp;|&nbsp;
                        Bahar → <span id="previewBahar">—</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Winning Number
                            <small class="text-muted fw-normal">(2 digits, e.g. 05)</small>
                        </label>
                        <input type="text"
                               name="win_number"
                               id="winNumberInput"
                               class="form-control"
                               maxlength="2"
                               pattern="[0-9]{2}"
                               placeholder="e.g. 05"
                               oninput="updatePreview(this.value)"
                               required>
                        <div class="form-text">
                            First digit → Andar &nbsp;|&nbsp; Last digit → Bahar &nbsp;|&nbsp; Full → Jodi
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success"
                            onclick="return confirmDeclare()">
                        Declare All
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(gameId, gameName) {
    document.getElementById('modalGameId').value = gameId;
    document.getElementById('modalTitle').textContent = 'Declare — ' + gameName;
    document.getElementById('winNumberInput').value = '';
    document.getElementById('splitPreview').style.display = 'none';
    new bootstrap.Modal(document.getElementById('resultModal')).show();
}

function updatePreview(val) {
    const preview = document.getElementById('splitPreview');
    if (val.length === 2 && /^[0-9]{2}$/.test(val)) {
        document.getElementById('previewJodi').textContent  = val;
        document.getElementById('previewAndar').textContent = val.charAt(0);
        document.getElementById('previewBahar').textContent = val.charAt(1);
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

function confirmDeclare() {
    const val = document.getElementById('winNumberInput').value;
    if (!/^[0-9]{2}$/.test(val)) {
        alert('Please enter exactly 2 digits (e.g. 05)');
        return false;
    }
    return confirm(
        'Declare Result?\n\nJodi: ' + val +
        '\nAndar: ' + val.charAt(0) +
        '\nBahar: ' + val.charAt(1) +
        '\n\nThis will credit all winners immediately.'
    );
}
</script>

@endsection