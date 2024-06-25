<!-- resources/views/accounts/tax/ratesCreate.blade.php -->

@extends('layouts.app')

@section('title', 'Create Tax Rate')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="color: #4e4e4e; font-style: bold; ">Create {{$tab}} Tax Rate</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('tax-rates.store') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ isset($tab) && $tab == 'Employee' ? 'Employee Income Tax' : '' }}" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="rate" class="col-md-4 col-form-label text-md-right">Rate</label>

                                <div class="col-md-6">
                                    <input id="rate" type="number" class="form-control" name="rate" required>
                                </div>
                            </div>

                            @if(isset($tab) && $tab == "Employee")
                                <div class="form-group row">
                                    <label for="min_earnings" class="col-md-4 col-form-label text-md-right">Minimum Earnings</label>

                                    <div class="col-md-6">
                                        <input id="min_earnings" type="number" class="form-control" name="min_earnings">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="max_earnings" class="col-md-4 col-form-label text-md-right">Maximum Earnings</label>

                                    <div class="col-md-6">
                                        <input id="max_earnings" type="number" class="form-control" name="max_earnings">
                                    </div>
                                </div>
                            @endif

                            <div class="align-middle">
                                <button type="submit" class="btn btn-success"> Create </button>
                                <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
                                @if(isset($tab) && $tab == "Employee")
                                    <a href="{{ route('tax-rates.index', ['tab' => 'Employee']) }}" class="btn btn-secondary">Cancel</a>
                                @else 
                                    <a href="{{ route('tax-rates.index') }}" class="btn btn-secondary">Cancel</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Function to reset form fields
        function resetForm() {
            document.getElementById("name").value = "";
            document.getElementById("rate").value = "";
            document.getElementById("min_earnings").value = "";
            document.getElementById("max_earnings").value = "";
            // document.getElementById("position").value = "";
            // document.getElementById("effective_date").value = "";
        }
    </script>
@endsection
