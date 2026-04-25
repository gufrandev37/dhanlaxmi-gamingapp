@extends('layouts.app')

@section('title', 'Bid History')

@section('content')

<div class="container-fluid">

   {{-- FILTER --}}
{{-- FILTER --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">

        <form method="GET" action="{{ route('admin.bid-history.index') }}">
            <div class="row align-items-end g-3">

                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted mb-1">
                        Select Game
                    </label>
                    <select name="game_id" class="form-select">
                        @foreach($games as $game)
                            <option value="{{ $game->id }}"
                                {{ $gameId == $game->id ? 'selected' : '' }}>
                                {{ $game->game_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <button type="submit"
                            class="btn btn-warning text-white px-4 fw-semibold">
                        Apply
                    </button>

                    <a href="{{ route('admin.bid-history.index') }}"
                       class="btn btn-light border px-4 fw-semibold ms-2">
                        Reset
                    </a>
                </div>

            </div>
        </form>

    </div>
</div>

    {{-- TOTAL BID RIGHT ALIGN --}}
    <div class="text-end mb-2">
        <span class="total-pill">
            Total Bid Amount :: {{ $totalBidAmount }}
        </span>
    </div>

    {{-- SELECTED GAME --}}
    <div class="mb-3 fw-bold">
        Selected Game :
        {{ $selectedGame?->game_name }}
        @if($selectedGame)
            , {{ $selectedGame->created_at->format('Y-m-d') }}
        @endif
    </div>

    {{-- 00–99 GRID --}}
   <div class="row g-1 mb-4">
        @foreach($jodiData as $number => $amount)
            <div class="col-3 col-sm-2 col-md-1">
                <div class="bid-box">
                    <div class="bid-number">{{ $number }}</div>
                    @if($amount > 0)
                        <div class="bid-amount">{{ $amount }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- ANDAR --}}
    <div class="fw-bold mb-1">Ander</div>
    <<div class="row g-1 mb-4">
        @foreach($andarData as $number => $amount)
            <div class="col-3 col-sm-2 col-md-1">
                <div class="bid-box">
                    <div class="bid-number">{{ $number }}</div>
                    @if($amount > 0)
                        <div class="bid-amount">{{ $amount }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- BAHAR --}}
    <div class="fw-bold mb-1">Bahar</div>
    <div class="row g-1 mb-4">
        @foreach($baharData as $number => $amount)
           <div class="col-3 col-sm-2 col-md-1">
                <div class="bid-box">
                    <div class="bid-number">{{ $number }}</div>
                    @if($amount > 0)
                        <div class="bid-amount">{{ $amount }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- TOTAL SUMMARY --}}
    <div class="mt-3 d-flex flex-wrap gap-2">
        <div class="total-pill">Total Jodi Amount :: {{ $totalJodiAmount }}</div>
        <div class="total-pill">Total Ander Amount :: {{ $totalAndarAmount }}</div>
        <div class="total-pill">Total Bahar Amount :: {{ $totalBaharAmount }}</div>
        <div class="total-pill">Total Bid Amount :: {{ $totalBidAmount }}</div>
        <div class="total-pill">Total Win Amount :: {{ $totalWinAmount }}</div>
        <div class="total-pill">Profit :: {{ $profit }}</div>
    </div>
   

</div>

@endsection