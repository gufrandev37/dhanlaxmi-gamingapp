<div class="sidebar d-flex flex-column">

    {{-- Logo --}}
    {{-- <div class="sidebar-logo text-center py-3">
        <img src="{{ asset('assets/image/logo.png') }}" alt="Logo" width="120">
    </div> --}}
    <div class="sidebar-logo">
    <img src="{{ asset('assets/image/logo.png') }}" alt="Logo" class="logo-img">
</div>

    <ul class="sidebar-menu list-unstyled px-3 flex-grow-1">

        {{-- Dashboard --}}
        <li>
            <a href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- USERS --}}
        @if(
                auth()->guard('admin')->check()
                && auth()->guard('admin')->user()->hasPermission('users.view')
            )
            <li>
                <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Users</span>
                </a>
            </li>
        @endif


        {{-- GAME --}}
        @if(
                auth()->guard('admin')->check()
                && auth()->guard('admin')->user()->hasPermission('game.view')
            )
            <li>
                <a href="{{ route('admin.games') }}" class="{{ request()->routeIs('admin.games*') ? 'active' : '' }}">
                    <i class="bi bi-controller"></i>
                    <span>Game</span>
                </a>
            </li>
        @endif

        <!-- Game price -->


        @if(auth()->guard('admin')->check())
            <li>
                <a href="{{ route('admin.game-price.index') }}"
                    class="{{ request()->routeIs('admin.game-price.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i>
                    <span>Game Price</span>
                </a>
            </li>
        @endif

<!-- Game result -->

        <li>
            <a href="{{ route('admin.game-result.index') }}"
                class="{{ request()->routeIs('admin.game-result.*') ? 'active' : '' }}">
                <i class="bi bi-trophy"></i>
                <span>Game Result</span>
            </a>
        </li>


        <!-- chart  -->

        @if(
                auth()->guard('admin')->check()
                && auth()->guard('admin')->user()->hasPermission('game.view')
            )
            <li>
                <a href="{{ route('admin.bid.chart') }}"
                    class="{{ request()->routeIs('admin.bid.chart') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Bid Chart</span>
                </a>
            </li>
        @endif
        <!-- Dublicate bids -->


        @if(
                auth()->guard('admin')->check()
                && auth()->guard('admin')->user()->hasPermission('game.view')
            )
            <li>
                <a href="{{ route('admin.duplicate.bids') }}"
                    class="{{ request()->routeIs('admin.duplicate.bids') ? 'active' : '' }}">
                    <i class="bi bi-files"></i>
                    <span>Duplicate Bids</span>
                </a>
            </li>
        @endif

        <!-- Bid history -->

        <li>
            <a href="{{ route('admin.bid-history.index') }}"
                class="{{ request()->routeIs('admin.bid-history.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>Bid History</span>
            </a>
        </li>

        <!-- wallet credit debit -->


        @if(
                auth()->guard('admin')->check()
                && auth()->guard('admin')->user()->hasPermission('wallet.manage')
            )
            <li>
                <a href="{{ route('admin.wallet') }}" class="{{ request()->routeIs('admin.wallet') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i>
                    <span>Wallet Management</span>
                </a>
            </li>
        @endif

        {{-- WALLET --}}
        @if(
                auth()->guard('admin')->check()
                && auth()->guard('admin')->user()->hasPermission('wallet.view')
            )
            <li>
                <a href="{{ route('admin.wallet.history') }}"
                    class="{{ request()->routeIs('admin.wallet.history') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Payment History</span>
                </a>
            </li>
        @endif

        {{-- WITHDRAW --}}
        @if(
                auth()->guard('admin')->check()
                && auth()->guard('admin')->user()->hasPermission('withdraw.view')
            )
            <li>
                <a href="{{ route('admin.payment.withdraw') }}"
                    class="{{ request()->routeIs('admin.payment.withdraw') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i>
                    <span>Payment Withdraw</span>
                </a>
            </li>
        @endif

        {{-- PAYMENT HISTORY --}}
        <!-- @if(
                auth()->guard('admin')->check()
                && auth()->guard('admin')->user()->hasPermission('payment.view')
            )
            <li>
                <a href="{{ route('admin.payment.history') }}"
                    class="{{ request()->routeIs('admin.payment.history') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Payment History</span>
                </a>
            </li>
        @endif -->

        {{-- WINNING --}}
        @if(
                auth()->guard('admin')->check()
                && auth()->guard('admin')->user()->hasPermission('winning.view')
            )
            <li>
                <a href="{{ route('admin.winning.history') }}"
                    class="{{ request()->routeIs('admin.winning.history') ? 'active' : '' }}">
                    <i class="bi bi-gift"></i>
                    <span>Winning History</span>
                </a>
            </li>
        @endif

        {{-- NOTIFICATION --}}
        @if(
                auth()->guard('admin')->check()
                && auth()->guard('admin')->user()->hasPermission('notification.view')
            )
            <li>
                <a href="{{ route('admin.notification') }}"
                    class="{{ request()->routeIs('admin.notification') ? 'active' : '' }}">
                    <i class="bi bi-bell"></i>
                    <span>Notification</span>
                </a>
            </li>
        @endif

        {{-- Change Password --}}
        <li>
            <a href="{{ route('admin.password.update') }}"
                class="{{ request()->routeIs('admin.password.update') ? 'active' : '' }}">
                <i class="bi bi-lock"></i>
                <span>Change Password</span>
            </a>
        </li>

        {{-- Manage Admin (Super Admin Only) --}}
        @if(auth()->guard('admin')->check() && auth()->guard('admin')->user()->isSuperAdmin())

            @php
                $manageActive = request()->routeIs('admin.add')
                    || request()->routeIs('admin.manage')
                    || request()->routeIs('admin.view')
                    || request()->routeIs('admin.toggle.status');
            @endphp

            <li class="submenu {{ $manageActive ? 'open' : '' }}">

                <a href="javascript:void(0)"
                    class="submenu-toggle d-flex align-items-center {{ $manageActive ? 'active' : '' }}">
                    <i class="bi bi-person-gear me-2"></i>
                    <span>Manage Admin</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>

                <ul class="submenu-items list-unstyled ps-4">
                    <li><a href="{{ route('admin.add') }}">Add Admin</a></li>
                    <li><a href="{{ route('admin.manage') }}">Manage Admin</a></li>
                </ul>
            </li>

        @endif

    </ul>

    {{-- Logout --}}
    <div class="sidebar-logout text-center py-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-danger text-decoration-none">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>

</div>

<script>
    document.querySelectorAll('.submenu-toggle').forEach(toggle => {
        toggle.addEventListener('click', function () {
            this.parentElement.classList.toggle('open');
        });
    });
</script>