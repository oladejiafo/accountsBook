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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <!-- Sidebar CSS -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <!-- Dialog Box CSS -->
    <link rel="stylesheet" href="{{ asset('css/dialogbox.css') }}">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>

    </style>
</head>
<body>
@include('layouts.header')
        <div id="content">
            <div class="row">
                <div class="col-md-12">
                    @if(session('status'))
                    <div class="alert alert-{{ session('status_type') }}">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="container" style="margin-top: 15%">
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
    
    // Set timeout to redirect to the login page after the session expires
    const sessionTimeout = setTimeout(function() {
        // Redirect to the login page
        window.location.href = '/login';
    }, timeoutMs);
    
    // Reset the timeout if the user interacts with the page
    document.addEventListener('mousemove', function() {
        clearTimeout(sessionTimeout);
        sessionTimeout = setTimeout(function() {
            // Redirect to the login page
            window.location.href = '/login';
        }, timeoutMs);
    });

// $(document).ready(function() {
//     // Toggle sidebar
//     $('#sidebarToggle').click(function() {
//         $('#sidebar').toggleClass('d-none');
//     });
// });


</script>

</body>
</html>
