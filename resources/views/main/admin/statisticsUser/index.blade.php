@extends('main.templates.main')
@section('title')
    User performance
@endsection
@section('styles')
    <link href="{{ asset('css/statistics.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">All users performance {{ $targetYear }} <span class="text-primary stats-change-year-link"><i class="fa-solid fa-chevron-down"></i></span></h4>
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
                                            <div class="col-md-12">
                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col">Vieta</th>
                                                        <th scope="col">Vardas</th>
                                                        <th scope="col">El-paštas</th>
                                                        <th scope="col">Atlikti užsakymai</th>
                                                        <th scope="col">Gautas pelnas</th>
                                                        <th scope="col"></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($data['users'] as $index => $user)
                                                        <tr>
                                                            <td>
                                                                {{ $index+1 }}
                                                                @if($index == 0)
                                                                    <i style="color: #ffd700;" class="fa-solid fa-trophy"></i>
                                                                @endif
                                                            </td>
                                                            <td>{{ $user['user']['name'] }}</td>
                                                            <td>{{ $user['user']['email'] }}</td>
                                                            <td>{{ $user['total_orders'] }}</td>
                                                            <td>{{ $user['profit'] }}</td>
                                                            <td>
                                                                <div class="btn-group" style="display: flex; width: 100%;">
                                                                    <a title="Peržiūrėti" href="{{ route('statistics-user.show', ['userId'=>$user['user']['id'], 'year'=>$targetYear, 'month'=>$data['month_name']]) }}" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i></a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
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
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-area me-1"></i> Metinė vartotojų pelno statistika
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card mb-4">
                                <div class="card-body"><canvas id="userPerformanceBarChart" width="100%" height="25"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('js/chart-bar-user-performance.js') }}"></script>
    <script>
        // Convert PHP array to JavaScript object
        var usersData = @json($performanceChartData);

        // Extract labels and data from the usersData object
        var labels = Object.keys(usersData);
        var data = Object.values(usersData);

        // Ensure the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeUserPerformanceBarChart(labels, data);
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
