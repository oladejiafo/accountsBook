<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <link rel="icon" href="{{ asset('images/favicon/favicon.ico') }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ asset('images/favicon//favicon.ico') }}" type="image/x-icon" />

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dialogbox.css') }}">

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
                    
                    <div class="container top-margin-10">
                        @yield('content')
                    </div>
                </div>
            </div>
            <br>
        </div>
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- colResizable Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/colresizable/1.6.0/colResizable-1.6.min.js"></script>
    <!-- Other scripts -->
    <script src="{{ asset('js/main.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>

    <script src="{{ asset('js/jquery-3.3.1.slim.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Check if the user is authenticated
            var isAuthenticated = "{{ auth()->check() }}";
    
            // If not authenticated, redirect to the login page
            if (!isAuthenticated) {
                window.location.href = "{{ route('login') }}";
            }

            // Initialize colResizable
            $(".resizable-table").colResizable({
                liveDrag: true,
                postbackSafe: true
            });

            // Session timeout
            const timeoutDuration = 120;
            const timeoutMs = timeoutDuration * 60 * 1000;
            let sessionTimeout = setTimeout(function() {
                window.location.href = '/login';
            }, timeoutMs);

            document.addEventListener('mousemove', function() {
                clearTimeout(sessionTimeout);
                sessionTimeout = setTimeout(function() {
                    window.location.href = '/login';
                }, timeoutMs);
            });

            // Full-width table
            $('table.table').addClass('full-width-table');
        });
    </script>

</body>
</html>
