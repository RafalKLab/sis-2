@extends('main.templates.main')
@section('title')
    Info
@endsection
@section('styles')
    <link href="{{ asset('css/goals.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid py-2">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="col-md-1"><b>Įvykdyta: @if($goalData) {{$goalData['sales']}} € </b>@endif</div>
                <div class="col-md-1 d-flex justify-content-end">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="datatablesSimple">
                        <thead>
                        <tr>
                            <th>Užsakymas</th>
                            <th>Sąskaitos numeris</th>
                            <th>Pirkėjas</th>
                            <th>Išrašymo data</th>
                            <th>Suma</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($goalData)
                            @foreach($goalData['sales_data']['invoice_data'] as $invoice)
                                <tr>
                                    <td>
                                        <a class="goal-order-link" href="{{ route('orders.view', ['id' => $invoice['order_id']]) }}">{{ $invoice['order_key'] }}</a>
                                    </td>
                                    <td>{{ $invoice['number'] }}</td>
                                    <td>{{ $invoice['buyer'] }}</td>
                                    <td>{{ $invoice['issue_date'] }}</td>
                                    <td>{{ $invoice['sum'] }} €</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
