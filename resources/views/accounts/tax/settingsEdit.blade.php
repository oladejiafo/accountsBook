@extends('layouts.app')
@section('title', 'Edit Tax Setting')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Edit Tax Setting</div>
                    <p class="text-muted">Configure various tax-related settings for your business.</p>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('tax-settings.update', $taxSetting->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="name">Setting Name:</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $taxSetting->name) }}">
                            </div>

                            <div class="form-group">
                                <label for="value">Value:</label>
                                <input type="text" class="form-control" id="value" name="value" value="{{ old('value', $taxSetting->value) }}">
                            </div>

                            <div class="form-group align-middle">
                                <button type="submit" class="btn btn-info">Update Tax Setting</button>
                                <a href="{{ route('tax-settings.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
