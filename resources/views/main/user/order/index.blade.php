@extends('main.templates.main')
@section('title')
    Orders
@endsection

@section('styles')
    <link href="{{ asset('css/table.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Orders view</h4>
        @if($tableData)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col-md-1">
                        <i class="fa-solid fa-table"></i>
                        {{ $tableData['name'] }}
                    </div>
                    <div class="col-md-3 d-flex justify-content-end">
                        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0" method="GET" action="{{ route('orders.index') }}">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    type="text"
                                    placeholder="Search for orders"
                                    aria-label="Search for orders"
                                    aria-describedby="btnNavbarSearch"
                                    name="search"
                                    value="{{ $search }}"
                                />
                                <button class="btn btn-primary" id="btnNavbarSearch" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="orders-table-admin">
                            <thead>
                            <tr>
                                <th>Užregistruotas</th>
                                @foreach($tableData['fields'] as $field)
                                    <th style="background-color: {{$field['color']}};">{{$field['name']}}</th>
                                @endforeach
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tableData['orders']['data'] as $order)
                                <tr>
                                    <td>{{ $order['user'] }}</td>
                                    @foreach($tableData['fields'] as $field)
                                        @if(array_key_exists($field['name'], $order))
                                            @if($field['type'] === 'id')
                                                <td>
                                                    <a class="order-view-link" href="{{ route('orders.view', ['id'=>$order['id']]) }}">
                                                        {{ $order[$field['name']] }}
                                                    </a>
                                                </td>
                                            @elseif ($field['type'] === 'file')
                                                <td><a href="{{ route('order-files.index', ['orderId' => $order['id']]) }}" class="order-view-link" title="View files"><i class="fa-regular fa-file"></i> {{$order['uploaded_files']}}</a></td>
                                            @else
                                                <td>{{ $order[$field['name']] }}</td>
                                            @endif

                                        @else
                                            <td></td>
                                        @endif
                                    @endforeach
                                    <td>
                                        <div class="btn-group" style="display: flex; width: 100%;">
                                            <a href="{{ route('orders.view', ['id'=>$order['id']]) }}" title="View" class="btn btn-outline-primary">View</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {!! $tableData['orders']['links'] !!}
                    </div>
                </div>
            </div>
    </div>
    @else
        Table does not exist
    @endif</div>
    @endsection

