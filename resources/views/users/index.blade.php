@extends('layouts.app')

@section('title', 'Users Management')
@section('content')
<div class="container">
    
    <div class="row">
        <div class="col-6 titles"  style="color: #4e4e4e; font-style: bold;">Users</div>
        <div class="col-6">
            <div style="float:right;" class="d-flex justify-content-end mt-3">
                <div>
                    <a href="{{ route('users.create') }}" class="btn btn-success mb-3">Create User</a>
                </div>
            </div>
        </div>
    </div>

    <div style="border-bottom: 1px solid white;"></div>
    <form method="GET" action="{{ route('users.index') }}">
        <div class="input-group search">
            <input type="text" name="search" class="form-control textinput" placeholder="Search for users">
            <div class="input-group-append">
               <button type="submit" class="btn btn-pink" style="border-radius:0 .5rem .5rem 0 !important">Search</button>
            </div>
        </div>
    </form>
    
    <br>

    <div class="table-responsive">
    <table class="table table-css table-bordered table-hover">
        <thead class="thead-light align-middle">
            <tr>
                <!-- <th>ID</th> -->
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Roles</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="align-middle">
            @foreach($users as $user)
                <tr>
                    <!-- <td>{{ $user->id }}</td> -->
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->department ? $user->department->name : '' }}</td>
                    <td>{{ $user->job_title ? $user->job_title->name : '' }}</td>
                    
                    <td>
                        @foreach($user->roles()->where('model_id', $user->id)->get() as $role)
                        <span class="badge badge-custom" style="font-size: 1.3em; background-color: #aec48b; color: #ffffff;">{{ $role->name }}</span>
                    @endforeach
                    </td>
                    <td>
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn btn-secondary" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn btn-info" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this record?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @if ($users->isNotEmpty())
    <div class="row mb-3">
        <div class="col-md-3  d-flex align-items-center">
            <form id="perPageForm" method="GET" action="{{ route('users.index') }}" class="form-inline">
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
                {{ $users->appends(['per_page' => request('per_page')])->links() }}
            </div>
        </div>
    </div>
    @endif
@endsection
