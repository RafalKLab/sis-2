@extends('main.templates.main')
@section('title')
    Statistics
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Statistics {{ $targetYear }} <span class="text-primary stats-change-year-link"><i class="fa-solid fa-chevron-down"></i></span></h4>
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
                    <button class="nav-link {{ $data['month_name'] === $currentMonth ? 'active' : '' }}" id="{{ $data['month_name'] }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $data['month_name'] }}" type="button" role="tab" aria-controls="{{ $data['month_name'] }}" aria-selected="true">{{ $data['month_name'] }}</button>
                </li>
            @endforeach
        </ul>

        <!-- Tab panes -->
        <div class="tab-content" id="monthTabContent">
            @foreach($statistics as $data)
                <div class="tab-pane fade show {{ $data['month_name'] === $currentMonth ? 'active' : '' }}" id="{{ $data['month_name'] }}" role="tabpanel" aria-labelledby="{{ $data['month_name'] }}-tab">
                    <!-- Tab content -->
                    <div class="container-fluid px-4">
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6>{{ $data['month_name'] }} - užsakymų kiekis: {{ count($data['orders']) }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mt-3">
                                    <div class="col-xl-3 col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                                <div class="col-md-6"><h6>Faktinis pelnas</h6></div>
                                                <div class="col-md-6 d-flex justify-content-end">
                                                    <i class="fa-solid fa-money-bill"></i>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h3>€{{$data['profit']['actual_profit']}}</h3>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                <div class="col-md-6"><h6>Numatomas pelnas</h6></div>
                                                <div class="col-md-6 d-flex justify-content-end">
                                                    <i class="fa-solid fa-money-bill-trend-up"></i>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h3>+ €{{$data['profit']['expected_profit']}}</h3>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                                                <div class="col-md-6"><h6>Sumokėta avansų</h6></div>
                                                <div class="col-md-6 d-flex justify-content-end">
                                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h3>in progress...</h3>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                                <div class="col-md-6"><h6>Mūsų skolos</h6></div>
                                                <div class="col-md-6 d-flex justify-content-end">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h3>in progress...</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="container-fluid px-4 mt-3">
            <div class="card">
                <div class="card-header text-white bg-success">
                    <i class="fas fa-chart-area me-1"></i> Metinė faktinio pelno diagrama
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card mb-4">
                                <div class="card-body"><canvas id="totalProfitAreaChart" width="100%" height="25"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('js/chart-area-total-profit.js') }}"></script>
    <script>
        var statistics = @json($profitAreaChartData);
        var labels = Object.keys(statistics);
        var data = Object.values(statistics);

        // Ensure the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeTotalProfitAreaChart(labels, data);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const yearLink = document.querySelector('.stats-change-year-link');
            const yearForm = document.getElementById('yearForm');

            yearLink.addEventListener('click', function() {
                // Toggle visibility
                yearForm.style.display = yearForm.style.display === 'none' ? 'block' : 'none';
            });
        });
    </script>
@endsection
