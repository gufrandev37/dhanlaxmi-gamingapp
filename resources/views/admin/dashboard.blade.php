@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $admin = auth()->guard('admin')->user();
@endphp

<div class="py-4">
    <div class="row g-4">


        {{-- Total Users --}}
        @if($admin->isSuperAdmin() || $admin->hasPermission('users.view'))
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-module-card">
                <div class="dashboard-icon-box">
                    <i class="bi bi-people"></i>
                </div>
                <h5 class="dashboard-title">Total Users</h5>
                <h2 class="dashboard-count">
                    <span class="counter" data-target="{{ $totalUsers ?? 0 }}">{{ $totalUsers ?? 0 }}</span>
                </h2>
            </div>
        </div>
        @endif

        {{-- Today Users --}}
        @if($admin->isSuperAdmin() || $admin->hasPermission('users.view'))
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-module-card">
                <div class="dashboard-icon-box">
                    <i class="bi bi-person-plus"></i>
                </div>
                <h5 class="dashboard-title">Today Users</h5>
                <h2 class="dashboard-count">
                    <span class="counter" data-target="{{ $todayUsers ?? 0 }}">{{ $todayUsers ?? 0 }}</span>
                </h2>
            </div>
        </div>
        @endif

        {{-- Wallet Amount --}}
        @if($admin->isSuperAdmin() || $admin->hasPermission('wallet.view'))
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-module-card">
                <div class="dashboard-icon-box">
                    <i class="bi bi-wallet2"></i>
                </div>
                <h5 class="dashboard-title">Wallet Amount</h5>
                <h2 class="dashboard-count">
                    ₹ <span class="counter" data-target="{{ $walletAmount ?? 0 }}">{{ number_format($walletAmount ?? 0, 2) }}</span>
                </h2>
            </div>
        </div>
        @endif

        {{-- Bet Amount --}}
        @if($admin->isSuperAdmin() || $admin->hasPermission('game.view'))
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-module-card">
                <div class="dashboard-icon-box">
                    <i class="bi bi-controller"></i>
                </div>
                <h5 class="dashboard-title">Bet Amount</h5>
                <h2 class="dashboard-count">
                    ₹ <span class="counter" data-target="{{ $betAmount ?? 0 }}">{{ number_format($betAmount ?? 0, 2) }}</span>
                </h2>
            </div>
        </div>
        @endif

        {{-- Win Amount --}}
        @if($admin->isSuperAdmin() || $admin->hasPermission('winning.view'))
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-module-card">
                <div class="dashboard-icon-box">
                    <i class="bi bi-trophy"></i>
                </div>
                <h5 class="dashboard-title">Win Amount</h5>
                <h2 class="dashboard-count">
                    ₹ <span class="counter" data-target="{{ $winAmount ?? 0 }}">{{ number_format($winAmount ?? 0, 2) }}</span>
                </h2>
            </div>
        </div>
        @endif

        {{-- My Earning --}}
        @if($admin->isSuperAdmin())
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-module-card">
                <div class="dashboard-icon-box">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <h5 class="dashboard-title">My Earning</h5>
                <h2 class="dashboard-count">
                    ₹ <span class="counter" data-target="{{ $adminEarning ?? 0 }}">{{ number_format($adminEarning ?? 0, 2) }}</span>
                </h2>
            </div>
        </div>
        @endif

        {{-- Loss --}}
        @if($admin->isSuperAdmin())
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-module-card loss-card">
                <div class="dashboard-icon-box">
                    <i class="bi bi-graph-down"></i>
                </div>
                <h5 class="dashboard-title">Loss</h5>
                <h2 class="dashboard-count">
                    ₹ <span class="counter" data-target="{{ $lossAmount ?? 0 }}">{{ number_format($lossAmount ?? 0, 2) }}</span>
                </h2>
            </div>
        </div>
        @endif

        {{-- Today Deposit --}}
        @if($admin->isSuperAdmin() || $admin->hasPermission('wallet.view'))
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-module-card">
                <div class="dashboard-icon-box">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <h5 class="dashboard-title">Today Deposit</h5>
                <h2 class="dashboard-count">
                    ₹ <span class="counter" data-target="{{ $todayDeposit ?? 0 }}">{{ number_format($todayDeposit ?? 0, 2) }}</span>
                </h2>
            </div>
        </div>
        @endif

        {{-- Total Deposit --}}
        @if($admin->isSuperAdmin() || $admin->hasPermission('wallet.view'))
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-module-card">
                <div class="dashboard-icon-box">
                    <i class="bi bi-bank"></i>
                </div>
                <h5 class="dashboard-title">Total Deposit</h5>
                <h2 class="dashboard-count">
                    ₹ <span class="counter" data-target="{{ $totalDeposit ?? 0 }}">{{ number_format($totalDeposit ?? 0, 2) }}</span>
                </h2>
            </div>
        </div>
        @endif

    </div>{{-- end .row --}}
</div>


@endsection

@section('scripts')
<script>
    // Counter animation — counts up from 0 to data-target value
    document.addEventListener('DOMContentLoaded', function () {
        var counters = document.querySelectorAll('.counter');

        counters.forEach(function (counter) {
            var target = parseFloat(counter.getAttribute('data-target')) || 0;

            // If target is 0, just show 0 immediately — no animation needed
            if (target === 0) {
                counter.textContent = '0';
                return;
            }

            var duration  = 1500;   // ms total animation time
            var steps     = 60;     // number of animation frames
            var stepTime  = duration / steps;
            var current   = 0;
            var increment = target / steps;
            var isDecimal = (target % 1 !== 0);  // show decimals for amounts

            var timer = setInterval(function () {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = isDecimal
                    ? current.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')
                    : Math.floor(current).toLocaleString('en-IN');
            }, stepTime);
        });
    });
</script>
@endsection