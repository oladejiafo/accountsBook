@extends('layouts.app')

@section('title', 'Edit Fixed Asset')

@section('content')
<div class="container">
 @if (auth()->user()->hasPermission('fixed_assets.edit') || auth()->user()->hasRole('Super_Admin'))

    <div class="card">
        <div class="card-header" style="color: #4e4e4e; font-style: bold;">
            Edit Fixed Asset
        </div>
        <div class="card-body">
            <form action="{{ route('fixed_assets.update', $fixedAsset->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $fixedAsset->name }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="acquisition_date">Acquisition Date</label>
                        <input type="date" name="acquisition_date" class="form-control" value="{{ $fixedAsset->acquisition_date }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="cost">Cost</label>
                        <input type="number" step="0.01" name="cost" class="form-control" value="{{ $fixedAsset->purchase_price }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="depreciation_method">Depreciation Method</label>
                        <input type="text" name="depreciation_method" class="form-control" value="{{ $fixedAsset->depreciation_method }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="useful_life">Useful Life (years)</label>
                        <input type="number" name="useful_life" class="form-control" value="{{ $fixedAsset->useful_life }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="salvage_value">Salvage Value</label>
                        <input type="number" step="0.01" name="salvage_value" class="form-control" value="{{ $fixedAsset->salvage_value }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="current_value">Current Value</label>
                        <input type="number" step="0.01" name="current_value" class="form-control" value="{{ $fixedAsset->current_value }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="location">Location</label>
                        <input type="text" name="location" class="form-control" value="{{ $fixedAsset->location }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control">{{ $fixedAsset->description }}</textarea>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="active" {{ $fixedAsset->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="disposed" {{ $fixedAsset->status == 'disposed' ? 'selected' : '' }}>Disposed</option>
                        <option value="sold" {{ $fixedAsset->status == 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="transferred" {{ $fixedAsset->status == 'transferred' ? 'selected' : '' }}>Transferred</option>
                    </select>
                </div>
                <div class="align-middle">
                    <button type="submit" class="btn btn-lg btn-primary">Update</button>
                    <a href="{{ route('fixed_assets.index') }}" class="btn btn-lg btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    @else
        <p>You do not have permission to edit fixed assets.</p>
    @endif
</div>
@endsection
