@extends('main.templates.main')
@section('title')
    Statistics
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Statistics {{ $targetYear }} {{ $targetCompany['name'] }} <span class="text-primary stats-change-year-link"><i class="fa-solid fa-chevron-down"></i></span></h4>
        <form class="mb-3" id="yearForm" style="display:none;" action="" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-1">
                    <select  class="form-control" id="yearInput" name="selectedYear" required>
                        @foreach($yearsSelect as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select  class="form-control" id="selectedCompany" name="selectedCompany" required>
                        @foreach($companySelect as $company)
                            <option value="{{ $company['id'] }}">{{ $company['name'] }}</option>
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
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6>{{ $data['month_name'] }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mt-3">
                                    <div class="col-xl-4 col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                                <div class="col-md-6"><h6 title="Pardavimo saskaita apmokėta">Faktinis pelnas</h6></div>
                                                <div class="col-md-6 d-flex justify-content-end">
                                                    <i class="fa-solid fa-money-bill"></i>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h3>€{{$data['profit']['actual_profit']}}</h3>
                                                <a href="#collapseActualProfit" class="more-details-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseActualProfit">
                                                    Show More <i class="fa fa-chevron-down"></i>
                                                </a>
                                            </div>
                                            <!-- Collapsible Content -->
                                            <div class="collapse" id="collapseActualProfit">
                                                <div class="card-body">
                                                    <!-- Additional information you want to show goes here -->
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">Užsakymas</th>
                                                            <th scope="col">Sąskaitos numeris</th>
                                                            <th scope="col">Pelnas</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($data['profit']['actual_profit_details'] as $actualProfitDetails )
                                                            <tr>
                                                                <td>
                                                                    <a class="more-details-link" href="{{ route('orders.view', ['id'=>$actualProfitDetails['order_id']]) }}">
                                                                        {{ $actualProfitDetails['order_key'] }}
                                                                    </a>
                                                                </td>
                                                                <td>{{ $actualProfitDetails['invoice_number'] }}</td>
                                                                <td class="text-success">{{ $actualProfitDetails['order_sales_sum'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                <div class="col-md-6"><h6 title="Pardavimo saskaita neapmokėta">Numatomas pelnas</h6></div>
                                                <div class="col-md-6 d-flex justify-content-end">
                                                    <i class="fa-solid fa-money-bill-trend-up"></i>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h3>+ €{{$data['profit']['expected_profit']}}</h3>
                                                <a href="#collapseExpectedProfit" class="more-details-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseExpectedProfit">
                                                    Show More <i class="fa fa-chevron-down"></i>
                                                </a>
                                            </div>
                                            <!-- Collapsible Content -->
                                            <div class="collapse" id="collapseExpectedProfit">
                                                <div class="card-body">
                                                    <!-- Additional information you want to show goes here -->
                                                    <table class="table" id="">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">Užsakymas</th>
                                                            <th scope="col">Sąskaitos numeris</th>
                                                            <th scope="col">Pelnas</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($data['profit']['expected_profit_details'] as $expectedProfitDetails )
                                                            <tr>
                                                                <td>
                                                                    <a class="more-details-link" href="{{ route('orders.view', ['id'=>$expectedProfitDetails['order_id']]) }}">
                                                                        {{ $expectedProfitDetails['order_key'] }}
                                                                    </a>
                                                                </td>
                                                                <td>{{ $expectedProfitDetails['invoice_number'] }}</td>
                                                                <td class="text-primary">{{ $expectedProfitDetails['order_sales_sum'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                                <div class="col-md-6"><h6 title="Visi mėnesio užsakymai">Iš viso užsakymų</h6></div>
                                                <div class="col-md-6 d-flex justify-content-end">
                                                    <i class="fa-solid fa-clipboard-list"></i>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h3>{{ count($data['orders']) }}</h3>
                                                <a href="#collapseTotalOrders" class="more-details-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseTotalOrders">
                                                    Show More <i class="fa fa-chevron-down"></i>
                                                </a>
                                            </div>
                                            <!-- Collapsible Content -->
                                            <div class="collapse" id="collapseTotalOrders">
                                                <div class="card-body">
                                                    <!-- Additional information you want to show goes here -->
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">Užsakymas</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($data['orders'] as $orderKey => $orderId)
                                                            <tr>
                                                                <td>
                                                                    <a class="more-details-link" href="{{ route('orders.view', ['id'=>$orderId]) }}">
                                                                        {{ $orderKey }}
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                                                <div class="col-md-6"><h6 title="Užsakymai, kur būsena apmokėta">Sumokėta avansų</h6></div>
                                                <div class="col-md-6 d-flex justify-content-end">
                                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h3>€{{$data['paid_in_advance']['total_prime_cost']}}</h3>
                                                <a href="#collapsePaidInAdvance" class="more-details-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapsePaidInAdvance">
                                                    Show More <i class="fa fa-chevron-down"></i>
                                                </a>
                                            </div>
                                            <!-- Collapsible Content -->
                                            <div class="collapse" id="collapsePaidInAdvance">
                                                <div class="card-body">
                                                    <!-- Additional information you want to show goes here -->
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">Užsakymas</th>
                                                            <th scope="col">Savikaina</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($data['paid_in_advance']['details'] as $details )
                                                            <tr>
                                                                <td>
                                                                    <a class="more-details-link" href="{{ route('orders.view', ['id'=>$details['order_id']]) }}">
                                                                        {{ $details['order_key'] }}
                                                                    </a>
                                                                </td>
                                                                <td class="text-warning">{{ $details['prime_cost'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                                <div class="col-md-6"><h6 title="Visos neapmokėtos sąskaitos">Mūsų skolos</h6></div>
                                                <div class="col-md-6 d-flex justify-content-end">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h3>{{ $data['debts']['total_debts'] }}&nbsp;&nbsp;|&nbsp;&nbsp;€{{ $data['debts']['total_debts_sum'] }}</h3>
                                                <a href="#collapseDebts" class="more-details-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseDebts">
                                                    Show More <i class="fa fa-chevron-down"></i>
                                                </a>
                                            </div>
                                            <!-- Collapsible Content -->
                                            <div class="collapse" id="collapseDebts">
                                                <div class="card-body">
                                                    <!-- Additional information you want to show goes here -->
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">Užsakymas</th>
                                                            <th scope="col">Sąskaitos numeris</th>
                                                            <th scope="col">Suma</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($data['debts']['details'] as $order)
                                                                @foreach($order['debts'] as $debt)
                                                                    <tr>
                                                                    <td>
                                                                        <a class="more-details-link" href="{{ route('orders.view', ['id'=>$order['order_id']]) }}">
                                                                            {{ $order['order_key'] }}
                                                                        </a>
                                                                    </td>
                                                                    <td>
                                                                        {{$debt['invoice_name']}}: {{$debt['invoice_number']}}
                                                                    </td>
                                                                    <td>
                                                                        <span class="text-danger">{{$debt['sum']}}</span>
                                                                    </td>
                                                                    </tr>
                                                                @endforeach
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                                <div class="col-md-6"><h6 title="Visos apmokėtos sąskaitos">Mūsų išlaidos</h6></div>
                                                <div class="col-md-6 d-flex justify-content-end">
                                                    <i class="fa-solid fa-receipt"></i>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h3>€{{ $data['expenses']['total_expenses'] }}</h3>
                                                <a href="#collapseExpenses" class="more-details-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseExpenses">
                                                    Show More <i class="fa fa-chevron-down"></i>
                                                </a>
                                            </div>
                                            <!-- Collapsible Content -->
                                            <div class="collapse" id="collapseExpenses">
                                                <div class="card-body">
                                                    <!-- Additional information you want to show goes here -->
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">Užsakymas</th>
                                                            <th scope="col">Sąskaitos numeris</th>
                                                            <th scope="col">Suma</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($data['expenses']['details'] as $order)
                                                            @foreach($order['expenses'] as $expenseData)
                                                                <tr>
                                                                    <td>
                                                                        <a class="more-details-link" href="{{ route('orders.view', ['id'=>$order['order_id']]) }}">
                                                                            {{ $order['order_key'] }}
                                                                        </a>
                                                                    </td>
                                                                    <td>
                                                                        {{ $expenseData['invoice_name'] ?: $expenseData['customer'] }}: {{$expenseData['invoice_number']}}
                                                                    </td>
                                                                    <td>
                                                                        <span class="text-danger">{{$expenseData['sum']}}</span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
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

                </div>
            @endforeach
        </div>

        <div class="container-fluid px-4 mt-3">
            <div class="card">
                <div class="card-header text-white bg-success">
                    <i class="fas fa-chart-area me-1"></i> Metinė faktinio ir numatomo pelno diagrama
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
        var labels = Object.keys(statistics.actual);
        var actualData = Object.values(statistics.actual);
        var expectedData = Object.values(statistics.expected);

        // Ensure the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeTotalProfitAreaChart(labels, actualData, expectedData);
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
