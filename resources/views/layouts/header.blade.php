<style>
    .dropdown-menu.user-drop {
    transform: translate3d(0, 66px, 0) !important;
    border-radius: 0px !important;
}

</style>
<!-- Button to toggle sidebar on small screens -->
<button class="btn btn-custom d-lg-none" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Dialog Box CSS  -->
<!-- Your dialog box HTML here -->

<main id="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header row align-items-center">
            <div class="sidebar-header-left col-md-3">
                <a class="navbar-brand titlefont mb-2" href="{{ route('home') }}">
                    <div class="logo-wrapper">
                        <img src="{{ asset('images/akontledger_logo-bg.png') }}"  class="logo"
                            alt="Logo">
                    </div>
                </a>
                {{-- @if (isset($companyName))
                    <div class="navbar-brandc coyfont text-center" style="color: #333">
                        {{ $companyName }}
                    </div>
                @endif --}}
            </div>

            <div class="col-md-6 text-center">
                @if (isset($companyName))
                <div class="navbar-brandcxx coyfont" style="color: #333">
                    {{ $companyName }}
                </div>
                @endif
                {{-- <form class="form-inline" action="#" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="query" placeholder="Search"
                            aria-label="Search" aria-describedby="basic-addon1">
                        <div class="input-group-prepend">
                            <button type="submit" class="input-group-text" id="basic-addon1">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form> --}}
            </div>

            <!-- User info and logout button -->
            <div class="user-info-container col-md-3 d-flex justify-content-end">
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

        <br>
        @include('layouts.menu-items')

    </nav>
