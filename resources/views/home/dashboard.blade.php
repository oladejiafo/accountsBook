@extends('layouts.app')

@section('title', 'Dashboard')
{{-- @can('dashboard_view') --}}
@section('content')
<style>
    .alert-custom {
        padding: 15px;
        border-radius: 10px;
    }

    .alert-custom:nth-child(odd) {
        background-color: #ffe6e6; /* Light red background for odd alerts */
    }

    .alert-custom:nth-child(even) {
        background-color: #e6f7ff; /* Light blue background for even alerts */
    }

</style>
<div class="container">
    <h1  style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Dashboard</h1>

    <!-- Overview Section -->
    <div class="card mb-4">
        <div class="card-header">Overview</div>
        <div class="card-body">
            <!-- Add overview content here -->
            <div class="row">
                <!-- Income -->
                <div class="col-md-4">
                    <div class="card bg-success text-white mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Income</h5>
                            <p class="card-text">${{ number_format($income, 2) }}</p>
                        </div>
                    </div>
                </div>
                <!-- Expenses -->
                <div class="col-md-4">
                    <div class="card bg-danger text-white mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Expenses</h5>
                            <p class="card-text">${{ number_format($expenses, 2) }}</p>
                        </div>
                    </div>
                </div>
                <!-- Cash Flow -->
                <div class="col-md-4">
                    <div class="card bg-info text-white mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Cash Flow</h5>
                            <p class="card-text">${{ number_format($cashFlow, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Account Balances -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-primary text-white mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Assets</h5>
                            <p class="card-text">${{ number_format($accountBalances['assets'], 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Liabilities</h5>
                            <p class="card-text">${{ number_format($accountBalances['liabilities'], 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-secondary text-white mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Equity</h5>
                            <p class="card-text">${{ number_format($accountBalances['equity'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="card mb-4">
        <div class="card-header">Financial Charts</div>
        <div class="card-body">
            <!-- Income and Expenses Chart -->
            <canvas id="incomeExpenseChart"></canvas>
        </div>
    </div>


    <!-- Alerts Section -->
    <div class="card mb-4">
        <div class="card-header">Alerts</div>
        <div class="card-body">
            <!-- Add alerts content here -->
            @foreach($alerts as $alert)
                <div class="alert alert-custom" role="alert">
                    {{ $alert }}
                </div>
            @endforeach
        </div>
    </div>


</div>

<!-- Loading Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get the canvas element
        var ctx = document.getElementById('incomeExpenseChart').getContext('2d');
        
        // Create the chart
        var incomeExpenseChart = new Chart(ctx, {
            type: 'bar',
            data: {!! json_encode($incomeExpensesData) !!}, // Pass the data from the controller
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    });
</script>

{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart configuration and data
    var ctx = document.getElementById('incomeExpenseChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{
                label: 'Income',
                data: [20000, 25000, 30000, 28000, 32000, 35000, 38000],
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Expenses',
                data: [15000, 18000, 20000, 22000, 25000, 27000, 28000],
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script> --}}

@endsection
{{-- @else
    <p>Sorry, you don't have permission to view this page.</p>
@endcan --}}