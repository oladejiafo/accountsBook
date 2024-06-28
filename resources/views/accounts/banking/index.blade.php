@extends('layouts.app')

@section('title', 'Bank Feeds')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; ">Bank Feeds</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end mt-3"  style="float:right;" >
                    <!-- Upload form -->
                    <form action="{{ route('bank-feeds.upload') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center">
                        @csrf
                        <label for="file" style="font-size: 16px">Upload Bank Feed:</label>
                        <div class="form-group">
                            <input type="file" class="form-control mt-4" style="width: 180px" id="file" name="file">
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Upload</button>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- Display bank feed records -->
                    <div class="table-responsive">
                    <table class="table table-css table-bordered table-hover">
                        <thead class="thead-light align-middle">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <!-- Add other table headers as needed -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bankFeeds as $bankFeed)
                                <tr>
                                    <td>{{ $bankFeed->date }}</td>
                                    <td>{{ $bankFeed->type }}</td>
                                    <td>{{ $bankFeed->amount }}</td>
                                    <td>{{ $bankFeed->description }}</td>
                                    <!-- Add other table cells as needed -->
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
        
                    <!-- Pagination links -->
                    @if ($bankFeeds->isNotEmpty())
                    <div class="row mb-3">
                        <div class="col-md-3  d-flex align-items-center">
                            <form id="perPageForm" method="GET" action="{{ route('bank-feeds.index') }}" class="form-inline">
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
                                {{ $bankFeeds->appends(['per_page' => request('per_page')])->links() }}
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            </div>
        
            <!-- Display success message if any -->
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>
        
    </div>
</div>
@endsection
