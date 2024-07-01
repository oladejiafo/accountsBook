@extends('layouts.app')

@section('title', 'Transfer Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-weight: bold;">Transfer Details</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">Transaction Type:</label>
                        <p>{{ $transfer->type }}</p>
                    </div>
                    <div class="form-group">
                        <label for="account_id">From Account:</label>
                        <p>{{ $transfer->account->code }} - {{ $transfer->account->description }}</p>
                    </div>
                    <div class="form-group">
                        <label for="to_account_id">To Account:</label>
                        <p>{{ $transfer->to_account->code }} - {{ $transfer->to_account->description }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date">Transaction Date:</label>
                        <p>{{ $transfer->date->toDateString() }}</p>
                    </div>
                    <div class="form-group">
                        <label for="amount">Transaction Amount:</label>
                        <p>${{ $transfer->amount }}</p>
                    </div>
                    <div class="form-group">
                        <label for="description">Details:</label>
                        <p>{{ $transfer->description }}</p>
                    </div>
                </div>
            </div>

            <div class="align-middle">
                <a href="{{ route('transfers.edit', $transfer->id) }}" class="btn btn-info">Edit Transfer</a>
                <form action="{{ route('transfers.destroy', $transfer->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Transfer</button>
                </form>
                <a href="{{ route('transfers.index') }}" class="btn btn-secondary">Back to Transfers</a>
            </div>
        </div>
    </div>
</div>
@endsection
