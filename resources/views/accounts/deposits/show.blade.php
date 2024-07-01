@extends('layouts.app')

@section('title', 'Deposit Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-weight: bold;">Deposit Details</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">Transaction Type:</label>
                        <p>{{ $deposit->type }}</p>
                    </div>
                    <div class="form-group">
                        <label for="account_id">Account Classification:</label>
                        <p>{{ $deposit->account->code }} - {{ $deposit->account->description }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date">Transaction Date:</label>
                        <p>{{ $deposit->date->toDateString() }}</p>
                    </div>
                    <div class="form-group">
                        <label for="amount">Transaction Amount:</label>
                        <p>${{ $deposit->amount }}</p>
                    </div>
                    <div class="form-group">
                        <label for="description">Details:</label>
                        <p>{{ $deposit->description }}</p>
                    </div>
                </div>
            </div>

            <div class="align-middle">
                <a href="{{ route('deposits.edit', $deposit->id) }}" class="btn btn-info">Edit Deposit</a>
                <form action="{{ route('deposits.destroy', $deposit->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Deposit</button>
                </form>
                <a href="{{ route('deposits.index') }}" class="btn btn-secondary">Back to Deposits</a>
            </div>
        </div>
    </div>
</div>
@endsection
