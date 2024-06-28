<style>
    .dropdown-menu.user-drop {
        transform: translate3d(0, 66px, 0) !important;
        border-radius: 0px !important;
    }

    /* Ensure spacing and alignment */
    .sidebar-header .btn-custom {
        margin-right: 0.5rem;
        /* Adjust as needed */
    }

    .sidebar-header .logo-wrapper {
        max-width: 100%;
        /* Ensure logo scales correctly */
    }

    .navbar-brand.titlefont {
        display: flex;
        align-items: center;
        justify-content: center;
    }

</style>


<main id="wrapper">
    {{-- <nav id="sidebar"> --}}
    <nav id="sidebar">
        <br>
        @include('layouts.menu-items')

    </nav>


    <div class="sidebar-header row align-items-center">
        <!-- Mobile toggle button -->
        <div class="col-sm-2 d-flex justify-content-start d-lg-none">
            <button class="btn btn-custom" id="sidebarToggle" onclick="toggleSidebar()" style="z-index: 9000;">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Logo Section -->
        <div class="sidebar-header-left col-12 col-md-3 d-flex justify-content-center">
            <!-- Logo for big screens -->
            <a class="navbar-brand titlefont mb-0 d-none d-lg-block" href="{{ route('home') }}">
                <div class="logo-wrapper">
                    <img src="{{ asset('images/akontledger_logo-bg.png') }}" class="logo" alt="Logo">
                </div>
            </a>
            <!-- Logo for small screens -->
            <a class="navbar-brand titlefont mb-0 d-lg-none d-sm-flex justify-content-end" href="{{ route('home') }}">
                <div class="logo-wrapper ml-5">
                    <img src="{{ asset('images/icon_mi.png') }}" class="logo" alt="Logo">
                </div>
            </a>
        </div>

        <!-- User Info and Logout Button -->
        <div class="user-info-container col-md-3 col-sm-12 col-lg-3 d-none d-lg-flex justify-content-end">
            <div id="app">
                <search-autosuggest></search-autosuggest>
            </div>
            <div class="user-info">
                @if (auth()->check())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle dropdown-user" id="dropdown-toggler" href="#"
                            role="button" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle" style="margin-right: .05rem; color: #333"></i>
                            <span style="color: #333;">{{ auth()->user()->name }}</span>
                        </a>
                        <div class="user-drop dropdown-menu dropdown-menu-right list-unstyled"
                            style="background-color: #d0d3da;">
                            <form method="POST" action="{{ route('logout') }}" style="background-color: #d0d3da;">
                                @csrf
                                <button type="submit" class="sidebar-text sidebar-subitem sidebar-button"
                                    style="background-color: #d0d3da; border: none; padding: 0; cursor: pointer; color: #000; text-decoration: underline; outline: inherit;">
                                    <span style="font-size: 1.1rem">Logout</span>
                                </button>
                            </form>
                        </div>
                    </li>
                @endif
            </div>
        </div>

        <!-- Company Name Section for large screens -->
        <div class="col-md-6 col-sm-6 col-lg-6 text-center">
            @if (isset($companyName))
                <div class="navbar-brandcxx coyfont" style="color: #333; font-size: 1.5rem;">
                    {{ $companyName }}
                </div>
            @endif
        </div>

        <!-- Company Name Section for mobile screens -->
        @if (isset($companyName))
            <div class="company-name-mobile  coyfont">
                {{ $companyName }}
            </div>
        @endif
    </div>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("active");
        }
    </script>

<!-- 
    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("active");
            var buttons = document.querySelectorAll("#sidebarToggleLarge, #sidebarToggle");
            buttons.forEach(button => button.classList.toggle("collapsed"));
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            const checkbox = document.getElementById('checkMy');

            checkbox.addEventListener('change', function() {
                toggleSidebar(); // Toggle sidebar when checkbox changes
            });
        });
    </script> -->


