@extends('layouts.app')
@section('title', 'View Fixed Asset')
@section('content')
<div class="container">
@if (auth()->user()->hasPermission('fixed_assets.index') || auth()->user()->hasRole('Super_Admin'))
    <h1>Fixed Asset Details</h1>
    <div class="card">
        <div class="card-header">
            {{ $fixedAsset->name }}
        </div>
        <div class="card-body">
            <p><strong>Description:</strong> {{ $fixedAsset->description }}</p>
            <p><strong>Acquisition Date:</strong> {{ $fixedAsset->acquisition_date }}</p>
            <p><strong>Cost:</strong> {{ $fixedAsset->purchase_price }}</p>
            <p><strong>Depreciation Method:</strong> {{ $fixedAsset->depreciation_method }}</p>
            <p><strong>Useful Life:</strong> {{ $fixedAsset->useful_life }} years</p>
            <p><strong>Salvage Value:</strong> {{ $fixedAsset->salvage_value }}</p>
            <p><strong>Current Value:</strong> {{ $fixedAsset->current_value }}</p>
            <p><strong>Location:</strong> {{ $fixedAsset->location }}</p>
            <p><strong>Status:</strong> {{ ucfirst($fixedAsset->status) }}</p>
        </div>
    </div>
    <a href="{{ route('fixed_assets.index') }}" class="btn btn-secondary mt-3">Back</a>
    @else
        <p>You do not have permission to fixed assets.</p>
    @endif

</div>
@endsection
