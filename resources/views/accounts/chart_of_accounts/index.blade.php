@extends('layouts.app')

@section('title', 'Chart of Accounts')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; ">Chart of Accounts</h1>
        </div>
        <div class="card-body">

            <div style="float:right;" class="d-flex justify-content-end mt-3">
                <div>
                    <a href="{{ route('chartOfAccounts.create') }}" class="btn btn-success mb-3">Create New Account</a>
                </div>
                <div>
                    <form action="{{ route('chartOfAccounts.upload') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center" id="uploadForm">
                        @csrf
                        <div class="form-group ml-2">
                            <label for="file" class="sr-only">Upload File</label>
                            <input type="file" name="file" id="file" title="upload from excel" accept=".xlsx, .xls, .csv" class="form-control-file mb-3" style="max-width:110px;height: auto;">
                        </div>
                        <button type="submit" class="btn btn-primary mb-3">Upload</button>
                    </form>
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @if(session('import_errors'))
                    <div class="alert alert-danger">
                        <ul>
                            @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>

                    @elseif (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                </div>
            </div>

            <div style="border-bottom: 1px solid white;"></div>
            <form method="GET" action="{{ route('chartOfAccounts') }}">
                <div class="input-group search">
                    <input type="text" name="search" class="form-control textinput" placeholder="Search for chart of accounts">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-info" style="border-radius:0 .5rem .5rem 0 !important">Search</button>
                    </div>
                </div>
            </form>
            <br>

            <div class="table-responsive">
                <table class="table table-css table-bordered table-hover">
                    <thead class="thead-dark align-middle">
                        <tr>
                            <th>Code</th>
                            {{-- <th>Name</th> --}}
                            <th>Account Type</th>
                            <th>Account Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle">
                        @foreach($chartOfAccounts as $account)
                        <tr>
                            <td>{{ $account->code }}</td>
                            {{-- <td>{{ $account->name }}</td> --}}
                            <td>{{ optional($account->types)->category ?? $account->type }}</td>
                            <td>{{ $account->category }}</td>
                            <td>
                                <a href="{{ route('chartOfAccounts.edit', $account->id) }}" class="btn btn-sm btn-info">Edit Chart</a>
                                <form action="{{ route('chartOfAccounts.destroy', $account->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this account?')">Delete Chart</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($chartOfAccounts->isNotEmpty())
            <div class="pagination">
                {{ $chartOfAccounts->links() }}
            </div>
            @endif
        </div>
        <script>
            document.getElementById('uploadForm').addEventListener('submit', function(event) {
                const fileInput = document.getElementById('file');
                const fileName = fileInput.value;
                const allowedExtensions = /(\.xlsx|\.xls|\.csv)$/i;

                if (!allowedExtensions.exec(fileName)) {
                    alert('The file must be a file of type: XLSX, XLS, CSV.');
                    event.preventDefault();
                    return false;
                }
            });
        </script>
    </div>
</div>
@endsection