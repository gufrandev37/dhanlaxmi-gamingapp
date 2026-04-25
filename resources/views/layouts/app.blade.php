<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>

<div class="layout-wrapper">

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @include('layouts.sidebar')

    <div class="main-content">

        <!-- Mobile Topbar -->
        <div class="topbar d-lg-none d-flex align-items-center justify-content-between">
            <button class="btn btn-light" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h6 class="mb-0 text-white">@yield('title')</h6>
        </div>

        <div class="content-wrapper">
            @yield('content')
        </div>

    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById("sidebarToggle")?.addEventListener("click", function () {
    document.querySelector(".sidebar").classList.toggle("active");
    document.getElementById("sidebarOverlay").classList.toggle("active");
});

document.getElementById("sidebarOverlay")?.addEventListener("click", function () {
    document.querySelector(".sidebar").classList.remove("active");
    this.classList.remove("active");
});

document.addEventListener("DOMContentLoaded", function () {

    const counters = document.querySelectorAll(".counter");

    counters.forEach(counter => {

        const target = parseFloat(counter.getAttribute("data-target"));
        let count = 0;
        const speed = 60;
        const increment = target / speed;

        const isMoney = counter.classList.contains("money");

        const updateCount = () => {
            count += increment;

            if (count < target) {
                counter.innerText = formatValue(count);
                requestAnimationFrame(updateCount);
            } else {
                counter.innerText = formatValue(target);
            }
        };

        const formatValue = (num) => {

            if (isMoney) {
                return num.toLocaleString("en-IN", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                return Math.floor(num);
            }
        };

        updateCount();
    });

});

</script>

@yield('scripts')

</body>
</html>
