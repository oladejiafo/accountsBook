@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div>
   
    <div style="color:#464646; font-style: bold; font-size: 3rem; border-bottom: 1px solid #464646;">
        <span>Welcome,  @auth {{ auth()->user()->name }} @else Guest @endauth </span>
        {{-- @if($companyName)
        <span >
            Company Name: {{ $companyName }}
        </span>
        @endif --}}

    </div>

 

    <br>

    <div id="container" style="position: relative; height:45vh; border: 1.2mm ridge #4e6570; border-radius: 30px;" class="align-middle table-bordered">
        <canvas id="bar-graph"></canvas>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <a href="{{ route('sales.create') }}" class="btn btn-success btn-lg btn-block btn-huge">Add New Sales</a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('select-supplier') }}" class="btn btn-success btn-lg btn-block btn-huge">Add New Purchases</a>
        </div>
    </div>
    
    <br>

    <div class="content-section">
        <div class="row">

            <div class="col-md-6">
                <div style="color: #4e6570; font-style: bold; font-size: 1.3em; border-bottom: 2px solid #4e6570">Recent Sales</div><br>
                @foreach($sales as $item)
                    @if (!$loop->first)
                        <br><div style="border-bottom: 0.5px solid #4e6570"></div><br>
                    @endif
                    <div class="row">               
                        <div class="col-md-8">
                            Bill No: #{{ $item->id }} <br> 
                            Purchased by <b>{{ $item->name }}</b> <br>
                            <small><i>{{ $item->created_at->format('F j, Y') }}</i></small>
                        </div>
                        <div class="col-md-3"> 
                            <br>  {{ $defaultCurrency }}{{ number_format($item->totalprice, 2) }} 
                            <br> <a href="{{ route('sales.show', $item->id) }}">View Bill</a> 
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-md-6">
                <div style="color: #4e6570; font-style: bold; font-size: 1.3em; border-bottom: 2px solid #4e6570">Recent Purchases</div><br>
                @foreach($purchases as $p_item)
                    @if (!$loop->first)
                        <br><div style="border-bottom: 0.5px solid #4e6570"></div><br>
                  
                    @endif
                    <div class="row">               
                        <div class="col-md-8">
                            Bill No: #{{ $p_item->id }} <br> 
                            Purchased from <b>{{ $p_item->supplier->name }}</b> <br>
                            <small><i>{{ $p_item->created_at->format('F j, Y') }}</i></small>
                        </div>
                        <div class="col-md-3"> 
                            <br>{{ $defaultCurrency }}
                            @foreach($p_item->items as $item)
                                {{ number_format($item->totalprice, 2)}}
                            @endforeach
                            {{-- {{ number_format($p_item->totalprice, 2) }}  --}}
                            <br> <a href="{{ route('purchase.show', $p_item->id) }}">View Bill</a> 
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>
    <!-- Loading Chart JS -->
    <script src="{{ asset('js/Chart.min.js') }}"></script>  
    <script>
        // Chart.defaults.global.defaultFontColor = '#3c3c3c';
        var colors = ['#1a79a5', '#ff7f0e', '#2ca02c', '#d62728', '#9467bd', '#8c564b', '#e377c2', '#7f7f7f', '#bcbd22', '#17becf'];

        //configuration for the bar graph
        var barConfig = {
            type: 'bar',
            data: {
                datasets: [{
                    backgroundColor: colors, //'#1a79a5',
                    label: 'Stocks in Inventory',
                    data: @json($data),
                    categories: @json($catt) 
                }],
                labels: @json($labels)
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                            var stockCategory = data.datasets[0].categories[tooltipItem.index]; // Access categories from the first dataset
                            return datasetLabel + ': ' + stockCategory + ' category' + ' - ' + tooltipItem.yLabel;
                        }
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true, // Ensure the scale starts at zero
                            suggestedMin: 0,   // Force the scale to start at zero
                        }
                    }]
                }
            },
        };


        //runs all charts on loading the webpage
        window.onload = function() {
            var ctx = document.getElementById('bar-graph').getContext('2d');
            window.BarStock = new Chart(ctx, barConfig);
        };

    </script>

@endsection
