@extends('layouts.app')

@section('title', 'Bank Feeds')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Bank Feeds</h1>
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
                    <table class="table table-css table-bordered table-hover">
                        <thead class="thead-dark align-middle">
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
        
                    <!-- Pagination links -->
                    {{ $bankFeeds->links() }}
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
