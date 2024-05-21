@extends('layouts.app')

@section('title', 'Deposit Transactions')

@section('content')
<div class="container">
    
    <div class="row">
        <div class="col-md-6 titles"  style="color: #4e4e4e; font-style: bold;">Deposits</div>
        <div class="col-md-6">
            <div style="float:right;" class="d-flex justify-content-end mt-3">
                <div>
                    <a href="{{ route('deposits.create') }}" class="btn btn-success mb-3">Create Deposit</a>
                </div>
            </div>
        </div>
    </div>

    <div style="border-bottom: 1px solid white;"></div>
    <form method="GET" action="{{ route('deposits.index') }}">
        <div class="input-group search">
            <input type="text" name="search" class="form-control textinput" placeholder="Search for deposits">
            <div class="input-group-append">
               <button type="submit" class="btn btn-pink" style="border-radius:0 .5rem .5rem 0 !important">Search</button>
            </div>
        </div>
    </form>
    <br>

    <table class="table table-css table-bordered table-hover">
        <thead class="thead-dark align-middle">
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Account Type</th>
                <th>Details</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody  class="align-middle">
            @foreach($deposits as $deposit)
            <tr> 
                <td>{{ $deposit->date }}</td>
                <td>{{ $deposit->type }}</td>
                <td>{{ optional($deposit->account)->category ?? '' }}</td>
                <td>{{ $deposit->description }}</td>
                <td>{{ number_format($deposit->amount,2) }}</td>
                <td>
                    <a href="{{ route('deposits.edit', $deposit->id) }}" class="btn btn-sm btn-info">Edit Deposit</a>
                    <form action="{{ route('deposits.destroy', $deposit->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete Deposit</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if ($deposits->isNotEmpty())
        <div class="pagination">
            {{ $deposits->links() }}
    </div>
    @endif
</div>

@endsection
