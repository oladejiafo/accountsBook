<html class="fixed">

<head>

    <!-- <link href="css/style.css" rel="stylesheet" type="text/css" media="all" /> -->
    <!-- font-awesome-icons -->

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <!-- Web Fonts  -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/font-awesome/css/font-awesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/magnific-popup/magnific-popup.css') }}" />

    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/stylesheets/theme.css') }}" />

    <!-- Skin CSS -->
    <link rel="stylesheet" href="{{ asset('assets/stylesheets/skins/default.css') }}" />

    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/stylesheets/theme-custom.css') }}">

    <!-- Head Libs -->
    <script src="{{ asset('js/modernizr/modernizr.js') }}"></script>

</head>

<body>

    <section class="body">
        <!-- start: header -->
        <header class="header">
            <div class="logo-container">
                <a class="navbar-brand titlefont mb-2" href="{{ route('home') }}">
                    <div class="logo-wrapper">
                        <img src="{{ asset('images/akontledger_logo-bg.png') }}" class="logo" alt="Logo">
                    </div>
                </a>
                <div class="visible-xs toggle-sidebar-left" style="background-color:#000" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
                    <i class="fa fa-bars" style="font-size:16px;margin-top:5px;" aria-label="Toggle sidebar"></i>
                </div>
            </div>
            <span class="separator"></span>
            <div align="center" style="width:60%;display:inline-block; vertical-align:middle">
                <div class="navbar-brandc coyfont text-center" style="color: #333">
                    {{ $companyName }}
                </div>
            </div>
            <span class="separator"></span>
            <!-- start: search & user box -->

            <div class="header-right">


                <div id="userbox" class="userbox">
                    @if (auth()->check())
                    <a href="#" data-toggle="dropdown">
                        <figure class="profile-picture">
                            <i class="fas fa-user-circle" style="margin-right: .05rem; color: #333"></i>
                        </figure>
                        <div class="profile-info" data-lock-name="" data-lock-email="">
                            <span style="color: #333">{{ auth()->user()->name }}</span>

                        </div>

                        <i class="fa custom-caret"></i>
                    </a>

                    <div class="dropdown-menu">
                        <ul class="list-unstyled">
                            <li class="divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" style="background-color: #d0d3da">
                                    @csrf
                                    <button type="submit" class="sidebar-text sidebar-subitem sidebar-button" style="background-color: #d0d3da; border: none; padding: 0;  cursor: pointer; color: #000; text-decoration: underlinel; outline: inherit;">
                                        <span style="font-size: 1.1rem">Logout</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            <!-- end: search & user box -->
        </header>
        <!-- end: header -->

        <div class="inner-wrapper">

            <!-- start: sidebar -->
            <aside id="sidebar-left" class="sidebar-left">

                <div class="sidebar-header">
                    <div class="sidebar-title">
                        Navigation
                    </div>
                    <div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
                        <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
                    </div>
                </div>


                        <nav id="menu" class="nav-main" role="navigation">
                            @include('layouts.menu-items')
                        </nav>

                        <hr class="separator" />



            </aside>
            <!-- end: sidebar -->

            <script src="{{ asset('js/bootstrap.min.js') }}"></script>
            <!-- meanmenu JS
		============================================ -->
            <script src="{{ asset('js/jquery.meanmenu.js') }}"></script>