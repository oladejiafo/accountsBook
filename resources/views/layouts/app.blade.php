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
<style>
    .logo-wrapper {
        max-width: 100%;
        height: auto;
        color: inherit; /* Ensure the logo inherits the color of its parent */
    }
</style>
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
                <a class="navbar-brand titlefont mr-1" href="{{ route('account.dashboard') }}">

                    <div class="logo-wrapper">
                        <img src="{{ asset('images/akontledger_logo_white.png') }}" class="logo" alt="Logo">
                    </div>
                </a>
                @if(isset($companyName))
                    <div class="navbar-brandc coyfont mr-1 text-center">
                        {{ $companyName }}
                    </div>
                @endif  
            </div>
            
            <ul class="list-unstyled sidebar-components" id="scrollable-menu">
                <!-- Grouping for Dashboards -->
                <li>
                    <span class="sidebar-textx sidebar-button coyfont mr-4 mt-2">DASHBOARDS</span>
                    <ul class="list-unstyled">
                        <li><a class="sidebar-text sidebar-button" href="{{ route('account.dashboard') }}"><i class="fas fa-tachometer-alt  fa-fw" style="margin-right: .75rem"></i> Accounts Insights</a></li>
                        <li><a class="sidebar-text sidebar-button" href="{{ route('home') }}"><i class="fas fa-chart-line fa-fw" style="margin-right: .75rem"></i> Inventory Insights</a></li>
                        {{-- <li><a class="sidebar-text sidebar-button" href="#"><i class="fas fa-chart-line fa-fw" style="margin-right: .75rem"></i> Sales Insights</a></li>
                        <li><a class="sidebar-text sidebar-button" href="#"><i class="fas fa-chart-line fa-fw" style="margin-right: .75rem"></i> Payments Insights</a></li>
                        <li><a class="sidebar-text sidebar-button" href="#"><i class="fas fa-chart-line fa-fw" style="margin-right: .75rem"></i> Assets Insights</a></li>
                        <li><a class="sidebar-text sidebar-button" href="#"><i class="fas fa-chart-line fa-fw" style="margin-right: .75rem"></i> Employee Insights</a></li> --}}
                    </ul>
                </li>
                <!-- Grouping for Accounts -->
                <li>
                    <span class="sidebar-textx sidebar-button coyfont mr-4 mt-0">ACCOUNTS</span>
                    <ul class="list-unstyled">
                        <!-- Sub-items for Accounting modules and sub-modules -->
                        <li>
                            <a href="#accountTransactionsSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-book fa-fw" style="margin-right: .75rem"></i> Ledgers</a>
                            <ul class="collapse list-unstyled" id="accountTransactionsSubmenu">
                                {{-- <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('new-stock') }}"><i class="fas fa-dot-circle  fa-fw"></i> Add New Ledger</a> </li> --}}
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('ledger.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> List All Ledgers</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('ledger.general_ledger') }}"><i class="fas fa-dot-circle  fa-fw"></i> General Ledger</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('ledger.accounts_receivable_ledger') }}"><i class="fas fa-dot-circle fa-fw"></i> Accounts Receivable Ledger</a></li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('ledger.accounts_payable_ledger') }}"><i class="fas fa-dot-circle fa-fw"></i> Accounts Payable Ledger</a></li>
                                
                            </ul>
                        </li>
                        <li>
                            <a href="#transSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fas fa-exchange-alt fa-fw" style="margin-right: .75rem"></i> Transactions</a>
                            <ul class="collapse list-unstyled" id="transSubmenu">
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('transactions.create') }}"><i class="fas fa-dot-circle  fa-fw"></i> Add New Transaction</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('transactions.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> List Transactions</a> </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#taxSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-money-bill-wave fa-fw" style="margin-right: .75rem"></i> Taxes</a>
                            <ul class="collapse list-unstyled" id="taxSubmenu">
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('tax-rates.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Tax Rates</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('tax-transactions.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Tax Transactions</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('tax-payments.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Tax Payments</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('tax-settings.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Tax Settings</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('tax-exemptions.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Tax Exemptions</a> </li>
                                {{-- <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('tax-forms.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Tax Forms</a> </li> --}}
                                {{-- <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('tax-authorities.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Tax Authorities</a> </li> --}}
                                {{-- <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('tax-codes.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Tax Codes</a> </li> --}}
                            </ul>
                        </li>
                        
                        <li>
                            <a href="#chartSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-chart-bar" style="margin-right: .75rem"></i> Chart of Accounts</a>
                            <ul class="collapse list-unstyled" id="chartSubmenu">
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('chartOfAccounts') }}"><i class="fas fa-dot-circle  fa-fw"></i> Charts of Account</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('chartOfAccounts.create') }}"><i class="fas fa-dot-circle  fa-fw"></i> Create A New Chart of Account</a> </li>
                                
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('transaction-account-mapping.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Transaction-Account Mapping</a> </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#bankingSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-university fa-fw" style="margin-right: .75rem"></i> Banking</a>
                            <ul class="collapse list-unstyled" id="bankingSubmenu">
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('bank-feeds.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Bank Feeds</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('reconciliation.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Reconciliation</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('transfers.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Transfers</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('deposits.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Deposits</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('withdrawals.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Withdrawals</a> </li>
                            </ul>
                        </li>

                    </ul>
                </li>

                <!-- Grouping for Inventory -->
                <li>
                    <span class="sidebar-textx sidebar-button  coyfont mr-4 mt-2">INVENTORIES</span>
                    <ul class="list-unstyled">
                        <li>
                            <a href="#inventorySubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-boxes  fa-fw" style="margin-right: .75rem"></i> Inventory</a>
                            <ul class="collapse list-unstyled" id="inventorySubmenu">
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('new-stock') }}"><i class="fas fa-dot-circle  fa-fw"></i> Add New Stock</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('inventory') }}"><i class="fas fa-dot-circle  fa-fw"></i> Inventory List</a> </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#purchaseSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-cash-register" style="margin-right: .75rem"></i> Purchases</a>
                            <ul class="collapse list-unstyled" id="purchaseSubmenu">
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('select-supplier') }}"><i class="fas fa-dot-circle"></i> New Incoming Stock</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('purchase.index') }}"><i class="fas fa-dot-circle"></i> Purchases List</a> </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#supplierSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-dolly-flatbed  fa-fw" style="margin-right: .75rem"></i> Suppliers</a>
                            <ul class="collapse list-unstyled" id="supplierSubmenu">
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('supplier.create') }}"><i class="fas fa-dot-circle  fa-fw"></i> Add New Supplier</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('supplier.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Suppliers List</a> </li>
                            </ul>
                        </li>

                    </ul>
                </li>

                <!-- Grouping for Customers -->
                <li>
                    <span class="sidebar-textx sidebar-button coyfont mr-4 mt-0">CUSTOMERS</span>
                    <ul class="list-unstyled">
                        <li>
                            <a href="#customersSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-users fa-fw" style="margin-right: .75rem"></i> Customers</a>
                            <ul class="collapse list-unstyled" id="customersSubmenu">
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('customers.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> List Customers</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('payments.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Payments</a> </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#saleSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-dollar-sign  fa-fw" style="margin-right: .75rem"></i> Sales/Returns</a>
                            <ul class="collapse list-unstyled" id="saleSubmenu">
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('sales.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Sales Order</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('returns.show') }}"><i class="fas fa-dot-circle  fa-fw"></i> Sales Returns</a> </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <!-- Grouping for HR -->
                <li>
                    <span class="sidebar-textx sidebar-button coyfont mr-4 mt-2">HR</span>
                    <ul class="list-unstyled">
                        <li>
                            <a href="#hrSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-users-cog fa-fw" style="margin-right: .75rem"></i> Employees</a>
                            <ul class="collapse list-unstyled" id="hrSubmenu">
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('new-stock') }}"><i class="fas fa-dot-circle  fa-fw"></i> Add New Employee</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('inventory') }}"><i class="fas fa-dot-circle  fa-fw"></i> Employee List</a> </li>
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('inventory') }}"><i class="fas fa-dot-circle  fa-fw"></i> Payroll</a> </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <!-- Grouping for Reports -->
                <li>
                    <span class="sidebar-textx sidebar-button coyfont mr-4 mt-2">REPORTS</span>
                    <ul class="list-unstyled">
                        <li>
                            <a href="#reportsSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-file-alt fa-fw" style="margin-right: .75rem"></i> Reports</a>
                            <ul class="collapse list-unstyled" id="reportsSubmenu">
                                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('new-stock') }}"><i class="fas fa-dot-circle  fa-fw"></i> Reports</a> </li>
                                
                            </ul>
                        </li>
                    </ul>
                </li>

                <li>
                    <a class="sidebar-text sidebar-button" href="{{ route('about') }}"><i class="fas fa-info-circle" style="margin-right: .75rem"></i> About</a>
                </li>

                <li class="bottomleftx">
                    <a href="#UserSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-user-circle" style="margin-right: .75rem"></i> @if(request()->user())
                        <span> {{ request()->user()->name }}</span>
                    @endif
                    </a>
                    <ul class="collapse list-unstyled" id="UserSubmenu">
                    
                            <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{% url 'admin:index' %}"><i class="fas fa-dot-circle"></i> Admin Page</a> </li>
                            <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{% url 'admin:index' %}"><i class="fas fa-dot-circle"></i> User Management</a> </li>
                            <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{% url 'admin:index' %}"><i class="fas fa-dot-circle"></i> Backup & resore</a> </li>
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
</script>

    
</body>
</html>
