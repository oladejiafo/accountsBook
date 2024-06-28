<!-- <div class="list-groupx xlist-group-flush list-unstyled sidebar-components" id="scrollable-menu"> -->
<ul class="list-unstyled sidebar-components" id="scrollable-menu">
        <li class="col-12 d-lg-none mt-1">
            @if (auth()->check())
                <a href="#UserSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"  style="margin-left: 0px !important"><i class="fas fa-user-circle" style="margin-right: .75rem"></i>
                    @if (request()->user())
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
            @endif
        </li> 

        {{-- <li>
            <div id="app">
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
                <ul id="searchResults" style="display: none;"></ul>
            </div>

        </li> --}}
        @if (auth()->user()->hasPermission('account.dashboard') || auth()->user()->hasPermission('home') || auth()->user()->hasRole('Super_Admin'))
        <!-- Grouping for Dashboards -->
        <li>
            <a href="{{ route('home') }}" style="text-decoration: none;">
                <span class="sidebar-textx sidebar-button menutitlefont mr-4 mt-2">DASHBOARDS</span>
            </a>
            <ul class="list-unstyled">
                @if (auth()->user()->hasPermission('home') || auth()->user()->hasRole('Super_Admin'))
                <li><a class="sidebar-text sidebar-button" href="{{ route('home') }}"><i class="fas fa-tachometer-alt fa-fw" style="margin-right: .75rem"></i> Dashboard</a>
                </li>
                @endif

                @if (auth()->user()->hasPermission('account.dashboard') || auth()->user()->hasRole('Super_Admin'))
                <li><a class="sidebar-text sidebar-button" href="{{ route('account.dashboard') }}"><i class="fas fa-chart-bar  fa-fw" style="margin-right: .75rem"></i> Accounts
                        Insights</a>
                </li>
                @endif
                @if (auth()->user()->hasPermission('insight.dashboard') || auth()->user()->hasRole('Super_Admin'))
                <li><a class="sidebar-text sidebar-button" href="{{ route('insight.dashboard') }}"><i class="fas fa-chart-line fa-fw" style="margin-right: .75rem"></i> Inventory Insights</a>
                </li>
                @endif
                {{-- <li><a class="sidebar-text sidebar-button" href="#"><i class="fas fa-chart-line fa-fw" style="margin-right: .75rem"></i> Sales Insights</a></li>
                        <li><a class="sidebar-text sidebar-button" href="#"><i class="fas fa-chart-line fa-fw" style="margin-right: .75rem"></i> Payments Insights</a></li>
                        <li><a class="sidebar-text sidebar-button" href="#"><i class="fas fa-chart-line fa-fw" style="margin-right: .75rem"></i> Assets Insights</a></li>
                        <li><a class="sidebar-text sidebar-button" href="#"><i class="fas fa-chart-line fa-fw" style="margin-right: .75rem"></i> Employee Insights</a></li> --}}
            </ul>
        </li>
        @endif
        @if (auth()->user()->hasPermission('reconciliation.index') || auth()->user()->hasPermission('chartOfAccounts') || auth()->user()->hasPermission('tax.index') || auth()->user()->hasPermission('transactions.index') || auth()->user()->hasPermission('ledger.index') || auth()->user()->hasRole('Super_Admin'))
        <li>
            <span class="sidebar-textx sidebar-button menutitlefont mr-4 mt-0">ACCOUNTS</span>
            <ul class="list-unstyled">
                @if (auth()->user()->hasPermission('ledger.index') || auth()->user()->hasRole('Super_Admin'))
                <li>
                    <a href="#accountTransactionsSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-book fa-fw" style="margin-right: .75rem"></i> Ledgers</a>
                    <ul class="collapse list-unstyled" id="accountTransactionsSubmenu">
                        {{-- <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('new-stock') }}"><i class="fas fa-dot-circle  fa-fw"></i> Add New Ledger</a>
                </li> --}}
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('ledger.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> List
                        All
                        Ledgers</a> </li>
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('ledger.general_ledger') }}"><i class="fas fa-dot-circle  fa-fw"></i> General Ledger</a> </li>
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('ledger.accounts_receivable_ledger') }}"><i class="fas fa-dot-circle fa-fw"></i> Accounts Receivable Ledger</a></li>
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('ledger.accounts_payable_ledger') }}"><i class="fas fa-dot-circle fa-fw"></i> Accounts Payable Ledger</a>
                </li>
            </ul>
        </li>
        @endif
        @if (auth()->user()->hasPermission('transactions.index') || auth()->user()->hasRole('Super_Admin'))
        <li>
            <a href="#transSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fas fa-exchange-alt fa-fw" style="margin-right: .75rem"></i>
                Transactions</a>
            <ul class="collapse list-unstyled" id="transSubmenu">
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('transactions.create') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                        Add New Transaction</a> </li>
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('transactions.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                        List Transactions</a> </li>
            </ul>
        </li>
        @endif

        @if (auth()->user()->hasPermission('tax.index') || auth()->user()->hasRole('Super_Admin'))
        <li>
            <a href="#taxSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-money-bill-wave fa-fw" style="margin-right: .75rem"></i>
                Taxes</a>
            <ul class="collapse list-unstyled" id="taxSubmenu">
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('tax-rates.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Tax
                        Rates</a> </li>
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('tax-transactions.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Tax Transactions</a> </li>
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('tax-payments.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                        Tax Payments</a> </li>
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('tax-settings.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                        Tax Settings</a> </li>
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('tax-exemptions.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Tax Exemptions</a> </li>
            </ul>
        </li>
        @endif
        @if (auth()->user()->hasPermission('chartOfAccounts') || auth()->user()->hasRole('Super_Admin'))
        <li>
            <a href="#chartSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-chart-bar" style="margin-right: .75rem"></i> Chart of Accounts</a>
            <ul class="collapse list-unstyled" id="chartSubmenu">
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('chartOfAccounts') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                        Charts of Account</a> </li>
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('chartOfAccounts.create') }}"><i class="fas fa-dot-circle  fa-fw"></i> Create A New Chart of Account</a>
                </li>

                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('transaction-account-mapping.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Transaction-Account Mapping</a> </li>
            </ul>
        </li>
        @endif
        @if (auth()->user()->hasPermission('reconciliation.index') || auth()->user()->hasRole('Super_Admin'))
        <li>
            <a href="#bankingSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-university fa-fw" style="margin-right: .75rem"></i> Banking</a>
            <ul class="collapse list-unstyled" id="bankingSubmenu">
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('bank-feeds.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                        Bank Feeds</a> </li>
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('reconciliation.index') }}"><i class="fas fa-dot-circle  fa-fw"></i> Reconciliation</a> </li>
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('transfers.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                        Transfers</a> </li>
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('deposits.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                        Deposits</a> </li>
                <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('withdrawals.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                        Withdrawals</a> </li>
            </ul>
        </li>
        @endif

        @endif
        @if (auth()->user()->hasPermission('supplier.index') || auth()->user()->hasPermission('purchase.index') || auth()->user()->hasPermission('inventory') || auth()->user()->hasRole('Super_Admin'))
        <!-- Grouping for Inventory -->
        <li>
            <span class="sidebar-textx sidebar-button  menutitlefont mr-4 mt-2">INVENTORIES</span>
            <ul class="list-unstyled">
                @if (auth()->user()->hasPermission('inventory') || auth()->user()->hasRole('Super_Admin'))
                <li>
                    <a href="#inventorySubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-boxes  fa-fw" style="margin-right: .75rem"></i> Inventory</a>
                    <ul class="collapse list-unstyled" id="inventorySubmenu">
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('new-stock') }}"><i class="fas fa-dot-circle  fa-fw"></i> Add
                                New
                                Stock</a> </li>
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('inventory') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                                Inventory List</a> </li>
                    </ul>
                </li>
                @endif
                @if (auth()->user()->hasPermission('purchase.index') || auth()->user()->hasRole('Super_Admin'))
                <li>
                    <a href="#purchaseSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-cash-register" style="margin-right: .75rem"></i> Purchases</a>
                    <ul class="collapse list-unstyled" id="purchaseSubmenu">
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('select-supplier') }}"><i class="fas fa-dot-circle"></i> New
                                Incoming Stock</a> </li>
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('purchase.index') }}"><i class="fas fa-dot-circle"></i>
                                Purchases
                                List</a> </li>
                    </ul>
                </li>
                @endif
                @if (auth()->user()->hasPermission('supplier.index') || auth()->user()->hasRole('Super_Admin'))
                <li>
                    <a href="#supplierSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-dolly-flatbed  fa-fw" style="margin-right: .75rem"></i>
                        Suppliers</a>
                    <ul class="collapse list-unstyled" id="supplierSubmenu">
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('supplier.create') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                                Add New Supplier</a> </li>
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('supplier.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                                Suppliers List</a> </li>
                    </ul>
                </li>
                @endif
            </ul>
        </li>
        @endif
        @if (auth()->user()->hasPermission('customers.index') || auth()->user()->hasPermission('sales.index') || auth()->user()->hasRole('Super_Admin'))
        <!-- Grouping for Customers -->
        <li>
            <span class="sidebar-textx sidebar-button menutitlefont mr-4 mt-0">CUSTOMERS/SALES</span>
            <ul class="list-unstyled">
                @if (auth()->user()->hasPermission('customers.index') || auth()->user()->hasRole('Super_Admin'))
                <li>
                    <a href="#customersSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-users fa-fw" style="margin-right: .75rem"></i> Customers</a>
                    <ul class="collapse list-unstyled" id="customersSubmenu">
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('customers.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                                List Customers</a> </li>
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('payments.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                                Payments</a> </li>
                    </ul>
                </li>
                @endif
                @if (auth()->user()->hasPermission('sales.index') || auth()->user()->hasRole('Super_Admin'))
                <li>
                    <a href="#saleSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-dollar-sign  fa-fw" style="margin-right: .75rem"></i>
                        Sales/Returns</a>
                    <ul class="collapse list-unstyled" id="saleSubmenu">
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('sales.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                                Sales
                                Order</a> </li>
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('returns.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                                Sales
                                Returns</a> </li>
                    </ul>
                </li>
                @endif
            </ul>
        </li>
        @endif
        <!-- Grouping for Assets -->

        @if (auth()->user()->hasPermission('fixed_assets.index') || auth()->user()->hasRole('Super_Admin'))
        <li>
            <span class="sidebar-textx sidebar-button menutitlefont mr-4 mt-2">Assets</span>
            <ul class="list-unstyled">
                <li>
                    <a href="#hrSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-users-cog fa-fw" style="margin-right: .75rem"></i> Assets</a>
                    <ul class="collapse list-unstyled" id="hrSubmenu">
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('fixed_assets.create') }}"><i class="fas fa-dot-circle  fa-fw"></i> Add
                                New
                                Asset</a> </li>
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('fixed_assets.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                                Assets
                                List</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        @endif
        <!-- Grouping for HR -->
        @if (auth()->user()->hasPermission('employees.index') || auth()->user()->hasRole('Super_Admin'))
        <li>
            <span class="sidebar-textx sidebar-button menutitlefont mr-4 mt-2">Human Resources</span>
            <ul class="list-unstyled">
                <li>
                    <a href="#hrSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-users-cog fa-fw" style="margin-right: .75rem"></i> Employees</a>
                    <ul class="collapse list-unstyled" id="hrSubmenu">
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('employees.create') }}"><i class="fas fa-dot-circle  fa-fw"></i> Add
                                New
                                Employee</a> </li>
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('employees.index') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                                Employee
                                List</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="sidebar-text sidebar-button" href="{{ route('inventory') }}">
                        <i class="fa fa-credit-card fa-fw" style="margin-right: .75rem"></i> Payroll
                    </a>
                </li>
            </ul>
        </li>
        @endif
        <!-- Grouping for Reports -->
        {{-- @if (auth()->user()->hasPermission('dashboard_view')) --}}
        <li>
            <span class="sidebar-textx sidebar-button menutitlefont mr-4 mt-2">REPORTS</span>
            <ul class="list-unstyled">
                <li>
                    <a href="#reportsSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-file-alt fa-fw" style="margin-right: .75rem"></i> Reports</a>
                    <ul class="collapse list-unstyled" id="reportsSubmenu">
                        <li> <a class="sidebar-text sidebar-subitem sidebar-button" href="{{ route('new-stock') }}"><i class="fas fa-dot-circle  fa-fw"></i>
                                Reports</a> </li>

                    </ul>
                </li>
            </ul>
        </li>
        {{-- @endif --}}
        <li>
            <a class="sidebar-text sidebar-button" href="{{ route('about') }}"><i class="fas fa-info-circle" style="margin-right: .75rem"></i> About</a>
        </li>

        @if (Auth::user()->hasRole('Super_Admin') || Auth::user()->hasRole('Admin'))
        <li class="bottomleftx">
            <span class="sidebar-textx sidebar-button menutitlefont mr-4 mt-2">SETTINGS</span>
          <ul class="list-unstyled">
           <li> <a class="sidebar-text sidebar-subitemX sidebar-button" href="{% url 'admin:index' %}"><i class="fas fa-cogs" style="margin-right: .75rem"></i> Admin Page</a> </li>
           <li> <a class="sidebar-text sidebar-subitemX sidebar-button" href="{{ route('users.index') }}"><i class="fas fa-users" style="margin-right: .75rem"></i> Users Management</a> </li>
           <li> <a class="sidebar-text sidebar-subitemX sidebar-button" href="{{ route('roles.index') }}"><i class="fas fa-user-lock" style="margin-right: .75rem"></i> Roles Management</a> </li>
           <li> <a class="sidebar-text sidebar-subitemX sidebar-button" href="{{ route('role-permissions.index') }}"><i class="fas fa-user-lock" style="margin-right: .75rem"></i> Roles
                Permissions</a> </li>
           <li> <a class="sidebar-text sidebar-subitemX sidebar-button" href="{% url 'admin:index' %}"><i class="fas fa-database" style="margin-right: .75rem"></i> Backup & restore</a> </li>
          </ul>
        </li>
        @endif
        <a href="#UserSubmenu" data-toggle="collapse" class="dropdown-toggle sidebar-text right-arrow sidebar-button"><i class="fas fa-user-circle" style="margin-right: .75rem"></i>
            @if (request()->user())
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

    </ul>