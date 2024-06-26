@extends('layouts.app')

@section('title', 'Withdrawal Transactions')

@section('content')
<div class="container">
    
    <div class="row">
        <div class="col-6 titles"  style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Withdrawals</div>
        <div class="col-6">
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
               <button type="submit" class="btn btn-pink" style="border-radius:0 .5rem .5rem 0 !important">Search</button>
            </div>
        </div>
    </form>
    <br>

    <div class="table-responsive">
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
    </div>

    @if ($withdrawals->isNotEmpty())
    <div class="row mb-3">
        <div class="col-md-3  d-flex align-items-center">
            <form id="perPageForm" method="GET" action="{{ route('withdrawals.index') }}" class="form-inline">
                <label for="per_page" class="mr-2" style="font-size: 13px">Records per page:</label>
                <select name="per_page" id="per_page" class="form-control" style="width: 65px" onchange="document.getElementById('perPageForm').submit();">
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="75" {{ request('per_page') == 75 ? 'selected' : '' }}>75</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </form>
        </div>
        <div class="col-md-9 d-flex justify-content-end">
            <div class="pagination">
                {{ $withdrawals->appends(['per_page' => request('per_page')])->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@endsection
