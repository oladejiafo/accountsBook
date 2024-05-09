@extends('layouts.app')

@section('content')
<div class="container">

<div class="row align-items-center">
    <div class="col-md-6">
        <h1 class="mt-0 mb-4" style="color: #4e4e4e; font-weight: bold; font-size: 3rem;">Dashboard</h1>
    </div>

    <!-- Filter selection dropdown -->
    <div class="dropdown mb-4 col-md-6 text-right">
        <button class="btn btn-info dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Filter By &nbsp;
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="filterDropdown">
            @foreach($filterOptions as $key => $option)
                <a class="dropdown-item" href="{{ route('home', ['filter' => $key]) }}">{{ $option }}</a>
            @endforeach
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
                            <div class="h4">{{ $insight['value'] }}</div>
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
