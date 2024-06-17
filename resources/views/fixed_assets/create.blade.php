@extends('layouts.app')

@section('title', 'Create Fixed Asset')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header"  style="color: #4e4e4e; font-style: bold; ">
            Create Fixed Asset
        </div>
        <div class="card-body">
            <form action="{{ route('fixed_assets.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="acquisition_date">Acquisition Date</label>
                        <input type="date" name="acquisition_date" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="cost">Cost</label>
                        <input type="number" step="0.01" name="cost" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="depreciation_method">Depreciation Method</label>
                        <input type="text" name="depreciation_method" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="useful_life">Useful Life (years)</label>
                        <input type="number" name="useful_life" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="salvage_value">Salvage Value</label>
                        <input type="number" step="0.01" name="salvage_value" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="current_value">Current Value</label>
                        <input type="number" step="0.01" name="current_value" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="location">Location</label>
                        <input type="text" name="location" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="disposed">Disposed</option>
                        <option value="sold">Sold</option>
                        <option value="transferred">Transferred</option>
                    </select>
                </div>
                <div class="align-middle">
                    <button type="submit" class="btn btn-lg btn-success">Create Fixed Asset</button>
                    <button type="reset" class="btn btn-lg btn-danger">Reset</button>
                    <a href="{{ route('fixed_assets.index') }}" class="btn btn-lg btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection