@extends('main.templates.main')
@section('title')
    {{ $user->name }} performance
@endsection
@section('styles')
    <link href="{{ asset('css/statistics.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">{{ $user->name }} performance {{ $targetYear }} <span class="text-primary stats-change-year-link"><i class="fa-solid fa-chevron-down"></i></span></h4>
        <form class="mb-3" id="yearForm" style="display:none;" action="" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-2">
                    <select  class="form-control" id="yearInput" name="selectedYear" required>
                        @foreach($yearsSelect as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                </div>
            </div>
        </form>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="monthTab" role="tablist">
            @foreach($statistics as $data)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $data['month_name'] === $currentMonth ? 'active' : '' }}" id="{{ $data['month_name'] }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $data['month_name'] }}" type="button" role="tab" aria-controls="{{ $data['month_name'] }}" aria-selected="true">
                        @if($data['month_name'] === $currentMonth)
                            <b>{{ $data['month_name'] }}</b>
                        @else
                            {{ $data['month_name'] }}
                        @endif
                    </button>
                </li>
            @endforeach
        </ul>

        <!-- Tab panes -->
        <div class="tab-content" id="monthTabContent">
            @foreach($statistics as $data)
                <div class="tab-pane fade show {{ $data['month_name'] === $currentMonth ? 'active' : '' }}" id="{{ $data['month_name'] }}" role="tabpanel" aria-labelledby="{{ $data['month_name'] }}-tab">
                    <!-- Tab content -->
                    <div class="container-fluid px-4">
                        <div class="row">
                            <div class="col-md-12"><div class="card mt-3">
                                    <div class="card-header">
                                        <h6>{{ $data['month_name'] }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="card mb-4">
                                                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                        <div class="col-md-8"><h6>Faktinis pelnas</h6></div>
                                                        <div class="col-md-4 d-flex justify-content-end">
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <h3>€ {{ $data['users'][0]['profit'] }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card mb-4">
                                                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                        <div class="col-md-8"><h6>Numatomas pelnas</h6></div>
                                                        <div class="col-md-4 d-flex justify-content-end">
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <h3>+ € {{ $data['users'][0]['expected_profit'] }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header text-white bg-success">
                                                        Apmokėti užsakymai: {{ $data['users'][0]['total_orders'] }}
                                                    </div>
                                                    <div class="card-body">
                                                        <table class="table">
                                                            <thead>
                                                            <tr>
                                                                <th scope="col">užsakymas</th>
                                                                <th scope="col">pelnas</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($data['users'][0]['orders'] as $order)
                                                                <tr>
                                                                    <td><a class="custom-link" href="{{ route('orders.view', ['id'=>$order['order_id']]) }}">{{ $order['order_key'] }}</a></td>
                                                                    <td>{{ $order['profit'] }}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header text-white bg-danger">
                                                        Neapmokėti užsakymai: {{ $data['users'][0]['total_not_paid_orders'] }}
                                                    </div>
                                                    <div class="card-body">
                                                        <table class="table">
                                                            <thead>
                                                            <tr>
                                                                <th scope="col">užsakymas</th>
                                                                <th scope="col">pelnas</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($data['users'][0]['not_paid_orders'] as $order)
                                                                <tr>
                                                                    <td><a class="custom-link" href="{{ route('orders.view', ['id'=>$order['order_id']]) }}">{{ $order['order_key'] }}</a></td>
                                                                    <td>{{ $order['profit'] }}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div></div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>


        <div class="container-fluid px-4 mt-3">
            <div class="row">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-chart-area me-1"></i> Metinė vartotojo statistika
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card mb-4">
                                        <div class="card-body"><canvas id="yearUserProfitAreaChart" width="100%" height="25"></canvas></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    Faktinis metinis pelnas
                                </div>
                                <div class="card-body">
                                    <h3>€ {{ $totalYearProfit }}</h3>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Iš viso užsakymų
                                </div>
                                <div class="card-body">
                                    <h3> {{ $totalYearOrders }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('js/chart-area-user-profit.js') }}"></script>
    <script>
        // Convert PHP array to JavaScript object
        var usersData = @json($userPerformanceChartData);

        // Extract labels and data from the usersData object
        var labels = Object.keys(usersData);
        var data = Object.values(usersData);

        // Ensure the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeTotalProfitAreaChart(labels, data);

            const yearLink = document.querySelector('.stats-change-year-link');
            const yearForm = document.getElementById('yearForm');

            yearLink.addEventListener('click', function() {
                // Toggle visibility
                yearForm.style.display = yearForm.style.display === 'none' ? 'block' : 'none';
            });
        });
    </script>
@endsection
