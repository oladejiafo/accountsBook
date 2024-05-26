@extends('layouts.app')
@section('title', 'Edit Tax Exemption')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="color: #4e4e4e; font-style: bold; ">Create Tax Exemption</div>
                    <p>This section displays a list of tax exemptions available for your company.</p>
                    <div class="card-body">
                        <form method="POST" action="{{ route('tax-exemptions.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                            </div>

                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="valid_from">Valid From:</label>
                                <input type="date" class="form-control" id="valid_from" name="valid_from" value="{{ old('valid_from') }}">
                            </div>

                            <div class="form-group">
                                <label for="valid_to">Valid To:</label>
                                <input type="date" class="form-control" id="valid_to" name="valid_to" value="{{ old('valid_to') }}">
                            </div>

                            <div class="align-middle">
                                <button type="submit" class="btn btn-success">Create Tax Exemption</button>
                                <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
                                <a href="{{ route('tax-exemptions.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
