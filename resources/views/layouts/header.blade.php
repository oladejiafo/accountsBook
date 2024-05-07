<!--     <div class="user-info">
        @if(auth()->check())
            <span>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link">Logout</button>
            </form>
        @endif
    </div> -->

    <!-- Button to toggle sidebar on small screens -->
    <button class="btn btn-custom d-lg-none" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Dialog Box CSS  -->
    <!-- Your dialog box HTML here -->

    <main id="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header row">
<div class="sidebar-header-left d-flex flex-column align-items-center" col-3>
    <a class="navbar-brand titlefont mb-2" href="{{ route('account.dashboard') }}">
        <div class="logo-wrapper">
            <img src="{{ asset('images/akontledger_logo.png') }}" class="logo" alt="Logo">
        </div>
    </a>
    @if(isset($companyName))
        <div class="navbar-brandc coyfont text-center" style="color: #333">
            {{ $companyName }}
        </div>
    @endif
</div>

<!-- Centered Search bar -->
<div class="text-center col-6">
    <form class="form-inline" action="{{ route('global.search') }}" method="GET">
        <div class="input-group">
            <input type="text" class="form-control" name="query" placeholder="Search" aria-label="Search" aria-describedby="basic-addon1">
            <div class="input-group-prepend">
                <button type="submit" class="input-group-text" id="basic-addon1">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>


<!-- User info and logout button -->
<div class="user-info-container col-3">
    <div class="user-infox">
        @if(auth()->check())
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle dropdown-user" id="dropdown-toggler" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle" style="margin-right: .05rem; color: #333"></i> 
                    <span style="color: #333">{{ auth()->user()->name }}</span>
                </a>
                <div class="dropdown-menu list-unstyled" style="background-color: #ffccc;">
                    <form method="POST" action="{{ route('logout') }}"  style="background-color: #ffccc">
                        @csrf
                        <button type="submit" class="sidebar-text sidebar-subitem sidebar-button" style="background: inherit; border: none; padding: 0; font: inherit; cursor: pointer; color: inherit; text-decoration: underline; outline: inherit;">
                            <i class="fas fa-dot-circle"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        @endif
    </div>
</div>


            </div>


<br>

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
                @if(auth()->user()->hasPermission('dashboard_view'))
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
                @endif
                <li>
                    <a class="sidebar-text sidebar-button" href="{{ route('about') }}"><i class="fas fa-info-circle" style="margin-right: .75rem"></i> About</a>
                </li>

                <li class="bottomleftx">
                    <span class="sidebar-textx sidebar-button coyfont mr-4 mt-2">SETTINGS</span>
                    
                    <li> <a class="sidebar-text sidebar-subitemX sidebar-button" href="{% url 'admin:index' %}"><i class="fas fa-cogs"></i> Admin Page</a> </li>
                    <li> <a class="sidebar-text sidebar-subitemX sidebar-button" href="{{ route('users.index') }}"><i class="fas fa-users"></i> Users Management</a> </li>
                    <li> <a class="sidebar-text sidebar-subitemX sidebar-button" href="{{ route('roles.index') }}"><i class="fas fa-user-lock"></i> Roles Management</a> </li>
                    <li> <a class="sidebar-text sidebar-subitemX sidebar-button" href="{{ route('role-permissions.index') }}"><i class="fas fa-user-lock"></i> Roles Permissions</a> </li>
                    <li> <a class="sidebar-text sidebar-subitemX sidebar-button" href="{% url 'admin:index' %}"><i class="fas fa-database"></i> Backup & restore</a> </li>
                    <a href="#UserSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-user-circle" style="margin-right: .75rem"></i> @if(request()->user())
                        <span> {{ request()->user()->name }}</span>
                    @endif
                    </a>
                    <ul class="collapse list-unstyled" id="UserSubmenu">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="sidebar-text sidebar-subitem sidebar-button" style="background: none; border: none; padding: 0; font: inherit; cursor: pointer; color: inherit; text-decoration: underline; outline: inherit;">
                                        <i class="fas fa-dot-circle"></i> Logout
                                    </button>
                                </form>
                            </li>
                    </ul>

                </li>
        
            </ul>

        </nav>
        