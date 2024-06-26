
@extends('layouts.app')

@section('title', 'Tax Rates')
<style>
    .nav-tabs .nav-link {
        font-weight: bold; /* Make the text bolder */
    }

    .nav-tabs .nav-item .nav-link.active {
        background-color: #cce1d8; /* Add a subtle shade when tab is active */
        border-color: #dee2e6 #dee2e6 #fff; /* Adjust border color */
    }
</style>
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="color: #4e4e4e; font-style: bold;">Tax Rates</div>
                    <p class="text-muted"> &nbsp; Configure various tax rates for your business.</p>
                    <div class="card-body">
                        <!-- Nav tabs -->
                        
                        <ul class="nav nav-tabs" id="taxTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="businessTaxes-tab" data-toggle="tab" href="#businessTaxes" role="tab" aria-controls="businessTaxes" aria-selected="true">Business Taxes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="employeeTaxes-tab" data-toggle="tab" href="#employeeTaxes" role="tab" aria-controls="employeeTaxes" aria-selected="false">Employee Taxes</a>
                            </li>
                        </ul>
                        
                        <!-- Tab panes -->
                        <div class="tab-content mt-3">
                            <div class="tab-pane fade show active" id="businessTaxes" role="tabpanel" aria-labelledby="businessTaxes-tab">
                                <!-- Content for Business Taxes tab -->
                                <a href="{{ route('tax-rates.create', ['tab' => 'Business']) }}" class="btn btn-success mb-3 ml-auto justify-content-end" style="float:right;">Create Business Tax Rate</a>
                                <div class="table-responsive">
                                <table class="table table-css table-bordered table-hover">
                                    <thead class="thead-dark align-middle">
                                        <tr>
                                            <th>Tax Name</th>
                                            <th>Tax Rate</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if ($taxRates->isEmpty() || $taxRates->every(fn($taxRate) => $taxRate->name === 'Income Tax' || $taxRate->name === 'Employee Tax' || Str::contains($taxRate->name, 'Income')))
                                            <tr>
                                                <td colspan="3">No business tax rates found.</td>
                                            </tr>

                                        @else
                                            @foreach ($taxRates as $taxRate)
                                                @if (isset($taxRates) && $taxRate->name !== 'Income Tax' && $taxRate->name !== 'Employee Tax' && !Str::contains($taxRate->name, 'Income'))
                                                    <tr>
                                                        <td>{{ $taxRate->name }}</td>
                                                        <td>{{ $taxRate->rate }}</td>
                                                        <td>
                                                            <a href="{{ route('tax-rates.edit', ['id' => $taxRate->id, 'tab' => 'Business']) }}" class="btn btn-sm btn-info">Edit</a>
                                                            <form action="{{ route('tax-rates.destroy', $taxRate->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this tax rate?')">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>                                    
                                </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="employeeTaxes" role="tabpanel" aria-labelledby="employeeTaxes-tab">
                                <!-- Content for Business Taxes tab -->
                                <a href="{{ route('tax-rates.create', ['tab' => 'Employee']) }}" class="btn btn-success mb-3 ml-auto justify-content-end" style="float:right;">Create Income Tax Rate</a>

                                <div class="table-responsive">
                                <table class="table ">
                                    <thead>
                                        <tr>
                                            <th>Tax Name</th>
                                            <th>Tax Rate</th>
                                            <th>Employee Position</th>
                                            <th>Min. Earnings</th>
                                            <th>Max. Earnings</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if ($taxRates->isEmpty() || $taxRates->every(fn($taxRate) => $taxRate->name !== 'Income Tax' && $taxRate->name !== 'Employee Tax' && !Str::contains($taxRate->name, 'Income')))
                                            <tr>
                                                <td colspan="6">No employee income tax rates found.</td>
                                            </tr>
                                        @else
                                            @foreach ($taxRates as $taxRate)
                                                @if ($taxRate->name === 'Income Tax' || $taxRate->name === 'Employee Tax' || Str::contains($taxRate->name, 'Income'))

                                                    <tr>
                                                        <td>{{ $taxRate->name }}</td>
                                                        <td>{{ $taxRate->rate }}</td>
                                                        <td>{{ $taxRate->position }}</td>
                                                        <td>{{ $taxRate->min_earnings }}</td>
                                                        <td>{{ $taxRate->max_earnings }}</td>
                                                        <td>
                                                            <a href="{{ route('tax-rates.edit', ['id' => $taxRate->id, 'tab' => 'Employee']) }}" class="btn btn-sm btn-info">Edit</a>
                                                            <form action="{{ route('tax-rates.destroy', $taxRate->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this tax rate?')">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>                                    
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simulate click on appropriate tab link when the page loads
        window.addEventListener('DOMContentLoaded', function() {
            var tabToClick = '{{ $tab }}'; // Assuming $tab is passed from the controller
    
            if (tabToClick === 'Employee') {
                var employeeTab = document.getElementById('employeeTaxes-tab');
                if (employeeTab) {
                    employeeTab.click(); // Simulate click event
                }
            } else {
                var businessTab = document.getElementById('businessTaxes-tab');
                if (businessTab) {
                    businessTab.click(); // Simulate click event
                }
            }
        });
    </script>
    
    
@endsection
