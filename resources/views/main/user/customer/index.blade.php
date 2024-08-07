@extends('main.templates.main')
@section('title')
    Customers
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Customers</h4>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="col-md-1"><i class="fa-solid fa-user-tie"></i></div>
                <div class="col-md-1 d-flex justify-content-end"></div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="datatablesSimple">
                        <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Orders</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($customersData as $customer => $data)
                            <tr>
                                <th>{{$customer}}</th>
                                <th>
                                    @foreach($data['orders'] as $orderKey => $orderId)
                                        <a href="{{ route('orders.view', ['id'=>$orderId]) }}">{{ $orderKey }}</a>
                                    @endforeach
                                </th>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
