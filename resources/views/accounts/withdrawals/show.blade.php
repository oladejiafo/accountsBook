@extends('layouts.app')

@section('title', 'Withdrawal Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-weight: bold;">Withdrawal Details</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">Transaction Type:</label>
                        <p>{{ $withdrawal->type }}</p>
                    </div>
                    <div class="form-group">
                        <label for="account_id">Account Classification:</label>
                        <p>{{ $withdrawal->account->code }} - {{ $withdrawal->account->description }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date">Transaction Date:</label>
                        <p>{{ $withdrawal->date->toDateString() }}</p>
                    </div>
                    <div class="form-group">
                        <label for="amount">Transaction Amount:</label>
                        <p>${{ $withdrawal->amount }}</p>
                    </div>
                    <div class="form-group">
                        <label for="description">Details:</label>
                        <p>{{ $withdrawal->description }}</p>
                    </div>
                </div>
            </div>

            <div class="align-middle">
                <a href="{{ route('withdrawals.edit', $withdrawal->id) }}" class="btn btn-info">Edit Withdrawal</a>
                <form action="{{ route('withdrawals.destroy', $withdrawal->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Withdrawal</button>
                </form>
                <a href="{{ route('withdrawals.index') }}" class="btn btn-secondary">Back to Withdrawals</a>
            </div>
        </div>
    </div>
</div>
@endsection
