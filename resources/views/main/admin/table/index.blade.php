@extends('main.templates.main')
@section('title')
    Table admin view
@endsection

@section('styles')
    <link href="{{ asset('css/table.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="alert alert-warning">
        This section is for viewing table layouts and fields only. For order actions, please use the <a class="order-view-link" href="{{ route('orders.index') }}">Orders tab</a>.
    </div>
    <div class="container-fluid px-4">
        <h4 class="mt-4">Table admin view</h4>
        @if($tableData)
            <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="col-md-1">
                    <i class="fa-solid fa-table"></i>
                    {{ $tableData['name'] }}
                </div>
                <div class="col-md-3 d-flex justify-content-end">
{{--                    Turned off because it is only for table display--}}
{{--                    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0" method="GET" action="{{ route('admin-table.index') }}">--}}
{{--                        <div class="input-group">--}}
{{--                            <input--}}
{{--                                class="form-control"--}}
{{--                                type="text"--}}
{{--                                placeholder="Search for orders"--}}
{{--                                aria-label="Search for orders"--}}
{{--                                aria-describedby="btnNavbarSearch"--}}
{{--                                name="search"--}}
{{--                                value="{{ $search }}"--}}
{{--                            />--}}
{{--                            <button class="btn btn-primary" id="btnNavbarSearch" type="submit"><i class="fas fa-search"></i></button>--}}
{{--                        </div>--}}
{{--                    </form>--}}
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="orders-table-admin">
                        <thead>
                        <tr>
                            @foreach($tableData['fields'] as $field)
                                <th style="background-color: {{$field['color']}};">{{$field['name']}}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tableData['orders']['data'] as $order)
                            <tr>
                                @foreach($tableData['fields'] as $field)
                                    @if(array_key_exists($field['name'], $order))
                                        <td>{{ $order[$field['name']] }}</td>
                                    @else
                                        <td></td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
{{--                Turned off because it is only for table display--}}
{{--                <div class="d-flex justify-content-center mt-3">--}}
{{--                    {!! $tableData['orders']['links'] !!}--}}
{{--                </div>--}}
                </div>
            </div>
        </div>
        @else
            Table does not exist
        @endif
    </div>
@endsection

