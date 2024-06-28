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

    <!-- Logo Section for big screens -->
    <div class="col-12 col-md-3 d-none d-lg-flex justify-content-center justify-content-lg-start">
        <a class="navbar-brand titlefont mb-0" href="{{ route('home') }}">
            <div class="logo-wrapper">
                <img src="{{ asset('images/akontledger_logo-bg.png') }}" class="logo" alt="Logo">
            </div>
        </a>
    </div>

    <!-- Logo Section for small screens -->
    <div class="col-12 d-lg-none d-flex justify-content-end">
        <a class="navbar-brand titlefont mb-0" href="{{ route('home') }}">
            <div class="logo-wrapper">
                <img src="{{ asset('images/icon_mi.png') }}" class="logo" alt="Logo">
            </div>
        </a>
    </div>

        <!-- User Info and Logout Button -->
        <div class="user-info-container col-md-3 col-sm-12 col-lg-3 d-none d-lg-flex justify-content-end">
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
        <div class="col-md-6 col-sm-6 col-lg-6  d-flex flex-column align-items-center justify-content-center">
            @if (isset($companyName))
                <div class="navbar-brandcxx coyfont" style="color: #333; font-size: 1.5rem;">
                    {{ $companyName }}
                </div>
            @endif
            <div class="navbar-brandcxx" style="color: #333; font-size: 1.5rem; margin-top: 10px;">
                <form class="form-inline my-2 my-lg-0">
                    <div class="input-group">
                        <input id="searchInput" class="form-control" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <ul id="searchResults" class="search-results" style="display: none;"></ul>
            </div>
        </div>

        <!-- Company Name Section for mobile screens -->
        @if (isset($companyName))
            <div class="company-name-mobile  coyfont">
                {{ $companyName }}
            </div>
        @endif
        <div class="searchBox navbar-brandcxx  d-block d-sm-none text-center mt-3 mb-5" style="color: #333; font-size: 1.5rem; margin-top: 10px;">
            <form class="form-inline my-2 my-lg-0">
                <div class="input-group">
                    <input id="searchInputMobile" class="form-control" type="search" placeholder="Search" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
            <ul id="searchResultsMobile" class="search-results" style="display: none;"></ul>
        </div>
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


<style>
    .search-results {
    position: absolute;
    top: 100%;
    left: inherit;
    width: inherit;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 0 0 5px 5px;
    max-height: 200px; 
    overflow-y: auto; 
    z-index: 1000; 
    padding: 0;
    margin: 0;
    list-style-type: none;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.search-results li {
    padding: 8px 12px;
    cursor: pointer;
}

.search-results li a {
    text-decoration: none;
    color: #333;
    font-size: 12px;
}

.search-results li:hover {
    background-color: #f1f1f1;
}

.searchBox {
    position: relative;
    top: -22px !important;
    margin-left: auto;
    margin-right: auto;
    max-width: 300px;
}
</style>