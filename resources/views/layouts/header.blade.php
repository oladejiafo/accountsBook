<style>
    .dropdown-menu.user-drop {
    transform: translate3d(0, 66px, 0) !important;
    border-radius: 0px !important;
}

</style>
<!-- Button to toggle sidebar on small screens -->
{{-- <button class="btn btn-custom d-lg-none" id="sidebarToggle" onclick="toggleSidebar()" style="z-index: 9000;">
    <i class="fas fa-bars"></i>
</button> --}}

<main id="wrapper">
    {{-- <nav id="sidebar"> --}}
        <nav id="sidebar">
            <br>
            @include('layouts.menu-items')
    
        </nav>
        <div class="sidebar-header row align-items-center">
            <div class="col-md-3 col-sm-12 col-lg-3 d-flex justify-content-start">
                <!-- Toggle button for mobile -->
                <button class="btn btn-custom d-lg-none" id="sidebarToggle" onclick="toggleSidebar()" style="z-index: 9000;">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="sidebar-header-left col-md-3 col-sm-12 col-lg-3">
                <!-- Logo for big screens -->
                <a class="navbar-brand titlefont mb-2 d-none d-lg-block" href="{{ route('home') }}">
                    <div class="logo-wrapper">
                        <img src="{{ asset('images/akontledger_logo-bg.png') }}" class="logo" alt="Logo">
                    </div>
                </a>
                <!-- Logo for small screens -->
                <a class="navbar-brand titlefont mb-2 d-lg-none" href="{{ route('home') }}">
                    <div class="logo-wrapper">
                        <img src="{{ asset('images/icon_mi.png') }}"  class="logo" alt="Logo">
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-sm-12 col-lg-6 text-center">
                <!-- Company name -->
                @if (isset($companyName))
                    <div class="navbar-brandcxx coyfont" style="color: #333; font-size: 1.5rem;"> <!-- Adjust font size -->
                        {{ $companyName }}
                    </div>
                @endif
            </div>

            <!-- User info and logout button -->
            <div class="user-info-container col-md-2 col-sm-12 col-lg-3 d-flex justify-content-end">
                <div class="user-info">
                    @if (auth()->check())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle dropdown-user" id="dropdown-toggler" href="#"
                            role="button" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle" style="margin-right: .05rem; color: #333"></i>
                            <span style="color: #333">{{ auth()->user()->name }}</span>
                        </a>
                        <div class="user-drop dropdown-menu dropdown-menu-right list-unstyled" style="background-color: #d0d3da;">
                            <form method="POST" action="{{ route('logout') }}" style="background-color: #d0d3da">
                                @csrf
                                <button type="submit" class="sidebar-text sidebar-subitem sidebar-button"
                                    style="background-color: #d0d3da; border: none; padding: 0;  cursor: pointer; color: #000; text-decoration: underlinel; outline: inherit;">
                                    <span style="font-size: 1.1rem">Logout</span>
                                </button>
                            </form>
                        </div>
                    </li>
                    
                    @endif
                </div>

            </div>
        </div> 

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("active");
          }
    </script>
