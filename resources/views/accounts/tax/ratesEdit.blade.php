@extends('layouts.app')

@section('title', 'Edit Tax Rate')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="color: #4e4e4e; font-style: bold;">Edit Tax Rate</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('tax-rates.update', $taxRate->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Tax Rate Name</label>
                            <div class="col-md-6">
                                <input type="text" name="name" id="name" class="form-control" value="{{ $taxRate->name }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="rate" class="col-md-4 col-form-label text-md-right">Tax Rate</label>
                            <div class="col-md-6">
                                <input type="number" name="rate" id="rate" class="form-control" value="{{ $taxRate->rate }}" required>
                            </div>
                        </div>

                        @if(isset($tab) && $tab == "Employee")
                            <div class="form-group row">
                                <label for="min_earnings" class="col-md-4 col-form-label text-md-right">Minimum Earnings</label>

                                <div class="col-md-6">
                                    <input id="min_earnings" type="number" class="form-control" name="min_earnings" value="{{ $taxRate->min_earnings }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="max_earnings" class="col-md-4 col-form-label text-md-right">Maximum Earnings</label>

                                <div class="col-md-6">
                                    <input id="max_earnings" type="number" class="form-control" name="max_earnings" value="{{ $taxRate->max_earnings }}">
                                </div>
                            </div>
                        @endif

                        <div class="align-middle">
                            <button type="submit" class="btn btn-info">Update Tax Rate</button>
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
        document.getElementById("account_id").value = "";
        document.getElementById("date").value = "";
        document.getElementById("type").value = "";
        document.getElementById("transaction_name").value = "";
        document.getElementById("amount").value = "";
        document.getElementById("description").value = "";
        document.getElementById("source").value = "";
        document.getElementById("status").value = "";
        document.getElementById("to_account_id").value = "";
    }
</script>
@endsection
