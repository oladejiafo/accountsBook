@extends('layouts.app')

@section('title', 'Transfer Transactions')

@section('content')
<div class="container">
    
    <div class="row">
        <div class="col-md-6"  style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Transfers</div>
        <div class="col-md-6">
            <div style="float:right;" class="d-flex justify-content-end mt-3">
                <div>
                    <a href="{{ route('transfers.create') }}" class="btn btn-success mb-3">Create Transfer</a>
                </div>
            </div>
        </div>
    </div>

    <div style="border-bottom: 1px solid white;"></div>
    <form method="GET" action="{{ route('transfers.index') }}">
        <div class="input-group search">
            <input type="text" name="search" class="form-control textinput" placeholder="Search for transfers">
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
                <th>Account Transfered To</th>
                <th>Account Transfered From</th>
                <th>Details</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody  class="align-middle">
            @foreach($transfers as $transfer)
            <tr> 
                <td>{{ $transfer->date }}</td>
                <td>{{ $transfer->type }}</td>
                <td>{{ optional($transfer->account)->category ?? '' }}</td>
                <td>{{ optional($transfer->fromAccount)->category ?? '' }}</td>
                <td>{{ $transfer->description }}</td>
                <td>{{ number_format($transfer->amount,2) }}</td>
                <td>
                    <a href="{{ route('transfers.edit', $transfer->id) }}" class="btn btn-sm btn-info">Edit Transfer</a>
                    <form action="{{ route('transfers.destroy', $transfer->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete Transfer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if ($transfers->isNotEmpty())
        <div class="pagination">
            {{ $transfers->links() }}
    </div>
    @endif
</div>

@endsection
