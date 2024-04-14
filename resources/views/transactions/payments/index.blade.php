@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Payments</div>

        <div class="card-body">
            <a href="{{ route('payments.create') }}" class="btn btn-primary mb-3">Create New Payment</a>
            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">Payment ID</th>
                        <th width="15%">Amount</th>
                        <th width="15%">Payment Date</th>
                        <th width="15%">Payment Method</th>
                        <th width="20%">Description</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                    <tr>
                        <td class="align-middle">{{ $payment->id }}</td>
                        <td class="align-middle">{{ $payment->amount }}</td>
                        <td class="align-middle">{{ $payment->payment_date }}</td>
                        <td class="align-middle">{{ $payment->payment_method }}</td>
                        <td class="align-middle">{{ $payment->description }}</td>
                        <td class="align-middle">
                            <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this payment?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
