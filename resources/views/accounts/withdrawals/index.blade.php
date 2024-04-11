@extends('layouts.app')

@section('title', 'Withdrawal Transactions')

@section('content')
<div class="container">
    
    <div class="row">
        <div class="col-md-6"  style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Withdrawals</div>
        <div class="col-md-6">
            <div style="float:right;" class="d-flex justify-content-end mt-3">
                <div>
                    <a href="{{ route('withdrawals.create') }}" class="btn btn-success mb-3">Create Withdrawal</a>
                </div>
            </div>
        </div>
    </div>

    <div style="border-bottom: 1px solid white;"></div>
    <form method="GET" action="{{ route('withdrawals.index') }}">
        <div class="input-group search">
            <input type="text" name="search" class="form-control textinput" placeholder="Search for withdrawals">
            <div class="input-group-append">
               <button type="submit" class="btn btn-pink">Search</button>
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
            @foreach($withdrawals as $withdrawal)
            <tr> 
                <td>{{ $withdrawal->date }}</td>
                <td>{{ $withdrawal->type }}</td>
                <td>{{ optional($withdrawal->account)->category ?? '' }}</td>
                <td>{{ $withdrawal->description }}</td>
                <td>{{ number_format($withdrawal->amount,2) }}</td>
                <td>
                    <a href="{{ route('withdrawals.edit', $withdrawal->id) }}" class="btn btn-sm btn-info">Edit Withdrawal</a>
                    <form action="{{ route('withdrawals.destroy', $withdrawal->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete Withdrawal</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if ($withdrawals->isNotEmpty())
        <div class="pagination">
            {{ $withdrawals->links() }}
    </div>
    @endif
</div>

@endsection
