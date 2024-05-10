@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <style>
        .dropdown-item:hover {
            background-color: #d0d3da;
        }
        .dropdown-item.dash-filter.active {
            background-color: #607d8b !important;
        }
    </style>
    
<div class="row align-items-center">
    <div class="col-md-6">
        <h1 class="mt-0 mb-4" style="color: #4e4e4e; font-weight: bold; font-size: 3rem;">Dashboard</h1>
    </div>

    <div class="col-md-6 mb-4 text-right">
        <div class="dropdown" style="display: inline-block;">
            <button class="btn btn-info" style="background-color: #dee0e6; color: #000; width: 200px;text-align:left;display: flex;border-radius:0px !important" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span style="text-align:left; margin-right:50px">{{ $filterOptions[$filter] }}</span> <i class="fas fa-caret-down" style="color: #000;margin-left: auto;"></i>
            </button>
            <div class="dropdown-menu" style="width: 200px; background-color: #dee0e6;" aria-labelledby="filterDropdown">
                @foreach($filterOptions as $key => $option)
                    <a class="dash-filter dropdown-item {{ $filter == $key ? 'active' : '' }}" href="{{ route('home', ['filter' => $key]) }}" style="color: #000;">{{ $option }}</a>
                @endforeach
            </div>
        </div>
    </div>
</div>


    <div class="row">
        @foreach($insights as $insight)
        <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="{{ $insight['icon'] }} fa-3x"></i>
                        </div>
                        <div class="col-9 text-right">
                            <div class="h5">{{ $insight['value'] }}</div>
                            <!-- <div class="text-muted">{{ $insight['title'] }}</div> -->
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-secondary">{{ $insight['title'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
