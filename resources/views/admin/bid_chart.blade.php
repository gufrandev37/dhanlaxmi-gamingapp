@extends('layouts.app')

@section('title', 'Bid Chart')

@section('content')
<div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Bid Chart</h4>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <h3 class="fw-bold text-primary">{{ $totalBids }}</h3>
                <span class="text-muted">Total Bids</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <h3 class="fw-bold text-success">₹ {{ number_format($totalAmount) }}</h3>
                <span class="text-muted">Total Amount</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <h3 class="fw-bold text-warning">{{ $todayBids }}</h3>
                <span class="text-muted">Today's Bids</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <h3 class="fw-bold text-info">₹ {{ number_format($todayAmount) }}</h3>
                <span class="text-muted">Today's Amount</span>
            </div>
        </div>
    </div>

    @php
        $ptConfig = [
            'jodi'       => ['label' => '🎯 Jodi',       'color' => '#1a3a6e', 'bidColor' => '#e65c00'],
            'crossing'   => ['label' => '✖ Crossing',   'color' => '#5a1a6e', 'bidColor' => '#8e24aa'],
            'copy_paste' => ['label' => '📋 Copy Paste', 'color' => '#1a506e', 'bidColor' => '#0277bd'],
        ];
        $activePt       = $ptConfig[$selectedPlayType];
        $activeColor    = $activePt['color'];
        $activeBidColor = $activePt['bidColor'];

        $activeCounts = match($selectedPlayType) {
            'crossing'   => $crossingCounts,
            'copy_paste' => $copyPasteCounts,
            default      => $jodiCounts,
        };
    @endphp

    {{-- ═══ FILTER BAR ═══ --}}
    <form method="GET" action="{{ route('admin.bid.chart') }}" class="mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row g-3 align-items-end">

                    {{-- Game --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filter by Game</label>
                        <select name="game_id" class="form-select">
                            <option value="">All Games</option>
                            @foreach($games as $game)
                                <option value="{{ $game->id }}" {{ $selectedGame == $game->id ? 'selected' : '' }}>
                                    {{ $game->game_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Play Type --}}
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Play Type</label>
                        <select name="play_type" class="form-select">
                            @foreach($ptConfig as $ptKey => $pt)
                                <option value="{{ $ptKey }}" {{ $selectedPlayType === $ptKey ? 'selected' : '' }}>
                                    {{ $pt['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Picker --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filter by Date</label>
                        <input type="date" name="date" class="form-control"
                               value="{{ $selectedDate ?? '' }}"
                               max="{{ date('Y-m-d') }}">
                    </div>

                    {{-- Quick date shortcuts --}}
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Quick Select</label>
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('admin.bid.chart') }}?date={{ date('Y-m-d') }}&play_type={{ $selectedPlayType }}{{ $selectedGame ? '&game_id='.$selectedGame : '' }}"
                               class="btn btn-sm {{ $selectedDate === date('Y-m-d') ? 'btn-warning' : 'btn-outline-secondary' }}">
                                Today
                            </a>
                            <a href="{{ route('admin.bid.chart') }}?date={{ date('Y-m-d', strtotime('-1 day')) }}&play_type={{ $selectedPlayType }}{{ $selectedGame ? '&game_id='.$selectedGame : '' }}"
                               class="btn btn-sm {{ $selectedDate === date('Y-m-d', strtotime('-1 day')) ? 'btn-warning' : 'btn-outline-secondary' }}">
                                Yesterday
                            </a>
                        </div>
                    </div>

                    {{-- preserve selected numbers --}}
                    @if($selectedNum !== null)
                        <input type="hidden" name="number" value="{{ $selectedNum }}">
                    @endif
                    @if($selectedAndarNum !== null)
                        <input type="hidden" name="andar_number" value="{{ $selectedAndarNum }}">
                    @endif
                    @if($selectedBaharNum !== null)
                        <input type="hidden" name="bahar_number" value="{{ $selectedBaharNum }}">
                    @endif

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn w-100 text-white" style="background:#d4ac1c">Apply</button>
                        <a href="{{ route('admin.bid.chart') }}" class="btn btn-light w-100">Reset</a>
                    </div>
                </div>

                {{-- Active date indicator --}}
                @if($selectedDate)
                    <div class="mt-2">
                        <span class="badge bg-warning text-dark fs-6">
                            📅 Showing bids for: {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                            &nbsp;
                            <a href="{{ route('admin.bid.chart') }}?play_type={{ $selectedPlayType }}{{ $selectedGame ? '&game_id='.$selectedGame : '' }}"
                               class="text-dark text-decoration-none">✕</a>
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </form>

    {{-- ═══ GAME PLAY BID CHART — single grid ═══ --}}

    {{-- Chart Header with quick-switch pills --}}
    <div class="card shadow-sm mb-2">
       <div class="card-header text-white fw-bold d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2"
             style="background:{{ $activeColor }}">
            <span>{{ $activePt['label'] }} Bid Chart (00–100)</span>
            <div class="d-flex gap-2 flex-wrap">
                @foreach($ptConfig as $ptKey => $pt)
                    <a href="{{ route('admin.bid.chart') }}?play_type={{ $ptKey }}{{ $selectedGame ? '&game_id='.$selectedGame : '' }}{{ $selectedDate ? '&date='.$selectedDate : '' }}"
                       class="badge text-decoration-none"
                       style="background:{{ $selectedPlayType === $ptKey ? '#d4ac1c' : 'rgba(255,255,255,0.25)' }};
                              color:#fff;font-size:13px;padding:6px 12px;border-radius:20px;">
                        {{ $pt['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Legend --}}
    <div class="d-flex gap-4 mb-3 align-items-center">
        <div class="d-flex align-items-center gap-2">
            <div style="background:#1a6e1a;width:22px;height:22px;border-radius:5px;"></div>
            <span class="text-muted">No Bids</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div style="background:{{ $activeBidColor }};width:22px;height:22px;border-radius:5px;"></div>
            <span class="text-muted">Has Bids</span>
        </div>
        @if($selectedNum !== null)
            <div class="d-flex align-items-center gap-2">
                <div style="background:#d4ac1c;width:22px;height:22px;border-radius:5px;"></div>
                <span class="text-muted">Selected</span>
            </div>
        @endif
    </div>

    {{-- Single Grid --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(70px,1fr));gap:12px;max-width:900px;margin:auto;">
                @for($i = 0; $i <= 99; $i++)
                    @php
                        $num        = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $data       = $activeCounts->get($num);
                        $hasBids    = $data && $data->total_bids > 0;
                        $isSelected = $selectedNum === $num;
                        $bg         = $isSelected ? '#d4ac1c' : ($hasBids ? $activeBidColor : '#1a6e1a');
                        $url        = route('admin.bid.chart')
                                    . '?number=' . $num
                                    . '&play_type=' . $selectedPlayType
                                    . ($selectedGame ? '&game_id=' . $selectedGame : '')
                                    . ($selectedDate  ? '&date=' . $selectedDate  : '');
                    @endphp
                    <a href="{{ $url }}"
                       title="{{ $hasBids ? $data->total_bids.' bids | ₹'.number_format($data->total_amount) : 'No bids on '.$num }}"
                       style="background:{{ $bg }};color:#fff;font-weight:bold;width:100%;aspect-ratio:1/1;max-width:80px;margin:auto;
                              display:flex;flex-direction:column;align-items:center;justify-content:center;
                              border-radius:8px;font-size:18px;text-decoration:none;
                              border:2px solid rgba(255,255,255,0.25);position:relative;">
                        {{ $num }}
                        @if($hasBids)
                            <span style="font-size:12px;font-weight:600;opacity:0.9;">{{ $data->total_bids }} bid{{ $data->total_bids > 1 ? 's' : '' }}</span>
                            <span style="position:absolute;top:-7px;right:-7px;background:#fff;
                                         color:{{ $isSelected ? '#d4ac1c' : $activeBidColor }};font-size:10px;font-weight:bold;
                                         border-radius:50%;width:20px;height:20px;display:flex;align-items:center;
                                         justify-content:center;border:1px solid {{ $isSelected ? '#d4ac1c' : $activeBidColor }};">
                                {{ $data->total_bids }}
                            </span>
                        @endif
                    </a>
                @endfor
            </div>
        </div>
    </div>

    {{-- Detail Table --}}
    @if($selectedNum !== null)
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center"
                 style="background:{{ $activeColor }}">
                <h5 class="mb-0 text-white fw-bold">
                    {{ $activePt['label'] }} Bids on Number: {{ $selectedNum }}
                    @if($selectedDate)
                        <small class="fw-normal opacity-75">({{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }})</small>
                    @endif
                </h5>
                <div class="d-flex gap-3 align-items-center">
                    {{-- Only non-cancelled counts shown here --}}
                    <span class="badge bg-white text-dark fs-6">{{ $selectedBidCount }} Active Bids</span>
                    <span class="badge bg-white text-dark fs-6">₹ {{ number_format($selectedAmount) }}</span>
                    <a href="{{ route('admin.bid.chart') }}?play_type={{ $selectedPlayType }}{{ $selectedGame ? '&game_id='.$selectedGame : '' }}{{ $selectedDate ? '&date='.$selectedDate : '' }}"
                       class="btn btn-sm btn-light">✕ Close</a>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th><th>User Name</th><th>Customer ID</th>
                            <th>Game</th><th>Play Type</th><th>Amount</th>
                            <th>Status</th><th>Date & Time</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bids as $key => $bid)
                            <tr class="{{ $bid->status === 'cancelled' ? 'table-secondary text-muted' : '' }}">
                                <td>{{ $bids->firstItem() + $key }}</td>
                                <td>{{ $bid->user->name ?? '-' }}</td>
                                <td>{{ $bid->user->cin ?? '-' }}</td>
                                <td>{{ $bid->game->game_name ?? '-' }}</td>
                                <td>
                                    @php $ptBadge = ['jodi'=>'primary','crossing'=>'warning','copy_paste'=>'info']; @endphp
                                    <span class="badge bg-{{ $ptBadge[$bid->play_type] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $bid->play_type)) }}
                                    </span>
                                </td>
                                <td>₹ {{ number_format($bid->amount) }}</td>
                                <td>
                                    <span class="badge
                                        @if($bid->status == 'win') bg-success
                                        @elseif($bid->status == 'lose') bg-danger
                                        @elseif($bid->status == 'cancelled') bg-secondary
                                        @else bg-warning text-dark @endif">
                                        {{ ucfirst($bid->status) }}
                                    </span>
                                </td>
                                <td>{{ $bid->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if($bid->status === 'pending')
                                        <form method="POST"
                                              action="{{ route('admin.bid.cancel', ['type' => 'game', 'id' => $bid->id]) }}"
                                              onsubmit="return confirm('Cancel this bid and refund ₹{{ number_format($bid->amount) }} to {{ $bid->user->name ?? 'user' }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel & Refund</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No bids found for {{ $selectedNum }}.</td></tr>
                        @endforelse
                    </tbody>
                    @if($bids->count())
                        @php $activeTotal = $bids->where('status','!=','cancelled')->sum('amount'); @endphp
                        <tfoot class="table-secondary fw-bold">
                            <tr>
                                <td colspan="5" class="text-end">Active Total:</td>
                                <td>₹ {{ number_format($activeTotal) }}</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
                <div class="mt-3">{{ $bids->links() }}</div>
            </div>
        </div>
    @endif


    {{-- ═══ ANDAR BID CHART ═══ --}}
    <div class="card shadow-sm mb-2">
        <div class="card-header text-white fw-bold" style="background:#1a6e3a">
            🟢 Andar Bid Chart (0–9)
        </div>
    </div>

    <div class="d-flex gap-4 mb-3 align-items-center">
        <div class="d-flex align-items-center gap-2">
            <div style="background:#1a6e1a;width:22px;height:22px;border-radius:5px;"></div>
            <span class="text-muted">No Bids</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div style="background:#1a6e3a;width:22px;height:22px;border-radius:5px;"></div>
            <span class="text-muted">Has Bids</span>
        </div>
        @if($selectedAndarNum !== null)
            <div class="d-flex align-items-center gap-2">
                <div style="background:#d4ac1c;width:22px;height:22px;border-radius:5px;"></div>
                <span class="text-muted">Selected</span>
            </div>
        @endif
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(70px,1fr));gap:12px;max-width:900px;margin:auto;">
                @for($i = 0; $i <= 9; $i++)
                    @php
                        $num        = (string)$i;
                        $data       = $andarCounts->get($num);
                        $hasBids    = $data && $data->total_bids > 0;
                        $isSelected = $selectedAndarNum === $num;
                        $bg         = $isSelected ? '#d4ac1c' : ($hasBids ? '#1a6e3a' : '#1a6e1a');
                        $url        = route('admin.bid.chart')
                                    . '?andar_number=' . $num
                                    . '&play_type=' . $selectedPlayType
                                    . ($selectedGame ? '&game_id=' . $selectedGame : '')
                                    . ($selectedDate  ? '&date=' . $selectedDate  : '');
                    @endphp
                    <a href="{{ $url }}"
                       title="{{ $hasBids ? $data->total_bids.' bids | ₹'.number_format($data->total_amount) : 'No Andar bids on '.$num }}"
                        style="background:{{ $bg }};color:#fff;font-weight:bold;width:100%;
aspect-ratio:1/1;max-width:90px;margin:auto;
display:flex;flex-direction:column;align-items:center;justify-content:center;
border-radius:8px;font-size:24px;text-decoration:none;
border:2px solid rgba(255,255,255,0.25);position:relative;">
                        {{ $num }}
                        @if($hasBids)
                            <span style="font-size:14px;font-weight:600;opacity:0.95;">{{ $data->total_bids }} bid{{ $data->total_bids > 1 ? 's' : '' }}</span>
                            <span style="position:absolute;top:-7px;right:-7px;background:#fff;
                                         color:{{ $isSelected ? '#d4ac1c' : '#1a6e3a' }};font-size:10px;font-weight:bold;
                                         border-radius:50%;width:20px;height:20px;display:flex;align-items:center;
                                         justify-content:center;border:1px solid {{ $isSelected ? '#d4ac1c' : '#1a6e3a' }};">
                                {{ $data->total_bids }}
                            </span>
                        @endif
                    </a>
                @endfor
            </div>
        </div>
    </div>

    @if($selectedAndarNum !== null)
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:#1a6e3a">
                <h5 class="mb-0 text-white fw-bold">
                    Andar Bids on Number: {{ $selectedAndarNum }}
                    @if($selectedDate)
                        <small class="fw-normal opacity-75">({{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }})</small>
                    @endif
                </h5>
                <div class="d-flex gap-3">
                    <span class="badge bg-white text-dark fs-6">{{ $selectedAndarBidCount }} Active Bids</span>
                    <span class="badge bg-white text-dark fs-6">₹ {{ number_format($selectedAndarAmount) }}</span>
                    <a href="{{ route('admin.bid.chart') }}?play_type={{ $selectedPlayType }}{{ $selectedGame ? '&game_id='.$selectedGame : '' }}{{ $selectedDate ? '&date='.$selectedDate : '' }}"
                       class="btn btn-sm btn-light">✕ Close</a>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th><th>User Name</th><th>Customer ID</th>
                            <th>Game</th><th>Number</th><th>Amount</th>
                            <th>Status</th><th>Date & Time</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($andarBids as $key => $bid)
                            <tr class="{{ $bid->status === 'cancelled' ? 'table-secondary text-muted' : '' }}">
                                <td>{{ $andarBids->firstItem() + $key }}</td>
                                <td>{{ $bid->user->name ?? '-' }}</td>
                                <td>{{ $bid->user->cin ?? '-' }}</td>
                                <td>{{ $bid->game->game_name ?? '-' }}</td>
                                <td><span class="badge" style="background:#1a6e3a">{{ $bid->number }}</span></td>
                                <td>₹ {{ number_format($bid->amount) }}</td>
                                <td>
                                    <span class="badge
                                        @if($bid->status == 'win') bg-success
                                        @elseif($bid->status == 'lose') bg-danger
                                        @elseif($bid->status == 'cancelled') bg-secondary
                                        @else bg-warning text-dark @endif">
                                        {{ ucfirst($bid->status) }}
                                    </span>
                                </td>
                                <td>{{ $bid->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if($bid->status === 'pending')
                                        <form method="POST"
                                              action="{{ route('admin.bid.cancel', ['type' => 'andar', 'id' => $bid->id]) }}"
                                              onsubmit="return confirm('Cancel this Andar bid and refund ₹{{ number_format($bid->amount) }} to {{ $bid->user->name ?? 'user' }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel & Refund</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No Andar bids found for number {{ $selectedAndarNum }}.</td></tr>
                        @endforelse
                    </tbody>
                    @if($andarBids->count())
                        @php $andarActiveTotal = $andarBids->where('status','!=','cancelled')->sum('amount'); @endphp
                        <tfoot class="table-secondary fw-bold">
                            <tr><td colspan="5" class="text-end">Active Total:</td><td>₹ {{ number_format($andarActiveTotal) }}</td><td colspan="3"></td></tr>
                        </tfoot>
                    @endif
                </table>
                <div class="mt-3">{{ $andarBids->links() }}</div>
            </div>
        </div>
    @endif


    {{-- ═══ BAHAR BID CHART ═══ --}}
    <div class="card shadow-sm mb-2">
        <div class="card-header text-white fw-bold" style="background:#6e1a1a">
            🔴 Bahar Bid Chart (0–9)
        </div>
    </div>

    <div class="d-flex gap-4 mb-3 align-items-center">
        <div class="d-flex align-items-center gap-2">
            <div style="background:#1a6e1a;width:22px;height:22px;border-radius:5px;"></div>
            <span class="text-muted">No Bids</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div style="background:#6e1a1a;width:22px;height:22px;border-radius:5px;"></div>
            <span class="text-muted">Has Bids</span>
        </div>
        @if($selectedBaharNum !== null)
            <div class="d-flex align-items-center gap-2">
                <div style="background:#d4ac1c;width:22px;height:22px;border-radius:5px;"></div>
                <span class="text-muted">Selected</span>
            </div>
        @endif
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
   <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(70px,1fr));gap:12px;max-width:900px;margin:auto;">
                @for($i = 0; $i <= 9; $i++)
                    @php
                        $num        = (string)$i;
                        $data       = $baharCounts->get($num);
                        $hasBids    = $data && $data->total_bids > 0;
                        $isSelected = $selectedBaharNum === $num;
                        $bg         = $isSelected ? '#d4ac1c' : ($hasBids ? '#6e1a1a' : '#1a6e1a');
                        $url        = route('admin.bid.chart')
                                    . '?bahar_number=' . $num
                                    . '&play_type=' . $selectedPlayType
                                    . ($selectedGame ? '&game_id=' . $selectedGame : '')
                                    . ($selectedDate  ? '&date=' . $selectedDate  : '');
                    @endphp
                    <a href="{{ $url }}"
                       title="{{ $hasBids ? $data->total_bids.' bids | ₹'.number_format($data->total_amount) : 'No Bahar bids on '.$num }}"
                   style="background:{{ $bg }};color:#fff;font-weight:bold;width:100%;
aspect-ratio:1/1;max-width:90px;margin:auto;
display:flex;flex-direction:column;align-items:center;justify-content:center;
border-radius:8px;font-size:24px;text-decoration:none;
border:2px solid rgba(255,255,255,0.25);position:relative;">
                        {{ $num }}
                        @if($hasBids)
                           <span style="font-size:14px;font-weight:600;opacity:0.95;">{{ $data->total_bids }} bid{{ $data->total_bids > 1 ? 's' : '' }}</span>
                            <span style="position:absolute;top:-7px;right:-7px;background:#fff;
                                         color:{{ $isSelected ? '#d4ac1c' : '#6e1a1a' }};font-size:10px;font-weight:bold;
                                         border-radius:50%;width:20px;height:20px;display:flex;align-items:center;
                                         justify-content:center;border:1px solid {{ $isSelected ? '#d4ac1c' : '#6e1a1a' }};">
                                {{ $data->total_bids }}
                            </span>
                        @endif
                    </a>
                @endfor
            </div>
        </div>
    </div>

    @if($selectedBaharNum !== null)
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:#6e1a1a">
                <h5 class="mb-0 text-white fw-bold">
                    Bahar Bids on Number: {{ $selectedBaharNum }}
                    @if($selectedDate)
                        <small class="fw-normal opacity-75">({{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }})</small>
                    @endif
                </h5>
                <div class="d-flex gap-3">
                    <span class="badge bg-white text-dark fs-6">{{ $selectedBaharBidCount }} Active Bids</span>
                    <span class="badge bg-white text-dark fs-6">₹ {{ number_format($selectedBaharAmount) }}</span>
                    <a href="{{ route('admin.bid.chart') }}?play_type={{ $selectedPlayType }}{{ $selectedGame ? '&game_id='.$selectedGame : '' }}{{ $selectedDate ? '&date='.$selectedDate : '' }}"
                       class="btn btn-sm btn-light">✕ Close</a>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th><th>User Name</th><th>Customer ID</th>
                            <th>Game</th><th>Number</th><th>Amount</th>
                            <th>Status</th><th>Date & Time</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($baharBids as $key => $bid)
                            <tr class="{{ $bid->status === 'cancelled' ? 'table-secondary text-muted' : '' }}">
                                <td>{{ $baharBids->firstItem() + $key }}</td>
                                <td>{{ $bid->user->name ?? '-' }}</td>
                                <td>{{ $bid->user->cin ?? '-' }}</td>
                                <td>{{ $bid->game->game_name ?? '-' }}</td>
                                <td><span class="badge" style="background:#6e1a1a">{{ $bid->number }}</span></td>
                                <td>₹ {{ number_format($bid->amount) }}</td>
                                <td>
                                    <span class="badge
                                        @if($bid->status == 'win') bg-success
                                        @elseif($bid->status == 'lose') bg-danger
                                        @elseif($bid->status == 'cancelled') bg-secondary
                                        @else bg-warning text-dark @endif">
                                        {{ ucfirst($bid->status) }}
                                    </span>
                                </td>
                                <td>{{ $bid->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if($bid->status === 'pending')
                                        <form method="POST"
                                              action="{{ route('admin.bid.cancel', ['type' => 'bahar', 'id' => $bid->id]) }}"
                                              onsubmit="return confirm('Cancel this Bahar bid and refund ₹{{ number_format($bid->amount) }} to {{ $bid->user->name ?? 'user' }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel & Refund</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No Bahar bids found for number {{ $selectedBaharNum }}.</td></tr>
                        @endforelse
                    </tbody>
                    @if($baharBids->count())
                        @php $baharActiveTotal = $baharBids->where('status','!=','cancelled')->sum('amount'); @endphp
                        <tfoot class="table-secondary fw-bold">
                            <tr><td colspan="5" class="text-end">Active Total:</td><td>₹ {{ number_format($baharActiveTotal) }}</td><td colspan="3"></td></tr>
                        </tfoot>
                    @endif
                </table>
                <div class="mt-3">{{ $baharBids->links() }}</div>
            </div>
        </div>
    @endif

</div>
@endsection