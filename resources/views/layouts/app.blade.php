<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <!-- Sidebar CSS -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <!-- Dialog Box CSS -->
    <link rel="stylesheet" href="{{ asset('css/dialogbox.css') }}">

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    
        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
    <script src="{{ asset('js/app.js') }}" defer></script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</head>
<body>

    <!-- Dialog Box CSS  -->
    <div id="dialogoverlay"></div>
    <div id="dialogbox">
        <div class="align-middle">
            <div id="dialogboxhead"></div>
            <div id="dialogboxbody"></div>
            <div id="dialogboxfoot"></div>
        </div>
    </div>

    <main id="wrapper">

        <nav id="sidebar">

            <div class="sidebar-header">
                <a class="navbar-brand titlefont mr-4" href="{{ route('home') }}"><h3>AfriLedger </h3></a>
                @if($companyName)
                    <div class="navbar-brandc coyfont mr-4 text-center">
                        [ {{ $companyName }} ]
                    </div>
                @endif  
            </div>
          
        
            <ul class="list-unstyled sidebar-components">
                <li> 
                    <a class="sidebar-text sidebar-button" href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a>
                </li>               
                <li>
                    <a href="#inventorySubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-boxes"></i> Inventory</a>
                    <ul class="collapse list-unstyled" id="inventorySubmenu">
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('new-stock') }}"><i class="fas fa-dot-circle"></i> Add New</a> </li>
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('inventory') }}"><i class="fas fa-dot-circle"></i> Inventory List</a> </li>
                    </ul>
                </li>
                <li>
                    <a href="#purchaseSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-cash-register"></i> Purchases</a>
                    <ul class="collapse list-unstyled" id="purchaseSubmenu">
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{% url 'select-supplier' %}"><i class="fas fa-dot-circle"></i> New Incoming Stock</a> </li>
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{% url 'purchases-list' %}"><i class="fas fa-dot-circle"></i> Purchases List</a> </li>
                    </ul>
                </li>
                <li>
                    <a href="#saleSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-dollar-sign"></i> Sales</a>
                    <ul class="collapse list-unstyled" id="saleSubmenu">
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{% url 'new-sale' %}"><i class="fas fa-dot-circle"></i> New Outgoing Stock</a> </li>
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{% url 'sales-list' %}"><i class="fas fa-dot-circle"></i> Sales Orders</a> </li>
                    </ul>
                </li>
                <li>
                    <a href="#supplierSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-dolly-flatbed"></i> Suppliers</a>
                    <ul class="collapse list-unstyled" id="supplierSubmenu">
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{% url 'new-supplier' %}"><i class="fas fa-dot-circle"></i> Add New Supplier</a> </li>
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{% url 'suppliers-list' %}"><i class="fas fa-dot-circle"></i> Suppliers List</a> </li>
                    </ul>
                </li>
                <li>
                    <a class="sidebar-text sidebar-button" href="{{ route('about') }}"><i class="fas fa-info-circle"></i> About</a>
                </li>

                <li class="bottomleft">
                    <a href="#UserSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-user-circle"></i> @if(request()->user())
                        <p>User: {{ request()->user()->name }}</p>
                    @endif
                    </a>
                    <ul class="collapse list-unstyled" id="UserSubmenu">
                     
                            <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{% url 'admin:index' %}"><i class="fas fa-dot-circle"></i> Admin Page</a> </li>
         
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="sidebar-text sidebar-subitem sidebar-button" style="background: none; border: none; padding: 0; font: inherit; cursor: pointer; color: inherit; text-decoration: underline; outline: inherit;">
                                        <i class="fas fa-dot-circle"></i> Logout
                                    </button>
                                </form>
                                {{-- <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('logout') }}"><i class="fas fa-dot-circle"></i> Logout</a>  --}}
                            </li>
                    </ul>
                </li>
            </ul>

        </nav>

        <div id="content">

            <div class="row">
                <div class="col-md-12">

                    @if(session('status'))
                    <div class="alert alert-{{ session('status_type') }}">
                        {{ session('status') }}
                    </div>
                    @endif

                    <div class="container">
                        @yield('content')
                    </div>

                </div>
            </div>
            <br>

        </div>

    </main>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{ asset('js/jquery-3.3.1.slim.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Check if the user is authenticated
            var isAuthenticated = "{{ auth()->check() }}";
    
            // If not authenticated, redirect to the login page
            if (!isAuthenticated) {
                window.location.href = "{{ route('login') }}";
            }
        });
    </script>

    <script>
        // Timeout duration in minutes (should match Laravel session lifetime)
        const timeoutDuration = 120;
        
        // Calculate timeout in milliseconds
        const timeoutMs = timeoutDuration * 60 * 1000;
        
        // Set timeout to show a prompt before the session expires
        const sessionTimeout = setTimeout(function() {
            // Display a modal, redirect to login page, or show a message to the user
            alert('Your session is about to expire. Please save your work.');
        }, timeoutMs);
        
        // Reset the timeout if the user interacts with the page
        document.addEventListener('mousemove', function() {
            clearTimeout(sessionTimeout);
            sessionTimeout = setTimeout(function() {
                alert('Your session is about to expire. Please save your work.');
            }, timeoutMs);
        });
    </script>
    
</body>
</html>
